var Tasks = (function () {
    var scope = {};

    var TaskTypes = ['', 'Email', 'Call', 'Meeting'];

    var initialized = false;

    function init() {
        $(document).on('change', '.task-list .task .task-status-toggle', function () {
            changeTaskStatus($(this).closest('.task').data('task'), this.checked);
        });

        initialized = true;
    }

    scope.$createTaskList = function (tasks) {
        if (!initialized) {
            init();
        }

        return $createTaskList(tasks);
    };

    function $createTaskList(tasks) {
        var $taskList = $('<div class="task-list"></div>');

        $.each(tasks, function (index, task) {
            $createTask(task).appendTo($taskList);
        });

        return $taskList;
    }

    scope.$createTask = function (task) {
        if (!initialized) {
            init();
        }

        return $createTask(task);
    };

    function $createTask(task) {
        var workspace_member = null;
        console.log(workspace_members);
        $.each(workspace_members, function (i, member) {
            console.log(member);
            if (member.user_id === task.owner_id) {
                workspace_member = member;
                return;
            }
        });

        var member_name   = workspace_member.user.first_name + ' ' + workspace_member.user.last_name;
        var member_avatar = '/images/profile-blank.png';

        if (workspace_member.user.avatar !== '') {
            member_avatar = '/uploads/users/avatars/' + workspace_member.user.avatar + '_200x200.jpg';
        } else {
            var i;
            var integration_count = workspace_member.user.integrations.length;
            for (i = 0; i < integration_count; i++) {
                if (workspace_member.user.integrations[i].avatar !== '') {
                    member_avatar = 'https://' + workspace_member.user.integrations[i].avatar;
                    break;
                }
            }
        }

        var $task = $('\
            <div class="deal-text-entry task' + (task.is_completed == 1 ? ' task-completed' : '') + '">\
                <div class="task-content content">\
                    <span class="status-date-wrapper">\
                        <input class="task-status-toggle" type="checkbox" value="1"' + (task.is_completed == 1 ? ' checked="checked"' : '') + ' />\
                        <span  data-name="due_at" data-type="datetime" data-mode="popup" class="task-date">' + getDateString(task.due_at) + '</span>\
                        <span class="task-status task-status-due"> Due</span><span class="task-status task-status-completed"> Completed</span>\
                    </span>\
                    <h3 data-name="type" data-type="select" data-mode="inline" data-value="' + task.type + '" class="task-title">' + TaskTypes[task.type] + '</h1>\
                    <p data-name="description" data-type="textarea" data-mode="inline" class="task-description">' + task.description + '</p>\
                </div>\
                <div class="meta">\
                    <img class="avatar" src="' + member_avatar + '" />\
                    <span class="fullname">' + member_name + '</span>\
                </div>\
            </div>\
        ').data('task', task)
                .attr('id', 'task-' + task.id)
                .attr('data-task-id', task.id);
        task.$element = $task;

        $task.find('[data-name="type"]').editable({
            source: [
                {value: 1, text: 'Email'},
                {value: 2, text: 'Call'},
                {value: 3, text: 'Meeting'}
            ]
        });
        $task.find('[data-name="due_at"]').editable({
            params: function (params) {
                var data = {};
                data[params.name] = (new Date(params.value)).getTime() / 1000;
                return data;
            },
            clear: false,
            format: 'M dd, yyyy',
            viewformat: 'M dd, yyyy',
            placement: 'left',
            validate: function (value) {
                if ($.trim(value) == '')
                    return 'Enter task due date.';
            },
            datetimepicker: {
                container: $task.find('[data-name="due_at"]').parent(),
                format: 'M dd, yyyy',
                todayBtn: 'linked',
                showMeridian: true,
                weekStart: 1,
                pickerPosition: 'top-left',
                autoclose: true,
                minView: 2
            }
        }).on('show.bs.modal', function (e) {
            e.stopPropagation();
        });

        $task.find('[data-name="description"]').editable({
            showbuttons: 'bottom',
            validate: function (value) {
                if ($.trim(value) == '')
                    return 'Enter task content.';
            }
        });

        $task.find('[data-name]')
                .editable('option', 'disabled', task.is_completed)
                .editable('option', 'pk', task.id)
                .editable('option', 'url', getApiUrl() + task.id)
                .editable('option', 'success', function (response, newValue) {
                    var name = $(this).attr('data-name');
                    var task = $(this).closest('.task').data('task');
                    if (name == 'due_at') {
                        task[name] = (new Date(newValue)).getTime() / 1000;
                    } else {
                        task[name] = newValue;
                    }
                });

        $task.append('<span class="mark"><span class="icon"><i class="fa fa-check-square-o"></i></span></span>');

        return $task;
    }

    function changeTaskStatus(task, status) {
        $.ajax({
            method: 'PUT',
            url: getApiUrl() + task.id,
            data: {is_completed: status},
            success: function () {
                showSuccessMessage("Success", "The task status has been changed successfully.");
                task.is_completed = status;
                if (status) {
                    task.$element.addClass('task-completed');
                } else {
                    task.$element.removeClass('task-completed');
                }
                task.$element.find('[data-name]').editable('option', 'disabled', status);
            }
        });
    }

    function getApiUrl() {
        return '/api/v1/tasks/';
    }

    return scope;
})();