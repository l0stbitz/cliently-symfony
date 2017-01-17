var Search = (function () {
    var scope = {};
    var SearchTypes = {
        LEADS: 'leads',
        TWITTER_TWEET: 'twitter_tweet',
        DATABASE: 'dbperson_user'
    };

    var $container = $(".page-quick-searchbar");
    var $q = $('#search_input');

    $container.find('ul.dropdown-menu>li>a').click(function (e) {
        var $this = $(this);
        selectSearchType(this.href.substr(this.href.indexOf('#') + 1), $('#page_search'));
        $this.closest('.dropdown').find('[data-toggle] .search-mode-icon').html($this.html()).end().removeClass('open');
        return false;
    });

    scope.load = function () {
        initLeadSearch();
        initSourceSearch();

        // var no = 1;
        // $container.find('ul.dropdown-menu>li:nth-child(' + no + ')>a').click();

        selectSearchType(SearchTypes.LEADS);
    };

    scope.initLeadSearch = function () {
        initLeadSearch();
    };

    function selectSearchType(mode, dom_container) {
        if (mode == SearchTypes.LEADS) {
            $q.off('keydown.search.sources')
                .off('keydown.search.people')
                .off('click.search.people')
                .autocomplete('enable')
                .attr('placeholder', 'Search...')
                .on('keyup.search.leads', function () {
                    var keywords = $(this).val();
                    $('.portlet').each(function () {
                        if ($(this).parents('.workflow-page').length === 0) {
                            var text_to_search = $(this).find('.portlet-header h1').text();
                            if (text_to_search.search(new RegExp(keywords, "i"))) {
                                $(this).fadeOut();
                            } else {
                                $(this).fadeIn();
                            }
                        }
                    });
                }).focus();
        } else if (mode == SearchTypes.TWITTER_TWEET) {
            $q.off('keyup.search.leads')
                    .off('keydown.search.people')
                    .off('click.search.people')
                    .css('cursor', 'default')
                    .attr('readonly', false)
                    .attr('placeholder', 'Search Twitter and hit Enter...')
                    .on('keydown.search.sources', function (e) {
                        if (e.keyCode == 13 && this.value) {
                            showSourceSearchResult();
                        } else {
                            hideSourceSearchResult();
                        }
                    }).focus();
        } else if (mode == SearchTypes.DATABASE) {
            $q.off('keyup.search.leads')
                    .off('keydown.search.sources')
                    .val('')
                    .css('cursor', 'pointer')
                    .attr('readonly', true)
                    .attr('placeholder', 'Search by Company name...')
                    .on('keydown.search.people', function (e) {
                        if (e.keyCode == 13) {
                            showPeopleSearchResult(dom_container);
                        }
                    })
                    .on('click.search.people', function (e) {
                        showPeopleSearchResult(dom_container);
                    });
        }
    }

    function initLeadSearch() {
        /** AUTOCOMPLETE **/
        $('#search_input').autocomplete({
            'appendTo': $('#search_input').parent(),
            source: function (request, response) {
                var params = {
                    q: request.term
                };
                if (current_owner_id != null && current_owner_id !== false) {
                    params.owner_id = current_owner_id;
                }
                $.ajax({
                    url: '/api/v1/pipelines/' + current_pipeline_id + '/deals/search',
                    mode: "GET",
                    // dataType: "json",
                    data: params,
                    success: function (data) {
                        response($.map(data, function (el) {
                            return {
                                label: el.clients[0].name,
                                value: el.clients[0].name
                            };
                        }));
                    }
                });
            }
        }).data("ui-autocomplete")
        ._renderItem = function (ul, item) {
            var matcher = new RegExp("^" + this.term, "i"),
                    template = "<span class='highlight--bold'>" + this.term + "</span>",
                    label = item.label.replace(matcher, template);

            return $("<li>").append("<a>" + label + "</a>").appendTo(ul);
        };

        $('#search_input').parent().on('click', 'ul.ui-autocomplete li.ui-menu-item>a', function () {
            if ($('#workflowpageT').length) {
                activatePage(Pages.MAIN_DASHBOARD);
            }
            var keywords = $(this).text();
            setTimeout(function () {
                $('.portlet').each(function () {
                    if ($(this).parents('.workflow-page').length === 0) {
                        text_to_search = $(this).find('.portlet-header h1').text();
                        if (text_to_search.search(new RegExp(keywords, "i"))) {
                            $(this).fadeOut();
                        } else {
                            $(this).fadeIn();
                        }
                    }
                })
            }, 1000);
        });

        $('#search_input').autocomplete('enable').on('keyup.search.leads', function () {
            var keywords = $(this).val();
            $('.portlet').each(function () {
                if ($(this).parents('.workflow-page').length === 0) {
                    var text_to_search = $(this).find('.portlet-header h1').text();
                    if (text_to_search.search(new RegExp(keywords, "i"))) {
                        $(this).fadeOut();
                    } else {
                        $(this).fadeIn();
                    }
                }
            });
        });
    }

    function initSourceSearch() {
        $addSourceSearchResultPane();

        $container.on('click', '#twitter-search-result', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            return false;
        }).on('click', '#twitter-search-result ul.search-result-list li.search-result-list-item button.btn-accept', function () {
            if ($(this).hasClass('btn-workflow-activation')) {
                showWorkflowDropdown($(this).closest('.form-actions'));
                return;
            }
            acceptSourceLead($(this).closest('li.search-result-list-item'));
        }).on('click', '#twitter-search-result ul.search-result-list li.search-result-list-item button.btn-reject', function () {
            rejectSourceLead($(this).closest('li.search-result-list-item'));
        }).on('click', '#twitter-search-result ul.search-result-list li.search-result-list-item .form-actions ul.workflow-list>li.workflow-list-item>a', function (e) {
            var $item = $(this).closest('.workflow-list-item');
            if ($item.hasClass('loading')) {
                return;
            }
            acceptSourceLead($(this).closest('li.search-result-list-item'), $(this).parent().attr('data-workflow-id'));
        });
    }

    function $addSourceSearchResultPane() {
        var $result = $('<div id="twitter-search-result" class="search-result dropdown-menu">\
                    <div class="twitter-search-result-inner">\
                        <ul class="search-result-list"></ul>\
                    </div>\
                </div>').appendTo($container);

        return $result;
    }

    function showSourceSearchResult() {
        var $result = $container.find('#twitter-search-result');
        if (!$result.length) {
            $result = $addSourceSearchResultPane();
        }

        var $list = $result.hide().find(".search-result-list").empty().append('\
            <li class="search-result-list-item loading">\
                <div class="search-result-list-item-inner">\
                    <span class="icon"><i class="fa fa-spin fa-spinner"></i></span>\
                    <p>Searching ...</p>\
                </div>\
            </li>\
        ');
        discoverDeals(SearchTypes.TWITTER_TWEET, $.trim($q.val())).done(function (data) {
            $list.hide().empty();
            var deals = data.results != null && data.results.length ? data.results : null;
            if (!deals && DevOptions.showSampleData) {
                data = [{
                        description: 'abc test testsetest ssfasdfasdfsafasfsafsafasf',
                        match_values: {
                            keywords: 'abc'
                        }
                    }];
            }
            if (deals) {
                $.each(deals, function (index, item) {
                    $('\
                        <li class="search-result-list-item">\
                            <div class="search-result-list-item-inner">\
                                <span class="icon"><i class="fa fa-cliently-twitter"></i></span>\
                                <div class="form-actions">\
                                    <button class="btn btn-success btn-sm btn-accept">Accept</button>\
                                    <button class="btn btn-warning btn-accept btn-workflow-activation"><small>ADD TO</small><br />Workflow</button>\
                                    <br />\
                                    <button class="btn btn-danger btn-sm btn-reject">Reject</button>\
                                </div>\
                                <p>' + decorateDealSourceText(item.description, item.match_values.keywords, 120) + '</p>\
                            </div>\
                        </li>\
                    ').data('deal', item).hide().appendTo($list).fadeIn({duration: 500, queue: false}).hide().slideDown(500);
                });
            } else {
                $list.append('\
                    <li class="search-result-list-item loading">\
                        <div class="search-result-list-item-inner">\
                            <span class="icon"><i class="fa fa-times"></i></span>\
                            <p>No matches found.</p>\
                        </div>\
                    </li>\
                ');
            }
            $list.show();
        });

        $result.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
        $(document).on('click.search.sources.toggle', function () {
            hideSourceSearchResult();
        });
    }

    var hideSourceSearchResult = function () {
        $container.find('#twitter-search-result').fadeOut('fast');
        $(document).off('click.search.sources.toggle');
    };

    var acceptSourceLead = function ($deal, workflowId) {
        if (typeof workflowId == 'undefined') {
            workflowId = null;
        }

        var deal = $deal.data('deal');
        var $btn = $deal.find('button.btn-accept:nth-child(' + (workflowId ? 2 : 1) + ')');
        var html = $btn.html();
        $btn.html('<i class="fa fa-spin fa-spinner"></i>');
        $deal.find('.form-actions button').attr('disabled', true);

        var data = {type: deal.type, match_values: deal.match_values, workspace_id: current_workspace_id};
        var activate_deal_workflow = false;
        if (workflowId) {
            data.workflow_id = workflowId;
            activate_deal_workflow = true;
        }

        $.post('/api/v1/leads/discover/' + deal.code + '/accept', data, function (lead) {
            var $card = Leads.$insertClient(lead).effect("highlight", {color: "#99ff99"}, 3000);
            var verify_needed = true;
            if (verify_needed) {
                Leads.verifyLead($card, activate_deal_workflow);
            } else {
                if (activate_deal_workflow) {
                    Workflow.setInitialDealWorkflowIsEnabled(lead.id, 'parent');
                }
            }
        }).done(function () {
            $btn.html('Accepted');
            setTimeout(function () {
                $deal.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                    $(this).remove();
                });
            }, 100);
        }).fail(function () {
            $btn.html(html);
            $deal.find('.form-actions button').attr('disabled', false);
        });
    };

    var rejectSourceLead = function ($deal) {
        var deal = $deal.data('deal');
        $deal.find('button.btn-reject').html('<i class="fa fa-spin fa-spinner"></i>').attr('disabled', true);
        $.post('/api/v1/leads/discover/' + deal.code + '/reject', {type: deal.type, match_values: deal.match_values, workspace_id: current_workspace_id}, function (data) {
            $deal.find('button.btn-reject').html('Rejected').attr('disabled', false);
            setTimeout(function () {
                $deal.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                    $(this).remove();
                });
            }, 500);
        });

    };
    //</editor-fold>

    //<editor-fold desc="People Search">
    scope.initPeopleSearch = function(dom_container) {
        dom_container.find('form.dbperson-search-form>.form-inner').slimScroll({
            height: '100%',
        });
        dom_container.find('div.search-result ul.search-result-list').slimScroll({
            height: '100%'
        });
        dom_container.find('div.search-result ul.search-result-list').on('scroll', function() {
            var $list = $(this);
            var scroll_amount = $list.scrollTop();
            if ($list.hasClass('scrolltrack') && scroll_amount + $list.innerHeight() >= $list[0].scrollHeight - 100) {
                $list.removeClass('scrolltrack');
                $list.append('\
                    <li class="search-result-list-item loading search-result-list-item-loader">\
                        <div class="search-result-list-item-inner">\
                            <span class="icon"><i class="fa fa-spin fa-spinner"></i></span>\
                            <p>Searching ...</p>\
                        </div>\
                    </li>\
                ');

                var $result = dom_container.find('#people-search-result');
                var page = $(this).data('page');
                if ( ! page) page = 1;
                else page++;
                peopleSearchFilter.page = page;
                discoverDeals(SearchTypes.DATABASE, peopleSearchFilter).done(function (data) {
                    $list.find('.search-result-list-item-loader').remove();
                    var deals = data.results != null && data.results.length ? data.results : null;
                    if (deals) {
                        if (data.total_count > 0) {
                            $list.data('page', page);
                        }
                        $.each(deals, function (index, deal) {
                            var $deal = $('\
                                <li class="search-result-list-item">\
                                    <div class="search-result-list-item-inner">\
                                        <div class="company-info">\
                                            <div class="company-logo-container">\
                                                <img class="company-logo" src="' + (deal.description.company.source.logo ? deal.description.company.source.logo : '/images/company-gray.png') + '" />\
                                            </div>\
                                            <div class="company-info-inner">\
                                                <h4 class="company-name">' + deal.description.company.name + '</h4>\
                                                <a target="_blank" class="company-url" href="' + (deal.description.company.website ? 'http://' + deal.description.company.website : '#') + '">' + deal.description.company.website + '</a>\
                                            </div>\
                                        </div>\
                                        <div class="people-info">\
                                            <img class="people-logo" src="' + (deal.description.client.source.avatar ? deal.description.client.source.avatar : '/images/profile-blank.png') + '" />\
                                            <div class="people-info-inner">\
                                                <!--h4 class="people-name">Donald Trump</h4-->\
                                                <span class="people-title">' + deal.description.client.occupation + '</span>\
                                                <span class="location"><i class="fa fa-map-marker"></i> ' + deal.description.client.location + '</span>\
                                            </div>\
                                        </div>\
                                        <div class="metrics-info">\
                                            ' + (deal.description.company.source.employee_count != null ? '<span><i class="fa fa-users"></i> ' + deal.description.company.source.employee_count + '</span>' : '') + '\
                                            ' + (deal.description.company.source.revenue != null ? '<span><i class="fa fa-bank"></i> ' + deal.description.company.source.revenue + '</span>' : '') + '\
                                            <!--span><i class="fa fa-dolla"></i> $336M</span-->\
                                            ' + (deal.description.company.source.industries != null ? '<span><i class="fa fa-suitcase"></i> ' + (deal.description.company.source.industries ? deal.description.company.source.industries.join(', ') : '') + '</span>' : '') + '\
                                            ' + (deal.description.company.location != null ? '<span class="location"><i class="fa fa-map-marker"></i> ' + deal.description.company.location + '</span>' : '') + '\
                                            <!--span><i class="fa fa-road"></i> 5 years</span-->\
                                        </div>\
                                        <div class="form-actions">\
                                            <button class="btn btn-success btn-sm btn-accept">Accept</button>\
                                            <button class="btn btn-warning btn-accept btn-workflow-activation"><small>ADD TO</small><br />Workflow</button>\
                                            <br />\
                                            <button class="btn btn-danger btn-sm btn-reject">Reject</button>\
                                        </div>\
                                    </div>\
                                </li>\
                            ').data('deal', deal).hide().appendTo($list).fadeIn({duration: 500, queue: false}).hide().slideDown(500);
                            $deal.find('img.company-logo').error(function () {
                                this.onerror = null;
                                this.src = '/images/company-gray.png';
                            });
                            $deal.find('img.people-logo').error(function () {
                                this.onerror = null;
                                this.src = '/images/profile-blank.png';
                            });
                        });
                        $result.find('.total-count').html(data.total_count);
                        $result.find('.total-count-container').removeClass('invisible');
                    } else {
                        $list.addClass('is-last-page');
                        $list.append('\
                            <li class="search-result-list-item loading">\
                                <div class="search-result-list-item-inner">\
                                    <span class="icon"><i class="fa fa-times"></i></span>\
                                    <p>No other matches found.</p>\
                                </div>\
                            </li>\
                        ');
                    }
                    // $list.show();
                    setTimeout(function() {
                        if (deals) {
                            $list.slimScroll({
                                scrollTo: scroll_amount + 'px'
                            });
                            $list.addClass('scrolltrack');
                        }
                    }, 1000);
                }).fail(function () {
                    $list.empty().append('\
                        <li class="search-result-list-item loading">\
                            <div class="search-result-list-item-inner">\
                                <span class="icon"><i class="fa fa-warning"></i></span>\
                                <p>Error occurred while searching.</p>\
                            </div>\
                        </li>\
                    ');
                });

                // showPeopleSearchResult(dom_container);
            }
        });

        var dbperson = Dbperson.getDbperson();
        var $roleList = $('<ul />').attr('data-dbperson-name', 'title_roles').addClass('dbperson-list').attr('data-dbperson-type', 'role').appendTo(dom_container.find('.role-list-container'));
        fillDbpersonValues($roleList, dbperson.title_roles);
        $roleList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $roleList.slimScroll({height: '200px', size: '4px', allowPageScroll: true});

        var $seniorityList = $('<ul />').attr('data-dbperson-name', 'title_seniorities').addClass('dbperson-list').attr('data-dbperson-type', 'seniority').appendTo(dom_container.find('.seniority-list-container'));
        fillDbpersonValues($seniorityList, dbperson.title_seniorities);
        $seniorityList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $seniorityList.slimScroll({height: '200px', size: '4px', allowPageScroll: true});

        var $industryList = $('<ul />').attr('data-dbperson-name', 'industries').addClass('dbperson-list').attr('data-dbperson-type', 'industry').appendTo(dom_container.find('.industry-list-container'));
        fillDbpersonValues($industryList, dbperson.industries);
        $industryList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $industryList.slimScroll({height: '200px', size: '4px', allowPageScroll: true});

        var $sizeList = $('<ul />').attr('data-dbperson-name', 'employee_sizes').addClass('dbperson-list').attr('data-dbperson-type', 'size').appendTo(dom_container.find('.size-list-container'));
        fillDbpersonValues($sizeList, dbperson.employee_sizes);
        $sizeList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $sizeList.slimScroll({height: '200px', size: '4px', allowPageScroll: true});

        var $revenueList = $('<ul />').attr('data-dbperson-name', 'revenues').addClass('dbperson-list').attr('data-dbperson-type', 'revenue').appendTo(dom_container.find('.revenue-list-container'));
        fillDbpersonValues($revenueList, dbperson.revenues);
        $revenueList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $revenueList.slimScroll({height: '200px', size: '4px', allowPageScroll: true});

        var $countryList = $('<ul />').attr('data-dbperson-name', 'countries,states,metro_regions').addClass('dbperson-list').attr('data-dbperson-type', 'country').appendTo(dom_container.find('.country-list-container'));
        fillDbpersonValues($countryList, dbperson.locations);
        $countryList.bonsai({
            createInputs: 'checkbox',
            checkboxes: true
        });
        $countryList.slimScroll({height: '200px', size: '4px', allowPageScroll: true});

        // activatePeopleFilterPane(1);

        dom_container.on('click', '#people-search-result', function (e) {
            //e.stopPropagation();
//        }).on('click', '#people-search-result .people-search-filter .people-filter .navigators .navigator', function (e) {
//            e.preventDefault();
//            e.stopPropagation();
//            e.cancelBubble = true;
//            var $this = $(this);
//            var $filter = $this.closest('.people-filter');
//
//            var pane = $filter.attr('data-pane');
//            if ($(this).hasClass('more')) {
//                if (pane >= $filter.attr('data-max-pane')) {
//                    return;
//                }
//                pane++;
//            } else if ($this.hasClass('less')) {
//                if (pane <= $filter.attr('data-min-pane')) {
//                    return;
//                }
//                pane--;
//            }
//
//            activatePeopleFilterPane(pane);
//            return false;
////        }).on('click', '#people-search-result ul.search-result-list li.search-result-list-item button.btn-accept', function () {
////            acceptPeopleLead($(this).closest('li.search-result-list-item'));
////        }).on('click', '#people-search-result ul.search-result-list li.search-result-list-item button.btn-reject', function () {
////            rejectPeopleLead($(this).closest('li.search-result-list-item'));
        }).on('click', '#people-search-result>.close', function (e) {
            hidePeopleSearchResult();
        }).on('change', '#people-search-result form.dbperson-search-form .filter-panes input:text[name]', function (e) {
//            if (e.keyCode == 13) {
            fetchDbpersonValue($(this));
            searchPeople(dom_container);
//            }
        }).on('change', '#people-search-result form.dbperson-search-form .filter-panes input:radio[name]', function (e) {
            fetchDbpersonValue($(this));
            searchPeople(dom_container);
        }).on('change', '#people-search-result form.dbperson-search-form .filter-panes select[name]', function (e) {
            fetchDbpersonValue($(this));
            searchPeople(dom_container);
        }).on('click', '#people-search-result form.dbperson-search-form .filter-panes .dbperson-list-container .form-control-static', function (e) {
            $(this).parent().toggleClass('show-dbperson-list');
        }).on('click', '#people-search-result form.dbperson-search-form .filter-panes .dbperson-list-container .dbperson-list input:checkbox', function (e) {
            var $infoList = $(this).closest('.dbperson-list');
            setTimeout(function () {
                fetchDbpersonListValues($infoList);
                searchPeople(dom_container);
            });
        }).on('click', '#people-search-result ul.search-result-list li.search-result-list-item .form-actions button.btn-accept', function () {
            if ($(this).hasClass('btn-workflow-activation')) {
                showWorkflowDropdown($(this).closest('.form-actions'));
                return;
            }
            acceptPeopleLead($(this).closest('li.search-result-list-item'));
        }).on('click', '#people-search-result ul.search-result-list li.search-result-list-item .form-actions button.btn-reject', function () {
            rejectPeopleLead($(this).closest('li.search-result-list-item'));
        }).on('click', '#people-search-result ul.search-result-list li.search-result-list-item .form-actions ul.workflow-list>li.workflow-list-item>a', function (e) {
            var $item = $(this).closest('.workflow-list-item');
            if ($item.hasClass('loading')) {
                return;
            }
            acceptPeopleLead($(this).closest('li.search-result-list-item'), $(this).parent().attr('data-workflow-id'));
        });

    }
