<?php

class User
{
	private $db;
	private $isLoggedIn;
	private $userId;
	private static $session = 'user';	

	public function __construct($user = null)
	{
		$this->db = DB::getConnection();

		if ($user !== null) {
			$this->userId = $user;
		} else {
			if (Session::exist(self::$session)) {
				$this->userId = Session::get(self::$session);
				$this->isLoggedIn = true;
			} else {
				$this->isLoggedIn = false;
				$this->userId = -1;
			}
		}
	}

	public function register($username, $name, $lastname, $email, $password)
	{
		try {
			$stmt = $this->db->prepare("INSERT INTO Users (username, name, lastname, email, password, permission) VALUES (?, ?, ?, ?, ?, ?)");
			$stmt->bindValue(1, $username, PDO::PARAM_STR);
			$stmt->bindValue(2, $name, PDO::PARAM_STR);
			$stmt->bindValue(3, $lastname, PDO::PARAM_STR);
			$stmt->bindValue(4, $email, PDO::PARAM_STR);
			$stmt->bindValue(5, $password, PDO::PARAM_STR);
			$stmt->bindValue(6, 1, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}

	public function login($username, $password)
	{
		try {
			$field = "username";

			if (strpos($username, "@")) {
				$field = "email";
			}

			$results = $this->db->prepare("SELECT id, username, password FROM Users WHERE {$field} = ? LIMIT 1");
			$results->bindParam(1, $username, PDO::PARAM_STR);
			$results->execute();

			$data = $results->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			return false;
		}

		if ($data) {
			if ($data['password'] == $password) {
				$this->userId = $data['id'];
				Session::put(self::$session, $this->userId);
				return true;
			}
		}

		return false;
	}

	public function logout()
	{
		if ($this->isLoggedIn() && Session::exist(self::$session)) {
			Session::remove(self::$session);
			$this->isLoggedIn = false;
		}
	}

	public function hasPermission($permission)
	{
		$p = $this->getData()['permission'];

		switch ($permission) {
			case PERMISSION_USER: 
				if ($p == PERMISSION_USER || $p == PERMISSION_MODERATOR || $p == PERMISSION_ADMINISTRATOR) {
					return true;
				}
				break;
			case PERMISSION_MODERATOR:
				if ($p == PERMISSION_MODERATOR || $p == PERMISSION_ADMINISTRATOR) {
					return true;
				}
				break;
			case PERMISSION_ADMINISTRATOR:
				if ($p == PERMISSION_ADMINISTRATOR) {
					return true;
				}
				break;
		}

		return false;
	}

	public function getPermissionName($permission)
	{
		try {
			$results = $this->db->prepare("SELECT permission_name FROM Permissions WHERE permission_id = ?");
			$results->bindParam(1, $permission, PDO::PARAM_INT);
			$results->execute();
		} catch (Exception $e) {
			return null;
		}

		return $results->fetchColumn(0);
	}

	public function exist($username, $email, $skipUser = null)
	{
		$skipField = ($skipUser !== null) ? ' AND NOT id = ?' : '';

		try {
			$results = $this->db->prepare("SELECT COUNT(*) FROM Users WHERE (username = ? OR email = ?){$skipField}");
			$results->bindParam(1, $username, PDO::PARAM_STR);
			$results->bindParam(2, $email, PDO::PARAM_STR);

			if ($skipUser !== null) {
				$results->bindParam(3, $skipUser, PDO::PARAM_INT);
			}

			$results->execute();

			$count = $results->fetchColumn(0);
		} catch (Exception $e) {
			return true;
		}

		return ($count > 0) ? true : false;
	}

	public function getData()
	{
		try {
			$results = $this->db->prepare("SELECT * FROM Users WHERE id = ?");
			$results->bindParam(1, $this->userId, PDO::PARAM_INT);
			$results->execute();

			$data = $results->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			return null;
		}

		return $data;
	}

	public function getAllUsers()
	{
		$data = array();

		try {
			$results = $this->db->prepare("SELECT id FROM Users");
			$results->execute();

			$data = $results->fetchAll(PDO::FETCH_COLUMN);
		} catch (Exception $e) {
			$data = [];
		}

		return $data;
	}

	public function update($userid, $username, $name, $lastname, $email, $permission, $password = null)
	{
		$passwordField = ($password !== null) ? ', `password` = ?' : '';

		$query = "UPDATE Users SET `username` = ?, `name` = ?, `lastname` = ?, `email` = ?, `permission` = ?{$passwordField} WHERE `id` = ?";

		try {
			$stmt = $this->db->prepare($query);
			$stmt->bindParam(1, $username, PDO::PARAM_STR);
			$stmt->bindParam(2, $name, PDO::PARAM_STR);
			$stmt->bindParam(3, $lastname, PDO::PARAM_STR);
			$stmt->bindParam(4, $email, PDO::PARAM_STR);
			$stmt->bindParam(5, $permission, PDO::PARAM_INT);

			if ($password !== null) {
				$stmt->bindParam(6, $password, PDO::PARAM_STR);
				$stmt->bindParam(7, $userid, PDO::PARAM_INT);
			} else {
				$stmt->bindParam(6, $userid, PDO::PARAM_INT);
			}

			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	public function remove($userid = null)
	{
		if ($userid === null) {
			if ($this->isLoggedIn()) {
				$userid = $this->userId;
			} else {
				return false;
			}
		}

		try {
			$stmt = $this->db->prepare("DELETE FROM Users WHERE id = ?");
			$stmt->bindParam(1, $userid, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function isLoggedIn()
	{
		return $this->isLoggedIn;
	}
}

