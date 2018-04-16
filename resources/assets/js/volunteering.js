$(document).ready(function() {

    var calendar = $('#calendar');

    // Initialite the calendar
    calendar.fullCalendar({
        themeSystem: 'bootstrap4',
        height: "auto",
        locale: locale,
        slotLabelFormat: 'H:mm',
        minTime: '08:00', // TODO  scrollTime: '08:00',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,timelineDay'
        },
        resourceLabelText: 'Resources',
        views: {
            month: {
                buttonText: 'Month'
            },
            timelineDay: {
                buttonText: 'Timeline'
            }
        },
        defaultView: 'timelineDay',
        firstDay: 1,
        weekends: true,
        weekNumbers: true,
        weekNumbersWithinDays: true,
        navLinks: true,
        eventLimit: true,
        events: listEventsUrl,
        editable: false,
        unselectAuto: false,
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
    });
});
