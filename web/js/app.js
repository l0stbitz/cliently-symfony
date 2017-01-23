//User Data
var UserData = null;

var accounts;
var current_account_index = null;
var current_account_id = null;

var workspaces;
var current_workspace_index = null;
var current_workspace_id = null;

var current_account_workspace_id = null;

var pipelines;
var current_pipeline_index = null;
var current_pipeline_id = null;

var workspace_members = null;

var workflows = null;

var current_owner_id = null;

var plan_class = 0;

var UserWizards = {
    NONE: -1,
    USER_INFO: 0,
    USER_CONNECTOR: 1,
    TOUR: 2
};
var user_wizard = -1;

var TourSteps = {
    COMPLETED: -1,
    START: 0,
    CLIENT_DETAILS: 2,
    CLIENT_DETAILS_TASKS: 4,
    LEFT_MENU: 5,
    LEAD_SOURCE: 6,
    LAST: 7
};
var tour_step = TourSteps.COMPLETED;

var MonthNames = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

function parseInitPathAndOptions(context) {
    var emptyFilter = function (ele) {
        return ele !== '';
    };
    var initParams = {
        page: Pages.CLIENTS,
        settingsPage: SettingsPages.USER,
        paths: null,
        sidebar: 0
    };
    var defaultDevOptions = {
        debug: false,
        localdev: false,
        showWorkInProgress: false,
        showSampleData: false,
        wizard: UserWizards.NONE
    };

    var devOptionNames = {
        debug: 'debug',
        localdev: 'localdev',
        showworkinprogress: 'showWorkInProgres',
        showsampledata: 'showSampleData',
        wizard: 'wizard'
    };


    var devOptions = $.extend({}, defaultDevOptions);

    if (window.location.pathname + window.location.search) {
        var pp = window.location.pathname + window.location.search; //pp: Paths and Parameters
        var paramsPos = pp.indexOf('?');
        if (paramsPos > -1) {
            var params = pp.substr(paramsPos + 1).split('&').filter(emptyFilter);
            pp = pp.substr(0, paramsPos);
            for (var i in params) {
                var param = params[i];
                var value = true;
                var pos = param.indexOf('=');
                if (pos > -1) {
                    value = param.substr(pos + 1);
                    param = param.substr(0, pos).toLowerCase();
                }

                if (typeof devOptionNames[param] != 'undefined') {
                    devOptions[devOptionNames[param]] = value === 'true' ? true : (value === 'false' ? false : value);
                }
            }
        }

        var paths = pp.split('/').filter(emptyFilter);
        if (paths.length > 0) {
            initParams.page = paths[0];
            paths.shift();
            if (initParams.page == Pages.SETTINGS && paths.length) {
                initParams.settingsPage = paths[0];
                paths.shift();
            }
            initParams.paths = paths;
        }
    }

    context.DefaultDevOptions = defaultDevOptions;
    context.DevOptions = devOptions.debug ? devOptions : defaultDevOptions;
    context.InitParams = initParams;
}
(function (context) {
    parseInitPathAndOptions(context);
    context.onpopstate = function () {
        parseInitPathAndOptions(context);
        Layout.activatePage(context.InitParams.page, context.InitParams.settingsPage);
    };
})(window);

$.fn.serializeObject = function ()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

$.fn.reset = function () {
    this.each(function () {
        if (typeof this.reset != 'undefined') {
            this.reset();
        }
    });

    return this;
};

$.fn.editable.defaults.ajaxOptions = {type: 'PUT'};
$.fn.editable.defaults.params = function (params) {
    var data = {};
    data[params.name] = params.value;
    return data;
};
$.fn.editable.defaults.error = function(response, newValue) {
    return '';
}

$.fn.qtip.zindex = 1019;

