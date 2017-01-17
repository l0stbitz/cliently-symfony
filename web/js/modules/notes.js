var Notes = (function () {
    var scope = {};

    var $editorForm = null;

    var initialized = false;

    function init() {
        $editorForm = $('\
            <form class="form action-form note-action-form note-editor-form" style="display: none">\
                <div class="form-group">\
                    <div class="bootstrap-text-editor form-control note-name" data-placeholder="Add Note"></div>\
                </div>\
                <div class="row">\
                    <div class="col-md-10">\
                    ' + getEditableWYSIHTML5OptionsHtml() + '\
                    </div>\
                    <div class="col-md-2">\
                        <div style="padding-top: 2px; padding-right: 2px; text-align: right">\
                            <button class="btn btn-primary btn-sm editor-save"><span class="glyphicon glyphicon-ok"></span></button>\
                            <button class="btn btn-default btn-sm editor-cancel"><span class="glyphicon glyphicon-remove"></span></button>\
                        </div>\
                    </div>\
                </div>\
            </form>\
        ');

        $editorForm.find('.bootstrap-text-editor').wysiwyg({toolbarSelector: $editorForm.find('[data-role="editor-toolbar"]')});

        $(document).on('click', '.note-list .note [data-name="description"]', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            startEditing($(this));
        }).on('click', '.note-list .note .note-editor-form .editor-save', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            var $xhr = save($(this).closest('.note').data('note'));
            if ($xhr) {
                var $btn = $(this);
                var html = $btn.html();
                $btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                $xhr.done(function () {
                    $btn.attr('disabled', false).html(html);
                }).fail(function () {
                    $btn.attr('disabled', false).html(html);
                });
            }
        }).on('click', '.note-list .note .note-editor-form .editor-cancel', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            stopEditing();
        });
        initialized = true;
    }

    scope.$createNoteList = function (notes) {
        if (!initialized) {
            init();
        }

        return $createNoteList(notes);
    };

    function $createNoteList(notes) {
        var $noteList = $('<div class="note-list"></div>');

        $.each(notes, function (index, note) {
            $createNote(note).appendTo($noteList);
        });

        return $noteList;
    }

    scope.$createNote = function (note) {
        if (!initialized) {
            init();
        }

        return $createNote(note);
    };

    function $createNote(note) {
        var workspace_member = null;
        $.each(workspace_members, function (i, member) {
            if (member.user_id === note.owner_id) {
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

        var $note = $('\
            <div class="deal-text-entry note">\
                <div class="note-content content">\
                    <span class="date-wrapper">\
                        <i class="fa fa-pencil-square-o"></i>\
                        <span class="note-date">' + getDateTimeString(note.created_at) + '</span>\
                    </span>\
                    <!-- <h3 class="note-title">You left a note for ' + note.lead.clients[0].name + '</h1> -->\
                    <div data-name="description" data-type="wysihtml5" data-mode="inline" class="note-description">' + note.description + '</div>\
                </div>\
                <div class="meta">\
                    <img class="avatar" src="' + member_avatar + '" />\
                    <span class="fullname">' + member_name + '</span>\
                </div>\
            </div>\
        ').data('note', note)
                .attr('id', 'note-' + note.id)
                .attr('data-note-id', note.id);
        note.$element = $note;

        $note.append('<span class="mark"><span class="icon"><i class="fa fa-file-text-o"></i></span></span>');

        return $note;
    }
    function startEditing($description) {
        if ($editorForm.is(":visible")) {
            stopEditing();
        }
        $editorForm.insertAfter($description.hide()).show().find('.bootstrap-text-editor').html($description.html()).focus();
        $(document).on('click.note-in-editing', function (e) {
            if ($(e.target).closest(".note-editor-form").length > 0) {
                return;
            }
            stopEditing();
        });
    }
    function stopEditing() {
        $(document).off('click.note-in-editing');
        $editorForm.prev().show();
        $editorForm.detach();
    }

    function save(note) {
        var description = $editorForm.find('.bootstrap-text-editor').html();

        if (!description) {
            showErrorMessage('Notes', 'Please enter description.');
            return false;
        }

        return $.ajax({
            url: getApiUrl() + note.id,
            method: "PUT",
            data: {description: description},
            success: function () {
                $editorForm.prev().html(description).show();
                note.description = description;
                stopEditing();
            }
        });
    }

    function getApiUrl() {
        return '/api/v1/notes/';
    }

    return scope;
})();