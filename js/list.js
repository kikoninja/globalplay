var API_KEY = 'AIzaSyAf_iglqYWK0lMWZJfaY51jAHwQXYkD6XA';

function ytAPI(options, callback){
	var def = {
		url: 'https://www.googleapis.com/youtube/v3/',
		type: 'get',
		data: {part: 'snippet', videoSyndicated: 'true'},
		comm: 'search'
	};
	$.extend(def, options);
	var data = '';
	$.each(def.data, function(key, val){
		data += key+'='+val+'&';
	});
	data += 'key='+API_KEY;

	$.ajax({
		type: def.type,
		url: def.url+def.comm,
		data: data,
		dataType: 'json',
		success: function(msg){
			callback(msg);
		}
	});
}

$('#search-query').keypress(function(e){
	if(e.keyCode!==13)
		return;

	ytAPI({
		data:{
			part: 'snippet',
			q: $('#search-query').val(),
			fields: 'nextPageToken,prevPageToken,items(id,snippet(title,thumbnails(default)))'
		}
	}, renderSearchResults);
}).focus(function(){
	if($('.search-results').html()=='')
		return;

	$('.search-results').show();
});

$(document).on("click", "#search-next, #search-prev", function(e){
	e.preventDefault();
	var pageToken = $(this).attr('data-token');
	console.log(pageToken);
	if(pageToken=='')
		return;
	ytAPI({
		data:{
			part: 'snippet',
			q: $('#search-query').val(),
			fields: 'nextPageToken,prevPageToken,items(id,snippet(title,thumbnails(default)))',
			pageToken: pageToken
		}
	}, renderSearchResults);
});

var sortStatus = {};
$('.playlist').sortable({
	handle: '.list-item-thumb',
	items: '.list-item',
	connectWith: '.trash-can',
	receive: function(event, ui){
		// ui.item.find('.add-to-list').remove();
		if(video==''){
			video = ui.item.attr('data-item-id');
			initPlayer(video);
		}

		$.ajax({
			url: baseURL+'item/add',
			type: 'post',
			data: 'list_id='+list+'&item_id='+ui.item.attr('data-item-id')+'&title='+ui.item.find('.list-item-text').text()+'&position='+ui.item.index(),
			success: function(res){
				ui.item.attr('data-id', res);
			}
		});
	},
	start: function(event, ui){
		$('.trash-can').show();
	},
	stop: function(event, ui){
		$('.trash-can').hide();
		if(sortStatus.update===true&&sortStatus.remove!==true)
			$.ajax({
				url: baseURL+'item/sort',
				type: 'post',
				data: 'list_id='+list+'&id='+ui.item.attr('data-id')+'&position='+ui.item.index()
			});
	},
	remove: function(event, ui){
		sortStatus.remove = true;
		// ui.item.remove();
		$.ajax({
			url: baseURL+'item/remove',
			type: 'post',
			data: 'id='+ui.item.attr('data-id')
		});
	},
	update: function(event, ui){
		sortStatus.update = true;
		// $.ajax({
		// 	url: baseURL+'item/sort',
		// 	type: 'post',
		// 	data: 'list_id='+list+'&id='+ui.item.attr('data-id')+'&position='+ui.item.index()
		// });
	}
});

$(document).on('click', '.playlist .list-item', function(e){
	player.loadVideoById(playlist.videoAt($(this).index()));
	socket.emit('sync', playlist.sync({index: playlist.index()}));
});

$('.trash-can').sortable({
	over: function(event, ui){
		ui.helper.addClass('to-be-deleted');
	},
	out: function(event, ui){
		if(ui.helper!=null)
			ui.helper.removeClass('to-be-deleted');
	},
	drop: function(event, ui){
		ui.draggable.remove();
	}
});

var renderSearchResults = function(res){
	var source = $("#search-template").html();
	var template = Handlebars.compile(source);
	// var asd = $.parseJSON(res);
	$(".search-results").html(template(res));
	if(!$(".search-results").is(':visible'))
		$(".search-results").show();

	$('.search-results').sortable({
		connectWith: '.playlist',
		handle: '.list-item-thumb',
		items: '.list-item',
		start: function(event, ui){
			$(".search-results").css('top', '-500px');
		},
		stop: function(event, ui){
			$(".search-results").removeAttr('style');
		}
	});
}

$(document).on('click', '.add-to-list', function(){
	var el = $(this).parent();
	var clone = el.clone();
	var searchBox = $('.search-results');
	var list = $('.playlist');
	var lastItem = $('.playlist .list-item:last');
	var placeholder = $('<div>');
	
	placeholder.css('height', el.outerHeight(true));
	lastItem.after(placeholder);
	
	list.scrollTo(placeholder, 200, function(){
		var offset = list.scrollTop();
	});

	clone.css({
		opacity: 0.6,
		position: 'absolute',
		top: el.offset().top,
		left: el.offset().left,
		width: el.width()
	});
	$('body').append(clone);
	// el.remove();
	clone.find('.add-to-list').remove();
	clone.animate({
		top: list.offset().top+list.height()-clone.outerHeight(true),
		left: placeholder.offset().left
	}, {
		duration: 400,
		complete: function(){
			clone.appendTo(list);
			clone.removeAttr('style');
			placeholder.remove();
		}
	});
});

$('#list-title').click(function(){
	var el = $(this);
	el.hide();
	$('#edit-list-title').show();
	$('#list-title-text').val(el.text());
	$('#list-title-text').focus();
});

$('#save-list-title').click(function(){
	saveTitle();
});

$('#list-title-text').keypress(function(e){
	if(e.keyCode!=13)
		return;

	saveTitle();
});

$('#cancel-list-title').click(function(){
	$('#edit-list-title').hide();
	$('#list-title').show();
});

function saveTitle(){
	var title = $('#list-title-text').val();
	$('#list-title').text(title);
	$('#edit-list-title').hide();
	$('#list-title').show();
	$.ajax({
		url: baseURL+'playlist/save_title',
		type: 'post',
		data: 'name='+title+'&id='+list
	});
}