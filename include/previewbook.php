<?php
$comments = $book->getBookComments();

?>

<div class="book-preview clearfix">
	<div class="book-preview-top clearfix">
		<div class="book-preview-left">
			<h1><?php echo $data['title']; ?></h1>
			<h3><?php echo 'Author: ' . $data['author']; ?></h3>
			<h3><?php echo $data['stock'] . ' Available'; ?></h3>
		</div>
		<div class="book-preview-right">
			<?php if ($book->hasRented($user)) { ?>
				<p class="button danger" style="width: 250px"><a href="book.php?id=<?php echo $bookId; ?>&option=unrent">UnRent book</a></p>
			<?php } else { ?>
				<p class="button" style="width: 250px"><a href="book.php?id=<?php echo $bookId; ?>&option=rent">Rent book</a></p>
			<?php }	?>		
			
			<?php if ($user->hasPermission(PERMISSION_MODERATOR)) { ?>
				<p class="button" style="width: 250px"><a href="book.php?id=<?php echo $bookId; ?>&option=edit">Edit book</a></p>
				<p class="button danger" style="width: 250px"><a href="book.php?id=<?php echo $bookId; ?>&option=remove">Remove book</a></p>
			<?php } ?>
		</div>
	</div>
	<p><?php echo $data['description']; ?></p>
	<div class="book-preview-comments"><p><b>Comments</b></p>
		<?php 

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$_comment = trim(filter_input(INPUT_POST, "comment", FILTER_SANITIZE_STRING));

			if (!empty($_comment)) {
				$book->addBookComment($user, $_comment);
				$comments = $book->getBookComments();
			}
		}

		foreach ($comments as $comment) { 
			$commentUser = new User($comment['user_id']);
			$commentUserData = $commentUser->getData();
			?>
			<div class="book-preview-comment">
				<?php 
				if ($user->getUserId() == $comment['user_id'] || $user->hasPermission(PERMISSION_MODERATOR)) { 
					$commentId = $comment['id'];
					$link = "book.php?id={$bookId}&option=removecomment&comment_id={$commentId}";
					?>
					<a href="<?php echo $link; ?>" style="float: right; color: blue">[delete]</a>
				<?php } ?>
				<div class="book-preview-comment-member">-- <i><b><?php echo $commentUserData['name'] . ' ' . $commentUserData['lastname']; ?></b></i></div>
				<div class="book-preview-comment-text"><?php echo $comment['comment']; ?></div>
				<div class="book-preview-comment-time"><i><?php echo $comment['time']; ?></i></div>
			</div>
		<?php } ?>

		<div class="book-preview-comment-new">
			<form action="#" method="POST" style="width: 100%">
				<label for="comment">New comment:</label>
				<textarea id="comment" name="comment"></textarea>

				<button type="submit">Send</button>
			</form>
		</div>

	</div>
</div>