<p>My books:</p>
<div class="books clearfix">

	<?php 
	$userBooks = new Book(-1);
	$bookData = $userBooks->getBooksByUser($user);

	foreach ($bookData as $book) {
		$b = new Book($book);

		$data = $b->getData();
		$cComments = $b->countBookComments();

		?>
		<a href="book.php?id=<?php echo $data['id']; ?>&option=preview"  class="book">
			<div class="book-title"><?php echo $data['title']; ?></div>
			<div class="book-stock"><?php echo $data['stock']; ?> Available</div>
			<div class="book-comments"><?php echo empty($cComments) ? '0' : $cComments; ?> Comments</div>
		</a>
		<?php
	}
	?>

</div>