ss.uploadSetup({
    url: '/api/v1/uploads',
    name: 'uploadfile', // upload parameter name        
    progressUrl: '/uploads/x-upload-session-progress', // enables cross-browser progress support (more info below)
    sessionProgressUrl: '/uploads/x-upload-session-progress', // enables cross-browser progress support (more info below)
    multiple: false,
    multipart: true,
    queue: false,
    responseType: 'json',
    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
    accept: 'image/*',
    debug: DevOptions.debug,
    maxSize: 1024, // kilobytes
    //hoverClass: 'ui-state-hover',
    //focusClass: 'ui-state-focus',
    //disabledClass: 'ui-state-disabled',
    onSubmit: function (filename, extension) {
        //this.setFileSizeBox(sizeBox); // designate this element as file size container
        //this.setProgressBar(progress); // designate as progress bar
    },
    onSizeError: function () {
        showErrorMessage("Error!!!", "Files may not exceed 1MB.");
    },
    onExtError: function () {
        showErrorMessage("Error!!!", "Invalid file type. Please select a PNG, JPG, GIF image.");
    }
});

$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1050 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function () {
        $('.modal-backdrop:not(.modal-backdrop-stacked)').css('z-index', zIndex - 1).addClass('modal-backdrop-stacked');
    }, 0);
}).on('shown.bs.modal', '.modal', function () {
    focusOnPopup($(this).find('>.modal-dialog'));
}).on('click', '.modal>.modal-dialog>.popup-focus', function () {
    return false;
});


function showGlobalLoader(msg) {
    $("#global-loader-back, #global-loader").stop().fadeIn();
    if (typeof msg != 'undefined' && msg) {
        $("#global-loader-text").html(msg).stop().fadeIn();
    }
}

function hideGlobalLoader() {
    $("#global-loader-back, #global-loader, #global-loader-text").stop().fadeOut('fast');
}

$(document).ready(function () {
    Layout.load();
    loadApp();
});

function loadApp() {
    var __loaders = [loadWorkingPlace, Dbperson.load];
    var __loadingCount = __loaders.length;
    var __loadedCount = 0;
    function done() {
        if (++__loadedCount >= __loadingCount) {
            hideGlobalLoader();
            startApp();
        }
    }

    $.each(__loaders, function (i, loader) {
        loader().done(done);
    });
}

function startApp() {
    user_wizard = UserData.wizard;

    if (DevOptions.debug && DevOptions.wizard > UserWizards.NONE) {
        user_wizard = DevOptions.wizard;
    }

    if (user_wizard < UserWizards.TOUR) {
        // Layout.activatePage(InitParams.page);
    } else {
        tour_step = user_wizard - 2;
        user_wizard = UserWizards.NONE;
        openTour();
    }

    Search.load();
}

//Loading profile including image
function loadWorkingPlace(workspace_id, page, subpage, refresh) {
    return $.getJSON('/api/v1/users/me?include_accounts=1&include_accounts_membership=1&include_workspaces=1&include_workspaces_membership=1&include_integrations=1', function (data) {
        if (data) {
            UserData   = data;
            accounts   = data.accounts;
            workspaces = data.workspaces;

            if (typeof workspace_id === 'undefined' || workspace_id === null) {
                workspace_id = false;
            }

            current_workspace_index = null;

            $.each(workspaces, function (i, workspace) {
                if ( ! workspace_id) {
                    if (workspace.membership.is_enabled) {
                        current_workspace_index = i;
                        return false;
                    }
                } else if (workspace.id === workspace_id) {
                    if (workspace.membership.is_enabled) {
                        current_workspace_index = i;
                    }
                    return false;
                }
            });

            if (current_workspace_index === null) {
                window.location = '/message/suspended-workspace-membership';
            }

            var account_id = data.workspaces[current_workspace_index].account_id;;

            current_account_index = null;

            $.each(accounts, function (i, account) {
                if ( ! account_id) {
                    if (account.membership.is_enabled) {
                        current_account_index = i;
                        return false;
                    }
                } else if (account.id === account_id) {
                    if (account.membership.is_enabled) {
                        current_account_index = i;
                    }
                    return false;
                }
            });

            if (current_account_index === null) {
                // window.location = '/message/suspended-account-membership';
                plan_class = 0;
            } else {
                current_account_id = data.accounts[current_account_index].id;
                plan_class = data.accounts[current_account_index].plan_class;
            }

            current_workspace_id = data.workspaces[current_workspace_index].id;

            if (data.avatar) {
                $("#user-profile-image").attr('src', '/uploads/users/avatars/' + data.avatar + '_200x200.jpg');
            } else if (data.integration_avatar) {
                if (data.integration_type == 2) {
                    $("#user-profile-image").attr('src', '//' + data.integration_avatar.replace(/normal/, '200x200'));
                } else if (data.integration_type == 3) {
                    $("#user-profile-image").attr('src', '//' + data.integration_avatar + '?sz=200');
                }
            } else {
                $("#user-profile-image").attr('src', '/images/profile-blank.png');
            }
            $("#user-profile-image").fadeIn();

            if (data.workspaces[current_workspace_index].membership.role === 'owner' || data.workspaces[current_workspace_index].membership.role === 'admin') {
                $('.page-menu-item-sources').removeClass('hide');
            } else {
                $('.page-menu-item-sources').addClass('hide');
            }

            updateAvailableDealCount(data.workspaces[current_workspace_index].membership.credit_balance);
            showUpgradeLink();
            loadWorkspaceMembers(current_workspace_id)
                .done(function() {
                    loadPipelines(current_workspace_id, page, subpage, refresh);
                    load_workflows(current_workspace_id).done(function(data) {
                        workflows = data;
                    });

                    $('.page-sidebar .workspace-name').text(workspaces[current_workspace_index].name);
                });
        }
    });
}

