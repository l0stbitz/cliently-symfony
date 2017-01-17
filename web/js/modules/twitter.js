var TwitterEventTypes = {
    TWEET: 'twitter_tweet',
    RETWEET: 'twitter_retweet',
    FOLLOW: 'twitter_follow',
    UNFOLLOW: 'twitter_unfollow',
    LIKE: 'twitter_favorite',
    DIRECT: 'twitter_direct',
    QUOTE: 'twitter_quote'
};
var TwitterEventTypeLabels = {
    'twitter_tweet': 'Tweet',
    'twitter_retweet': 'Retweet',
    'twitter_follow': 'Followed',
    'twitter_unfollow': 'Unfollowed',
    'twitter_favorite': 'Liked',
    'twitter_direct': 'Direct Message',
    'twitter_quote': 'Quoted'
};
var TwitterNoticeEvents = ['twitter_follow', 'twitter_unfollow', 'twitter_favorite'];

var Twitter = (function () {
    var scope = {};

    var $defaultMessageForm = null;

    var initialized = false;

    function init() {
        $defaultMessageForm = $('#lead-card-container.modal .lead-actions>.tab-content>.tab-pane#lead-action-tab-pane-twitter>.twitter-action .client-twitter-info .twitter-message-form[data-role="default"]').clone().hide();

        $(document).on('click', '.twitter-item .twitter-item-buttons a.twitter-item-button', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.cancelBubble = true;

            if ($(this).hasClass('disabled')) {
                return;
            }

            var type = $(this).data('event-type');

            showMessageForm($(this).closest('.twitter-item'), type);
        }).on('keyup', '.twitter-item .twitter-message-form textarea', function (e) {
            validateMessageLength($(this));
        }).on('click', '.twitter-item .twitter-message-form button.btn-save', function (e) {
            var $xhr = sendMessage($(this).closest('.twitter-message-form'));
            if ($xhr) {
                var $btn = $(this);
                $btn.html('<i class="fa fa-spin fa-spinner"></i> Sending...');
                $xhr.done(function () {
                    $btn.html('Send');
                }).fail(function () {
                    $btn.html('Send');
                });
            }
        }).on('click', '.twitter-item .twitter-message-form button.btn-cancel', function (e) {
            toggleMessageForm($(this).closest('.twitter-message-form'));
        });

        initialized = true;
    }

    function showMessageForm($twitterItem, type) {
        if (type == TwitterEventTypes.DIRECT) {
            toggleMessageForm($twitterItem.find('.twitter-message-form[data-event-type="' + TwitterEventTypes.DIRECT + '"]'));
        } else if (type == TwitterEventTypes.TWEET || type == TwitterEventTypes.RETWEET) {
            var $form = $twitterItem.find('>.twitter-message-form[data-event-type="' + type + '"]');
            if ($form.length <= 0) {
                $form = $defaultMessageForm.clone().attr('data-event-type', type).appendTo($twitterItem);
            }
            if (!toggleMessageForm($form)) {
                return;
            }
            
            var $textarea = $form.find("textarea");
            if (type == TwitterEventTypes.TWEET) {
                $textarea.attr('placeholder', 'Reply Message ...');
            } else {
                $textarea.attr('placeholder', 'Retweet Message ...');
            }
            var maxlen = 140;
            var id = $form.closest('.post').data('id');
            if (typeof id == 'undefined' || id == 'undefined') {
                id = null;
            }
            var textareaId = id ? 'twitter-message-' + id : null;
            var username = $twitterItem.find(".username").html();

            if (type == TwitterEventTypes.TWEET && username) {
                $form.find(".twitter-message-username").attr('for', textareaId).text('@' + username + ' ');
                var username_width = $form.find(".twitter-message-username").width() + 10;
                $form.find("textarea").css('text-indent', username_width + 'px');
                maxlen -= username.length + 2;
            } else {
                $form.find(".twitter-message-username").attr('for', null).empty();
                $form.find("textarea").css('text-indent', 0);
            }

            if (type == TwitterEventTypes.RETWEET) {
                $textarea.attr('required', false);
                maxlen = 116;
            }

            $textarea.attr('id', textareaId).empty().val('').attr('maxlength', maxlen);
            $form.find('.left-letter-count').html(maxlen).data('maxlen', maxlen);
        }
    }

    function toggleMessageForm(form) {
        var $form = $(form);
        if ($form.is(":visible")) {
            $form.stop().fadeOut({duration: 500, queue: false}).slideUp(500);
            return false;
        } else {
            $form.siblings('.twitter-message-form').hide();
            $form.stop().fadeIn({duration: 500, queue: false}).hide().slideDown(500).find('textarea').focus();
            return true;
        }
    }

    function validateMessageLength($textarea) {
        if ($textarea.attr('maxlength') > 140) {
            return;
        }
        setTimeout(function () {
            var val = $textarea.val();
            var len = val.length;
            if (navigator.userAgent.indexOf('NT') > -1) {
                var lines = val.match(/\r?\n|\r/g);
                if (lines) {
                    len += lines.length;
                }
            }
            var $left = $textarea.closest('.twitter-message-form').find('.left-letter-count');
            var left = $left.data('maxlen') - len;
            if (left < 0) {
                left = 0;
            }
            $left.html(left);
        });
    }

    function sendMessage($form) {
        var sourceId = $form.closest('.twitter-item').attr('data-twitter-item-id');
        if (typeof sourceId == 'undefined' || sourceId == 'undefined') {
            sourceId = null;
        }

        var type = $form.data('event-type');
        var description = $form.find('textarea').val();

        if (type != TwitterEventTypes.RETWEET && description == '') {
            showErrorMessage('Message', 'Enter the description.');
            $form.find('textarea').focus();
            return;
        }

        if (type == TwitterEventTypes.RETWEET && description != '') {
            type = TwitterEventTypes.QUOTE;
        }

        var message = {
            type: type,
            description: description,
            source_id: sourceId
        };
        
        var lead = $form.closest('.twitter-item').data('lead');

        return $.post('/api/v1/clients/' + lead.clients[0].id + '/twitter/messages', message, function (res) {
            if (res && res.id && res.id > 0) {
                showSuccessMessage("Messaging", TwitterEventTypeLabels[type] + " Success.");
                lead.twitter.unshift(res);
                $createTwitterItem(res).prependTo($("#lead-card-container.modal .lead-actions>.tab-content>.tab-pane#lead-action-tab-pane-twitter>.twitter-action .twitter-list-container .twitter-list")).fadeIn({duration: 500, queue: false}).hide().slideDown(500).find('[data-toggle="tooltip"]').tooltip({container: '#lead-card-container.modal'});
                toggleMessageForm($form);
                $form.reset();
                if (type != TwitterEventTypes.DIRECT) {
                    $form.find('.left-letter-count').html('140');
                }
            }
        });
    }

    scope.$createTwitterItemList = function (twitterItems) {
        if (!initialized) {
            init();
        }

        return $createTwitterItemList(twitterItems);
    };

    function $createTwitterItemList(twitterItems) {
        var $twitteritemList = $('<div class="twitter-item-list"></div>');

        $.each(twitterItems, function (index, twitteritem) {
            $createTwitterItem(twitteritem).appendTo($twitteritemList);
        });

        return $twitteritemList;
    }

    scope.$createTwitterItem = function (twitterItem) {
        if (!initialized) {
            init();
        }

        return $createTwitterItem(twitterItem);
    };

    function $createTwitterItem(twitterItem) {
        var sourceItem = twitterItem;
        if (twitterItem.type == TwitterEventTypes.RETWEET && twitterItem.attachments) {
            sourceItem = twitterItem.attachments.retweet;
        }

        var $twitterItem = $('\
            <div class="twitter-item">\
                <div class="twitter-item-inner">\
                    <span class="date-wrapper">\
                        <span class="twitter-item-date">' + (twitterItem.type == TwitterEventTypes.RETWEET ? twitterItem.sender.fullname + ' ' : '') + TwitterEventTypeLabels[twitterItem.type] + ' ' + getDateTimeString(twitterItem.created_at) + '</span>\
                    </span>\
                    <img class="twitter-item-profile-image" src="' + sourceItem.sender.avatar.replace(/normal/, '200x200') + '" />\
                    <div class="twitter-item-info">\
                        <div class="twitter-item-header">\
                            <h4 class="fullname">' + sourceItem.sender.fullname + '</h4>\
                            <span>@</span><span class="username">' + sourceItem.sender.username + '</span>\
                        </div>\
                    </div>\
                    <div class="twitter-item-buttons"></div>\
                </div>\
            </div>\
        ').data('twitter-item', twitterItem)
                .data('lead', twitterItem.lead)
                .attr('id', 'twitter-item-' + twitterItem.id)
                .attr('data-twitter-item-id', twitterItem.id)
                .attr('data-event-tyye', twitterItem.type);

        if (twitterItem.is_own) {
            $twitterItem.addClass('mine');
        }

        if (!twitterItem.is_own
                && twitterItem.type != TwitterEventTypes.DIRECT
                && twitterItem.type != TwitterEventTypes.FOLLOW
                && twitterItem.type != TwitterEventTypes.UNFOLLOW
                && twitterItem.type != TwitterEventTypes.LIKE) {
            $twitterItem.find('.twitter-item-buttons')
                    .append('<a href="#" class="twitter-item-button" data-event-type="' + TwitterEventTypes.TWEET + '" data-toggle="tooltip" title="Reply to Tweet"><i class="fa fa-reply"></i></a>')
                    .append('<a href="#" class="twitter-item-button" data-event-type="' + TwitterEventTypes.RETWEET + '" data-toggle="tooltip" title="Retweet"><i class="fa fa-retweet"></i></a>');
        }

        if (TwitterNoticeEvents.indexOf(twitterItem.type) > -1) {
            $twitterItem.addClass('has-notice').find(".twitter-item-info").append('<div class="twitter-item-notice"><span>' + TwitterEventTypeLabels[twitterItem.type] + '</span> <h4 class="fullname">' + twitterItem.recipient.fullname + '</h4> <span>@</span><span class="username">' + twitterItem.recipient.username + '</span></div>');
        } else {
            $twitterItem.find(".twitter-item-info").append('<div class="twitter-item-description">' + style_tweet(sourceItem.description) + '</div>')
        }

        if (twitterItem.type == TwitterEventTypes.QUOTE && twitterItem.attachments) {
            var retweet = twitterItem.attachments.retweet;
            $('<div class="twitter-item">\
                <div class="twitter-item-info">\
                    <div class="twitter-item-header">\
                        <h4 class="fullname">' + retweet.sender.fullname + '</h4>\
                        <span>@</span><span class="username">' + retweet.sender.username + '</span>\
                    </div>\
                    <div class="twitter-item-description">' + retweet.description + '</div>\
                </div>\
            </div>').insertBefore($twitterItem.find('.twitter-item-buttons'));
        }

        twitterItem.$element = $twitterItem;

        $twitterItem.append('<span class="mark"><span class="icon"><i class="fa fa-envelope"></i></span></span>');
        return $twitterItem;
    }

    return scope;
})();