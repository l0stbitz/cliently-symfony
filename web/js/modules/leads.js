var LeadSourceTypes = {
    TWITTER_TWEET: 'twitter_tweet',
    TWITTER_USER: 'twitter_user',
    DBPERSON: 'dbperson_user'
};

var LeadSourceTypeIcons = {};
LeadSourceTypeIcons[LeadSourceTypes.TWITTER_TWEET] = '<i class="fa fa-cliently-twitter"></i>';
LeadSourceTypeIcons[LeadSourceTypes.TWITTER_USER] = '<i class="fa fa-cliently-twitter"></i>';
LeadSourceTypeIcons[LeadSourceTypes.DBPERSON] = '<img src="/images/company-gray.png" />';

var Leads = (function () {
    var scope = {};
    var initialized = false;

    var $stageColumns;
    var $deleteStageOptionDlg = null;

    var qt = null;
    var qtTimer = null;

    var lead = null;
    var leadId = null;
    var leadAccepted = false;

    var $cardPopup = null;
    var $card = null;

    var $personInfo = null;

    var contactAvatarUploader = null;
    var companyLogoUploader = null;

    var $manageInfo = null;
    var $stageInfo = null;
    var $sourceInfo = null;
    var $source = null;

    var $leadActions = null;
    var $clientTwitterInfo = null;

    function init() {
        $(window).resize(function () {
            if ($('#page-clients.main-block-wrapper').length) {
                resizeClients();
            }
        });

        // Search.initLeadSearch();

        $(document).on('mousemove', function (e) {
            movePopover($(e.target));
        }).on('click', '.owner-dropdown-container .dropdown-menu li', function (e) {
            var $li = $(this);
            $li.parents('.owner-dropdown-container').find('h1').text($li.text());
            $li.siblings('.active').removeClass('active');
            $li.addClass('active');
            if ($li.hasClass('all-owners')) {
                current_owner_id = false;
            } else {
                current_owner_id = $li.data('id');
            }
            reloadClients(current_owner_id, current_pipeline_id);
         }).on('click', '.pipeline-dropdown-container .dropdown-menu li', function (e) {
            var $li = $(this);
            if ($(this).hasClass('add-new')) {
                var $btn = $(this);
                var $toggle = $btn.parents('.pipeline-dropdown-container').find('>.pipeline-add-popup-toggle > .toggle');
                if ( ! $toggle.length) {
                    var $toggle_container = $('<div class="pipeline-add-popup-toggle"><div class="toggle"></div></div>').appendTo($btn.parents('.pipeline-dropdown-container'));
                    $toggle = $toggle_container.find('.toggle');
                    $toggle.editable({
                        type: 'text',
                        placement: 'right',
                        emptytext: 'Pipeline Title',
                        name: 'name',
                        validate: function (value) {
                            if ($.trim(value) == '')
                                return 'Enter pipeline title.';
                        },
                        success: function (response, newValue) {
                            addPipeline(newValue);
                            $('#page-clients .main-block .main-header .pipeline-dropdown-container h4').text(newValue);
                        }
                    });
                }
                $toggle.editable('setValue', '').click();

                e.preventDefault();
                e.stopPropagation();
                e.cancelBubble = true;
                return false;
            } else {
                $li.parents('.pipeline-dropdown-container').find('h4').text($li.text());
                $li.siblings('.active').removeClass('active');
                $li.addClass('active');
                current_pipeline_index = $(this).data('index');
                current_pipeline_id = $(this).data('id');
                var pipeline_header = $('#page-clients .main-block .main-header .pipeline-dropdown-container h4');
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

                reloadClients(current_owner_id, current_pipeline_id);
            }
        }).on('click', '#page-clients.main-block-wrapper .main-block .btn-stage-add', function (e) {
            var $btn = $(this);
            var btn_container = $btn.parent('.pipeline-addstage, .pipeline-addstage-big');
            var $toggle = btn_container.find('>.stage-add-popup-toggle');
            if (!$toggle.length) {
                if (btn_container.filter('.pipeline-addstage-big')) {
                    var placement = 'top';
                } else {
                    var placement = 'right';
                }
                $toggle = $('<div class="stage-add-popup-toggle" />').insertBefore($btn).editable({
                    type: 'text',
                    placement: placement,
                    emptytext: 'Stage Title',
                    name: 'name',
                    validate: function (value) {
                        if ($.trim(value) == '')
                            return 'Enter stage title.';
                    },
                    success: function (response, newValue) {
                        $('#page-clients .pipeline-addstage-big').remove();
                        addStage(newValue);
                    }
                });
            }
            $toggle.editable('setValue', '').click();

            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            return false;
        }).on('click', '#page-clients.main-block-wrapper .main-block .main-header .clients-sort-dropdown ul.dropdown-menu>li>a', function (e) {
            var $li = $(this).parent();
            $li.siblings('.active').removeClass('active');
            $li.addClass('active');
            $li.closest('.clients-sort-dropdown').attr('data-sort', this.rel);
            reloadClients();
        }).on('click', '.columns.stage-columns .column.stage-column .stage .btn-client-add', function (e) {
            var $btn = $(this);
            var $stage = $btn.closest('.stage');
            var $toggle = $stage.find('>.stage-inner>.client-add-popup-toggle');
            if (!$toggle.length) {
                $toggle = $('<div class="client-add-popup-toggle" />').appendTo($stage.find('>.stage-inner')).editable({
                    type: 'text',
                    emptytext: 'Client Name',
                    name: 'name',
                    validate: function (value) {
                        if ($.trim(value) == '')
                            return 'Enter client name.';
                    },
                    success: function (response, newValue) {
                        addClient(newValue, $(this).closest('.stage'));
                    }
                }).on('shown', function () {
                    setTimeout(function () {
                        $(".columns-container").css('overflow-x', 'visible');
                    }, 100);
                }).on('hidden', function () {
                    $(".columns-container").css('overflow-x', 'auto');
                });
            }
            $toggle.editable('setValue', '').click();

            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            return false;
        }).on('click', '.columns.stage-columns .column.stage-column .stage .stage-header .close', function (e) {
            deleteStage($(this).closest('.stage'));
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            return false;
        }).on('click', '.column.stage-column .stage .stage-content .clients .client', function () {
            var $lead = $(this).removeClass("portlet-new").addClass('portlet-accessed');
            var lid = $lead.attr('data-lead-id');
            if (lid != leadId) {
                lead = $lead.data('lead');
                leadId = lid;
            }
            popupCard();
        }).on('click', '.column.stage-column .stage .stage-content .clients .client .btn-client-delete', function (e) {
            deleteClient($(this).closest('.client'));
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            return false;
        }).on('click', '.manage-pipeline-info .dropdown-toggle', function (e) {
            var dropdown = $(this).siblings('ul');
            $.get('/api/v1/workspaces/' + current_workspace_id + '/pipelines', function (data) {
                dropdown.html('');
                if (lead.stage.selected_pipeline_id == null) selected_pipeline_id = lead.stage.pipeline_id;
                else selected_pipeline_id = lead.stage.selected_pipeline_id;
                $.each(data, function (id, pipeline) {
                    dropdown.append('<li data-id="' + pipeline.id + '"' + (pipeline.id === selected_pipeline_id ? ' class="active"' : '') + '><a>' + pipeline.name + '</a></li>');
                });
            });
        }).on('click', '.manage-pipeline-info .dropdown-menu li', function (e) {
            var pipeline_id = $(this).data('id');
            var $li = $(this);
            $li.parents('.manage-pipeline-info').find('h4').text($li.text());
            $li.siblings('.active').removeClass('active');
            $li.addClass('active');
            lead.stage.selected_pipeline_id = pipeline_id;
            $.get('/api/v1/pipelines/' + pipeline_id + '/stages', function (result) {
                fillStage(true, result);
            });
        }).on('click', '.manage-owner-info .dropdown-toggle', function (e) {
            var dropdown = $(this).siblings('ul');
            $.get('/api/v1/workspaces/' + current_workspace_id + '/workspace_members?include_user=1', function (data) {
                dropdown.html('');
                $.each(data, function (id, workspace_member) {
                    if (workspace_member.user !== null) {
                        dropdown.append('<li data-id="' + workspace_member.user.id + '"' + (workspace_member.user.id === lead.owner_id ? ' class="active"' : '') + '><a>' + workspace_member.user.first_name + ' ' + workspace_member.user.last_name + '</a></li>');
                    }
                });
            });
        }).on('click', '.manage-owner-info .dropdown-menu li', function (e) {
            var owner_id = $(this).data('id');
            var $li = $(this);
            $li.siblings('.active').removeClass('active');
            $li.addClass('active');

            $.ajax({
                url: '/api/v1/deals/' + lead.id,
                type: 'PUT',
                data: {owner_id: owner_id},
                success: function () {
                    $li.parents('.manage-owner-info').find('h4').text($li.text());
                    lead.owner_id = owner_id;
                }
                });

        }).on('click', '#lead-card-container.modal .lead-card .stage-info .stages .stage .point', function () {
            if ($stageInfo.hasClass('saving')) {
                return;
            }

            var $point = $(this);
            var stid = $point.parent().attr('data-stage-id');
            if (stid == lead.stage_id) {
                return;
            }

            var $xhr = changeStage(stid);
            if ($xhr) {
                $stageInfo.addClass('saving');
                $point.html('<i class="fa fa-spin fa-spinner"></i>').parent().addClass('saving');
                $xhr.done(function () {
                    lead.stage_id = stid;
                    $stageInfo.find('.stage-info-reminder').addClass('hide');
                    $point.empty().parent().removeClass('saving');
                    $stageInfo.removeClass('saving');
                    fillStage();
                }).fail(function () {
                    $point.empty().parent().removeClass('saving');
                    $stageInfo.removeClass('saving');
                });
            }
        }).on('click', '#lead-card-container.modal .lead-card .lead-info .source-info .source-toggle', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            toggleSource();
        }).on('show.bs.tab', '#lead-card-container.modal .lead-actions>ul.nav.nav-tabs>li>a', function () {
            var tab = this.href.substr(this.href.indexOf('#'));
            switch (tab) {
                case '#lead-action-tab-pane-task':
                    onTaskActionTabShown();
                    break;
                case '#lead-action-tab-pane-note':
                    onNoteActionTabShown();
                    break;
                case '#lead-action-tab-pane-mail':
                    onMailActionTabShown();
                    break;
                case '#lead-action-tab-pane-twitter':
                    onTwitterActionTabShown();
                    break;
            }
        }).on('click', '#lead-card-container.modal .lead-actions>ul.nav.nav-tabs>li>a', function () {
            var tab = this.href.substr(this.href.indexOf('#'));
            switch (tab) {
                case '#lead-action-tab-pane-twitter':
                    // lead.clients[0].integrations.twitter = -1;
                    $(this).find('span.count').fadeOut();
                    break;
            }
        }).on('click', '.deals-import-export-modal-btn', function () {
            popupDealImportExportModal();
            return false;
        }).on('change', '#modal_deal_import_export input[type="file"]', function () {
            $file_input = $(this);
            $file_input.data('delimiter', false);
            var file = $(this)[0].files[0];
            $('#step_deal_import_file h4').text(file.name);
            Papa.parse(file, {
                complete: function(results, file) {
                     $('#step_deal_import_fields_match h4').text(file.name);
                     $('#step_deal_import_dst h4').text(file.name);
                     $('#modal_deal_import_export .row-count span').text(numberWithCommas(results.data.length - 1));
                     $('#step_deal_import_dst .row-count-inner span').text(numberWithCommas(results.data.length - 1));
                    if (results.data.length < 2) {
                        alert('File must have at least 1 data row');
                    } else if (results.data.length > 501) {
                        alert('File can have maximum 500 data rows');
                    } else {
                        var headers = results.data.shift();
                        var headers_count = headers.length;
                        $('#modal_deal_import_export').attr('data-ui-import-step', 'step_deal_import_fields_match');
                        var row_container = $('#step_deal_import_fields_match tbody');
                        row_container.html('');
                        var tpl_row = $($('#tpl_tblrow_deal_import_fields').html());
                        for (var i = 0; i < headers_count; i++) {
                            tpl_row.clone().appendTo(row_container).find('.csv-col-name').text(headers[i]);
                        }
                        $file_input.data('delimiter', results.meta.delimiter);
                    }
                }
            });
            return false;
        }).on('shown.bs.tab', '#modal_deal_import_export .nav-tabs a', function(e) {
            var tab = $(e.target).attr('href').substr(1);
            $('#modal_deal_import_export').attr('data-ui-tab', tab);
        }).on('click', '#modal_deal_import_export .btn-next', function() {
            var valid_select = false;
            $.each($('#step_deal_import_fields_match select'), function(i, select) {
                if ($(select).val()) {
                    valid_select = true;
                    return false;
                }
            });

            if ( ! valid_select) {
                alert('At least 1 row has to be selected');
            } else {
                $('#modal_deal_import_export').attr('data-ui-import-step', 'step_deal_import_dst');
            }
        }).on('change', '#step_deal_import_fields_match select', function() {
            $('#step_deal_import_fields_match option').attr('disabled', false);
            $.each($('#step_deal_import_fields_match select'), function(i, select) {
                var $select = $(select);
                var select_val = $select.val();
                if (select_val) {
                    $.each($('#step_deal_import_fields_match select').not($select), function(j, compared_select) {
                        $(compared_select).find('option[value="' + select_val + '"]').attr('disabled', true);
                    });
                }
            });
         }).on('change', '#select_deal_import_dst_pipeline', function() {
            var tpl_dom_option = $($('#tpl_dom_option').html());
            var pipeline_id = $(this).val();
            var pipeline_index = $(this).find('option[value="' + pipeline_id + '"]').data('index');

            $('#select_deal_import_dst_stage').html('');
            $.each(pipelines[pipeline_index].stages, function(i, stage) {
                tpl_dom_option.clone().attr('value', stage.id).text(stage.name).appendTo($('#select_deal_import_dst_stage'));
            });
        }).on('change', '#step_deal_import_dst tbody input[type="checkbox"]', function() {
            $(this).parents('tr').find('select').prop('disabled', ! this.checked);
        }).on('click', '#modal_deal_import_export .btn-import', function() {
            var $button = $(this);
            var $file_input = $('#modal_deal_import_export input[type="file"]').first();

            $button.addClass('is-pending');
            if ( ! $file_input[0].files.length) {
                alert('No file selected');
                return false;
            }

            var fdata = new FormData();
            var delimiter = $file_input.data('delimiter');
            var stage_id = $('#select_deal_import_dst_stage').val();
            var workflow_id = $('#select_deal_import_dst_workflow').val();

            if ( ! $('#select_deal_import_dst_workflow').prop('disabled') && workflow_id) {
                fdata.append('workflow_id', workflow_id);
            }

            $('#step_deal_import_fields_match select').each(function(i, select) {
                fdata.append('col_map[]', $(select).val());
            });

            if (delimiter) fdata.append('delimiter', delimiter);
            fdata.append('file', $file_input[0].files[0]);

            $.ajax({
                url: '/api/v1/stages/' + stage_id + '/deals/_import_csv',
                method: 'POST',
                data: fdata,
                contentType: false,
                processData: false
            }).done(function(data) {
                $('#step_deal_import_success .row-count-final span').text(numberWithCommas(data.count));
                $('#modal_deal_import_export').attr('data-ui-import-step', 'step_deal_import_success');
                loadClients();
            }).always(function() {
                $button.removeClass('is-pending');
            });
        }).on('submit', '.integration-twitter-add-form', function () {
            var $btn = $(this).find('.btn-save');
            var html = $btn.html();
            $btn.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Saving...');
            var social = $('.social-iconlist form').serializeObject();
            social.twitter = $(this).find('input').val();
            $.ajax({
                url: '/api/v1/clients/' + $personInfo.find('.contact-info').attr('data-client-id'),
                method: 'PUT',
                data: {social: social},
                success: function () {
                    $(this).find('input').val('');
                    lead.clients[0].social = social;
                    fillSocial();
                    $leadActions.find('>.tab-content>.tab-pane>.loaded').removeClass('loaded');
                    loadFullLead().done(function () {
                        $btn.attr('disabled', false).html(html).closest('.dropdown').removeClass('open');
                    });
                }
            }).fail(function () {
                $btn.attr('disabled', false).html(html);
            });
            return false;
        });

        initialized = true;
    }
    scope.load = function () {
        if (!initialized) {
            init();
        }
    };

    // <editor-fold desc="Stage">
    scope.loadStages = function () {
        if (!initialized) {
            init();
        }

        return loadStages();
    };

    function loadStages() {
        $stageColumns = $('#page-clients .columns.stage-columns');
        return $.get('/api/v1/pipelines/' + current_pipeline_id + '/stages', function (result) {
            console.log(result);
            pipelines[current_pipeline_index].stages = result;

            if ( ! result.length) {
                $('#page-clients .columns-container').append('\
                    <div class="pipeline-addstage-big">\
                        <a href="#" class="btn-stage-add">Add Stage +</a>\
                    </div>\
                ');
            } else {
                $('#page-clients .pipeline-addstage-big').remove();
                $.each(result, function (i, stage) {
                    $createStageColumn().appendTo($('.columns.stage-columns'))
                            .find('.column-inner')
                            .append($createStage(stage));
                });
            }
            resizeStageColumns();
        });
    }
    function $createStageColumn() {
        return Layout.$createColumn().addClass('stage-column');
    }
    function $createStage(stage) {
        console.log(stage);
        var html = '\
            <div class="stage">\
                <div class="stage-inner">\
                    <div class="stage-header">\
                        <h1 data-name="name" data-pk="' + stage.id + '">' + stage.name + '</h1>\
                        <p>$<span class="stage-value">' + stage.value + '</span></p>\
                        <h1 class="stage-count-container"><span class="stage-count">0</span> <span>Lead</span></h1>';
        if (workspaces[current_workspace_index].membership.role === 'owner' || workspaces[current_workspace_index].membership.role === 'admin') {
            html += '<a href="#" class="close"><i class="fa fa-times"></i></a>';
        }
        html += '\
                    </div>\
                    <div class="stage-content">\
                        <div class="clients"></div>\
                    </div>\
                    <a class="btn-client-add" href="#">Add a Client...</a>\
                </div>\
            </div>';
        var $stage = $(html).attr('data-stage-id', stage.id).data('stage', stage);

        if (workspaces[current_workspace_index].membership.role === 'owner' || workspaces[current_workspace_index].membership.role === 'admin') {
            $stage.find('.stage-header h1[data-name]').editable({
                validate: function (value) {
                    if ($.trim(value) == '')
                        return 'Enter stage title.';
                },
                title: 'Stage Title',
                placement: 'bottom',
                url: '/api/v1/stages/' + stage.id,
                success: function (response, newValue) {
                    $(this).closest('.stage').data('stage').name = newValue;
                }
            });
        }
        $stage.find('.stage-header h1[data-name]').on('shown', function () {
            setTimeout(function () {
                $(".columns-container").css('overflow-x', 'visible');
            }, 100);
        }).on('hidden', function () {
            $(".columns-container").css('overflow-x', 'auto');
        });

        $stage.find('.clients').sortable({
            items: '.client',
            connectWith: '.clients',
            placeholder: 'client-placeholder',
            zIndex: 2,
            revert: 150,
            forcePlaceholderSize: true,
            helper: 'clone',
            delay: 100,
            distance: 5,
            opacity: 0.85,
            update: function (event, ui) {
                //updatePosition(ui.item);
            },
            start: function (event, ui) {
                $('.stage-content').css('overflow-y', 'hidden');
            },
            stop: function (event, ui) {
                $('.stage-content').removeAttr('style');
                var $client = ui.item;
                var client = $client.data('lead');
                var $newStage = $client.closest('.stage');
                var newStageId = $newStage.attr('data-stage-id');
                $.ajax({
                    url: '/api/v1/deals/' + $client.attr('data-lead-id'),
                    type: 'PUT',
                    data: {stage_id: newStageId},
                    success: function () {
                        var $oldStage = $stageColumns.find('.stage[data-stage-id="' + client.stage_id + '"]');
                        updateStageCount($oldStage);
                        updateStageCount($oldStage);
                        client.stage_id = newStageId;
                        updateStageCount($newStage);
                        updateStageCount($newStage);
                    }
                });
            }
        });

        return $stage;
    }
    function resizeStageColumns() {
        Layout.resizeColumns($('#page-clients'), 20);
        resizeClients();
    }

    function updateStageCount($stage) {
        var count = $stage.find('.clients>.client').length;
        $stage.find('.stage-header .stage-count').html(count).next().html(count > 1 ? 'Leads' : 'Lead');
    }
    function updateStageValue($stage) {
        var value = 0;
        $stage.find('.clients>.client').each(function () {
            value += $(this).data('lead').value;
        });
        $stage.find('.stage-header .stage-value').html(numberWithCommas(value));
    }
    function addStage(name) {
        return $.post('/api/v1/pipelines/' + current_pipeline_id + '/stages', {
            name: name
        }, function (data) {
            var stage = {id: data.stage_id, name: name, value: 0};
            $createStageColumn().appendTo($('.columns.stage-columns'))
                    .find('.column-inner')
                    .append($createStage(stage));
            resizeStageColumns();
        });
    }
    function addPipeline(name) {
        return $.post('/api/v1/workspaces/' + current_workspace_id + '/pipelines', {
            name: name
        }, function (data) {
            pipelines.push(data);
            current_pipeline_id = data.id;
            current_pipeline_index = pipelines.length - 1;

            var pipeline_menu = $('#page-clients .main-block .main-header .pipeline-dropdown-container .dropdown-menu');
            pipeline_menu.html('');
            $.each(pipelines, function (id, pipeline) {
                pipeline_menu.append('<li data-id="' + pipeline.id + '" data-index="' + id + '"' + (pipeline.id == current_pipeline_id ? ' class="active"' : '') + '><a>' + pipeline.name + '</a></li>');
            });
            if (workspaces[current_workspace_index].membership.role === 'owner' || workspaces[current_workspace_index].membership.role === 'admin') {
                pipeline_menu.append('<li class="add-new"><a>Add new</a></li>');
            }

            loadClients(current_owner_id, data.id);
        });
    }
    //Delete Stage
    function deleteStage($stage) {
        var stageId = $stage.attr('data-stage-id');
        $deleteStageOptionDlg = bootbox.dialog({
            title: "Delete Stage",
            message: '<div class="row">  ' +
                    '<div class="col-md-12"> ' +
                    '<form id="delete-stage-option-form" class="form"> ' +
                    '<div class="form-group"> ' +
                    '<label class="control-label">What do you want to do with the leads in this stage?</label> ' +
                    '<div>' +
                    '<div class="radio">' +
                    '<label>' +
                    '<input type="radio" name="stage_delete_option" id="stage-delete-option-delete-leads" value="0" checked="checked"> ' +
                    'Delete the leads in the stage as well.' +
                    '</label> ' +
                    '</div> ' +
                    '<div class="radio">' +
                    '<label>' +
                    '<input type="radio" name="stage_delete_option" id="stage-delete-option-move-leads" value="1"> ' +
                    'Move the leads to <select></select>.' +
                    '</label> ' +
                    '</div> ' +
                    '</div> ' +
                    '</div>' +
                    '</form> </div>  </div>',
            onEscape: true,
            buttons: {
                success: {
                    label: "Delete",
                    className: "btn-danger",
                    callback: function () {
                        bootbox.confirm('Are you sure you want to delete the stage?',
                                function (result) {
                                    if (result) {
                                        $deleteStageOptionDlg.modal('hide');
                                        var option = $deleteStageOptionDlg.find('input[name="stage_delete_option"]:checked').val();
                                        var data = {};
                                        if (option == 1) {
                                            data.deal_new_stage = $deleteStageOptionDlg.find('select').val();
                                        }
                                        // API request
                                        $.ajax({
                                            type: 'DELETE',
                                            url: '/api/v1/stages/' + stageId,
                                            data: data,
                                            success: function () {
                                                showSuccessMessage("Success", "The stage has been deleted successfully.");
                                                $stage.closest('.column').remove();
                                                resizeStageColumns();

                                                if (option == 1) {
                                                    reloadClients();
                                                }
                                                return false;
                                            }
                                        });
                                    }
                                });
                        return false;
                    }
                },
                cancel: {
                    label: "Cancel",
                    className: "btn-default"
                }
            }
        });

        var $stageList = $deleteStageOptionDlg.find('select').empty();
        $stageColumns.find('.stage').each(function () {
            var $st = $(this);
            var stId = $st.attr('data-stage-id');
            $("<option>", {value: stId, html: $st.find('h1[data-name]').html() + (stageId == stId ? '(current)' : ''), disabled: stageId == stId}).appendTo($stageList);
        });

        return false;
    }
    // </editor-fold>

    // <editor-fold desc="Lead">
    scope.loadClients = function (owner_id, pipeline_id) {
        return loadClients(owner_id, pipeline_id);
    };

    function loadClients(owner_id, pipeline_id) {
        var params = getSearchParams();
        if (owner_id == null) owner_id = current_owner_id;
        if (pipeline_id == null) pipeline_id = current_pipeline_id;

        $stageColumns = $('#page-clients .columns.stage-columns');
        $stageColumns.empty();
        return $.get('/api/v1/pipelines/' + current_pipeline_id + '/stages', function (result) {
            pipelines[current_pipeline_index].stages = result;

            if ( ! result.length) {
                $('#page-clients .columns-container').append('\
                    <div class="pipeline-addstage-big">\
                        <a href="#" class="btn-stage-add">Add Stage +</a>\
                    </div>\
                ');
            } else {
                $('#page-clients .pipeline-addstage-big').remove();
                $.each(result, function (i, stage) {
                    $createStageColumn().appendTo($('.columns.stage-columns'))
                            .find('.column-inner')
                            .append($createStage(stage));
                });
            }

            resizeStageColumns();

            var url = '/api/v1/pipelines/' + pipeline_id + '/deals/list';
            if (owner_id != null && owner_id !== false) params.owner_id = owner_id;
            return $.get(url, params, function (leads) {
                if (!leads.length) {
                    return;
                }
                $stageColumns.hide();
                $stageColumns.find('.clients').empty();
                $.each(leads, function (i, lead) {
                    $createClient(lead).appendTo($stageColumns.find('.stage[data-stage-id="' + lead.stage_id + '"] .clients'));
                });

                $stageColumns.find('.stage').each(function () {
                    var $stage = $(this);
                    updateStageCount($stage);
                    updateStageValue($stage);
                });

                $stageColumns.fadeIn();
            });
        });
    }
    function getSearchParams() {
        var sort = $('#page-clients .main-block .clients-sort-dropdown').attr('data-sort');
        if (!sort) {
            return {};
        }

        var params = {
            st: sort,
            so: (sort == 'value' || sort == 'due') ? 'desc' : ''
        };

        return params;
    }
    function reloadClients(owner_id, pipeline_id) {
        showGlobalLoader();
        loadClients(owner_id, pipeline_id).done(function () {
            hideGlobalLoader();
        });
    }
    scope.reloadClients = function () {
        return reloadClients();
    }

    function $createClient(lead) {
        var client_avatar = lead.clients[0] && lead.clients[0].source && lead.clients[0].source.avatar && !DevOptions.debug ? (lead.source._type == LeadSourceTypes.TWITTER_TWEET || lead.source._type == LeadSourceTypes.TWITTER_USER ? '//' : '') + lead.clients[0].source.avatar : '/images/profile-blank.png';
        var client_name = lead.clients[0].name.length > 25 ? lead.clients[0].name.substr(0, 25) : lead.clients[0].name;
        var $lead = $('\
            <div class="portlet client portlet-lead ' + (lead.accessed_at > 0 ? '' : ' portlet-new') + (lead.source != null ? ' portlet-has-source' : '') + (lead.accessed_at > 0 ? ' portlet-accessed' : '') + ' lead-accepted' + '"' + ' id="user-portlet-' + lead.id + '">\
                <div class="client-header portlet-header">\
                    <span class="avatar">' + '<img src="' + client_avatar + '" /></span>\
                    <h1>' + client_name + '</h1>\
                    <h2>' + (lead.company ? lead.company.name : '') + '</h2>\
                    <!--p>' + lead.clients[0].occupation + '</p-->\
                </div>\
                <div class="client-content portlet-content">\
                    <p class="lead-cost-container">$<span class="lead-cost">' + numberWithCommas(lead.value) + '</span></p>\
                    <p class="lead-task-expiration-date-container"><span class="lead-task-expiration-date date">' + (lead.task_due_at ? '<i class="fa fa-clock-o"></i> ' + getDateString(lead.task_due_at).replace(', ' + (new Date()).getFullYear(), '') : '') + '</span></p>\
                    <div class="verify-pane' + (lead.clients[0].is_verified ? ' verified' : '') + (lead.clients[0].email ? ' email-verified' : '') + (lead.clients[0].phone ? ' phone-verified' : '') + '">\
                        <div class="verify-icons">\
                            <span class="verify-icon verify-icon-email"><i class="fa fa-envelope"></i></span>\
                            <span class="verify-icon verify-icon-phone"><i class="fa fa-phone"></i></span>\
                        </div>\
                        <div class="verify-author">Added by Cliently</div>\
                    </div>\
                </div>\
                <a class="social-cliently-btn">' + (lead.source != null ? LeadSourceTypeIcons[lead.source._type] : '<img src="/images/logo.png" />') + '</a>\
                <a href="javascript:void(0)" class="btn-client-delete"><i class="fa fa-times"></i></a>\
                <span class="new-twitter-count">' + (lead.new_events_count ? lead.new_events_count : '') + '</span>\
            </div>\
        ').data('lead', lead).attr('data-lead-id', lead.id);
        lead.$element = $lead;

        if (!lead.new_events_count || lead.new_events_count <= 0) {
            $lead.find('.new-twitter-count').hide();
        }

        $lead.find('.portlet-header .avatar img').error(function () {
            this.onerror = null;
            this.src = '/images/profile-blank.png';
        });

        return $lead;
    }
    scope.$insertClient = function (client) {
        var $stage = $stageColumns.find('>.column.stage-column:first-child .stage');
        $stage.find('.stage-content').scrollTop(0);
        return $createClient(client).prependTo($stage.find('.clients'));/*.effect("highlight", {color: "#99ff99"}, 3000)*/
    };


    function verifyLead($lead, activate_deal_workflow, deal) {

        if (deal == null) {
            var lead = $lead.data('lead');
            var client_id = lead.clients[0].id;
            var deal_id = lead.id;
        } else {
            var client_id = deal.clients[0].id;
            var deal_id = deal.id;
        }

        $.ajax({
            url: '/api/v1/clients/' + client_id + '/verify',
            method: 'POST'
        }).done(function(e) {
            if (deal == null) {
                $.each(e, function (field, value) {
                    if (field === 'is_verified' || value !== false) {
                        lead.clients[0][field] = value;
                    }
                });
                var client_name = lead.clients[0].name.length > 25 ? lead.clients[0].name.substr(0, 25) : lead.clients[0].name;
                $lead.find('.portlet-header h1').html(client_name);
                $lead.find('.verify-pane').addClass((e.is_verified ? 'verified' : '') + (e.email ? ' email-verified' : '') + (e.phone ? ' phone-verified' : ''));
            }

            refreshAvailableDealCount();

            if (activate_deal_workflow) {
                Workflow.setInitialDealWorkflowIsEnabled(deal_id, 'parent');
            }
        }).fail(function(jqXHR) {
            if (jqXHR.status === 402) {
                showErrorMessage('Error', 'Please purchase additional credits to accept more leads.');
            } else if (jqXHR.status === 410) {
                showErrorMessage('Error', 'Email information is no longer valid. Lead has been deleted and no credits have been charged.');
                if (deal == null) {
                    setTimeout(function () {
                        $lead.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                            $(this).remove();
                        });
                    }, 100);
                }
            }
        });

        if (deal == null) {
            startLeadVerificationTimer($lead);
        }
    }
    scope.verifyLead = verifyLead;

    function startLeadVerificationTimer($lead) {
        var lead = $lead.data('lead');
        var $progressBarContainer = $('\
        <div class="verification-progress-bar-container">\
            <div class="verification-progress-bar-container-inner">\
                <div class="verification-progress-bar" data-progress="0">\
                    <div class="verification-progress-bar-inner">\
                        <img src="' + (lead.clients[0].source.avatar ? (lead.source._type == LeadSourceTypes.TWITTER_TWEET || lead.source._type == LeadSourceTypes.TWITTER_USER ? '//' : '') + lead.clients[0].source.avatar.replace(/normal/, '200x200') : '/images/profile-blank.png') + '" />\
                        <div class="verification-progress-container">\
                            <div class="verification-progress">10%</div>\
                        </div>\
                    </div>\
                </div>\
            </div>\
        </div>\
    ').hide().appendTo($lead).fadeIn();
        $progressBarContainer.find('.verification-progress-bar-inner img').error(function () {
            this.onerror = null;
            this.src = '/images/profile-blank.png';
        });
        countLeadVerificationTimer($progressBarContainer);
    }

    function countLeadVerificationTimer($progressBarContainer) {
        var $progressBar = $progressBarContainer.find('.verification-progress-bar');
        var progress = parseInt($progressBar.attr('data-progress')) + 1;
        if (progress < 50) {
            $progressBar.css('background-image', 'linear-gradient(90deg, #fff 50%, transparent 50%, transparent), linear-gradient(' + (90 + 360 / 100 * progress) + 'deg, #00aaf0 50%, #fff 50%, #fff)');
        } else {
            $progressBar.css('background-image', 'linear-gradient(' + (-90 + 360 / 100 * (progress - 50)) + 'deg, #00aaf0 50%, transparent 50%, transparent), linear-gradient(270deg, #00aaf0 50%, #fff 50%, #fff)');
        }
        $progressBar.attr('data-progress', progress).find('.verification-progress').html(progress + '%');
        if (progress >= 100) {
            $progressBarContainer.fadeOut(function () {
                $(this).remove();
            });
        } else {
            setTimeout(function () {
                countLeadVerificationTimer($progressBarContainer);
            }, 20);
        }
    }

    function resizeClients() {
        $stageColumns.find('.clients').each(function () {
            $(this).css('width', (parseFloat($(this).closest('.stage').width()) - 2) + 'px');
        });
    }
    function addClient(name, $stage) {
        $.post('/api/v1/stages/' + $stage.attr('data-stage-id') + '/deals', {
            client: {
                name: name
            }
        }, function (data) {
            var ts = (new Date()).getTime() / 1000;
            var client = {
                id: data.id,
                clients: [
                    {
                        name: name,
                        occupation: ''
                    }
                ],
                value: 0,
                created_at: ts,
                accessed_at: ts,
                // source_type: null
            };
            $createClient(client).prependTo($stage.find('.clients')).click();
            updateStageCount($stage);
            updateStageValue($stage);
        });
    }
    function deleteClient($client) {
        return $.ajax({
            type: 'PUT',
            data: {is_enabled: false},
            url: '/api/v1/deals/' + $client.data('lead').id,
            success: function () {
                $client.fadeOut(function () {
                    $(this).remove();
                    updateStageCount($client.closest('.stage'));
                    updateStageValue($client.closest('.stage'));
                });
                showSuccessMessage("Success", "A lead has been deleted successfully.");

                if (typeof analytics !== 'undefined' && analytics !== null) {
                    analytics.track('Lead Deleted', {
                        // leadsource_twitterUsername: $("#qtip-0 iframe").contents().find(".TweetAuthor-screenName").text(),
                        // leadsource: $("#qtip-0 iframe").contents().find(".Tweet-text").text()
                    });
                }
                return false;
            }
        });
    }
    // </editor-fold>

    // <editor-fold desc="Popover">
    function loadPopover() {
        var popHTML = '\
            <div class="lead-source" data-source-type="' + LeadSourceTypes.TWITTER_TWEET + '">\
                <div class="twitter-info-container">\
                    <div class="twitter-info"></div>\
                </div>\
            </div>\
            <div class="lead-source" data-source-type="' + LeadSourceTypes.TWITTER_USER + '">\
            </div>\
            <div class="lead-source" data-source-type="' + LeadSourceTypes.DBPERSON + '">\
            </div>\
        ';
        qt = $("#page-clients .main-block").qtip({
            id: 'lead-source-popover',
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
                viewport: $(".main-block"),
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
                    $(".has-popup").removeClass('has-popup');
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
    }
    function movePopover($ele) {
        if ($ele.closest(".portlet.portlet-has-source .avatar").length <= 0) {
            if (qt && $(".has-popup").length > 0 && !qtTimer) {
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
        var $card = $ele.closest(".portlet");
        if ($card.hasClass("has-popup")) {
            return;
        }

        if (!qt) {
            loadPopover();
        }

        qt.qtip('option', 'position.target', $ele[0]);
        qt.qtip('option', 'show.target', $ele[0]);
        qt.qtip('enable');
        $(".has-popup").removeClass('has-popup');
        $card.addClass('has-popup');

        var lead = $card.data('lead');
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
                        .find("h4.fullname").html(lead.source.fullname).end()
                        .find("span.username").html(lead.source.username).end()
                        .find(".twitter-item-description").html(lead.source.description).end()
                        .find("span.location").html('<i class="fa fa-map-marker"></i> ' + lead.source.location + '');
                break;
            case LeadSourceTypes.DBPERSON:
                qt.qtip('option', 'content.title', '<span class="popover-title-icon"><img src="/images/company-gray.png" /></span> Lead Source - Database');
                var $metricsInfo = $popoverSource.find('.metrics-info');
                if (!$metricsInfo.length) {
                    $metricsInfo = $('<div class="metrics-info" />').appendTo($popoverSource);
                }
                $metricsInfo.empty();
                if (lead.source.company) {
                    if (typeof lead.source.company.employee_count != null) {
                        $metricsInfo.append('<span><i class="fa fa-users"></i> ' + lead.source.company.employee_count + '</span>');
                    }
                    if (typeof lead.source.company.revenue != null) {
                        $metricsInfo.append('<span><i class="fa fa-bank"></i> ' + lead.source.company.revenue + '</span>');
                    }
                    if (typeof lead.source.company.industries != null) {
                        $metricsInfo.append('<span><i class="fa fa-suitcase"></i> ' + (lead.source.company.industries ? lead.source.company.industries.join(', ') : '') + '</span>');
                    }
                    if (typeof lead.source.company.location != null) {
                        $metricsInfo.append('<span><i class="fa fa-map-marker"></i> ' + lead.source.company.location + '</span>');
                    }
                }
                break;
        }

        qt.qtip('api').reposition(null, false);
        qt.qtip('show');
    }
    // </editor-fold>

    function createLeadCardPopup() {
        $cardPopup = $('\
            <div id="lead-card-container" class="modal fade" role="dialog">\
                <div class="modal-dialog modal-lg">\
                    <div class="modal-content">\
                        <div class="modal-body"></div>\
                    </div>\
                </div>\
            </div>\
        ').hide().appendTo('body');
        $card = $('\
            <div class="lead-card">\
                <div class="lead-card-inner">\
                    <div class="lead-info"></div>\
                    <div class="lead-actions"></div>\
                </div>\
            </div>\
        ').appendTo($cardPopup.find('.modal-body'));
        $card.find('>.lead-card-inner').slimScroll({
            height: '100%'
        });

        var $leadInfo = $card.find('.lead-info');
        $personInfo = $('\
            <div class="person-info">\
                <div class="row">\
                    <div class="col-md-6">\
                        <div class="contact-info"></div>\
                    </div>\
                    <div class="col-md-6">\
                        <div class="company-info"></div>\
                    </div>\
                </div>\
            </div>\
        ').appendTo($leadInfo);

        $('\
            <div class="shortcuts">\
                <a class="shortcut" href="javascript:void(0);" data-command="expand"><span class="fa fa-expand"></span></a>\
                <a class="shortcut" href="javascript:void(0);" data-command="close"><i class="fa fa-times"></i></a>\
            </div>\
        ').appendTo($personInfo);

        $manageInfo = $('\
            <div class="manage-info">\
                <div class="row">\
                    <div class="col-md-6">\
                        <div class="manage-pipeline-info">\
                            <h3>Pipeline</h3>\
                            <h4 data-id="' + current_pipeline_id + '">' + pipelines[current_pipeline_index].name + '</h4>\
                            <div class="dropdown" data-sort="">\
                                <a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></a>\
                                <ul class="dropdown-menu pull-left">\
                                </ul>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="col-md-6">\
                        <div class="manage-owner-info">\
                            <h3>Lead Owner</h3>\
                            <h4></h4>\
                            <div class="dropdown" data-sort="">\
                                <a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></a>\
                                <ul class="dropdown-menu pull-left">\
                                </ul>\
                            </div>\
                        </div>\
                    </div>\
                </div>\
            </div>\
        ').appendTo($leadInfo);

        $stageInfo = $('\
            <div class="stage-info">\
                <h3>Stage</h3>\
                <span class="stage-info-reminder hide">Please choose your stage for the new Pipeline to save</span>\
                <span class="stage-info-empty hide">Pipeline must have at least one stage before you can add your lead card</span>\
                <ul class="stages">\
                    <li class="stage completed"><span class="point"></span></li>\
                    <li class="stage completed"><span class="point"></span></li>\
                    <li class="stage completed"><span class="point"></span></li>\
                    <li class="stage"><span class="point"></span></li>\
                    <li class="stage"><span class="point"></span></li>\
                </ul>\
            </div>\
        ').appendTo($leadInfo);

        $sourceInfo = $('\
            <div class="source-info">\
                <div class="source">\
                    <div class="source-inner">\
                        <div class="source-values" data-name="source_description">Hong Kong, 1000 miles, "grand opening"</div>\
                        <div class="source-created-date">created on Apr 14, 2016</div>\
                        <div class="tweets-wrapper">\
                            <div class="tweets-entries"></div>\
                        </div>\
                        <div class="twitter-bios-info"></div>\
                        <div class="dbperson-values-wrapper"></div>\
                    </div>\
                </div>\
                <a href="javascript:void(0)" class="source-toggle">\
                    Lead Source <span class="source-icon"><i class="fa fa-cliently-twitter"></i></span>\
                </a>\
            </div>\
        ').appendTo($leadInfo);
        $source = $sourceInfo.find('.source');

        $leadActions = $card.find('.lead-actions').append('\
            <ul class="nav nav-tabs" role="tablist">\
                <li role="presentation">\
                    <a data-toggle="tab" href="#lead-action-tab-pane-task" role="tab"><i class="fa fa-check-square-o"></i> Task</a>\
                </li>\
                <li role="presentation">\
                    <a data-toggle="tab" href="#lead-action-tab-pane-note" role="tab"><i class="fa fa-file-text-o"></i> Note\
                    </a>\
                </li>\
                <li role="presentation">\
                    <a data-toggle="tab" href="#lead-action-tab-pane-mail" role="tab"><i class="fa fa-envelope-o"></i> Email\
                    </a>\
                </li>\
                <li role="presentation">\
                    <a data-toggle="tab" href="#lead-action-tab-pane-twitter" role="tab"><i class="fa fa-cliently-twitter"></i> Twitter\
                        <span class="count">2</span>\
                    </a>\
                </li>\
            </ul>\
            <div class="tab-content">\
                <div class="tab-pane fade" id="lead-action-tab-pane-task" role="tabpanel"></div>\
                <div class="tab-pane fade" id="lead-action-tab-pane-note" role="tabpanel"></div>\
                <div class="tab-pane fade" id="lead-action-tab-pane-mail" role="tabpanel"></div>\
                <div class="tab-pane fade disabled" id="lead-action-tab-pane-twitter" role="tabpanel">\
                    <div class="hover-lock"></div>\
                </div>\
            </div>\
        ');

        Sidebar.load();

        $cardPopup.modal({
            show: false,
            keyboard: false
        });
    }

    function popupCard() {
        if (!$cardPopup) {
            createLeadCardPopup();
        }

        $('#lead-card-container.modal .sidebar>ul.nav.nav-tabs>li.active').removeClass('active');
        $cardPopup.removeClass('sidebar-expanded').modal('show');

        ShowCard();
    }

    function ShowCard() {
        leadAccepted = true;
        if (leadAccepted) {
            $card.addClass('lead-accepted');
        } else {
            $card.removeClass('lead-accepted');
        }

        fillSource();

        if (!leadAccepted) {
            $personInfo.hide();
            $manageInfo.hide();
            $stageInfo.hide();
            $leadActions.hide();
            $source.show();
            return;
        }

        $personInfo.show();
        $manageInfo.show();
        $stageInfo.show();
        $leadActions.show();
        $source.hide();

        fillCard(true);
    }

    function fillSource() {
        $sourceInfo.find(">.source-toggle>.source-icon").html((lead.source != null ? LeadSourceTypeIcons[lead.source._type].replace('company-gray', 'company') : '<img src="/images/logo.png" />'));

        $source.find(".editable").editable('destroy');
        $source.find('>.source-inner>.source-created-date').html(getDateString(lead.created_at));
        $source.find('.tweets-wrapper').hide();
        $source.find('.twitter-bios-info').hide();
        $source.find('.dbperson-values-wrapper').hide();
        if (lead.source == null) {
            $source.find('>.source-inner>.source-values').html(lead.source_description).editable({
                emptytext: 'Enter lead source',
                pk: lead.id,
                validate: function (value) {
                    if ($.trim(value) == '')
                        return 'Enter lead source.';
                },
                mode: 'inline',
                url: getApiUrl(),
                success: function (response, newValue) {
                    lead[$(this).attr('data-name')] = newValue;
                }
            });
        } else if (lead.source._type == LeadSourceTypes.TWITTER_TWEET) {
            var action_values_string = '';
            if (lead.action_values.location != null) action_values_string = lead.action_values.location + ', ';
            if (lead.action_values.range != null) action_values_string += lead.action_values.range + ' miles, ';
            if (lead.action_values.keywords != null && lead.action_values.keywords[0] != null) action_values_string += lead.action_values.keywords[0];
            else lead.action_values.keywords = [];

            $source.find('>.source-inner>.source-values').html(action_values_string);
            var $tweets = $source.find('.tweets-wrapper>.tweets-entries');
            $tweets.find('>.tweet-entry').hide();
            var $tweet = $tweets.find('>.tweet[data-source-id="' + lead.source.code + '"]');
            if (!$tweet.length) {
                if (twttr && twttr.widgets) {
                    var action_values = lead.action_values;
                    $tweet = $('<div class="tweet-entry" data-source-id="' + lead.source.code + '">').appendTo($tweets);
                    twttr.widgets.createTweet(lead.source.code, $tweet[0]).then(function (el) {
                        var $ttifrm = $tweet.find('>iframe');
                        $ttifrm.css('width', '100%');
                        if (lead.client_avatar == 0) {
                            $personInfo.find('.contact-info .contact-avatar img').attr('src', $ttifrm.contents().find('img.Avatar').attr('src').replace(/normal/, '200x200'));
                        }
                        var head = $ttifrm.contents().find('head');
                        if (head.length) {
                            head.append('<style>.EmbeddedTweet { max-width: 99% !important; width: 99% !important; }/style>');
                        }
                        decorateTweet($ttifrm, action_values.keywords, leadAccepted);
                    });
                }
            }
            $tweet.show();
            $source.find('.tweets-wrapper').show();
        } else if (lead.source._type == LeadSourceTypes.TWITTER_USER) {
            $source.find('>.source-inner>.source-values').html(lead.action_values.keywords[0]);
            var $twitterItem = $source.find('.twitter-bios-info>.twitter-item');
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
                ').appendTo($source.find('.twitter-bios-info'));
                $twitterItem.find('img').error(function () {
                    this.onerror = null;
                    this.src = '/images/profile-blank.png';
                });
            }
            $twitterItem
                    .find("img.twitter-item-profile-image").attr('src', '//' + lead.source.avatar).end()
                    .find("h4.fullname").html(lead.source.fullname).end()
                    .find("span.username").html(lead.source.username).end()
                    .find(".twitter-item-description").html(lead.source.description).end()
                    .find("span.location").html('<i class="fa fa-map-marker"></i> ' + lead.source.location + '');
            $source.find('.twitter-bios-info').show();
        } else if (lead.source._type == LeadSourceTypes.DBPERSON) {
            $source.find('>.source-inner>.source-values').html('Company Database');

            var values = lead.action_values;
            var $filterValues = $source.find('.dbperson-values-wrapper>.filter-values');
            if (!$filterValues.length) {
                $filterValues = $('<div class="filter-values" />').appendTo($source.find('.dbperson-values-wrapper'));
            } else {
                $filterValues.empty();
            }
            var $row = $('<div class="row" />').appendTo($filterValues);
            var $colt = $('\
                <div class="col-md-2">\
                    <label></label>\
                </div>\
            ');

            function fetchDbpersonValue($valueContainer, value, title) {
                $valueContainer.addClass('dbperson-value-container').find('>label').html(title);
                var $value = $('<ul />').addClass('dbperson-value').appendTo($valueContainer);

                $value.html('<li><label>' + value + '</label></li>');
            }
            function fetchDbpersonListValues($valuesContainer, values, title) {
                $valuesContainer.addClass('dbperson-values-container').find('>label').html(title);
                var $values = $('<ul />').addClass('dbperson-values').appendTo($valuesContainer);

                $.each(values, function (i, value) {
                    $('<li />').html('<label>' + value + '</label>').appendTo($values);
                });
            }

            if (values.company_names && values.company_names.length > 0) {
                fetchDbpersonValue($colt.clone().appendTo($row), values.company_names, 'Company Name');
            }
            if (values.person_title && values.person_title.length > 0) {
                fetchDbpersonValue($colt.clone().appendTo($row), values.person_title, 'Person Title');
            }
            if (values.employee_sizes && values.employee_sizes.length > 0) {
                var employee_sizes = Dbperson.getDbpersonNamesByValues('employee_sizes', values.employee_sizes);
                fetchDbpersonListValues($colt.clone().appendTo($row), employee_sizes, 'Size');
            }
            if (values.revenues && values.revenues.length > 0) {
                var revenues = Dbperson.getDbpersonNamesByValues('revenues', values.revenues);
                fetchDbpersonListValues($colt.clone().appendTo($row), revenues, 'Revenue');
            }
            if (values.title_roles && values.title_roles.length > 0) {
                var roles = Dbperson.getDbpersonNamesByValues('title_roles', values.title_roles);
                fetchDbpersonListValues($colt.clone().appendTo($row), roles, 'Role');
            }
            if (values.title_seniorities && values.title_seniorities.length > 0) {
                var seniorities = Dbperson.getDbpersonNamesByValues('title_seniorities', values.title_seniorities);
                fetchDbpersonListValues($colt.clone().appendTo($row), seniorities, 'Seniority');
            }
            if (values.industries && values.industries.length > 0) {
                var industries = Dbperson.getDbpersonNamesByValues('industries', values.industries);
                fetchDbpersonListValues($colt.clone().appendTo($row), industries, 'Industry');
            }

            var locations = [];
            if (values.countries && values.countries.length > 0) {
                locations = locations.concat(locations, Dbperson.getDbpersonNamesByValues('locations', values.countries));
            }
            if (values.states && values.states.length > 0) {
                locations = locations.concat(locations, Dbperson.getDbpersonNamesByValues('locations', values.states));
            }
            if (values.metro_regions && values.metro_regions.length > 0) {
                locations = locations.concat(locations, Dbperson.getDbpersonNamesByValues('locations', values.metro_regions));
            }
            if (locations && locations.length > 0) {
                fetchDbpersonListValues($colt.clone().appendTo($row), locations, 'Location');
            }

            if (values.location) {
                fetchDbpersonValue($colt.clone().appendTo($row), values.location, 'City/Sate/ZIP/Country');
            }
            if (values.location_match_type) {
                fetchDbpersonValue($colt.clone().appendTo($row), values.location_match_type, 'Mach Type');
            }
            if (typeof values.daily_limit != 'undefined' && values.daily_limit !== null && values.daily_limit != '') {
                fetchDbpersonValue($colt.clone().appendTo($row), values.daily_limit, 'Daily Limit');
            }
            if (values.sorting) {
                fetchDbpersonValue($colt.clone().appendTo($row), values.sorting, 'Sorting');
            }

            $source.find('.dbperson-values-wrapper').show();
        }
    }

    function fillCard(onShow) {
        console.log('fillCard');
        //Client Info
        fillClient();
        
        //Company Info
        fillCompany();

        if (onShow) {
            if (lead.stage != null && lead.stage.selected_pipeline_id != null) {
                lead.stage.selected_pipeline_id = null;
            }
            console.log('reset ? ');
            resetTaskActionTab();
            resetNoteActionTab();
            resetMailActionTab();
            resetTwitterActionTab();
        }

        $card.find('.lead-actions>ul.nav.nav-tabs>li>a[href="#lead-action-tab-pane-twitter"]').parent().removeClass('active');
        $card.find('.lead-actions>.tab-content>.tab-pane#lead-action-tab-pane-twitter').removeClass('active in');
        if (!lead.is_full) {
            console.log('full');
            loadFullLead();
        } else {
            console.log('not full');
            fillManage(true);
            fillStage(true);
            fillTasks(); //fill tasks
            fillNotes(); //fill notes
            fillMails(); //fill mails
            if (lead.clients[0].integrations && lead.clients[0].integrations.twitter) {
                $card.find('.lead-actions>ul.nav.nav-tabs>li>a[href="#lead-action-tab-pane-twitter"]').tab('show').parent();
            } else {
                $card.find('.lead-actions>ul.nav.nav-tabs>li:first-child>a').tab('show');
            }

            fillTwitters(); //fill twitters

            if (tour_step >= TourSteps.CLIENT_DETAILS && tour_step < TourSteps.LEFT_MENU) {
                openTour();
            }
        }

    }

    function fillClient() {
        console.log('fillClient');
        var $contactInfo = $personInfo.find('.contact-info');
        if ($contactInfo.is(':empty')) {
            $contactInfo.append('\
                <h1>Contact</h1>\
                <div class="row">\
                    <div class="col-xs-4">\
                        <span class="contact-avatar">\
                            <img alt="contact" class="img-responsive" src="/images/profile-blank.png">\
                        </span>\
                        <span class="social-iconlist dropdown">\
                            <a href="javascript:void(0)" class="social-add-form-dropdown-toggle dropdown-toggle" data-toggle="dropdown"><i class="fa fa-plus"></i></a>\
                            <form class="dropdown-menu form form-horizontal social-add-form">\
                                <div class="form-body">\
                                    <div class="form-group">\
                                        <label class="col-md-2 control-label"><i class="fa fa-cliently-facebook"></i></label>\
                                        <div class="col-md-10"><input type="text" name="facebook" value="" class="form-control" /></div>\
                                    </div>\
                                    <div class="form-group">\
                                        <label class="col-md-2 control-label"><i class="fa fa-cliently-twitter"></i></label>\
                                        <div class="col-md-10"><input type="text" name="twitter" value="" class="form-control" /></div>\
                                    </div>\
                                    <div class="form-group">\
                                        <label class="col-md-2 control-label"><i class="fa fa-cliently-linkedin"></i></label>\
                                        <div class="col-md-10"><input type="text" name="linkedin" value="" class="form-control" /></div>\
                                    </div>\
                                </div>\
                                <div class="form-actions">\
                                    <div class="row">\
                                        <div class="col-md-offset-3 col-md-9 text-right">\
                                            <button type="submit" class="btn btn-primary btn-save">Save</button>\
                                        </div>\
                                    </div>\
                                </div>\
                            </form>\
                        </span>\
                    </div>\
                    <div class="col-xs-8">\
                        <span class="contact-list-container">\
                            <a href="#" class="contact-list-dropdown-toggle"><i class="fa fa-angle-down fa-2x"></i></a>\
                        </span>\
                        <h2 data-name="name" class="contact-name">Season Moore</h2>\
                        <div class="contact-details">\
                            <span data-name="occupation" data-emptytext="+ Add Job Title" class="contact-job">+ Add Job Title</span>\
                            <span data-name="phone" data-emptytext="+ Add Phone Number" class="contact-phone">+ Add Phone Number</span>\
                            <span data-name="email" data-emptytext="+ Add Email" class="contact-email">+ Add Email</span>\
                            <span data-name="location" data-emptytext="+ Add Location" class="contact-location">+ Add Location</span>\
                        </div>\
                    </div>\
                </div>\
            ');
            contactAvatarUploader = new ss.SimpleUpload({
                button: $contactInfo.find('.contact-avatar'), // file upload button
            });
            $contactInfo.find('.contact-avatar img').error(function () {
                this.onerror = null;
                this.src = '/images/profile-blank.png';
            });

            $contactInfo.find('.social-add-form').submit(function () {
                var $btn = $(this).find('.btn-save');
                var html = $btn.html();
                $btn.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Saving...');
                var social = $(this).serializeObject();
                $.ajax({
                    url: '/api/v1/clients/' + $personInfo.find('.contact-info').attr('data-client-id'),
                    method: 'PUT',
                    data: {social: social},
                    success: function () {
                        lead.clients[0].social = social;
                        fillSocial();
                        $leadActions.find('>.tab-content>.tab-pane>.loaded').removeClass('loaded');
                        loadFullLead().done(function () {
                            $btn.attr('disabled', false).html(html).closest('.dropdown').removeClass('open');
                        });
                    }
                }).fail(function () {
                    $btn.attr('disabled', false).html(html);
                });
                return false;
            });

            $contactInfo.find('.contact-list-dropdown-toggle').click(function (e) {
                showContactListDropdown();
                return e.preventDefault();
            });

            $contactInfo.find('[data-name]').each(function () {
                if ($(this).attr('data-name') == 'location') {
                    $(this).editable({
                        type: 'address',
                        placement: 'bottom',
                        params: function (params) {
                            var data = {};
                            $.each(params.value, function (name, value) {
                                data[name] = value;
                            });
                            return data;
                        },
                        success: function (response, newValue) {
                            $.each(newValue, function (name, value) {
                                lead.clients[0][name] = value;
                            });
                        }
                    });
                } else {
                    $(this).editable({
                        success: function (response, newValue) {
                            var name = $(this).attr('data-name');
                            lead.clients[0][name] = newValue;
                            switch (name) {
                                case 'name':
                                    lead.$element.find('.portlet-header>h1').html(newValue);
                                case 'email':
                                    $leadActions.find('.mail-action form.mail-action-form input.mail-to[readonly]').val(newValue);
                            }
                        }
                    });
                }
            });
        }

        var client = lead.clients[0];
        $contactInfo.attr('data-client-id', client.id);
        if (client.avatar) {
            $contactInfo.find('.contact-avatar img').attr('src', '/uploads/clients/avatars/' + client.avatar + '_200x200.jpg');
        } else if (!lead.source || !lead.source._type) {
            $contactInfo.find('.contact-avatar img').attr("src", '/images/profile-blank.png');
        } else if (lead.source._type == LeadSourceTypes.TWITTER_TWEET || lead.source._type == LeadSourceTypes.TWITTER_USER) {
            if (client.integrations && client.integrations.twitter && client.integrations.twitter.avatar) {
                $contactInfo.find('.contact-avatar img').attr('src', client.integrations.twitter.avatar.replace(/normal/, '200x200'));
            } else {
                $contactInfo.find('.contact-avatar img').attr("src", '/images/profile-blank.png');
            }
        } else if (lead.source._type == LeadSourceTypes.DBPERSON) {
            if (client.source && client.source.avatar) {
                $contactInfo.find('.contact-avatar img').attr('src', client.source.avatar);
            } else {
                $contactInfo.find('.contact-avatar img').attr("src", '/images/profile-blank.png');
            }
        }

        contactAvatarUploader.disable();
        if (lead.is_full) {
            contactAvatarUploader.enable();
            contactAvatarUploader.setOptions({
                onComplete: function (filename, response) {
                    if (!response) {
                        showErrorMessage("Error!!!", filename + " file upload failed.");
                        return false;
                    }

                    if (response.success === true) {
                        showSuccessMessage("Success", "The client avatar has been uploaded successfully.");
                        client.avatar = response.id;
                        $contactInfo.find('.contact-avatar img').attr('src', '/uploads/clients/avatars/' + client.avatar + '_200x200.jpg'); //.effect("highlight");
                        $.ajax({
                            url: '/api/v1/clients/' + client.id,
                            method: 'PUT',
                            data: {avatar: client.avatar}
                        });
                    } else {
                        if (response.msg) {
                            showErrorMessage("Error!!!", response.msg);
                        } else {
                            showErrorMessage("Error!!!", "Unable to upload file");
                        }
                    }
                }
            });
        }

        fillSocial();

        $contactInfo
                .find('[data-name="name"]').editable('setValue', client.name).end()
                .find('[data-name="occupation"]').editable('setValue', client.occupation).end()
                .find('[data-name="phone"]').editable('setValue', client.phone).end()
                .find('[data-name="email"]').editable('setValue', client.email).end()
                .find('[data-name="location"]').editable('setValue', client).end()

                .find('[data-name]')
                .editable('option', 'pk', client.id)
                .editable('option', 'url', '/api/v1/clients/' + client.id)
                .editable('option', 'disabled', !lead.is_full);

        $contactInfo.find('[data-name="email"]').editable('option', 'error', function(response, new_value) {
            if(response.status === 409) {
                showErrorMessage('Error', 'Client with such email already exists.');
            }
        });

    }

    function fillSocial() {
        var $socialIconList = $personInfo.find('.contact-info .social-iconlist');
        $socialIconList.find('.cliently-socialicon').remove();
        if (lead.is_full) {
            var socials = {
                facebook: lead.clients[0].social.facebook,
                twitter: lead.clients[0].social.twitter,
                linkedin: lead.clients[0].social.linkedin,
            }
            $.each(socials, function (social, handle) {
                if (handle) {
                    $('<a class="cliently-socialicon" data-social="' + social + '" href="https://' + social + '.com/' + (social == 'linkedin' ? 'in/' : '') + handle + '" target="_blank"><i class="fa fa-cliently-' + social + '"></i></a>').appendTo($socialIconList);
                }
                $socialIconList.find('input[name="' + social + '"]').val(handle);
            });
        }
    }

    function showContactListDropdown() {
        var $dropdown = $personInfo.find('.contact-info .contact-list-container>.contact-list-dropdown');
        if (!$dropdown.length) {
            $dropdown = $('<div class="contact-list-dropdown dropdown-menu">\
                            <ul class="contact-list"></ul>\
                        </div>').appendTo($personInfo.find('.contact-info .contact-list-container'));
            $dropdown.find(".contact-list").on('click', '>li>a', function () {
//                    var wId = $(this).parent().attr('data-workflow-id');
//                    if (lead.workflow_id == wId) {
//                        return false;
//                    }
//                    $.ajax({
//                        url: "/api/v1/deals/" + leadId,
//                        method: 'put',
//                        data: {workflow_id: wId},
//                        success: function () {
//                            lead.workflow_id = wId;
//                            loadWorkflow();
//                        }
//                    });
            });
        }

        var $list = $dropdown.hide().find(".contact-list").empty().append('<li class="contact-list-item loading">\
                    <div class="contact-list-item-inner">\
                        <a><i class="fa fa-spin fa-spinner"></i> Searching ...</a>\
                    </div>\
                </li>');
//            Contacts.loadContacts(true).done(function (data) {
        $list.empty();
//                if (!data.length && DevOptions.showSampleData) {
        var data = [
            {id: 1, name: 'contact123'},
            {id: 2, name: 'contact333'},
            {id: 3, name: 'contact444'},
            {id: 4, name: 'contact555'},
            {id: 5, name: 'contact777'},
            {id: 6, name: 'contact999'}
        ];
//                }
        if (data && data.length) {
            $.each(data, function (index, item) {
                $('<li class="contact-list-item ' + (item.id == lead.contact_id ? 'selected' : '') + '">\
                    <a ' + (item.id == lead.contact_id ? 'disabled="disabled"' : '') + '><i class="fa fa-check"></i> ' + item.name + '</a>\
                </li>').attr('data-contact-id', item.id)/*.hide()*/.appendTo($list);//.fadeIn({duration: 500, queue: false});//.hide().slideDown(500);
                ;
            });
        } else {
            $list.append('<li class="contact-list-item loading">\
                            <a><i class="fa fa-times"></i> No matches found.</a>\
                        </li>');
        }
//            });

        $dropdown.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
        $(document).off('click.lead.contact.list.toggle');
        setTimeout(function () {
            $(document).on('click.lead.contact.list.toggle', function () {
                $personInfo.find('.contact-info .contact-list-container>.contact-list-dropdown').fadeOut('fast');
                $(document).off('click.lead.contact.list.toggle');
            });
        });
    }


    function fillCompany() {
        console.log('fillCompany');
        var $companyInfo = $personInfo.find('.company-info');
        var $companyDetails = $companyInfo.find('.company-details');

        var value_field = $companyInfo.find('[data-name="value"]');
        if (typeof value_field.editable === 'function') {
            value_field.editable('destroy');
        }
        value_field.editable({
            pk: leadId,
            url: getApiUrl(),
            success: function (response, newValue) {
                lead.value = newValue;
            }
        });
        console.log('fillCompany2');
        if ($companyInfo.is(':empty')) {
            console.log('fillCompanyempty');
            $companyInfo.append('\
                <h1 class="add-company">+ Add Company</h1>\
                <div class="company-details">\
                    <h2 class="company-title"><span class="company-logo"><img src="/images/company-gray.png"/></span> <div data-name="name" data-emptytext="Company Name">SalesLoft</div></h2>\
                    <span data-name="location" data-emptytext="Location" class="company-location">Atlanta, GA</span>\
                    <span class="separator">|</span>\
                    <span class="company-founded-year" data-name="foundation_year" data-emptytext="Founded 0">Founded 2011</span>\
                    <span class="separator">|</span>\
                    <span data-name="phone" data-emptytext="(000) 000-0000" class="company-phone">(415) 347-8782</span>\
                    <a data-name="website" data-emptytext="http://company-url.com" class="company-site-url" target="_blank" href="#">www.salesloft.com</a>\
                    <p data-name="description" data-type="textarea" data-placement="bottom" data-emptytext="Please write the company description here." class="company-description">SalesLoft is the simplest way on the internet to email and call prospects in order to set up more qualified appts.</p>\
                </div>\
                <strong class="company-cost">$ <span data-name="value" data-emptytext="0">0</span></strong>\
                <ul class="company-status">\
                    <li class="active">Open</li>\
                    <li>Won</li>\
                    <li>Lost</li>\
                </ul>\
            ');
            $companyInfo.find('h1.add-company').click(function () {
                var $this = $(this);
                if ($this.hasClass('saving loading')) {
                    return;
                }
                var $companyInfo = $personInfo.find('.company-info');
                if ($companyInfo.hasClass('has-company')) {
                    var enable = !$companyInfo.hasClass('show-company');
                    var $xhr = toggleCompany(enable);
                    if ($xhr) {
                        var html = $this.html();
                        $this.html('<i class="fa fa-spin fa-spinner"></i>').addClass('adding');
                        $xhr.done(function () {
                            $this.removeClass('adding');
                            if ($companyInfo.hasClass('show-company')) {
                                $companyInfo.removeClass('show-company').find('>.add-company').html('+ Add Company');
                            } else {
                                $companyInfo.addClass('show-company').find('>.add-company').html('Company');
                            }
                        }).fail(function () {
                            $this.html(html).removeClass('adding');
                        });
                    }
                    return;
                }

                var $xhr = addCompany();
                if ($xhr) {
                    var html = $this.html();
                    $this.html('<i class="fa fa-spin fa-spinner"></i>').addClass('adding');
                    $xhr.done(function () {
                        $this.removeClass('adding');
                    }).fail(function () {
                        $this.html(html).removeClass('adding');
                    });
                }
            });
            companyLogoUploader = new ss.SimpleUpload({
                button: $companyInfo.find('.company-logo'), // file upload button
            });
            $companyInfo.find('.company-logo img').error(function () {
                this.onerror = null;
                this.src = '/images/company-gray.png';
            });
            console.log('fillCompany3');
            $companyDetails = $companyInfo.find('.company-details');
            console.log('fillCompany4');
            $companyDetails.find('[data-name]').each(function () {
                console.log('fillCompany5');
                console.log($(this));
                if ($(this).attr('data-name') == 'location') {
                    $(this).editable({
                        type: 'address',
                        placement: 'bottom',
                        params: function (params) {
                            var data = {};
                            console.log('fillCompanyparams');
                            $.each(params.value, function (name, value) {
                                console.log(value);
                                data[name] = value;
                            });
                            return data;
                        },
                        success: function (response, newValue) {
                            console.log('fillCompany-leads?');
                            $.each(newValue, function (name, value) {
                                console.log(value);
                                lead.company[name] = value;
                            });
                        }
                    });
                } else {
                    $(this).editable({
                        success: function (response, newValue) {
                            var name = $(this).attr('data-name');
                            lead.company[name] = newValue;
                            switch (name) {
                                case 'name':
                                    lead.$element.find('.portlet-header>h2').html(newValue);
                                    break;
                                case 'name':
                                    this.href = newValue;
                                    break;
                            }
                        }
                    });
                }
            });

            $companyDetails.find('[data-name="foundation_year"]').editable('option', 'display', function (value, sourceData) {
                $(this).html(value > 0 ? ('Founded ' + value) : '');
            });
        }

        if (lead.is_full) {
            $companyInfo.removeClass('loading');
        } else {
            $companyInfo.addClass('loading');
        }

        var company = lead.company;
        $companyInfo.attr('data-company-id', company && company.id ? company.id : '');
        companyLogoUploader.disable();

        if (company && company.id) {
            if (company.logo) {
                $companyInfo.find('.company-logo img').attr('src', '/uploads/companies/logos/' + company.logo + '_200x200.jpg');
            } else {
                if (company.source && company.source.logo) {
                    $companyInfo.find('.company-logo img').attr('src', company.source.logo);
                } else {
                    $companyInfo.find('.company-logo img').attr('src', '/images/company-gray.png');
                }
            }
            if (lead.is_full) {
                companyLogoUploader.enable();
                companyLogoUploader.setOptions({
                    onComplete: function (filename, response) {
                        if (!response) {
                            showErrorMessage("Error!!!", filename + " file upload failed.");
                            return false;
                        }

                        if (response.success === true) {
                            showSuccessMessage("Success", "The company logo has been updated successfully.");
                            company.logo = response.id;
                            $companyInfo.find('.company-logo img').attr('src', '/uploads/companies/logos/' + company.logo + '_200x200.jpg'); //.effect("highlight");
                            $.ajax({
                                url: '/api/v1/companies/' + company.id,
                                method: 'PUT',
                                data: {avatar: company.logo}
                            });
                        } else {
                            if (response.msg) {
                                showErrorMessage("Error!!!", response.msg);
                            } else {
                                showErrorMessage("Error!!!", "Unable to upload file");
                            }
                        }
                    }
                });
            }

            $companyInfo.find('[data-name="value"]').editable('setValue', lead.value);

            $companyDetails
                    .find('[data-name="name"]').editable('setValue', company.name).end()
                    .find('[data-name="location"]').editable('setValue', company).end()
                    .find('[data-name="foundation_year"]').editable('setValue', company.foundation_year).end()
                    .find('[data-name="phone"]').editable('setValue', company.phone).end()
                    .find('[data-name="website"]').editable('setValue', company.website).end()
                    .find('[data-name="description"]').editable('setValue', company.description).end()

                    .find('[data-name]')
                    .editable('option', 'pk', company.id)
                    .editable('option', 'url', '/api/v1/companies/' + company.id)
                    .editable('option', 'disabled', !lead.is_full);

            $companyInfo.addClass('has-company');
            if (company.is_enabled) {
                $companyInfo.addClass('show-company');
                $companyInfo.find('>.add-company').html('Company');

            } else {
                $companyInfo.removeClass('show-company');
                $companyInfo.find('>.add-company').html('+ Add Company');
            }
        } else {
            $companyInfo.removeClass('has-company show-company');
            $companyInfo.find('>.add-company').html('+ Add Company');
        }
    }

    function fillManage(refresh) {
        console.log('fillmanage');
        var member_index = 0;
        $.each(workspace_members, function (i, member) {
            console.log(member);
            if (member.user_id === lead.owner_id) {
                member_index = i;
                return;
            }
        });
        $manageInfo.find('.manage-owner-info h4').text(workspace_members[member_index].user.first_name + ' ' + workspace_members[member_index].user.last_name);
        $manageInfo.find('.manage-pipeline-info h4').text(pipelines[current_pipeline_index].name);
    }

    function fillStage(refresh, stages) {
        var __$stages = $stageInfo.find('>ul.stages');
        console.log(stages);
        if (refresh) {
            __$stages.empty();
            var $stages = $('.columns.stage-columns .column.stage-column .stage');
            var width = 100 / ($stages.length > 1 ? ($stages.length - 1) : 1);

            if (stages == null) {
                $stageInfo.find('.stage-info-reminder').addClass('hide');
                if ( ! $stages.length) {
                    $stageInfo.find('.stage-info-empty').removeClass('hide');
                }
                $stages.each(function (i) {
                    var field = $(this).data('stage');
                    var $stage = $('\
                        <li class="stage" data-stage-id="' + field.id + '">\
                            <span class="point"></span>\
                            <span class="title">' + field.name + '</span>\
                        </li>\
                    ').appendTo(__$stages);
                    if (i > 0) {
                        $stage.css('width', width + '%');
                    }
                });
            } else {
                if (stages.length) {
                    $stageInfo.find('.stage-info-reminder').removeClass('hide');
                    $stageInfo.find('.stage-info-empty').addClass('hide');
                } else {
                    $stageInfo.find('.stage-info-reminder').addClass('hide');
                    $stageInfo.find('.stage-info-empty').removeClass('hide');
                }
                $.each(stages, function (i, field) {
                    var $stage = $('\
                        <li class="stage" data-stage-id="' + field.id + '">\
                            <span class="point"></span>\
                            <span class="title">' + field.name + '</span>\
                        </li>\
                    ').appendTo(__$stages);
                    if (i > 0) {
                        $stage.css('width', width + '%');
                    }
                });
            }
        }

        var selected_stage = __$stages.find('>.stage[data-stage-id="' + lead.stage_id + '"]');
        if (selected_stage.length) {
            selected_stage.prevAll().addClass('completed')
            selected_stage.addClass('completed')
            selected_stage.nextAll().removeClass('completed');
        }
    }

    function loadFullLead() {
        return $.get(getApiUrl() + '?include_main_client=1&include_all_clients=1&include_source=1&include_client_source=1&include_twitter=1&include_company=1&include_tasks=1&include_notes=1&include_mails=1&include_integrations=1&include_company_source=1&include_stage=1', function (data) {
            data.$element = lead.$element;
            data.is_full = true;
            lead = data;
            console.log('tasks');
            $.each(lead.tasks, function (i, task) {
                task.lead = lead;
            });
            console.log('n');
            $.each(lead.notes, function (i, note) {
                note.lead = lead;
            });
            console.log('m');
            $.each(lead.mails, function (i, mail) {
                mail.lead = lead;
            });
            console.log('tw');
            $.each(lead.twitter, function (i, item) {
                item.lead = lead;
            });
            
            lead.$element.data('lead', lead);
            console.log('fillCard');
            fillCard();
        });
    }

    function fillTasks() {
        if (!lead.is_full) {
            return;
        }
        var $container = $leadActions.find('.task-list-container');
        if ($container.length && !$container.hasClass('loaded')) {
            $container.append(Tasks.$createTaskList(lead.tasks)).addClass('loaded');
        }
    }
    function fillNotes() {
        if (!lead.is_full) {
            return;
        }
        var $container = $leadActions.find('.note-list-container');
        if ($container.length && !$container.hasClass('loaded')) {
            $container.append(Notes.$createNoteList(lead.notes)).addClass('loaded');
        }
    }
    function fillMails() {
        if (!lead.is_full) {
            return;
        }
        var $container = $leadActions.find('.mail-list-container');
        if ($container.length && !$container.hasClass('loaded')) {
            $container.append(Mails.$createMailList(lead.mails)).addClass('loaded');
        }
    }

    function fillTwitters() {
        if (!lead.is_full) {
            return;
        }

        $('#lead-action-tab-pane-twitter').addClass('disabled');
        $.each(UserData.integrations, function(i, integration) {
            if (integration.type === 'twitter') {
                $('#lead-action-tab-pane-twitter').removeClass('disabled');
            }
        });

        if (UserData.id !== lead.owner_id) {
             $('.twitter-item-button-follow').addClass('disabled');
        } else {
            $('.twitter-item-button-follow').removeClass('disabled');
        }

        $('.integration-twitter-add-dropdown form input').val('');
        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-twitter>.twitter-action');
        if ($action.length && !$action.hasClass('loaded')) {
            var client = lead.clients[0];
            if (client.integrations.twitter == null) {
                $('.integration-twitter-add-dropdown').removeClass('hide');
                $('.client-twitter-info .twitter-item').addClass('hide');
            } else if (client.integrations.twitter) {
                $('.integration-twitter-add-dropdown').addClass('hide');
                $('.client-twitter-info .twitter-item').removeClass('hide');
                var twitter = client.integrations.twitter;
                if (DevOptions.debug) {
                    twitter.is_follower = true;
                    twitter.is_followed = true;
                }

//                if (twitter.new_events_count > 0) {
//                    $card.find('.lead-actions>ul.nav.nav-tabs>li>a[href="#lead-action-tab-pane-twitter"]').find('span.count').html(twitter.new_events_count).show();
//                } else {
//                    $card.find('.lead-actions>ul.nav.nav-tabs>li>a[href="#lead-action-tab-pane-twitter"]').find('span.count').hide();
//                }

                var $clientTwitterItem = $clientTwitterInfo.find('.twitter-item');
                $clientTwitterItem
                        .data('lead', lead)
                        .find("img.twitter-item-profile-image").attr('src', twitter.avatar.replace(/normal/, '200x200')).end()
                        .find("h4.fullname").html(twitter.fullname).end()
                        .find("span.username").html(twitter.username).end()
                        .find(".twitter-item-description").html(twitter.description);

                if (twitter.is_followed) {
                    $clientTwitterInfo.addClass('following');
                    $clientTwitterItem.find("button.twitter-item-button-follow").html("Following");
                } else {
                    $clientTwitterInfo.removeClass('following');
                    $clientTwitterItem.find("button.twitter-item-button-follow").html("Follow");
                }

                if (twitter.is_follower) {
                    $clientTwitterInfo.addClass('follower');
                } else {
                    $clientTwitterInfo.removeClass('follower');
                }

                updateTwitterDirectMessageIcon();

                if (DevOptions.debug && DevOptions.showSampleData) {
                    lead.twitter.push({
                        id: 33333,
                        description: 'test data',
                        sender: {
                            avatar: '',
                            fullname: 'test',
                            username: 'data'
                        },
                        created_at: ((new Date()).getTime() / 1000),
                        is_own: false,
                        type: 'twitter_tweet'
                    });
                }

                $action.find('.twitter-list-container').append(Twitter.$createTwitterItemList(lead.twitter)).find('[data-toggle="tooltip"]').tooltip({container: $cardPopup});
            }
            $action.addClass('loaded');
        }
    }

    function toggleSource() {
        if (!leadAccepted) {
            return;
        }

        if ($source.is(':visible')) {
            $source.stop().fadeOut({duration: 500, queue: false}).slideUp(500);
        } else {
            $source.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
        }
        return false;
    }

    function toggleCompany(is_enabled) {
        return $.ajax({
            method: 'PUT',
            data: {
                is_enabled: is_enabled
            },
            url: '/api/v1/companies/' + lead.company.id,
            success: function () {
                lead.company.is_enabled = is_enabled;
            }
        });
    }
    function addCompany() {
        return $.post(getApiUrl() + '/companies', function (company) {
            lead.company = company;
            fillCompany();
        });
    }

    function changeStage(stid) {
        return $.ajax({
            url: getApiUrl(),
            type: 'PUT',
            data: {stage_id: stid},
            success: function () {
                lead.stage_id = stid;
                updateStageCount(lead.$element.closest('.stage'));
                lead.$element.detach().appendTo(".stage[data-stage-id=" + stid + "] .clients");
                updateStageCount(lead.$element.closest('.stage'));
                fillStage();
            }
        });
    }

    function onTaskActionTabShown() {
        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-task>.task-action');
        if (!$action.length) {
            $action = $('\
                <div class="task-action">\
                    <form class="form action-form task-action-form">\
                        <div class="form-group">\
                            <textarea class="form-control" name="description" placeholder="Add Task" rows="3"></textarea>\
                        </div>\
                        <div class="row">\
                            <div class="col-md-3">\
                                <div class="form-group">\
                                    <select class="form-control" name="type">\
                                        <option disabled="" selected="">Task Type</option>\
                                        <option value="1">Email</option>\
                                        <option value="2">Call</option>\
                                        <option value="3">Meeting</option>\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="col-md-3">\
                                <div class="form-group">\
                                    <span class="input-group date">\
                                        <input class="form-control" name="due_at" onkeydown="return false" placeholder="Set a Date" type="text">\
                                        <span class="input-group-addon">\
                                            <span class="glyphicon glyphicon-calendar"></span>\
                                        </span>\
                                    </span>\
                                </div>\
                            </div>\
                            <div class="col-md-3">\
                                <div class="form-group">\
                                    <select class="form-control" name="is_completed">\
                                        <option value="1">Complete</option>\
                                        <option value="0">Due</option>\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="col-md-2 col-md-offset-1">\
                                <button class="btn btn-primary btn-block btn-save">Submit</button>\
                            </div>\
                        </div>\
                    </form>\
                    <div class="task-list-container"></div>\
                </div>\
            ').appendTo($leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-task'));

            var $form = $action.find('form.task-action-form');
            $form.find('input[name="due_at"]').parent().datetimepicker({
                container: $form.find('input[name="due_at"]').parent(),
                format: 'M dd, yyyy',
                todayBtn: 'linked',
                showMeridian: true,
                weekStart: 1,
                pickerPosition: 'top-left',
                autoclose: true,
                minView: 2
            });
            $form.on('submit', function () {
                var $xhr = createTask($(this));
                if ($xhr) {
                    var $btn = $(this).find('button.btn-save');
                    var html = $btn.html();
                    $btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving');
                    $xhr.done(function () {
                        $btn.attr('disabled', false).html(html);
                    }).fail(function () {
                        $btn.attr('disabled', false).html(html);
                    });
                }
                return false;
            });
            fillTasks();
        }

        $action.find('input[name="description"]').focus();
    }

    function resetTaskActionTab() {
        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-task>.task-action');
        if ($action.length) {
            $action.find('form.task-action-form').reset();
            $action.find('.task-list-container').removeClass('loaded').empty(); //Clear Task List
        }
    }

    function createTask($form) {
        var values = {};
        var params = $form.serializeArray();
        $.each(params, function (i, param) {
            values[param.name] = param.value;
        });

        if (!values.type || !values.due_at || !values.description) {
            showErrorMessage('Tasks', 'Please enter description, type and due date.');
            return false;
        }

        values.due_at = (new Date(values.due_at)).getTime() / 1000;
        return $.post(getApiUrl() + '/clients/' + lead.clients[0].id + '/tasks', values, function (task) {
            showSuccessMessage("Success", "A task has been added successfully.");
            task.lead = lead;
            lead.tasks.unshift(task);
            Tasks.$createTask(task).hide().prependTo($leadActions.find('.task-list-container>.task-list')).fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            $form.reset();
            lead.$element.find('.date').html('<i class="fa fa-clock-o"></i> ' + getDateString(task.due_at).replace(', ' + (new Date()).getFullYear(), ''));
        });
    }

    function onNoteActionTabShown() {
        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-note>.note-action');
        if (!$action.length) {
            $action = $('\
                <div class="note-action">\
                    <form class="form action-form note-action-form">\
                        <div class="form-group">\
                            <div class="bootstrap-text-editor form-control note-description" data-placeholder="Add Note"></div>\
                        </div>\
                        <div class="row">\
                            <div class="col-md-10">\
                            ' + getEditableWYSIHTML5OptionsHtml() + '\
                            </div>\
                            <div class="col-md-2">\
                                <button class="btn btn-primary btn-block btn-save">Submit</button>\
                            </div>\
                        </div>\
                    </form>\
                    <div class="note-list-container"></div>\
                </div>\
            ').appendTo($leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-note'));

            var $form = $action.find('form.note-action-form');
            $form.find('.bootstrap-text-editor').wysiwyg({toolbarSelector: $form.find('[data-role="editor-toolbar"]')});
            $form.on('submit', function () {
                var $xhr = createNote($(this));
                if ($xhr) {
                    var $btn = $(this).find('button.btn-save');
                    var html = $btn.html();
                    $btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving');
                    $xhr.done(function () {
                        $btn.attr('disabled', false).html(html);
                    }).fail(function () {
                        $btn.attr('disabled', false).html(html);
                    });
                }
                return false;
            });
            fillNotes();
        }
    }

    function resetNoteActionTab() {
        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-note>.note-action');
        if ($action.length) {
            $action.find('form.note-action-form').reset()
                    .find('.bootstrap-text-editor.note-description').empty();
            $action.find('.note-list-container').removeClass('loaded').empty(); //Clear Note List
        }
    }

    function createNote($form) {
        var values = {};
        values.description = $form.find('.bootstrap-text-editor.note-description').html();

        if (!values.description) {
            showErrorMessage('Notes', 'Please enter description.');
            return false;
        }

        return $.post(getApiUrl() + '/clients/' + lead.clients[0].id + '/notes', values, function (note) {
            showSuccessMessage("Success", "A note has been added successfully.");
            note.lead = lead;
            lead.notes.unshift(note);
            Notes.$createNote(note).hide().prependTo($leadActions.find('.note-list-container>.note-list')).fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            $form.reset().find('.bootstrap-text-editor.note-description').empty();
        });
    }

    function onMailActionTabShown() {
        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-mail>.mail-action');
        if (!$action.length) {
            $action = $('\
                <div class="mail-action">\
                    <form class="form action-form mail-action-form">\
                        <div class="row">\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <input class="form-control" name="name" placeholder="Subject" type="text">\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group mail-to-wrapper">\
                                    <input class="form-control mail-to" placeholder="To:" readonly type="text">\
                                    <button type="button" class="btn btn-sm btn-default btn-cc-bcc-toggle">CC/BCC</button>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="row cc-bcc-wrapper">\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <input class="form-control" name="cc" placeholder="CC:" type="text">\
                                </div>\
                            </div>\
                            <div class="col-md-6">\
                                <div class="form-group">\
                                    <input class="form-control" name="bcc" placeholder="BCC:" type="text">\
                                </div>\
                            </div>\
                        </div>\
                        <div class="form-group">\
                            <div class="bootstrap-text-editor form-control mail-description" data-placeholder="Message"></div>\
                        </div>\
                        <div class="row">\
                            <div class="col-md-10">\
                            ' + getEditableWYSIHTML5OptionsHtml() + '\
                            </div>\
                            <div class="col-md-2">\
                                <button class="btn btn-primary btn-block btn-save">Submit</button>\
                            </div>\
                        </div>\
                    </form>\
                    <div class="mail-list-container"></div>\
                </div>\
            ').appendTo($leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-mail'));

            var $form = $action.find('form.mail-action-form');
            $form
                    .find('.bootstrap-text-editor').wysiwyg({toolbarSelector: $form.find('[data-role="editor-toolbar"]')}).end()
                    .find('input.mail-to').val(lead.clients[0].email);

            $form.on('submit', function () {
                var $xhr = createMail($(this));
                if ($xhr) {
                    var $btn = $(this).find('button.btn-save');
                    var html = $btn.html();
                    $btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving');
                    $xhr.done(function () {
                        $btn.attr('disabled', false).html(html);
                    }).fail(function () {
                        $btn.attr('disabled', false).html(html);
                    });
                }
                return false;
            }).on('click', 'button.btn-cc-bcc-toggle', function () {
                $(this).closest('form.mail-action-form').toggleClass('show-cc-bcc');
            });
            fillMails();
        }
    }

    function resetMailActionTab() {
        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-mail>.mail-action');
        if ($action.length) {
            $action.find('form.mail-action-form').reset()
                    .find('input.mail-to').val(lead.clients[0].email).end()
                    .find('.bootstrap-text-editor.mail-name').empty();
            $action.find('.mail-list-container').removeClass('loaded').empty(); //Clear Mail List
        }
    }

    function createMail($form) {
        var values = $form.serializeObject();
        values.description = $form.find('.bootstrap-text-editor.mail-description').html();
        var to = $form.find('input.mail-to').val();

        if (!values.name || !to || !values.description) {
            showErrorMessage('Tasks', 'Please enter subject, to and message.');
            return false;
        }

        return $.post(getApiUrl() + '/clients/' + lead.clients[0].id + '/mails', values, function (mail) {
            showSuccessMessage("Success", "A mail has been added successfully.");
            mail.lead = lead;
            lead.mails.unshift(mail);
            Mails.$createMail(mail).hide().appendTo($leadActions.find('.mail-list-container>.mail-list')).fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            $form.reset().find('.bootstrap-text-editor.mail-description').empty();
        });
    }

    function onTwitterActionTabShown() {
        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-twitter>.twitter-action');
        if (!$action.length) {
            $action = $('\
                <div class="twitter-action">\
                    <div class="client-twitter-info">\
                        <span class="integration-twitter-add-dropdown dropdown hide">\
                            <a href="javascript:void(0)" class="integration-twitter-add-dropdown-toggle dropdown-toggle btn" data-toggle="dropdown"><i class="fa fa-plus"></i> Add Twitter</a>\
                            <form class="dropdown-menu form form-horizontal integration-twitter-add-form">\
                                <div class="form-body">\
                                    <div class="form-group">\
                                        <label class="col-md-2 control-label"><i class="fa fa-cliently-twitter"></i></label>\
                                        <div class="col-md-6"><input type="text" name="twitter" value="" class="form-control" /></div>\
                                        <div class="col-md-3 text-right">\
                                            <button type="submit" class="btn btn-primary btn-save">Save</button>\
                                        </div>\
                                    </div>\
                                </div>\
                            </form>\
                        </span>\
                        <div class="twitter-item hide">\
                            <div class="twitter-item-inner">\
                                <img class="twitter-item-profile-image" src="/images/profile-blank.png" />\
                                <button class="btn btn-blue twitter-item-button-follow">Follow</button>\
                                <div class="twitter-item-info">\
                                    <div class="twitter-item-header">\
                                        <h4 class="fullname">Full Name</h4>\
                                        <span>@</span><span class="username">username</span>\
                                    </div>\
                                </div>\
                                <div class="twitter-item-description">\
                                </div>\
                                <div class="twitter-item-buttons">\
                                    <a href="#" class="twitter-item-button" data-event-type="twitter_direct" data-toggle="tooltip" title="Direct Message"><img src="/images/direct-message-disabled.png" /></a>\
                                    <a href="#" class="twitter-item-button" data-event-type="twitter_tweet" data-toggle="tooltip" title="Reply to Tweet"><i class="fa fa-reply"></i></a>\
                                </div>\
                            </div>\
                            <div class="form twitter-message-form" data-event-type="twitter_direct" data-role="default">\
                                <label for="msg" class="twitter-message-username"></label>\
                                <textarea class="form-control twitter-message" placeholder="Message ..." rows="5" maxlength="10000"></textarea>\
                                <div class="twitter-message-form-buttons">\
                                    <span class="left-letter-count">140</span>\
                                    <button class="btn btn-primary btn-save">Send</button>\
                                    <button class="btn btn-default btn-cancel">Cancel</button>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="twitter-list-container"></div>\
                </div>\
            ').appendTo($leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-twitter'));
            $clientTwitterInfo = $action.find('.client-twitter-info');

            $clientTwitterInfo.find('.twitter-item button.twitter-item-button-follow').on('mouseover', function () {
                if ($clientTwitterInfo.hasClass('following')) {
                    $(this).html('Unfollow');
                }
            }).on('mouseout', function () {
                if ($clientTwitterInfo.hasClass('following')) {
                    $(this).html('Following');
                }
            }).on('click', function () {
                if ( ! $(this).hasClass('disabled')) {
                    var type = $clientTwitterInfo.hasClass('following') ? TwitterEventTypes.UNFOLLOW : TwitterEventTypes.FOLLOW;
                    var $xhr = twitterFollow(type);
                    if ($xhr) {
                        var $btn = $(this);
                        $btn.html('<i class="fa fa-spin fa-spinner"></i> ' + (type == TwitterEventTypes ? 'Unfollowing...' : 'Following...')).attr('disabled', true);
                        $xhr.done(function () {
                            $btn.html((type == TwitterEventTypes ? 'Follow' : 'Unfollow')).attr('disabled', false);

                        }).fail(function () {
                            $btn.html((type == TwitterEventTypes ? 'Unfollow' : 'Follow')).attr('disabled', false);
                        });
                    }
                }
            });

            $clientTwitterInfo.find('[data-toggle="tooltip"]').tooltip({container: $cardPopup});

            fillTwitters();
        }
    }

    function resetTwitterActionTab() {
        if (DevOptions.debug && lead.new_events_count != -1) {
            lead.new_events_count = 10;
        }
        if (lead.new_events_count > 0) {
            $card.find('.lead-actions>ul.nav.nav-tabs>li>a[href="#lead-action-tab-pane-twitter"]').find('span.count').html(lead.new_events_count).show();
        } else {
            $card.find('.lead-actions>ul.nav.nav-tabs>li>a[href="#lead-action-tab-pane-twitter"]').find('span.count').hide();
        }

        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-twitter>.twitter-action');
        if ($action.length) {
            var $clientTwitterItem = $clientTwitterInfo.find('.twitter-item');
            $clientTwitterItem
                    .find("img.twitter-item-profile-image").attr('src', '/images/profile-blank.png').end()
                    .find("h4.fullname").html('Full Name').end()
                    .find("span.username").html('username').end()
                    .find(".twitter-item-description").html('Description').end();
            $clientTwitterItem.find('.twitter-message-form').hide().find('.twitter-message').empty();
            $clientTwitterInfo.removeClass('following follower');
            $clientTwitterItem.find("button.twitter-item-button-follow").html("Follow");

            updateTwitterDirectMessageIcon();

            $action.find('.twitter-list-container').empty(); //Clear Twitter List
            $action.removeClass('loaded');
        }
    }

    function updateTwitterDirectMessageIcon() {
        var $action = $leadActions.find('>.tab-content>.tab-pane#lead-action-tab-pane-twitter>.twitter-action');
        if ($action.find('.client-twitter-info').hasClass('following follower')) {
            $action.find('.client-twitter-info .twitter-item').find('.twitter-item-buttons a[data-event-type="' + TwitterEventTypes.DIRECT + '"]').removeClass('disabled').attr('data-original-title', 'Direct Message').find('img').attr('src', '/images/direct-message.png');
        } else {
            $action.find('.client-twitter-info .twitter-item').find('.twitter-item-buttons a[data-event-type="' + TwitterEventTypes.DIRECT + '"]').addClass('disabled').attr('data-original-title', 'You will only be able to Direct Message once your client has followed you').find('img').attr('src', '/images/direct-message-disabled.png');
        }
    }

    function twitterFollow(type) {
        return $.post('/api/v1/clients/' + lead.clients[0].id + '/twitter/messages', {type: type}, function (res) {
            if (res && res.id && res.id > 0) {
                if (type == TwitterEventTypes.FOLLOW) {
                    $clientTwitterInfo.addClass('following');
                    lead.clients[0].integrations.twitter.is_followed = true;
                    showSuccessMessage("Follow", "You are now following the client successfully.");
                } else {
                    $clientTwitterInfo.removeClass('following');
                    lead.clients[0].integrations.twitter.is_followed = false;
                    showSuccessMessage("Unfollow", "You have unfollowed the client successfully.");
                }
                Twitter.$createTwitterItem(res).prependTo($leadActions.find(".twitter-list-container .twitter-item-list")).fadeIn({duration: 500, queue: false}).hide().slideDown(500).find('[data-toggle="tooltip"]').tooltip({container: $cardPopup});
                updateTwitterDirectMessageIcon();
            }
        });
    }

    function popupDealImportExportModal(step) {
        if ( ! $('#modal_deal_import_export').length) {
            $($('#tpl_modal_deal_import_export').html()).appendTo('#page-clients .main-header');
        }

        $('#modal_deal_import_export').attr('data-ui-import-step', 'step_deal_import_file');
        $('#modal_deal_import_export .export-btn').attr('href', '/api/v1/workspaces/' + current_workspace_id + '/deals/export');
        $('#select_deal_import_dst_pipeline').html('');
        $('#select_deal_import_dst_stage').html('');
        $('#select_deal_import_dst_workflow').html('');

        var tpl_dom_option = $($('#tpl_dom_option').html());
        $.each(pipelines, function(i, pipeline) {
            tpl_dom_option.clone().attr('value', pipeline.id).data('index', i).text(pipeline.name).appendTo($('#select_deal_import_dst_pipeline'));
        });
        $.each(pipelines[0].stages, function(i, stage) {
            tpl_dom_option.clone().attr('value', stage.id).text(stage.name).appendTo($('#select_deal_import_dst_stage'));
        });
        $.each(workflows, function(i, workflow) {
            tpl_dom_option.clone().attr('value', workflow.id).text(workflow.name).appendTo($('#select_deal_import_dst_workflow'));
        });

        $('#modal_deal_import_export').modal('show');
    }

    function getApiUrl() {
        return '/api/v1/deals/' + leadId;
    }

    var Sidebar = (function () {
        var scope = {};

        var $sidebar = null;

        var $workflowsContainer = null;
        var $contactsContainer = null;
        var $contactsSearch = null;

        var initialized = false;

        function init() {
            $(document).on('show.bs.modal', '#lead-card-container.modal', function () {
                setTimeout(function () {
                    $('.modal-backdrop').addClass('lead-card-container-modal-backdrop');
                });
            }).on('click', '#lead-card-container.modal .shortcuts .shortcut', function () {
                switch ($(this).attr('data-command')) {
                    case 'expand':
                        if ($('#lead-card-container.modal .sidebar>ul.nav.nav-tabs>li.active>a').length) {
                            $('#lead-card-container.modal .sidebar>ul.nav.nav-tabs>li.active>a').click();
                        } else if ($('#lead-card-container.modal .sidebar>ul.nav.nav-tabs>li.last-active>a').length) {
                            $('#lead-card-container.modal .sidebar>ul.nav.nav-tabs>li.last-active>a').click();
                        } else {
                            $('#lead-card-container.modal .sidebar>ul.nav.nav-tabs>li:first-child>a').click();
                        }
                        break;
                    case 'close':
                        $cardPopup.modal('hide');
                        break;
                }
            }).on('show.bs.tab', '#lead-card-container.modal .sidebar>ul.nav.nav-tabs>li>a[data-toggle="tab"]', function () {
                $cardPopup.addClass('sidebar-expanded');
                $('.lead-card-container-modal-backdrop').removeClass('in');

                $(this).addClass('being-activated');
                var tab = this.href.substr(this.href.indexOf('#'));
                switch (tab) {
                    case '#lead-sidebar-tab-pane-workflows':
                        onSidebarWorkflowTabShown();
                        break;
                    case '#lead-sidebar-tab-pane-timeline':
                        onSidebarTimelineTabShown();
                        break;
                    case '#lead-sidebar-tab-pane-contacts':
                        onSidebarContactsTabShown();
                        break;
                }
            }).on('click', '#lead-card-container.modal .sidebar>ul.nav.nav-tabs>li>a[data-toggle="tab"]', function () {
                var $this = $(this);
                if ($this.hasClass('being-activated')) {
                    $this.removeClass('being-activated');
                } else {
                    $this.parent().removeClass('active');
                    $this.attr('aria-expanded', false);
                    $cardPopup.removeClass('sidebar-expanded');
                    $('.lead-card-container-modal-backdrop').addClass('in');
                }
                $(this).parent().addClass('last-active').siblings().removeClass('last-active');
            }).on('submit', '#lead-card-container.modal .sidebar #lead-sidebar-tab-pane-contacts .contacts-container #lead-sidebar-contacts-search .contacts-search-form', function () {
                doContactsSearch();
                return false;
            }).on('click', '#lead-card-container.modal .sidebar #lead-sidebar-tab-pane-contacts .contacts-container #lead-sidebar-contacts-search .contacts-search-result .btn-back', function () {
                $contactsSearch.removeClass('show-search-result');
                return false;
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
            if (!$sidebar) {
                $sidebar = $('\
                    <div class="sidebar">\
                        <ul class="nav nav-tabs nav-stacked">\
                            <li><a data-toggle="tab" href="#lead-sidebar-tab-pane-workflows"><i class="fa fa-calendar"></i><br />Workflow<span class="badge"></span></a></li>\
                            <!--li><a data-toggle="tab" href="#lead-sidebar-tab-pane-timeline"><i class="fa fa-clock-o"></i><br />Timeline<span class="badge">3</span></a></li>\
                            <li><a data-toggle="tab" href="#lead-sidebar-tab-pane-contacts"><i class="fa fa-users"></i><br />Contacts<span class="badge">3</span></a></li-->\
                        </ul>\
                        <div class="tab-content">\
                            <div id="lead-sidebar-tab-pane-workflows" class="tab-pane fade"></div>\
                            <div id="lead-sidebar-tab-pane-timeline" class="tab-pane fade"></div>\
                            <div id="lead-sidebar-tab-pane-contacts" class="tab-pane fade"></div>\
                        </div>\
                    </div>\
                ').appendTo($cardPopup.find('.modal-body'));
            }
        }

        function onSidebarWorkflowTabShown() {
            loadWorkflows();
        }

        function onSidebarTimelineTabShown() {
            loadTimeline();
        }

        function onSidebarContactsTabShown() {
            loadContacts();
        }

        function loadWorkflows() {
            $workflowsContainer = $sidebar.find('#lead-sidebar-tab-pane-workflows .workflows-container');
            if (!$workflowsContainer.length) {
                var $inner = $('<div class="tab-pane-inner" />').appendTo($sidebar.find('#lead-sidebar-tab-pane-workflows')).slimScroll({height: '100%'});
                $workflowsContainer = $('\
                    <div class="workflows-container">\
                        <div class="contact-list-container">\
                            <a href="#" class="contact-list-dropdown-toggle">Contact Name <i class="fa fa-angle-down"></i></a>\
                        </div>\
                        <ul class="nav nav-tabs nav-justified workflow-toggles">\
                            <li><a href="#active">Active Flows <i class="fa fa-angle-down"></i></a></li>\
                            <li><a href="#inactive">Inactive Flows <i class="fa fa-angle-down"></i></a></li>\
                        </ul>\
                        <span class="workflow-list-dropdown-toggle"><i class="fa fa-angle-down fa-2x"></i></span>\
                        <div class="workflow-container"></div>\
                    </div>\
                ').appendTo($inner.append(''));
                $workflowsContainer.find('.contact-list-dropdown-toggle').click(function (e) {
                    showContactListDropdown();
                    return e.preventDefault();
                });
                $workflowsContainer.find('.workflow-toggles a[href="#active"]').click(function (e) {
                    showActiveWorkflowListDropdown();
                    return e.preventDefault();
                });
                $workflowsContainer.find('.workflow-toggles a[href="#inactive"]').click(function (e) {
                    showInactiveWorkflowListDropdown();
                    return e.preventDefault();
                });
                $workflowsContainer.find('.workflow-list-dropdown-toggle').click(function (e) {
                    showWorkflowListDropdown();
                    return e.preventDefault();
                });
            }

            loadWorkflow();
        }

        function loadWorkflow() {
            var $workflowContainer = $workflowsContainer.find('.workflow-container').empty();

            $.getJSON(getApiUrl() + '/deal_workflows?include_deal_actions=1').done(function (data) {
                if (data && data.length) {
                    var workflow = data[0];
                    workflow.actions = workflow.deal_actions;
                    workflow.lead = lead;
                    workflow.leadId = leadId;
                    workflow.frld = true;
                    $workflowContainer.append(Workflow.$createWorkflow(workflow, false));
                    if (lead.workflow_id > 0 && workflow.actions && workflow.actions.length) {
                        Workflow.loadWorkflow(lead.workflow_id).done(function (data) {
                            $workflowContainer.find('.workflow .flow-header h3').html(data.name);
                        });
                    }
                }
            });
        }

        function showContactListDropdown() {
            var $dropdown = $workflowsContainer.find('>.contact-list-container>.contact-list-dropdown');
            if (!$dropdown.length) {
                $dropdown = $('<div class="contact-list-dropdown dropdown-menu">\
                            <ul class="contact-list"></ul>\
                        </div>').appendTo($workflowsContainer.find('>.contact-list-container'));
                $dropdown.find(".contact-list").on('click', '>li>a', function () {
//                    var wId = $(this).parent().attr('data-workflow-id');
//                    if (lead.workflow_id == wId) {
//                        return false;
//                    }
//                    $.ajax({
//                        url: "/api/v1/deals/" + leadId,
//                        method: 'put',
//                        data: {workflow_id: wId},
//                        success: function () {
//                            lead.workflow_id = wId;
//                            loadWorkflow();
//                        }
//                    });
                });
            }

            var $list = $dropdown.hide().find(".contact-list").empty().append('<li class="contact-list-item loading">\
                    <div class="contact-list-item-inner">\
                        <a><i class="fa fa-spin fa-spinner"></i> Searching ...</a>\
                    </div>\
                </li>');
//            Contacts.loadContacts(true).done(function (data) {
            $list.empty();
//                if (!data.length && DevOptions.showSampleData) {
            var data = [
                {id: 1, name: 'contact123'},
                {id: 2, name: 'contact333'},
                {id: 3, name: 'contact444'},
                {id: 4, name: 'contact555'},
                {id: 5, name: 'contact777'},
                {id: 6, name: 'contact999'}
            ];
//                }
            if (data && data.length) {
                $.each(data, function (index, item) {
                    $('<li class="contact-list-item ' + (item.id == lead.contact_id ? 'selected' : '') + '">\
                    <a ' + (item.id == lead.contact_id ? 'disabled="disabled"' : '') + '><i class="fa fa-check"></i> ' + item.name + '</a>\
                </li>').attr('data-contact-id', item.id)/*.hide()*/.appendTo($list);//.fadeIn({duration: 500, queue: false});//.hide().slideDown(500);
                    ;
                });
            } else {
                $list.append('<li class="contact-list-item loading">\
                            <a><i class="fa fa-times"></i> No matches found.</a>\
                        </li>');
            }
//            });

            $dropdown.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            $(document).off('click.lead.sidebar.contact.list.toggle');
            setTimeout(function () {
                $(document).on('click.lead.sidebar.contact.list.toggle', function () {
                    $workflowsContainer.find('.contact-list-dropdown').fadeOut('fast');
                    $(document).off('click.lead.sidebar.contact.list.toggle');
                });
            });
        }

        function showActiveWorkflowListDropdown() {
            var $dropdown = $workflowsContainer.find('.active-workflow-list-dropdown');
            if (!$dropdown.length) {
                $dropdown = $('<div class="active-workflow-list-dropdown dropdown-menu">\
                            <ul class="workflow-list"></ul>\
                        </div>').appendTo($workflowsContainer.find('>.workflow-toggles a[href="#active"]').parent());
                $dropdown.find(".workflow-list").on('click', '>li>a', function () {
//                    var wId = $(this).parent().attr('data-workflow-id');
//                    if (lead.workflow_id == wId) {
//                        return false;
//                    }
//                    $.ajax({
//                        url: "/api/v1/deals/" + leadId,
//                        method: 'put',
//                        data: {workflow_id: wId},
//                        success: function () {
//                            lead.workflow_id = wId;
//                            loadWorkflow();
//                        }
//                    });
                });
            }

            var $list = $dropdown.hide().find(".workflow-list").empty().append('<li class="workflow-list-item loading">\
                    <div class="workflow-list-item-inner">\
                        <a><i class="fa fa-spin fa-spinner"></i> Searching ...</a>\
                    </div>\
                </li>');
//            Workflows.loadWorkflows(true).done(function (data) {
            $list.empty();
//                if (!data.length && DevOptions.showSampleData) {
            var data = [
                {id: 1, name: 'workflow123'},
                {id: 2, name: 'workflow333'},
                {id: 3, name: 'workflow444'},
                {id: 4, name: 'workflow555'},
                {id: 5, name: 'workflow777'},
                {id: 6, name: 'workflow999'}
            ];
//                }
            if (data && data.length) {
                $.each(data, function (index, item) {
                    $('<li class="workflow-list-item ' + (item.id == lead.workflow_id ? 'selected' : '') + '">\
                    <a ' + (item.id == lead.workflow_id ? 'disabled="disabled"' : '') + '><i class="fa fa-check"></i> ' + item.name + '</a>\
                </li>').attr('data-workflow-id', item.id)/*.hide()*/.appendTo($list);//.fadeIn({duration: 500, queue: false});//.hide().slideDown(500);
                    ;
                });
            } else {
                $list.append('<li class="workflow-list-item loading">\
                            <a><i class="fa fa-times"></i> No matches found.</a>\
                        </li>');
            }
//            });

            $dropdown.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            $(document).off('click.lead.sidebar.active-workflow.list.toggle');
            setTimeout(function () {
                $(document).on('click.lead.sidebar.active-workflow.list.toggle', function () {
                    $workflowsContainer.find('.active-workflow-list-dropdown').fadeOut('fast');
                    $(document).off('click.lead.sidebar.active-workflow.list.toggle');
                });
            });
        }

        function showInactiveWorkflowListDropdown() {
            var $dropdown = $workflowsContainer.find('.inactive-workflow-list-dropdown');
            if (!$dropdown.length) {
                $dropdown = $('<div class="inactive-workflow-list-dropdown dropdown-menu">\
                            <ul class="workflow-list"></ul>\
                        </div>').appendTo($workflowsContainer.find('>.workflow-toggles a[href="#inactive"]').parent());
                $dropdown.find(".workflow-list").on('click', '>li>a', function () {
//                    var wId = $(this).parent().attr('data-workflow-id');
//                    if (lead.workflow_id == wId) {
//                        return false;
//                    }
//                    $.ajax({
//                        url: "/api/v1/deals/" + leadId,
//                        method: 'put',
//                        data: {workflow_id: wId},
//                        success: function () {
//                            lead.workflow_id = wId;
//                            loadWorkflow();
//                        }
//                    });
                });
            }

            var $list = $dropdown.hide().find(".workflow-list").empty().append('<li class="workflow-list-item loading">\
                    <div class="workflow-list-item-inner">\
                        <a><i class="fa fa-spin fa-spinner"></i> Searching ...</a>\
                    </div>\
                </li>');
//            Workflows.loadWorkflows(true).done(function (data) {
            $list.empty();
//                if (!data.length && DevOptions.showSampleData) {
            var data = [
                {id: 1, name: 'workflow123'},
                {id: 2, name: 'workflow333'},
                {id: 3, name: 'workflow444'},
                {id: 4, name: 'workflow555'},
                {id: 5, name: 'workflow777'},
                {id: 6, name: 'workflow999'}
            ];
//                }
            if (data && data.length) {
                $.each(data, function (index, item) {
                    $('<li class="workflow-list-item ' + (item.id == lead.workflow_id ? 'selected' : '') + '">\
                    <a ' + (item.id == lead.workflow_id ? 'disabled="disabled"' : '') + '><i class="fa fa-check"></i> ' + item.name + '</a>\
                </li>').attr('data-workflow-id', item.id)/*.hide()*/.appendTo($list);//.fadeIn({duration: 500, queue: false});//.hide().slideDown(500);
                    ;
                });
            } else {
                $list.append('<li class="workflow-list-item loading">\
                            <a><i class="fa fa-times"></i> No matches found.</a>\
                        </li>');
            }
//            });

            $dropdown.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            $(document).off('click.lead.sidebar.inactive-workflow.list.toggle');
            setTimeout(function () {
                $(document).on('click.lead.sidebar.inactive-workflow.list.toggle', function () {
                    $workflowsContainer.find('.inactive-workflow-list-dropdown').fadeOut('fast');
                    $(document).off('click.lead.sidebar.inactive-workflow.list.toggle');
                });
            });
        }

        function showWorkflowListDropdown() {
            var $dropdown = $workflowsContainer.find('>.workflow-list-dropdown');
            if (!$dropdown.length) {
                $dropdown = $('<div class="workflow-list-dropdown dropdown-menu">\
                            <ul class="workflow-list"></ul>\
                        </div>').appendTo($workflowsContainer);
                $dropdown.find(".workflow-list").on('click', '>li>a', function () {
                    var wId = $(this).parent().attr('data-workflow-id');
                    if (lead.workflow_id == wId) {
                        return false;
                    }
                    $.ajax({
                        url: "/api/v1/deals/" + leadId,
                        method: 'put',
                        data: {workflow_id: wId},
                        success: function () {
                            lead.workflow_id = wId;
                            loadWorkflow();
                        }
                    });
                });
            }

            var $list = $dropdown.hide().find(".workflow-list").empty().append('<li class="workflow-list-item loading">\
                    <div class="workflow-list-item-inner">\
                        <a><i class="fa fa-spin fa-spinner"></i> Searching ...</a>\
                    </div>\
                </li>');
            Workflow.loadWorkflowList(true).done(function (data) {
                $list.empty();
                if (!data.length && DevOptions.showSampleData) {
                    var data = [
                        {id: 1, name: 'workflow123'},
                        {id: 2, name: 'workflow333'},
                        {id: 3, name: 'workflow444'},
                        {id: 4, name: 'workflow555'},
                        {id: 5, name: 'workflow777'},
                        {id: 6, name: 'workflow999'}
                    ];
                }
                $('<li class="workflow-list-item custom-workflow"><a><i class="fa fa-plus"></i> Custom Workflow</a></li>').attr('data-workflow-id', 0).appendTo($list);
                if (data && data.length) {
                    $.each(data, function (index, item) {
                        $('<li class="workflow-list-item ' + (item.id == lead.workflow_id ? 'selected' : '') + '">\
                    <a ' + (item.id == lead.workflow_id ? 'disabled="disabled"' : '') + '><i class="fa fa-check"></i> ' + item.name + '</a>\
                </li>').attr('data-workflow-id', item.id)/*.hide()*/.appendTo($list);//.fadeIn({duration: 500, queue: false});//.hide().slideDown(500);
                    });
                } else {
                    $list.append('<li class="workflow-list-item loading">\
                            <a><i class="fa fa-times"></i> No matches found.</a>\
                        </li>');
                }
            });

            $dropdown.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            $(document).off('click.lead.sidebar.workflow.list.toggle');
            setTimeout(function () {
                $(document).on('click.lead.sidebar.workflow.list.toggle', function () {
                    $workflowsContainer.find('>.workflow-list-dropdown').fadeOut('fast');
                    $(document).off('click.lead.sidebar.workflow.list.toggle');
                });
            });
        }

        function loadTimeline() {
            var $timelineContainer = $sidebar.find('#lead-sidebar-tab-pane-timeline .timeline-container');
            if (!$timelineContainer.length) {
                var $inner = $('<div class="tab-pane-inner" />').appendTo($sidebar.find('#lead-sidebar-tab-pane-timeline')).slimScroll({height: '100%'});
                $timelineContainer = $('<div class="timeline-container"></div>').appendTo($inner);
            } else {
                $timelineContainer.empty();
            }

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
            $timelineContainer.append(Timeline.$createTimeline({
                lead: lead,
                actions: actions
            }));
        }

        function loadContacts() {
            if (!$contactsContainer) {
                var $inner = $('<div class="tab-pane-inner" />').appendTo($sidebar.find('#lead-sidebar-tab-pane-contacts')).slimScroll({height: '100%'});
                $contactsContainer = $('\
                    <div class="contacts-container">\
                        <ul class="nav nav-tabs nav-justified">\
                            <li><a data-toggle="tab" href="#lead-sidebar-contacts-list">Contacts</a></li>\
                            <li class="active"><a data-toggle="tab" href="#lead-sidebar-contacts-search">Find Contacts</a></li>\
                        </ul>\
                        <div class="tab-content">\
                            <div id="lead-sidebar-contacts-list" class="tab-pane fade">\
                                <div class="header">\
                                    <button class="btn btn-primary btn-add">+ Add Contact</button>\
                                    <h2>Contacts</h2>\
                                    <p>Short Description</p>\
                                </div>\
                                <div class="contact-list-container"></div>\
                            </div>\
                            <div id="lead-sidebar-contacts-search" class="tab-pane fade in active">\
                                <form class="contacts-search-form">\
                                    <button class="btn btn-primary btn-search">Find Contacts</button>\
                                </form>\
                                <div class="contacts-search-result">\
                                    <button class="btn btn-back"><img src="/images/arrow2.png" /> Back</button>\
                                    <div class="contact-list-container"></div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                ').appendTo($inner);
                $contactsSearch = $contactsContainer.find('#lead-sidebar-contacts-search');
                drawContactsSearchFilter();
            }

            var contacts = [
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: false
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
            ];

            var $contacts = $contactsContainer.find('#lead-sidebar-contacts-list .contact-list-container').empty();
            Contacts.$createContacts(contacts).appendTo($contacts);
        }

        function drawContactsSearchFilter() {
            var filters = [
                {
                    name: "Function",
                    options: [
                        {name: 'Sales', count: 11},
                        {name: 'Suport', count: 8},
                        {name: 'Information Technology', count: 7},
                        {name: 'Entrepreneurship', count: 7},
                        {name: 'Marketing', count: 2},
                        {name: 'Media and Communication', count: 2},
                        {name: 'Operations', count: 2},
                    ]
                },
                {
                    name: "Title",
                    options: [
                        {name: 'President', count: 11},
                        {name: 'Vice President', count: 8},
                        {name: 'Project Manager', count: 7},
                        {name: 'Developer', count: 7}
                    ]
                },
                {
                    name: "Seniority Level",
                    options: [
                        {name: 'Entry', count: 11},
                        {name: 'Senior', count: 8},
                        {name: 'Manager', count: 7},
                        {name: 'Owner', count: 7},
                    ]
                },
                {
                    name: "Location",
                    options: [
                        {name: 'New York, US', count: 11},
                        {name: 'California, US', count: 8},
                        {name: 'Hong Kong', count: 7},
                    ]
                }
            ];

            var $filters = $('<ul class="filter-list"/>').appendTo($contactsSearch.find('.contacts-search-form'));
            $.each(filters, function (i, filter) {
                var $options = $('<ul class="option-list"/>').appendTo($('<li class="filter">').append('<label>' + filter.name + '</label>').appendTo($filters));
                $('<li class="option">').append('<label><input type="checkbox" disabled="disabled" checked="checked" /> All</label>').appendTo($options).find('input:checkbox').iCheck({checkboxClass: 'icheckbox_flat-blue'});
                $.each(filter.options, function (j, option) {
                    $('<li class="option">').append('<label><input type="checkbox" /> ' + option.name + ' (' + option.count + ')</label>').appendTo($options).find('input:checkbox').iCheck({checkboxClass: 'icheckbox_flat-blue'});
                });
            });
        }

        function doContactsSearch() {
            var $contacts = $contactsSearch.find('.contacts-search-result .contact-list-container').empty();

            var contacts = [
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: false
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                },
                {
                    name: 'Daniel Pasker',
                    avatar: '/images/public/testi_3.png',
                    title: 'CEO',
                    phone: '(415)328-7934',
                    email: 'dan@salesloft.com',
                    accepted: true
                }
            ];

            Contacts.$createContacts(contacts, true).appendTo($contacts);
            $contactsSearch.addClass('show-search-result');
        }

        return scope;
    })();

    return scope;
})();