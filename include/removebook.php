<?php

if ($data = $book->getData()) {

	if (isset($_GET['answer'])) {
		$answer = $_GET['answer'];

		if ($answer === 'yes') {
			$book->remove();
			Redirect::to('page.php');
		} elseif ($answer === 'no') {
			Redirect::to('book.php?id=' . $bookId . '&option=preview');
		}
	}
?>

<center>
	<p>Are you sure you want remove book <b><?php echo $data['title']; ?></b>?</p>
	<p class="button danger" style="width: 250px"><a href="book.php?id=<?php echo $bookId; ?>&option=remove&answer=yes">Yes</a></p>
	<p class="button" style="width: 250px"><a href="book.php?id=<?php echo $bookId; ?>&option=remove&answer=no">No</a></p>
</center>

<?php

} else { 
	Redirect::to('book.php?id=' . $bookId . '&option=preview');
}

