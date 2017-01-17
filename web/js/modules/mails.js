var Mails = (function () {
    var scope = {};

    var MailActions = {
        REPLY: 'reply',
        FORAWARD: 'forward'
    };

    var initialized = false;

    function init() {
        $(document).on('click', '.mail-list .mail .action-buttons a', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            showForm($(this).closest('.mail'), $(this).attr('data-action'));
        }).on('submit', '.mail-list .mail>.mail-action-form', function () {
            var $form = $(this);
            var $xhr = save($form.closest('.mail'), $form.data('action'), $form);
            if ($xhr) {
                var $btn = $form.find('button.btn-save');
                var html = $btn.html();
                $btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                $xhr.done(function () {
                    $btn.attr('disabled', false).html(html);
                }).fail(function () {
                    $btn.attr('disabled', false).html(html);
                });
            }
            return false;
        }).on('click', '.mail-list .mail>.mail-action-form .btn-cancel', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;
            hideForm($(this).closest('form'));
        });
        initialized = true;
    }

    scope.$createMailList = function (mails) {
        if (!initialized) {
            init();
        }

        return $createMailList(mails);
    };

    function $createMailList(mails) {
        var $mailList = $('<div class="mail-list"></div>');

        $.each(mails, function (index, mail) {
            $createMail(mail).appendTo($mailList);
        });

        return $mailList;
    }

    scope.$createMail = function (mail) {
        if (!initialized) {
            init();
        }

        return $createMail(mail);
    };

    function $createMail(mail) {
        var workspace_member = null;
        $.each(workspace_members, function (i, member) {
            if (member.user_id === mail.owner_id) {
                workspace_member = member;
                return;
            }
        });

        var member_name   = workspace_member.user.first_name + ' ' + workspace_member.user.last_name;
        var member_avatar = '/images/profile-blank.png';

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

        var $mail = $('\
            <div class="deal-text-entry mail">\
                <div class="mail-content content">\
                    <span class="date-wrapper">\
                        ' + (mail.is_own ? 'Sent' : 'Received') + ' |\
                        <span class="mail-date">' + getDateTimeString(mail.created_at) + '</span>\
                    </span>\
                    <h3 class="mail-subject">' + mail.name + '</h1>\
                    <div>From: <span class="mail-from">' + (mail.is_own ? mail.handle : mail.email) + '</span></div>\
                    <div>To: <span class="mail-to">' + (mail.is_own ? mail.email : mail.handle) + '</span></div>\
                    <div>Cc: <span class="mail-cc">' + mail.cc + '</span></div>\
                    <div>Bcc: <span class="mail-bcc">' + mail.bcc + '</span></div>\
                    <div class="mail-description">' + mail.description + '</div>\
                    <div class="action-buttons">\
                        <a data-action="reply"><i class="fa fa-reply"></i> Reply</a>\
                        <a data-action="forward"><i class="fa fa-share"></i> Forward</a>\
                    </div>\
                </div>\
                <div class="meta">\
                    <img class="avatar" src="' + member_avatar + '" />\
                    <span class="fullname">' + member_name + '</span>\
                </div>\
            </div>\
        ').data('mail', mail)
                .attr('id', 'mail-' + mail.id)
                .attr('data-mail-id', mail.id);
        mail.$element = $mail;

        $mail.append('<span class="mark"><span class="icon"><i class="fa fa-envelope"></i></span></span>');

        return $mail;
    }

    function showForm($mail, action) {
        var $form = $mail.find('>.mail-action-form[data-action="' + action + '"]');
        if (!$form.length) {
            var $form = $('\
                <form class="form action-form mail-action-form" data-action="' + action + '">\
                    <div class="row">\
                        <div class="col-md-6">\
                            <div class="form-group">\
                                <input class="form-control" name="name" placeholder="Subject" type="text">\
                            </div>\
                        </div>\
                        <div class="col-md-6">\
                            <input class="form-control mail-to" placeholder="To:" type="text">\
                        </div>\
                    </div>\
                    <div class="form-group">\
                        <div class="bootstrap-text-editor form-control mail-description" data-placeholder="Message"></div>\
                    </div>\
                    <div class="row">\
                        <div class="col-md-8">\
                        ' + getEditableWYSIHTML5OptionsHtml() + '\
                        </div>\
                        <div class="col-md-4">\
                            <button class="btn btn-default btn-cancel">Cancel</button>\
                            <button class="btn btn-primary btn-save">Submit</button>\
                        </div>\
                    </div>\
                </form>\
            ').hide().appendTo($mail);
            $form.find('.bootstrap-text-editor').wysiwyg({toolbarSelector: $form.find('[data-role="editor-toolbar"]')});
        }
        var mail = $mail.data('mail');
        switch (action) {
            case MailActions.REPLY:
                $form
                        .find('input[name="name"]').val('Re: ' + mail.name).end()
                        .find('.mail-to').val(mail.lead.clients[0].email).attr('readonly', true).end()
                        .find('.bootstrap-text-editor.mail-description').html('<br />' + mail.description);
                break;
            case MailActions.FORAWARD:
                $form
                        .find('input[name="name"]').val('Fw: ' + mail.name).end()
                        .find('.mail-to').attr('name', 'email').val('').end()
                        .find('.bootstrap-text-editor.mail-description').html('<br />' + mail.description);
                break;
        }

        $form.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500);
        $form.find('.bootstrap-text-editor.mail-description').focus();
    }
    function hideForm($form) {
        $form.stop().fadeOut({duration: 500, queue: false}).slideUp(500);
    }

    function save($mail, action, $form) {
        var mail = $mail.data('mail');
        var values = $form.serializeObject();
        values.description = $form.find('.bootstrap-text-editor.mail-description').html();
        var to = $form.find('input.mail-to').val();

        if (!values.name || !to || !values.description) {
            showErrorMessage('Tasks', 'Please enter subject, to and message.');
            return false;
        }

        return $.post(getApiUrl() + mail.id + '/' + action, values, function (newMail) {
            showSuccessMessage("Success", "A mail has been " + (action == MailActions.REPLY ? 'replied' : 'forwarded') + " successfully.");
            newMail.lead = mail.lead;
            newMail.lead.mails.unshift(newMail);
            $createMail(newMail).hide().insertAfter($mail).fadeIn({duration: 500, queue: false}).hide().slideDown(500);
            hideForm($form);
        });
    }

    function getApiUrl() {
        return '/api/v1/mails/';
    }

    return scope;
})();