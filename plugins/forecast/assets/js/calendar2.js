class GoogleCalendar {

    constructor() {
        this.clientId = 'primary'; //choose web app client Id, redirect URI and Javascript origin set to http://localhost
        this.apiKey = 'YOUR_APIKEY_HERE'; //choose public apiKey, any IP allowed (leave blank the allowed IP boxes in Google Dev Console)
        this.userEmail = "YOUR_ADDRESS@gmail.com"; //your calendar Id
        this.userTimeZone = "YOUR_TIME_ZONE_HERE"; //example "Rome" "Los_Angeles" ecc...
        this.maxRows = 10; //events to shown
        this.calName = "YOUR CALENDAR NAME"; //name of calendar (write what you want, doesn't matter)
            
        this.scopes = 'https://www.googleapis.com/auth/calendar';
        this.handleClientLoad();
    }
    
    handleClientLoad() {
        function printCalendar() {
            // The "Calendar ID" from your calendar settings page, "Calendar Integration" secion:
            // 1. Create a project using google's wizzard: https://console.developers.google.com/start/api?id=calendar
            // 2. Create credentials:
            //    a) Go to https://console.cloud.google.com/apis/credentials
            //    b) Create Credentials / API key
            //    c) Since your key will be called from any of your users' browsers, set "Application restrictions" to "None",
            //       leave "Website restrictions" blank; you may optionally set "API restrictions" to "Google Calendar API"
            var apiKey = 'YOUR_API_KEY';
            // You can get a list of time zones from here: http://www.timezoneconverter.com/cgi-bin/zonehelp
            var userTimeZone = "Europe/Budapest";
    
            // Initializes the client with the API key and the Translate API.
            gapi.client.init({
                'apiKey': apiKey,
                // Discovery docs docs: https://developers.google.com/api-client-library/javascript/features/discovery
                'discoveryDocs': ['https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest'],
            }).then(function () {
                // Use Google's "apis-explorer" for research: https://developers.google.com/apis-explorer/#s/calendar/v3/
                // Events: list API docs: https://developers.google.com/calendar/v3/reference/events/list
                return gapi.client.calendar.events.list({
                    'calendarId': primary,
                    'timeZone': userTimeZone,
                    'singleEvents': true,
                    'timeMin': (new Date()).toISOString(), //gathers only events not happened yet
                    'maxResults': 20,
                    'orderBy': 'startTime'
                });
            }).then(function (response) {
                if (response.result.items) {
                    var calendarRows = ['<table class="wellness-calendar"><tbody>'];
                    response.result.items.forEach(function(entry) {
                        var startsAt = moment(entry.start.dateTime).format("L") + ' ' + moment(entry.start.dateTime).format("LT");
                        var endsAt = moment(entry.end.dateTime).format("LT");
                        calendarRows.push(`<tr><td>${startsAt} - ${endsAt}</td><td>${entry.summary}</td></tr>`);
                    });
                    calendarRows.push('</tbody></table>');
                    $('#wellness-calendar').html(calendarRows.join(""));
                }
            }, function (reason) {
                console.log('Error: ' + reason.result.error.message);
            });
        };
    }
    
    checkAuth() {
        gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: true}, handleAuthResult);
    }
    
    handleAuthResult(authResult) {
        if (authResult) {
            makeApiCall();
        }
    }
    
    makeApiCall() {
        var today = new Date(); //today date
        gapi.client.load('calendar', 'v3', function () {
        //     var request = gapi.client.calendar.events.list({
        //         'calendarId' : userEmail,
        //         'timeZone' : userTimeZone, 
        //         'singleEvents': true, 
        //         'timeMin': today.toISOString(), //gathers only events not happened yet
        //         'maxResults': maxRows, 
        //         'orderBy': 'startTime'});
        //     request.execute(function (resp) {
        //     for (var i = 0; i < resp.items.length; i++) {
        //         var li = document.createElement('li');
        //         var item = resp.items[i];
        //         var classes = [];
        //         var allDay = item.start.date? true : false;
        //         var startDT = allDay ? item.start.date : item.start.dateTime;
        //         var dateTime = startDT.split("T"); //split date from time
        //         var date = dateTime[0].split("-"); //split yyyy mm dd
        //         var startYear = date[0];
        //         var startMonth = monthString(date[1]);
        //         var startDay = date[2];
        //         var startDateISO = new Date(startMonth + " " + startDay + ", " + startYear + " 00:00:00");
        //         var startDayWeek = dayString(startDateISO.getDay());
        //         if( allDay == true){ //change this to match your needs
        //             var str = [
        //             '<font size="4" face="courier">',
        //             startDayWeek, ' ',
        //             startMonth, ' ',
        //             startDay, ' ',
        //             startYear, '</font><font size="5" face="courier"> @ ', item.summary , '</font><br><br>'
        //             ];
        //         }
        //         else{
        //             var time = dateTime[1].split(":"); //split hh ss etc...
        //             var startHour = AmPm(time[0]);
        //             var startMin = time[1];
        //             var str = [ //change this to match your needs
        //                 '<font size="4" face="courier">',
        //                 startDayWeek, ' ',
        //                 startMonth, ' ',
        //                 startDay, ' ',
        //                 startYear, ' - ',
        //                 startHour, ':', startMin, '</font><font size="5" face="courier"> @ ', item.summary , '</font><br><br>'
        //                 ];
        //         }
        //         li.innerHTML = str.join('');
        //         li.setAttribute('class', classes.join(' '));
        //         document.getElementById('events').appendChild(li);
        //     }
        // document.getElementById('updated').innerHTML = "updated " + today;
        // document.getElementById('calendar').innerHTML = calName;
        });
    }
}