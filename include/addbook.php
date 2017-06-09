<?php

$error = array();
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$title = trim(filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING));
	$author = trim(filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING));
	$description = trim(filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING));
	$available = $_POST['available'];

	if (empty($title)) $error[] = 'Please fill title field';
	if (empty($author)) $error[] = 'Please fill author field';
	if (empty($description)) $error[] = 'Please fill description field';
	if ($available < 0) $error[] = 'Available number can\'t go below 0';

	if (!Alert::hasError($error)) {
		if ($book->add($title, $author, $description, $available)) {
			$success = true;
		} else {
			$error[] = 'Failed';
		}
	}

	if (Alert::hasError($error)) {
		Alert::error($error);
	}

	if ($success) { 
		Alert::success("You have successfully added new book");
	}
}
				
?>

<form method="post" action="">

	<h1>Add new book</h1>
	<label for="title">Title:</label>
	<input type="text" name="title" id="title" value="<?php echo fieldValue('title'); ?>">

	<label for="author">Author:</label>
	<input type="text" name="author" id="author" value="<?php echo fieldValue('author'); ?>">

	<label for="description">Description:</label>
	<textarea name="description" id="description"><?php echo htmlspecialchars(fieldValue('description')); ?></textarea>

	<label for="available">Available in stock:</label>
	<input type="number" name="available" id="available" value="<?php echo fieldValue('available'); ?>">

	<button type="submit">Add</button>

</form>