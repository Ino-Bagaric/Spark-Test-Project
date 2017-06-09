<?php

$title = 'Books';
require_once(__DIR__ . '/core/init.php');

if (!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

function fieldValue($value) {
	return (isset($_POST[$value])) ? $_POST[$value] : '';
}

?> <body> <div class="wrapper"> <?php

$option = null;
$bookId = -1;

if (empty($_GET)) {

	require_once(__DIR__ . '/include/mybooks.php');

} else {
	if (isset($_GET['id'])) {
		$bookId = $_GET['id'];
	} 

	$book = new Book($bookId);

	if (isset($_GET['option'])) {
		$option = $_GET['option'];
	
		if ($option !== null) {
			$data = $book->getData();

			if ($option === 'add') {

				if (!$user->hasPermission(PERMISSION_MODERATOR)) {
					Redirect::to('page.php');
				}

				require_once(__DIR__ . '/include/addbook.php');

			} elseif ($option === 'remove') {

				if (!$book->exist()) {
					Alert::error("Book does not exist");
					die;
				}

				if (!$user->hasPermission(PERMISSION_MODERATOR)) {
					Redirect::to('book.php?id=' . $bookId . '&option=preview');
				}

				require_once(__DIR__ . '/include/removebook.php');

			} elseif ($option === 'edit') {

				if (!$book->exist()) {
					Alert::error("Book does not exist");
					die;
				}

				if (!$user->hasPermission(PERMISSION_MODERATOR)) {
					Redirect::to('book.php?id=' . $bookId . '&option=preview');
				}

				require_once(__DIR__ . '/include/editbook.php');

			} elseif ($option === 'preview') {

				if (!$book->exist()) {
					Alert::error("Book does not exist");
					die;
				}

				require_once(__DIR__ . '/include/previewbook.php');

			} elseif ($option === 'rent') {

				if (!$book->exist()) {
					Alert::error("Book does not exist");
					die;
				}

				$success = false;
				$error = array();

				if ($book->rent($user)) {
					$success = true;
				} else {
					$error[] = 'You can\'t rent this book right now<br>';
				}

				if (Alert::hasError($error)) {
					Alert::error($error);
				}

				if ($success) {
					Alert::success("You have successfully rented the book");
				}

				// Fresh data
				$data = $book->getData();

				require_once(__DIR__ . '/include/previewbook.php');

			} elseif ($option === 'unrent') {

				if (!$book->exist()) {
					Alert::error("Book does not exist");
					die;
				}

				$success = false;
				$error = array();

				if ($book->unrent($user)) {
					$success = true;
				} else {
					$error[] = 'You can\'t unrent this book';
				}

				if (Alert::hasError($error)) {
					Alert::error($error);
				}

				if ($success) {
					Alert::success("You have successfully unrented the book");
				}

				// Fresh data
				$data = $book->getData();

				require_once(__DIR__ . '/include/previewbook.php');

			} elseif ($option === 'removecomment') {

				if (!$book->exist()) {
					Alert::error("Book does not exist");
					die;
				}

				if (!isset($_GET['comment_id'])) {
					Alert::error("Book does not exist");
					die;
				}

				$comment = $_GET['comment_id'];

				$success = false;
				$error = array();

				if ($book->removeBookComment($comment, $user)) {
					$success = true;
				} else {
					$error[] = 'you can\'t remove this comment';
				}

				if (Alert::hasError($error)) {
					Alert::error($error);
				}

				if ($success) {
					Alert::success("You have successfully removed comment");
				}

				// Fresh data
				$data = $book->getData();

				require_once(__DIR__ . '/include/previewbook.php');

			} else {
					Alert::error("Page does not exist");
			}
		} else {
			Redirect::to('page.php');
		}

	}

}

?> </div> </body> 


