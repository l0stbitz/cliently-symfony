var Contacts = (function () {
    var scope = {};

    var initialized = false;

    function init() {

        initialized = true;
    }

    scope.$createContacts = function (contacts, isFromSearch) {
        if (!initialized) {
            init();
        }

        return $createContacts(contacts, isFromSearch);
    };

    function $createContacts(contacts, isFromSearch) {
        var $contacts = $('<div class="contact-list" />');
        if (isFromSearch) {
            $contacts.addClass('contact-list-from-search');
        }

        $.each(contacts, function (i, contact) {
            var $contact = $createContact(contact).appendTo($contacts);
            if (!isFromSearch) {
                tweakContact($contact, contact);
            } else {
                tweakContactFromSearch($contact, contact);
            }
        });

        return $contacts;
    }

    function $createContact(contact) {
        return $('\
            <div class="contact">\
                <span class="contact-avatar"><img src="' + contact.avatar + '" /></span>\
                <div class="contact-info">\
                    <h3 class="contact-name">' + contact.name + '</h3>\
                    <span class="contact-title">' + contact.title + ' @ SalesLoft</span>\
                    <span class="contact-phone">' + contact.phone + '</span>\
                    <span class="contact-email">' + contact.email + '</span>\
                </div>\
            </div>\
        ').data('contact', contact);
    }

    function tweakContact($contact, contact) {
        $contact.find('.contact-info').append('\
            <div class="action-buttons btn-group">\
                <a class="btn" data-action="email_send"><i class="fa fa-envelope"></i> Send email</a>\
                <a class="btn" data-action="twitter_tweet"><i class="fa fa-cliently-twitter"></i> Tweet</a>\
                <a class="btn" data-action="workflow_add"><i class="fa fa-recycle"></i> Add to workflow</a>\
            </div>\
        ').on('click', 'a[data-action]', function (e) {
            var $contact = $(this).closest('.contact');
            var action = $(this).attr('data-action');
            if (action == 'email_send') {
                var $form = $contact.find('form.mail-action-form');
                if (!$form.length) {
                    $form = $createMailForm().hide().appendTo($contact);
                    $form.find('.bootstrap-text-editor').wysiwyg({toolbarSelector: $form.find('[data-role="editor-toolbar"]')});
                    $form.find('.btn').addClass('btn-xs');
                }
                $form.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
                $form.find('input[name="name"]').focus();
            } else if (action == 'twitter_tweet') {
                var $form = $contact.find('form.twitter-message-form');
                if (!$form.length) {
                    $form = $createTweetForm().hide().appendTo($contact);
                }
                $form.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
                $form.find('textarea[name="description"]').focus();
            } else if (action == 'workflow_add') {
                showWorkflowDropdown($(this).closest('.contact'));
            }

            return e.preventDefault();
        });
    }

    function tweakContactFromSearch($contact, contact) {
        if (contact.accepted) {
            $contact.addClass('contact-accepted');
        }
        $contact.find('.contact-avatar').append('\
            <div class="social-links">\
                <a href="https://facebook.com/" target="_blank"><i class="fa fa-cliently-facebook"></i></a>\
                <a href="https://twitter.com/" target="_blank"><i class="fa fa-cliently-twitter"></i></a>\
                <a href="https://linkedin.com/" target="_blank"><i class="fa fa-cliently-linkedin"></i></a>\
            </div>');
        $contact.find('.contact-info').append('<a href="" target="_blank" class="view-more">more...</a>');
        //$contact.append('<span class="contact-accepted-status"><i class="fa"></span></span>');
        $contact.append('\
            <div class="form-actions">\
                <button class="btn btn-success btn-sm btn-accept">Accept</button>\
                <button class="btn btn-warning btn-accept btn-workflow-activation"><small>ADD TO</small><br />Workflow</button>\
                <br />\
            </div>\
        ');
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

    function showWorkflowDropdown($contact) {
        var $dropdown = $contact.find('.workflow-list-dropdown');
        if (!$dropdown.length) {
            $dropdown = $('<div class="workflow-list-dropdown dropdown-menu">\
                            <ul class="workflow-list"></ul>\
                        </div>').appendTo($contact);
        }

        var $list = $dropdown.hide().find(".workflow-list").empty().append('\
            <li class="workflow-list-item loading">\
                <div class="workflow-list-item-inner">\
                    <a><i class="fa fa-spin fa-spinner"></i> Searching ...</a>\
                </div>\
            </li>\
        ');
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
        $(document).off('click.lead.search.workflow.list.toggle');
        setTimeout(function () {
            $(document).on('click.lead.search.workflow.list.toggle', function () {
                $('.contact-list .contact .workflow-list-dropdown').fadeOut('fast');
                $(document).off('click.lead.search.workflow.list.toggle');
            });
        });
    }
    //</editor-fold>


    return scope;
})();