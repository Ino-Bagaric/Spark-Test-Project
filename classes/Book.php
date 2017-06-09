<?php

class Book
{
	private $db;
	private $bookId;

	public function __construct($bookId)
	{
		$this->bookId = $bookId;
		$this->db = DB::getConnection();
	}

	public function add($title, $author, $description, $stock)
	{
		try {
			$stmt = $this->db->prepare("INSERT INTO Books (title, author, description, stock) VALUES (?, ?, ?, ?)");
			$stmt->bindValue(1, $title, PDO::PARAM_STR);
			$stmt->bindValue(2, $author, PDO::PARAM_STR);
			$stmt->bindValue(3, $description, PDO::PARAM_STR);
			$stmt->bindValue(4, $stock, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}

	public function exist()
	{
		try {
			$results = $this->db->prepare("SELECT COUNT(*) FROM Books WHERE id = ?");
			$results->bindParam(1, $this->bookId, PDO::PARAM_STR);
			$results->execute();

			$count = $results->fetchColumn(0);
		} catch (Exception $e) {
			return true;
		}

		return ($count > 0) ? true : false;
	}

	public function getData()
	{
		$data = array();

		try {
			$results = $this->db->prepare("SELECT * FROM Books WHERE id = ?");
			$results->bindParam(1, $this->bookId, PDO::PARAM_INT);
			$results->execute();

			$data = $results->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			return null;
		}

		return $data;
	}

	public function getAllBooks()
	{
		$data = array();

		try {
			$results = $this->db->prepare("SELECT id FROM Books");
			$results->execute();

			$data = $results->fetchAll(PDO::FETCH_COLUMN);
		} catch (Exception $e) {
			$data = [];
		}

		return $data;
	}

	public function update($title, $author, $description, $stock)
	{
		$query = "UPDATE Books SET `title` = ?, `author` = ?, `description` = ?, `stock` = ? WHERE `id` = ?";

		try {
			$stmt = $this->db->prepare($query);
			$stmt->bindParam(1, $title, PDO::PARAM_STR);
			$stmt->bindParam(2, $author, PDO::PARAM_STR);
			$stmt->bindParam(3, $description, PDO::PARAM_STR);
			$stmt->bindParam(4, $stock, PDO::PARAM_STR);
			$stmt->bindParam(5, $this->bookId, PDO::PARAM_INT);

			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	public function remove()
	{
		try {
			$stmt = $this->db->prepare("DELETE FROM Books WHERE id = ?");
			$stmt->bindParam(1, $this->bookId, PDO::PARAM_INT);
			$stmt->execute();

			$stmt = $this->db->prepare("DELETE FROM BooksOwners WHERE book_id = ?");
			$stmt->bindParam(1, $this->bookId, PDO::PARAM_INT);
			$stmt->execute();

			$stmt = $this->db->prepare("DELETE FROM BooksComments WHERE book_id = ?");
			$stmt->bindParam(1, $this->bookId, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	public function getBooksByUser(User $user)
	{
		$userId = $user->getUserId();
		$data = array();

		try {
			$results = $this->db->prepare("SELECT book_id FROM BooksOwners WHERE user_id = ?");
			$results->bindParam(1, $userId, PDO::PARAM_INT);
			$results->execute();

			$data = $results->fetchAll(PDO::FETCH_COLUMN);
		} catch (Exception $e) {
			$data = [];
		}

		return $data;
	}

	public function rent(User $user)
	{
		$userId = $user->getUserId();
		$data = $this->getData();

		if ($data['stock'] <= 0) return false;
		if ($this->hasRented($user)) return false;

		try {
			$stmt = $this->db->prepare("INSERT INTO BooksOwners (book_id, user_id) VALUES (?, ?)");
			$stmt->bindValue(1, $this->bookId, PDO::PARAM_INT);
			$stmt->bindValue(2, $userId, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}

		$title = $data['title'];
		$author = $data['author'];
		$description = $data['description'];
		$stock = $data['stock'] - 1;

		$this->update($title, $author, $description, $stock);
		return true;
	}

	public function unRent(User $user)
	{
		$userId = $user->getUserId();

		if (!$this->hasRented($user)) return false;

		try {
			$stmt = $this->db->prepare("DELETE FROM BooksOwners WHERE user_id = ?");
			$stmt->bindParam(1, $userId, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}

		$data = $this->getData();

		$title = $data['title'];
		$author = $data['author'];
		$description = $data['description'];
		$stock = $data['stock'] + 1;

		$this->update($title, $author, $description, $stock);
		return true;
	}

	public function hasRented(User $user)
	{
		$books = $this->getBooksByUser($user);

		foreach ($books as $book) {
			if ($book == $this->bookId) {
				return true;
			}
		} 

		return false;
	}

	public function getBookComments()
	{
		$data = array();

		try {
			$results = $this->db->prepare("SELECT * FROM BooksComments WHERE book_id = ?");
			$results->bindParam(1, $this->bookId, PDO::PARAM_INT);
			$results->execute();

			$data = $results->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			$data = null;
		}

		return $data;
	}

	function getCommentData($comment)
	{
		$data = array();

		try {
			$results = $this->db->prepare("SELECT * FROM BooksComments WHERE id = ?");
			$results->bindParam(1, $comment, PDO::PARAM_INT);
			$results->execute();

			$data = $results->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			$data = null;
		}

		return $data;
	}

	public function countBookComments()
	{
		$count = 0;

		try {
			$results = $this->db->prepare("SELECT COUNT(*) FROM BooksComments WHERE book_id = ? GROUP BY book_id");
			$results->bindParam(1, $this->bookId, PDO::PARAM_INT);
			$results->execute();

			$count = $results->fetchColumn(0);
		} catch (Exception $e) {
			return 0;
		}

		return $count;
	}

	public function addBookComment(User $user, $comment)
	{
		$userId = $user->getUserId();

		try {
			$time = date('d/m/Y - H:i:s');

			$stmt = $this->db->prepare("INSERT INTO BooksComments (book_id, user_id, comment, time) VALUES (?, ?, ?, ?)");
			$stmt->bindValue(1, $this->bookId, PDO::PARAM_STR);
			$stmt->bindValue(2, $userId, PDO::PARAM_INT);
			$stmt->bindValue(3, $comment, PDO::PARAM_STR);
			$stmt->bindValue(4, $time, PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}

	public function removeBookComment($comment, User $user)
	{
		$data = $this->getCommentData($comment);

		if ($data == null) return false;

		if ($user->getUserId() != $data['user_id']) {
			if (!$user->hasPermission(PERMISSION_MODERATOR)) {
				return false;
			}
		}

		try {
			$stmt = $this->db->prepare("DELETE FROM BooksComments WHERE id = ?");
			$stmt->bindParam(1, $comment, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}

		return true;
	}
}