//    function activatePeopleFilterPane(pane) {
//        var $filter = dom_container.find('#people-search-result .people-search-filter .people-filter');
//        $filter.find('>.people-filter-pane[data-pane="' + pane + '"]').stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500)
//                .siblings('.people-filter-pane').stop().fadeOut({duration: 500, queue: false}).slideUp(500);
//        if (pane < $filter.attr('data-max-pane')) {
//            $filter.addClass('show-more');
//        } else {
//            $filter.removeClass('show-more');
//        }
//        if (pane > $filter.attr('data-min-pane')) {
//            $filter.addClass('show-less');
//        } else {
//            $filter.removeClass('show-less');
//        }
//
//        $filter.attr('data-pane', pane);
//    }

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

    function fetchDbpersonValue($element) {
        var name = $element.attr('name');
        if (!name) {
            return;
        }
        var value = $element.val();
        if (name == 'company_names') {
            value = value ? [value] : null;
        }
        setPeopleSearchFilter(name, value);
    }

    function fetchDbpersonListValues($infoList) {
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

        var names = $infoList.attr('data-dbperson-name').split(',');
        var values = [];
        for (var i = 0; i < names.length; i++) {
            values[i] = [];
        }
        $.each(infos, function (i, info) {
            values[names.length > 1 ? info.depth - 1 : 0].push(info.value);
        });
        for (var i = 0; i < names.length; i++) {
            setPeopleSearchFilter(names[i], values[i]);
        }
    }

    var peopleSearchFilter = null;
    function setPeopleSearchFilter(name, value) {
        if (!value) {
            if (peopleSearchFilter && typeof peopleSearchFilter[name] != 'undefined') {
                delete peopleSearchFilter[name];
            }
            return;
        }
        if (!peopleSearchFilter) {
            peopleSearchFilter = {};
        }
        peopleSearchFilter[name] = value;
    }

    function searchPeople(dom_container) {
        var $result = dom_container.find('#people-search-result');
        $result.find('.search-result-inner').removeClass('is-empty');

        $result.find('.total-count-container').addClass('invisible');
        $result.find('.total-count').html('0');

        if (jQuery.isEmptyObject(peopleSearchFilter)) {
            $result.find(".search-result-list").hide().empty().append('\
                <li class="search-result-list-item loading">\
                    <div class="search-result-list-item-inner">\
                        <span class="icon"><i class="fa fa-search"></i></span>\
                        <p>No search condition given.</p>\
                    </div>\
                </li>\
            ').show();
            return;
        }

        var $list = $result/*.hide()*/.find(".search-result-list").hide().empty().append('\
            <li class="search-result-list-item loading">\
                <div class="search-result-list-item-inner">\
                    <span class="icon"><i class="fa fa-spin fa-spinner"></i></span>\
                    <p>Searching ...</p>\
                </div>\
            </li>\
        ').show();
        $list.data('page', 1);
        peopleSearchFilter.page = 1;
        discoverDeals(SearchTypes.DATABASE, peopleSearchFilter).done(function (data) {
            $list.hide().empty();
            var deals = data.results != null && data.results.length ? data.results : null;
            if (deals) {
                $.each(deals, function (index, deal) {
                    var $deal = $('\
                        <li class="search-result-list-item">\
                            <div class="search-result-list-item-inner">\
                                <div class="company-info">\
                                    <div class="company-logo-container">\
                                        <img class="company-logo" src="' + (deal.description.company.source.logo ? deal.description.company.source.logo : '/images/company-gray.png') + '" />\
                                    </div>\
                                    <div class="company-info-inner">\
                                        <h4 class="company-name">' + deal.description.company.name + '</h4>\
                                        <a target="_blank" class="company-url" href="' + (deal.description.company.website ? 'http://' + deal.description.company.website : '#') + '">' + deal.description.company.website + '</a>\
                                    </div>\
                                </div>\
                                <div class="people-info">\
                                    <img class="people-logo" src="' + (deal.description.client.source.avatar ? deal.description.client.source.avatar : '/images/profile-blank.png') + '" />\
                                    <div class="people-info-inner">\
                                        <!--h4 class="people-name">Donald Trump</h4-->\
                                        <span class="people-title">' + deal.description.client.occupation + '</span>\
                                        <span class="location"><i class="fa fa-map-marker"></i> ' + deal.description.client.location + '</span>\
                                    </div>\
                                </div>\
                                <div class="metrics-info">\
                                    ' + (deal.description.company.source.employee_count != null ? '<span><i class="fa fa-users"></i> ' + deal.description.company.source.employee_count + '</span>' : '') + '\
                                    ' + (deal.description.company.source.revenue != null ? '<span><i class="fa fa-bank"></i> ' + deal.description.company.source.revenue + '</span>' : '') + '\
                                    <!--span><i class="fa fa-dolla"></i> $336M</span-->\
                                    ' + (deal.description.company.source.industries != null ? '<span><i class="fa fa-suitcase"></i> ' + (deal.description.company.source.industries ? deal.description.company.source.industries.join(', ') : '') + '</span>' : '') + '\
                                    ' + (deal.description.company.location != null ? '<span class="location"><i class="fa fa-map-marker"></i> ' + deal.description.company.location + '</span>' : '') + '\
                                    <!--span><i class="fa fa-road"></i> 5 years</span-->\
                                </div>\
                                <div class="form-actions">\
                                    <button class="btn btn-success btn-sm btn-accept">Accept</button>\
                                    <button class="btn btn-warning btn-accept btn-workflow-activation"><small>ADD TO</small><br />Workflow</button>\
                                    <br />\
                                    <button class="btn btn-danger btn-sm btn-reject">Reject</button>\
                                </div>\
                            </div>\
                        </li>\
                    ').data('deal', deal).hide().appendTo($list).fadeIn({duration: 500, queue: false}).hide().slideDown(500);
                    $deal.find('img.company-logo').error(function () {
                        this.onerror = null;
                        this.src = '/images/company-gray.png';
                    });
                    $deal.find('img.people-logo').error(function () {
                        this.onerror = null;
                        this.src = '/images/profile-blank.png';
                    });
                });
                $result.find('.total-count').html(data.total_count);
                $result.find('.total-count-container').removeClass('invisible');
            } else {
                $list.append('\
                    <li class="search-result-list-item loading">\
                        <div class="search-result-list-item-inner">\
                            <span class="icon"><i class="fa fa-times"></i></span>\
                            <p>No matches found.</p>\
                        </div>\
                    </li>\
                ');
            }
            $list.show();
            setTimeout(function() {
                $list.addClass('scrolltrack');
            }, 1000);
        }).fail(function () {
            $list.empty().append('\
                <li class="search-result-list-item loading">\
                    <div class="search-result-list-item-inner">\
                        <span class="icon"><i class="fa fa-warning"></i></span>\
                        <p>Error occurred while searching.</p>\
                    </div>\
                </li>\
            ');
        });

        showPeopleSearchResult(dom_container);
    }

    function showPeopleSearchResult(dom_container) {
        if (dom_container.find('#people-search-result').is(':visible')) {
            return;
        }

        dom_container.find('#people-search-result').stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);

        fetchDbpersonValue(dom_container.find('#people-search-result').find('input[name="company_names"]').val($q.val()));
        searchPeople(dom_container);

        $(document).on('click.search.people.toggle', function (e) {
            if ($(e.target).closest('#people-search-result').length) {
                return;
            } else if ($(e.target).filter('#search_input').length) {
                return;
            }

            hidePeopleSearchResult();
        });
    }
    var hidePeopleSearchResult = function () {
        dom_container.find('#people-search-result').fadeOut('fast');
        $(document).off('click.search.people.toggle');
    };

    var acceptPeopleLead = function ($deal, workflowId) {
        if (workflowId == null) {
            workflowId = null;
        }

        var deal = $deal.data('deal');
        var $btn = $deal.find('button.btn-accept:nth-child(' + (workflowId ? 2 : 1) + ')');
        var html = $btn.html();
        $btn.html('<i class="fa fa-spin fa-spinner"></i>');
        $deal.find('.form-actions button').attr('disabled', true);

        var data = {type: deal.type, match_values: deal.match_values, workspace_id: current_workspace_id};
        var activate_deal_workflow = false;
        if (workflowId) {
            data.workflow_id = workflowId;
            activate_deal_workflow = true;
        }

        $.ajax({
            url: '/api/v1/leads/discover/' + deal.code + '/accept',
            method: 'POST',
            data: data
        }).done(function(lead) {
            var verify_needed = false;
            if (verify_needed) {
                Leads.verifyLead(null, activate_deal_workflow, lead);
            } else {
                refreshAvailableDealCount();
                if (activate_deal_workflow) {
                    Workflow.setInitialDealWorkflowIsEnabled(lead.id, 'parent');
                }
            }
            $btn.html('Accepted');
            setTimeout(function () {
                $deal.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                    $(this).remove();
                });
            }, 100);
        }).fail(function(jqXHR) {
            if (jqXHR.status === 402) {
                $btn.html('Accept');
                showErrorMessage('Error', 'Please purchase additional credits to accept more leads.');
            } else if (jqXHR.status === 410) {
                showErrorMessage('Error', 'Email information is no longer valid. Lead has been deleted and no credits have been charged.');
                setTimeout(function () {
                    $deal.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
                        $(this).remove();
                    });
                }, 100);
            } else {
                $btn.html(html);
                $deal.find('.form-actions button').attr('disabled', false);
            }
        });
    };

    var rejectPeopleLead = function ($deal) {
        $deal.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
            $(this).remove();
        });
