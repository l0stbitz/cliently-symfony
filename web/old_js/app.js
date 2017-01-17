(function () {
    var root;

    root = typeof exports !== "undefined" && exports !== null ? exports : this;

    root_domain = document.domain.replace(/^app\./, '') + (location.port ? ':' + location.port : '');

    $(document).ready(function () {
        $('#form_signin').on('submit', function (event) {
            $.post('//' + root_domain + '/login_check', {
                '_csrf_token': $(this).find('input[name="_csrf_token"]').val(),
                '_username': $(this).find('input[name="_username"]').val(),
                '_password': $(this).find('input[name="_password"]').val()
            }).done(function (data) {
                $('#form_signin').find('._messages .alert-box').addClass('hide');
                if (data.success != null) {
                    $('#form_signin').find('._signin-success').removeClass('hide');
                    return setTimeout(function () {
                        return window.location = '//app.' + root_domain;
                    }, 2000);
                } else {
                    return $('#form_signin').find('._signin-fail').removeClass('hide');
                }
            });
            return event.preventDefault();
        });
        $('#auth_signout').on('click', function (event) {
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

            return event.preventDefault();
        });
        $('#form_signup').on('submit', function (event) {
            var isEmailAlreadyExisting = false;
            $.ajax({
                url: '/api/v1/validations/email/availability?q=' + $(this).find('input[name="email"]').val(),
                async: false,
                dataType: 'json',
                success: function (data) {
                    if ( ! data.success) {
                        isEmailAlreadyExisting = true;
                    }
                }
            });

            if (isEmailAlreadyExisting) {
                $.bigBox({
                    title: 'Signup',
                    content: 'Email account already exists, please use a different one.',
                    tabicon: false,
                    sound: false,
                    color: "#993333",
                    timeout: 3000
                });
                return false;
            }

            $.post('//' + root_domain + '/register', {
                'fos_user_registration_form[username]': $(this).find('input[name="_email"]').val(),
                'fos_user_registration_form[email]': $(this).find('input[name="_email"]"]').val(),
                'fos_user_registration_form[plainPassword][first]': $(this).find('input[name="_password"]').val(),
                'fos_user_registration_form[plainPassword][second]': $(this).find('input[name="_password"]').val(),
                'fos_user_registration_form[_token]': $(this).find('input[name="fos_user_registration_form[_token]"]').val(),
            }).done(function (data) {
                $('#form_signup').find('._messages .alert-box').addClass('hide');
                if (data.success != null) {
                    $('#form_signup').find('._signup-success').removeClass('hide');
                    return setTimeout(function () {
                        return window.location = '//app.' + root_domain;
                    }, 2000);
                } else {
                    return $('#form_signup').find('._signup-fail-values').removeClass('hide');
                }
            });
            return event.preventDefault();
        });
        return $('#form_passreset').on('submit', function (event) {
            $.post('//' + root_domain + '/x-password-reset', {
                'email': $(this).find('input[name="email"]').val()
            }).done(function (data) {
                $('#form_passreset').find('._messages .alert-box').addClass('hide');
                if (data.success != null) {
                    $('#form_passreset').find('._passreset-success').removeClass('hide');
                    return setTimeout(function () {
                        return window.location = '/message/password-reset-submitted';
                    }, 2000);
                } else if (data.errors != null) {
                    return $('#form_passreset').find('._passreset-fail-notexists').removeClass('hide');
                } else {
                    return $('#form_passreset').find('._passreset-fail-values').removeClass('hide');
                }
            });
            return event.preventDefault();
        });
    });

}).call(this);
