var SocialIntegrationTypes = {
    GOOGLE: 'google',
    TWITTER: 'twitter'
};

var SocialIntegrator = (function () {
    var scope = {};
    var initialized = false;

    function init() {
        $(document).on('click', 'a.social-integration-handler', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;

            startIntegrating($(this));

            return false;
        });


        initialized = true;
    }

    scope.$createGoogleIntegration = function (callback) {
        return this.$create(SocialIntegrationTypes.GOOGLE, callback);
    };
    scope.$createTwitterIntegration = function (callback) {
        return this.$create(SocialIntegrationTypes.TWITTER, callback);
    };

    scope.$create = function (type, callback) {
        if (!initialized) {
            init();
        }

        var $integration =  $('<a href="#" class="social-integration-handler" data-type="' + type + '">' + getIcon(type) + ' <label class="title">' + getEmptyTitle(type) + '</label></a>');
        if (typeof callback != 'undefined') {
            $integration.data('callback', callback);
        }
        return $integration;
    };

    function getEmptyTitle(type) {
        switch (type) {
            case SocialIntegrationTypes.GOOGLE:
                return 'Connect to Google';
            case SocialIntegrationTypes.TWITTER:
                return 'Connect to Twitter';
        }
    }

    function getIcon(type) {
        switch (type) {
            case SocialIntegrationTypes.GOOGLE:
                return '<span class="icon"><img src="/images/gmail-icon.png" /><span class="text">Connect</span><span class="checked"><i class="fa fa-check"></i></span></span>';
            case SocialIntegrationTypes.TWITTER:
                return '<span class="icon"><i class="fa fa-cliently-twitter"></i><span class="text">Connect</span><span class="checked"><i class="fa fa-check"></i></span></span>';
        }
    }

    function startIntegrating($integration) {
        var wnd = openIntegrationWindow('/integrations/' + $integration.data('type') + '/connect', '', 626, 436);
        var timer = setInterval(function() {
            if(wnd.closed) {
                clearInterval(timer);
                reloadIntegration($integration);
            }
        }, 500);
    }

    function reloadIntegration($integration) {
        var type = $integration.data('type');
        $integration.removeClass('active empty').html("<i class='fa fa-spin fa-spinner'></i>").attr('title', 'Loading...');
        $.get("/api/v1/integrations/" + type, function (data) {
            if (data && data.integration) {
                $integration.addClass('active').html(getIcon(type) + ' <label class="title">' + data.integration.handle + '</label>').attr('title', 'Change Account');
            } else {
                var title = getEmptyTitle(type);
                $integration.addClass('empty').html(getIcon(type) + ' <label class="title">' + title + '</label>').attr('title', title);
            }
            var callback = $integration.data('callback');
            if (typeof callback != 'undefined') {
                callback(data.integration);
            }
        });
    }
    scope.reloadIntegration = reloadIntegration;

    function openIntegrationWindow(url, title, w, h) {
        // Fixes dual-screen position                         Most browsers      Firefox
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var newWindow = window.open(url, title, 'toolbar=0,status=0,scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        // Puts focus on the newWindow
        if (window.focus) {
            newWindow.focus();
        }

        return newWindow;
    }
    scope.openIntegrationWindow = openIntegrationWindow;

    return scope;
})();
