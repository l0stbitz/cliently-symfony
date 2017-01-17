var UserInfo = (function () {
    var scope = {};

    var $container = null;
    var $saveButton = null;

    var initialized = false;

    function init() {
        $(document).on('submit', '#user-info-popup.modal form.user-info-form', function () {
            saveUserInfo($(this));
            return false;
        }).on('click', '#user-info-popup.modal button.btn-save', function () {
            $container.find('form.user-info-form').submit();
        });

        initialized = true;
    }

    scope.show = function () {
        if (!initialized) {
            init();
        }

        popupUserInfo();
    };

    function popupUserInfo() {
        if (!$container) {
            $container = $('<div id="user-info-popup" class="modal fade" role="dialog">\
                                <div class="modal-dialog">\
                                  <div class="modal-content">\
                                    <div class="modal-header">\
                                      <h4 class="modal-title">Let\'s get your account setup</h4>\
                                    </div>\
                                    <div class="modal-body">\
                                        <form method="post" class="form user-info-form">\
                                            <div class="row">\
                                                <div class="col-md-offset-2 col-md-4">\
                                                    <div class="form-group">\
                                                        <label class="control-label">First Name</label>\
                                                        <input type="text" name="first_name" class="form-control" placeholder="First Name">\
                                                    </div>\
                                                </div>\
                                                <div class="col-md-4">\
                                                    <div class="form-group">\
                                                        <label class="control-label">Last Name</label>\
                                                        <input type="text" name="last_name" class="form-control" placeholder="Last Name">\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="row">\
                                                <div class="col-md-offset-3 col-md-6">\
                                                    <div class="form-group">\
                                                        <label class="control-label">Email</label>\
                                                        <input type="email" name="email" class="form-control" placeholder="Email">\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="row">\
                                                <div class="col-md-offset-3 col-md-6">\
                                                    <div class="form-group">\
                                                        <label class="control-label">Password</label>\
                                                        <input type="password" name="password" class="form-control" placeholder="Password (8+ characters)">\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="row">\
                                                <div class="col-md-offset-3 col-md-6">\
                                                    <div class="form-group">\
                                                        <label class="control-label">Company Name</label>\
                                                        <input type="text" name="company_name" class="form-control" placeholder="Company Name">\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="row">\
                                                <div class="col-md-offset-3 col-md-6">\
                                                    <div class="form-group">\
                                                        <label class="control-label">Company Size</label>\
                                                        <select name="company_size" class="form-control" placeholder="Company Size">\
                                                            <option value="1-5">1-5</option>\
                                                            <option value="5-10">5-10</option>\
                                                            <option value="10-20">10-20</option>\
                                                            <option value="20-50">20-50</option>\
                                                            <option value="50-100">50-100</option>\
                                                            <option value="100-250">100-250</option>\
                                                            <option value="250-500">250-500</option>\
                                                            <option value="500-1,000">500-1,000</option>\
                                                            <option value="1,000-5,000">1,000-5,000</option>\
                                                            <option value="5,000-10,000">5,000-10,000</option>\
                                                            <option value="Over 10,000">Over 10,000</option>\
                                                        </select>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <div class="row">\
                                                <div class="col-md-offset-3 col-md-6">\
                                                    <div class="form-group">\
                                                        <label class="control-label">Phone Number</label>\
                                                        <input type="phone" name="phone" class="form-control" placeholder="Phone Number">\
                                                    </div>\
                                                </div>\
                                            </div>\
                                            <hr />\
                                            <div class="row">\
                                                <div class="col-md-offset-2 col-md-4">\
                                                    <div class="form-group">\
                                                        <label class="control-label">Location</label>\
                                                        <input type="text" name="location" id="user_location" class="form-control" placeholder="Location">\
                                                    </div>\
                                                </div>\
                                                <div class="col-md-4">\
                                                    <div class="form-group">\
                                                        <label class="control-label">Industry</label>\
                                                        <select name="industries[]" id="industries" class="form-control" placeholder="Industries"></select>\
                                                    </div>\
                                                </div>\
                                            </div>\
                                        </form>\
                                    </div>\
                                    <div class="modal-footer">\
                                        <div class="note">*All fields are required. Your location and industry allow us to provide you with the most relevant leads.</div>\
                                        <button type="button" class="btn btn-primary btn-save">Next <i class="fa fa-angle-double-right"></i></button>\
                                    </div>\
                                  </div>\
                                </div>\
                            </div>').modal({
                show: false,
                keyboard: false,
                backdrop: 'static'
            });
            
            $container.find('input[name=first_name]').val(UserData.first_name);
            $container.find('input[name=last_name]').val(UserData.last_name);
            $container.find('input[name=email]').val(UserData.email);

            var $industries = $container.find("#industries").empty();
            $.getJSON('/api/v1/industries', function (data) {
                if (data && data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        var industry = data[i];
                        $('<option>', {value: industry.id}).html(industry.name).appendTo($industries);
                    }
                }
            });
            $.get("https://ipinfo.io?token=90e29d6880232f", function (response) {
                $container.find('#user_location').val(response.city + ', ' + response.region + ', ' + response.country);
            }, "jsonp");

            $container.find('#user_location').focus(function () {
                $(this).select();
            }).geocomplete();
            $saveButton = $container.find('button.btn-save');
        }

        $container.modal('show');
    }

    function saveUserInfo($form) {
        var userInfo = $form.serializeObject();
        if (!userInfo.first_name || !userInfo.last_name || !userInfo.email || !userInfo.password || !userInfo.company_size || !userInfo.phone || !userInfo.location) {
            showErrorMessage('Account Info', 'Please enter first name, last name, password, phone number and location.');
            return false;
        }

        var isEmailAlreadyExisting = false;
        $.ajax({
            url: '/api/v1/validations/email/availability?q=' + userInfo.email,
            async: false,
            dataType: 'json',
            success: function (data) {
                if (!data.success) {
                    isEmailAlreadyExisting = true;
                }
            }
        });

        if (isEmailAlreadyExisting) {
            showErrorMessage('Account Info', 'Email account already exists, please use a different one.');
            return false;
        }

        $.ajax({
            url: "https://maps.googleapis.com/maps/api/geocode/json?address=" + userInfo.location,
            async: false,
            dataType: 'json',
            success: function (res) {
                if (res.results[0].geometry) {
                    userInfo.coords = res.results[0].geometry.location.lat + "," + res.results[0].geometry.location.lng;
                }
            }
        });

        userInfo.user_wizard = UserWizards.USER_CONNECTOR;
        $saveButton.attr('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Saving...');
        $.ajax({
            url: '/api/v1/users/me',
            method: 'PUT',
            data: userInfo
        }).done(function (ret) {
            if (!ret.success) {
                showErrorMessage('Account Info', 'Error occurred while saving the account info. Please try again.');
                $saveButton.attr('disabled', false).html('<i class="fa fa-save"></i> Save');
                return;
            }

            $saveButton.html('<i class="fa fa-save"></i> Saved');

            if (typeof analytics !== 'undefined' && analytics !== null) {
                window.segment_traits.email = userInfo.email;
                window.segment_traits.firstName = userInfo.first_name;
                window.segment_traits.lastName = userInfo.last_name;
                window.segment_traits.phone = userInfo.phone;
                window.segment_traits.companySize = userInfo.company_size;
                window.segment_traits.companyName = userInfo.company_name != null ? userInfo.company_name : '';
                analytics.identify(window.segment_user, window.segment_traits);
                analytics.track('Account Created', {
                    plan: 'free'
                });
            }

            setTimeout(function () {
                $container.modal('hide');
                user_wizard = UserWizards.USER_CONNECTOR;
                UserConnector.show();
            }, 100);
        });
    }

    return scope;
})();
