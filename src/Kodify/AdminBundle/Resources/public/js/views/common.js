
function validateStrTimes(start, end)
{
    return (end > start)
}

function secondsToTime(secs)
{
    var t = new Date(1970,0,1);
    t.setSeconds(secs);
    var s = t.toTimeString().substr(0,8);
    if(secs > 86399)
        s = Math.floor((t - Date.parse("1/1/70")) / 3600000) + s.substr(2);
    return s;
}

function timeToMinutes(time)
{
    timeArray = time.split(':');
    minutes = timeArray[0] * 60 + timeArray[1];
    if(timeArray[2] > 30){
        minutes++;
    }
    return minutes;
}

function validateTags(tagList)
{
    var functionResponse = jQuery.ajax({
        url: validateTagsUrl,
        type: 'POST',
        dataType: 'json',
        data: {'list' : tagList},
        async:false
    }).responseText;

    return JSON.parse(functionResponse);
}

function validatePornstars(pornstarsList)
{
    var functionResponse = jQuery.ajax({
        url: validatePornstarsUrl,
        type: 'POST',
        dataType: 'json',
        data: {'list' : pornstarsList},
        async:false
    }).responseText;

    return JSON.parse(functionResponse);
}