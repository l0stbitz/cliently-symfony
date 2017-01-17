//Tour has been deprecated

var tour = null;
function initializeTour() {
    tour = new Shepherd.Tour({
        defaults: {
            classes: 'shepherd-element shepherd-open shepherd-theme-arrows',
            scroll: true,
            buttons: [
                {
                    text: 'Next',
                    action: moveTourNext,
                    classes: 'shepherd-button-example-primary'
                }
            ],
        }
    });
    tour.addStep({
        id: 'step-0',
        text: "We've gathered some potential gigs for you, based on your location and interests.",
        attachTo: '.columns .column:first-child left'
    }).addStep({
        id: 'step-1',
        text: "Let's check out this first opportunity we've discovered",
        attachTo: '.columns .column:first-child .portlet:first-child left'
    }).addStep({
        id: 'step-2',
        text: "Accept if the prospect looks promising and you would like to get detailed information.<br /><br />" +
                "If you don't like the prospect -- just Reject it.<br /><br />" +
                "Only Accepted prospects count toward your monthly limit.<br /><br />" +
                "Let's go ahead and Accept this one.",
        attachTo: '.lead-source right'
    }).addStep({
        id: 'step-3',
        text: "BOOM!<br /><br />" +
                "Once Accepted, you will get information about your potential client and can easily follow or tweet them to start a conversation.",
        attachTo: '.personal-info-and-lead-source left'
    }).addStep({
        id: 'step-4',
        text: "Easily create tasks, notes or send emails directly from Cliently.<br /><br />" +
                "Pretty slick, right?",
        attachTo: '.tabs-container top'
    }).addStep({
        id: 'step-5',
        text: "One last thing.<br /><br />" +
                "If you want to change location or look for gigs in other industries, check out Sources page.",
        attachTo: '.left-menu .page-menu .page-menu-item.sources left'
    }).addStep({
        id: 'step-6',
        text: "This is the gig generation source we've created based on your location and interest.",
        attachTo: '.columns .column:first-child left'
    }).addStep({
        id: 'step-7',
        text: "To add a new source, just click this button any time and we will search for more gigs.<br /><br />" +
                "That's it! Go check out the prospects we've found for you!",
        attachTo: '.add-lead-source-li bottom',
        buttons: [
            {
                text: 'Finish',
                classes: 'shepherd-button-example-primary',
                action: function () {
                    tour.cancel();
                }
            }
        ]
    }).on('cancel', function () {
        var steps_before_finish = TourSteps.LAST - tour_step;

        tour_step = TourSteps.COMPLETED;
        $('.shepherd-not-target').removeClass('shepherd-not-target');
        $('body').removeClass('shepherd-active');
        $("#tour-back").remove();
        $("#tour-step-counter").fadeOut(function () {
            $(this).remove();
        });
        $.ajax({
            url: '/api/v1/users/me',
            method: 'PUT',
            data: {wizard: -1},
            success: function () {


                if (typeof analytics !== 'undefined' && analytics !== null) {
                    analytics.track('Wizard Completed', {
                        steps_before_finish: steps_before_finish
                    });
                }
            }
        });
        activatePage(Pages.MAIN_DASHBOARD);
    });

    $('<div id="tour-back">').appendTo('body');

    $('<div id="tour-step-counter"><span class="value"></span></div>').appendTo('body');
}

function startTour() {
    tour_step = TourSteps.START;
    openTour();
}

function openTour() {
    if (tour_step < TourSteps.START) {
        return;
    }

    if (tour) {
        tour.hide();
        $('.shepherd-not-target').removeClass('shepherd-not-target');
        $('body').removeClass('shepherd-active');
    }

    if (tour_step > TourSteps.LAST) {
        tour.cancel();
        return;
    }

    if (tour_step < TourSteps.LEFT_MENU) {
        if (!$("#page-clients").length) {
            activatePage(Pages.MAIN_DASHBOARD);
            return false;
        }
        if (tour_step >= TourSteps.CLIENT_DETAILS) {
            if (!$('#mini-info-menu').parent().is(":visible")) {
                $('.columns .column:first-child .portlet:first-child').click();
                return false;
            }
            if (tour_step > TourSteps.CLIENT_DETAILS) {
                if (!$('#mini-info-menu').parent().hasClass("lead-approved")) {
                    $(".lead-source .form-actions .accept").click();
                    return false;
                }

                if (tour_step == TourSteps.CLIENT_DETAILS_TASKS) {
                    var ele = $("#mini-info-menu")[0];
                    ele.scrollTop = ele.scrollHeight;
                }
            }
        }
    } else if (tour_step == TourSteps.LEFT_MENU) {
        if ($('#mini-info-menu').parent().is(":visible")) {
            $('#mini-info-menu').parent().click();
        }
    } else if (tour_step > TourSteps.LEFT_MENU) {
        if (!$("#page-sources").length) {
            activatePage(Pages.LEAD_SOURCES);
            return false;
        }
    }

    if (tour == null) {
        initializeTour();
    }

    tour.show('step-' + tour_step);
    $('body').addClass('shepherd-active');
    $("#tour-step-counter .value").html((tour_step + 1) + ' / 8');

    $('body.shepherd-active *').each(function () {
        var $this = $(this);
        if (!$this.is('.shepherd-element.shepherd-open') &&
                !$this.is('.shepherd-target.shepherd-enabled') &&
                !$this.is('#tour-back') &&
                !$this.is('#tour-step-counter') &&
                !$this.is('.intercom-container') &&
                !$this.find('.shepherd-element.shepherd-open, .shepherd-target.shepherd-enabled, #tour-back, #tour-step-counter, .intercom-container').length &&
                !$this.parents('.shepherd-element.shepherd-open, .shepherd-target.shepherd-enabled, #tour-back, #tour-step-counter, .intercom-container').length &&
                !$this.closest('.shepherd-not-target').length) {
            $this.addClass('shepherd-not-target');
        }
    });

    return true;
}

function moveTourNext() {
    tour_step++;
    openTour();
}
