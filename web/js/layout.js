var Pages = {
    CLIENTS: 'clients',
    SEARCH: 'search',
    SOURCES: 'sources',
    SETTINGS: 'settings'
};

var SettingsPages = {
    USER: 'user',
    TEAM_MEMBERS: 'team-members',
    BILLING: 'billing',
    INTEGRATIONS: 'integrations'
};

var Layout = (function () {
    var scope = {};

    var page = Pages.CLIENTS;

    var $layout = null;

    scope.setPage = function (__page) {
        page = __page;
    };

    scope.load = function () {
        loadWorkspaces();

        $('.page-sidebar .page-menu').on('click', '.page-menu-item', function () {
            var __page = $(this).data('page');
            if (__page == page) {
                if (__page == Pages.SETTINGS) {
                    toggleSettingsPageSidebar();
                }
                return;
            }

            activatePage(__page);
        });

        $('.page-header .top-menu').on('click', '.plan-upgrade-recommender a', function () {
            activatePage(Pages.SETTINGS, 'billing');
            return false;
        }).on('click', 'ul.nav.navbar-nav.navbar-right ul.dropdown-menu>li>a', function (e) {
            var action = this.href.substr(this.href.indexOf('#') + 1);
            switch (action) {
                case 'settings':
                    activatePage(Pages.SETTINGS);
                    break;
                case 'logout':
                    var root_domain = document.domain.replace(/^app\./, '') + ':' + location.port;
                    $.ajax({
                        type: 'post',
                        url: '//' + root_domain + '/x-sign-out',
                        crossDomain: true,
                        data: "type=" + 1,
                        xhrFields: {
                            withCredentials: true
                        },
                        success: function (data) {
                            return setTimeout(function () {
                                return location.reload();
                            }, 100);
                        }
                    });
                    break;
            }
            return e.preventDefault();
        });

        $("[data-toggle='tooltip']").tooltip();

        hideGlobalLoader();
    };

    function loadWorkspaces() {
        var $logo = $('.page-sidebar .page-logo');
        var $workspace_name = $('.page-sidebar .workspace-name');
        var $workspaces = $('<ul class="workspace-list"/>').on('click', 'li', function () {
            var workspace_id = $(this).data('id');
            $(this).siblings('li').removeClass('active');
            $(this).addClass('active');
            current_account_workspace_id = workspace_id;
            current_owner_id = UserData.id;
            loadWorkingPlace(workspace_id, Pages.CLIENTS, false, true);
        });

        $('.page-sidebar .page-logo, .page-sidebar .workspace-name').on('click', function () {
             if (workspaces.length > 0) {
                $('body').toggleClass('page-sidebar-expanded');
                $workspaces.html('');
                $.each(workspaces, function (i, workspace) {
                    $('\
                        <li class="workspace' + (i === current_workspace_index ? ' active' : '') + '" data-id="' + workspace.id + '">\
                            <!-- <img src="/images/logo.png" /> -->\
                            <label>' + workspace.name + '</label>\
                        </li>\
                    ').appendTo($workspaces);
                });
                $workspaces.insertAfter($workspace_name);
            }
        });
    }

    function activatePage(__page, subpage, refresh) {
        //console.log('Activating Page: ' + __page);
        if (!__page) {
            return;
        }

        $('body').attr('data-page', __page);

        var $menu = $('.page-sidebar .page-menu');
        var $active = $menu.find('.page-menu-item.active');

        if ($active.hasClass('page-menu-item-' + __page)) {
            if (__page == Pages.SETTINGS && subpage) {
                Settings.activateSettingsPage(subpage);
            }
            if ( ! refresh) return;
        }

        $active.removeClass('active');
        $menu.find('.page-menu-item.page-menu-item-' + __page).addClass('active');

        showGlobalLoader();
        if ( ! refresh && page == Pages.SETTINGS && isSettingsPageSidebarOpen()) {
            closeSettingsPageSidebar();
            setTimeout(function () {
                __activatePage(__page, subpage);
            }, 801);
        } else {
            __activatePage(__page, subpage);
        }
    }
    function __activatePage(__page, subpage) {
        if (__page != page) {
            page = __page;
            InitParams.page = page;
            changeUrlWithOptionParams(getRootUrl() + __page);
        }

        switch (__page) {
            case Pages.CLIENTS:
                $(".add-lead-source-li").stop().hide();
                $('body').addClass('page-header-fixed');
                closeSettingsPageSidebar();
                openClients();
                break;
            case Pages.SEARCH:
                $(".add-lead-source-li").stop().hide();
                $('body').addClass('page-header-fixed');
                closeSettingsPageSidebar();
                openSearch();
                break;
            case Pages.SOURCES:
                $(".add-lead-source-li").stop().fadeIn();
                $('body').addClass('page-header-fixed');
                closeSettingsPageSidebar();
                openSources();
                break;
            case Pages.SETTINGS:
                $(".add-lead-source-li").stop().hide();
                $('body').removeClass('page-header-fixed');
                openSettings(subpage);
        }
    }

    scope.activatePage = activatePage;

    function openClients(pipeline_id) {
        if (pipeline_id != null) current_pipeline_id = pipeline_id;
        $.get("/js/ajax/main.html?" + (new Date().getTime()), function (data) {
            $(".page-content").html(data);
            current_owner_id = current_owner_id == null ? UserData.id : current_owner_id;

            var member_index = 0;
            $.each(workspace_members, function (i, member) {
                if (member.user_id === current_owner_id) {
                    member_index = i;
                    return;
                }
            });

            if (workspaces[current_workspace_index].membership.role !== 'owner' && workspaces[current_workspace_index].membership.role !== 'admin') {
                $(".page-content .btn-stage-add").addClass('hide');
                $(".pipeline-stage-border").addClass('hide');
                $('#page-clients .main-block .main-header .owner-dropdown-container .owner-dropdown').addClass('hide');
            } else {
                var owner_menu = $('#page-clients .main-block .main-header .owner-dropdown-container .dropdown-menu');
                owner_menu.html('');
                owner_menu.append('<li class="all-owners' + (current_owner_id === false ? ' active' : '') + '"><a>All Clients</a></li>');
                $.each(workspace_members, function (id, workspace_member) {
                    if (workspace_member.user !== null) {
                        owner_menu.append('<li data-id="' + workspace_member.user.id + '"' + (workspace_member.user.id == current_owner_id ? ' class="active"' : '') + '><a>' + workspace_member.user.first_name + ' ' + workspace_member.user.last_name + '\'s Clients</a></li>');
                    }
                });
            }

            $('#page-clients .main-block .main-header h1.page-title').html(workspace_members[member_index].user.first_name + ' ' + workspace_members[member_index].user.last_name + '\'s Clients');
            var pipeline_header = $('#page-clients .main-block .main-header .pipeline-dropdown-container h4');
            pipeline_header.html(pipelines[current_pipeline_index].name);
            if (typeof pipeline_header.editable === 'function') {
                pipeline_header.editable('destroy');
            }
            if (workspaces[current_workspace_index].membership.role === 'owner' || workspaces[current_workspace_index].membership.role === 'admin') {
                pipeline_header
                    .editable({
                        mode: 'inline',
                        type: 'text',
                        clear: false,
                        onblur: 'submit',
                        title: 'Pipeline Name',
                        placement: 'bottom',
                        name: 'name',
                        pk: 1,
                        url: '/api/v1/pipelines/' + current_pipeline_id,
                        success: function(response, newValue) {
                            pipelines[current_pipeline_index].name = newValue;
                            var pipeline_menu = $('#page-clients .main-block .main-header .pipeline-dropdown-container .dropdown-menu');
                            pipeline_menu.html('');
                            $.each(pipelines, function (id, pipeline) {
                                pipeline_menu.append('<li data-id="' + pipeline.id + '" data-index="' + id + '"' + (pipeline.id == current_pipeline_id ? ' class="active"' : '') + '><a>' + pipeline.name + '</a></li>');
                            });
                            if (workspaces[current_workspace_index].membership.role === 'owner' || workspaces[current_workspace_index].membership.role === 'admin') {
                                pipeline_menu.append('<li class="add-new"><a>Add new</a></li>');
                            }
                        },
                        validate: function(value) {
                            if (value === '') {
                                pipeline_header.editable('hide');
                                return;
                            }
                        }
                    })
                    .on('shown', function(e, editable) {
                        editable.input.$input.attr('placeholder', pipeline_header.text());
                    });
            }

            var pipeline_menu = $('#page-clients .main-block .main-header .pipeline-dropdown-container .dropdown-menu');
            pipeline_menu.html('');
            $.each(pipelines, function (id, pipeline) {
                pipeline_menu.append('<li data-id="' + pipeline.id + '" data-index="' + id + '"' + (pipeline.id == current_pipeline_id ? ' class="active"' : '') + '><a>' + pipeline.name + '</a></li>');
            });

            if (workspaces[current_workspace_index].membership.role === 'owner' || workspaces[current_workspace_index].membership.role === 'admin') {
                pipeline_menu.append('<li class="add-new"><a>Add new</a></li>');
            }

            $('#page-clients [data-toggle="tooltip"]').tooltip();

            Leads.loadStages().done(function () {
                if (user_wizard == UserWizards.NONE) {
                    Leads.loadClients().done(function () {
                        hideGlobalLoader();
                        if (tour_step >= TourSteps.START && tour_step < TourSteps.LEFT_MENU) {
                            openTour();
                        }
                    });
                } else {
                    hideGlobalLoader();
                    if (user_wizard == UserWizards.USER_INFO) {
                        UserInfo.show();
                    } else if (user_wizard == UserWizards.USER_CONNECTOR) {
                        UserConnector.show();
                    }
                }
            });
            Sidebar.load();
        });
    }

    function openSearch() {
        $(".page-content").html($('#tpl_page_search').html());
        Search.initPeopleSearch($('#page_search'));
        Sidebar.load();
        hideGlobalLoader();
    }

    function openSources(el) {
        $.get("/js/ajax/leads.html?" + (new Date()).getTime(), function (data) {
            $(".page-content").html(data);
            Sources.load();
        });
    }
    function openSettings(subpage) {
        Settings.load();
        setTimeout(function () {
            openSettingsPageSidebar();
        }, 100);
        Settings.activateSettingsPage(subpage ? subpage : (InitParams.settingsPage ? InitParams.settingsPage : SettingsPages.USER));
//        $.get("/js/ajax/settings.html?" + (new Date().getTime()), function (data) {
//            $(".page-content").html(data);
//            if (subpage == 'billing') {
//                openBillingPage();
//            } else {
//                openUserSettings();
//            }
//            hideGlobalLoader();
//            setTimeout(function () {
//                openSettingsPageSidebar();
//            }, 100);
//        });
    }
    function isSettingsPageSidebarOpen() {
        return $('body').hasClass('settings-page-sidebar-open');
    }
    function openSettingsPageSidebar() {
        $('body').addClass('settings-page-sidebar-open');
        //$('#settings-page-sidebar').removeClass('slideOutLeft').addClass('slideInLeft');
    }
    function closeSettingsPageSidebar() {
        //$('#settings-page-sidebar').removeClass('slideInLeft').addClass('slideOutLeft');
        $('body').removeClass('settings-page-sidebar-open');
    }
    function toggleSettingsPageSidebar() {
        if (!isSettingsPageSidebarOpen()) {
            openSettingsPageSidebar();
        } else {
            closeSettingsPageSidebar();
        }
    }


    function $createColumn() {
        return $('<div class="column stage-column"><div class="column-inner"></div></div>');
    }
    scope.$createColumn = $createColumn;

    function resizeColumns($container, span) {
        var $columns = $('.main-block .main-content .columns-container .columns', $container);
        var $columnList = $columns.find('>.column');
        var columnsWidth = $columnList.length * span;
        $columns.css('width', columnsWidth + '%');
        var columnWidth = 100 / $columnList.length;
        $columnList.css('width', columnWidth + '%');
    }
    scope.resizeColumns = resizeColumns;

    return scope;
})();
