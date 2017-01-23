var Workflow = (function () {
    var scope = {};

    var WorkflowModuleClasses = {
        TWITTER: 'twitter',
        EMAIL: 'email',
        EVENT_TIME: 'event_time',
        PHYSICALMAIL: 'physicalmail',
    };
    var WorkflowClasses = {
        TWITTER_TWEET: 'twitter_tweet',
        TWITTER_REPLY: 'twitter_reply',
        TWITTER_RETWEET: 'twitter_retweet',
        TWITTER_FOLLOW: 'twitter_follow',
        EMAIL_SEND: 'email_send',
        EVENT_TIME_DELAY: 'event_time_delay',
        PHYSICALMAIL_POSTCARD: 'physicalmail_postcard',
        EMAIL_VIDEO: 'email_video'
    };

    var WorkCreationWizardSteps = {
        WORK_TYPE_CHOOSER: 'work-type-chooser',
        TWITTER_WORK_TYPE_CHOOSER: 'twitter-work-type-chooser',
        WORK_PANE_TWITTER_TWEET: 'work-pane-twitter-tweet',
        WORK_PANE_TWITTER_REPLY: 'work-pane-twitter-reply',
        WORK_PANE_TWITTER_RETWEET: 'work-pane-twitter-retweet',
        WORK_PANE_TWITTER_FOLLOW: 'work-pane-twitter-follow',
        WORK_PANE_EMAIL_SEND: 'work-pane-email-send',
        WORK_PANE_EVENT_TIME_DELAY: 'work-pane-event-time-delay',
        WORK_PANE_PHYSICALMAIL_POSTCARD: 'work-pane-physicalmail-postcard',
        WORK_PANE_EMAIL_VIDEO: 'work-pane-email-video'
    };

    var WorkCreationWizardStepTitles = {};
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.WORK_TYPE_CHOOSER] = '<i class="fa fa-cubes"></i> Add Workflow Item';
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.TWITTER_WORK_TYPE_CHOOSER] = '<i class="fa fa-cliently-twitter"></i> Add Twitter Item';
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.WORK_PANE_TWITTER_TWEET] = '<i class="fa fa-cliently-twitter"></i> Twitter - Tweet';
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.WORK_PANE_TWITTER_REPLY] = '<i class="fa fa-reply"></i> Twitter - Reply';
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.WORK_PANE_TWITTER_RETWEET] = '<i class="fa fa-retweet"></i> Twitter - Retweet';
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.WORK_PANE_TWITTER_FOLLOW] = '<i class="fa fa-user-plus"></i> Twitter - Follow';
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.WORK_PANE_EMAIL_SEND] = '<img src="/images/gmail-icon.png" /> Send Email';
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.WORK_PANE_EVENT_TIME_DELAY] = '<i class="fa fa-clock-o"></i> Time Delay';
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.WORK_PANE_PHYSICALMAIL_POSTCARD] = '<img src="/images/postcard_orig.png" /> Postcard';
    WorkCreationWizardStepTitles[WorkCreationWizardSteps.WORK_PANE_EMAIL_VIDEO] = '<i class="fa fa-video-camera"></i> Video Message';

    var $wizard = null;
    var $saveButton = null;
    var $delButton = null;

    var step = null;
    var workflowClass = null;
    var workflowId = null;
    var isFromLead = false;
    var workId = null;
    var position = null;
    var isCompleted = null;

    var $optionsPopup = null;

    var $twitterIntegrator = null;
    var $mailIntegrator = null;
    var $googleIntegrator = null;

    var EmailTokens = {
        FIRST_NAME: 'FirstName',
        LAST_NAME: 'LastName',
        FULL_NAME: 'FullName',
        COMPANY_NAME: 'CompanyName'
    };
    var EmailTokenLabels = {};
    EmailTokenLabels[EmailTokens.FIRST_NAME] = 'First Name';
    EmailTokenLabels[EmailTokens.LAST_NAME] = 'Last Name';
    EmailTokenLabels[EmailTokens.FULL_NAME] = 'Full Name';
    EmailTokenLabels[EmailTokens.COMPANY_NAME] = 'Company Name';

    var frontImageTemplates = [
        '/images/templates/ColorfulFront.jpg',
        '/images/templates/GeometricFront.jpeg',
        '/images/templates/HexagonFront.jpeg',
        '/images/templates/LawFront.jpeg',
        '/images/templates/SplatterFront.jpeg',
        '/images/templates/SquareFront.jpeg',
        '/images/templates/ThankNavyFront.jpeg',
        '/images/templates/WordsFront.jpeg',
        '/images/templates/ZigZagFront.jpeg'
    ];
    var backImageTemplates = [
        '/images/templates/BWTop.jpg',
        '/images/templates/ColorfulTop.jpg',
        '/images/templates/GeometricTop.jpg',
        '/images/templates/HexagonTop.jpg',
        '/images/templates/LawTop.jpg',
        '/images/templates/SplatterTop.jpg',
        '/images/templates/SquareTop.jpg',
        '/images/templates/ThankNavyTop.jpg',
        '/images/templates/ZigZagTop.jpg',
    ];

    var postcardFrontImageUploader = null;
    var postcardBackImageUploader = null;

    var recordingPlayer = null;
    var $recordingButton = null;
    var recordedBlob = null;
    var thumbBlob = null;

    var initialized = false;

    var postcardUploadState = 0;
    var videoUploadState = 0;
    var videoUploadTimeout = 30000;

    function init() {
        $(document).on('click', '.workflow .flow-header .switch', function (e) {
            e.stopPropagation();
        });
        $(document).on('change', '.workflow .flow-header .switch input', function () {
            var input = $(this);
            var $workflow = $(this).closest('.workflow');
            var data = {};
            data.is_enabled = this.checked ? 1 : 0;
            var status = this.checked;
            var is_deal_workflow = $workflow.hasClass('workflow-from-lead');
            $.ajax({
                method: 'put',
                url: getApiUrl(is_deal_workflow, $workflow.attr('data-workflow-id')),
                data: data
            }).done(function () {
                if (status && is_deal_workflow) {
                    refreshActionDates($workflow);
                }
            }).fail(function (jqXHR) {
                if (jqXHR.status === 409) {
                    setTimeout(function () {
                        input.attr('checked', false);
                    }, 1000);
                    showErrorMessage('Error', 'Please upgrade your account to add additional lead sources or workflows.');
                }
            });

            $(this).parent().attr('title', this.checked ? 'Click to Pause Workflow.' : 'Click to make Workflow Live.')
                    .tooltip('fixTitle')
                    .tooltip('setContent')
                    .tooltip('show');
            if (this.checked) {
                $(this).closest('.workflow').removeClass('disabled');
                $(this).closest('.source-workflow').find('.btn-workflow-action-add').removeClass('disabled');
            } else {
                $(this).closest('.workflow').addClass('disabled');
                $(this).closest('.source-workflow').find('.btn-workflow-action-add').addClass('disabled');
            }
        }).on('click', '.workflow .work .work-header, .workflow .work > .date, .workflow .work > .estdelivery-date, .workflow .work > .tracking-date', function () {
            var $work = $(this).closest('.work');
            if ($work.hasClass('empty')) {
                return;
            }
            if ($work.hasClass('collapsed')) {
                $work.removeClass('collapsed').find('.work-content').stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            } else {
                $work.addClass('collapsed').find('.work-content').stop().fadeOut({duration: 500, queue: false}).slideUp(500);
            }
        }).on('click', '.workflow button.add-work', function () {
            workflowId = $(this).closest('.workflow').attr('data-workflow-id');
            isFromLead = $(this).closest('.workflow').hasClass('workflow-from-lead');
            workId = null;
            position = $(this).attr('data-work-position');
            isCompleted = false;

            if (DevOptions.debug) {
                popupWorkCreationWizard(WorkCreationWizardSteps.WORK_PANE_EMAIL_VIDEO);
            } else {
                openWorkTypeChooser();
            }
            $wizard.data('workflow-id', workflowId).data('work-id', workId).data('work-position', position);
        }).on('click', '.workflow .work .work-content', function () {
            var $work = $(this).parent();
            workflowId = $(this).closest('.workflow').attr('data-workflow-id');
            isFromLead = $(this).closest('.workflow').hasClass('workflow-from-lead');
            workId = $work.attr('data-work-id');
            position = null;
            isCompleted = $work.hasClass('completed') || ($work.data('work').started_at > 0);

            if (isCompleted) {
                bootbox.alert('Action can not be edited as it has already taken place.');
                return;
            }
            popupWorkCreationWizard(__class2step($work.attr('data-work-class')));
            $wizard.data('workflow-id', workflowId).data('work-id', workId).data('work-position', position);
        }).on('click', '.workflow .work .work-content video', function (event) {
            event.stopPropagation();
        }).on('click', '.workflow .flow-options-popup-toggle', function () {
            workflowId = $(this).closest('.workflow').attr('data-workflow-id');
            isFromLead = $(this).closest('.workflow').hasClass('workflow-from-lead');
            popupWorkflowOptions();
        }).on('shown.bs.modal', '#work-creation-wizard.modal', function () {
            onAfterWorkCreationWizardPopup();
        }).on('hide.bs.modal', '#work-creation-wizard.modal', function () {
            onHideWorkCreationWizardPopup();
        }).on('click', '#work-creation-wizard.modal ul.work-type-list li', function () {
            popupWorkCreationWizard($(this).data('next-step'));
        }).on('click', '#work-creation-wizard.modal .work-creation-wizard-step[data-step="work-pane-twitter-follow"] button.btn-follow', function () {
            var $this = $(this);
            $this.closest('form').find('input[name="follow"]').val($this.hasClass('unfollow') ? 'false' : 'true');
            $this.parent().find('button').removeClass('active');
            $this.addClass('active');
        }).on('keyup', '#work-creation-wizard.modal form.twitter-message-form textarea.twitter-message', function () {
            var $this = $(this);
            setTimeout(function () {
                var val = $this.val();
                var len = val.length;
                if (navigator.userAgent.indexOf('NT') > -1) {
                    var lines = val.match(/\r?\n|\r/g);
                    if (lines) {
                        len += lines.length;
                    }
                }
                var $left = $this.closest('.twitter-message-form').find('.left-letter-count');
                var left = $left.data('maxlen') - len;
                if (left < 0) {
                    left = 0;
                }
                $left.html(left);
            });
        }).on('submit', '#work-creation-wizard.modal form', function () {
            saveWork($(this));
            return false;
        }).on('click', '#work-creation-wizard.modal button.btn-save', function () {
            $wizard.find('.work-creation-wizard-step[data-step="' + step + '"] form').submit();
        }).on('click', '#work-creation-wizard.modal button.btn-delete', function () {
            deleteWork();
        }).on('click', '#work-creation-wizard.modal button.btn-backward', function () {
            switch (step) {
                case WorkCreationWizardSteps.TWITTER_WORK_TYPE_CHOOSER:
                    openWorkTypeChooser();
                    break;
                case WorkCreationWizardSteps.WORK_PANE_TWITTER_TWEET:
                case WorkCreationWizardSteps.WORK_PANE_TWITTER_REPLY:
                case WorkCreationWizardSteps.WORK_PANE_TWITTER_RETWEET:
                case WorkCreationWizardSteps.WORK_PANE_TWITTER_FOLLOW:
                    openTwitterWorkTypeChooser();
                    break;
                case WorkCreationWizardSteps.WORK_PANE_EMAIL_VIDEO:
                    stopRecordingPlayer();
                    openWorkTypeChooser();
                    break;
                default:
                    openWorkTypeChooser();
            }
        }).on('shown.bs.modal', '#workflow-options-popup', function () {
            var workflow = $('.workflow[data-workflow-id="' + workflowId + '"]').data('workflow');
            $.each(workflow.stop_on_respond, function (i, val) {
                if (val == 'all') {
                    $optionsPopup.find('form.workflow-options-form input:checkbox[value="all"]').iCheck('check').trigger('ifChanged');
                    return false;
                } else {
                    $optionsPopup.find('form.workflow-options-form input:checkbox[value="' + val + '"]').iCheck('check');
                }
            });
        }).on('submit', '#workflow-options-popup form.workflow-options-form', function () {
            saveOptions($(this));
            return false;
        }).on('click', '#workflow-options-popup button.btn-save', function () {
            $optionsPopup.find('form.workflow-options-form').submit();
        }).on('ifChanged', '#workflow-options-popup form.workflow-options-form input:checkbox', function () {
            if (this.value == 'all') {
                if (this.checked) {
                    $(this).closest('label').next().find('input:checkbox').iCheck('check').iCheck('disable');
                } else {
                    $(this).closest('label').next().find('input:checkbox').iCheck('uncheck').iCheck('enable');
                }
            }
        });

        initialized = true;
    }

    scope.$createWorkflow = function (workflow, expand) {
        if (!initialized) {
            init();
        }

        return $createWorkflow(workflow, expand);
    };

    function $createWorkflow(workflow, expand) {
        var $workflow = $('<div class="workflow' + (workflow.is_enabled ? '' : ' disabled') + '" id="workflow-' + workflow.id + '" data-workflow-id="' + workflow.id + '"></div>').data('workflow', workflow);
        workflow.$element = $workflow;

        if (typeof workflow.frld == 'undefined') {
            workflow.frld = false;
        }

        if (workflow.frld) {
            $workflow.addClass('workflow-from-lead');
        } else {
            //$workflow.addClass('vh60');
        }

        if (!workflow.actions || !workflow.actions.length) {
            return $workflow.append('<button class="flow-add-button add-work"><i class="fa fa-plus"></i> Add' + (workflow.frld ? ' Custom' : '') + ' Workflow</button>');
        } else {
            $workflow.addClass('workflow-has-actions');
        }

        if (!workflow.is_enabled) {
            $workflow.addClass('disabled');
        }

        var $workflowInner = $('<div class="workflow-inner" />').appendTo($workflow);
        $workflowInner.append('\
            <div class="flow-header">\
                <div class="flow-options-popup-toggle">\
                    <i class="fa fa-gear"></i> Settings\
                    <label class="switch" data-toggle="tooltip" data-container="body" data-placement="top" title="' + (workflow.is_enabled ? 'Click to Pause Workflow.' : 'Click to make Workflow Live.') + '">\
                        <input type="checkbox"' + (workflow.is_enabled ? ' checked="checked"' : '') + '>\
                        <span data-on="On" data-off="Off"></span>\
                        <i></i>\
                    </label>\
                </div>\
                <h3 data-name="name" data-pk="' + workflow.id + '">' + (workflow.name ? workflow.name : 'Customer Workflow') + '</h3>\
            </div>\
        ').find('[data-toggle="tooltip"]').tooltip();

        if (!workflow.frld) {
            $workflowInner.prepend('<div class="flow-beginning-mark"><h3>Begin<br />Workflow</h3><img src="/images/arrow-flow.png"></div>');
            $workflowInner.find('.flow-header h3[data-name]').editable({
                validate: function (value) {
                    if ($.trim(value) == '')
                        return 'Enter workflow name.';
                },
                mode: 'popup',
                title: 'Workflow Name',
                placement: 'bottom',
                url: '/api/v1/workflows/' + workflow.id,
            });
        }

        var first_work = null;
        $.each(workflow.actions, function (index, work) {
            work.workflow = workflow;
            if (workflow.frld) {
                work.day = calculateDateSpan(first_work, work);
                work.show_day = !first_work || first_work.day < work.day;
                if (first_work === null) {
                    first_work = work;
                }
            }
            if (expand) {
                $createWork(work).appendTo($workflowInner);
            } else {
                $createWork(work).appendTo($workflowInner).addClass('collapsed').find('.work-content').hide();
            }
        });

        $workflowInner.sortable({
            items: '.work',
            zIndex: 2,
            revert: 150,
            forcePlaceholderSize: true,
            helper: 'clone',
            delay: 100,
            distance: 5,
            opacity: 0.85,
            update: function (event, ui) {
                updatePosition(ui.item);
            }
        });

        return $workflow;
    }

    function $createWork(work) {
        if (!work.position) {
            work.position = 1;
        }
        var $work = null;
        switch (work.module_class) {
            case WorkflowModuleClasses.TWITTER:
                $work = $createWorkTwitter(work);
                break;
            case WorkflowModuleClasses.EMAIL:
                $work = $createWorkEmail(work);
                break;
            case WorkflowModuleClasses.EVENT_TIME:
                $work = $createWorkEventTime(work);
                break;
            case WorkflowModuleClasses.PHYSICALMAIL:
                $work = $createWorkPhysicalMail(work);
                break;
        }

        $work.find('.work-footer')
                .append('<div class="plus-icon-wrapper">\
                            <span class="line start"></span>\
                            <button class="flow-add-button-small add-work" data-work-position="' + work.position + '"><img src="/images/plus.png" alt="Add Workflow" /></button>\
                            <span class="line end"></span>\
                        </div>');
        if (work.workflow.frld) {
            var completed = work.executed_at > 0;
            if (completed) {
                $work.addClass('completed');
            }
            $work.append('<span class="status"><span class="point"></span></span>')
                .append('<span class="date">' + (!completed ? 'Due ' + (work.due_at ? getDateTimeString(work.due_at) : '') : getDateTimeString(work.executed_at)) + '</span>');

            if (work.class === WorkflowClasses.PHYSICALMAIL_POSTCARD) {
                $work.append('<span class="estdelivery-date"></span>');
                if (work.extra != null && work.extra.est_delivery_at != null && work.extra.est_delivery_at != 0) {
                    $work.find('.estdelivery-date').html('Est delivery: ' + getDateString(work.extra.est_delivery_at));
                }

                $work.append('<span class="tracking-date"></span>');
                if (work.extra != null && work.extra.tracking_events != null && work.extra.tracking_events.length > 0) {
                    var lastEvent = work.extra.tracking_events[work.extra.tracking_events.length - 1];
                    $work.find('.tracking-date').html(lastEvent[0] + ': ' + getDateTimeString(lastEvent[1]));
                }
            }
            $work.append('<span class="day">' + (work.show_day ? 'Day ' + work.day : '') + '</span>');

            
        }
        return $work.data('work', work)
                .attr('id', 'work-' + work.id)
                .attr('data-work-id', work.id)
                .attr('data-work-position', work.position)
                .attr('data-work-module-class', work.module_class)
                .attr('data-work-class', work.class);
    }

    function $createWorkTwitter(work) {
        var title = null;
        switch (work.class) {
            case WorkflowClasses.TWITTER_TWEET:
                title = 'Tweet';
                break;
            case WorkflowClasses.TWITTER_REPLY:
                title = 'Reply to Tweet';
                break;
            case WorkflowClasses.TWITTER_RETWEET:
                title = 'Reweet';
                break;
            case WorkflowClasses.TWITTER_FOLLOW:
                title = 'Follow';
                break;
        }
        var $work = $('<div class="work">\
                    <div class="work-header">\
                        <span class="icon"><i class="fa fa-cliently-twitter"></i></span>\
                        <h3>' + title + '</h3>\
                        <span class="toggle"><i class="fa"></i></span>\
                    </div>\
                    <div class="work-content"></div>\
                    <div class="work-footer">\
                    </div>\
                </div>');
        var $content = $work.find('.work-content');
        switch (work.class) {
            case WorkflowClasses.TWITTER_TWEET:
            case WorkflowClasses.TWITTER_REPLY:
                $content.append('<p class="tweet-entry">' + work.values.msg + '</p>');
                if (work.workflow.frld && work.workflow.lead.integrations && work.workflow.lead.integrations.twitter) {
                    $content.find('p.tweet-entry').prepend('<span class="twitter-handle">@' + work.workflow.lead.integrations.twitter.username + ' </span>');
                }
                break;
            case WorkflowClasses.TWITTER_RETWEET:
                $content.append('<label>Retweet</label>');
                break;
            case WorkflowClasses.TWITTER_FOLLOW:
                $content.append('<label>' + (work.values.follow === 'true' ? 'Follow' : 'Unfollow') + '</label>');
                break;
        }

        return $work;
    }

    function $createWorkEventTime(work) {
        return $('<div class="work">\
                    <div class="work-header">\
                        <span class="icon"><i class="fa fa-clock-o"></i></span>\
                        <h3>Wait ' + work.values.type_value + ' Days </h3>\
                        <span class="toggle"><i class="fa"></i></span>\
                    </div>\
                    <div class="work-content">\
                        <label class="time-delay-notice">Wait <span class="days">' + work.values.type_value + '</span> days</label>\
                    </div>\
                    <div class="work-footer">\
                    </div>\
                </div>');
    }

    function $createWorkEmail(work) {
        if (work.class == WorkflowClasses.EMAIL_SEND) {
            return $('<div class="work">\
                    <div class="work-header">\
                        <span class="icon"><img src="/images/gmail-icon.png" /></span>\
                        <h3>Send Email</h3>\
                        <span class="toggle"><i class="fa"></i></span>\
                    </div>\
                    <div class="work-content">\
                        <div class="title">' + (work.values.title) + '</div>\
                        <div class="message">' + (work.values.msg) + '</div>\
                    </div>\
                    <div class="work-footer">\
                    </div>\
                </div>');
        } else if (work.class == WorkflowClasses.EMAIL_VIDEO) {
            var s3_upload_path = ADDPIPE_BUCKET_URL;
            var video_html;
            if (typeof work.values.video === 'string' && work.values.video.substring(0, s3_upload_path.length) === s3_upload_path) {
                video_html = '<video controls src="' + work.values.video + '"></video>';
            } else {
                video_html = '<video controls src="' + (work.values.video ? '/uploads/actions/videos/' + work.values.video + '.mp4' : '') + '"></video>';
            }

            return $('<div class="work">\
                    <div class="work-header">\
                        <span class="icon">\
                            <i class="fa fa-video-camera"></i>\
                        </span>\
                        <h3>Video Message</h3>\
                        <span class="toggle"><i class="fa"></i></span>\
                    </div>\
                    <div class="work-content">\
                        <div class="title">' + (work.values.title) + '</div>\
                        <div class="media">' + video_html + '</div>\
                        <div class="message">' + (work.values.msg) + '</div>\
                    </div>\
                    <div class="work-footer">\
                    </div>\
                </div>');
        }
    }

    function $createWorkPhysicalMail(work) {
        var $work = $('<div class="work">\
                    <div class="work-header">\
                        <span class="icon"><img src="/images/postcard_orig.png" /></span>\
                        <h3>Postcard</h3>\
                        <span class="toggle"><i class="fa"></i></span>\
                    </div>\
                    <div class="work-content">\
                        <div class="media"><img /></div>\
                        <!--div class="address"></div-->\
                        <div class="message">' + (work.values.message.replace(/(?:\r\n|\r|\n)/g, '<br />')) + '</div>\
                    </div>\
                    <div class="work-footer">\
                    </div>\
                </div>');
        if (work.values.front && work.values.front !== '' && work.values.front !== 'false') {
            $work.find('div.media img').attr('src', '/uploads/actions/postcards/fronts/' + work.values.front + '.jpg');
        } else if (work.values.front_tpl && work.values.front_tpl !== '' && work.values.front_tpl !== 'false') {
            $work.find('div.media img').attr('src', frontImageTemplates[work.values.front_tpl - 1]);
        }
        return $work;

    }

    function openWorkTypeChooser() {
        popupWorkCreationWizard(WorkCreationWizardSteps.WORK_TYPE_CHOOSER);
    }

    function openTwitterWorkTypeChooser() {
        popupWorkCreationWizard(WorkCreationWizardSteps.TWITTER_WORK_TYPE_CHOOSER);
    }

    function popupWorkCreationWizard(st) {
        if (!$wizard) {
            $wizard = $('<div id="work-creation-wizard" class="modal fade" role="dialog">\
                            <div class="modal-dialog">\
                                <div class="modal-content">\
                                    <div class="modal-header">\
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>\
                                        <h4 class="modal-title"></h4>\
                                    </div>\
                                    <div class="modal-body"></div>\
                                    <div class="modal-footer">\
                                        <div class="left">\
                                            <button class="btn btn-rounded btn-backward">Back</button>\
                                            <div class="social-integration-handler-container"></div>\
                                        </div>\
                                        <button type="button" class="btn btn-rounded btn-primary btn-save"><i class="fa fa-save"></i> Save</button>\
                                        <button type="button" class="btn btn-rounded btn-default btn-delete">Delete</button>\
                                        <button type="button" class="btn btn-rounded btn-danger" data-dismiss="modal">Close</button>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>');
            $saveButton = $wizard.find('button.btn-save');
            $delButton = $wizard.find('button.btn-delete');
        }

        $wizard.attr('data-step', st);
        step = st;

        $wizard.find('.modal-header .modal-title').html(WorkCreationWizardStepTitles[step]);

        var $step = $wizard.find('.work-creation-wizard-step[data-step="' + step + '"]');
        if (!$step.length) {
            $step = $createWorkCreationWizardStep(step).appendTo($wizard.find('.modal-body'));
            initWorkCreationWizardStep($step);
        }

        switch (step) {
            case WorkCreationWizardSteps.WORK_PANE_PHYSICALMAIL_POSTCARD:
                $step.find('.nav-tabs a[href="#work-creation-wizard-step-work-pane-physicalmail-tab-pane-front"]').tab('show');
                $step.find('.tab-pane').removeClass('left-pane-open');
                if (workId > 0) {
                    $step.find('.nav-tabs > .preview').show();
                } else {
                    $step.find('.nav-tabs > .preview').hide();
                }
                break;
            case WorkCreationWizardSteps.WORK_PANE_EMAIL_VIDEO:
                var token_type = isFromLead ? 2 : 1;
                var token_type_id = workId > 0 ? workId : 0;
                createToken(token_type, token_type_id, function(token) {
                    $step.find('input[name="token_value"]').val(token.value);

                    $step.find('.video-recorder-wrapper #VideoRecorder').remove();
                    $step.find('.video-recorder-wrapper').prepend('<div id="hdfvr-content" ></div>');

                    size = {
                        width:320,
                        height:270
                    };
                    flashvars = {
                        qualityurl: "avq/240p.xml",
                        accountHash: ADDPIPE_PK,
                        eid: ADDPIPE_ENV_ID,
                        showMenu: "true",
                        mrt: 120,
                        sis: 0, asv: 1,
                        payload: token.value,
                    };
                    (function() {var pipe = document.createElement("script"); pipe.type = "text/javascript"; pipe.async = true;pipe.src = ("https:" == document.location.protocol ? "https://" : "http://") + "s1.addpipe.com/1.3/pipe.js";var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(pipe, s);})();

                })
                break;
        }

        workflowClass = __step2class(step);

        if (workflowClass.indexOf(WorkflowModuleClasses.TWITTER) === 0) {
            if (!$twitterIntegrator) {
                $twitterIntegrator = SocialIntegrator.$createTwitterIntegration().hide();
                $wizard.find(".social-integration-handler-container").append($twitterIntegrator);
            }
            if (!$twitterIntegrator.is(':visible')) {
                SocialIntegrator.reloadIntegration($twitterIntegrator.show());
            }
        } else if ($twitterIntegrator) {
            $twitterIntegrator.hide();
        }

        if (workflowClass.indexOf(WorkflowModuleClasses.EMAIL) === 0) {
            if (!$mailIntegrator) {
                $mailIntegrator = SocialIntegrator.$createMailIntegration().hide();
            }
            if (!$googleIntegrator) {
                $googleIntegrator = SocialIntegrator.$createGoogleIntegration().hide();
            }
            $step.find(".mail-n-google-integration-handler-container").append($googleIntegrator);
            if (!$googleIntegrator.is(':visible')) {
                var $xhr = SocialIntegrator.reloadIntegration($googleIntegrator.show());
                if ($xhr) {
                    $xhr.done(function(data) {
                        if (data && data.integration) {
                            $googleIntegrator.show()
                            $mailIntegrator.hide();
                        }
                    })
                }
            }
            $step.find(".mail-n-google-integration-handler-container").append($mailIntegrator);
            if (!$googleIntegrator.is(':visible')) {
                var $xhr = SocialIntegrator.reloadIntegration($mailIntegrator.show());
                if ($xhr) {
                    $xhr.done(function(data) {
                        if (data && data.integration) {
                            $googleIntegrator.hide()
                            $mailIntegrator.show();
                        }
                    })
                }
            }
//        } else if ($googleIntegrator) {
//            $googleIntegrator.hide();
        }

        resetWorkForm($step.find('form.work-form'));

        if ($wizard.is(':visible')) {
            $wizard.find('.work-creation-wizard-step:visible').hide();//.stop().fadeOut({duration: 500, queue: false}).slideUp(500);
            $step.show();//.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
        } else {
            $wizard.find('.work-creation-wizard-step').hide();
            $step.show();
            $wizard.modal('show');
        }

        if (workId) {
            $wizard.removeClass('adding-new-work').addClass('updating-work');
            $saveButton.attr('disabled', isCompleted).html('<i class="fa fa-save"></i> Save');
            $delButton.attr('disabled', isCompleted).html('Delete').show();
        } else {
            $wizard.removeClass('updating-work').addClass('adding-new-work');
            $saveButton.attr('disabled', false).html('<i class="fa fa-save"></i> Add');
            $delButton.attr('disabled', true).hide();
        }

        var $input = $step.find('input,textarea,select');
        if ($input.length) {
            $($input[0]).focus().select();
        }
    }

    function closeWizardWithDelay() {
        setTimeout(function () {
            $wizard.modal('hide');
        }, 100);

    }

    function $createWorkCreationWizardStep(st) {
        var $step = $('<div />', {
            class: 'work-creation-wizard-step',
            id: 'work-creation-wizard-step-' + st,
            'data-step': st
        });

        switch (st) {
            case WorkCreationWizardSteps.WORK_TYPE_CHOOSER:
                $step.append('<ul class="work-type-list">\
                                <li data-work-module-class="' + WorkflowModuleClasses.TWITTER + '" data-next-step="' + WorkCreationWizardSteps.TWITTER_WORK_TYPE_CHOOSER + '">\
                                    <span class="icon"><i class="fa fa-cliently-twitter"></i></span>\
                                    <label>Twitter</label>\
                                </li>\
                                <li data-work-module-class="' + WorkflowModuleClasses.EMAIL + '" data-next-step="' + WorkCreationWizardSteps.WORK_PANE_EMAIL_SEND + '">\
                                    <span class="icon"><img src="/images/gmail-icon.png" /></span>\
                                    <label>Email</label>\
                                </li>\
                                <li data-work-module-class="' + WorkflowModuleClasses.EVENT_TIME + '" data-next-step="' + WorkCreationWizardSteps.WORK_PANE_EVENT_TIME_DELAY + '">\
                                    <span class="icon"><i class="fa fa-clock-o"></i></span>\
                                    <label>Time Delay</label>\
                                </li>\
                                <li data-work-module-class="' + WorkflowModuleClasses.PHYSICALMAIL + '" data-next-step="' + WorkCreationWizardSteps.WORK_PANE_PHYSICALMAIL_POSTCARD + '">\
                                    <span class="icon"><img src="/images/postcard_orig.png" /></span>\
                                    <label>Postcard</label>\
                                </li>\
                                <li data-work-class="' + WorkflowClasses.EMAIL_VIDEO + '" data-next-step="' + WorkCreationWizardSteps.WORK_PANE_EMAIL_VIDEO + '">\
                                    <span class="icon"><i class="fa fa-video-camera"></i></span>\
                                    <label>Video Message</label>\
                                </li>\
                            </ul>');
                break;
            case WorkCreationWizardSteps.TWITTER_WORK_TYPE_CHOOSER:
                $step.append('<ul class="work-type-list">\
                                <!--<li data-work-class="' + WorkflowClasses.TWITTER_TWEET + '" data-next-step="' + WorkCreationWizardSteps.WORK_PANE_TWITTER_TWEET + '">\
                                    <span class="icon"><i class="fa fa-cliently-twitter"></i></span>\
                                    <label>Tweet</label>\
                                </li>\
                                <li data-work-class="' + WorkflowClasses.TWITTER_REPLY + '" data-next-step="' + WorkCreationWizardSteps.WORK_PANE_TWITTER_REPLY + '">\
                                    <span class="icon"><i class="fa fa-reply"></i></span>\
                                    <label>Reply</label>-->\
                                </li>\
                                <li data-work-class="' + WorkflowClasses.TWITTER_RETWEET + '" data-next-step="' + WorkCreationWizardSteps.WORK_PANE_TWITTER_RETWEET + '">\
                                    <span class="icon"><i class="fa fa-retweet"></i></span>\
                                    <label>Retweet</label>\
                                </li>\
                                <li data-work-class="' + WorkflowClasses.TWITTER_FOLLOW + '" data-next-step="' + WorkCreationWizardSteps.WORK_PANE_TWITTER_FOLLOW + '">\
                                    <span class="icon"><i class="fa fa-user-plus"></i></span>\
                                    <label>Follow</label>\
                                </li>\
                            </ul>');
                break;
            case WorkCreationWizardSteps.WORK_PANE_TWITTER_TWEET:
                $step.append('<form class="form work-form twitter-message-form">\
                                <label>\
                                    <span class="twitter-message-username">@client</span>\
                                    <textarea name="msg" maxlength="127" class="form-control twitter-message">Congrats on your #grand opening!\nDo you have a website up and ready for all your new customers!?</textarea>\
                                    <span class="left-letter-count">127</span>\
                                </label>\
                            </form>');
                break;
            case WorkCreationWizardSteps.WORK_PANE_TWITTER_REPLY:
                $step.append('<form class="form work-form twitter-message-form">\
                                <label>\
                                    <span class="twitter-message-username">@client</span>\
                                    <textarea name="msg" maxlength="127" class="form-control twitter-message">Congrats on your #grand opening!\nDo you have a website up and ready for all your new customers!?</textarea>\
                                    <span class="left-letter-count">127</span>\
                                    <p class="note">* The Reply will be towards the initial sourced tweet from Cliently only.</p>\
                                </label>\
                            </form>');
                break;
            case WorkCreationWizardSteps.WORK_PANE_TWITTER_RETWEET:
                $step.append('<form class="form work-form"><button class="btn btn-lg btn-retweet" type="button"><i class="fa fa-retweet"></i> Retweet</button><p class="note">* Retweet will only work for Twitter sourced leads directly from Cliently. Leads not sourced from Cliently will skip this action. The Retweet will be for the initial sourced tweet.</p></form>');
                break;
            case WorkCreationWizardSteps.WORK_PANE_TWITTER_FOLLOW:
                $step.append('<form class="form work-form">\
                                <div class="btn-group">\
                                    <button class="btn btn-primary btn-lg btn-follow follow" type="button"><i class="fa fa-check"></i> Follow</button>\
                                    <button class="btn btn-primary btn-lg btn-follow unfollow" type="button"><i class="fa fa-times"></i> Unfollow</button>\
                                </div>\
                                <input type="hidden" name="follow" value="" />\
                            </form>');
                break;
            case WorkCreationWizardSteps.WORK_PANE_EMAIL_SEND:
                $step.append('<form class="form work-form form-horizontal">\
                                <div class="form-group from-form-group">\
                                    <label class="control-label col-md-2 text-right">From: </label>\
                                    <div class="col-md-10">\
                                        <div class="form-control-static">\
                                            <div class="mail-n-google-integration-handler-container integration-handler-container"></div>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="form-group">\
                                    <label class="control-label col-md-2 text-right">Subject: </label>\
                                    <div class="col-md-10">\
                                        <input type="text" class="form-control email-title can-have-token" title="Subject" name="title" />\
                                    </div>\
                                </div>\
                                <div class="form-group">\
                                    <div class="col-md-12">\
                                        ' + getEditableWYSIHTML5OptionsHtml() + '\
                                       <div class="bootstrap-text-editor form-control email-message can-have-token" data-placeholder="Enter message here"></div>\
                                    </div>\
                                </div>\
                            </form>');
                $step.find('[data-role="editor-toolbar"]').append('\
                    <div class="btn-group dropdown token-dropdown">\
                        <a class="btn dropdown-toggle" title="Insert Token" data-toggle="dropdown">{-}</a>\
                        <ul class="dropdown-menu" role="menu"></ul>\
                    </div>\
                ').end().find('.btn').addClass('btn-xs');
                var $tokens = $step.find('.dropdown-menu');
                $.each(EmailTokens, function (i, token) {
                    $('<li role="presentation"><a data-token="' + token + '" href="#' + token + '">' + EmailTokenLabels[token] + '</a></li>').appendTo($tokens);
                });
                $step.on('click', '.dropdown.token-dropdown .dropdown-menu a[data-token]', function (e) {
                    e.stopPropagation();
                    e.cancelBubble = true;
                    var text = '{' + $(this).attr('data-token') + '}';
                    var $editor = $(this).closest('form').find('.can-have-token.active');
                    if (!$editor.length) {
                        $editor = $(this).closest('.form-group').find('.bootstrap-text-editor.email-message').focus();
                    }
                    if ($editor.is('.email-title')) {
                        insertAtCursor($editor[0], text);
                    } else {
                        document.execCommand('insertHTML', false, text);
                    }
                    $(this).closest('.dropdown.token-dropdown').removeClass('open');
                    $editor.focus();
                    return false;
                }).on('mousedown', '.dropdown.token-dropdown>.dropdown-toggle', function () {
                    $(this).closest('form').find('.can-have-token.active').addClass('keep-active');
                }).on('click', '.dropdown.token-dropdown>.dropdown-toggle', function () {
                    $(this).closest('form').find('.can-have-token.active').removeClass('active');
                    $(this).closest('form').find('.can-have-token.keep-active').removeClass('keep-active').addClass('active').focus();
                }).on('focus', '.can-have-token', function () {
                    $(this).closest('form').find('.can-have-token').removeClass('active');
                    $(this).addClass('active');
                });
                break;
            case WorkCreationWizardSteps.WORK_PANE_EVENT_TIME_DELAY:
                $step.append('<form class="form work-form">\
                                <label class="">Wait <input type="text" name="type_value" class="form-control" size="2" value="3" /> days</label>\
                                <input type="hidden" name="type" value="1" />\
                                <input type="hidden" name="weekends" value="true" />\
                            </form>');
                break;
            case WorkCreationWizardSteps.WORK_PANE_PHYSICALMAIL_POSTCARD:
                $step.append('\
                    <form class="form work-form">\
                        <ul class="nav nav-tabs">\
                            <li class="active"><a data-toggle="tab" href="#work-creation-wizard-step-work-pane-physicalmail-tab-pane-front">Front</a></li>\
                            <li><a data-toggle="tab" href="#work-creation-wizard-step-work-pane-physicalmail-tab-pane-back">Back</a></li>\
                            <li class="preview"><button class="btn btn-rounded btn-primary" type="button" data-toggle="tab" href="#work-creation-wizard-step-work-pane-physicalmail-tab-pane-preview">Preview</a></li>\
                        </ul>\
                        <div class="tab-content">\
                            <div id="work-creation-wizard-step-work-pane-physicalmail-tab-pane-front" class="tab-pane fade active in"></div>\
                            <div id="work-creation-wizard-step-work-pane-physicalmail-tab-pane-back" class="tab-pane fade"></div>\
                            <div id="work-creation-wizard-step-work-pane-physicalmail-tab-pane-preview" class="tab-pane fade"></div>\
                        </div>\
                    </form>\
                ');

                $('.nav-tabs a[href="#work-creation-wizard-step-work-pane-physicalmail-tab-pane-front"]').tab('show');

                var $preview = $step.find('#work-creation-wizard-step-work-pane-physicalmail-tab-pane-preview').append('\
                    <div class="main-pane-wrapper">\
                        <div class="main-pane">\
                            <div class="main-pane-inner">\
                                <div class="main-content">\
                                    <div class="preview-container">\
                                        <img class="preview-front" />\
                                        <img class="preview-back" />\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                ');

                $step.find('.work-form > .nav > .preview').on('click', function () {
                    if (workId > 0) {
                        showGlobalLoader("Preview can take up to 10 seconds to appear");
                        var preview_url = '/api/v1/postcards/preview?' + (isFromLead ? 'deal_action_id=' : 'action_id=') + workId;
                        $.ajax({
                            method: "GET",
                            url: preview_url,
                        }).done(function (ret) {
                            setTimeout(function() {
                                $preview.find('.preview-front').attr('src', ret.front);
                                $preview.find('.preview-back').attr('src', ret.back);
                                hideGlobalLoader();
                            }, 10000);
                        });
                    }
                });

                var $front = $step.find('#work-creation-wizard-step-work-pane-physicalmail-tab-pane-front').append('\
                    <div class="left-pane-wrapper">\
                        <div class="left-pane">\
                            <div class="left-pane-inner">\
                                <a href="#" class="left-pane-toggle"><i class="fa fa-chevron-left"></i> Hide Template</a>\
                                <div class="left-content">\
                                    <div class="template-list-wrapper">\
                                        <ul class="template-list"></ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="main-pane-wrapper">\
                        <div class="main-pane">\
                            <div class="main-pane-inner">\
                                <div class="main-content">\
                                    <div class="front-container">\
                                        <div class="front-image-container">\
                                            <img class="front-image" src="/images/transparent.png" />\
                                            <div class="image-upload-container">\
                                                <img class="image-upload" src="/images/image-upload.png" />\
                                                <a class="btn-image-upload" href="#">Click Here to Upload Image</a>\
                                                <div class="note">Use JPEG or PNG that is 1875px by 1275px</div>\
                                                <div class="template-list-toggle-wrapper">Or<br /><a href="#" class="template-list-toggle">Click Here to Choose from one of our templates</a></div>\
                                            </div>\
                                            <input type="hidden" name="front" value="" />\
                                            <input type="hidden" name="front_tpl" value="" />\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                ');
                $front.find('.template-list-wrapper').slimScroll({
                    height: '100%',
                    size: '3px',
                    color: '#0ab8ff',
                    railVisible: true,
                    railOpacity: 1,
                    railColor: '#eee',
                    opacity: 1
                });
                var $templates = $front.find('.template-list');
                $.each(frontImageTemplates, function (i, template) {
                    $('<li data-index="' + (i + 1) + '"><img src="' + template + '" /></li>').appendTo($templates);
                });
                $front.find('.left-pane-toggle').click(function (e) {
                    $(this).closest('.tab-pane').toggleClass('left-pane-open');
                    return e.preventDefault();
                });
                $front.find('ul.template-list').on('click', '>li', function () {
                    var $this = $(this);
                    if ($this.hasClass('active')) {
                        return;
                    }
                    $this.parent().find('>li.active').removeClass('active');
                    $this.addClass('active');
                    $this.closest('.tab-pane')
                            .find('img.front-image').attr('src', $this.find('>img').attr('src')).end()
                            .find('input[name="front_tpl"]').val($this.attr('data-index')).end()
                            .find('input[name="front"]').val('false').end()
                            .find('.front-image-container').addClass('image-selected');
                });
                $front.find('.template-list-toggle').click(function (e) {
                    $(this).closest('.tab-pane').toggleClass('left-pane-open');
                    return e.preventDefault();
                });

                var $back = $step.find('#work-creation-wizard-step-work-pane-physicalmail-tab-pane-back').append('\
                    <!--a href="#" class="left-pane-toggle"><i class="fa fa-rocket"></i> Show Template</a-->\
                    <div class="left-pane-wrapper">\
                        <div class="left-pane">\
                            <div class="left-pane-inner">\
                                <a href="#" class="left-pane-toggle"><i class="fa fa-chevron-left"></i> Hide Template</a>\
                                <div class="left-content">\
                                    <div class="template-list-wrapper">\
                                        <ul class="template-list"></ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="main-pane-wrapper">\
                        <div class="main-pane">\
                            <div class="main-pane-inner">\
                                <div class="main-content">\
                                    <div class="back-container">\
                                        <input type="hidden" name="has_banner" value="true" />\
                                        <a class="back-image-toggle" href="#"><i class="fa"></i></a>\
                                        <div class="back-image-container-wrapper">\
                                            <div class="back-image-container">\
                                                <img class="back-image" src="/images/transparent.png" />\
                                                <div class="image-upload-container">\
                                                    <img class="image-upload" src="/images/image-upload-gray.png" />\
                                                    <a class="btn-image-upload" href="#">Click Here to Upload Image (1875px x 390px)</a>\
                                                    <div class="template-list-toggle-wrapper">Or<br /><a href="#" class="template-list-toggle">Click Here to Choose from one of our templates</a></div>\
                                                </div>\
                                                <input type="hidden" name="back" value="" />\
                                                <input type="hidden" name="back_tpl" value="" />\
                                            </div>\
                                        </div>\
                                        <div class="card">\
                                            <div class="card-addresses-container">\
                                                <div class="form-control from-address-toggle">Add return address (optional)</div>\
                                                <div class="from-address-wrapper">\
                                                    <div class="row">\
                                                        <div class="col-md-12">\
                                                            <input tabindex="7" type="text" class="form-control input-sm" name="from_name" placeholder="Name" />\
                                                        </div>\
                                                    </div>\
                                                    <div class="row">\
                                                        <div class="col-md-12">\
                                                            <input tabindex="7" type="text" class="form-control input-sm" name="from_company_name" placeholder="Company Name" />\
                                                        </div>\
                                                    </div>\
                                                    <div class="row">\
                                                        <div class="col-md-6">\
                                                            <input tabindex="8" type="text" class="form-control input-sm" name="from_address_line1" placeholder="Address Line 1" />\
                                                        </div>\
                                                        <div class="col-md-6">\
                                                            <input tabindex="9" type="text" class="form-control input-sm" name="from_address_line2" placeholder="Address Line 2" />\
                                                        </div>\
                                                    </div>\
                                                    <div class="row">\
                                                        <div class="col-md-3">\
                                                            <input tabindex="10" type="text" class="form-control input-sm" name="from_city" placeholder="City" />\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <input tabindex="11" type="text" class="form-control input-sm" name="from_state" placeholder="State" />\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <input tabindex="12" type="text" class="form-control input-sm" name="from_zip" placeholder="ZIP" />\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <input tabindex="13" type="text" class="form-control input-sm" name="from_country" placeholder="Country" />\
                                                        </div>\
                                                    </div>\
                                                    <label class="from-address-toggle">I do not want to add a return address</label>\
                                                    <input type="checkbox" class="hidden" name="from" value="1" />\
                                                </div>\
                                                <div class="image-postcard-wrapper">\
                                                    <img src="/images/postcard.png" />\
                                                </div>\
                                                <div class="to-address-toggle">\
                                                    <div class="name">Full Name</div>\
                                                    <div class="company-name">Company Name</div>\
                                                    <div class="addressline1-addressline2">123 Example Dr</div>\
                                                    <div class="ciy-state-zip-country">City, State 12345</div>\
                                                    <div class="note">Address will be populated to your lead card</div>\
                                                </div>\
                                                <div class="to-address-wrapper">\
                                                    <div class="row">\
                                                        <div class="col-md-12">\
                                                            <input tabindex="7" type="text" class="form-control input-sm" name="to_name" placeholder="Name" />\
                                                        </div>\
                                                    </div>\
                                                    <div class="row">\
                                                        <div class="col-md-12">\
                                                            <input tabindex="7" type="text" class="form-control input-sm" name="to_company_name" placeholder="Company Name" />\
                                                        </div>\
                                                    </div>\
                                                    <div class="row">\
                                                        <div class="col-md-6">\
                                                            <input tabindex="8" type="text" class="form-control input-sm" name="to_address_line1" placeholder="Address Line 1" />\
                                                        </div>\
                                                        <div class="col-md-6">\
                                                            <input tabindex="9" type="text" class="form-control input-sm" name="to_address_line2" placeholder="Address Line 2" />\
                                                        </div>\
                                                    </div>\
                                                    <div class="row">\
                                                        <div class="col-md-3">\
                                                            <input tabindex="10" type="text" class="form-control input-sm" name="to_city" placeholder="City" />\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <input tabindex="11" type="text" class="form-control input-sm" name="to_state" placeholder="State" />\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <input tabindex="12" type="text" class="form-control input-sm" name="to_zip" placeholder="ZIP" />\
                                                        </div>\
                                                        <div class="col-md-3">\
                                                            <input tabindex="13" type="text" class="form-control input-sm" name="to_country" placeholder="Country" />\
                                                        </div>\
                                                    </div>\
                                                    <input type="checkbox" class="hidden" name="to" value="1" />\
                                                </div>\
                                            </div>\
                                            <div class="card-message-container-wrapper">\
                                                <div class="card-message-container">\
                                                    <div class="card-message">\
                                                        <textarea id="postcard_message" data-line-limit="15" tabindex="1" name="message" class="can-hav-token-active" placeholder="Type message here..."></textarea>\
                                                    </div>\
                                                    <div class="btn-group dropdown token-dropdown">\
                                                        <a class="btn btn-default btn-xs dropdown-toggle" title="Insert Token" data-toggle="dropdown">{-}</a>\
                                                        <ul class="dropdown-menu dropdown-menu-right" role="menu"></ul>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                ');
                $back.find('#postcard_message').on('input', function() {
                    textareaLineCountResize(this, $(this).data('line-limit'), 14 * 1.42857);
                });
                $back.find('.template-list-wrapper').slimScroll({
                    height: '100%',
                    size: '3px',
                    color: '#0ab8ff',
                    railVisible: true,
                    railOpacity: 1,
                    railColor: '#eee',
                    opacity: 1
                });
                var $templates = $back.find('.template-list');
                $.each(backImageTemplates, function (i, template) {
                    $('<li data-index="' + (i + 1) + '"><img src="' + template + '" /></li>').appendTo($templates);
                });
                $back.find('.left-pane-toggle').click(function (e) {
                    $(this).closest('.tab-pane').toggleClass('left-pane-open');
                    return e.preventDefault();
                });
                $back.find('ul.template-list').on('click', '>li', function () {
                    var $this = $(this);
                    if ($this.hasClass('active')) {
                        return;
                    }
                    $this.parent().find('>li.active').removeClass('active');
                    $this.addClass('active');

                    $this.closest('.tab-pane')
                            .find('img.back-image').attr('src', $this.find('>img').attr('src')).end()
                            .find('input[name="back_tpl"]').val($this.attr('data-index')).end()
                            .find('input[name="back"]').val('false').end()
                            .find('.back-image-container').addClass('image-selected');
                });
                $back.find('.template-list-toggle').click(function (e) {
                    $(this).closest('.tab-pane').toggleClass('left-pane-open');
                    return e.preventDefault();
                });
                $back.find('.back-image-toggle').click(function (e) {
                    var $container = $(this).closest('.back-container').toggleClass('back-image-hide');
                    $container.find('input[name="has_banner"]').val($container.hasClass('back-image-hide') ? 'false' : 'true');
                    $back.find('#postcard_message').data('line-limit', $container.hasClass('back-image-hide') ? 15 : 15);
                    return e.preventDefault();
                });

                var $tokens = $back.find('.dropdown-menu');
                $.each(EmailTokens, function (i, token) {
                    $('<li role="presentation"><a data-token="' + token + '" href="#' + token + '">' + EmailTokenLabels[token] + '</a></li>').appendTo($tokens);
                });
                $back.on('click', '.dropdown.token-dropdown .dropdown-menu a[data-token]', function (e) {
                    var text = '{' + $(this).attr('data-token') + '}';
                    var $editor = $(this).closest('form').find('.can-have-token.active');
                    if (!$editor.length) {
                        $editor = $(this).closest('.card-message-container').find('textarea[name="message"]').focus();
                    }
                    insertAtCursor($editor[0], text);
                    textareaLineCountResize($editor[0], $editor.data('line-limit'), 14 * 1.42857);
                    $(this).closest('.dropdown.token-dropdown').removeClass('open');
                    $editor.focus();
                    return e.preventDefault();
                });

                $back.find('.from-address-toggle').click(function () {
                    var $container = $(this).closest('.card-addresses-container').toggleClass('from-address-visible');
                    var $from = $container.find('input[name="from"]');
                    $from.attr('checked', !$from.attr('checked'));
                });
                $back.find('.to-address-toggle').click(function () {
                    if (!isFromLead) {
                        return;
                    }
                    var $container = $(this).closest('.card-addresses-container').toggleClass('to-address-visible');
                    $container.find('input[name="to"]').click();
                });
                break;
            case WorkCreationWizardSteps.WORK_PANE_EMAIL_VIDEO:
                $step.append('\
                    <form class="form work-form form-horizontal">\
                        <div class="form-group from-form-group">\
                            <label class="control-label col-md-2 text-right">From: </label>\
                            <div class="col-md-10">\
                                <div class="form-control-static">\
                                    <div class="mail-n-google-integration-handler-container integration-handler-container"></div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="form-group">\
                            <label class="control-label col-md-2 text-right">Subject: </label>\
                            <div class="col-md-10">\
                                <input type="text" class="form-control email-title can-have-token" title="Subject" name="title" />\
                            </div>\
                        </div>\
                        <div class="form-group">\
                            <div class="col-md-12">\
                                ' + getEditableWYSIHTML5OptionsHtml() + '\
                               <div class="bootstrap-text-editor form-control email-message can-have-token" data-placeholder="Enter message here"></div>\
                            </div>\
                        </div>\
                        <div class="form-group">\
                            <div class="col-md-12">\
                                <div class="video-recorder-wrapper">\
                                    <div id="hdfvr-content" ></div>\
                                    <!--<div class="video-recorder">\
                                        <video controls></video>\
                                        <button type="button" class="btn btn-primary btn-record">Record</button>\
                                    </div>-->\
                                        <input type="hidden" name="video" />\
                                        <input type="hidden" name="thumb" />\
                                    <input type="hidden" name="token_value" />\
                                </div>\
                            </div>\
                        </div>\
                    </form>\
                ');
                $step.find('[data-role="editor-toolbar"]').append('\
                    <div class="btn-group dropdown token-dropdown">\
                        <a class="btn dropdown-toggle" title="Insert Token" data-toggle="dropdown">{-}</a>\
                        <ul class="dropdown-menu" role="menu"></ul>\
                    </div>\
                ').end().find('.btn').addClass('btn-xs');
                var $tokens = $step.find('.dropdown-menu');
                $.each(EmailTokens, function (i, token) {
                    $('<li role="presentation"><a data-token="' + token + '" href="#' + token + '">' + EmailTokenLabels[token] + '</a></li>').appendTo($tokens);
                });
                $step.on('click', '.dropdown.token-dropdown .dropdown-menu a[data-token]', function (e) {
                    e.stopPropagation();
                    e.cancelBubble = true;
                    var text = '{' + $(this).attr('data-token') + '}';
                    var $editor = $(this).closest('form').find('.can-have-token.active');
                    if (!$editor.length) {
                        $editor = $(this).closest('.form-group').find('.bootstrap-text-editor.email-message').focus();
                    }
                    if ($editor.is('.email-title')) {
                        insertAtCursor($editor[0], text);
                    } else {
                        document.execCommand('insertHTML', false, text);
                    }
                    $(this).closest('.dropdown.token-dropdown').removeClass('open');
                    $editor.focus();
                    return false;
                }).on('mousedown', '.dropdown.token-dropdown>.dropdown-toggle', function () {
                    $(this).closest('form').find('.can-have-token.active').addClass('keep-active');
                }).on('click', '.dropdown.token-dropdown>.dropdown-toggle', function () {
                    $(this).closest('form').find('.can-have-token.active').removeClass('active');
                    $(this).closest('form').find('.can-have-token.keep-active').removeClass('keep-active').addClass('active').focus();
                }).on('focus', '.can-have-token', function () {
                    $(this).closest('form').find('.can-have-token').removeClass('active');
                    $(this).addClass('active');
                });

                recordingPlayer = $step.find('.video-recorder video')[0];
                $recordingButton = $step.find('.video-recorder button.btn-record').click(function () {
                    var button = this;

                    if ($(button).hasClass('recording')/*button.innerHTML === 'Stop Recording'*/) {
                        button.disabled = true;
                        button.disableStateWaiting = true;
                        setTimeout(function () {
                            button.disabled = false;
                            button.disableStateWaiting = false;
                        }, 2 * 1000);

                        button.innerHTML = 'Record Again';
                        $(button).removeClass('recording');

                        function stopStream() {
                            if (button.stream && button.stream.stop) {
                                button.stream.stop();
                                button.stream = null;
                            }
                        }

                        if (button.recordRTC) {
                            if (button.recordRTC.length) {
                                button.recordRTC[0].stopRecording(function (url) {
                                    if (!button.recordRTC[1]) {
                                        button.recordingEndedCallback(url);
                                        recordedBlob = button.recordRTC[0].blob;
                                        stopStream();
                                        return;
                                    }

                                    button.recordRTC[1].stopRecording(function (url) {
                                        button.recordingEndedCallback(url);
                                        stopStream();
                                    });
                                });
                            } else {
                                button.recordRTC.stopRecording(function (url) {
                                    button.recordingEndedCallback(url);
                                    recordedBlob = button.recordRTC.blob;
                                    stopStream();
                                });
                            }
                        }

                        return;
                    }

                    button.disabled = true;

                    var commonConfig = {
                        onMediaCaptured: function (stream) {
                            button.stream = stream;
                            if (button.mediaCapturedCallback) {
                                button.mediaCapturedCallback();
                            }

                            button.innerHTML = 'Stop Recording';
                            button.disabled = false;
                            $(button).addClass('recording');
                        },
                        onMediaStopped: function () {
                            button.innerHTML = 'Record Again';
                            $(button).removeClass('recording');

                            if (!button.disableStateWaiting) {
                                button.disabled = false;
                            }
                        },
                        onMediaCapturingFailed: function (error) {
                            if (error.name === 'PermissionDeniedError' && !!navigator.mozGetUserMedia) {
                                InstallTrigger.install({
                                    'Foo': {
                                        // URL: 'https://addons.mozilla.org/en-US/firefox/addon/enable-screen-capturing/',
                                        URL: 'https://addons.mozilla.org/firefox/downloads/file/355418/enable_screen_capturing_in_firefox-1.0.006-fx.xpi?src=cb-dl-hotness',
                                        toString: function () {
                                            return this.URL;
                                        }
                                    }
                                });
                            }

                            commonConfig.onMediaStopped();
                        }
                    };

                    var mimeType = 'video/mp4';//'video/webm';

                    var mediaConstraints = {video: true, audio: true};
                    var successCallback = function (audioVideoStream) {
                        recordingPlayer.controls = false;
                        recordingPlayer.removeAttribute('controls');
                        recordingPlayer.muted = true;
                        recordingPlayer.setAttribute('muted', true);
                        if (DevOptions.debug) {
                            console.log('Video muted=true');
                        }
                        recordingPlayer.srcObject = audioVideoStream;
                        recordingPlayer.play();

                        setTimeout(function () {
                            var canvas = document.createElement('canvas');
                            canvas.width = parseFloat($(recordingPlayer).width());
                            canvas.height = parseFloat($(recordingPlayer).height());
                            canvas.getContext('2d').drawImage(recordingPlayer, 0, 0, canvas.width, canvas.height);
                            canvas.toBlob(function (blob) {
                                thumbBlob = blob;
                            }, 'image/jpeg', 1);
                        }, 2000);

                        commonConfig.onMediaCaptured(audioVideoStream);

                        audioVideoStream.onended = function () {
                            commonConfig.onMediaStopped();
                        };
                    };
                    var errorCallback = function (error) {
                        commonConfig.onMediaCapturingFailed(error);
                    }

                    var isBlackBerry = !!(/BB10|BlackBerry/i.test(navigator.userAgent || ''));
                    if (isBlackBerry && !!(navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia)) {
                        navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
                        navigator.getUserMedia(mediaConstraints, successCallback, errorCallback);
                        return;
                    }

                    navigator.mediaDevices.getUserMedia(mediaConstraints).then(successCallback).catch(errorCallback);

                    button.mediaCapturedCallback = function () {
                        var params = {
                            canvas_width: parseFloat($(recordingPlayer).width()),
                            canvas_height: parseFloat($(recordingPlayer).height()),
                            disableLogs: !DevOptions.debug
                        };
                        if (typeof MediaRecorder === 'undefined') { // opera or chrome etc.
                            if (DevOptions.debug) {
                                console.log('MediaRecorder is undefined');
                            }
                            button.recordRTC = [];

                            if (!params.bufferSize) {
                                // it fixes audio issues whilst recording 720p
                                params.bufferSize = 16384;
                            }

                            var options = {
                                type: 'audio',
                                bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
                                sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
                                leftChannel: params.leftChannel || false,
                                disableLogs: params.disableLogs || false,
                                recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
                            };

                            if (typeof params.sampleRate == 'undefined') {
                                delete options.sampleRate;
                            }

                            var audioRecorder = RecordRTC(button.stream, options);

                            var videoRecorder = RecordRTC(button.stream, {
                                type: 'video',
                                disableLogs: params.disableLogs || false,
                                canvas: {
                                    width: params.canvas_width || 320,
                                    height: params.canvas_height || 240
                                },
                                frameInterval: typeof params.frameInterval !== 'undefined' ? parseInt(params.frameInterval) : 20 // minimum time between pushing frames to Whammy (in milliseconds)
                            });

                            // to sync audio/video playbacks in browser!
                            videoRecorder.initRecorder(function () {
                                audioRecorder.initRecorder(function () {
                                    audioRecorder.startRecording();
                                    videoRecorder.startRecording();
                                });
                            });

                            button.recordRTC.push(audioRecorder, videoRecorder);

                            button.recordingEndedCallback = function () {
                                var audio = new Audio();
                                audio.src = audioRecorder.toURL();
                                audio.controls = true;
                                audio.autoplay = true;

                                audio.onloadedmetadata = function () {
                                    recordingPlayer.controls = true;
                                    recordingPlayer.setAttribute('controls', true);
                                    recordingPlayer.muted = false;
                                    recordingPlayer.removeAttribute('muted');
                                    if (DevOptions.debug) {
                                        console.log('Video muted=false');
                                    }
                                    recordingPlayer.src = videoRecorder.toURL();
                                    recordingPlayer.play();
                                };

                                recordingPlayer.parentNode.appendChild(document.createElement('hr'));
                                recordingPlayer.parentNode.appendChild(audio);

                                if (audio.paused)
                                    audio.play();
                            };
                            return;
                        }

                        button.recordRTC = RecordRTC(button.stream, {
                            type: 'video',
                            mimeType: mimeType,
                            disableLogs: params.disableLogs || false,
                            // bitsPerSecond: 25 * 8 * 1025 // 25 kbits/s
                            getNativeBlob: false, // enable it for longer recordings,
                            width: params.canvas_width || 320,
                            height: params.canvas_height || 240
                        });

                        button.recordingEndedCallback = function (url) {
//                            if (DevOptions.debug) {
//                                console.log(url);
//                            }
                            recordingPlayer.controls = true;
                            recordingPlayer.setAttribute('controls', true);
                            recordingPlayer.muted = false;
                            recordingPlayer.removeAttribute('muted');
                            if (DevOptions.debug) {
                                console.log('Video muted=false');
                            }
                            recordingPlayer.src = url;
                            recordingPlayer.play();

                            recordingPlayer.onended = function () {
                                recordingPlayer.pause();
                                recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
                            };
                        };

                        button.recordRTC.startRecording();
                        if (DevOptions.debug) {
                            console.log('MediaRecorder is defined.');
                        }
                    };
                });
                break;
        }

        return $step;
    }

    function initWorkCreationWizardStep($step) {
        switch ($step.attr('data-step')) {
            case WorkCreationWizardSteps.WORK_PANE_EMAIL_SEND:
                $step.find('.bootstrap-text-editor').wysiwyg({toolbarSelector: $step.find('[data-role="editor-toolbar"]')});
                break;
            case WorkCreationWizardSteps.WORK_PANE_PHYSICALMAIL_POSTCARD:
                var $front = $step.find('#work-creation-wizard-step-work-pane-physicalmail-tab-pane-front');
                var $frontImageUploader = $front.find('.btn-image-upload');
                postcardFrontImageUploader = new ss.SimpleUpload({
                    button: $frontImageUploader, // file upload button
                    autoSubmit: false,
                    maxSize: 102400, // kilobytes
                    onChange: function (filename, extension, uploadBtn, fileSize, file) {
                        var oFReader = new FileReader();
                        oFReader.onload = function (oFREvent) {
                            $('#work-creation-wizard-step-work-pane-physicalmail-tab-pane-front')
                                    .find('img.front-image').attr('src', oFREvent.target.result).end()
                                    .find('input[name="front"]').val('true').end()
                                    .find('input[name="front_tpl"]').val('false').end()
                                    .find('.front-image-container').addClass('image-selected');
                        };
                        oFReader.readAsDataURL(file);
                    }
                });
                $(postcardFrontImageUploader._input).parent().hide();
                $frontImageUploader.click(function (e) {
                    $(postcardFrontImageUploader._input).click();
                    return e.preventDefault();
                });

                var $back = $step.find('#work-creation-wizard-step-work-pane-physicalmail-tab-pane-back');
                var $backImageUploader = $back.find('.btn-image-upload');
                postcardBackImageUploader = new ss.SimpleUpload({
                    button: $backImageUploader, // file upload button
                    autoSubmit: false,
                    maxSize: 102400, // kilobytes
                    onChange: function (filename, extension, uploadBtn, fileSize, file) {
                        var oFReader = new FileReader();
                        oFReader.onload = function (oFREvent) {
                            $('#work-creation-wizard-step-work-pane-physicalmail-tab-pane-back')
                                    .find('img.back-image').attr("src", oFREvent.target.result).end()
                                    .find('input[name="back"]').val('true').end()
                                    .find('input[name="back_tpl"]').val('false').end()
                                    .find('.back-image-container').addClass('image-selected');
                        };
                        oFReader.readAsDataURL(file);
                    }
                });
                $(postcardBackImageUploader._input).parent().hide();
                $backImageUploader.click(function (e) {
                    $(postcardBackImageUploader._input).click();
                    return e.preventDefault();
                });

                break;
            case WorkCreationWizardSteps.WORK_PANE_EMAIL_VIDEO:
                $step.find('.bootstrap-text-editor').wysiwyg({toolbarSelector: $step.find('[data-role="editor-toolbar"]')});
                break;
        }
    }

    function onAfterWorkCreationWizardPopup() {
        switch (step) {
            case WorkCreationWizardSteps.WORK_PANE_PHYSICALMAIL_POSTCARD:
                break;
        }
    }

    function onHideWorkCreationWizardPopup() {
        switch (step) {
            case WorkCreationWizardSteps.WORK_PANE_EMAIL_VIDEO:
                stopRecordingPlayer();
                break;
        }
    }

    function resetWorkForm($form) {
        var work = null;

        if (workId > 0) {
            work = $('.workflow[data-workflow-id="' + workflowId + '"] .work[data-work-id="' + workId + '"]').data('work');
        } else {
            work = createDefaultWork();
        }

        switch (workflowClass) {
            case WorkflowClasses.TWITTER_TWEET:
            case WorkflowClasses.TWITTER_REPLY:
                $form.find('.left-letter-count').data('maxlen', 127).html('127');
                $form.find('textarea[name="msg"]').val(work.values.msg).keyup();
                break;
            case WorkflowClasses.TWITTER_RETWEET:
                break;
            case WorkflowClasses.TWITTER_FOLLOW:
                $form.find('button').removeClass('active');
                if (work.values.follow === 'true') {
                    $form.find('button.btn-follow.follow').addClass('active');
                } else if (work.values.follow === 'false') {
                    $form.find('button.btn-follow.unfollow').addClass('active');
                }
                $form.find('input[name="follow"]').val(work.values.follow);
                break;
            case WorkflowClasses.EMAIL_SEND:
                $form.find('.email-title').val((work.values.title));
                $form.find('.bootstrap-text-editor.email-message').html((work.values.msg));
                break;
            case WorkflowClasses.PHYSICALMAIL_POSTCARD:
                var $front = $form.find('#work-creation-wizard-step-work-pane-physicalmail-tab-pane-front');
                $front.find('input[name="front"]').val('false');
                $front.find('input[name="front_tpl"]').val('false');
                $front.find('ul.template-list>li.active').removeClass('active');
                if (work.values.front && work.values.front !== '' && work.values.front !== 'false') {
                    $front.find('img.front-image').attr('src', '/uploads/actions/postcards/fronts/' + work.values.front + '.jpg');
                    $front.find('input[name="front"]').val(work.values.front);
                    $front.find('.front-image-container').addClass('image-selected');
                } else if (work.values.front_tpl && work.values.front_tpl !== '' && work.values.front_tpl !== 'false') {
                    $front.find('ul.template-list>li[data-index="' + work.values.front_tpl + '"]').click();
                    $front.find('input[name="front_tpl"]').val(work.values.front_tpl);
                    $front.find('.front-image-container').addClass('image-selected');
                } else {
                    $front.find('img.front-image').attr('src', null);
                    $front.find('.front-image-container').removeClass('image-selected');
                }
                var $back = $form.find('#work-creation-wizard-step-work-pane-physicalmail-tab-pane-back');
                $back.find('input[name="has_banner"]').val(work.values.has_banner);
                if (work.values.has_banner && work.values.has_banner !== '' && work.values.has_banner !== 'false') {
                    $back.find('.back-container').removeClass('back-image-hide');
                } else {
                    $back.find('.back-container').addClass('back-image-hide');
                }
                if (work.values.from) {
                    var from = work.values.from;
                    $back.find('input[name="from_name"]').val(from.name);
                    $back.find('input[name="from_company_name"]').val(from.company_name);
                    $back.find('input[name="from_address_line1"]').val(from.address_line1);
                    $back.find('input[name="from_address_line2"]').val(from.address_line2);
                    $back.find('input[name="from_city"]').val(from.city);
                    $back.find('input[name="from_state"]').val(from.state);
                    $back.find('input[name="from_zip"]').val(from.zip);
                    $back.find('input[name="from_country"]').val(from.country);
                    $back.find('.card-addresses-container').addClass('from-address-visible');
                    $back.find('input[name="from"]').attr('checked', true);
                } else {
                    $back.find('input[name="from_name"]').val('');
                    $back.find('input[name="from_company_name"]').val('');
                    $back.find('input[name="from_address_line1"]').val('');
                    $back.find('input[name="from_address_line2"]').val('');
                    $back.find('input[name="from_city"]').val('');
                    $back.find('input[name="from_state"]').val('');
                    $back.find('input[name="from_zip"]').val('');
                    $back.find('input[name="from_country"]').val('');
                    $back.find('.card-addresses-container').removeClass('from-address-visible');
                    $back.find('input[name="from"]').attr('checked', false);
                }
                $back.find('input[name="back"]').val('false');
                $back.find('input[name="back_tpl"]').val('false');
                $back.find('ul.template-list>li.active').removeClass('active');
                if (work.values.back && work.values.back !== '' && work.values.back !== 'false') {
                    $back.find('img.back-image').attr('src', '/uploads/actions/postcards/backs/' + work.values.back + '.jpg');
                    $back.find('input[name="back"]').val(work.values.back);
                    $back.find('.back-image-container').addClass('image-selected');
                } else if (work.values.back_tpl && work.values.back_tpl !== '' && work.values.back_tpl !== 'false') {
                    $back.find('ul.template-list>li[data-index="' + work.values.back_tpl + '"]').click();
                    $back.find('input[name="back_tpl"]').val(work.values.back_tpl);
                    $back.find('.back-image-container').addClass('image-selected');
                } else {
                    $back.find('img.back-image').attr('src', null);
                    $back.find('.back-image-container').removeClass('image-selected');
                }

                if (work.workflow && work.workflow.frld) {
                    var to = work.values.to;
                    if (!to || to === 'true') {
                        var client = work.workflow.lead.clients[0];
                        to = {
                            name: client.name,
                            address_line1: client.address_line1,
                            address_line2: client.address_line2,
                            city: client.city,
                            state: client.state,
                            zip: client.zip,
                            country: client.country
                        };
                        $back.find('.card-addresses-container').removeClass('to-address-visible');
                        $back.find('input[name="to"]').attr('checked', false);
                    } else {
                        $back.find('.card-addresses-container').addClass('to-address-visible');
                        $back.find('input[name="to"]').attr('checked', true);
                    }
                    $back.find('input[name="to_name"]').val(to.name);
                    $back.find('input[name="to_company_name"]').val(to.company_name);
                    $back.find('input[name="to_address_line1"]').val(to.address_line1);
                    $back.find('input[name="to_address_line2"]').val(to.address_line2);
                    $back.find('input[name="to_city"]').val(to.city);
                    $back.find('input[name="to_state"]').val(to.state);
                    $back.find('input[name="to_zip"]').val(to.zip);
                    $back.find('input[name="to_country"]').val(to.country);

                    $back.find('.to-address-toggle .name').html(to.name);
                    var address12 = '';
                    if (to.address_line1) {
                        address12 += to.address_line1;
                    }
                    if (to.address_line2) {
                        if (address12) {
                            address12 += ', ';
                        }
                        address12 += to.address_line2;
                    }
                    $back.find('.to-address-toggle .addressline1-addressline2').html(address12);
                    var cszc = '';
                    if (to.city) {
                        cszc += to.city;
                    }
                    if (to.state) {
                        if (cszc) {
                            cszc += ', ';
                        }
                        cszc += to.state;
                    }
                    if (to.zip) {
                        if (cszc) {
                            cszc += ', ';
                        }
                        cszc += to.zip;
                    }
                    if (to.country) {
                        if (cszc) {
                            cszc += ', ';
                        }
                        cszc += to.country;
                    }
                    $back.find('.to-address-toggle .city-state-zip-country').html(cszc);
                } else {
                    $back.find('input[name="to_name"]').val('');
                    $back.find('input[name="to_company_name"]').val('');
                    $back.find('input[name="to_address_line1"]').val('');
                    $back.find('input[name="to_address_line2"]').val('');
                    $back.find('input[name="to_city"]').val('');
                    $back.find('input[name="to_state"]').val('');
                    $back.find('input[name="to_zip"]').val('');
                    $back.find('input[name="to_country"]').val('');
                    $back.find('.to-address-toggle .name').html('Full Name');
                    $back.find('.to-address-toggle .company-name').html('Company Name');
                    $back.find('.to-address-toggle .addressline1-addressline2').html('123 Example Dr');
                    $back.find('.to-address-toggle .city-state-zip-country').html('City, State 12345');
                    $back.find('.card-addresses-container').removeClass('to-address-visible');
                    $back.find('input[name="to"]').attr('checked', false);
                }
                $back.find('textarea[name="message"]').val(work.values.message);
                break;
            case WorkflowClasses.EVENT_TIME_DELAY:
                $form.find('input[name="type_value"]').val(work.values.type_value);
                break;
            case WorkflowClasses.EMAIL_VIDEO:
                $form.find('.email-title').val((work.values.title));
                $form.find('.bootstrap-text-editor.email-message').html((work.values.msg));
                $form.find('input[name="video"]').val(work.values.video ? work.values.video : '');
                $form.find('input[name="thumb"]').val(work.values.thumb ? work.values.thumb : '');
                var s3_upload_path = ADDPIPE_BUCKET_URL;
                if (typeof work.values.video === 'string' && work.values.video.substring(0, s3_upload_path.length) === s3_upload_path) {
                    // form.find('.video-recorder video').attr('src', work.values.video);
                } else {
                    $form.find('.video-recorder video').attr('src', work.values.video ? '/uploads/actions/videos/' + work.values.video + '.mp4' : '');
                }
                recordedBlob = null;
                thumbBlob = null;
                break;
        }
    }

    function createDefaultWork() {
        var work = {values: {}};
        switch (workflowClass) {
            case WorkflowClasses.TWITTER_TWEET:
            case WorkflowClasses.TWITTER_REPLY:
                work.values.msg = '';//'Congrats on your #grand opening!\nDo you have a website up and ready for all your new customers!?';
                break;
            case WorkflowClasses.TWITTER_RETWEET:
                break;
            case WorkflowClasses.TWITTER_FOLLOW:
                break;
            case WorkflowClasses.EMAIL_SEND:
                work.values.title = '';//'Email Subject';
                work.values.msg = '';//'Email Content';
                break;
            case WorkflowClasses.EVENT_TIME_DELAY:
                work.values.type_value = 3;
                break;
            case WorkflowClasses.PHYSICALMAIL_POSTCARD:
                work.values.has_banner = 'true';
                work.values.front = 'false';
                work.values.front_tpl = 'false';
                work.values.back = 'false';
                work.values.back_tpl = 'false';
                work.values.message = '';
                break;
            case WorkflowClasses.EMAIL_VIDEO:
                work.values.title = '';//'Email Subject';
                work.values.msg = '';// 'Type a message here to go with your video...';//'Email Content';
                break;
        }

        return work;
    }

    function __class2step(cls) {
        return 'work-pane-' + cls.replace(/_/g, '-');
    }
    function __step2class(st) {
        if (st.indexOf('work-pane-') === -1) {
            return '';
        }
        return st.replace('work-pane-', '').replace(/\-/g, '_');
    }

    function saveWork($form) {

        var values = $form.serializeObject();

        switch (workflowClass) {
            case WorkflowClasses.TWITTER_TWEET:
            case WorkflowClasses.TWITTER_REPLY:
            case WorkflowClasses.TWITTER_FOLLOW:
            case WorkflowClasses.TWITTER_RETWEET:
                if (!$twitterIntegrator.hasClass('active')) {
                    showErrorMessage('Workflow', 'Please connect to twitter to use twitter functions.');
                    return;
                }
                if (workflowClass != WorkflowClasses.TWITTER_FOLLOW) {
                    values.msg = '';
                }
                break;
            case WorkflowClasses.EMAIL_SEND:
                if (!DevOptions.debug && ! ($googleIntegrator.hasClass('active') || $mailIntegrator.hasClass('active'))) {
                    showErrorMessage('Workflow', 'Please connect to google or imap to send email.');
                    return;
                }

                if (!values.title) {
                    showErrorMessage('Workflow', 'Please enter email subject.');
                    return;
                }

                values.msg = $form.find('.bootstrap-text-editor.email-message').html();
                break;
            case WorkflowClasses.PHYSICALMAIL_POSTCARD:
                if ((!values.front || values.front === '' || values.front === 'false')
                        && (!values.front_tpl || values.front_tpl === '' || values.front_tpl === 'false')) {
                    showErrorMessage('Workflow', 'Please add cover image.');
                    return;
                }
                if (values.has_banner && values.has_banner !== '' && values.has_banner !== 'false') {
                    if ((!values.back || values.back === '' || values.back === 'false')
                            && (!values.back_tpl || values.back_tpl === '' || values.back_tpl === 'false')) {
                        showErrorMessage('Workflow', 'please add image or click the arrow to not include an image');
                        return;
                    }
                }
                if (values.from) {
                    values.from = {
                        name: values.from_name,
                        company_name: values.from_company_name,
                        address_line1: values.from_address_line1,
                        address_line2: values.from_address_line2,
                        city: values.from_city,
                        state: values.from_state,
                        zip: values.from_zip,
                        country: values.from_country
                    };
                } else {
                    values.from = 'false';
                }
                delete values['from_name'];
                delete values['from_company_name'];
                delete values['from_address_line1'];
                delete values['from_address_line2'];
                delete values['from_city'];
                delete values['from_state'];
                delete values['from_zip'];
                delete values['from_country'];

                if (values.to) {
                    values.to = {
                        name: values.to_name,
                        company_name: values.to_company_name,
                        address_line1: values.to_address_line1,
                        address_line2: values.to_address_line2,
                        city: values.to_city,
                        state: values.to_state,
                        zip: values.to_zip,
                        country: values.to_country
                    };
                } else {
                    values.to = 'true';
                }
                delete values['to_name'];
                delete values['to_company_name'];
                delete values['to_address_line1'];
                delete values['to_address_line2'];
                delete values['to_city'];
                delete values['to_state'];
                delete values['to_zip'];
                delete values['to_country'];
                break;
            case WorkflowClasses.EMAIL_VIDEO:
                var token_value = values['token_value'];
                delete values['token_value'];

                if (!DevOptions.debug && ! ($googleIntegrator.hasClass('active') || $mailIntegrator.hasClass('active'))) {
                    showErrorMessage('Workflow', 'Please connect to google or imap to send email.');
                    return;
                }

                if (!values.title) {
                    showErrorMessage('Workflow', 'Please enter email subject.');
                    return;
                }

                values.msg = $form.find('.bootstrap-text-editor.email-message').html();
                break;
        }

        if (workId > 0) {
            var $work = $('.workflow[data-workflow-id="' + workflowId + '"] .work[data-work-id="' + workId + '"]').effect("highlight", {color: "#99ff99"}, 3000);
            var work = $work.data('work');
            work.values = values;
        } else {
            $saveButton.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Adding...');
            var work = {class: workflowClass, values: values};
        }

        if (workflowClass === WorkflowClasses.PHYSICALMAIL_POSTCARD) {
            if (postcardUploadState === 0) {
                if (values.front === 'true') {
                    doBeforeSaving($form, values, 'action_postcard_front');
                    return;
                } else {
                    postcardUploadState = 1;
                }
            }
            if (postcardUploadState === 1) {
                if (values.back === 'true') {
                    doBeforeSaving($form, values, 'action_postcard_back');
                    return;
                } else {
                    postcardUploadState = 2;
                }
            }
            if (postcardUploadState === 2) {
                postcardUploadState = 0;
            }
        }
        else if (workflowClass === WorkflowClasses.EMAIL_VIDEO) {
            if (videoUploadState === 0) {
                if (recordedBlob) {
                    doBeforeSaving($form, values, 'action_video');
                    return;
                } else {
                    videoUploadState = 1;
                }
            }
            if (videoUploadState === 1) {
                if (recordedBlob) {
                    doBeforeSaving($form, values, 'action_video_thumb');
                    return;
                } else {
                    videoUploadState = 2;
                }
            }
            if (videoUploadState === 2) {
                videoUploadState = 0;
            }
        }

        if (workId > 0) {
            $saveButton.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Saving...');
            var new_data = {values: values};
            if (workflowClass === WorkflowClasses.EMAIL_VIDEO) {
                new_data.token = token_value;
                new_data.return_obj = 1;
            }
            $.ajax({
                method: "PUT",
                url: getApiUrl(isFromLead, workflowId, true, workId),
                data: new_data
            }).done(function (ret) {
                showSuccessMessage("Success", "The lead source has been updated successfully.");
                $saveButton.html('<i class="fa fa-save"></i> Saved');
                closeWizardWithDelay();

                if (workflowClass === WorkflowClasses.EMAIL_VIDEO) {
                    values = ret.values;
                }

                var $work = $('.workflow[data-workflow-id="' + workflowId + '"] .work[data-work-id="' + workId + '"]').effect("highlight", {color: "#99ff99"}, 3000);
                var work = $work.data('work');
                work.values = values;
                
                fillWorkValues($work, values);

            }).fail(function () {
                showErrorMessage('Workflow', 'Error occurred while saving the workflow item. Please try again.');
                $saveButton.attr('disabled', false).html('<i class="fa fa-save"></i> Save');
            });
        } else {
            $saveButton.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Adding...');
            var work = {class: workflowClass, values: values};
            if (workflowClass === WorkflowClasses.EMAIL_VIDEO) {
                work.token = token_value;
                work.return_obj = 1;
            }
            if (position) {
                work.position = parseInt(position) + 1;
            }
            $.post(getApiUrl(isFromLead, workflowId, true), work).done(function (work) {
                if (!work || !work.id) {
                    showErrorMessage('Workflow', 'Error occurred while adding a new workflow item. Please try again.');
                    $saveButton.attr('disabled', false).html('<i class="fa fa-save"></i> Add');
                    return;
                }

                showSuccessMessage("Success", "An action has been added successfully.");
                $saveButton.html('<i class="fa fa-save"></i> Added');
                var $workflow = $('.workflow[data-workflow-id="' + workflowId + '"]');
                var workflow = $workflow.data('workflow');
                if (typeof workflow.actions == 'undefined') {
                    workflow.actions = [];
                }
                workflow.actions.push(work);
                work.workflow = workflow;
                if (position) {
                    var $work = $createWork(work).insertAfter('.workflow[data-workflow-id="' + workflowId + '"] .work[data-work-position="' + position + '"]').fadeIn({duration: 500, queue: false});//.hide().slideDown(500);
                    var $wrapper = $work.closest('.source-workflow-content-wrapper');
                    $wrapper.scrollTop($work.position().top + $work.height());
                    reorderActions($workflow);

                    if (work.class === WorkflowClasses.EMAIL_VIDEO) {
                        $work.find('.media').append('<div class="loading-div"><i class="fa fa-spin fa-spinner"></i> Saving...</div>');
                        $work.find('video').hide();
                        setTimeout(function() {
                            var url = '/api/v1/' + (isFromLead ? 'deal_actions/' : 'workflows/' + workflowId + '/actions/') + work.id;
                            $.getJSON(url, function (data) {
                                $work.find('.loading-div').remove();
                                var s3_upload_path = ADDPIPE_BUCKET_URL;
                                if (typeof data.values.video === 'string' && data.values.video.substring(0, s3_upload_path.length) === s3_upload_path) {
                                    $work.find('video').attr('src', data.values.video);
                                } else {
                                    $work.find('video').attr('src', data.values.video ? '/uploads/actions/videos/' + data.values.video + '.mp4' : '');
                                }
                                $work.find('video').show();

                                if (workflow.frld) {
                                    refreshActionDates($workflow);
                                }
                            });
                        }, videoUploadTimeout);
                    } else {
                        if (workflow.frld) {
                            refreshActionDates($workflow);
                        }
                    }
                } else {
                    var $new_workflow = $createWorkflow(workflow, true);
                    $workflow.replaceWith($new_workflow.fadeIn({duration: 500, queue: false}).hide().slideDown(500));

                    if (work.class === WorkflowClasses.EMAIL_VIDEO) {
                        $new_workflow.find('.media').append('<div class="loading-div"><i class="fa fa-spin fa-spinner"></i> Saving...</div>');
                        $new_workflow.find('video').hide();
                        setTimeout(function() {
                            var url = '/api/v1/' + (isFromLead ? 'deal_actions/' : 'workflows/' + workflowId + '/actions/') + work.id;
                            $.getJSON(url, function (data) {
                                $new_workflow.find('.loading-div').remove();
                                var s3_upload_path = ADDPIPE_BUCKET_URL;
                                if (typeof data.values.video === 'string' && data.values.video.substring(0, s3_upload_path.length) === s3_upload_path) {
                                    $new_workflow.find('video').attr('src', data.values.video);
                                } else {
                                    $new_workflow.find('video').attr('src', data.values.video ? '/uploads/actions/videos/' + data.values.video + '.mp4' : '');
                                }
                                $new_workflow.find('video').show();

                                if (workflow.frld) {
                                    refreshActionDates($workflow);
                                }
                            });
                        }, videoUploadTimeout);
                    } else {
                        if (workflow.frld) {
                            refreshActionDates($workflow);
                        }
                    }

                    if (!workflow.frld) {
                        $new_workflow.closest('.source-workflow').find('.btn-workflow-action-add').show();
                    }
                    setTimeout(function () {
                        $('.workflow[data-workflow-id="' + workflowId + '"]').find('.flow-header h3[data-name]').effect("highlight", {color: "#ff9999"}, 5000);
                    }, 500);
                }

                closeWizardWithDelay();

            }).fail(function () {
                showErrorMessage('Workflow', 'Error occurred while adding a new workflow item. Please try again.');
                $saveButton.attr('disabled', false).html('<i class="fa fa-save"></i> Add');
            });
        }
    }

    function doBeforeSaving($form, values, step) {
        if (workflowClass == WorkflowClasses.PHYSICALMAIL_POSTCARD) {
            if (values.front === 'true') {
                if (step === 'action_postcard_front') {
                    postcardFrontImageUploader.setOptions({
                        url: '/api/v1/uploads',
                        data: {type: 'action_postcard_front'},
                        onComplete: function (filename, response) {
                            $form.find('input[name="front"]').val(response.id);
                            postcardUploadState++;
                            saveWork($form);
                        }});
                    postcardFrontImageUploader.submit();
                }
            }
            if (values.back === 'true') {
                if (step === 'action_postcard_back') {
                    postcardBackImageUploader.setOptions({
                        url: '/api/v1/uploads',
                        data: {type: 'action_postcard_back'},
                        onComplete: function (filename, response) {
                            $form.find('input[name="back"]').val(response.id);
                            postcardUploadState++;
                            saveWork($form);
                        }
                    });
                    postcardBackImageUploader.submit();
                }
            }
        } else if (workflowClass == WorkflowClasses.EMAIL_VIDEO) {
            if (recordedBlob) {
                var data = new FormData();
                if (step === 'action_video') {
                    data.append('type', 'action_video');
                    data.append('uploadfile', recordedBlob, 'video.mp4');
                    $.ajax({
                        url: '/api/v1/uploads',
                        type: 'post',
                        data: data,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-File-Name', 'video.mp4');
                            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        },
                        success: function (response) {
                            $form.find('input[name="video"]').val(response.id);
                            videoUploadState++;
                            saveWork($form);
                        }
                    });
                }
                else if (step === 'action_video_thumb') {
                    data.append('type', 'action_video_thumb');
                    data.append('uploadfile', thumbBlob, 'thumb.jpg');
                    $.ajax({
                        url: '/api/v1/uploads',
                        type: 'post',
                        data: data,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-File-Name', 'thumb.jpg');
                            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        },
                        success: function (response) {
                            $form.find('input[name="thumb"]').val(response.id);
                            videoUploadState++;
                            saveWork($form);
                        }
                    });
                }
            }
        }
    }

    function deleteWork() {
        bootbox.confirm('Are you sure you want to delete the action?',
                function (result) {
                    if (result) {
                        $delButton.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Deleting...');
                        $.ajax({
                            type: 'delete',
                            url: getApiUrl(isFromLead, workflowId, true, workId),
                            success: function () {
                                showSuccessMessage("Workflow", "The action has been deleted successfully.");
                                $delButton.html('Deleted');
                                closeWizardWithDelay();
                                $('.workflow[data-workflow-id="' + workflowId + '"] .work[data-work-id="' + workId + '"]').fadeOut(function () {
                                    var work = $(this).data('work');
                                    $(this).remove();
                                    var workflow = work.workflow;
                                    $.each(workflow.actions, function (i, __work) {
                                        if (work.id == __work.id) {
                                            workflow.actions.splice(i, 1);
                                            return false;
                                        }
                                    });
                                    if (!workflow.actions.length) {
                                        var $workflow = workflow.$element;
                                        var $new_workflow = $createWorkflow(workflow, false);
                                        $workflow.replaceWith($new_workflow);
                                        if (!workflow.frld) {
                                            $new_workflow.closest('.source-workflow').find('.btn-workflow-action-add').hide();
                                        }
                                    } else {
                                        if (workflowClass == WorkflowClasses.EVENT_TIME_DELAY) {
                                            refreshActionDates(workflow.$element);
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
    }



    function fillWorkValues($work, values) {
        switch (workflowClass) {
            case WorkflowClasses.TWITTER_TWEET:
            case WorkflowClasses.TWITTER_REPLY:
                $work.find('p.tweet-entry').html(values.msg);
                break;
            case WorkflowClasses.TWITTER_RETWEET:
                break;
            case WorkflowClasses.TWITTER_FOLLOW:
                $work.find('label').html(values.follow === 'true' ? 'Follow' : 'Unfollow');
                break;
            case WorkflowClasses.EMAIL_SEND:
                $work.find('div.title').html((values.title));
                $work.find('div.message').html((values.msg));
                break;
            case  WorkflowClasses.PHYSICALMAIL_POSTCARD:
                if (values.front && values.front !== '' && values.front !== 'false') {
                    $work.find('div.media img').attr('src', '/uploads/actions/postcards/fronts/' + work.values.front + '.jpg');
                } else if (values.front_tpl && values.front_tpl !== '' && values.front_tpl !== 'false') {
                    $work.find('div.media img').attr('src', frontImageTemplates[values.front_tpl - 1]);
                } else {
                    $work.find('div.media img').attr('src', null);
                }
                $work.find('div.message').html((values.message.replace(/(?:\r\n|\r|\n)/g, '<br />')));
                break;
            case WorkflowClasses.EVENT_TIME_DELAY:
                $work.find('span.days').html(values.type_value);
                break;
            case WorkflowClasses.EMAIL_VIDEO:
                $work.find('div.title').html((values.title));
                $work.find('.media').append('<div class="loading-div"><i class="fa fa-spin fa-spinner"></i> Saving...</div>');
                $work.find('video').hide();
                setTimeout(function() {
                    var url = '/api/v1/' + (isFromLead ? 'deal_actions/' : 'workflows/' + workflowId + '/actions/') + workId;
                    $.getJSON(url, function (data) {
                        $work.find('.loading-div').remove();
                        var s3_upload_path = ADDPIPE_BUCKET_URL;
                        if (typeof data.values.video === 'string' && data.values.video.substring(0, s3_upload_path.length) === s3_upload_path) {
                            $work.find('video').attr('src', data.values.video);
                        } else {
                            $work.find('video').attr('src', data.values.video ? '/uploads/actions/videos/' + data.values.video + '.mp4' : '');
                        }
                        $work.find('video').show();
                    });
                }, videoUploadTimeout);
                $work.find('div.message').html((values.msg));
                break;
        }
    }

    function updatePosition($work) {
        var $workflow = $work.closest('.workflow');
        reorderActions($workflow);
        $.ajax({
            method: 'put',
            url: getApiUrl($workflow.hasClass('workflow-from-lead'), $workflow.attr('data-workflow-id'), true, $work.attr('data-work-id')),
            data: {position: $work.attr('data-work-position')}
        }).done(function () {
            if ($workflow.hasClass('workflow-from-lead')) {
                refreshActionDates($workflow);
            }
        });
    }

    scope.updateActiveDealWorkflows = function (deal_id, data) {
        $.ajax({
            method: 'put',
            url: '/api/v1/deals/' + deal_id + '/deal_workflows/active',
            data: data,
        }).done(function () {
        });
    }

    scope.setInitialDealWorkflowIsEnabled = function (deal_id, value) {
        $.ajax({
            method: 'post',
            url: '/api/v1/deals/' + deal_id + '/deal_workflows/initial/is_enabled',
            data: {value: value},
        }).done(function () {
        });
    }

    function refreshActionDates($workflow) {
        var workflow = $workflow.data('workflow');
        $.getJSON('/api/v1/deal_workflows/' + $workflow.attr('data-workflow-id') + '/deal_actions', function (data) {
            var first_work = null;
            $.each(data, function (i, work) {
                var $work = $workflow.find('.work[data-work-id="' + work.id + '"]');
                if (!$work.length) {
                    return;
                }
                work.workflow = workflow;
                work.day = calculateDateSpan(first_work, work);
                work.show_day = !first_work || first_work.day < work.day;
                if (first_work === null) {
                    first_work = work;
                }
                $work.data('work', work);

                var completed = work.executed_at > 0;
                if (completed) {
                    $work.addClass('completed');
                } else {
                    $work.removeClass('completed');
                }
                $work.find('.date').html(!completed ? 'Due ' + (work.due_at ? getDateTimeString(work.due_at) : '') : getDateTimeString(work.executed_at));

                if (work.class === WorkflowClasses.PHYSICALMAIL_POSTCARD) {
                    if (work.extra != null && work.extra.est_delivery_at != null && work.extra.est_delivery_at != 0) {
                        $work.find('.estdelivery-date').html('Est delivery: ' + getDateString(work.extra.est_delivery_at));
                    }

                    if (work.extra != null && work.extra.tracking_events != null && work.extra.tracking_events.length > 0) {
                        var lastEvent = work.extra.tracking_events[work.extra.tracking_events.length - 1];
                        $work.find('.tracking-date').html(lastEvent[0] + ': ' + getDateTimeString(lastEvent[1]));
                    }
                }

                $work.find('.day').html(work.show_day ? 'Day ' + work.day : '');
            });
        });
    }

    function reorderActions($workflow) {
        $workflow.find('.work').each(function (index) {
            var pos = index + 1;
            $(this).attr('data-work-position', pos).find('.add-work.flow-add-button-small').attr('data-work-position', pos);
            $(this).data('work').position = pos;
        });
    }

    function calculateDateSpan(work1, work2) {
        if (!work1) {
            return 1;
        } else {
            var at1 = work1.executed_at > 0 ? work1.executed_at : work1.due_at;
            var at2 = work2.executed_at > 0 ? work2.executed_at : work2.due_at;
            return parseInt(at2 / (24 * 3600)) - parseInt(at1 / (24 * 3600)) + 1;
        }
    }

    function getApiUrl(frld, workflow_id, is_action, work_id) {
        var url = '/api/v1';
        if (!(frld && work_id > 0)) {
            url += '/' + (frld ? 'deal_workflows' : 'workflows') + '/' + workflow_id;
        }
        if (is_action) {
            url += '/' + (frld ? 'deal_actions' : 'actions');
        }
        if (work_id && work_id > 0) {
            url += '/' + work_id;
        }
        return url;
    }

    function popupWorkflowOptions() {
        if (!$optionsPopup) {
            $optionsPopup = $('<div id="workflow-options-popup" class="modal fade" role="dialog">\
                                    <div class="modal-dialog">\
                                      <div class="modal-content">\
                                        <div class="modal-header">\
                                          <button type="button" class="close" data-dismiss="modal">&times;</button>\
                                          <h4 class="modal-title"><i class="fa fa-gear"></i> Workflow Options</h4>\
                                        </div>\
                                        <div class="modal-body">\
                                            <form class="workflow-options-form">\
                                                <ul>\
                                                    <li>\
                                                        <label><input name="stop_on_respond" value="all" type="checkbox" /> Stop workflow if Client Responds</label>\
                                                        <ul>\
                                                            <li><label><input name="stop_on_respond" value="' + WorkflowClasses.TWITTER_RETWEET + '" type="checkbox" /> Via Retweet</label></li>\
                                                            <li><label><input name="stop_on_respond" value="' + WorkflowClasses.TWITTER_FOLLOW + '" type="checkbox" /> Via Follow</label></li>\
                                                            <li><label><input name="stop_on_respond" value="' + WorkflowClasses.TWITTER_REPLY + '" type="checkbox" /> Via Reply Tweet</label></li>\
                                                            <li><label><input name="stop_on_respond" value="twitter_direct" type="checkbox" /> Via Direct Message</label></li>\
                                                            <li><label><input name="stop_on_respond" value="' + WorkflowClasses.EMAIL_SEND + '" type="checkbox" /> Via Email Reply</label></li>\
                                                        </ul>\
                                                    </li>\
                                                </ul>\
                                            </form>\
                                        </div>\
                                        <div class="modal-footer">\
                                            <button type="button" class="btn btn-primary btn-save"><i class="fa fa-save"></i> Save</button>\
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>\
                                        </div>\
                                      </div>\
                                    </div>\
                                  </div>\
                                ');
            $optionsPopup.find('input:checkbox').iCheck({checkboxClass: 'icheckbox_flat-blue'});
        }

        $optionsPopup.find('form.workflow-options-form input:checkbox').iCheck('uncheck').iCheck('enable');
        $optionsPopup.find('button.btn-save').attr('disabled', false).html('<i class="fa fa-save"></i> Save');
        $optionsPopup.modal('show');

    }

    function saveOptions($form) {
        var params = $form.serializeArray();
        var stop_on_respond = [];
        $.each(params, function (i, param) {
            if (param.name == 'stop_on_respond') {
                stop_on_respond.push(param.value);
                if (param.value == 'all') {
                    return false;
                }
            }
        });
        if (!stop_on_respond.length) {
            stop_on_respond = 'false';
        }

        $optionsPopup.find('button.btn-save').attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Saving...');
        $.ajax({
            method: "PUT",
            url: getApiUrl(isFromLead, workflowId),
            data: {stop_on_respond: stop_on_respond},
            success: function (ret) {
//                if (ret != 0) {
//                    showErrorMessage('Workflow', 'Error occurred while saving options.');
//                    return;
//                }

                showSuccessMessage("Success", "The options have been saved successfully.");
                $optionsPopup.find('button.btn-save').html('<i class="fa fa-save"></i> Saved');
                $('.workflow[data-workflow-id="' + workflowId + '"]').data('workflow').stop_on_respond = stop_on_respond;
                $optionsPopup.delay(100).modal('hide');
            }
        });
    }

    scope.loadWorkflowList = function (include_actions, include_sources, is_enabled) {
        if (typeof include_sources == 'undefined') {
            include_sources = false;
        }
        if (typeof include_actions == 'undefined') {
            include_actions = false;
        }

        var params = {include_sources: include_sources, include_actions: include_actions};
        if (is_enabled != null) {
            params.is_enabled = is_enabled;
        }
        return $.getJSON('/api/v1/workspaces/' + current_workspace_id + '/workflows', params);
    };

    scope.loadWorkflow = function (id) {
        return $.getJSON('/api/v1/workflows/' + id);
    };

    function insertAtCursor(ele, txt)
    {
        if (document.selection) { //IE support
            ele.focus();
            var sel = document.selection.createRange();
            sel.text = txt;
            sel.moveStart('character', -txt.length);
            sel.select();
        } else if (ele.selectionStart || ele.selectionStart == '0') { //MOZILLA/NETSCAPE support
            var startPos = ele.selectionStart;
            var endPos = ele.selectionEnd;
            ele.value =
                    ele.value.substring(0, startPos)
                    + txt
                    + ele.value.substring(endPos, ele.value.length);
            ele.selectionStart = startPos;
            ele.selectionEnd = startPos + txt.length;
        } else { //Anyone else.
            ele.value += txt;
        }
    }

    function stopRecordingPlayer() {
        if ($recordingButton != null && $recordingButton.hasClass('recording')) {
            $recordingButton.click();
        }
        setTimeout(function () {
            if ($recordingButton[0] != null && $recordingButton[0].recordRTC) {
                console.log($recordingButton[0].recordRTC);
                if ($recordingButton[0].recordRTC.length) {
                    for (var i in $recordingButton[0].recordRTC) {
                        try {
                            $recordingButton[0].recordRTC[i].clearRecordedData();
                            console.log(i);
                        } catch (e) {
                            if (DevOptions.debug) {
                                console.log(e);
                            }
                        }
                    }
                } else {
                    $recordingButton[0].recordRTC.clearRecordedData();
                    console.log('cleared');
                }

                $recordingButton[0].recordRTC = null;
            }
            recordedBlob = null;
            thumbBlob = null;
            if (recordingPlayer) {
                recordingPlayer.pause();
            }
        }, 500);
    }
    return scope;
})();
