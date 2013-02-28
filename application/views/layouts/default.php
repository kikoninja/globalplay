<!DOCTYPE html>
<html>
<head>
	<title>Shared YouTube</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo base_url('css/bootstrap.min.css') ?>" type="text/css">
	<link rel="stylesheet" href="<?php echo base_url('css/style.css') ?>" type="text/css">
</head>
<body>
	<div class="container">
		<header>
			<?php if($user = get_session('user')): ?>
			<div class="row-fluid">
				<img src="<?php echo $user['thumb'] ?>" class="thumbnail pull-left span1" />
				<div class="pull-left text-righ span6">
					<span><?php echo $user['name'] ?></span><br />
					<a href="<?php echo base_url() ?>auth/logout" class="btn btn-primary">logout</a>
				</div>
				<input type="hidden" id="user-name" value="<?php echo $user['name'] ?>">
				<input type="hidden" id="user-thumb" value="<?php echo $user['thumb'] ?>">
			</div>
			<div class="clear"></div>
			<?php else: ?>
			<a href="<?php echo base_url() ?>auth/google" class="btn btn-primary btn-large" id="login">Login with Google</a>
			<?php endif; ?>
		</header>

		<?php echo $main ?>
	</div>

	<script type="text/javascript" src="//code.jquery.com/jquery.js"></script>
	<script type="text/javascript" src="//code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url('js/socket.io/socket.io.min.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('js/bootstrap.min.js') ?>"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
	<script type="text/javascript" src="<?php echo base_url('js/app.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('js/player.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('js/list.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('js/chat.js') ?>"></script>
</body>
</html>