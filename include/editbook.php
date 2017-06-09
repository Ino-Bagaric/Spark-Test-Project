<?php

$error = array();
$success = false;

if ($data = $book->getData()) {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		$title = trim(filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING));
		$author = trim(filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING));
		$description = trim(filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING));
		$stock = $_POST['available'];

		if (strlen($title) < 3) $error[] = 'Title can\'t be less than 3 characters';
		if (empty($author)) $error[] = 'Please fill author input';
		if (empty($description)) $error[] = 'Please fill description input';
		if ($stock < 0) $error[] = 'Available input must be below 0';

		if (!Alert::hasError($error)) {
			if ($book->update($title, $author, $description, $stock)) {
				$success = true;

				// Get new fresh data
				$data = $book->getData();
			} else {
				$error[] = 'Couldn\'t update book, please try again';
			}
		}
	}

	if (Alert::hasError($error)) {
		Alert::error($error);
	}

	if ($success) {
		Alert::success("You have successfully updated book");
	}
	?>

	<form method="post" action="">

		<h1>Edit book</h1>
		<label for="title">Title:</label>
		<input type="text" name="title" id="title" value="<?php echo $data['title']; ?>">

		<label for="author">Author:</label>
		<input type="text" name="author" id="author" value="<?php echo $data['author']; ?>">

		<label for="description">Description:</label>
		<textarea name="description" id="description"><?php echo htmlspecialchars($data['description']); ?></textarea>

		<label for="available">Available in stock:</label>
		<input type="number" name="available" id="available" value="<?php echo $data['stock']; ?>">

		<button type="submit">Save Book</button>

	</form>

	<?php
} else { 
	Redirect::to('book.php?id=' . $bookId . '&option=preview');
}