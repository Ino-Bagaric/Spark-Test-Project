<?php

$title = 'All Members';
require_once(__DIR__ . '/core/init.php');

if (!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

$u = new User();
$users = $u->getAllUsers();


?> <body> <div class="wrapper"> <div class="members clearfix"> <?php

foreach ($users as $user) {
	$tmpUser = new User($user);
	$data = $tmpUser->getData();

	?>
	<a href="profile.php?id=<?php echo $data['id']; ?>" class="member">
		<div class="member-avatar"><img src="css/img/default-user.png"></div>
		<div class="member-name"><?php echo $data['name'] . ' ' . $data['lastname'] . ($data['id'] === $u->getUserId() ? ' (<i>you</i>)' : ''); ?></div>
		<div class="member-username">@<?php echo $data['username']; ?></div>
		<div class="member-role"><?php echo $u->getPermissionName($data['permission']); ?></div>
	</a>
	<?php
}



?>

</div> </div> </body>

