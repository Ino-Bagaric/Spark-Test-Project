<?php

$error = array();
$success = false;

if ($data = $tmpUser->getData()) {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		$username = trim(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));
		$name = trim(filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING));
		$lastname = trim(filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_STRING));
		$email = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING));
		$password = trim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING));
		$rpassword = trim(filter_input(INPUT_POST, "rpassword", FILTER_SANITIZE_STRING));
		$permission = !isset($_POST['permission']) ? $data['permission'] : $_POST['permission'];

		if (strlen($username) < 3) $error[] = 'Username can\'t be less than 3 characters';
		if (strlen($name) < 3) $error[] = 'Name can\'t be less than 3 characters';
		if (strlen($lastname) < 3) $error[] = 'Last Name can\'t be less than 3 characters';
		if ($tmpUser->exist($username, $email, $id)) $error[] = 'This username or an email is already in use'; 

		if (!empty($password)) {
			if ($password !== $rpassword) {
				$error[] = 'Passwords are not matching';
			}
		}

		if (!Alert::hasError($error)) {
			$hash = !empty($password) ? hash('sha256', $password) : null;
			if ($tmpUser->update($id, $username, $name, $lastname, $email, $permission, $hash)) {
				$success = true;

				// Get new fresh data
				$data = $tmpUser->getData();
			} else {
				$error[] = 'Couldn\'t update profile, please try again';
			}
		}
	}				

	if (Alert::hasError($error)) {
		Alert::error($error);
	}

	if ($success) {
		Alert::success("You have successfully updated profile");
	}
	?>

	<form method="post" action="">

		<h1>Edit profile</h1>
		<label for="username">Username:</label>
		<input type="text" name="username" id="username" value="<?php echo $data['username']; ?>">

		<label for="name">Name:</label>
		<input type="text" name="name" id="name" value="<?php echo $data['name']; ?>">

		<label for="lastname">Last Name:</label>
		<input type="text" name="lastname" id="lastname" value="<?php echo $data['lastname']; ?>">

		<label for="email">Email:</label>
		<input type="email" name="email" id="email" value="<?php echo $data['email']; ?>">

		<fieldset>
		<label for="password">New Password:<br><i>(Leave empty if you don't want change)</i></label>
		<input type="password" name="password" id="password">


		<label for="rpassword">Repeat New Password:<br><i></i></label>
		<input type="password" name="rpassword" id="rpassword">
		</fieldset>

		<?php if ($user->hasPermission(PERMISSION_ADMINISTRATOR)) { ?>
		<label for="permission">User Permission:</label>
		<select id="permission" name="permission">
			<option value="1" <?php echo ($data['permission'] == 1) ? 'selected' : ''; ?>>User</option>
			<option value="2" <?php echo ($data['permission'] == 2) ? 'selected' : ''; ?>>Moderator</option>
			<option value="3" <?php echo ($data['permission'] == 3) ? 'selected' : ''; ?>>Administrator</option>
		</select>
		<?php } ?>

		<button type="submit">Save Profile</button>

	</form>

	<?php
} else { 
	Redirect::to('profile.php?id=' . $id);
}