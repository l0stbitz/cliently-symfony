var Sidebar = (function () {
    var scope = {};

    var newLeadsCount = 0;

    var $sidebar = null;
    var $timelineContainer = null;
    var $newLeadsContainer = null;
    var $rejectedContainer = null;

    var qt = null;
    var qtTimer = null;

    var initialized = false;

    function init() {
        $(document).on('show.bs.tab', '#sidebar>ul.nav.nav-tabs>li>a[data-toggle="tab"]', function () {
            $sidebar.find('>.tab-content').addClass('open');
            $(this).addClass('being-activated');
            var tab = this.href.substr(this.href.indexOf('#'));
            switch (tab) {
                case '#sidebar-tab-pane-new-leads':
                    onNewLeadsTabShow();
                    break;
            }
        }).on('click', '#sidebar>ul.nav.nav-tabs>li>a[data-toggle="tab"]', function () {
            var $this = $(this);
            if ($this.hasClass('being-activated')) {
                $this.removeClass('being-activated');
            } else {
                $this.parent().removeClass('active');
                $this.attr('aria-expanded', false);
                $sidebar.find('>.tab-content').removeClass('open');
            }
        }).on('show.bs.tab', '#sidebar #sidebar-tab-pane-new-leads>ul.nav.nav-tabs>li>a[data-toggle="tab"]', function () {
            var tab = this.href.substr(this.href.indexOf('#'));
            switch (tab) {
                case '#sidebar-new-leads-tab-pane-new-leads':
                    newLeadsCount = 0;
                    updateNewLeadsCount();
                    onNewLeadsTabNewLeadsShow();
                    break;
                case '#sidebar-new-leads-tab-pane-rejected-leads':
                    onNewLeadsTabRejectedLeadsShow();
                    break;
            }
        });

        initialized = true;
    }
    scope.load = function () {
        if (!initialized) {
            init();
        }

        addSidebar();
    };
    function addSidebar() {
        $sidebar = $('#sidebar');
        if (!$sidebar.length) {
            $sidebar = $('\
                        <div id="sidebar">\
                            <ul class="nav nav-tabs nav-stacked">\
                                <li><a data-toggle="tab" href="#sidebar-tab-pane-new-leads"><i class="fa fa-user-plus"></i><br />New Leads<span class="badge"></span></a></li>\
                                <!--li><a data-toggle="tab" href="#sidebar-tab-pane-timeline"><i class="fa fa-clock-o"></i><br />Timeline<span class="badge">3</span></a></li>\
                                <li><a data-toggle="tab" href="#sidebar-tab-pane-tasks"><i class="fa fa-check-square-o"></i><br />Tasks<span class="badge">3</span></a></li>\
                                <li><a data-toggle="tab" href="#sidebar-tab-pane-calendar"><i class="fa fa-calendar"></i><br />Calendar<span class="badge">3</span></a></li-->\
                            </ul>\
                            <div class="tab-content">\
                                <div id="sidebar-tab-pane-new-leads" class="tab-pane fade">\
                                    <ul class="nav nav-tabs nav-justified">\
                                        <li><a data-toggle="tab" href="#sidebar-new-leads-tab-pane-new-leads">New Leads</a></li>\
                                        <li><a data-toggle="tab" href="#sidebar-new-leads-tab-pane-rejected-leads">Rejected</a></li>\
                                    </ul>\
                                    <div class="tab-content">\
                                        <div id="sidebar-new-leads-tab-pane-new-leads" class="tab-pane fade"></div>\
                                        <div id="sidebar-new-leads-tab-pane-rejected-leads" class="tab-pane fade"></div>\
                                    </div>\
                                </div>\
                                <div id="sidebar-tab-pane-timeline" class="tab-pane fade">Timeline</div>\
                                <div id="sidebar-tab-pane-tasks" class="tab-pane fade">Tasks</div>\
                                <div id="sidebar-tab-pane-calendar" class="tab-pane fade"></div>\
                            </div>\
                        </div>\
                        ').appendTo($('#page-clients.main-block-wrapper'));
            $timelineContainer = $sidebar.find('#sidebar-tab-pane-timeline');
            $newLeadsContainer = $sidebar.find('#sidebar-new-leads-tab-pane-new-leads');
            $rejectedContainer = $sidebar.find('#sidebar-new-leads-tab-pane-rejected-leads');

            updateNewLeadsCount();
        }
        
        if (InitParams.sidebar) {
            $sidebar.find('>ul.nav.nav-tabs>li>a[data-toggle="tab"][href="#sidebar-tab-pane-new-leads"]').tab('show');
            InitParams.sidebar = false;
        }

        loadTimeline();
    }

    scope.addNewLeadsCount = function (count) {
        newLeadsCount += count;
        updateNewLeadsCount();
    };

    function updateNewLeadsCount() {
        var $badge = $('#sidebar a[href="#sidebar-tab-pane-new-leads"] .badge').html(newLeadsCount);
        if (newLeadsCount) {
            $badge.show();
        } else {
            $badge.hide();
        }

    }
    function onNewLeadsTabShow() {
        if (!$('#sidebar #sidebar-tab-pane-new-leads>ul.nav.nav-tabs>li>a[data-toggle="tab"].active').length) {
            $('#sidebar #sidebar-tab-pane-new-leads>ul.nav.nav-tabs>li:first-child>a[data-toggle="tab"]').click();
            loadPopover();
        }
    }

    function onNewLeadsTabNewLeadsShow() {
        if ($newLeadsContainer.is(':empty')) {
            loadNewLeads();
        }
    }

    function onNewLeadsTabRejectedLeadsShow() {
        loadRejectedLeads();
    }

    function loadNewLeads() {
        $newLeadsContainer.html('<div class="loader"><i class="fa fa-spin fa-spinner fa-4x"></i></div>');

        $.get("/api/v1/workspaces/" + current_workspace_id + "/leads?is_enabled=1&include_source=1&include_workflow_status=1", function (newLeads) {
            var prevDate = 0;
            var $newLeads = $('<div class="new-leads">').appendTo($newLeadsContainer.empty());
            $.each(newLeads, function (i, lead) {
                var date = new Date(lead.created_at * 1000);
                date.setHours(0);
                date.setMinutes(0);
                date.setSeconds(0);
                var tc = date.getTime();
                if (!prevDate || prevDate != tc) {
                    var monthName = MonthNames[date.getMonth()];
                    monthName = monthName.substr(0, 3);
                    $('<div class="date-viewer"><div class="date"><span class="month">' + monthName + '</span><span class="day">' + date.getDate() + '</span></div></div>').appendTo($newLeads);
                }
                prevDate = tc;

                $createNewLead(lead).appendTo($newLeads);
            });
        });
    }

    function $createNewLead(lead, rejected) {
        var $lead = null;
        if (lead.source != null && lead.source._type) {
            switch (lead.source._type) {
                case LeadSourceTypes.TWITTER_TWEET:
                case LeadSourceTypes.TWITTER_USER:
                    $lead = $createNewLeadForTwitter(lead, rejected);
                    break;
                case LeadSourceTypes.DBPERSON:
                    $lead = $createNewLeadForDbperson(lead, rejected);
                    break;
            }
            $lead.attr('data-lead-type', lead.source._type);
        } else {
            $lead = $createNewLeadForManual(lead, rejected);
        }

        $lead.data('lead', lead).attr('data-lead-id', lead.id);

        $lead.on('click', '.btn-accept', function () {
            var $btn = $(this);
            var $lead = $btn.closest('.new-lead');
            var lead = $lead.data('lead');
            var activate_workflow = $btn.hasClass('btn-workflow-activation');
            var $xhr = acceptLead(lead, activate_workflow);
            if ($xhr) {
                var html = $btn.html();
                $btn.html((activate_workflow ? '<small>ADD TO</small><br />' : '') + '<i class="fa fa-spin fa-spinner"></i>Accepting...').attr('disabled', true);
                $xhr.done(function () {
                    $btn.html(html);
                    setTimeout(function () {
                        $lead.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                            $(this).remove();
                        });
                        if ($lead.prev().is('.date-viewer') && (!$lead.next().length || $lead.next().is('.date-viewer'))) {
                            $lead.prev().fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                                $(this).remove();
                            });
                        }
                    }, 100);
                }).fail(function (jqXHR) {
                    $btn.html(html).attr('disabled', false);
                    if (jqXHR.status === 402) {
                        $btn.html(html);
                        showErrorMessage('Error', 'Please purchase additional credits to accept more leads.');
                    } else if (jqXHR.status === 404 || jqXHR.status === 409) {
                        $btn.html(html);
                        showErrorMessage('Lead', 'Another Team Member has already accepted this lead.');
                        setTimeout(function () {
                            $lead.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                                $(this).remove();
                            });
                            if ($lead.prev().is('.date-viewer') && (!$lead.next().length || $lead.next().is('.date-viewer'))) {
                                $lead.prev().fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                                    $(this).remove();
                                });
                            }
                        }, 100);
                    } else if (jqXHR.status === 410) {
                        $btn.html(html);
                        showErrorMessage('Error', 'Email information is no longer valid. Lead has been deleted and no credits have been charged.');
                        setTimeout(function () {
                            $lead.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                                $(this).remove();
                            });
                            if ($lead.prev().is('.date-viewer') && (!$lead.next().length || $lead.next().is('.date-viewer'))) {
                                $lead.prev().fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                                    $(this).remove();
                                });
                            }
                        }, 100);
                    }
                });
            }
        }).on('click', '.btn-reject', function () {
            var $btn = $(this);
            var $lead = $btn.closest('.new-lead');
            var $xhr = rejectLead($lead.data('lead'));
            if ($xhr) {
                var $btn = $(this);
                var html = $btn.html();
                $btn.html('<i class="fa fa-spin fa-spinner"></i> Rejecting...').attr('disabled', true);
                $xhr.done(function () {
                    $btn.html(html);
                    setTimeout(function () {
                        $lead.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                            $(this).remove();//prependTo($rejectedContainer.find('.new-leads')).show();
                        });
                        if ($lead.prev().is('.date-viewer') && (!$lead.next().length || $lead.next().is('.date-viewer'))) {
                            $lead.prev().fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                                $(this).remove();
                            });
                        }
                    }, 100);
                }).fail(function () {
                    $btn.html(html).attr('disabled', false);
                });
            }
        });

        return $lead;
    }
    function $createNewLeadForTwitter(lead, rejected) {
        if (lead.source._type == LeadSourceTypes.TWITTER_TWEET) {
            var text            = lead.source.text ? decorateDealSourceText(lead.source.text, lead.action_values.keywords, 100) : '';
            var client_fullname = (lead.source.user != null && lead.source.user.fullname) ? lead.source.user.fullname : '';
            var client_username = (lead.source.user != null && lead.source.user.username) ? lead.source.user.username : '';
            var client_avatar   = (lead.source.user != null && lead.source.user.avatar)   ? lead.source.user.avatar : '/images/profile-blank.png';
        } else if (lead.source._type == LeadSourceTypes.TWITTER_USER) {
            var text = '';
            var client_fullname = lead.source.fullname ? lead.source.fullname : '';
            var client_username = lead.source.username ? lead.source.username : '';
            var client_avatar   = lead.source.avatar   ? lead.source.avatar   : '/images/profile-blank.png';
        }
        var $lead = $('\
            <div class="new-lead new-lead-from-twitter">\
                <span class="icon"><i class="fa fa-cliently-twitter"></i></span>\
                <span class="twitter-icon">' + (lead.source._type == LeadSourceTypes.TWITTER_TWEET ? '<i class="fa fa-edit"></i>' : '<img src="/images/big-egg.png" />') + '</span>\
                <div class="form-actions">\
                    <button class="btn btn-success btn-accept">Accept</button>\
                    <button class="btn btn-warning btn-accept btn-workflow-activation' + ((lead.workflow == null || ! lead.workflow.is_enabled) ? ' hide' : '') + '"><small>ADD TO</small><br />Workflow</button>\
                    <br/>\
                    ' + (rejected ? '' : '<button class="btn btn-reject">Reject</button>') + '\
                </div>\
                <div class="twitter-info">\
                    <span class="twitter-info-avatar"><img src="//' + client_avatar + '"/></span>\
                    <div class="twitter-info-header">\
                        <h4 class="twitter-info-fullname">' + client_fullname + '</h4>\
                        <span class="twitter-info-username">@' + client_username + '</span>\
                    </div>\
                    <p class="twitter-info-description">' + text + '</p>\
                    <div class="twitter-source-info">\
                        ' + (lead.source._type == LeadSourceTypes.TWITTER_TWEET ? '<label><span class="icon"><i class="fa fa-map-marker"></i></span>' + (lead.action_values.location ? lead.action_values.location : '') + '</label>' : '') + '\
                        <label><span class="icon"><i class="fa fa-search"></i></span>' + lead.action_values.keywords[0] + '</label>\
                        ' + (lead.source._type == LeadSourceTypes.TWITTER_TWEET ? '<label><span class="icon"><i class="fa fa-crosshairs"></i></span>' + (lead.action_values.range ? lead.action_values.range.toLocaleString() : '') + ' mi</label>' : '') + '\
                    </div>\
                </div>\
            </div>\
        ');
        $lead.find('.twitter-info .twitter-info-avatar img').error(function () {
            this.onerror = null;
            this.src = '/images/profile-blank.png';
        });

        return $lead;
    }
    function $createNewLeadForDbperson(lead, rejected) {
        var name       = lead.source.name       ? lead.source.name       : '';
        var email      = lead.source.email      ? lead.source.email      : '';
        var phone      = lead.source.phone      ? lead.source.phone      : '';
        var occupation = lead.source.occupation ? lead.source.occupation : '';
        var avatar     = lead.source.avatar     ? lead.source.avatar     : '/images/profile-blank.png';

        var values = lead.action_values;
        var countries = Dbperson.getDbpersonNamesByValues('locations', values.countries);
        var states = Dbperson.getDbpersonNamesByValues('locations', values.states);
        var metro_regions = Dbperson.getDbpersonNamesByValues('locations', values.metro_regions);
        var industries = Dbperson.getDbpersonNamesByValues('industries', values.industries);
        var revenues = Dbperson.getDbpersonNamesByValues('revenues', values.revenues);
        var sizes = Dbperson.getDbpersonNamesByValues('employee_sizes', values.employee_sizes);

        var locations = '';
        if (countries) {
            locations += '"' + countries.join('" "') + '"';
        }
        if (states) {
            locations += (locations ? ' ' : '') + '"' + states.join('" "') + '"';
        }
        if (metro_regions) {
            locations += (locations ? ' ' : '') + '"' + metro_regions.join('" "') + '"';
        }

        var $lead = $('\
            <div class="new-lead new-lead-from-dbperson">\
                <span class="icon"><img src="/images/company-gray.png" /></span>\
                <div class="form-actions">\
                    <button class="btn btn-success btn-accept">Accept</button>\
                    <button class="btn btn-warning btn-accept btn-workflow-activation' + ((lead.workflow == null || ! lead.workflow.is_enabled) ? ' hide' : '') + '"><small>ADD TO</small><br />Workflow</button>\
                    <br/>\
                    ' + (rejected ? '' : '<button class="btn btn-reject">Reject</button>') + '\
                </div>\
                <div class="dbperson">\
                    <span class="dbperson-avatar"><img src="' + avatar + '" /></span>\
                    <div class="dbperson-header">\
                        <h4 class="dbperson-fullname">' + name  + '</h4>\
                        <div class="verify-pane' + (email ? ' email-verified' : '') + (phone ? ' phone-verified' : '') + '">\
                            <div class="verify-icons">\
                                <span class="verify-icon verify-icon-email" title="' + email + '"><i class="fa fa-envelope"></i></span>\
                                <span class="verify-icon verify-icon-phone" title="' + phone + '"><i class="fa fa-phone"></i></span>\
                            </div>\
                        </div>\
                        <br /><span class="dbperson-role">' + occupation + '</span> | <span class="dbperson-company-name">' + (lead.source.company != null ? lead.source.company.name : '') + '</span>\
                    </div>\
                    <div class="dbperson-more">\
                        ' + (sizes ? '<label><span class="icon"><i class="fa fa-users"></i></span>' + '"' + sizes.join('" "') + '"' + '</label>' : '') + '\
                        ' + (revenues ? '<label><span class="icon"><i class="fa fa-dollar"></i></span>' + '"' + revenues.join('" "') + '"' + '</label>' : '') + '\
                        ' + (locations ? '<label><span class="icon"><i class="fa fa-map-marker"></i></span>' + locations + '</label>' : '') + '\
                        ' + (industries ? '<label><span class="icon"><i class="fa fa-paper-plane-o"></i></span>' + '"' + industries.join('" "') + '"' + '</label>' : '') + '\
                    </div>\
                </div>\
            </div>\
        ');

        $lead.find('.dbperson .dbperson-avatar img').error(function () {
            this.onerror = null;
            this.src = '/images/profile-blank.png';
        });

        return $lead;
    }
    function $createNewLeadForManual(lead, rejected) {
        var client_avatar = lead.clients[0] && lead.clients[0].source && lead.clients[0].source.avatar && !DevOptions.debug ? lead.clients[0].source.avatar : '/images/profile-blank.png';
        var $lead = $('\
            <div class="new-lead new-lead-from-dbperson">\
                <span class="icon"><img src="/images/logo.png" /></span>\
                <div class="form-actions">\
                    <button class="btn btn-success btn-accept">' + (lead.is_accepted > 0 ? 'Restore' : 'Accept') + '</button>\
                    <button class="btn btn-warning btn-accept btn-workflow-activation' + ((lead.workflow == null || ! lead.workflow.is_enabled) ? ' hide' : '') + '"><small>ADD TO</small><br />Workflow</button>\
                    <br/>\
                    ' + (rejected ? '' : '<button class="btn btn-reject">Reject</button>') + '\
                </div>\
                <div class="manual-info">\
                    <span class="manual-info-avatar"><img src="' + client_avatar + '" /></span>\
                    <div class="manual-info-header">\
                        <h4 class="manual-info-fullname">' + lead.clients[0].name + '</h4>\
                    </div>\
                </div>\
            </div>\
        ');

        $lead.find('.manual-info .manual-info-avatar img').error(function () {
            this.onerror = null;
            this.src = '/images/profile-blank.png';
        });

        return $lead;
    }

    function acceptLead(lead, activate_deal_workflow) {
        if (!checkIfLeadAcceptable()) {
            return;
        }

        var data = {};
        if (activate_deal_workflow) data.enable_workflow = 1;

        return $.ajax({
            method: 'POST',
            data: data,
            url: '/api/v1/leads/' + lead.id + '/accept',
            success: function (lead) {
                showSuccessMessage("Success", "A lead has been accepted successfully.");
                var accepting = lead.is_accepted <= 0;
                lead.is_accepted = 1;
                var $card = Leads.$insertClient(lead);
                if (accepting) {
                    // var verify_needed = true;
                    // if (verify_needed) {
                    //     Leads.verifyLead($card, activate_deal_workflow);
                    // } else {
                    //     Workflow.setInitialDealWorkflowIsEnabled(lead.id, 'parent');
                    // }

                    if (typeof analytics !== 'undefined' && analytics !== null) {
                        var action_values = lead.action_values;
                        var data = {
                            action_type: 'window',
                            source_type: lead.source != null ? lead.source._type : null
                        };
                        if (lead.source._type == LeadSourceTypes.TWITTER_TWEET) {
                            data.source_keywords = action_values.keywords[0];
                            data.source_location = action_values.location;
                            data.source_range = action_values.range;
                            //source_user: $("#qtip-0 iframe").contents().find(".TweetAuthor-screenName").text(),
                            //source_text: $("#qtip-0 iframe").contents().find(".Tweet-text").text()
                        }
                        analytics.track('Lead Accepted', data);
                    }
                }
            }
        });
    }
    function rejectLead(lead) {
        return $.ajax({
            type: 'PUT',
            data: {is_enabled: false},
            url: '/api/v1/leads/' + lead.id,
            success: function () {
                showSuccessMessage("Success", "A lead has been deleted successfully.");

                if (typeof analytics !== 'undefined' && analytics !== null) {
                    var action_values = lead.action_values;
                    var data = {
                        action_type: 'window',
                        source_type: lead.source._type
                    };
                    if (lead.source._type == LeadSourceTypes.TWITTER_TWEET) {
                        data.source_keywords = action_values.keywords[0];
                        data.source_location = action_values.location;
                        data.source_range = action_values.range;
                        //source_user: $("#qtip-0 iframe").contents().find(".TweetAuthor-screenName").text(),
                        //source_text: $("#qtip-0 iframe").contents().find(".Tweet-text").text()
                    }
                    analytics.track('Lead Rejected', data);
                }
            }
        });
    }

    function loadPopover() {
        var popHTML = '\
            <div class="lead-source" data-source-type="' + LeadSourceTypes.TWITTER_TWEET + '">\
                <div class="twitter-info-container">\
                    <div class="twitter-info-blocker active"></div>\
                    <div class="twitter-info"></div>\
                </div>\
            </div>\
            <div class="lead-source" data-source-type="' + LeadSourceTypes.TWITTER_USER + '">\
            </div>\
            <div class="lead-source" data-source-type="' + LeadSourceTypes.DBPERSON + '">\
            </div>\
        ';
        qt = $newLeadsContainer.qtip({
            id: 'sidebar-lead-source-popover',
            prerender: true,
            content: {
                title: '<span class="fa fa-cliently-twitter"></span> Lead Source',
                text: popHTML
            },
            style: {classes: 'qtip-rounded qtip-shadow qtip-cluetip qtip-blue'},
            position: {
                //target: "mouse", // Position it where the click was...
                my: 'center left', // Position my top left...
                at: 'center right', // at the bottom right of...
                viewport: $sidebar,
                adjust: {
                    mouse: true, x: 0, y: 0,
                    method: 'none shift'
                }
            },
            show: {
                //delay: 500,
                effect: function (offset) {
                    $(this).stop().fadeIn(500);
                }
            },
            hide: {
                event: 'mouseleave',
                inactive: 30000,
                fixed: true,
                effect: function (offset) {
                    $(this).stop().fadeOut(500);
                }
            },
            events: {
                hide: function () {
                    $(".has-popover").removeClass('has-popover');
                }
            }
        });
        qt.qtip('hide');
        $(qt.qtip('api').elements.tooltip).on('mouseover', function () {
            if (qtTimer) {
                clearTimeout(qtTimer);
                qtTimer = null;
            }
        });

        $(document).on('mousemove', function (e) {
            if ($(e.target).closest("#sidebar .new-leads .new-lead .icon").length <= 0) {
                if ($(".has-popover").length > 0 && !qtTimer) {
                    qtTimer = setTimeout(function () {
                        qt.qtip('hide');
                        qt.qtip('disable');
                        qtTimer = null;
                    }, 1000);
                }
                return;
            }
            clearTimeout(qtTimer);
            qtTimer = null;
            var $lead = $(e.target).closest(".new-lead");
            if ($lead.hasClass("has-popover")) {
                return;
            }

            qt.qtip('option', 'position.target', e.target);
            qt.qtip('option', 'show.target', e.target);
            qt.qtip('enable');
            $(".has-popover").removeClass('has-popover');
            $lead.addClass('has-popover');

            var lead = $lead.data('lead');
            var action_values = lead.action_values;

            var $content = qt.qtip('api').elements.content;
            var $popoverSource = $content.find('.lead-source').hide().filter('[data-source-type="' + lead.source._type + '"]').show();
            switch (lead.source._type) {
                case LeadSourceTypes.TWITTER_TWEET:
                    qt.qtip('option', 'content.title', '<span class="popover-title-icon"><i class="fa fa-cliently-twitter"></i></span> Lead Source - ' + action_values.location + ', ' + action_values.range + ' miles, ' + action_values.keywords[0]);
                    if (twttr && twttr.widgets) {
                        var $twitterInfo = $popoverSource.find('.twitter-info').html("<div class='loader' style='padding: 10px 0'><i class='fa fa-spin fa-spinner fa-2x'></i></div>");
                        twttr.widgets.createTweet(lead.source.code, $twitterInfo[0]).then(function (el) {
                            $('iframe[id^=twitter-widget-]').each(function () {
                                $(".loader", $twitterInfo).remove();
                                $(this).css('margin-top', '3px');
                                decorateTweet(this, action_values.keywords, true);
                            });

                            qt.qtip('api').reposition(null, false);
                        });
                    }
                    break;
                case LeadSourceTypes.TWITTER_USER:
                    qt.qtip('option', 'content.title', '<span class="popover-title-icon"><i class="fa fa-cliently-twitter"></i></span> Lead Source - ' + action_values.keywords[0]);
                    var $twitterItem = $popoverSource.find('.twitter-item');
                    if (!$twitterItem.length) {
                        $twitterItem = $('\
                            <div class="twitter-item">\
                                <div class="twitter-item-inner">\
                                    <img class="twitter-item-profile-image" src="/images/profile-blank.png" />\
                                    <!--span class="icon"><i class=" fa fa-cliently-twitter"></i></span-->\
                                    <div class="twitter-item-info">\
                                        <div class="twitter-item-header">\
                                            <h4 class="fullname">Full Name</h4>\
                                            <span>@</span><span class="username">username</span>\
                                        </div>\
                                    </div>\
                                    <div class="twitter-item-description">\
                                    </div>\
                                    <div class="twitter-item-metrics">\
                                        <span class="location"></span>\
                                    </div>\
                                </div>\
                            </div>\
                        ').appendTo($popoverSource);
                        $twitterItem.find('img').error(function () {
                            this.onerror = null;
                            this.src = '/images/profile-blank.png';
                        });
                    }
                    $twitterItem
                            .find("img.twitter-item-profile-image").attr('src', '//' + lead.source.avatar).end()
                            .find("h4.fullname").html(decorateDealSourceText(lead.source.fullname, lead.action_values.keywords)).end()
                            .find("span.username").html(decorateDealSourceText(lead.source.username, lead.action_values.keywords)).end()
                            .find(".twitter-item-description").html(decorateDealSourceText(lead.source.description, lead.action_values.keywords)).end()
                            .find("span.location").html('<i class="fa fa-map-marker"></i> ' + decorateDealSourceText(lead.source.location, lead.action_values.keywords) + '');
                    break;
                case LeadSourceTypes.DBPERSON:
                    qt.qtip('option', 'content.title', '<span class="popover-title-icon"><img src="/images/company-gray.png" /></span> Lead Source - Database');
                    var $metricsInfo = $popoverSource.find('.metrics-info');
                    if (!$metricsInfo.length) {
                        $metricsInfo = $('<div class="metrics-info" />').appendTo($popoverSource);
                    }
                    $metricsInfo.empty();
                    if (lead.source.company) {
                        if (lead.source.company.employee_count != null) {
                            $metricsInfo.append('<span><i class="fa fa-users"></i> ' + lead.source.company.employee_count + '</span>');
                        }
                        if (lead.source.company.revenue != null) {
                            $metricsInfo.append('<span><i class="fa fa-bank"></i> ' + lead.source.company.revenue + '</span>');
                        }
                        if (lead.source.company.industries != null) {
                            $metricsInfo.append('<span><i class="fa fa-suitcase"></i> ' + (lead.source.company.industries ? lead.source.company.industries.join(', ') : '') + '</span>');
                        }
                        if (lead.source.company.location != null) {
                            $metricsInfo.append('<span><i class="fa fa-map-marker"></i> ' + lead.source.company.location + '</span>');
                        }
                    }
                    break;
            }

            qt.qtip('api').reposition(null, false);
            qt.qtip('show');
        });
    }

    function loadRejectedLeads() {
        var prevDate = 0;
        $rejectedContainer.html('<div class="loader"><i class="fa fa-spin fa-spinner fa-4x"></i></div>');
        $.get("/api/v1/workspaces/" + current_workspace_id + "/leads?is_enabled=0&include_source=1&include_workflow_status=1", function (rejectedLeads) {
            var $newLeads = $('<div class="new-leads">').appendTo($rejectedContainer.empty());
            $.each(rejectedLeads, function (i, lead) {
                var date = new Date(lead.created_at * 1000);
                date.setHours(0);
                date.setMinutes(0);
                date.setSeconds(0);
                var tc = date.getTime();
                if (!prevDate || prevDate != tc) {
                    var monthName = MonthNames[date.getMonth()];
                    monthName = monthName.substr(0, 3);
                    $('<div class="date-viewer"><div class="date"><span class="month">' + monthName + '</span><span class="day">' + date.getDate() + '</span></div></div>').appendTo($newLeads);
                }
                prevDate = tc;

                $createNewLead(lead, true).appendTo($newLeads);
            });
        });
    }

    function loadTimeline() {
        var actions = [
            {
                id: 1,
                module_class: 'email',
                "class": 'email_send',
                values: {
                    title: 'Email subject',
                    from: 'lead@gmail.com',
                    to: 'user@gmail.com',
                    msg: 'Ut wisi enim and minim veniam, quis nostrud aliquip ex ea commodo consequat. Duis autem'
                },
                lead: {
                    lead_name: 'Lead Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                user: {
                    user_name: 'User Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                created_at: (new Date()).getTime() / 1000
            },
            {
                id: 2,
                module_class: 'task',
                "class": 'task_add',
                values: {
                    task_type: 'Call',
                    task_status: 0,
                    task_te: (new Date()).getTime() / 1000,
                    task_desc: 'Ut wisi enim and minim veniam, quis nostrud aliquip ex ea commodo consequat. Duis autem'
                },
                lead: {
                    lead_name: 'Lead Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                user: {
                    user_name: 'User Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                created_at: (new Date()).getTime() / 1000 - 3600 * 24
            },
            {
                id: 3,
                module_class: 'note',
                "class": 'note_add',
                values: {
                    note_desc: 'Ut wisi enim and minim veniam, quis nostrud aliquip ex ea commodo consequat. Duis autem'
                },
                lead: {
                    lead_name: 'Lead Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                user: {
                    user_name: 'User Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                created_at: (new Date()).getTime() / 1000 - 3600 * 24 * 2
            },
            {
                id: 4,
                module_class: 'twitter',
                "class": 'twitter_follow',
                values: {
                },
                lead: {
                    lead_name: 'Lead Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                user: {
                    user_name: 'User Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                created_at: (new Date()).getTime() / 1000 - 3600 * 24 * 3
            },
            {
                id: 5,
                module_class: 'twitter',
                "class": 'twitter_tweet',
                values: {
                    description: 'Lorem ipsum dolor sit amet. consectetur adipiscing elit. Alitquam blandit risus sed leo',
                    sender: {
                        avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg',
                        fullname: 'Lead Twitter Name',
                        username: 'lead_handle'
                    }
                },
                lead: {
                    lead_name: 'Lead Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                user: {
                    user_name: 'User Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                created_at: (new Date()).getTime() / 1000 - 3600 * 24 * 3
            },
            {
                id: 6,
                module_class: 'lead',
                "class": 'lead_stage_move',
                values: {
                    from_stage_name: 'Stage 1',
                    to_stage_name: 'Stage 2'
                },
                lead: {
                    lead_name: 'Lead Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                user: {
                    user_name: 'User Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                created_at: (new Date()).getTime() / 1000 - 3600 * 24 * 3
            },
            {
                id: 7,
                module_class: 'lead',
                "class": 'lead_accept',
                values: {
                    source_info: {
                        location: 'New York, NY, United States',
                        range: 250,
                        keywords: 'engagement'
                    },
                    twitter_info: {
                        description: 'Lorem ipsum dolor sit amet. consectetur adipiscing elit. Alitquam blandit risus sed leo',
                        sender: {
                            avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg',
                            fullname: 'Lead Twitter Name',
                            username: 'lead_handle'
                        }
                    }
                },
                lead: {
                    lead_name: 'Lead Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                user: {
                    user_name: 'User Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                created_at: (new Date()).getTime() / 1000 - 3600 * 24 * 3
            },
            {
                id: 8,
                module_class: 'workflow',
                "class": 'workflow_add',
                values: {
                    workflow_name: 'Workflow 1'
                },
                lead: {
                    lead_name: 'Lead Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                user: {
                    user_name: 'User Name',
                    avatar: '//pbs.twimg.com/profile_images/478284874/avatar_copy_200x200.jpg'
                },
                created_at: (new Date()).getTime() / 1000 - 3600 * 24 * 3
            }
        ];
        $timelineContainer.empty().append(Timeline.$createTimeline({
            id: 33333,
            actions: actions
        }));
    }

    return scope;
})();