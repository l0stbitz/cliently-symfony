var UserConnector = (function () {
    var scope = {};

    var UserConnectorWizardSteps = {
        GETTING_STARTED: 'getting-started',
        GMAIL_CONNECT: 'gmail-connect',
        TWITTER_CONNECT: 'twitter-connect',
        TWITTER_TWEET: 'twitter-tweet'
    };

    var UserConnectorWizardStepTitles = {};
    UserConnectorWizardStepTitles[UserConnectorWizardSteps.GETTING_STARTED] = '<i class="fa fa-cubes"></i> Getting Started';
    UserConnectorWizardStepTitles[UserConnectorWizardSteps.GMAIL_CONNECT] = 'Send Automatic Emails with Gmail';
    UserConnectorWizardStepTitles[UserConnectorWizardSteps.TWITTER_CONNECT] = 'Find Leads on Twitter';
    UserConnectorWizardStepTitles[UserConnectorWizardSteps.TWITTER_TWEET] = 'Get 5 more leads for free!';

    var UserConnectorWizardStepDescriptions = {};
    UserConnectorWizardStepDescriptions[UserConnectorWizardSteps.GETTING_STARTED] = 'Getting Started';
    UserConnectorWizardStepDescriptions[UserConnectorWizardSteps.GMAIL_CONNECT] = 'Use workflows to send emails automatically to your clients.<br />Track and receive emails directly inside the application.';
    UserConnectorWizardStepDescriptions[UserConnectorWizardSteps.TWITTER_CONNECT] = 'We find great leads on Twitter in your industry based on critical business events.<br />To do so, we need access to your Twitter account.';
    UserConnectorWizardStepDescriptions[UserConnectorWizardSteps.TWITTER_TWEET] = 'Simply tweet and we\'ll give you five more leads for free.';

    var UserConnectorWizardStepSkipNotes = {};
    UserConnectorWizardStepSkipNotes[UserConnectorWizardSteps.GETTING_STARTED] = '';
    UserConnectorWizardStepSkipNotes[UserConnectorWizardSteps.GMAIL_CONNECT] = 'you won\'t be able to send emails through Cliently.';
    UserConnectorWizardStepSkipNotes[UserConnectorWizardSteps.TWITTER_CONNECT] = 'you won\'t be able to search Twitter for leads.';
    UserConnectorWizardStepSkipNotes[UserConnectorWizardSteps.TWITTER_TWEET] = 'No thanks, I don\'t want any free leads.';

    var $wizard = null;
    var $saveButton = null;

    var step = null;

    var twitter_handle = null;

    var initialized = false;

    function init() {
        $(document).on('click', '#user-connector-wizard.modal .btn-save', function (e) {
            var skip = $(this).hasClass('btn-skip');
            moveNext(skip);
            return false;
        });

        initialized = true;
    }

    scope.show = function () {
        if (!initialized) {
            init();
        }

        popupUserConnectorWizard(UserConnectorWizardSteps.GMAIL_CONNECT);
    };

    function popupUserConnectorWizard(st) {
        if (!$wizard) {
            $wizard = $('<div id="user-connector-wizard" class="modal fade" role="dialog">\
                            <div class="modal-dialog">\
                                <div class="modal-content">\
                                    <div class="modal-header">\
                                        <h4 class="modal-title"></h4>\
                                        <h5 class="modal-description"></h5>\
                                    </div>\
                                    <div class="modal-body">\
                                        <!--ul class="statuses">\
                                            <li class="status" data-step="' + UserConnectorWizardSteps.GETTING_STARTED + '"><span class="point"><span class="title">Getting Started</span></span></li>\
                                            <li class="status" data-step="' + UserConnectorWizardSteps.GMAIL_CONNECT + '"><span class="point"><span class="title">Email</span></span></li>\
                                            <li class="status" data-step="' + UserConnectorWizardSteps.TWITTER_CONNECT + '"><span class="point"><span class="title">Social</span></span></li>\
                                        </ul-->\
                                    </div>\
                                    <div class="modal-footer">\
                                        <!--button type="button" class="btn btn-primary btn-save">Next <i class="fa fa-angle-double-right"></i></button-->\
                                        <a href="#" class="btn-save btn-skip">Skip for now</a><span class="skip-note"></span>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>').modal({
                show: false,
                keyboard: false,
                backdrop: 'static'
            });

            $saveButton = $wizard.find('.btn-save');
        }

        $wizard.attr('data-step', st);
        step = st;

        $wizard.find('ul.statuses li.status.current').removeClass('current').addClass('completed');
        $wizard.find('ul.statuses li.status[data-step="' + step + '"]').addClass('current');

        $wizard.find('.modal-header .modal-title').html(UserConnectorWizardStepTitles[step]);
        $wizard.find('.modal-header .modal-description').html(UserConnectorWizardStepDescriptions[step]);
        $wizard.find('.modal-footer .skip-note').html(UserConnectorWizardStepSkipNotes[step] ? ' (' + UserConnectorWizardStepSkipNotes[step] + ')' : '');

        var $step = $wizard.find('.user-connector-wizard-step[data-step="' + step + '"]');
        if (!$step.length) {
            $step = $createUserConnectorWizardStep(step).appendTo($wizard.find('.modal-body'));
        }

        if ($wizard.is(':visible')) {
            $wizard.find('.user-connector-wizard-step:visible').stop().fadeOut({duration: 500, queue: false}).slideUp(500);
            $step.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
        } else {
            $wizard.find('.user-connector-wizard-step').hide();
            $step.show();
            $wizard.modal('show');
        }

        if (step == UserConnectorWizardSteps.TWITTER_TWEET) {
            $saveButton.html('Skip');
        }
    }

    function $createUserConnectorWizardStep(st) {
        var $step = $('<div />', {
            class: 'user-connector-wizard-step',
            id: 'user-connector-wizard-step-' + st,
            'data-step': st
        });

        switch (st) {
            case UserConnectorWizardSteps.GETTING_STARTED:
                $step.append('\
                    <iframe src="//fast.wistia.net/embed/iframe/0z21wjtiwj" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen></iframe>\
                    <script src="//fast.wistia.net/assets/external/E-v1.js" async></script>\
                ');
                break;
            case UserConnectorWizardSteps.GMAIL_CONNECT:
                $step.append('\
                    <div class="social-integration-handler-container"></div>\
                    <div class="social-integration-note">* We will never send emails without asking you first.</div>\
                ');
                var callback = function (integration) {
                    if (integration) {
                        popupUserConnectorWizard(UserConnectorWizardSteps.TWITTER_CONNECT);
                    }
                };
                SocialIntegrator.reloadIntegration(SocialIntegrator.$createGoogleIntegration(callback).appendTo($step.find(".social-integration-handler-container")));
                break;
            case UserConnectorWizardSteps.TWITTER_CONNECT:
                $step.append('\
                    <div class="social-integration-handler-container"></div>\
                    <div class="social-integration-note">* We will never send messages, follow or do other things without asking you first.</div>\
                ');
                var callback = function (integration) {
                    if (integration) {
                        twitter_handle = integration.handle;
                        popupUserConnectorWizard(UserConnectorWizardSteps.TWITTER_TWEET);
                    }
                };
                SocialIntegrator.reloadIntegration(SocialIntegrator.$createTwitterIntegration(callback).appendTo($step.find(".social-integration-handler-container")));
                break;
            case UserConnectorWizardSteps.TWITTER_TWEET:
                $step.append('\
                    <form class="form">\
                        <div class="form-group">\
                            <label class="handle">@' + twitter_handle + ': </label>\
                            <textarea readonly="readonly" name="message" class="form-control">Hey guys, found this great tool to get great leads. Check out @getcliently https://cliently.com</textarea>\
                        </div>\
                        <div class="row">\
                            <div class="col-md-12 text-right">\
                                <button class="btn btn-primary btn-save"><i class="fa fa-cliently-twitter"></i> Tweet!</button>\
                            </div>\
                        </div>\
                    </form>\
                ');
                break;
        }

        return $step;
    }

    function moveNext(skip) {
        switch (step) {
            case UserConnectorWizardSteps.GETTING_STARTED:
                popupUserConnectorWizard(UserConnectorWizardSteps.GMAIL_CONNECT);
                break;
            case UserConnectorWizardSteps.GMAIL_CONNECT:
                popupUserConnectorWizard(UserConnectorWizardSteps.TWITTER_CONNECT);
                break;
            case UserConnectorWizardSteps.TWITTER_CONNECT:
                var $step = $wizard.find('.user-connector-wizard-step[data-step="' + UserConnectorWizardSteps.TWITTER_CONNECT + '"]');
                if ($step.find('.social-integration-handler').hasClass('active')) {
                    popupUserConnectorWizard(UserConnectorWizardSteps.TWITTER_TWEET);
                    break;
                }
            case UserConnectorWizardSteps.TWITTER_TWEET:
                user_wizard = UserWizards.NONE;
                var data = {wizard: user_wizard};
                if ( ! skip) {
                    data.twitter_viral = true;
                }
                $.ajax({
                    url: '/api/v1/users/me',
                    method: 'PUT',
                    data: data
                }).done(function () {
                    loadWorkingPlace();
                    Layout.activatePage(Pages.CLIENTS, false, true);
                    if (typeof analytics !== 'undefined' && analytics !== null) {
                        analytics.track('Account Connector Finished');
                    }
                });
                $wizard.modal('hide');

                if (workspaces[current_workspace_index].membership === 'owner' || workspaces[current_workspace_index].membership === 'admin') {
                    Layout.activatePage(Pages.SOURCES);
                    //Leads.reloadClients();
                    if (typeof inline_manual_player !== 'undefined' && inline_manual_player !== null) {
                        inline_manual_player.activateTopic(19465);
                    }
                } else {
                    if (typeof inline_manual_player !== 'undefined' && inline_manual_player !== null) {
                        // inline_manual_player.activateTopic(19465);
                    }
                }
                break;
        }
    }

    return scope;
})();
