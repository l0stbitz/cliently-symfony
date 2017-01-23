var SourceModuleTypes = {
    TWITTER: 'twitter',
    DBPERSON: 'dbperson'
};

var SourceTypes = {
    TWITTER_GET_DEALS: 'twitter_get_deals',
    TWITTER_GET_USERS: 'twitter_get_users',
    DBPERSON_GET_CLIENTS: 'dbperson_get_clients'
};

var Sources = (function () {
    var scope = {};

    var SourceCreationWizardSteps = {
        SOURCE_TYPE_CHOOSER: 'source-type-chooser',
        TWITTER_SOURCE_TYPE_CHOOSER: 'twitter-source-type-chooser',
        DBPERSON_SOURCE_TYPE_CHOOSER: 'dbperson-source-type-chooser',
        SOURCE_PANE_TWITTER_GET_DEALS: 'source-pane-twitter-get-deals',
        SOURCE_PANE_TWITTER_GET_USERS: 'source-pane-twitter-get-users',
        SOURCE_PANE_DBPERSON_GET_CLIENTS: 'source-pane-dbperson-get-clients'
    };

    var SourceCreationWizardStepTitles = {};
    SourceCreationWizardStepTitles[SourceCreationWizardSteps.SOURCE_TYPE_CHOOSER] = 'Add Lead Source';
    SourceCreationWizardStepTitles[SourceCreationWizardSteps.TWITTER_SOURCE_TYPE_CHOOSER] = 'Twitter';
    SourceCreationWizardStepTitles[SourceCreationWizardSteps.DBPERSON_SOURCE_TYPE_CHOOSER] = 'Company Database';
    SourceCreationWizardStepTitles[SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_DEALS] = 'Search By Keyword or Hashtag';
    SourceCreationWizardStepTitles[SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_USERS] = 'Search By Bios';
    SourceCreationWizardStepTitles[SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS] = 'Search Company Database';
    var SourceCreationWizardStepIcons = {};
    SourceCreationWizardStepIcons[SourceCreationWizardSteps.SOURCE_TYPE_CHOOSER] = '<i class="fa fa-database"></i>';
    SourceCreationWizardStepIcons[SourceCreationWizardSteps.TWITTER_SOURCE_TYPE_CHOOSER] = '<i class="fa fa-cliently-twitter"></i>';
    SourceCreationWizardStepIcons[SourceCreationWizardSteps.DBPERSON_SOURCE_TYPE_CHOOSER] = '<img src="/images/company.png" />';
    SourceCreationWizardStepIcons[SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_DEALS] = '<i class="fa fa-edit"></i>';
    SourceCreationWizardStepIcons[SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_USERS] = '<img src="/images/big-egg.png" />';
    SourceCreationWizardStepIcons[SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS] = '<img src="/images/company.png" />';

    var $wizard = null;
    var $saveButton = null;
    var $delButton = null;

    var step = null;
    var workflowId = null;
    var sourceId = null;
    var source = null;
    var sourceType = null;

    //Twitter Source Form
    var currentLocation = null;
    var $location = null;
    var $range = null;
    var $keywords = null;

    var $optionsPopup = null;

    var $twitterIntegrator = null;

    var initialized = false;
    function init() {
        $(document).on('click', '.column.source-column .source .source-header .switch', function (e) {
            e.stopPropagation();
        });
        $(document).on('change', '.column.source-column .source .source-header .switch input', function () {
            var input = $(this);
            var $source = $(this).closest('.source');
            var $workflow = $source.siblings('.workflow');
            var data = {};
            data.is_enabled = this.checked ? 1 : 0;
            var status = this.checked;
            $.ajax({
                method: 'PUT',
                url: '/api/v1/workflows/' + $workflow.data('workflow-id') + '/sources/' + $source.data('source-id'),
                data: data
            }).done(function () {
            }).fail(function (jqXHR) {
                if (jqXHR.status === 409) {
                    setTimeout(function () {
                        input.attr('checked', false);
                    }, 1000);
                    showErrorMessage('Error', 'Please upgrade your account to add additional lead sources or workflows.');
                }
            });
            $(this).parent().attr('title', this.checked ? 'Click to Pause Source.' : 'Click to make Source Live.')
                    .tooltip('fixTitle')
                    .tooltip('setContent')
                    .tooltip('show');
            if (this.checked) {
                $(this).closest('.source').removeClass('disabled');
            } else {
                $(this).closest('.source').addClass('disabled');
            }
        });

        $(window).resize(function () {
            if ($('#page-sources.main-block-wrapper').length) {
                resizeSourcesAndWorkflows();
            }
        });

        $(document).on('click', '.add-lead-source-li', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;

            popupSourceCreationWizard();

            return false;
        }).on('click', '.column.source-column .source .source-header', function () {
            var $source = $(this).closest('.source');
            if ($source.hasClass('empty')) {
                return;
            }
            if ($source.hasClass('collapsed')) {
                $source.removeClass('collapsed').find('.source-content').stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            } else {
                $source.find('.source-content').stop().fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                    $(this).closest('.source').addClass('collapsed');
                });
            }
        }).on('click', '.column.source-column .source .source-header .source-options-popup-toggle', function (e) {
            popupSourceOptions($(this).closest('.source').data('source'));
            e.stopPropagation();
        }).on('click', '.column.source-column .source .source-content', function () {
            var $source = $(this).parent();
            popupSourceCreationWizard(__type2step($source.attr('data-source-type')), $source.data('source'));
        }).on('click', '.column.source-column .source-workflow .btn-workflow-action-add', function () {
            $(this).closest('.source-workflow').find('.workflow .add-work').last().click();
        }).on('click', '#source-creation-wizard.modal ul.source-type-list li', function () {
            popupSourceCreationWizard($(this).data('next-step'));
        }).on('submit', '#source-creation-wizard.modal form', function () {
            var $xhr = saveSource($(this));
            if ($xhr) {
                var html = $saveButton.html();
                $saveButton.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> ' + (sourceId ? 'Updating...' : 'Adding...'));
                $xhr.done(function (ret) {
                    $saveButton.attr('disabled', false).html('<i class="fa fa-save"></i> ' + (sourceId ? 'Updated' : 'Added'));
                }).fail(function () {
                    $saveButton.attr('disabled', false).attr(html);
                });
            }
            return false;
        }).on('click', '#source-creation-wizard.modal button.btn-save', function () {
            $wizard.find('.source-creation-wizard-step[data-step="' + step + '"] form').submit();
        }).on('click', '#source-creation-wizard.modal button.btn-delete', function () {
            deleteSource();
        }).on('click', '#source-creation-wizard.modal button.btn-backward', function () {
            switch (step) {
                case SourceCreationWizardSteps.TWITTER_SOURCE_TYPE_CHOOSER:
                    popupSourceCreationWizard();
                    break;
                case SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_DEALS:
                case SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_USERS:
                    popupSourceCreationWizard(SourceCreationWizardSteps.TWITTER_SOURCE_TYPE_CHOOSER);
                    break;
                case SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS:
                    popupSourceCreationWizard();
                    break;
                default:
                    popupSourceCreationWizard();
            }
        }).on('focus', '#source-creation-wizard.modal .source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_DEALS + '"] .source-form input[name="location"]', function () {
            $(this).select();
        }).on('click', '#source-creation-wizard.modal .source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"] .filter-values .dbperson-values-container ul.dbperson-values li span.remove', function (e) {
            $wizard.find('.source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"] .filter-panes .dbperson-list-container .dbperson-list[data-dbperson-type="' + $(this).closest('.dbperson-values').attr('data-dbperson-type') + '"] li[data-value="' + $(this).closest('li').attr('data-value') + '"]>input:checkbox').click();
        }).on('click', '#source-creation-wizard.modal .source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"] .filter-values .dbperson-value-container .dbperson-value span.remove', function (e) {
            clearDbpersonValue($(this).closest('.dbperson-value').attr('data-dbperson-name'));
            resizeFormForFilterValues();
        }).on('click', '#source-creation-wizard.modal .source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"] .filter-panes .dbperson-list-container .dbperson-list input:checkbox', function (e) {
            if (!$('#source-creation-wizard.modal .source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"]').hasClass('step-loaded')) {
                return;
            }
            var $infoList = $(this).closest('.dbperson-list');
            setTimeout(function () {
                fetchDbpersonListValues($infoList);
            });
        }).on('click', '#source-creation-wizard.modal .source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"] .filter-panes .dbperson-list-container .form-control-static', function (e) {
            $(this).parent().toggleClass('show-dbperson-list');
        }).on('change', '#source-creation-wizard.modal .source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"] .filter-panes input[name]', function (e) {
            fetchDbpersonValue($(this));
            resizeFormForFilterValues();
        }).on('change', '#source-creation-wizard.modal .source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"] .filter-panes select[name]', function (e) {
            fetchDbpersonValue($(this));
            resizeFormForFilterValues();
        }).on('click', '#source-creation-wizard.modal .source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"] .filter-panes .navigators .navigator', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            $(this).closest('.filter-panes').toggleClass('show-more').find('.filter-pane-more').slideToggle();
            return false;
        }).on('submit', '#source-options-popup form.source-options-form', function () {
            saveOptions($(this));
            return false;
        }).on('click', '#source-options-popup button.btn-save', function () {
            $optionsPopup.find('form.source-options-form').submit();
        });

        initialized = true;
    }

    scope.load = function () {
        if (!initialized) {
            init();
        }

        loadWorkflowList();
    };

    function loadWorkflowList() {
        Workflow.loadWorkflowList(true, true).done(function (workflows) {
            $(".columns-container").hide();
            $.each(workflows, function (index, workflow) {
                var source = workflow.sources[0];
                if (!source) {
                    return;
                }
                source.workflowId = workflow.id;
                var $workflow = Workflow.$createWorkflow(workflow, false);
                $createSourceWorkflow(workflow.id).appendTo($createSourceColumn(workflow.id).appendTo($('.columns.source-columns')).find('.column-inner'))
                        .find('.source-workflow-content')
                        .append($createSource(source))
                        .append($workflow);
                if ($workflow.find('.work').length) {
                    $workflow.closest('.source-workflow').find('.btn-workflow-action-add').show();
                } else {
                    $workflow.closest('.source-workflow').find('.btn-workflow-action-add').hide();
                }
                if (workflow.is_enabled) {
                    $workflow.closest('.source-workflow').find('.btn-workflow-action-add').removeClass('disabled');
                } else {
                    $workflow.closest('.source-workflow').find('.btn-workflow-action-add').addClass('disabled');
                }
            });
            $(".columns-container").fadeIn();
            resizeSourceColumns();

            hideGlobalLoader();
            if (tour_step > TourSteps.LEFT_MENU && tour_step <= TourSteps.LAST) {
                openTour();
            }
        });

    }

    function $createSourceColumn(__workflowId) {
        var $column = $('<div class="column source-column" id="source-column-' + __workflowId + '" data-workflow-id="' + __workflowId + '"></div>');
        $('<div class="column-inner"></div>').appendTo($column);//.slimScroll({'height': '100%'});

        return $column;
    }
    function resizeSourceColumns() {
        Layout.resizeColumns($('#page-sources'), 25);
        resizeSourcesAndWorkflows();
    }

    function $createSourceWorkflow(__workflowId) {
        var $sourceWorkflow = $('\
            <div class="source-workflow" id="source-workflow-' + __workflowId + '" data-workflow-id="' + __workflowId + '">\
                <div class="source-workflow-inner">\
                    <div class="source-workflow-content-wrapper">\
                        <div class="source-workflow-content"></div>\
                    </div>\
                    <button class="btn-workflow-action-add"><i class="fa fa-plus"></i> Add</button>\
                </div>\
            </div>\
        ');

        return $sourceWorkflow;
    }
    function resizeSourcesAndWorkflows() {
        $('#page-sources.main-block-wrapper .columns').find('.source-workflow-content').each(function () {
            $(this).css('width', (parseFloat($(this).closest('.source-workflow-content-wrapper').width())) + 'px');
        });
    }

    function $createSource(source) {
        var $source = $('\
            <div class="source' + (source.is_enabled ? '' : ' disabled') + '" id="source-' + source.id + '" data-source-id="' + source.id + '">\
                <div class="source-header">\
                    <span class="icon">' + SourceCreationWizardStepIcons[source.module_class == SourceModuleTypes.TWITTER ? SourceCreationWizardSteps.TWITTER_SOURCE_TYPE_CHOOSER : SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS] + '</span>\
                    <h3>Lead Source</h3>\
                    <span class="toggle"><i class="fa"></i></span>\
                    <div class="source-options-popup-toggle">\
                        <i class="fa fa-gear"></i> Settings\
                        <label class="switch" data-toggle="tooltip" data-container="body" data-placement="top" title="' + (source.is_enabled ? 'Click to Pause Source.' : 'Click to make Source Live.') + '">\
                            <input type="checkbox"' + (source.is_enabled ? ' checked="checked"' : '') + '>\
                            <span data-on="On" data-off="Off"></span>\
                            <i></i>\
                        </label>\
                    </div>\
                </div>\
                <div class="source-content"></div>\
            </div>\
        ');
        $source.addClass('collapsed').find('.source-content').hide();
        fillSourceValues($source, source);

        return $source.data('source', source)
                .attr('id', 'source-' + source.id)
                .attr('data-source-id', source.id)
                .attr('data-source-module-type', source.module_class)
                .attr('data-source-type', source.class);
    }

    function fillSourceValues($source, source) {
        var values = source.values;
        switch (source.class) {
            case SourceTypes.TWITTER_GET_DEALS:
                var tagsHTML = '';
                if (values.keywords) {
                    $.each(values.keywords, function (i, keyword) {
                        tagsHTML += '<span class="fake-tagit-choice">' + keyword + '</span>';
                    });
                }
                $source.find('.source-content').html('\
                    <p>\
                        ' + values.location + '<br>\
                        ' + values.range + ' Miles<br>\
                        <span class="fake-tagit">' + tagsHTML + '</span>\
                    </p>\
                ');
                break;
            case SourceTypes.TWITTER_GET_USERS:
                var tagsHTML = '';
                if (values.keywords && Array.isArray(values.keywords)) {
                    $.each(values.keywords, function (i, keyword) {
                        tagsHTML += '<span class="fake-tagit-choice">' + keyword + '</span>';
                    });
                }
                $source.find('.source-content').html('\
                    <p>\
                        <span class="fake-tagit">' + tagsHTML + '</span>\
                    </p>\
                ');
                break;
            case SourceTypes.DBPERSON_GET_CLIENTS:
                var $content = $source.find('.source-content').empty();

                if (values.company_names && values.company_names.length > 0) {
                    $content.append($createDbpersonValue('Company Name', values.company_names ? '"' + values.company_names.join('" "') + '"' : ''));
                }
                if (values.employee_sizes && values.employee_sizes.length > 0) {
                    var sizes = Dbperson.getDbpersonNamesByValues('employee_sizes', values.employee_sizes);
                    $content.append($createDbpersonValue('Size', '"' + sizes.join('" "') + '"'));
                }
                if (values.revenues && values.revenues.length > 0) {
                    var revenues = Dbperson.getDbpersonNamesByValues('revenues', values.revenues);
                    $content.append($createDbpersonValue('Revenue', '"' + revenues.join('" "') + '"'));
                }
                if (values.title_roles && values.title_roles.length > 0) {
                    var roles = Dbperson.getDbpersonNamesByValues('title_roles', values.title_roles);
                    $content.append($createDbpersonValue('Role', '"' + roles.join('" "') + '"'));
                }
                if (values.title_seniorities && values.title_seniorities.length > 0) {
                    var seniorities = Dbperson.getDbpersonNamesByValues('title_seniorities', values.title_seniorities);
                    $content.append($createDbpersonValue('Seniority', '"' + seniorities.join('" "') + '"'));
                }
                if (values.industries && values.industries.length > 0) {
                    var industries = Dbperson.getDbpersonNamesByValues('industries', values.industries);
                    $content.append($createDbpersonValue('Industry', '"' + industries.join('" "') + '"'));
                }

                var countries = Dbperson.getDbpersonNamesByValues('locations', values.countries);
                var states = Dbperson.getDbpersonNamesByValues('locations', values.states);
                var metro_regions = Dbperson.getDbpersonNamesByValues('locations', values.metro_regions);

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
                if (locations) {
                    $content.append($createDbpersonValue('Location', locations));
                }

                if (values.location) {
                    $content.append($createDbpersonValue('City/Sate/ZIP/Country', values.location));
                }
                if (values.location_match_type) {
                    $content.append($createDbpersonValue('Mach Type', values.location_match_type));
                }
//                if (typeof values.daily_limit != 'undefined' && values.daily_limit !== null && values.daily_limit != '') {
//                    $content.append($createDbpersonValue('Lead Limit', values.daily_limit));
//                }
//                if (values.sorting) {
//                    $content.append($createDbpersonValue('Sorting', values.sorting));
//                }
                break;
        }
    }

    function $createDbpersonValue(title, value) {
        return $('<div class="field"><label>' + title + ':&nbsp;</label><span class="value">' + value + '</span></div>');
    }

    function popupSourceCreationWizard(__step, __source) {
        if (!__step) {
            __step = SourceCreationWizardSteps.SOURCE_TYPE_CHOOSER;
        }
        if (!$wizard) {
            $wizard = $('<div id="source-creation-wizard" class="modal fade" role="dialog">\
                            <div class="modal-dialog">\
                                <div class="modal-content">\
                                    <div class="modal-header">\
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>\
                                        <h4 class="modal-title"></h4>\
                                    </div>\
                                    <div class="modal-body"></div>\
                                    <div class="modal-footer">\
                                        <div class="left">\
                                            <button class="btn-backward"><img src="/images/arrow2.png" /></button>\
                                            <div class="social-integration-handler-container"></div>\
                                        </div>\
                                        <button type="button" class="btn btn-primary btn-save"><i class="fa fa-save"></i> Save</button>\
                                        <button type="button" class="btn btn-default btn-delete">Delete</button>\
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>');
            $saveButton = $wizard.find('button.btn-save');
            $delButton = $wizard.find('button.btn-delete');
        }
        source = __source;
        sourceId = __source ? __source.id : null;
        workflowId = __source ? __source.workflowId : null;

        sourceType = __step2type(__step);
        step = __step;
        $wizard.attr('data-step', __step);

        $wizard.find('.modal-header .modal-title').html(SourceCreationWizardStepIcons[step] + ' ' + SourceCreationWizardStepTitles[step]);

        var $step = $wizard.find('.source-creation-wizard-step[data-step="' + step + '"]');
        if (!$step.length) {
            $step = $createSourceCreationWizardStep(step).appendTo($wizard.find('.modal-body'));
            initSourceCreationWizardStep($step);
        }
        $step.removeClass('step-loaded');

        if (sourceType.indexOf(SourceModuleTypes.TWITTER) === 0) {
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

        resetSourceForm($step.find('form.source-form'));

        if ($wizard.is(':visible')) {
            $wizard.find('.source-creation-wizard-step:visible').hide();//.stop().fadeOut({duration: 500, queue: false}).slideUp(500);
            $step.show();//.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
        } else {
            $wizard.find('.source-creation-wizard-step').hide();
            $step.show();
            $wizard.modal('show');
        }

        if (sourceId) {
            $wizard.removeClass('adding-new-source').addClass('updating-source');
            $saveButton.attr('disabled', false).html('<i class="fa fa-save"></i> Update');
            $delButton.attr('disabled', false).html('Delete').show();
        } else {
            $wizard.removeClass('updating-source').addClass('adding-new-source');
            $saveButton.attr('disabled', false).html('<i class="fa fa-save"></i> Add');
            $delButton.attr('disabled', true).hide();
        }

        var $input = $step.find('input,textarea,select');
        if ($input.length) {
            $($input[0]).focus().select();
        }

        if (step == SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS) {
            resizeFormForFilterValues();
        }

        $step.addClass('step-loaded');
    }


    function $createSourceCreationWizardStep(st) {
        var $step = $('<div />', {
            class: 'source-creation-wizard-step',
            id: 'source-creation-wizard-step-' + st,
            'data-step': st
        });

        switch (st) {
            case SourceCreationWizardSteps.SOURCE_TYPE_CHOOSER:
                $step.append('\
                    <ul class="source-type-list">\
                        <li data-source-module-type="' + SourceModuleTypes.TWITTER + '" data-next-step="' + SourceCreationWizardSteps.TWITTER_SOURCE_TYPE_CHOOSER + '">\
                            <span class="icon">' + SourceCreationWizardStepIcons[SourceCreationWizardSteps.TWITTER_SOURCE_TYPE_CHOOSER] + '</span>\
                            <label>' + SourceCreationWizardStepTitles[SourceCreationWizardSteps.TWITTER_SOURCE_TYPE_CHOOSER] + '</label>\
                        </li>\
                        <li data-source-module-type="' + SourceModuleTypes.DBPERSON + '" data-next-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '">\
                            <span class="icon">' + SourceCreationWizardStepIcons[SourceCreationWizardSteps.DBPERSON_SOURCE_TYPE_CHOOSER] + '</span>\
                            <label>' + SourceCreationWizardStepTitles[SourceCreationWizardSteps.DBPERSON_SOURCE_TYPE_CHOOSER] + '</label>\
                        </li>\
                    </ul>\
                ');
                break;
            case SourceCreationWizardSteps.TWITTER_SOURCE_TYPE_CHOOSER:
                $step.append('\
                    <ul class="source-type-list">\
                        <li data-source-module-type="' + SourceModuleTypes.TWITTER + '" data-next-step="' + SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_DEALS + '">\
                            <span class="icon">' + SourceCreationWizardStepIcons[SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_DEALS] + '</span>\
                            <label>Tweets</label>\
                        </li>\
                        <li data-source-module-type="' + SourceModuleTypes.DBPERSON + '" data-next-step="' + SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_USERS + '">\
                            <span class="icon">' + SourceCreationWizardStepIcons[SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_USERS] + '</span>\
                            <label>Bios</label>\
                        </li>\
                    </ul>\
                ');
                break;
            case SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_DEALS:
                $step.append('\
                    <form class="form source-form form-horizontal">\
                        <div class="form-group">\
                            <div class="col-md-12">\
                                <input type="text" name="location" tabindex="1" class="form-control" placeholder="Enter the location" />\
                            </div>\
                        </div>\
                        <div class="form-group">\
                            <div class="col-md-6" style="padding-right: 2px;">\
                                <input type="number" tabindex="2" min="1" max="1500" name="range" class="form-control" placeholder="Enter search range" />\
                            </div>\
                            <div class="col-md-6" style="padding-left: 2px;"><span style="display: inline-block; padding-top: 8px; color: gray"><i>miles</i></span></div>\
                        </div>\
                        <div class="form-group">\
                            <div class="col-md-12">\
                                <ul class="tags"></ul>\
                            </div>\
                        </div>\
                    </form>\
                ');

                $location = $step.find('.source-form input[name="location"]');
                $range = $step.find('.source-form input[name="range"]');
                $keywords = $step.find('.source-form .tags');

                try {
                    $location.geocomplete();
                } catch (e) {
                }
                $keywords.tagit({
                    fieldName: "keywords",
                    caseSensitive: false,
                    allowDuplicates: false,
                    tagLimit: 100,
                    allowSpaces: true,
                    singleField: true,
                    singleFieldDelimiter: ',',
                    singleFieldNode: null,
                    tabIndex: 3,
                    placeholderText: 'Enter keywords or hashtags'
                });

                $.get("https://ipinfo.io?token=90e29d6880232f", function (response) {
                    currentLocation = response.city + ', ' + response.region + ', ' + response.country;
                    if ($location && $location.val() == '') {
                        $location.val(currentLocation);
                    }
                }, "jsonp");

                break;
            case SourceCreationWizardSteps.SOURCE_PANE_TWITTER_GET_USERS:
                $step.append('\
                    <form class="form source-form form-horizontal">\
                        <div class="form-group">\
                            <div class="col-md-12">\
                                <ul class="tags"></ul>\
                            </div>\
                        </div>\
                    </form>\
                ');

                $step.find('.source-form .tags').tagit({
                    fieldName: "keywords",
                    caseSensitive: false,
                    allowDuplicates: false,
                    tagLimit: 100,
                    allowSpaces: true,
                    singleField: true,
                    singleFieldDelimiter: ',',
                    singleFieldNode: null,
                    tabIndex: 3,
                    placeholderText: 'Search by topical interest, full name, company name, etc.'
                });
                break;
            case SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS:
                $step.append('\
                    <form class="form source-form">\
                        <div class="filter-values"></div>\
                        <div class="form-inner">\
                            <h3 class="form-section"><i class="fa fa-search"></i> Company/People</h3>\
                            <div class="company-people-filter-panes filter-panes">\
                                <div class="filter-pane">\
                                    <div class="row">\
                                        <div class="col-md-4">\
                                            <div class="form-group">\
                                                <input type="text" name="company_names" class="form-control" placeholder="Company:" />\
                                            </div>\
                                        </div>\
                                        <div class="col-md-4">\
                                            <div class="form-group">\
                                                <input type="text" class="form-control" name="person_title" placeholder="Job Title:" />\
                                            </div>\
                                        </div>\
                                        <div class="col-md-4">\
                                            <div class="form-group">\
                                                <div class="form-control role-list-container dbperson-list-container">\
                                                    <div class="form-control-static">Role:</div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="filter-pane">\
                                    <div class="row">\
                                        <div class="col-md-4">\
                                            <div class="form-group">\
                                                <div class="form-control seniority-list-container dbperson-list-container">\
                                                    <div class="form-control-static">Seniority:</div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-4">\
                                            <div class="form-group">\
                                                <div class="form-control industry-list-container dbperson-list-container">\
                                                    <div class="form-control-static">Industry:</div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-4">\
                                            <div class="form-group">\
                                                <div class="form-control size-list-container dbperson-list-container">\
                                                    <div class="form-control-static">Size:</div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="filter-pane">\
                                    <div class="row">\
                                        <div class="col-md-4">\
                                            <div class="form-group">\
                                                <div class="form-control revenue-list-container dbperson-list-container">\
                                                    <div class="form-control-static">Revenue:</div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                                <!--div class="navigators">\
                                    <a href="#" class="navigator less">Less...</a><a href="#" class="navigator more">More...</a>\
                                </div-->\
                            </div>\
                            <h3 class="form-section"><i class="fa fa-map-marker"></i> Location</h3>\
                            <div class="location-filter-panes filter-panes">\
                                <div class="filter-pane">\
                                    <div class="row">\
                                        <div class="col-md-4">\
                                            <div class="form-group">\
                                                <div class="form-control country-list-container dbperson-list-container">\
                                                    <div class="form-control-static">Location:</div>\
                                                </div>\
                                            </div>\
                                        </div>\
                                        <div class="col-md-3">\
                                            <div class="form-group location-input-container">\
                                                <label> - OR - </label>\
                                                <input type="text" name="location" class="form-control input-inline" placeholder="City/State/ZIP/Country:" />\
                                            </div>\
                                        </div>\
                                    <!--/div>\
                                </div>\
                                <div class="filter-pane">\
                                    <div class="row"-->\
                                        <div class="col-md-5">\
                                            <ul class="location-match-type-list">\
                                                <li><input type="radio" name="location_match_type" value="PersonOrHQ" id="sources-location-match-type-person-or-hq" /><label for="sources-location-match-type-person-or-hq">Either the Person or Company HQ is at selected location </label></li>\
                                                <li><input type="radio" name="location_match_type" value="PersonAndHQ" id="sources-location-match-type-person-and-hq" /><label for="sources-location-match-type-person-and-hq">Both the Person or Company HQ are at selected location </label></li>\
                                                <li><input type="radio" name="location_match_type" value="Person" id="sources-location-match-type-person" /><label for="sources-location-match-type-person">Person is at selected location </label></li>\
                                                <li><input type="radio" name="location_match_type" value="HQ" id="sources-location-match-type-hq" /><label for="sources-location-match-type-hq">Company HQ is at selected location </label></li>\
                                            </ul>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                            <!--h3 class="form-section"><i class="fa fa-gear"></i> Options</h3>\
                            <div class="filter-panes option-filter-panes" data-pane="1" data-min-pane="1" data-max-pane="1">\
                                <div class="row">\
                                    <div class="col-md-3">\
                                        <div class="form-group">\
                                            <input type="number" name="daily_limit" class="form-control" placeholder="# Leads per day" min="0" max="100" />\
                                        </div>\
                                    </div>\
                                    <div class="col-md-3">\
                                        <div class="form-group">\
                                            <select class="form-control" name="sorting" placeholder="Sorting">\
                                                <option value="">-- Sorting--</option>\
                                                <option value="Name">Name</option>\
                                                <option value="EmployeeCount">EmployeeCount</option>\
                                                <option value="Revenue">Revenue</option>\
                                            </select>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div-->\
                        </div>\
                    </form>\
                ');

                $step.find('>form>.form-inner').slimScroll({
                    height: '100%'
                });

                break;
        }

        return $step;
    }

    function initSourceCreationWizardStep($step) {
        $step.hide();
        switch ($step.attr('data-step')) {
            case SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS:
                fillDbperson();

                break;
        }
        $step.show();
    }

    function resetSourceForm($form) {
        var __source = source ? source : createDefaultSource();

        switch (sourceType) {
            case SourceTypes.TWITTER_GET_DEALS:
                $location.val(__source.values.location);
                $range.val(__source.values.range);
                $keywords.tagit('removeAll');
                var keywords = __source.values.keywords;
                if (keywords && keywords.length > 0) {
                    for (var i = 0; i < keywords.length; i++) {
                        $keywords.tagit('createTag', keywords[i]);
                    }
                }
                break;
            case SourceTypes.TWITTER_GET_USERS:
                var $tags = $form.find('ul.tags');
                $tags.tagit('removeAll');
                var keywords = __source.values.keywords;
                if (keywords && keywords.length > 0) {
                    for (var i = 0; i < keywords.length; i++) {
                        $tags.tagit('createTag', keywords[i]);
                    }
                }
                break;
            case SourceTypes.DBPERSON_GET_CLIENTS:
                $form.find('.filter-values').empty();
                var $company_names = $form.find('input[name="company_names"]').val(__source.values.company_names ? (typeof __source.values.company_names == 'string' ? __source.values.company_names : __source.values.company_names[0]) : '');
                fetchDbpersonValue($company_names);
                var $person_title = $form.find('input[name="person_title"]').val(__source.values.person_title);
                fetchDbpersonValue($person_title);

                var $sizeList = $form.find('.filter-panes .dbperson-list[data-dbperson-type="size"]');
                var $sizeBonsai = $sizeList.data('bonsai');
                $sizeBonsai.collapseAll();
                $sizeBonsai.setCheckedValues(__source.values.employee_sizes ? __source.values.employee_sizes : []);
                fetchDbpersonListValues($sizeList);

                var $revenueList = $form.find('.filter-panes .dbperson-list[data-dbperson-type="revenue"]');
                var $revenueBonsai = $revenueList.data('bonsai');
                $revenueBonsai.collapseAll();
                $revenueBonsai.setCheckedValues(__source.values.revenues ? __source.values.revenues : []);
                fetchDbpersonListValues($revenueList);

                var $roleList = $form.find('.filter-panes .dbperson-list[data-dbperson-type="role"]');
                var $roleBonsai = $roleList.data('bonsai');
                $roleBonsai.collapseAll();
                $roleBonsai.setCheckedValues(__source.values.title_roles ? __source.values.title_roles : []);
                fetchDbpersonListValues($roleList);

                var $seniorityList = $form.find('.filter-panes .dbperson-list[data-dbperson-type="seniority"]');
                var $seniorityBonsai = $seniorityList.data('bonsai');
                $seniorityBonsai.collapseAll();
                $seniorityBonsai.setCheckedValues(__source.values.title_seniorities ? __source.values.title_seniorities : []);
                fetchDbpersonListValues($seniorityList);

                var $industryList = $form.find('.filter-panes .dbperson-list[data-dbperson-type="industry"]');
                var $industryBonsai = $industryList.data('bonsai');
                $industryBonsai.collapseAll();
                $industryBonsai.setCheckedValues(__source.values.industries ? __source.values.industries : []);
                fetchDbpersonListValues($industryList);

                var $countryList = $form.find('.filter-panes .dbperson-list[data-dbperson-type="country"]');
                $countryList.data('bonsai').collapseAll();
                var all = $countryList.find('li input[type=checkbox]').prop('checked', false).prop('indeterminate', false);
                if (__source.values.countries) {
                    $.each(__source.values.countries, function (key, value) {
                        all.filter('li[data-depth="1"] [value="' + value + '"]')
                                .prop('checked', true)
                                .trigger('change');
                    });
                }
                if (__source.values.states) {
                    $.each(__source.values.states, function (key, value) {
                        all.filter('li[data-depth="2"] [value="' + value + '"]')
                                .prop('checked', true)
                                .trigger('change');
                    });
                }
                if (__source.values.metro_regions) {
                    $.each(__source.values.metro_regions, function (key, value) {
                        all.filter('li[data-depth="3"] [value="' + value + '"]')
                                .prop('checked', true)
                                .trigger('change');
                    });
                }
                fetchDbpersonListValues($countryList);

                var $loc = $form.find('input[name="location"]').val(__source.values.location);
                fetchDbpersonValue($loc);
                var $match_type = $form.find('input[name="location_match_type"]').prop('checked', false).filter('[value="' + __source.values.location_match_type + '"]').prop('checked', true);
                fetchDbpersonValue($match_type);

//                var $daily_limit = $form.find('input[name="daily_limit"]').val(__source.values.daily_limit);
//                fetchDbpersonValue($daily_limit);
//                var $sorting = $form.find('select[name="sorting"]').val(__source.values.sorting);
//                fetchDbpersonValue($sorting);

                $form.find('.filter-panes .dbperson-list-container').removeClass('show-dbperson-list');

                break;
        }
    }

    function fetchDbpersonValue($element) {
        var name = $element.attr('name');
        if (!name) {
            return;
        }
        var $step = $wizard.find('.source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"]');
        var $value = $step.find('.filter-values .dbperson-value[data-dbperson-name="' + name + '"]');
        var value = $element.val();
        if (!$value.length) {
            if (!value) {
                return;
            }
            var $valueContainer = $createFormValueInFilterValues();
            var title = null;
            switch (name) {
                case 'company_names':
                    title = 'Company: ';
                    break;
                case 'person_title':
                    title = 'Job Title: ';
                    break;
                case 'location':
                    title = 'City/State/ZIP/Country: ';
                    break;
                case 'location_match_type':
                    title = 'Location Match Type: ';
                    break;
//                case 'sorting':
//                    title = 'Sorting: ';
//                    break;
//                case 'daily_limit':
//                    title = 'Lead Limit: ';
//                    break;
            }
            $valueContainer.addClass('dbperson-value-container').find('>label').html(title);
            $value = $('<ul />').addClass('dbperson-value')
                    .attr('data-dbperson-name', name)
                    .appendTo($valueContainer);

        }
        if (value) {
            $value.html('<li><label>' + value + '</label><span class="remove"><i class="fa fa-times"></i></span></li>');
        } else {
            $value.closest('.col-md-2').remove();
        }
    }

    function clearDbpersonValue(name) {
        var $element = $wizard.find('.source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"] form.source-form [name="' + name + '"]');
        switch (name) {
            case 'company_names':
            case 'person_title':
            case 'location':
                $element.val('');
                break;
            case 'location_match_type':
                $element.prop('checked', false);
                break;
//            case 'sorting':
//                $element.val('');
//                break;
//            case 'daily_limit':
//                $element.val('');
//                break;
        }
        fetchDbpersonValue($element.eq(0));
    }

    function resizeFormForFilterValues() {
        setTimeout(function () {
            var $step = $wizard.find('.source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"]');
            $step.find('form.source-form>.slimScrollDiv>.form-inner').css('padding-top', $step.find('.filter-values').height());
        }, 200);
    }

    function $createFormValueInFilterValues() {
        var $step = $wizard.find('.source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"]');
        var $row = $step.find('.filter-values>.row');
        if (!$row.length) {
            $row = $('<div class="row" />').appendTo($step.find('.filter-values')).after('<hr />');
        }
        return $('\
            <div class="col-md-2">\
                <label></label>\
            </div>\
        ').appendTo($row);
    }

    function createDefaultSource() {
        var __source = {values: {}};
        switch (sourceType) {
            case SourceTypes.TWITTER_GET_DEALS:
                __source.values.location = currentLocation;
                __source.values.range = '';
                __source.values.keywords = null;
                break;
            case SourceTypes.TWITTER_GET_USERS:
                __source.values.keywords = null;
                break;
            case SourceTypes.EVENT_TIME_DELAY:
                __source.values.location = '';
                __source.values.location_match_type = '';
//                __source.values.sorting = '';
                break;
        }

        return __source;
    }

    function __type2step(cls) {
        return 'source-pane-' + cls.replace(/_/g, '-');
    }
    function __step2type(st) {
        if (st.indexOf('source-pane-') === -1) {
            return '';
        }
        return st.replace('source-pane-', '').replace(/\-/g, '_');
    }

    function closeWizardWithDelay() {
        setTimeout(function () {
            $wizard.modal('hide');
        }, 100);

    }

    function saveSource($form) {
        var values = $form.serializeObject();

        switch (sourceType) {
            case SourceTypes.TWITTER_GET_DEALS:
                values.location = $location.val();
                values.range = $range.val();
                values.keywords = $keywords.tagit('assignedTags');

                if (!values.location || !values.range || !values.keywords.length) {
                    showErrorMessage('Lead Source', 'Please enter location, range and keywords.');
                    return false;
                }
                break;
            case SourceTypes.TWITTER_GET_USERS:
                values.keywords = $form.find('ul.tags').tagit('assignedTags');

                if (!values.keywords.length) {
                    showErrorMessage('Lead Source', 'Please enter keywords.');
                    return false;
                }
                break;
            case SourceTypes.DBPERSON_GET_CLIENTS:
                if (values.company_names) {
                    values.company_names = [values.company_names];
                } else {
                    delete values.company_names;
                }
                $form.find('.filter-values ul.dbperson-values').each(function () {
                    var names = $(this).attr('data-dbperson-name').split(',');
                    $(this).children().each(function () {
                        var value = $(this).attr('data-value');
                        var index = names.length > 1 ? $(this).attr('data-depth') - 1 : 0;
                        if (typeof values[names[index]] == 'undefined') {
                            values[names[index]] = [];
                        }
                        values[names[index]].push(value);
                    });
                });
                break;
        }

        if (sourceId > 0 && !confirm("Your old search will no longer run. Please confirm these changes.")) {
            return;
        }

        if (sourceType == SourceTypes.TWITTER_GET_DEALS) {
            values.coords = getCoords(values.location);
        }

        if (sourceId) {
            return $.ajax({
                method: "PUT",
                url: '/api/v1/workflows/' + workflowId + '/sources/' + sourceId,
                data: {values: values},
                success: function (ret) {
                    showSuccessMessage("Success", "The lead source has been updated successfully.");
                    closeWizardWithDelay();
                    var $source = $('#source-' + sourceId).effect("highlight", {color: "#99ff99"}, 3000);
                    source.values = values;
                    fillSourceValues($source, source);
                    trackAnalytics('Updated Lead Source', values);
                }
            });
        } else {
            var xhr = $.post('/api/v1/workspaces/' + current_workspace_id + '/workflows', {
                source: {
                    values: values,
                    class: sourceType
                }
            }, function (workflow) {
                showSuccessMessage("Success", "A lead source has been added successfully.");
                closeWizardWithDelay();
                var source = workflow.sources[0];
                source.workflowId = workflow.id;
                var $workflow = Workflow.$createWorkflow(workflow, false);
                $createSourceWorkflow(workflow.id).appendTo($createSourceColumn(workflow.id).appendTo($('.columns.source-columns')).find('.column-inner'))
                        .find('.source-workflow-content')
                        .append($createSource(source))
                        .append($workflow).effect("highlight", {color: "#99ff99"}, 3000);
                if ($workflow.find('.work').length) {
                    $workflow.closest('.source-workflow').find('.btn-workflow-action-add').show();
                } else {
                    $workflow.closest('.source-workflow').find('.btn-workflow-action-add').hide();
                }

                resizeSourceColumns();
                trackAnalytics('Added Lead Source', values);

            });

            xhr.fail(function (jqXHR) {
                if (jqXHR.status === 409) {
                    showErrorMessage('Error', 'Please upgrade your account to add additional lead sources or workflows.');
                    closeWizardWithDelay();
                }
            });

            return xhr;
        }

        return false;
    }

    function deleteSource() {
        bootbox.confirm('Are you sure you want to delete the lead source?  This will also delete the workflow below it.',
                function (result) {
                    if (result) {
                        $delButton.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Deleting...');
                        $.ajax({
                            type: 'delete',
                            url: '/api/v1/workflows/' + workflowId,
                            success: function () {
                                showSuccessMessage("Workflow", "The lead source has been deleted successfully.");
                                $delButton.html('Deleted');
                                closeWizardWithDelay();
                                $('#source-column-' + workflowId).fadeOut(function () {
                                    $(this).remove();
                                    resizeSourceColumns();
                                });
                            }
                        });
                    }
                });
    }

    function getCoords(location) {
        var coords = null;
        if (location) {
            $.ajax({
                url: "https://maps.googleapis.com/maps/api/geocode/json?address=" + location,
                async: false,
                dataType: 'json',
                success: function (res) {
                    if (res.results[0].geometry) {
                        coords = res.results[0].geometry.location.lat + "," + res.results[0].geometry.location.lng;
                    }
                }
            });
        }

        return coords;
    }

    function trackAnalytics(title, values) {
        if (typeof analytics !== 'undefined' && analytics !== null) {
            analytics.track(title, {
                keywords: values.keywords.join(' | '),
                location: values.location,
                range: values.range
            });
        }
    }

    function popupSourceOptions(__source) {
        source = __source;
        sourceId = __source.id;
        workflowId = __source.workflowId;

        if (!$optionsPopup) {
            $optionsPopup = $('\
                <div id="source-options-popup" class="modal fade" role="dialog">\
                    <div class="modal-dialog">\
                      <div class="modal-content">\
                        <div class="modal-header">\
                          <button type="button" class="close" data-dismiss="modal">&times;</button>\
                          <h4 class="modal-title"><i class="fa fa-gear"></i> Lead Source Options</h4>\
                        </div>\
                        <div class="modal-body">\
                            <form class="form source-options-form">\
                                <input type="text" name="daily_limit" class="form-control" placeholder="# Leads per day" />\
                                <div class="help-block">\
                                    Set your daily limit for how many leads this source will provide each day.<br />\
                                    * There is a 100 lead maximum per day.\
                                </div>\
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
        }

        $optionsPopup.find('input[name="daily_limit"]').val(typeof source.values.daily_limit != 'undefined' ? source.values.daily_limit : '');

        $optionsPopup.find('button.btn-save').attr('disabled', false).html('<i class="fa fa-save"></i> Save');
        $optionsPopup.modal('show');

    }

    function saveOptions($form) {
        var params = $form.serializeObject();
        var values = {};
        $.each(source.values, function (name, value) {
            values[name] = value;
        })
        values.daily_limit = params.daily_limit;
        $optionsPopup.find('button.btn-save').attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Saving...');
        $.ajax({
            method: "PUT",
            url: '/api/v1/workflows/' + workflowId + '/sources/' + sourceId,
            data: {values: values},
            success: function (ret) {
//                if (!ret || ret.code != 0) {
//                    showErrorMessage('Lead Source', 'Error occurred while saving options.');
//                    return;
//                }

                showSuccessMessage("Success", "The options have been saved successfully.");
                $optionsPopup.find('button.btn-save').html('<i class="fa fa-save"></i> Saved');
                source.values.daily_limit = params.daily_limit;
                $optionsPopup.delay(100).modal('hide');
            }
        });
    }

    //Dbperson
    function fetchDbpersonListValues($infoList) {
        var $step = $wizard.find('.source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"]');
        var field = $infoList.attr('data-dbperson-type');
        var infos = [];
        $infoList.find('input:checkbox:checked:not(indeterminate)').each(function (i) {
            var $item = $(this).closest('li');
            var parents = $item.attr('data-parents');
            if (!parents && field == 'country') {
                return;
            }
            for (var i = 0; i < infos.length; i++) {
                if (parents.indexOf(infos[i].value) > -1) {
                    return;
                }
            }
            infos.push({value: $item.attr('data-value'), title: $item.find('label').html(), depth: $item.attr('data-depth')});
        });

        var $values = $step.find('.filter-values .dbperson-values[data-dbperson-type="' + field + '"]');
        if (!$values.length) {
            if (!infos.length) {
                return;
            }
            var $valuesContainer = $createFormValueInFilterValues();
            $valuesContainer.addClass('dbperson-values-container').find('>label').html($infoList.closest('.dbperson-list-container').find('.form-control-static').html());
            $values = $('<ul />').addClass('dbperson-values')
                    .attr('data-dbperson-type', field)
                    .attr('data-dbperson-name', $infoList.attr('data-dbperson-name'))
                    .appendTo($valuesContainer);

            $values.slimScroll({height: '100px', size: '4px'});
        }
        $values.empty();

        if (infos && infos.length) {
            $.each(infos, function (i, info) {
                $('<li />').attr('data-value', info.value).attr('data-depth', info.depth).html('<label>' + info.title + '</label><span class="remove"><i class="fa fa-times"></i></span>').appendTo($values);
            });
        } else {
            $values.closest('.col-md-2').remove();

        }
    }

    //fillDbperson();
    function fillDbperson() {
        var dbperson = Dbperson.getDbperson();
        var $step = $wizard.find('.source-creation-wizard-step[data-step="' + SourceCreationWizardSteps.SOURCE_PANE_DBPERSON_GET_CLIENTS + '"]');
        if ($step.hasClass('dbperson-added')) {
            return;
        }

        var $roleList = $('<ul />').attr('data-dbperson-name', 'title_roles').addClass('dbperson-list').attr('data-dbperson-type', 'role').appendTo($step.find('.role-list-container'));
        fillDbpersonValues($roleList, dbperson.title_roles);
        $roleList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $roleList.slimScroll({height: '200px', size: '4px'});

        var $seniorityList = $('<ul />').attr('data-dbperson-name', 'title_seniorities').addClass('dbperson-list').attr('data-dbperson-type', 'seniority').appendTo($step.find('.seniority-list-container'));
        fillDbpersonValues($seniorityList, dbperson.title_seniorities);
        $seniorityList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $seniorityList.slimScroll({height: '200px', size: '4px'});

        var $industryList = $('<ul />').attr('data-dbperson-name', 'industries').addClass('dbperson-list').attr('data-dbperson-type', 'industry').appendTo($step.find('.industry-list-container'));
        fillDbpersonValues($industryList, dbperson.industries);
        $industryList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $industryList.slimScroll({height: '200px', size: '4px'});

        var $sizeList = $('<ul />').attr('data-dbperson-name', 'employee_sizes').addClass('dbperson-list').attr('data-dbperson-type', 'size').appendTo($step.find('.size-list-container'));
        fillDbpersonValues($sizeList, dbperson.employee_sizes);
        $sizeList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $sizeList.slimScroll({height: '200px', size: '4px'});

        var $revenueList = $('<ul />').attr('data-dbperson-name', 'revenues').addClass('dbperson-list').attr('data-dbperson-type', 'revenue').appendTo($step.find('.revenue-list-container'));
        fillDbpersonValues($revenueList, dbperson.revenues);
        $revenueList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $revenueList.slimScroll({height: '200px', size: '4px'});

        var $countryList = $('<ul />').attr('data-dbperson-name', 'countries,states,metro_regions').addClass('dbperson-list').attr('data-dbperson-type', 'country').appendTo($step.find('.country-list-container'));
        fillDbpersonValues($countryList, dbperson.locations);
        $countryList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $countryList.slimScroll({height: '200px', size: '4px'});

        $step.addClass('dbperson-added');
    }

    function fillDbpersonValues($infoList, infoList, parents, depth) {
        $.each(infoList, function (i, info) {
            if (typeof info != 'object') {
                info = {name: info, code: info};
            } else if (Array.isArray(info)) {
                info = {code: info[0], name: info[1], children: info[2]};
            }
            if (typeof info.code == 'undefined' || !info.code) {
                info.code = info.name;
            }
            if (typeof parents == 'undefined') {
                parents = '';
            }
            if (typeof depth == 'undefined') {
                depth = 0;
            }
            var $info = $('<li>').html(info.name)
                    .attr('data-value', info.code)
                    .attr('data-parents', parents)
                    .attr('data-depth', depth)
                    .appendTo($infoList);

            if (info.children) {
                fillDbpersonValues($('<ul/>').appendTo($info), info.children, parents + info.code + '-', depth + 1);
            }
        });
    }

    return scope;
})();