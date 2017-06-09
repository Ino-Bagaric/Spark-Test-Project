<?php

function setActive($page = null) {
	if ($page === null) {
		$page = 'index';
	}

	$currentPage = basename($_SERVER['PHP_SELF'], ".php");
	if ($page === $currentPage) {
		return ' class="active" ';
	}
	return '';
}

$user = new User();

?>

<body>

<div class="navigation">
	<ul>
		<?php if (!$user->isLoggedIn()) { ?>
		<li<?php echo setActive('index'); ?>><a href="index.php">Index</a></li>
		<li<?php echo setActive('register'); ?> style="float: right"><a href="register.php">Register</a></li>
		<li<?php echo setActive('login'); ?> style="float: right"><a href="login.php">Login</a></li>
		<?php } else { ?>
		<li<?php echo setActive('page'); ?>><a href="page.php">Index</a></li>
		<li<?php echo setActive('members'); ?>><a href="members.php">Members</a></li>
		<li<?php echo setActive('book'); ?>><a href="book.php">Books</a></li>
		<li<?php echo setActive(); ?> style="float: right"><a href="logout.php">Logout</a></li>
		<li<?php echo setActive('profile'); ?> style="float: right"><a href="profile.php">Profile</a></li>
		<?php } ?>
	</ul>	
</div>

</body>

