<?php

$title = 'Register';
require_once(__DIR__ . '/core/init.php');

$user = new User();
if ($user->isLoggedIn()) {
	Redirect::to('page.php');
}

$error = array();
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = trim(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));
	$name = trim(filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING));
	$lastname = trim(filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_STRING));
	$email = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING));
	$password = trim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING));
	$rpassword = trim(filter_input(INPUT_POST, "rpassword", FILTER_SANITIZE_STRING));

	if (empty($username)) $error[] = 'Field `username` is empty';
	if (empty($name)) $error[] = 'Field `name` is empty';
	if (empty($lastname)) $error[] = 'Field `lastname` is empty';
	if (empty($email)) $error[] = 'Field `email` is empty';
	if (empty($password)) $error[] = 'Field `password` is empty';

	if ($password !== $rpassword) $error[] = 'Passwords are not matching';
	if ($user->exist($username, $email)) $error[] = 'This username or an email is already in use'; 

	if (!Alert::hasError($error)) {
		$hash = hash('sha256', $password);

		$success = true;

		if ($user->register($username, $name, $lastname, $email, $hash)) {
			$success = true;
		} else {
			$error[] = 'Couldn\'t register you, please try again';
		}
	}
}

function fieldValue($value) {
	return (isset($_POST[$value])) ? $_POST[$value] : '';
}

?>

<body>

<div class="wrapper">

	<?php
	if (Alert::hasError($error)) {
		Alert::error($error);
	}
	if ($success) {
		Alert::success("You have successfully registered in<br>Please wait...");
		Redirect::to('login.php', 2);
	} else { ?>

	<form method="post" action="">

		<h1>Sign Up</h1>
		<label for="username">Username:</label>
		<input type="text" name="username" id="username" value="<?php echo fieldValue('username'); ?>">

		<label for="name">Name:</label>
		<input type="text" name="name" id="name" value="<?php echo fieldValue('name'); ?>">

		<label for="lastname">Last Name:</label>
		<input type="text" name="lastname" id="lastname" value="<?php echo fieldValue('lastname'); ?>">

		<label for="email">Email:</label>
		<input type="email" name="email" id="email" value="<?php echo fieldValue('email'); ?>">

		<label for="password">Password:</label>
		<input type="password" name="password" id="password">

		<label for="rpassword">Repeat Password:</label>
		<input type="password" name="rpassword" id="rpassword">

		<button type="submit">Sign Up</button>

	</form>

	<?php } ?>
</div>

</body>


