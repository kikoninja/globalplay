<input type="hidden" id="video" value="<?php echo @$list['items'][0]['item_id'] ?>">
<input type="hidden" id="list" value="<?php echo $list['id'] ?>">
<img class="trash-can img-rounded" src="<?php echo base_url('img/trash-can.png') ?>" />

<div class="row-fluid">
	<div class="row span9">
		<div id="playerapi">Start searching and add videos to your list</div>
		<div class="text-center">
			<a href="#" id="play" class="btn btn-inverse"><i class="icon-play icon-white"></i></a>
			<a href="#" id="pause" class="btn btn-inverse"><i class="icon-pause icon-white"></i></a>
			<a href="#" id="stop" class="btn btn-inverse"><i class="icon-stop icon-white"></i></a>
			<a href="#" id="prev" class="btn btn-inverse"><i class="icon-step-backward icon-white"></i></a>
			<a href="#" id="next" class="btn btn-inverse"><i class="icon-step-forward icon-white"></i></a>
			<a href="#" id="sync" class="btn btn-inverse"><i class="icon-refresh icon-white"></i></a>
		</div>
	</div>

	<div class="row span3" style="margin-left:0;">

		<div class="navbar-search search-box">
			<input type="text" id="search-query" class="search-query custom-search-query" placeholder="Search" spellcheck="false" autocomplete="off">
			<i class="icon-search custom-icon-search"></i>
		</div>
		<div class="clearfix"></div>
		<div class="search-results well hide"></div>

		<h3 class="text-center" id="list-title"><?php echo $list['name'] ?></h3>
	    <div class="input-append hide" id="edit-list-title">
			<input id="list-title-text" type="text">
			<button class="btn" type="button" id="save-list-title">Save</button>
			<button class="btn" type="button" id="cancel-list-title">Cancel</button>
		</div>
		<div class="playlist well">
			<?php if(!empty($list['items'])): ?>
			<?php foreach($list['items'] as $k=>$item): ?>
			<div class="list-item<?php if($k==0) echo ' active' ?>" data-id="<?php echo $item['id'] ?>" data-item-id="<?php echo $item['item_id'] ?>">
				<img class="img-rounded list-item-thumb pull-left" src="http://i.ytimg.com/vi/<?php echo $item['item_id'] ?>/default.jpg" />
				<p class="list-item-text"><?php echo $item['title'] ?></p>
				<div class="clearfix"></div>
			</div>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="row span9 chat-holder">
	<div class="chat well">
	</div>
	<div class="input-append">
		<input type="text" class="input-xxlarge" id="chat-text">
		<button class="btn" id="chat-send">Send</button>
	</div>
</div>

<script id="test-template" type="text/x-handlebars-template">
	{{test}}
</script>

<script id="chat-template" type="text/x-handlebars-template">
	<div class="chat-item">
		<div class="chat-person span2">
			<img class="media-object pull-left chat-img" src="{{thumb}}" />
			<div class="pull-left">{{user}}:</div>
		</div>
		<div class="chat-text span6">
			{{msg}}
		</div>
		<div class="clearfix"></div>
	</div>
</script>

<script id="search-template" type="text/x-handlebars-template">
	{{#each items}}
	<div class="list-item" data-item-id="{{id.videoId}}">
		<img class="img-rounded list-item-thumb pull-left" src="{{snippet.thumbnails.default.url}}" />
		<a href="https://www.youtube.com/watch?v={{id.videoId}}" target="_blank" class="youtube-link"><img src="<?php echo base_url('img/youtube-logo.png') ?>" /></a>
		<p class="list-item-text">{{snippet.title}}</p>
		{{! <button class="btn btn-inverse img-circle add-to-list"><i class="icon-plus icon-white"></i></button> }}
		<div class="clearfix"></div>
	</div>
	{{/each}}

	<div class="pagination pagination-centered custom-pagination">
		<ul>
			<li{{#unless prevPageToken}} class="active"{{/unless}}><a href="#" id="search-prev" data-token="{{#if prevPageToken}}{{prevPageToken}}{{/if}}">Prev</a></li>
			<li{{#unless nextPageToken}} class="active"{{/unless}}><a href="#" id="search-next" data-token="{{#if nextPageToken}}{{nextPageToken}}{{/if}}">Next</a></li>
		</ul>
	</div>
</script>