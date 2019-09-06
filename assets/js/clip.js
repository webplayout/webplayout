const $ = require('jquery');

require("jquery-ui/ui/widgets/tabs");
require("jquery-ui/ui/widgets/draggable");
require("jquery-ui/ui/widgets/sortable");
require("jquery-ui/ui/widgets/resizable");

require("jquery-ui/themes/base/all.css");
require("jquery-ui/themes/base/tabs.css");
require("jquery-ui/themes/base/sortable.css");
require("jquery-ui/themes/base/resizable.css");

require('../global.scss')

//<link href="{{asset('bundles/omarevtv/css/smoothness/jquery-ui-1.8.18.custom.css')}}" rel="stylesheet" type="text/css">
//<link href="{{asset('bundles/omarevtv/css/bootstrap.css')}}" rel="stylesheet">
//<link href="{{asset('bundles/omarevtv/css/bootstrap-responsive.css')}}" rel="stylesheet">
//
//<script src="{{asset('bundles/omarevtv/js/jquery-1.7.1.min.js')}}" type="text/javascript"></script>
//<script src="{{asset('bundles/omarevtv/js/jquery-ui-1.8.18.custom.min.js')}}" type="text/javascript"></script>
//<script src="{{asset('bundles/omarevtv/js/bootstrap.min.js')}}" type="text/javascript"></script>
//<script src="{{asset('bundles/omarevtv/js/admin.js')}}" type="text/javascript"></script>

$( '#catalog' ).tabs({ heightStyle: 'fill' });

function removeButtonHandler(event)
{
    var el = $(event.target).parent().parent();
    $(event.target).parent().trigger('remove').remove();

    updateInput(el);
    updatePlaylistDuration();
    updatePlaylistItemsStartTime();
}

function formatDurationToReadableTime(seconds)
{
    var startDate = new Date();
        startDate.setHours(0);
        startDate.setMinutes(0);
        startDate.setSeconds(seconds);

    var hours = startDate.getHours();

    if (hours < 10) {
        hours = '0' + hours;
    }

    var minutes = startDate.getMinutes();

    if (minutes < 10) {
        minutes = '0' + minutes;
    }

    var seconds = startDate.getSeconds();

    if (seconds < 10) {
        seconds = '0' + seconds;
    }

    return hours + ':' + minutes + ':' + seconds;
}

function updatePlaylistDuration()
{
    var currentDuration = 0;

    $.each($('#playlist').children(), function(index, row) {
        currentDuration += parseInt($(row).data('duration'));
    });

    currentDuration = formatDurationToReadableTime(currentDuration);

    $('#playlistDuration').html(currentDuration);
}

function updatePlaylistItemsStartTime()
{
    var currentDuration = 0;

    $.each($('#playlist').children(), function(index, row) {

        var startTime = formatDurationToReadableTime(currentDuration);

        if($(row).find('span.start').length) {
            $(row).find('span.start').text(startTime);
        } else {
            $(row).prepend('<span class="start">' + startTime + '</span> ');
        }

        currentDuration += $(row).data('duration');
    });
}

updateInput = (elem) => {
    let items = $(elem).children().map((index, el) => {
        return $(el).data('id');
    }).toArray();

    $('#app_clip_files').val(items.join(','));
};

$("#playlist").sortable({
    revert: true,
    over: function( event, ui ) {

        ui.item.css('width', Math.ceil($(event.target).width()) + 'px');

    },
    update: (event, ui)  => {

        updatePlaylistDuration();
        updatePlaylistItemsStartTime();

        updateInput(event.target);
    }
});

$( "ul, li" ).disableSelection();

$( "#catalog" ).resizable({
    alsoResize: "#tabs-1"
});

$.getJSON('/files/', {limit:1000}, function(data) {

    $(data._embedded.items).each(function(index, item){
        $('#tabs-1 ul').append('<li data-id="' + item.id + '" data-duration="' + item.duration + '"'
            + ' class="ui-state-default ui-draggable" style="display: list-item;">'
            + '<span class="ui-icon ui-icon-document"></span>'
            + item.name + '<a class="ui-icon ui-icon-minus"></a></li>');
    });

    $( "#catalog .list li" ).draggable({
        appendTo: "body" ,
        connectToSortable: "#playlist",
        helper: "clone",
        revert: "invalid",
        stop: function(event, ui) {
            $('#playlist .ui-icon-minus').click(removeButtonHandler);
            //$(ui.helper).css('width', `auto`);

            $(ui.helper).css('transform', 'rotate(0)');
            $(ui.helper).css('webkit-transform', 'rotate(0)');
        },
        start: function(event, ui) {
          //$(ui.helper).css('width', `${ $(event.target).width() }px`);

          $(ui.helper).css('transform', 'rotate(-7deg)');
          $(ui.helper).css('webkit-transform', 'rotate(-7deg)');
          //$(ui.helper).css('display', 'inline-block');
          $(ui.helper).css('border', '1px solid red');
          //display: 'inline-block',
       }
    });

    $('#playlist .ui-icon-minus').click(removeButtonHandler);

    $('#playlist a.ui-draggable').remove();

    updatePlaylistDuration();
    updatePlaylistItemsStartTime();
});