function loadPipelines(workspace_id, page, subpage, refresh) {
    return $.get('/api/v1/workspaces/' + workspace_id + '/pipelines?include_stages=1', function (data) {
        pipelines = data;
        if (data.length) {
            current_pipeline_index = 0;
            current_pipeline_id = data[0].id;
            checkNewLeads();
        }

        if (page) {
            Layout.activatePage(page, subpage, refresh);
        } else {
            Layout.activatePage(InitParams.page, InitParams.settingsPage, true);
        }
    });
}

function loadWorkspaceMembers(workspace_id) {
    return $.get('/api/v1/workspaces/' + workspace_id + '/workspace_members?include_user=1&include_user_integrations=1', function (data) {
        workspace_members = data;
    });
}

function load_workflows(workspace_id, include_actions, include_sources, is_enabled) {
    var params;

    if (include_sources) {
        params.include_sources = 1;
    }

    if (include_actions) {
        params.include_actions = 1;
    }

    if (is_enabled != null) {
        params.is_enabled = is_enabled ? 1 : 0;
    }

    return $.getJSON('/api/v1/workspaces/' + workspace_id + '/workflows', params);
}

function createToken(type, type_id, callback) {
    return $.ajax({
        method: 'post',
        url: '/api/v1/tokens',
        data: {
            type: type,
            type_id: type_id,
        },
    }).done(function (data) {
        callback(data);
    });
}

function startProspectSearching() {
    startProspectSearchingCounter();
    searchProspects();
}

var prospectSearchingTimerHander = null;
var prospectSearchingTimer = 60;
function startProspectSearchingCounter() {
    $('<div id="plsearching-counter">\
        <div class="plsearching-counter-inner">\
            <img src="/images/clock2x.png" />\
            <div class="counter-wrapper">\
                <h6>Searching for Prospects</h6>\
                <span class="counter">60 seconds</span>\
            </div>\
        </div>\
    </div>').appendTo('body');
    prospectSearchingTimerHander = setTimeout(countProspectSearching, 1000);
}

function countProspectSearching() {
    //console.log('prospectSearchingTimer: ' + prospectSearchingTimer);
    prospectSearchingTimer--;
    $("#plsearching-counter .counter").html(prospectSearchingTimer + ' second' + (prospectSearchingTimer > 1 ? 's' : ''));
    if (prospectSearchingTimer <= 0) {
        stopProspectSearchingCounter();
    } else {
        prospectSearchingTimerHander = setTimeout(countProspectSearching, 1000);
    }
}

function stopProspectSearchingCounter() {
    if (prospectSearchingTimerHander) {
        clearTimeout(prospectSearchingTimerHander);
    }
    prospectSearchingTimerHander = null;
}

