const $ = require('jquery');

require("jquery-ui/ui/widgets/draggable");
require("jquery-ui/ui/widgets/sortable");

require("jquery-ui/themes/base/draggable.css");
require("jquery-ui/themes/base/sortable.css");

require('../global.scss')

var MediaType = require('./clip/MediaType'),
InputType = require('./clip/InputType'),
Paginator = require('./clip/Paginator'),
ResourceList = require('./clip/ResourceList');

var paginator = new Paginator('#catalog-paginator', function(e) {
    mediaList.setPage($(e.currentTarget).data('page'));
    mediaList.reload();
});

function dblClickHandler(e) {
    $(e.currentTarget).clone().appendTo('#playlist')
        .css('backgroundColor', '#f8f8fe')
        .find('button').toggleClass('d-none', false).click(removeButtonHandler);

    updateInput($('#playlist'));
    updatePlaylistDuration();
    updatePlaylistItemsStartTime();
}

function listItem(item) {
    var subItems = '';
    for (var index in item.files) {
        subItems += '<li data-duration="' + item.files[index].file.duration + '">' + item.files[index].file.name + '</li>';
    }

    return '<li data-id="' + item.id + '" data-duration="' + item.duration + '"'
        + ' class="ui-state-default ui-draggable text-truncate list-group-item" style="display: list-item;"></span>'
        + item.name
        + '<button type="button" class="close d-none" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
        + '<ul class="d-none">' + subItems + '</ul>'
        +'</li>';
}
var deleteButtonSelector = '#playlist button.close';
var mediaList = new ResourceList('/files/',function(data) {
    paginator.setCurrent(data.page);
    paginator.setPages(data.pages);
    paginator.render();



    $('ul#catalog > li').remove();

    $(data._embedded.items).each(function(index, item){
        $('ul#catalog').append(listItem(item));
    });

    $('ul#catalog > li').dblclick(dblClickHandler);

    $( "ul#catalog > li" ).draggable({
        appendTo: "body" ,
        connectToSortable: "#playlist",
        helper: "clone",
        revert: "invalid",
        stop: function(event, ui) {
            $(deleteButtonSelector).click(removeButtonHandler);
            $(ui.helper)
                .css('transform', 'rotate(0)')
                .css('webkit-transform', 'rotate(0)')
                .css('backgroundColor', '#f8f8fe')
                .css('height', 'auto')
                .find('button, ul').toggleClass('d-none', false)
            ;
        },
        start: function(event, ui) {
            $(ui.helper)
                .css('width', Math.ceil($(event.target).outerWidth()) + 'px')
                .css('transform', 'rotate(-7deg)')
                .css('webkit-transform', 'rotate(-7deg)')
            ;
        }
    });

    $(deleteButtonSelector).click(removeButtonHandler);

    updatePlaylistDuration();
    updatePlaylistItemsStartTime();
});
mediaList.setSort('name', 'asc')
mediaList.reload();

var mediaType = new MediaType('#media-type button', function(value) {
    mediaList.setSort('name', 'asc')
    mediaList.setCriteria('type','equal', value);
    mediaList.reload();
});

var inputType = new InputType('#media-search', function(value) {
    mediaList.setCriteria('search','contains', value);
    mediaList.reload();
});

function removeButtonHandler(event)
{
    var el = $(event.currentTarget).parent();
    el.trigger('remove').remove();

    updateInput($('#playlist'));
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

function updatePlaylistItemsStartTime(children, currentDuration = 0)
{
    if (!children) children = $('#playlist').children();

    $.each(children, function(index, row) {

        var startTime = formatDurationToReadableTime(currentDuration);

        if($(row).find('span.start').length) {
            $(row).find('span.start').text(startTime);
        } else {
            $(row).prepend('<span class="start">' + startTime + '</span> ');
        }

        updatePlaylistItemsStartTime($(row).find('ul').children(), currentDuration);

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

//$( "#playlist li, #catalog li" ).disableSelection();
