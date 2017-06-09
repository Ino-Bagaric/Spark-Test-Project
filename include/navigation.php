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

// Navigation items
$items = array();

if ($user->isLoggedIn()) {
	$items[] = [   'page',  'left'];
	$items[] = ['members',  'left'];
	$items[] = [   'book',  'left'];
	$items[] = [ 'logout', 'right'];
	$items[] = ['profile', 'right'];
} else {
	$items[] = [   'index',  'left'];
	$items[] = ['register', 'right'];
	$items[] = [   'login', 'right'];
}

function generateNavigation(array $items)
{
	echo '<div class="navigation"> <ul>';

	foreach ($items as $item) {
		echo '<li ' . setActive($item[0]) . ' style="float: ' . $item[1] . '"><a href="' . $item[0] . '.php">' . ucfirst($item[0]) . '</a></li>';
	}

	echo '</div> </ul>';
}

?>

<body>

<?php generateNavigation($items); ?>

</body>