var prospectSearchingCounter = 2;
function searchProspects(time) {
    //console.log('prospectSearchingCounter: ' + prospectSearchingCounter);
    if (prospectSearchingCounter > 0) {
        setTimeout(function () {
            Leads.loadClients().done(function (leads) {
                if (leads.length <= 0) {
                    searchProspects(5000);
                    return false;
                } else {
                    stopProspectSearching(leads.length);
                }
                hideGlobalLoader();
            });
        }, typeof time == 'undefined' ? 10000 : time);
        prospectSearchingCounter--;
    } else {
        stopProspectSearching(0);
    }
}

function stopProspectSearching(count) {
    stopProspectSearchingCounter();
    showProspectsSearchResultPopup(count, 60 - prospectSearchingTimer);
}

//Available Deal Count functions
var available_deal_count = 0;

function updateAvailableDealCount(count) {
    available_deal_count = count;
    if (typeof repeat == 'undefined') {
        repeat = 1;
    }

    var html = parseInt(available_deal_count);
    var fraction = available_deal_count - html;
    var numerator = fraction / 0.25;
    if (numerator > 0) {
        html += ' <span class="fraction"><sup>' + numerator + '</sup>/<sub>4</sub></span>';
    }
    $(".available-deal-counter a").html(html);
    if ($(".available-deal-counter").hasClass('hidden')) {
        $(".available-deal-counter").removeClass('hidden');
    }

    try {
        $(".available-deal-counter a").pulsate('destroy');
    } catch (e) {
    }
    if (available_deal_count > 1) {
        $(".available-deal-counter a").removeClass('danger').pulsate({color: "#399bc3", repeat: 2});
    } else {
        $(".available-deal-counter a").addClass('danger').pulsate({color: "#bf1c56", pause: 5000});
    }
    $(".available-deal-counter a").attr('data-original-title', count + ' Lead' + (count > 1 ? 's' : '') + ' Remaining');
}

function refreshAvailableDealCount() {
    $.getJSON('/api/v1/workspaces/' + current_workspace_id + '?include_membership=1', function (data) {
        if (data.membership !== null) {
            updateAvailableDealCount(data.membership.credit_balance);
        }
    });
}

function isLeadAcceptable() {
    return available_deal_count > 0;
}

function checkIfLeadAcceptable() {
    if (isLeadAcceptable()) {
        return true;
    } else {
//        $(".available-deal-counter a").pulsate({color: "#bf1c56", repeat: 3});
//        showErrorMessage('Accept Lead', 'No more available deals.');
        if (workspaces[current_workspace_index].membership.role === 'owner' || workspaces[current_workspace_index].membership.role === 'admin') {
            bootbox.confirm('Please purchase additional credits to accept more leads.',
                function (result) {
                    if (result) {
                        Layout.activatePage(Pages.SETTINGS, 'billing');
                    }
                }
            );
        } else {
            bootbox.confirm('Please contact your account owner or admin to add more credits.',
                function (result) {
                }
            );
        }
//        if (confirm('Upgrade your account to accept more prospects.')) {
//            activatePage(Pages.SETTINGS, 'billing');
//        }

        return false;
    }
}

function showUpgradeLink() {
    if (plan_class === 'free') {
        $(".plan-upgrade-recommender").show();
    } else {
        $(".plan-upgrade-recommender").hide();
    }
}

//The function that checks new leads
function checkNewLeads() {
    $.getJSON('/api/v1/workspaces/' + current_workspace_id + '/leads/info/new', function (data) {
        if (data.count > 0 && data.since > 0) {
            var since = moment(data.since * 1000).fromNow();
            showSuccessMessage("New Leads", data.count + " new lead(s) have(has) been added to your account since " + since + ".");

            // Update sidebar leads tab badge value
            Sidebar.addNewLeadsCount(data.count)
        }
    });
    setTimeout(checkNewLeads, 15 * 60 * 1000);
}

