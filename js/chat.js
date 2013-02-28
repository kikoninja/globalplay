var chat = {
	defaults: {
		user: $('#user-name').val(),
		thumb: $('#user-thumb').val(),
		room: list
	},
	setOptions: function(opt){
		this.defaults = $.extend(this.defaults, opt);
	},
	send: function(msg){
		this.defaults.msg = msg;
		socket.emit('chat_send', this.defaults);
	}
};

socket.on('chat_receive', function(data){
	addMessage(data);
});

function addMessage(data){
	var template = Handlebars.compile($('#chat-template').html());
	$('.chat').append(template(data));
	$('.chat').scrollTo($('.chat .chat-item:last'));
}

$('#chat-send').click(function(){
	if($('#chat-text').val()!=''){
		chat.send($('#chat-text').val());
		addMessage(chat.defaults);
		$('#chat-text').val('');
	}
});

$('#chat-text').keypress(function(e){
	if(e.keyCode!=13)
		return;
	$('#chat-send').click();
});