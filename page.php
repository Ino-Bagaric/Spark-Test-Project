<?php

$title = 'Books';
require_once(__DIR__ . '/core/init.php');

$user = new User();
$data = $user->getData();

if (!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

?>

<body>
	<div class="wrapper">
		<p>Hello <?php echo $data['name']; ?></p>


		<div class="books clearfix">

			<?php 
			$bookManager = new Book(-1);
			$books = $bookManager->getAllBooks();

			foreach ($books as $book) {
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


			if ($user->hasPermission(PERMISSION_MODERATOR)) {
			?>
				<a href="book.php?option=add" class="book" style="width: 100%">
					<div class="book-title" style="text-align: center; margin: 0; line-height: 90px; color: #72798e">+ ( Add new book ) +</div>
				</a>
			<?php } ?>
		</div>

	</div>
</body>


