<?php

require_once(__DIR__ . '/core/init.php');

$user = new User();
if ($user->isLoggedIn()) {
	$user->logout();
}

Redirect::to('page.php');