//        var deal = $deal.data('deal');
//        $deal.find('button.btn-reject').html('<i class="fa fa-spin fa-spinner"></i> Rejecting...').attr('disabled', true);
//        $.post('/api/v1/leads/discover/' + deal.code + '/reject', {type: deal.type, match_values: deal.match_values, workspace_id: current_workspace_id}, function (data) {
//            $deal.find('button.btn-reject').html('Rejected').attr('disabled', false);
//            setTimeout(function () {
//                $deal.fadeOut({duration: 500, queue: false}).slideUp(500, function () {
//                    $(this).remove();
//                });
//            }, 500);
//        });
//
    };

    function showWorkflowDropdown($formActions) {
        var $dropdown = $formActions.find('.workflow-list-dropdown');
        if (!$dropdown.length) {
            $dropdown = $('<div class="workflow-list-dropdown dropdown-menu">\
                            <ul class="workflow-list"></ul>\
                        </div>').appendTo($formActions);
        }

        var $list = $dropdown.hide().find(".workflow-list").empty().append('\
            <li class="workflow-list-item loading">\
                <div class="workflow-list-item-inner">\
                    <a><i class="fa fa-spin fa-spinner"></i> Searching ...</a>\
                </div>\
            </li>\
        ');
        Workflow.loadWorkflowList(true, false, true).done(function (data) {
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
            if (data && data.length) {
                $.each(data, function (index, item) {
                    $('\
                        <li class="workflow-list-item">\
                            <a><i class="fa fa-check"></i> ' + item.name + '</a>\
                        </li>\
                    ').attr('data-workflow-id', item.id).appendTo($list);
                });
            } else {
                $list.append('\
                    <li class="workflow-list-item loading">\
                        <a><i class="fa fa-times"></i> No matches found.</a>\
                    </li>\
                ');
            }
        });

        $dropdown.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
        setTimeout(function () {
            $(document).on('click.lead.search.workflow.list.toggle', function () {
                hideWorkflowDropdown();
            });
        });
    }

    function hideWorkflowDropdown() {
        dom_container.find('.form-actions>.workflow-list-dropdown').fadeOut('fast');
        $(document).off('click.lead.search.workflow.list.toggle');
    }
    //</editor-fold>

    function discoverDeals(type, params) {
        return $.getJSON(getApiUrl(), {type: type, q: params});
    }

    function getApiUrl() {
        return '/api/v1/workspaces/' + current_workspace_id + '/leads/discover';
    }

    return scope;
})();