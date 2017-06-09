<?php

session_start();

const PERMISSION_USER = 1;
const PERMISSION_MODERATOR = 2;
const PERMISSION_ADMINISTRATOR = 3;

spl_autoload_register(function($class) {
	require_once('classes/' . $class . '.php');
});


// Page title
if (!isset($title)) {
	$title = 'Library';
}

?>

<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
</html>

<?php
require_once('include/navigation.php');


