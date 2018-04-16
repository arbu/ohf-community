$(document).ready(function() {

    var calendar = $('#calendar');

    // Initialite the calendar
    calendar.fullCalendar({
        themeSystem: 'bootstrap4',
        height: "auto",
        locale: locale,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        defaultView: 'timelineMonth',
        firstDay: 1,
        weekends: true,
        weekNumbers: true,
        weekNumbersWithinDays: true,
        navLinks: true,
        eventLimit: true,
        events: listEventsUrl,
        editable: false,
        resources: listResourcesUrl,
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        resourceAreaWidth: '15%',
        resourceLabelText: 'Jobs',
    });
});
