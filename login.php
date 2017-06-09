<?php

$title = 'Login';
require_once(__DIR__ . '/core/init.php');

$user = new User();
if ($user->isLoggedIn()) {
	Redirect::to('page.php');
}

$error = array();
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = trim(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));
	$password = trim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING));

	if (empty($username)) $error[] = 'Field `username` is empty';
	if (empty($password)) $error[] = 'Field `password` is empty';

	if (!Alert::hasError($error)) {
		$hash = hash('sha256', $password);
		if ($user->login($username, $hash)) {
			$success = true;
		} else {
			$error[] = 'Wrong username or password';
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
		Alert::success("You have successfully logged in<br>Please wait...");
		Redirect::to('page.php', 2);
	} else { ?>

	<form method="post" action="">

		<h1>Sign In</h1>
		<label for="username">Username / Email:</label>
		<input type="text" name="username" id="username" value="<?php echo fieldValue('username'); ?>">

		<label for="password">Password:</label>
		<input type="password" name="password" id="password" value="<?php echo fieldValue('password'); ?>">

		<button type="submit">Sign In</button>

	</form>

	<?php } ?>
</div>

</body>


