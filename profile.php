<?php

$title = 'Profile';
require_once(__DIR__ . '/core/init.php');

$user = new User();

if (!$user->isLoggedIn()) {
	Redirect::to('page.php');
}

$id = $user->getUserId();
$data = null;

$option = isset($_GET['option']) ? $_GET['option'] : null;

if (isset($_GET['id'])) {
	$id = $_GET['id'];
}
$tmpUser = new User($id);

?> <body> <div class="wrapper"><?php

if ($option !== null) {
	if ($option === 'remove') {
		if (!$user->hasPermission(PERMISSION_ADMINISTRATOR)) {
			Redirect::to('profile.php?id=' . $id);
		}

		require_once(__DIR__ . '/include/removeprofile.php');
		
	} elseif ($option === 'edit') {
		if (!$user->hasPermission(PERMISSION_ADMINISTRATOR) && $id != $user->getUserId()) {
			Redirect::to('profile.php?id=' . $id);
		}

		require_once(__DIR__ . '/include/editprofile.php');

	} else {
		Redirect::to('profile.php?id=' . $id);
	}
} else {
	if ($data = $tmpUser->getData()) {

		Alert::Info($data['name'] . ' ' . $data['lastname']);

		?>
		<br><br><br>
		<center>
		<b>User ID: </b><?php echo $data['id']; ?><br>
		<b>Username: </b><?php echo $data['username']; ?><br>
		<b>Name: </b><?php echo $data['name']; ?><br>
		<b>Last Name: </b><?php echo $data['lastname']; ?><br>
		<b>Email: </b><?php echo $data['email']; ?><br>
		<b>Role (Permissions): </b><?php echo $user->getPermissionName($data['permission']); ?><br><br><br>
		<?php 
		if ($user->hasPermission(PERMISSION_ADMINISTRATOR)) { 
		?>
			<p class="button" style="width: 250px"><a href="profile.php?id=<?php echo $id; ?>&option=edit">Edit profile</a></p>
			<p class="button danger" style="width: 250px"><a href="profile.php?id=<?php echo $id; ?>&option=remove">Remove profile</a></p>
		<?php 
		} else { 
			if ($id == $user->getUserId()) { ?>
				<p class="button" style="width: 250px"><a href="profile.php?option=edit">Edit profile</a></p>
			<?php 
			} 
		} 
		?>
		</center>
		<?php
	} else {
		Alert::error("Profile not found");		
	}
}

?> </div> </body>




