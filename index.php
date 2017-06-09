<?php

require_once(__DIR__ . '/core/init.php');

$db = DB::getConnection();
$user = new User();

if ($user->isLoggedIn()) {
	Redirect::to('page.php');
}

?>

<body>

<div class="buttons-box">
	<?php Alert::warning("You are not logged in, please login to access the page"); ?>

	<p class="button" style="margin-top: 25px"><a href="login.php">Login</a></p>
	<p class="button"><a href="register.php">Register</a></p>
</div>

</body>

