var Timeline = (function () {
    var scope = {};

//    var ActionModuleClasses = {
//        TASK: 'task',
//        NOTE: 'note',
//        EMAIL: 'email',
//        TWITTER: 'twitter'
//    };
    var ActionClasses = {
        LEAD_STAGE_MOVE: 'lead_stage_move',
        LEAD_ACCEPT: 'lead_accept',
        WORKFLOW_ADD: 'workflow_add',
        TASK_ADD: 'task_add',
        NOTE_ADD: 'note_add',
        EMAIL_SEND: 'email_send',
        TWITTER_TWEET: 'twitter_tweet',
        TWITTER_REPLY: 'twitter_reply',
        TWITTER_RETWEET: 'twitter_retweet',
        TWITTER_FOLLOW: 'twitter_follow'
    };

    var ActionTitles = {};
    ActionTitles[ActionClasses.LEAD_STAGE_MOVE] = 'Stage Changed for for {lead_name}';
    ActionTitles[ActionClasses.LEAD_ACCEPT] = 'Lead accepted for {lead_name}';
    ActionTitles[ActionClasses.WORKFLOW_ADD] = '{lead_name} added to Workflow {workflow_name}';
    ActionTitles[ActionClasses.WORKFLOW_AUTO_STOP] = 'Workflow Auto-Stopped for {lead_name}';
    ActionTitles[ActionClasses.TASK_ADD] = 'Task added for {lead_name}';
    ActionTitles[ActionClasses.NOTE_ADD] = 'Note added for {lead_name}';
    ActionTitles[ActionClasses.EMAIL_SEND] = 'Email {lead_name}';
    ActionTitles[ActionClasses.TWITTER_TWEET] = 'Tweeted at {lead_name}';
    ActionTitles[ActionClasses.TWITTER_REPLY] = 'Tweet Replied at {lead_name}';
    ActionTitles[ActionClasses.TWITTER_RETWEET] = 'Retweeted at {lead-name}';
    ActionTitles[ActionClasses.TWITTER_FOLLOW] = '{lead_name} followed {user_name}';
    ;

    var ActionMarkIcons = {};
    ActionMarkIcons[ActionClasses.LEAD_STAGE_MOVE] = '<i class="fa fa-cliently-twitter"></i>';
    ActionMarkIcons[ActionClasses.LEAD_ACCEPT] = '<i class="fa fa-cliently-twitter"></i>';
    ActionMarkIcons[ActionClasses.WORKFLOW_ADD] = '<i class="fa fa-cliently-twitter"></i>';
    ActionMarkIcons[ActionClasses.WORKFLOW_AUTO_STOP] = '<i class="fa fa-cliently-twitter"></i>';
    ActionMarkIcons[ActionClasses.TASK_ADD] = '<i class="fa fa-check-square-o"></i>';
    ActionMarkIcons[ActionClasses.NOTE_ADD] = '<i class="fa fa-file-text-o"></i>';
    ActionMarkIcons[ActionClasses.EMAIL_SEND] = '<i class="fa fa fa-envelope"></i>';
    ActionMarkIcons[ActionClasses.TWITTER_TWEET] = '<i class="fa fa-cliently-twitter"></i>';
    ActionMarkIcons[ActionClasses.TWITTER_REPLY] = '<i class="fa fa-reply"></i>';
    ActionMarkIcons[ActionClasses.TWITTER_RETWEET] = '<i class="fa fa-reteet"></i>';
    ActionMarkIcons[ActionClasses.TWITTER_FOLLOW] = '<i class="fa fa-user-plus"></i>';

    var initialized = false;

    function init() {

        initialized = true;
    }

    scope.$createTimeline = function (timeline) {
        if (!initialized) {
            init();
        }

        return $createTimeline(timeline);
    };

    function $createTimeline(timeline) {
        var $timeline = $('<div class="timeline" id="timeline-' + timeline.id + '" data-timeline-id="' + timeline.id + '"><h2>Timeline</h2></div>').data('timeline', timeline);

        var prevDate = 0;
        $.each(timeline.actions, function (index, action) {
            action.timeline = timeline;
            var date = new Date(action.created_at * 1000);
            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            var tc = date.getTime();
            if (!prevDate || prevDate != tc) {
                var monthName = MonthNames[date.getMonth()];
                monthName = monthName.substr(0, 3);
                $('<div class="date-viewer"><div class="date"><span class="month">' + monthName + '</span><span class="day">' + date.getDate() + '</span></div></div>').appendTo($timeline);
            }
            prevDate = tc;
            $createAction(action).appendTo($timeline);
        });

        return $timeline;
    }

    function $createAction(action) {
        var $action = $('\
            <div class="action">\
                <div class="action-inner">\
                    <div class="action-header">\
                        <span class="time">' + getTimeString(action.created_at) + '</span>\
                        <h3>' + generateActionTitle(action) + '</h3>\
                    </div>\
                    <div class="action-content"></div>\
                    <div class="action-footer">\
                        <h4 class="leadname"><span class="avatar"><img src="' + action.lead.avatar + '" /></span> ' + action.lead.lead_name + ' | Company Name</h4>\
                        <h4 class="username"><span class="avatar"><img src="' + action.user.avatar + '" /></span> ' + action.user.user_name + '</h4>\
                    </div>\
                </div>\
            </div>\
            ').data('action', action)
                .attr('id', 'action-' + action.id)
                .attr('data-action-id', action.id)
                .attr('data-action-module-class', action.module_class)
                .attr('data-action-class', action.class);

        fillAction($action);

        $action.find('.acion-inner').append('<span class="mark"><span class="icon">' + ActionMarkIcons[action.class] + '</span></span>');

        return $action;
    }

    function generateActionTitle(action) {
        var title = ActionTitles[action.class];
        var params = {
            lead_name: action.lead.lead_name,
            user_name: action.user.user_name
        };
        switch (action.class) {
            case ActionClasses.LEAD_STAGE_MOVE:
//                params.from_stage_name = action.values.from_stage_name;
//                params.to_stage_name = action.values.to_stage_name;
                break;
            case ActionClasses.WORKFLOW_ADD:
                params.workflow_name = action.values.workflow_name;
                break;
        }
        $.each(params, function (name, value) {
            title = title.replace(new RegExp('\\{' + name + '\\}'), value);
        });
        return title;
    }

    function fillAction($action) {
        var action = $action.data('action');
        var $content = $action.find('.action-content');
        switch (action.class) {
            case ActionClasses.LEAD_STAGE_MOVE:
                $content.append('\
                    <p class="message">' + action.values.from_stage_name + ' to ' + action.values.to_stage_name + '</p>\
                ');
                break;
            case ActionClasses.LEAD_ACCEPT:
                $content.append('\
                    <label class="source-info">Twitter, ' + action.values.source_info.location + ', ' + action.values.source_info.range + ' miles, ' + action.values.source_info.keywords + '</label>\
                    <div class="twitter-info">\
                        <span class="twitter-info-avatar"><img src="' + action.values.twitter_info.sender.avatar + '" /></span>\
                        <div class="twitter-info-header">\
                            <h4 class="twitter-info-fullname">' + action.values.twitter_info.sender.fullname + '</h4>\
                            <span class="twitter-info-username">@' + action.values.twitter_info.sender.username + '</span>\
                        </div>\
                        <div class="twitter-info-description">' + action.values.twitter_info.description + '</div>\
                    </div>\
                ');
                break;
            case ActionClasses.WORKFLOW_ADD:
                break;
            case ActionClasses.TASK_ADD:
                $content.append('\
                    <p class="message">' + action.values.task_desc + '</p>\
                    <label class="task-info"><span class="task-status' + (action.values.task_status ? ' task-status-completed' : '') + '"></span> ' + getDateTimeString(action.values.task_te) + ' ' + (action.values.task_status ? 'Completed' : 'Due') + '</label>\
                ');
                break;
            case ActionClasses.NOTE_ADD:
                $content.append('\
                    <p class="message">' + action.values.note_desc + '</p>\
                ');
                break;
            case ActionClasses.EMAIL_SEND:
                $content.append('\
                    <div class="subject"><label>Subject: </label><span>' + action.values.title + '</span></div>\
                    <div class="from"><label>From: </label><span>' + action.values.from + '</span></div>\
                    <div class="to"><label>To: </label><span>' + action.values.to + '</span></div>\
                    <p class="message">' + action.values.msg + '</p>\
                    <div class="action-buttons">\
                        <a data-action="email_reply"><i class="fa fa-reply"></i></a>\
                        <a data-action="email_forward"><i class="fa fa-share"></i></a>\
                    </div>\
                ').on('click', 'a[data-action]', function (e) {
                    var $action = $(this).closest('.action');
                    var action = $(this).attr('data-action');
                    if (action == 'email_reply' || action == 'email_forward') {
                        var $form = $action.find('form.mail-action-form');
                        if (!$form.length) {
                            $form = $createMailForm().hide().appendTo($action);
                            $form.find('.bootstrap-text-editor').wysiwyg({toolbarSelector: $form.find('[data-role="editor-toolbar"]')});
                            $form.find('.btn').addClass('btn-xs');
                        }
                        $form.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
                        $form.find('input[name="name"]').focus();
                    }

                    return e.preventDefault();
                });
                break;
            case ActionClasses.TWITTER_TWEET:
            case ActionClasses.TWITTER_REPLY:
                $content.append('\
                    <div class="twitter-info">\
                        <span class="twitter-info-avatar"><img src="' + action.values.sender.avatar + '" /></span>\
                        <div class="twitter-info-header">\
                            <h4 class="twitter-info-fullname">' + action.values.sender.fullname + '</h4>\
                            <span class="twitter-info-username">@' + action.values.sender.username + '</span>\
                        </div>\
                        <div class="twitter-info-description">' + action.values.description + '</div>\
                    </div>\
                    <div class="action-buttons">\
                        <a data-action="twitter_reply"><i class="fa fa-reply"></i></a>\
                        <a data-action="twitter_retweet"><i class="fa fa-retweet"></i></a>\
                    </div>\
                ').on('click', 'a[data-action]', function (e) {
                    var $action = $(this).closest('.action');
                    var action = $(this).attr('data-action');
                    if (action == 'twitter_reply' || action == 'twitter_retweet') {
                        var $form = $action.find('form.mail-action-form');
                        if (!$form.length) {
                            $form = $createTweetForm().hide().appendTo($action);
                        }
                        $form.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
                        $form.find('textarea[name="description"]').focus();
                    }

                    return e.preventDefault();
                });
                break;
            case ActionClasses.TWITTER_RETWEET:
                break;
            case ActionClasses.TWITTER_FOLLOW:
                $content.append('\
                    <p class="message">\
                        @lead_handle followed <span class="twitter-info-username">@' + action.user.user_name + '</span>\
                    </p>\
                    <div class="action-buttons">\
                        <a data-action="btn btn-blue" class="button">Follow</a>\
                    </div>\
                ');
                break;
        }

        return $action;
    }

    function $createMailForm() {
        return $('\
            <form class="form action-form mail-action-form">\
                <div class="row">\
                    <div class="col-md-12">\
                        <div class="form-group">\
                            <input class="form-control" name="name" placeholder="Subject" type="text">\
                        </div>\
                    </div>\
                </div>\
                <div class="row">\
                    <div class="col-md-12">\
                        <input class="form-control mail-to" readonly placeholder="To:" type="text">\
                    </div>\
                </div>\
                <div class="form-group">\
                    <div class="bootstrap-text-editor form-control mail-description" data-placeholder="Message"></div>\
                </div>\
                <div class="row">\
                    <div class="col-md-12">\
                    ' + getEditableWYSIHTML5OptionsHtml() + '\
                    </div>\
                </div>\
                <div class="row">\
                    <div class="col-md-12 text-right">\
                        <button class="btn btn-default btn-cancel" type="button">Cancel</button>\
                        <button class="btn btn-primary btn-save">Submit</button>\
                    </div>\
                </div>\
            </form>\
        ').on('click', 'button.btn-cancel', function () {
            $(this).closest('form').fadeOut({duration: 500, queue: false}).slideUp(500);
        });
    }

    function $createTweetForm() {
        return $('\
            <form class="form action-form twitter-message-form" data-event-type="twitter_direct" data-role="default">\
                <label for="msg" class="twitter-message-username"></label>\
                <textarea class="form-control twitter-message" placeholder="Message ..." name="description" rows="5" maxlength="10000"></textarea>\
                <div class="twitter-message-form-buttons">\
                    <span class="left-letter-count">140</span>\
                    <button class="btn btn-primary btn-save">Send</button>\
                    <button class="btn btn-default btn-cancel" type="button">Cancel</button>\
                </div>\
            </form>\
        ').on('click', 'button.btn-cancel', function () {
            $(this).closest('form').fadeOut({duration: 500, queue: false}).slideUp(500);
        });
    }

    return scope;
})();