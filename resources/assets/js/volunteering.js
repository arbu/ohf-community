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
            right: 'timelineWeek,timelineMonth'
        },
        buttonText: {
            today: todayLabel,
            timelineWeek: weekLabel,
            timelineMonth: monthLabel,
        },
        viewRender: function(view, element){
            localStorage.setItem('volunteer-calendar-view-name', view.name)
        },
        defaultView: localStorage.getItem('volunteer-calendar-view-name') ? localStorage.getItem('volunteer-calendar-view-name') : 'timelineWeek',
        firstDay: 1,
        slotDuration: '24:00:00',
        //slotWidth: 10,
        weekends: true,
        weekNumbers: true,
        weekNumbersWithinDays: true,
        navLinks: true,
        eventLimit: true,
        events: listEventsUrl,
        editable: false,
        resources: listResourcesUrl,
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        //resourceAreaWidth: '15%',
        resourceLabelText: resourceLabel,
    });
});
