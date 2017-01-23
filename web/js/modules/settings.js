var Settings = (function () {
    var scope = {};

    var $page = null;

    var settingsPage = SettingsPages.USER;

    var account_workspace_count = 0;

    scope.load = function () {
        var html = '\
            <div id="page-settings">\
                <div class="settings-page-sidebar">\
                    <div class="head-section">\
                        <h1>Settings</h1>\
                        <!--h2>Short description</h2-->\
                    </div>\
                    <ul class="settings-page-menu">\
                        <li class="settings-page-menu-item settings-page-menu-item-' + SettingsPages.USER + '"><a href="#user">Account Settings</a></li>';
        if (current_account_id === workspaces[current_workspace_index].account_id || workspaces[current_workspace_index].membership.role === 'owner' || workspaces[current_workspace_index].membership.role === 'admin') {
            html += '<li class="settings-page-menu-item settings-page-menu-item-' + SettingsPages.TEAM_MEMBERS + '"><a href="#team-members">Team Members</a></li>';
        }
        if (current_account_id === workspaces[current_workspace_index].account_id) {
            html += '<li class="settings-page-menu-item settings-page-menu-item-' + SettingsPages.BILLING + '"><a href="#billing">Billing</a></li>';
        }
        html += '\
                        <li class="settings-page-menu-item settings-page-menu-item-' + SettingsPages.INTEGRATIONS + '"><a href="#integrations">Integrations</a></li>\
                    </ul>\
                </div>\
                <div id="settings-pages"></div>\
            </div>';
        $page = $(html).appendTo($('.page-content').empty());

        $page.find('ul.settings-page-menu').on('click', 'li>a', function (e) {
            var __page = this.href.substr(this.href.indexOf('#') + 1);
            activateSettingsPage(__page);
            return e.preventDefault();
        });

        $(document).on('click', function (e) {
            $('[data-toggle="popover"],[data-original-title]').each(function () {
                //the 'is' for buttons that trigger popups
                //the 'has' for icons within a button that triggers a popup
                if ( ! $(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                    (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false; // fix for BS 3.3.6
                }
            });
        });

        $(document).on('change', '#settings-pages .team-members-list .select-all', function (e) {
            $(this).prop('indeterminate', false);
            $('#settings-pages .team-members-list .table-body .check-box-col input').prop('checked', this.checked);
            if (this.checked) {
                $('#settings-pages .remove-team-member').removeClass('disabled');
            } else {
                $('#settings-pages .remove-team-member').addClass('disabled');
            }
        });

        $(document).on('change', '#settings-pages .team-members-list .table-body .check-box-col input', function (e) {
            var global_checkbox = $('#settings-pages .team-members-list .select-all');
            var all_choices = $('#settings-pages .team-members-list .table-body .check-box-col input');
            var checked_choices = all_choices.filter(':checked');
            if (checked_choices.length) {
                if (checked_choices.length === all_choices.length) {
                    global_checkbox.prop('indeterminate', false);
                    global_checkbox.prop('checked', true);
                } else {
                    global_checkbox.prop('indeterminate', true);
                    global_checkbox.prop('checked', false);
                }
                $('#settings-pages .remove-team-member').removeClass('disabled');
            } else {
                global_checkbox.prop('indeterminate', false);
                global_checkbox.prop('checked', false);
                $('#settings-pages .remove-team-member').addClass('disabled');
            }
        });

        $(document).on('click', '#settings-pages .team-members-list .role-switch-container ul.dropdown-menu > li > a', function (e) {
            var $li = $(this).parent();
            if ($li.hasClass('active')) return false;
            var workspace_member_id = $(this).parents('.user').data('id');
            $li.siblings('.active').removeClass('active');
            $li.addClass('active');
            $.ajax({
                url: '/api/v1/workspace_members/' + workspace_member_id,
                method: 'PUT',
                data: {
                    role: $(this).data('role')
                }
            }).done(function () {
                showSuccessMessage('Workspace members', 'Role was successfully changed.');
                openTeamMembers();
            });
        });

        $(document).on('click', '#settings-pages .team-members-list .is-enabled-switch-container ul.dropdown-menu > li > a', function (e) {
            var $li = $(this).parent();
            if ($li.hasClass('active')) return false;
            var workspace_member_id = $(this).parents('.user').data('id');
            $li.siblings('.active').removeClass('active');
            $li.addClass('active');
            $.ajax({
                url: '/api/v1/workspace_members/' + workspace_member_id,
                method: 'PUT',
                data: {
                    is_enabled: $(this).data('is-enabled')
                }
            }).done(function () {
                showSuccessMessage('Workspace members', 'Status was successfully changed.');
                openTeamMembers();
            }).fail(function (jqXHR) {
                if (jqXHR.status === 409) {
                    showErrorMessage('Error', 'Please upgrade your account to add additional workspace members.');
                }
            });;
        });

        $(document).on('click', '#settings-pages .account-workspaces-dropdown .dropdown-toggle', function (e) {
            var dropdown = $(this).siblings('ul');
            var chosen_workspace_id;
            if (current_account_workspace_id == null) chosen_workspace_id = current_workspace_id;
            else chosen_workspace_id = current_account_workspace_id;
            $.get('/api/v1/accounts/' + current_account_id + '/workspaces', function (data) {
                dropdown.html('');
                $.each(data, function (id, workspace) {
                    dropdown.append('<li data-id="' + workspace.id + '"' + (chosen_workspace_id === workspace.id ? ' class="active"' : '') + '><a>' + workspace.name + '</a></li>');
                });
            });
        });

        $(document).on('click', '#settings-pages .account-workspaces-dropdown .dropdown-menu li', function (e) {
            var workspace_id = $(this).data('id');
            var $li = $(this);
            $li.siblings('.active').removeClass('active');
            $li.addClass('active');
            current_account_workspace_id = workspace_id;
            openTeamMembers(workspace_id);
        });

        $(document).on('click', '#settings-pages .account-workspace-create-btn', function (e) {
            var $btn = $(this);
            var $toggle = $btn.parent().find('>.workspace-add-popup-toggle');
            if ( ! $toggle.length) {
                $toggle = $('<div class="workspace-add-popup-toggle" />').insertAfter($btn).editable({
                    mode: 'popup',
                    type: 'text',
                    clear: false,
                    title: 'Workspace Name',
                    defaultValue: 'Workspace ' + (account_workspace_count + 1),
                    placement: 'bottom',
                    name: 'name',
                    pk: 1,
                    ajaxOptions: {
                        type: 'POST'
                    },
                    url: '/api/v1/accounts/' + $btn.data('id') + '/workspaces',
                    success: function(response, newValue) {
                        openTeamMembers(response.id);
                        account_workspace_count++;
                        $toggle.editable('option', 'defaultValue', 'Workspace ' + account_workspace_count);
                        $('.page-sidebar .workspace-list').append('\
                            <li class="workspace" data-id="' + response.id + '">\
                                <label>' + response.name + '</label>\
                            </li>\
                        ');
                        workspaces.push(response);
                    },
                    error: function(response, newValue) {
                        if (response.status === 409) {
                            showErrorMessage('Error', 'Please upgrade your account to add additional workspaces.');
                        }
                    }
                });
            }
            $toggle.editable('setValue', '').click();

            return false;
        });

        $(document).on('change', '#settings-pages .workspace-header .switch input', function () {
            var input = $(this);
            var $workspace_header = $(this).closest('.workspace-header');
            var $workspace_team = $('#settings-pages .main-content');
            var data = {
                is_enabled: this.checked ? 1 : 0
            }
            $.ajax({
                method: 'PUT',
                url: '/api/v1/workspaces/' + $workspace_header.data('id'),
                data: data
            }).done(function () {
            }).fail(function (jqXHR) {
                if (jqXHR.status === 409) {
                    setTimeout(function () {
                        input.attr('checked', false);
                    }, 1000);
                    showErrorMessage('Error', 'Please upgrade your account to manage more workspaces.');
                }
            });
            $(this).parent().attr('title', this.checked ? 'Click to Disable Workspace.' : 'Click to Enable Workspace.')
                    .tooltip('fixTitle')
                    .tooltip('setContent')
                    .tooltip('show');
            if (this.checked) {
                $workspace_team.removeClass('disabled');
            } else {
                $workspace_team.addClass('disabled');
            }
        });
    };

    function activateSettingsPage(__page, refresh) {
        if (!__page) {
            return;
        }

        var $menu = $page.find('.settings-page-menu');
        var $active = $menu.find('li.settings-page-menu-item.active');

        if ( ! refresh && $active.hasClass('settings-page-menu-item-' + __page)) {
            return;
        }

        $active.removeClass('active');
        $menu.find('li.settings-page-menu-item.settings-page-menu-item-' + __page).addClass('active');

        showGlobalLoader();

        switch (__page) {
            case SettingsPages.USER:
                openUserSettings();
                break;
            case SettingsPages.TEAM_MEMBERS:
                openTeamMembers();
                break;
            case SettingsPages.BILLING:
                openBillingPage();
                break;
            case SettingsPages.INTEGRATIONS:
                openIntegrationPage();
                break;
        }

        if (settingsPage != __page) {
            settingsPage = __page;
            InitParams.settingsPage = settingsPage;
            changeUrlWithOptionParams(getRootUrl() + InitParams.page + '/' + settingsPage);
        }
    }
    scope.activateSettingsPage = activateSettingsPage;

    function openUserSettings() {
        $.get("/js/ajax/settings/user-settings.html?" + (new Date().getTime()), function (data) {
            $("#settings-pages").html(data);
            hideGlobalLoader();
        });
    }

    function openTeamMembers(workspace_id) {
        account_workspace_count = workspaces.length;
        if (workspace_id == null) workspace_id = current_workspace_id;
        $.get('/api/v1/workspaces/' + workspace_id + '?include_membership=1&include_workspace_members=1&include_workspace_members_user=1&include_workspace_members_user_integrations=1', function (data) {
            if (current_account_index !== null) {
                $.get('/api/v1/accounts/' + current_account_id + '/workspaces/', function (data) {

                });
            }

            if (data.membership !== null) {
                updateAvailableDealCount(data.membership.credit_balance);
            }

            var html = '\
                <div class="col-md-12" id="main-block">\
                    <div class="main-block">\
                        <div class="row main-header">\
                            <div class="account-credit-count-wrapper text-center' + (current_account_index === null ? ' hide' : '') + '">\
                                <span class="account-credit-count">' + (current_account_index === null ? '' : accounts[current_account_index].credit_balance) + '</span><br />\
                                Account Credits\
                            </div>\
                            <div class="team-credit-count-wrapper text-center">\
                                <span data-id="' + data.id + '" class="team-credit-count' + (current_account_index !== null ? ' allow-edit' : '') + '">' + data.credit_balance + '</span><br />\
                                Team Credits\
                            </div>\
                            <div class="workspace-settings-header">\
                                <h1>Workspace Settings</h1>\
                                <div class="account-workspace-create-btn-container">\
                                    <button data-id="' + data.account_id + '" class="btn btn-success account-workspace-create-btn' + (current_account_index === null ? ' hide' : '') + '">Add new workspace</button>\
                                </div>\
                            </div>\
                            <div class="workspace-header" data-id="' + data.id + '">\
                                <h2 data-id="' + data.id + '">' + data.name + '</h2>\
                                <div class="dropdown account-workspaces-dropdown' + (current_account_index === null ? ' hide' : '') + '" data-sort="">\
                                    <a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></a>\
                                    <ul class="dropdown-menu pull-left">\
                                    </ul>\
                                </div>\
                                <label class="switch" data-toggle="tooltip" data-container="body" data-placement="top" title="' + (data.is_enabled ? 'Click to Disable Workspace.' : 'Click to Enable Workspace.') + '">\
                                    <input type="checkbox"' + (data.is_enabled ? ' checked="checked"' : '') + '>\
                                    <span data-on="On" data-off="Off"></span>\
                                    <i></i>\
                                </label>\
                            </div>\
                            <div class="sub-header">\
                                Team Members\
                            </div>\
                        </div>\
                        <div class="h20"></div>\
                        <div class="main-content' + (data.is_enabled ? '' : ' disabled') + '">\
                            <div class="team-members-head row">\
                                <div class="col-md-8 add-remove-members">\
                                    <ul>\
                                        <li>\
                                            <a href="#" onclick="addTeamMemberWindow();">\
                                                <i class="fa fa-plus"></i>\
                                                Add team member</a>\
                                        </li>\
                                        <li>\
                                            <!--a class="remove" href="#" onclick="removeTeamMembers();">Remove</a-->\
                                            <a class="remove-team-member disabled" data-toggle="tooltip" data-container="body" data-placement="bottom" title="What do you want to do with the leads in this account?">Remove</a>\
                                        </li>\
                                    </ul>\
                                    <!-- Add Team Member Modal Window -->\
                                    <div id="add-team-member-modal">\
                                        <div class="row">\
                                            <div class="col-md-12">\
                                                <div class="form-group">\
                                                    <input class="form-control" id="teamMemberName" placeholder="Team Member Name" type="text">\
                                                </div>\
                                                <div class="form-group">\
                                                    <input class="form-control" id="teamMemberEmail" placeholder="Team Member Email" type="text">\
                                                </div>\
                                                <div class="form-group">\
                                                    <select class="form-control" id="teamMemberRole" name="role">\
                                                        <option value="admin">Administrator</option>\
                                                        <option value="user" selected="selected">Team member</option>\
                                                    </select>\
                                                </div>\
                                            </div>\
                                            <!-- <div class="col-md-12">\
                                                <div class="form-group">\
                                                    <input type="text" class="form-control" id="clientValue" placeholder="Client Value">\
                                                </div>\
                                            </div> -->\
                                            <div class="col-md-12">\
                                                <button class="btn btn-primary" name="button" onclick="addTeamMember();" type="button">Add Team Member</button>\
                                            </div>\
                                        </div>\
                                    </div>\
                                    <!-- / Add Team Member Modal Window -->\
                                </div>\
                                <div class="col-md-4 workspace-member-searchbar">\
                                    <div class="form-group">\
                                        <a class="search-btn" href="#">\
                                            <i class="fa fa-search"></i>\
                                        </a>\
                                        <input class="form-control" placeholder="Search" type="text" onkeydown="searchTeamMembers(this)">\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="team-members-list row">\
                                <div class="table-header row">\
                                    <div class="col-md-1 check-box-col text-center">\
                                        <input class="select-all" type="checkbox">\
                                    </div>\
                                    <div class="col-md-2 col-name">Name</div>\
                                    <div class="col-md-2">User role</div>\
                                    <div class="col-md-2">Connected</div>\
                                    <div class="col-md-2 text-center">Last Login</div>\
                                    <div class="col-md-2 text-center">Credits Remaining</div>\
                                    <div class="col-md-1 text-center">Status</div>\
                                </div>\
                                <div class="table-body col-md-12">';
            $.each(data.workspace_members, function (id, workspace_member) {
                var role_title;
                var member_name;

                if (workspace_member.role === 'owner')      role_title = 'Owner';
                else if (workspace_member.role === 'admin') role_title = 'Administrator';
                else if (workspace_member.role === 'user')  role_title = 'Team member';

                if (workspace_member.user === null) {
                    if (workspace_member.extra.name != null) {
                        member_name = workspace_member.extra.name;
                    }
                    if (workspace_member.extra.email != null) {
                        member_email = workspace_member.extra.email;
                    }
                    member_avatar = '/images/profile-blank.png';
                } else {
                    member_name = workspace_member.user.first_name + ' ' + workspace_member.user.last_name;
                    member_email = workspace_member.user.email;
                    member_avatar = '/images/profile-blank.png';
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
                }
                html += '\
                                    <div class="user row" data-id="' + workspace_member.id + '">\
                                        <div class="col-md-1 check-box-col text-center">';
                if (workspace_member.role !== 'owner') {
                    html += '<input type="checkbox">';
                }
                html += '\
                                        </div>\
                                        <div class="col-md-2 col-name">\
                                            <img alt="' + member_name + '" class="img-responsive member-img" src="' + member_avatar + '" />\
                                            <h1>' + member_name + '</h1><br>\
                                            <span class="email">' + member_email + '</span>\
                                        </div>\
                                        <div class="col-md-2 role-switch-container">\
                                            <span class="dropdown-toggle team-member-role"' + (workspace_member.role !== 'owner' ? ' data-toggle="dropdown"' : '') + '>' + role_title + '</span>';
                if (workspace_member.role !== 'owner') {
                    html += '\
                                            <ul class="dropdown-menu pull-right">\
                                                <li class="' + (workspace_member.role === 'admin' ? 'active' : '') + '"><a data-role="admin">Administrator</a></li>\
                                                <li class="' + (workspace_member.role === 'user' ? 'active' : '') + '"><a data-role="user">Team member</a></li>\
                                            </ul>';
                }
                html += '\
                                        </div>\
                                        <div class="col-md-2">\
                                            <ul class="social-links">';
                if (workspace_member.user != null) {
                    $.each(workspace_member.user.integrations, function (id, integration) {
                        if (integration.type === 'twitter') {
                            html += '\
                                                    <li>\
                                                        <a class="cliently-twitter">\
                                                            <i class="fa fa-cliently-twitter"></i> @' + integration.handle + '\
                                                        </a>\
                                                    </li>';
                        } else if (integration.type === 'google') {
                            html += '\
                                                    <li>\
                                                        <a class="google-cliently-plus">\
                                                            <img src="/images/gmail-icon.png" /> ' + integration.handle + '\
                                                        </a>\
                                                    </li>';
                        }
                    });
                }
                html += '\
                                            </ul>\
                                        </div>\
                                        <div class="col-md-2 text-center">' + getDateString(workspace_member.created_at) + '</div>\
                                        <div class="col-md-2 text-center member-credit-balance-container"><span class="member-credit-balance" data-id="' + workspace_member.id + '">' + workspace_member.credit_balance + '</span></div>\
                                        <div class="col-md-1 text-center is-enabled-switch-container">\
                                            <span class="dropdown-toggle team-member-is-enabled"' + (workspace_member.role !== 'owner' ? ' data-toggle="dropdown"' : '') + '><span>' + (workspace_member.is_enabled ? 'Active' : 'Inactive') + '</span>' + (workspace_member.role !== 'owner' ? '<i class="fa fa-angle-down"></i>' : '') + '</span>';
                if (workspace_member.role !== 'owner') {
                    html += '\
                                            <ul class="dropdown-menu pull-left">\
                                                <li class="' + (workspace_member.is_enabled ? 'active' : '') + '"><a data-is-enabled="1">Active</a></li>\
                                                <li class="' + ( ! workspace_member.is_enabled ? 'active' : '') + '"><a data-is-enabled="0">Inactive</a></li>\
                                            </ul>';
                }
                html += '\
                                        </div>\
                                    </div>\
                                    <hr>';
            });
            html += '\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                </div>';
                

            $("#settings-pages").html(html);

            var workspace_header = $('#settings-pages .workspace-header h2');
            workspace_header.editable({
                mode: 'inline',
                type: 'text',
                clear: false,
                onblur: 'submit',
                title: 'Workspace Name',
                placement: 'bottom',
                name: 'name',
                pk: 1,
                url: '/api/v1/workspaces/' + workspace_header.data('id'),
                success: function(response, newValue) {
                    if (current_workspace_id == workspace_header.data('id')) {
                        $('.page-sidebar .workspace-name').text(newValue);
                    }
                    $('.page-sidebar .workspace-list .workspace[data-id="' + workspace_header.data('id') + '"] label').text(newValue);
                    var workspaces_len = workspaces.length;
                    for (var i = 0; i < workspaces_len; i++) {
                        if (workspaces[i].id == workspace_header.data('id')) {
                            workspaces[i].name = newValue;
                            break;
                        }
                    }
                }
            });

            if (current_account_index !== null) {
                var team_credit_count = $('#settings-pages .team-credit-count');
                team_credit_count.editable({
                    mode: 'inline',
                    type: 'text',
                    clear: false,
                    onblur: 'submit',
                    title: 'Workspace Balance',
                    placement: 'bottom',
                    name: 'credit_balance',
                    pk: 1,
                    url: '/api/v1/workspaces/' + team_credit_count.data('id'),
                    success: function(response, newValue) {
                        loadWorkingPlace(current_workspace_id, Pages.SETTINGS, SettingsPages.TEAM_MEMBERS, true);
                    }
                });
            }

            $('#settings-pages .member-credit-balance').each(function() {
                var id = $(this).data('id');
                $(this).editable({
                    mode: 'inline',
                    type: 'text',
                    clear: false,
                    onblur: 'submit',
                    title: 'Balance',
                    placement: 'bottom',
                    name: 'credit_balance',
                    pk: 1,
                    url: '/api/v1/workspace_members/' + id,
                    success: function(response, newValue) {
                        openTeamMembers();
                    },
                    validate: function(value) {
                        if (value === '') {
                            $(this).editable('hide');
                            return;
                        }
                    }
                });
                $(this).on('shown', function(e, editable) {
                    editable.input.$input.attr('placeholder', $(this).text());
                });
            });

            hideGlobalLoader();

            $('#seetings-pages [data-toggle="tooltip"]').tooltip();
            $(document).off('click', '.remove-team-member').on('click', '.remove-team-member', function() {
                if ($(this).hasClass('disabled')) {
                    $(this).popover('destroy');
                } else {
                    if ( ! $(this).data('bs.popover') || ! $(this).attr('data-popoverAttached')) {
                        $(this).popover({
                            html: true,
                            content: function () {
                                var selected_members = [];
                                $('#settings-pages .team-members-list > .table-body .check-box-col input:checked').each(function(index) {
                                    selected_members.push($(this).parents('.user').data('id'));
                                });
                                var selected_members_val = selected_members.join(',');
                                var str = '\
                                    <form class="form team-member-delete-form" data-ids="' + selected_members_val + '">\
                                        <div class="form-body">\
                                            <ul class="team-member-delete-option-list">\
                                                <li><input type="radio" name="delete_option" value="delete_all" id="team-member-delete-option-delete-all" checked="checked"> <label for="team-member-delete-option-delete-all">Delete all leads.</label></li>\
                                                <li><input type="radio" name="delete_option" value="move" id="team-member-delete-option-move"> <label for="team-member-delete-option-move">Move the leads to \
                                                    <select name="reassign_to">';
                                $.each(data.workspace_members, function (id, workspace_member) {
                                    if (workspace_member.user == null) return;
                                    else if (selected_members.indexOf(workspace_member.id) !== -1) return;

                                    str += '\
                                                        <option value="' + workspace_member.id + '">'
                                                            + workspace_member.user.first_name + ' ' + workspace_member.user.last_name + ' (' + workspace_member.user.email + ')\
                                                        </option>';
                                });

                                str += '\
                                                </select>\
                                            </label></li>\
                                        </ul>\
                                    </div>\
                                    <div class="form-actions">\
                                        <div class="row">\
                                            <div class="col-md-12 text-right">\
                                                <button class="btn btn-danger btn-remove btn-xs">Remove</button>&nbsp;\
                                                <button type="button" class="btn btn-default btn-cancel btn-xs" data-toggle="dismiss">Cancel</button>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </form>';
                                return str;
                            }
                        }).off('shown.bs.popover').on('shown.bs.popover', function () {
                            var toggle = $(this);
                            var popover = $('#' + $(this).attr('aria-describedby'));

                            popover.off('change click', 'select[name="reassign_to"]').on('change click', 'select[name="reassign_to"]', function (e) {
                                $('#team-member-delete-option-move').attr('checked', true);
                            });

                            popover.off('submit', 'form.team-member-delete-form').on('submit', 'form.team-member-delete-form', function () {
                                // removeTeamMembers();
                                var data = {
                                    ids: $(this).data('ids')
                                };
                                if (data.ids) {
                                    if ($('#team-member-delete-option-delete-all').is(':checked')) {
                                        data.deactivate_deals = 1;
                                    }
                                    if ($('#team-member-delete-option-move').is(':checked')) {
                                        data.reassign_to = $(this).find('select[name="reassign_to"]').val();
                                    }

                                    $.ajax({
                                        url: '/api/v1/workspace_members/_delete',
                                        method: 'POST',
                                        data: data
                                    }).done(function (data) {
                                        showSuccessMessage('Workspace members', 'You have successully deleted selected accounts');
                                        openTeamMembers();
                                    });
                                }

                                ((toggle.popover('hide').data('bs.popover')||{}).inState||{}).click = false;
                                return false;
                            });

                            popover.off('click', 'button.btn-cancel').on('click', 'button.btn-cancel', function () {
                                ((toggle.popover('hide').data('bs.popover')||{}).inState||{}).click = false;
                            });
                        }).data('bs.popover').tip().addClass('popover-remove-team-members');
                    }
                    $(this).popover('show');
                }
            });
        });
    }
    scope.openBillingPage = function(el) {
        openBillingPage(el);
    }
    function openBillingPage(el) {
        $.get("/js/ajax/settings/billing.html?" + (new Date().getTime()), function (data) {
            $("#settings-pages").html(data);
            openBilling();
            hideGlobalLoader();
        });
    }

    function openIntegrationPage(el) {
        $("#settings-pages").html($('#tpl_page_integrations').html());

        $.fn.bootstrapSwitch.defaults.size = 'small';
        deactivateAllIntegrations();
        reloadIntegrations();

        $(".checkbox-s").bootstrapSwitch().on('switchChange.bootstrapSwitch', function (event, state) {
            var type = $(this).closest('.user.row').data('type');
            var name = IntegrationNames[type];
            var always_delete = true;
            if (state) {
                if (type == IntegrationTypes.IMAP) {
                    if ( ! $('#modal_mail_integration').length) {
                        $($('#tpl_modal_mail_integration').html()).appendTo('body');
                    }
                    $('#modal_mail_integration').modal('show');
                    loadImapInfo();
                } else if (type == IntegrationTypes.TWITTER) {
                    loginTwitter();
                } else if (type == IntegrationTypes.GOOGLE) {
                    loginGoogle();
                } else if (type == IntegrationTypes.SLACK) {
                    loginSlack();
                }
            } else {
                if (confirm("Are you sure you want to deactivate " + name + " integration?")) {
                    if ( ! always_delete && type == IntegrationTypes.IMAP) {
                        $.ajax({
                            method: "PUT",
                            url: '/api/v1/integrations/imap',
                            data: {status: 0},
                            success: function () {
                                deactivateIntegration(type);
                                showSuccessMessage("Success", "The " + name + " integration has been deactivated successfully.");
                            }
                        });
                    } else {
                        $.ajax({
                            method: 'DELETE',
                            url: '/api/v1/integrations/' + name,
                            success: function () {
                                deactivateIntegration(type);
                                showSuccessMessage("Success", "The " + name + " has been deactivated successfully.");
                            }
                        });
                    }
                } else {
                    $(this).bootstrapSwitch('state', true, true);
                }
            }
        });

        hideGlobalLoader();
    }

    return scope;
})();

// USER + TEAM SETTINGS PAGES


function openBilling() {
    $subscriptions = [];
    $plans = [];
    $.get('/api/v1/subscriptions', function (data) {
        $.each(data, function (id, result) {
            var monthly_id, annually_id;
            $.each(result.plans, function (plan_id, plan_result) {
                if (plan_result.period == 1) {
                    plan_result.period_word = 'month';
                    plan_result.month_period_word = 'month';
                } else if (plan_result.period == 12) {
                    plan_result.period_word = 'year';
                    plan_result.month_period_word = 'month';
                } else {
                    plan_result.period_word = false;
                    plan_result.month_period_word = false;
                }

                if (plan_result.price == 0) {
                    plan_result.price_word = 'Free';
                    plan_result.month_price_word = 'Free';
                } else {
                    plan_result.price_word = '$' + plan_result.price;
                    plan_result.month_price_word = '$' + plan_result.price / plan_result.period;
                }

                $plans[plan_result.id] = plan_result;
            });
            if (result.plans.length === 2) {
                if (result.plans[1].period == 12) {
                    monthly_id = result.plans[0].id;
                    annually_id = result.plans[1].id;
                    monthly_class = result.plans[0].class;
                    annually_class = result.plans[1].class;
                } else {
                    monthly_id = result.plans[1].id;
                    annually_id = result.plans[0].id;
                    monthly_class = result.plans[1].class;
                    annually_class = result.plans[0].class;
                }
            } else {
                monthly_id = result.plans[0].id;
                annually_id = result.plans[0].id;
                monthly_class = result.plans[0].class;
                annually_class = result.plans[0].class;
            }
            $subscriptions[result.id] = result;
            $subscriptions[result.id].monthly_id = monthly_id;
            $subscriptions[result.id].annually_id = annually_id;
            $subscriptions[result.id].monthly_class = monthly_class;
            $subscriptions[result.id].annually_class = annually_class;
        });
        $.each(data.reverse(), function (id, result) {
            var plan_id = $subscriptions[result.id].monthly_id;
            var plan_class = $subscriptions[result.id].monthly_class;
            // var subscription_html = '<div id="subscription_' + result.id + '" class="subscription-outside col-md-4">\
            //         <div class="price-box">\
            //             <h1>' + result.name + '</h1>\
            //             <h2 class="price">' + $plans[plan_id].price_word + '</h2>';
            // if ($plans[plan_id].period_word !== false) {
            //     subscription_html += '<span class="period">/' + $plans[plan_id].period_word + '</span>';
            // }

            // subscription_html += '\
            //             <div class="info">\
            //                 <ul>\
            //                     <li><span class="team-size">' + result.users + '</span> User Account</li>\
            //                     <li><span class="deal-count">' + result.deals + '</span> Lead Credits</li>\
            //                     <li>Email Automation</li>\
            //                     <li>Reports</li>\
            //                 </ul>\
            //             </div>\
            //             <div class="select-plan">\
            //                 <button class="btn-blue btn-primary btn-cliently btn-choose-plan" href="javascript:void(0);">SUBSCRIBE</button>\
            //             </div>\
            //         </div>\
            //     </div>';
            // $('.price-boxes-container').prepend(subscription_html);

            $('#subscription_' + result.id).data('id', result.id);
            $('#subscription_' + result.id).data('plan-id', plan_id);
            $('#subscription_' + result.id).data('value', $plans[plan_id].price);
            $('#subscription_' + result.id + ' .btn-choose-plan').attr('onclick', 'openPaymentPage(' + current_account_id + ', ' + result.id + ', ' + plan_id + ', "' + plan_class + '");');
        });
        $('#annual-billing-outside').click(function () {
            $('.subscription-outside').filter(function () {
                var plan_id = $subscriptions[$(this).data('id')].annually_id;
                var plan_class = $subscriptions[$(this).data('id')].annually_class;
                update_subscription_card($(this), $(this).data('id'), plan_id, plan_class);
            });
        }).click();
        $('#monthly-billing-outside').click(function () {
            $('.subscription-outside').filter(function () {
                var plan_id = $subscriptions[$(this).data('id')].monthly_id;
                var plan_class = $subscriptions[$(this).data('id')].monthly_class;
                update_subscription_card($(this), $(this).data('id'), plan_id, plan_class);
            });
        });
        $('#billing-page a[href="#signup-for-free"]').click(function () {
            var $this = $(this);
            var html = $this.html();
            $this.html('<i class="fa fa-spin fa-spinner"></i>');
            $.ajax({
                url: '/api/v1/accounts/' + current_account_id,
                method: 'PUT',
                data: {next_plan_class: 'free'}
            }).done(function () {
                showSuccessMessage('Billing', 'You have signed up for the free plan successfully.');
                $this.html(html);
                openBillingPage(false);
            }).fail(function () {
                $this.html(html);
            });
            return false;
        });
        $('#billing-page a[href="#cancel-subscription"]').click(function () {
            var $this = $(this);
            var html = $this.html();
            $this.html('<i class="fa fa-spin fa-spinner"></i>');
            $.ajax({
                url: '/api/v1/accounts/' + current_account_id,
                method: 'PUT',
                data: {next_plan_class: 'free'}
            }).done(function () {
                $('#billing_period_word').html($('#billing_period_word').html() + ' (cancelled)');
                showSuccessMessage('Billing', 'You have cancelled your subscription successfully.');
                $this.html(html);
                openBillingPage(false);
            }).fail(function () {
                $this.html(html);
            });
            return false;
        });
        $user_usage = [];
        $.get('/api/v1/accounts/' + current_account_id, function (data) {
            $user_usage = data;
            var plan = $plans[data.plan_id];
            var next_plan = $plans[data.next_plan_id];
            var subscription = $subscriptions[plan.subscription_id];
            var next_subscription = $subscriptions[next_plan.subscription_id];
            var period_word = subscription.name;
            var next_period_word = next_subscription.name;
            if (plan.period == 1)
                period_word += ' Monthly';
            else if (plan.period == 12)
                period_word += ' Annual';
            else
                period_word = 'No Payment';
            if (plan.period == 1 || plan.period == 12) {
                period_word += (data.plan_id != data.next_plan_id && data.next_plan_id == 1 ? ' (cancelled)' : '');
            }

            if (next_plan.period == 1)
                next_period_word += ' Monthly';
            else if (next_plan.period == 12)
                next_period_word += ' Annual';
            else
                next_period_word = 'No Payment';
            if (plan.price == 0)
                price_word = 'Free';
            else
                price_word = '$' + plan.price;
            if (next_plan.price == 0)
                next_price_word = 'Free';
            else
                next_price_word = '$' + next_plan.price;
            updateAvailableDealCount(data.credit_balance);
            $('#billing_member_count').text(data.member_count);
            $('#billing_period_word').text(period_word);
            $('#billing_price').text(price_word);
            $('#billing_credit_balance').text(data.credit_balance);
            if (plan.period == -1) {
                $('#billing_next_payment').hide();
                $('#billing_ends_on').hide();
            } else {
                $('#billing_next_payment').show();
                $('#billing_ends_on').show();
                var dt = new Date(data.plan_started_at * 1000);
                dt.setMonth(dt.getMonth() + parseInt(plan.period));
                var expire_at = dt.getTime() / 1000;
                var time_left = Math.max(expire_at - Date.now() / 1000, 0);
                var fullday = 60 * 60 * 24;
                var days_left = Math.ceil(time_left / fullday)

                $('#billing_days').text(days_left);
                $('#billing_expire_at').text(getDateString(expire_at));
            }

            if (next_plan.period == -1) {
                $('#billing_next_payment').hide();
                $('#billing_next_plan').hide();
            } else {
                $('#billing_next_payment').show();
                $('#billing_next_plan').show();
                $('#next_plan').text(next_period_word);
            }

            $('body').data('plan-id', data.plan_id);
            $('body').data('plan-period', plan.period);
            $('body').data('paid-at', data.plan_started_at);
            $('.subscription-outside').filter(function () {
                if ($(this).data('plan-id') == data.plan_id) {
                    $('#payment-form button').prop('disabled', true).hide();
                    $(this).find('.btn-choose-plan').prop('disabled', true);
                    if (plan.period == -1) {
                        $(this).find('.btn-choose-plan').text('ACTIVE');
                    } else {
                        $(this).find('.btn-choose-plan').text('Ends on: ' + getDateString(expire_at));
                    }
                } else {
                    $('#payment-form button').prop('disabled', false).show();
                    $(this).find('.btn-choose-plan').prop('disabled', false);
                    $(this).find('.btn-choose-plan').text('SUBSCRIBE');
                }
            });
        });
    });
}

function openPaymentPage(account_id, subscription_id, plan_id, plan_class) {
    if ($user_usage.plan_id != 1) {
        $.ajax({
            url: '/api/v1/accounts/' + account_id,
            method: 'PUT',
            data: {next_plan_class: plan_class}
        }).done(function () {
            showSuccessMessage('Billing', 'You have changed your next subscription successfully.');
            refreshAvailableDealCount();
            openBillingPage(false);
        });
    } else {
        $.get("/js/ajax/payments.html?" + (new Date().getTime()), function (data) {
            $("#settings-pages").html(data);
            $('#annual-billing-inside').click(function () {
                $('.subscription-inside').filter(function () {
                    var plan_id = $subscriptions[$(this).data('id')].annually_id;
                    var plan_class = $subscriptions[$(this).data('id')].annually_class;
                    update_subscription_card($(this), $(this).data('id'), plan_id, plan_class);
                });
            });
            $('#monthly-billing-inside').click(function () {
                $('.subscription-inside').filter(function () {
                    var plan_id = $subscriptions[$(this).data('id')].monthly_id;
                    var plan_class = $subscriptions[$(this).data('id')].monthly_class;
                    update_subscription_card($(this), $(this).data('id'), plan_id, plan_class);
                });
            });
            update_subscription_card($('#payment-page .card'), subscription_id, plan_id, plan_class);
        });
    }
}

// Add Team Member
function addTeamMemberWindow() {
    $('#add-team-member-modal').fadeToggle();
}

function addTeamMember() {
    var member = $('#teamMemberName').val();
    var email  = $('#teamMemberEmail').val();
    var role   = $('#teamMemberRole').val();
    if (member != '' && email != '' && role != '') {
        $.ajax({
            url: '/api/v1/workspaces/' + current_workspace_id + '/workspace_members',
            method: 'POST',
            data: {
                user_name: member,
                user_email: email,
                role: role,
                is_enabled: 1
            }
        }).done(function (data) {
            showSuccessMessage('Workspace members', 'You have successully sent invitation to ' + email);

            if (role === 'owner')      role_title = 'Owner';
            else if (role === 'admin') role_title = 'Administrator';
            else if (role === 'user')  role_title = 'Team member';

            $('.table-body').prepend('<div class="user row" data-id="' + data.id + '">\
                <div class="col-md-1 check-box-col text-center">\
                    <input type="checkbox">\
                </div>\
                <div class="col-md-2 col-name">\
                    <img alt="Member" class="img-responsive member-img" src="/images/profile-blank.png" />\
                    <h1>' + member + '</h1><br>\
                    <span class="email">' + email + '</span>\
                </div>\
                <div class="col-md-2 role-switch-container">\
                    <span class="dropdown-toggle team-member-role" data-toggle="dropdown">' + role_title + '</span>\
                    <ul class="dropdown-menu pull-right">\
                        <li><a data-role="admin">Administrator</a></li>\
                        <li><a data-role="user">Team member</a></li>\
                    </ul>\
                </div>\
                <div class="col-md-2">\
                    <ul class="social-links">\
                    </ul>\
                </div>\
                <div class="col-md-2 text-center"> - </div>\
                <div class="col-md-2 text-center">0</div>\
                <div class="col-md-1 text-right dropdown-options">\
                    <i class="dropdown-toggle" data-toggle="dropdown">\
                        <i class="fa fa-circle-thin"></i>\
                        <i class="fa fa-circle-thin"></i>\
                        <i class="fa fa-circle-thin"></i>\
                    </i>\
                    <ul class="dropdown-menu">\
                        <li>\
                            <a href="#">Make Inactive</a>\
                        </li>\
                        <li>\
                            <a href="javascript:void(0)" onclick="removeTeamMemberI(this)">Remove</a>\
                        </li>\
                    </ul>\
                </div>\
            </div>\
            <hr>');
            $('#teamMemberName').val('');
            $('#teamMemberEmail').val('');
            $('#teamMemberRole').val('user');
        }).fail(function (jqXHR) {
            if (jqXHR.status === 409) {
                showErrorMessage('Error', 'Please upgrade your account to add additional workspace members.');
            } else {
                showSuccessMessage('Workspace members', 'User with such email is already in your workspace');
            }
        });
        
        $('#add-team-member-modal').fadeOut();
    }
}

// Search Team Members

function searchTeamMembers(el) {
    var keywords = $(el).val();
    $('.team-members-list .table-body .user').each(function () {
        text_to_search = $(this).find('.col-md-3 h1').text();
        if (text_to_search.search(new RegExp(keywords, "i"))) {
            $(this).fadeOut();
            $(this).next().fadeOut();
        } else {
            $(this).fadeIn();
            $(this).next().fadeIn();
        }
    })
}

// Remove Team Members
function removeTeamMembers() {
    $('.table-body .user').each(function () {

        if ($(this).children('.check-box-col').find('input:checked').length == 1) {
            $(this).fadeOut();
            $(this).next().remove();
        }


    })
}

function removeTeamMemberI(el) {
    $(el).parents('.user').fadeOut();
    $(el).parents('.user').next().fadeOut();
}

var IntegrationTypes = {
    IMAP:    1,
    TWITTER: 2,
    GOOGLE:  3,
    SLACK:   4
};
var IntegrationNames = ['', 'imap', 'twitter', 'google', 'slack'];
var timerG = null;
var timerT = null;
var child_integration_window = null;

function deactivateAllIntegrations() {
    for (var i = 0; i < IntegrationNames.lengh - 1; i++) {
        deactivateIntegration(i + 1);
    }
}

function reloadIntegrations(type) {
    if (type == undefined) {
        type = 0;
    }
    $.getJSON("/api/v1/integrations/" + IntegrationNames[type], function (data) {
        if (type > 0) {
            if (data && data.integration) {
                activateIntegration(data.integration, type);
                if (type != IntegrationTypes.TWITTER && data.integration.is_primary != 1) {
                    checkAndSetPrimaryEmail(type);
                }
            } else {
                deactivateIntegration(type);
            }
        } else {
            for (var i = 0; i < data.length; i++) {
                activateIntegration(data[i], data[i].type);
            }
        }
    });
}

function activateIntegration(integration, type) {
    if (integration.status != 2) {
        return;
    }

    var date = new Date(integration.created_at * 1000);
    $("#integration-" + type)
            .find("h1").html(integration.name).end()
            .find(".email").html(integration.handle).end()
            .find('.date').html(date.toLocaleString()).end()
            .find(".checkbox-s").bootstrapSwitch('state', true, true);
}

function deactivateIntegration(type) {
    $("#integration-" + type)
            .find("h1").html('').end()
            .find(".email").html('').end()
            .find('.date').html('').end()
            .find(".checkbox-s").bootstrapSwitch('state', false, true);
}

function loginGoogle() {
    child_integration_window = SocialIntegrator.openIntegrationWindow('/integrations/google/connect', '', 626, 436);
}
function checkChildGoogle() {
    if (child_integration_window.closed) {
        clearInterval(timerG);
        reloadIntegrations(IntegrationTypes.GOOGLE);
    }
}

function loginTwitter() {
    child_integration_window = SocialIntegrator.openIntegrationWindow('/integrations/twitter/connect', '', 626, 436);
    timerT = setInterval(checkChildTwitter, 500);
}

function checkChildTwitter() {
    if (child_integration_window.closed) {
        clearInterval(timerT);
        reloadIntegrations(IntegrationTypes.TWITTER);
    }
}

function loginSlack() {
    child_integration_window = SocialIntegrator.openIntegrationWindow('/integrations/slack/connect', '', 626, 436);
    timerT = setInterval(checkChildSlack, 500);
}

function checkChildSlack() {
    if (child_integration_window.closed) {
        clearInterval(timerT);
        reloadIntegrations(IntegrationTypes.SLACK);
    }
}

function loadImapInfo() {
    $('#modal_mail_integration').find('.btn-primary').attr('disabled', true).html('<i class="fa fa-spin fa spinner"></i> Loading...');
    $.get('/api/v1/integrations/imap', function (data) {
        if (data.integration !== undefined) {
            $('#fullname').val(data.integration.values.fullname);
            $('#email').val(data.integration.values.email);
            $('#password').val(data.integration.values.password);
            $('#smtp_port').val(data.integration.values.smtp_port);
            $('#smtp_server').val(data.integration.values.smtp_server);
            $('#imap_port').val(data.integration.values.imap_port);
            $('#imap_server').val(data.integration.values.imap_server);
        }
        $('#modal_mail_integration').find('.btn-primary').attr('disabled', false).html('<i class="fa fa-save"></i> Save Changes');
    });
}

function saveImapInfo() {
    var email_autoconfig = $('#email_autoconfig')[0].checked;
    var fullname         = $('#fullname').val();
    var email            = $('#email').val();
    var password         = $('#password').val();
    var imap_port        = email_autoconfig ? '' : $('#imap_port').val();
    var imap_server      = email_autoconfig ? '' : $('#imap_server').val();
    var port             = email_autoconfig ? '' : $('#smtp_port').val();
    var smtp             = email_autoconfig ? '' : $('#smtp_server').val();

    if (fullname == "") {
        showErrorMessage('IMAP Info', 'Please enter full name.');
        validateForm('#fullname');
        return false;
    }

    if (email == "") {
        showErrorMessage('IMAP Info', 'Please enter email.');
        validateForm('#email');
        return false;
    }

    if (password == "") {
        showErrorMessage('IMAP Info', 'Please enter password.');
        validateForm('#password');
        return false;
    }

    if ( ! email_autoconfig) {
        if (imap_port == "") {
            showErrorMessage('IMAP Info', 'Please enter IMAP Port.');
            validateForm('#imap_port');
            return false;
        }

        if (imap_server == "") {
            showErrorMessage('IMAP Info', 'Please enter IMAP Server.');
            validateForm('#imap_server');
            return false;
        }

        if (port == "") {
            showErrorMessage('IMAP Info', 'Please enter SMTP Port.');
            validateForm('#smtp_port');
            return false;
        }

        if (smtp == "") {
            showErrorMessage('IMAP Info', 'Please enter SMTP Server.');
            validateForm('#smtp_server');
            return false;
        }
    }

    var isEmailAlreadyExisting = false;
    $.ajax({
        url: '/api/v1/validations/email/availability?q=' + email,
        async: false,
        dataType: 'json',
        success: function (data) {
            if ( ! data.success) {
                isEmailAlreadyExisting = true;
            }
        }
    });

    if (isEmailAlreadyExisting) {
        showErrorMessage('IMAP Info', 'Email account already exists, please use a different one.');
        return false;
    }

    $('#modal_mail_integration').find('.btn-primary').attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Saving...');
    $.ajax({
        method: "PUT",
        url: '/api/v1/integrations/imap',
        data: {
            name: fullname,
            values: {
                fullname: fullname,
                email: email,
                password: password,
                imap_server: imap_server,
                imap_port: imap_port,
                smtp_server: smtp,
                smtp_port: port
            },
            handle: email,
            status: 2
        },
        success: function () {
            $('#modal_mail_integration').modal('hide');
            $('.modal-backdrop').removeAttr('style');
            showSuccessMessage("Success", "The imap has been saved successfully.");
            if ($('#page_integrations').length) {
                reloadIntegrations(IntegrationTypes.IMAP);
            } else {
                var $int_containers = $('.social-integration-handler[data-type="imap"]');
                $int_containers.each(function(index, obj) {
                    SocialIntegrator.reloadIntegration($(obj));
                });
            }
        }
    }).fail(function(jqXHR) {
        var error = jqXHR.responseJSON.errors[0];
        $('#modal_mail_integration').find('.btn-primary').removeAttr('disabled').html('Save changes');
        if (error.code_class == null) {
            showErrorMessage("Error", "Wrong input");
        } else if (error.object === 'values.fullname') {
            showErrorMessage("Error", "Invalid fullname");
        } else if (error.object === 'values.email') {
            showErrorMessage("Error", "Invalid email");
        } else if (error.object === 'values.password') {
            showErrorMessage("Error", "Invalid password");
        } else if (error.object === 'values.imap_server') {
            showErrorMessage("Error", "Invalid IMAP server");
        } else if (error.object === 'values.imap_port') {
            showErrorMessage("Error", "Invalid IMAP port");
        } else if (error.object === 'values.smtp_server') {
            showErrorMessage("Error", "Invalid SMTP server");
        } else if (error.object === 'values.smtp_port') {
            showErrorMessage("Error", "Invalid SMTP port");
        } else if (error.object === 'email_autoconfig') {
            if (error.reason === 'only_pop3') {
                showErrorMessage("Email Autoconfiguration", "Looks like your mail server doesn't have IMAP support");
            } else if (error.reason === 'failure') {
                showErrorMessage("Email Autoconfiguration", "We couldn't autoconfigure your mailbox. Please input values manually");
            }
        }
    });

    return false;
}

function checkAndSetPrimaryEmail(type) {
//        $.get('/api/v1/integrations/' + IntegrationNames[type], function (data) {
//            if (data.integration !== undefined && data.integration.is_primary != 1) {
            if (!confirm('Will you set this email as primary?')) {
                return;
            }

            $.ajax({
                method: "PUT",
                url: '/api/v1/integrations/' + IntegrationNames[type],
                data: {is_primary: 1},
                success: function () {
                    showSuccessMessage("You have set primary email successfully.");
                }
            });
//            }
//        });
}

$(document).on('click', '#modal_mail_integration button[data-dismiss="modal"]', function() {
    $('.modal-backdrop').removeAttr('style');
})
