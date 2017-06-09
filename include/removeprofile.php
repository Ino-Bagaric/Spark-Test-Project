<?php

if ($data = $tmpUser->getData()) {

	if (isset($_GET['answer'])) {
		$answer = $_GET['answer'];

		if ($answer === 'yes') {
			if ($id === $user->getUserId()) {
				$user->logout();
			}
			$tmpUser->remove($id);
			Redirect::to('profile.php');
		} elseif ($answer === 'no') {
			Redirect::to('profile.php?id=' . $id);
		}
	}
?>

<center>
	<p>Are you sure you want remove profile of <b><?php echo $data['name'] . ' ' . $data['lastname']; ?></b>?</p>

	<p class="button danger" style="width: 250px"><a href="profile.php?id=<?php echo $id; ?>&option=remove&answer=yes">Yes</a></p>
	<p class="button" style="width: 250px"><a href="profile.php?id=<?php echo $id; ?>&option=remove&answer=no">No</a></p>
</center>

<?php

} else { 
	Redirect::to('profile.php?id=' . $id);
}

