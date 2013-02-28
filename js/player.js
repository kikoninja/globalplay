var host = window.location.protocol+'//'+window.location.host;

var port = '3000';

var socket = io.connect(host+':'+port);

var params = { allowScriptAccess: "always" };
var atts = { id: "player" };
var video = $('#video').val();
var list = $('#list').val();

if(video!='')
	initPlayer(video);

function initPlayer(video){
	swfobject.embedSWF("http://www.youtube.com/v/"+video+"?enablejsapi=1&playerapiid=player&version=3",
                   "playerapi", "854", "510", "8", null, null, params, atts);
	list = $('#list').val();
	socket = io.connect(host+':'+port);
}

var player;
function onYouTubePlayerReady(playerId) {
	player = document.getElementById('player');
	player.addEventListener("onStateChange", "onStateChange");
}

var listVideos = [];
function onPlayerReady(event) {
	// player.cuePlaylist({list: playlist, listType: 'playlist'});
}

function onStateChange(state) {
	switch(state){
		case -1:
			// console.log(player.getVideoUrl());
			break;
		case 0:
			player.loadVideoById(playlist.next());
			break;
		case 1:
			// if(listVideos.length==0){
			// 	listVideos = player.getPlaylist();
			// 	populatePlaylist();
			// }
			// setPlaylistButtonStyle();
			break;
		case 2:
			break;
		case 3:
			break;
		case 4:
			break;
		case 5:
			//player.playVideo();
			break;
	}
	// console.log(state);
}

socket.on('connect', function(){
	socket.emit('room', list);
});

var playlist = {
	items: function(){
		return $('.playlist .list-item');
	},
	active: function(){
		return $('.playlist .list-item.active');
	},
	id: function(item){
		return item.attr('data-item-id');
	},
	next: function(){
		var active = this.active();
		active.removeClass('active');
		var next = active.next().hasClass('list-item')?active.next():$('.playlist .list-item:first');
		next.addClass('active');
		return this.id(next);
	},
	prev: function(){
		var active = this.active();
		active.removeClass('active');
		var prev = active.prev().hasClass('list-item')?active.prev():$('.playlist .list-item:last');
		prev.addClass('active');
		return this.id(prev);
	},
	videoAt: function(index){
		this.active().removeClass('active');
		var item = $(this.items().get(index));
		item.addClass('active');
		return this.id(item);
	},
	getIndex: function(item){
		return item.index();
	},
	index: function(){
		return this.active().index();
	},
	sync: function(args){
		if(args==undefined)
			args = {};

		var def = {
			state: 1,
			time: new Date().getTime(),
			room: list
		}

		return $.extend(def, args);
	}
}

var playItem = function(index){
	player.loadVideoById(playlist.videoAt(index));
}

socket.on('update', function (data) {
	// console.log(data);
	if(data.index!=undefined){
		playItem(data.index);
	}

	var diff = (new Date().getTime() - data.time)/1000;
	var seekTo = data.seekTo!=undefined?data.seekTo+diff:player.getCurrentTime()+diff;

	player.seekTo(seekTo);

	switch(data.state){
		case -1:
			player.stopVideo();
			break;
		case 0:
			break;
		case 1:
			player.playVideo();
			break;
		case 2:
			player.pauseVideo();
			break;
		case 3:
			break;
		case 4:
			break;
		case 5:
			//player.playVideo();
			break;
	}
});

$('#play').click(function(e){
	e.preventDefault();
	var time = new Date().getTime();
	player.playVideo();
	socket.emit('sync', playlist.sync());
});

$('#pause').click(function(e){
	e.preventDefault();
	var time = new Date().getTime();
	player.pauseVideo();
	socket.emit('sync', playlist.sync({state: 2}));
});

$('#stop').click(function(e){
	e.preventDefault();
	var time = new Date().getTime();
	player.stopVideo();
	socket.emit('sync', playlist.sync({state: -1}));
});

$('#prev').click(function(e){
	e.preventDefault();
	// var index = player.getPlaylistIndex();
	// if(index==0)
	// 	index = listVideos.length-1;
	// else
	// 	index -= 1;
	var time = new Date().getTime();
	// player.playVideoAt(index);
	player.loadVideoById(playlist.prev());
	socket.emit('sync', playlist.sync({index: playlist.index()}));
});

$('#next').click(function(e){
	e.preventDefault();
	// var index = player.getPlaylistIndex();
	// if(index==listVideos.length-1)
	// 	index = 0;
	// else
	// 	index += 1;
	var time = new Date().getTime();
	// player.playVideoAt(index);
	player.loadVideoById(playlist.next());
	socket.emit('sync', playlist.sync({index: playlist.index()}));
});

$('#sync').click(function(e){
	e.preventDefault();
	var index = player.getPlaylistIndex();
	var seekTo = player.getCurrentTime();
	var time = new Date().getTime();
	socket.emit('sync', playlist.sync({seekTo: seekTo}));
});

function populatePlaylist(){
	// var div = $('<div></div>');
	// div.css({
	// 	height: 102,
	// 	width: 132*listVideos.length
	// });
	for(var i=0; i<listVideos.length; i++){
		var btn = $('<a href="#" class="list_btn" id="'+i+'"><img src="http://img.youtube.com/vi/'+listVideos[i]+'/default.jpg" /></a>');
		btn.click(function(e){
			e.preventDefault();
			var index = $(this).attr('id');
			var time = new Date().getTime();
			player.playVideoAt(index);
			socket.emit('sync', {state: 1, time: time, index: index});
		});
		$('.playlist').append(btn);
	}
	// $('.playlist').html(div);
}