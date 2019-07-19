/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
require('../global.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
//require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

// $(document).ready(function() {
//     $('[data-toggle="popover"]').popover();
// });

//console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

require('video.js/dist/video-js.css')

window.videojs = require('video.js/dist/video.js')
require('videojs-flash/dist/videojs-flash.js')
require('videojs-contrib-hls/dist/videojs-contrib-hls.js')

require('jquery.scrollto')

$('button#go_to_current').click(function () {
    $('#program_wrapper').scrollTo($('tr.onAir', $('#program')), {duration: 600} );
});

function getPos(currentPos)
{
	var lastSubtitleTime = "";
	$('#program').find('tr').each(
		function() {
			var itemTime = $(this).attr("begin");
			if (currentPos >= itemTime)
			{
				lastSubtitleTime=$(this).attr("begin");
			}
		}
	);

	return lastSubtitleTime;
}

var programTime, oldPos;

$.getJSON('/program/json', {}, function (data, textStatus, jqXHR) {
	if(data.programs)
	{
		$.each(data.programs, function(index, item) {
            var startTime = new Date(item.begin);
            var startTime = new Date();
                startTime.setHours(6);
                startTime.setMinutes(0);
                startTime.setSeconds(item.begin);

            var hours = startTime.getHours();

            if (hours < 10) {
                    hours = '0' + hours;
            }

            var minutes = startTime.getMinutes();

            if (minutes < 10) {
                    minutes = '0' + minutes;
            }

            var seconds = startTime.getSeconds();

            if (seconds < 10) {
                    seconds = '0' + seconds;
            }

            startTime = hours + ':' + minutes + ':' + seconds;

			$('#program').append('<tr begin="' + item.begin + '" end="' + item.end + '"><td><span class="label">' + startTime + '</span> ' + item.name + '</td></tr>');
		});

		programTime = data.time;

		setInterval(function(){
			//console.log(programTime + ' ' + getPos(data.time));
			var pos = getPos(programTime);
			if(pos != oldPos)
			{
				oldPos = pos;
				$('#program tr').removeClass('onAir');
				$('#program tr[begin="'+ pos +'"]').addClass('onAir', true);
				//$('#program').scrollTo($('tr[begin="'+pos+'"]', $('#program')), {duration: 600} );

                $('#program_wrapper').scrollTo($('tr[begin="'+pos+'"]', $('#program')), {duration: 600} );
			}
			programTime++;
		}, 1000);
	}
});