function getRootUrl() {
    return (location.origin ? location.origin : location.protocol + '//' + location.hostname) + '/';
}
function getBaseUrl() {
    var re = new RegExp(/^.*\//);
    return re.exec(location.href);
}

function getOptionParams() {
    var params = '';
    if (DevOptions.debug) {
        for (var name in DevOptions) {
            var value = DevOptions[name];
            if (value) {
                if (DefaultDevOptions[name] === value) {
                    continue;
                }

                if (params) {
                    params += '&';
                }
                params += name;
                if (value !== true) {
                    params += '=' + value;
                }
            }
        }
    }

    return params;
}
function changeUrlWithOptionParams(url) {
    var params = getOptionParams();
    if (params) {
        url += '?' + params;
    }
    window.history.pushState(null, null, url);
}

function decorateTweet(ttiframe, keywords, status) {
    keywords = keywords[0];
    var $tweet = $(ttiframe).contents();
    var head = $tweet.find('head');
    if (head.length) {
        head.append('<style>.keyword-highlight { color: #c63 !important; font-weight: bold; }</style>');
    }

    if (keywords && keywords.length > 0) {
        keywords = keywords.toLowerCase();
        //highlights by hashtag
        if (keywords[0] == '#') {
            $tweet.find('.Tweet-body').find(".hashtag").each(function () {
                var k = $(this).find(".PrettyLink-value").html();
                if (k && k.length > 0 && "#" + k.toLowerCase() == keywords) {
                    k = "#" + k;
                    if (keywords == k) {
                        $(this).addClass('keyword-highlight');
                    }
                }
            });
        } else {
            if (keywords[0] == '"' && keywords[keywords.length - 1] == '"') {
                keywords = [keywords.substr(1, keywords.length - 2)];
            } else {
                keywords = keywords.split(' ');
            }

            //highlights by keyword
            for (var j = 0; j < keywords.length; j++) {
                $tweet.find('.Tweet-body').find('*').contents().filter(function () {
                    return this.nodeType == 3;
                }).each(function () {
                    var n = this;
                    var keyword = keywords[j];
                    var regex = new RegExp('\\b' + keyword + '\\b');
                    for (var i; (i = n.nodeValue.toLowerCase().search(regex)) > -1; n = after) {
                        var after = n.splitText(i + keyword.length);
                        var highlighted = n.splitText(i);
                        var span = document.createElement('span');
                        span.className = 'keyword-highlight';
                        span.appendChild(highlighted);
                        after.parentNode.insertBefore(span, after);
                    }
                });
            }
        }
    }

    if (!status) {
        //$tweet.find('.Tweet-header').hide();
        //$tweet.find('.Tweet-header').css('padding-left', 0);
        $tweet.find('.Tweet-brand').hide();
        //$tweet.find('.TweetAuthor-avatar').hide();
        //$tweet.find('.TweetAuthor-screenName').removeClass('TweetAuthor-screenName');
        $tweet.find('.Tweet-actions').hide();
        //$tweet.find('.Tweet-header').show();
    }
}

function ltrim(str, chr) {
    var rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^' + chr + '+');
    return str.replace(rgxtrim, '');
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function decorateDealSourceText(text, keywords, maxlen) {
    if (typeof maxlen == 'undefined') {
        maxlen = 0;
    }

    keywords = keywords[0];
    if (keywords[0] == '"' && keywords[keywords.length - 1] == '"') {
        keywords = [keywords.substr(1, keywords.length - 2)];
    } else {
        keywords = keywords.split(' ');
    }

    //highlights by keyword
    for (var j = 0; j < keywords.length; j++) {
        var keyword = keywords[j];
        text = text.replace(new RegExp('\\b' + keyword + '\\b', 'gi'), '<span class="keyword-highlight">' + keyword + '</span>');
    }

    if (maxlen > 0) {
        text = (text.length > maxlen ? $('<div />').append(text.substr(0, maxlen) + '...').html() : text);
    }

    return text;
}

function getDateString(tc, showFullMonthName) {
    var dt = new Date(tc * 1000);
    var monthName = MonthNames[dt.getMonth()];
    //if (!showFullMonthName) {
    monthName = monthName.substr(0, 3);
    //}
    return monthName + ' ' + dt.getDate() + ', ' + dt.getFullYear();
}
function getTimeString(tc) {
    var dt = new Date(tc * 1000);
    var hours = dt.getHours();
    var minutes = dt.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    return hours + ":" + minutes + " " + ampm;
}
function getDateTimeString(tc, showFullMonthName) {
    var dt = new Date(tc * 1000);
    var hours = dt.getHours();
    var minutes = dt.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    return getDateString(tc, showFullMonthName) + " " + getTimeString(tc);
}

function validateForm(el) {
    $(el).addClass("danger-required");
    setTimeout(function () {
        $(el).removeClass("danger-required");
    }, 2500);
}

// Close NOTIFICATION MENU

function closeNotificationMenu() {
    $('#notification-menu').removeClass('slideInRight');
    $('#notification-menu').addClass('slideOutRight');
}

function showNotificationMenu() {
    $('#notification-menu').removeClass('slideOutRight');
    $('#notification-menu').addClass('slideInRight');
}

function showSuccessMessage(title, msg) {
    $.bigBox({
        title: title,
        content: msg,
        tabicon: false,
        sound: false,
        color: "#00aaf0",
        timeout: 3000
    });
}

function showWarningMessage(title, msg) {
    $.bigBox({
        title: title,
        content: msg,
        tabicon: false,
        sound: false,
        color: "#cc9933",
        timeout: 3000
    });
}

function showErrorMessage(title, msg) {
    $.bigBox({
        title: title,
        content: msg,
        tabicon: false,
        sound: false,
        color: "#993333",
        timeout: 3000
    });
}

function update_subscription_card(card, subscription_id, plan_id, plan_class) {
    card.data('id', subscription_id);
    card.data('plan-id', plan_id);
    card.data('value', $plans[plan_id].price);
    card.find('.team-size').text($subscriptions[subscription_id].users);
    // card.find('.deal-count').text($subscriptions[subscription_id].deals * Math.abs($plans[plan_id].period));

    card.find('h1.subscription-name').html($subscriptions[subscription_id].name);

    if ($plans[plan_id].period_word !== false) {
        card.find('.plan-period').removeClass('active');
        card.find('.plan-period-' + $plans[plan_id].period_word).addClass('active');
        card.find('.billing-variants').show();
        card.find('.period-container').show();
    } else {
        card.find('.billing-variants').hide();
        card.find('.period-container').hide();
    }
    card.find('.price').text($plans[plan_id].month_price_word);
    card.find('.period').text($plans[plan_id].month_period_word);
    if ($plans[plan_id].month_price_word !== $plans[plan_id].price_word && $plans[plan_id].period > 1) {
        card.find('.total-price-container').css('visibility', 'visible');
        card.find('.total-price').text($plans[plan_id].price_word);
        card.find('.total-period').text($plans[plan_id].period_word);
        card.find('.for-annual-only').css('visibility', 'visible');
    } else {
        card.find('.total-price-container').css('visibility', 'hidden');
        card.find('.for-annual-only').css('visibility', 'hidden');
    }

    card.find('.btn-choose-plan').attr('onclick', 'openPaymentPage(' + current_account_id + ', ' + subscription_id + ', ' + plan_id + ', "' + plan_class + '");');

    plan_annually = $plans[$subscriptions[subscription_id].annually_id];
    plan_monthly = $plans[$subscriptions[subscription_id].monthly_id];
    try {
        save_percentage = Math.floor((plan_monthly.period * plan_monthly.price - plan_annually.price / plan_annually.period) / (plan_monthly.period * plan_monthly.price) * 100);
        save_percentage_over = Math.floor(save_percentage / 10) * 10;
    } catch (e) {
        save_percentage = false;
    }

    if (plan_id == plan_monthly.id) {
        card.find('.save-money').hide();
    } else {
        if (save_percentage > 0) {
            if (save_percentage <= 10 || save_percentage % 10 === 0) {
                card.find('.save-percentage').text(save_percentage + '%');
            } else {
                card.find('.save-percentage').text('over ' + save_percentage_over + '%!');
            }
            card.find('.save-money').show();
        } else {
            card.find('.save-money').hide();
        }
    }

    if (plan_id == $('body').data('plan-id')) {
        $('#payment-form button').prop('disabled', true).hide();
        card.find('.btn-choose-plan').prop('disabled', true);
        if ($plans[plan_id].period == -1) {
            card.find('.btn-choose-plan').text('ACTIVE');
        } else {
            var dt = new Date($('body').data('paid-at') * 1000);
            dt.setMonth(dt.getMonth() + parseInt($plans[plan_id].period));
            var expire_at = dt.getTime() / 1000;
            card.find('.btn-choose-plan').text('Ends on: ' + getDateString(expire_at));
        }
    } else {
        $('#payment-form button').prop('disabled', false).show();
        card.find('.btn-choose-plan').prop('disabled', false);
        card.find('.btn-choose-plan').text('SUBSCRIBE');
    }

}

$(document).on('submit', '#payment-form', function (event) {
    var $form = $(this);
    $form.find('button').prop('disabled', true);
    $form.data('plan-id', $('#payment-page .card').data('plan-id'));
    $form.data('value', $('#payment-page .card').data('value'));

    Stripe.card.createToken($form, stripeResponseHandler);

    return false;
});

function stripeResponseHandler(status, response) {
    var $form = $('#payment-form');

    if (response.error) {
        showErrorMessage("Error!!!", response.error.message);
        $form.find('button').prop('disabled', false);
    } else {
        var token = response.id;
        $.ajax({
            url: '/api/v1/accounts/' + current_account_id + '/purchases',
            method: 'POST',
            data: {
                token: response.id,
                type: 1,
                product_id: $form.data('plan-id'),
                value: $form.data('value'),
                quantity: 1,
                coupon_code: $form.find('input[name=coupon_code]').val(),
            },
            success: function () {
                showSuccessMessage("Payment Information", "Payment completed successfully.");
                Settings.openBillingPage(false);



                if (typeof analytics !== 'undefined' && analytics !== null) {
                    window.segment_traits.plan = $plans[$form.data('plan-id')].class;
                    analytics.identify(window.segment_user, window.segment_traits);

                    analytics.track('Subscription Started', {
                        // username: values.keywords,
                        // billingEmail: values.location,
                        prev_plan_class: $user_usage.plan_class,
                        plan_class: $plans[$form.data('plan-id')].class
                    });
                }
            }
        });
        // Insert the token into the form so it gets submitted to the server
        // $form.append($('<input type="hidden" name="stripeToken" />').val(token));
        // // and submit
        // $form.get(0).submit();
    }
}

function style_tweet(content) {
    content = content.replace(/( |^)([@#].+?)(?=( |$))/g, function (match, $1, $2, offset, original) {
        return $1 + '<span class="style-twitter-links">' + $2 + '</span>';
    });

    content = content.replace(/( |^)(https?:\/\/.+?)(?=( |$))/g, function (match, $1, $2, offset, original) {
        return $1 + '<a href="' + $2 + '" class="style-twitter-links">' + $2 + '</span>';
    });
    return content;
}

function getEditableWYSIHTML5OptionsHtml() {
    return '\
        <div class="btn-toolbar" data-role="editor-toolbar">\
            <div class="btn-group">\
                <a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>\
                <a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>\
                <a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>\
                <a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>\
                <a class="btn" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>\
                <a class="btn" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>\
                <a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>\
                <a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>\
                <a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>\
                <a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>\
            </div>\
            <div class="btn-group">\
                <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>\
                <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>\
            </div>\
        </div>\
    ';
}

function focusOnPopup($dialog) {
    var $focus = $dialog.find('>.popup-focus');
    if (!$focus.length) {
        $focus = $('<a href="#">').prependTo($dialog);
    }
    $focus.focus();
}

function textareaLineCountResize (element, limit, lineHeight) {
    var carretPos = element.selectionStart;
    element.style.height = lineHeight + 'px';
    var lineCount = Math.round(element.scrollHeight / lineHeight);
    if (lineCount > limit) {
        var prevValue = $(element).data('prev-value');
        var prevCarretPos = $(element).data('prev-carret-pos');
        if (prevValue != null) {
            if (element.value === prevValue) {
                element.value = '';
            } else {
                element.value = prevValue;
                element.value = prevValue;
            }
        }
        element.style.height = limit * lineHeight + 'px';
        element.selectionStart = prevCarretPos;
        element.selectionEnd = prevCarretPos;
    } else {
        element.style.height = lineCount * lineHeight + 'px';
        $(element).data('prev-value', element.value);
        $(element).data('prev-carret-pos', carretPos);
    }
}
