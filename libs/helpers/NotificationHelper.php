<?php

class NotificationHelper {

	const NOTIFICATION_TABLE = 'notifications';

	public function __construct() {

		global $eqDb;

		$this->eqDb = $eqDb;
	}

	public function getAllNotifications() {

		$usersQ = $this->eqDb->subQuery ("u");
		$usersQ->get ("users", null, 'id, firstname, username');

		$this->eqDb->join($usersQ, "u.id = n.invitation_by", "LEFT");
		$this->eqDb->where('user_id', $_SESSION['scouty_user_id']);
		$products = $this->eqDb->get (self::NOTIFICATION_TABLE . " n", null, "u.firstname, n.type, n.time, u.username, n.isread");

		return $products;
	}

	public function createNotification() {

	}

	public function deleteNotification() {

	}

	public function loadNotificatoinsByUser() {
		
	}
}

?>