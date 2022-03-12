require(['jquery', 'jquery/ui'], function ($) {
    var start = new Date;
    var endingHour = window.shippingHour.split(':');
    start.setHours(endingHour, 0, 0); // 2pm
    function pad(num) {
        return ("0" + parseInt(num)).substr(-2);
    }

    function tick() {
        var now = new Date;
        if (now > start) { // too late, go to tomorrow
            start.setDate(start.getDate() + 1);
        }
        var remain = ((start - now) / 1000);
        var hh = pad((remain / 60 / 60) % 60);
        if(hh == '00'){
            var hour = ''
            hh = '';
        } else if(hh == '01'){
            var hour = 'Stunde'
        } else {
            var hour = 'Stunden';
        }
        var mm = pad((remain / 60) % 60);
        var ss = pad(remain % 60);
        document.getElementById('counting-time').innerHTML =
            hh + " " + hour + " " + mm + " Minute " + ss + " Sekunden ";
        setTimeout(tick, 1000);
    }
    tick();
})