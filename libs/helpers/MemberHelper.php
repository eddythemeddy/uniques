<?php

require_once('libs/helpers/PaginationHelper.php');

class MemberHelper {

	const MEMBERTABLE = 'users';
	const NOTIFICATION_TABLE = 'notifications';
	const TRIALTABLE = 'trials';

	protected $memberrecords = 'id, firstname, lastname, email, username, gender, city, city_full, country, bio, nationality, attributes, type, dob, lat, lng, title, weight, height, main_position, other_position, preferred_foot';
	protected $trialrecords = 'id, name, posted_by, location';

	public function __construct() {

		global $eqDb;

		$this->eqDb = $eqDb;
	}

	public function getConnections($userId, $search = false) {
	
		$query = 'SELECT 
				IF(u.id = "' . $userId . '", u2.id, u.id ) AS data,
				IF(u.id = "' . $userId . '", 
					CONCAT(u2.firstname," ",u2.lastname), 
					CONCAT(u.firstname," ",u.lastname)
				) AS value
			FROM 
				connections c
				LEFT JOIN (
					SELECT username, firstname, lastname, id FROM users
				) u on u.id = c.user_id
				LEFT JOIN (
					SELECT username, firstname, lastname, id FROM users
				) u2 on u2.id = c.request_to ';
		if($search) {
			$query .= 'WHERE ';
			$query .= '(IF(u.id = "' . $userId . '", u2.firstname, u.firstname ) LIKE "' . $search . '%" OR 
			IF(u.id = "' . $userId . '", u2.lastname, u.lastname ) LIKE "' . $search . '%") ';
		} else {
			$query .= 'LIMIT 8';
		}

		$res = $this->eqDb->rawQuery($query);

		return ['suggestions' => $res];
	}

	/*
		checks if logged in person is connected to
		$id
	*/
	public function checkInviteAccepted($id) {

		$fromUser = $_SESSION['scouty_user_id'];
		$query = 'SELECT * FROM 
					connections 
				  WHERE
					(
						(user_id = "' . $fromUser . '" AND request_to = "' . $id . '") 
							OR
						(user_id = "' . $id . '" AND request_to = "' . $fromUser . '")
					)
				  AND accepted = "1" 
				  AND declined = "0"';
		
		$a = $this->eqDb->rawQuery($query);

		return $a;
	}

	/*
		checks if logged in person has invited
		$id
	*/
	public function checkInvited($id) {

		$fromUser = $_SESSION['scouty_user_id'];

		$this->eqDb->where('user_id', $fromUser);
		$this->eqDb->where('request_to', $id);
		$this->eqDb->where('accepted', 0);
		$this->eqDb->where('declined', 0);
		$a = $this->eqDb->get('connections');

		return $a;
	}

	/*
		checks if logged in person is invited by
		$id
	*/
	public function checkInvitedToMe($id) {

		$fromUser = $_SESSION['scouty_user_id'];

		$this->eqDb->where('user_id', $id);
		$this->eqDb->where('request_to', $fromUser);
		$this->eqDb->where('accepted', 0);
		$this->eqDb->where('declined', 0);
		$a = $this->eqDb->get('connections');

		return $a;
	}

	public function connectButton($id) {

		$invited = $this->checkInvited($id);
		$invitedToMe = $this->checkInvitedToMe($id);
		$accepted = $this->checkInviteAccepted($id);

		$invitedButton = 'Connect';
		$invitedButtonClass = 'connect-button btn btn-primary btn-xs padding-5 p-r-10 p-l-10 font-montserrat text-uppercase bold';
		$invitedData = 'not-connected';
		$invitedIcon = 'flaticon flaticon-share-symbol';

		if($invited) {
		    $invitedButton = 'Cancel Request';
		    $invitedButtonClass = 'connect-button btn btn-info disabled btn-xs padding-5 p-r-10 p-l-10 font-montserrat text-uppercase bold';
		    $invitedData = 'request-sent';
		    $invitedIcon = 'flaticon flaticon-multiply fs-10 bold p-r-0 m-t-5 m-l-5';
		}

		if($invitedToMe) {
		    $invitedButton = 'Accept Request';
		    $invitedButtonClass = 'connect-button btn btn-info disabled btn-xs padding-5 p-r-10 p-l-10 font-montserrat text-uppercase text-center';
		    $invitedData = 'accept-request bold';
		    $invitedIcon = '';
		}

		if($accepted) {
		    $invitedButton = 'Accepted';
		    $invitedButtonClass = 'connect-button btn btn-info disabled btn-xs padding-5 p-r-10 p-l-10 font-montserrat text-uppercase';
		    $invitedData = 'accept-request';
		    $invitedIcon = '';
		}

		$html = '';

        if(isset($_SESSION['scouty_user_id']) && $_SESSION['scouty_user_id'] == $id):
        	return false;
        endif;

          if($invited):
            $html = '<button 
            			class="' . $invitedButtonClass . '" 
            			data-status="' . $invitedData . '">
		            <span>' . $invitedButton . '</span>
		            <i class="' . $invitedIcon . ' p-r-0 m-t-5"></i>
		          </button>';

          elseif($invitedToMe):
            $html = '<button 
            			class="' . $invitedButtonClass . '" 
            			data-status="' . $invitedData . '">
		            <span>' . $invitedButton . '</span>
		            <i class="' . $invitedIcon . ' p-r-0 m-t-5"></i>
		          </button>
		          <button class="connect-button btn btn-danger btn-xs padding-5 p-r-10 p-l-10 font-montserrat text-uppercase fs-10" data-status="decline-request">
		            <span>Decline</span>
		            <i class="' . $invitedIcon . ' p-r-0 m-t-5"></i>
		          </button>';

          elseif($accepted):
            $html = '<div class="btn-group dropdown-default"> 
      				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> Connected <i class="fa fa-caret"></i> 
      				</a>
		            <ul class="dropdown-menu ">
		              <li>
		              	<a class="unfollow" data-unfollow="' . $id . '">Unfollow</a>
		              </li>
		            </ul>
			       </div>';
          else:
            $html = '<button 
            			class="' . $invitedButtonClass . '" 
            			data-status="' . $invitedData . '">
		            <span>' . $invitedButton . '</span>
		            <i class="' . $invitedIcon . ' p-r-0 m-t-5"></i>
		          </button>';
          endif;

        return $html;
	}

	public function getAllNotifications() {

		if(isset($_SESSION['scouty_user_id'])) {
			$this->eqDb->where ('user_id = "' . $_SESSION['scouty_user_id'] . '"');
			$this->eqDb->where ('isread = "0"');
			$member = $this->eqDb->get (self::NOTIFICATION_TABLE, null, '*');

			if(count($member)){
				return $member;
			}
		}
		
		return false;
	}

	public function readAllNotifications() {

		$data = [
			'isread' => 1
		];

		$this->eqDb->where ('user_id', $_SESSION['scouty_user_id']);
		if ($this->eqDb->update (self::NOTIFICATION_TABLE, $data))
			return true;
		else 
		echo "Last executed query was ". $this->eqDb->getLastQuery();
			echo 'update failed: ' . $this->eqDb->getLastError();
	}

	public function prettyDate($date) {

		if(empty($date)) {
			return '';
		}

		$date = explode('-', $date);
		$date = $date[2] . '/' . $date[1] . '/' . $date[0];

		return $date;
	}
	
	public function getAllMembersSearch($ref = array()) {

		$table = $ref['type'] == 'people' ? self::MEMBERTABLE : self::TRIALTABLE;

		if(!empty($ref['name'])) {
			$this->eqDb->where ('(firstname LIKE "'.$ref['name'].'%" OR lastname LIKE "'.$ref['name'].'%")');
		}
		if(!empty($ref['playerType'])) {
			$this->eqDb->where ('type = "' . $ref['playerType'] . '"');
		}
		if(!empty($ref['location'])) {
			$this->eqDb->where ('country = "' . $ref['location'] . '"');
		}

        if (!isset($_GET['page'])){
            $page = 1;
        } else {
            $page = intval($_GET['page']);
            if($page < 1) $page = 1;
        }

		$this->eqDb->pageLimit = 3;
		$users = $this->eqDb->arraybuilder()->paginate("users", $page, $this->memberrecords);

		$response = [
			'totalPages' => $this->eqDb->totalPages,
			'totalResults' => $this->eqDb->totalCount,
			'result' => $users
		];

		return $response;
	}

	public function getAllMemberRecords() {
		return explode(',',$this->memberrecords);
	}

	public function saveMember($username, $data) {
		
		$this->eqDb->where ('username', $username);
		if ($this->eqDb->update (self::MEMBERTABLE, $data))
			return true;
		else 
		echo "Last executed query was ". $this->eqDb->getLastQuery();
			echo 'update failed: ' . $this->eqDb->getLastError();

	}

	public function getAge($dob = '') {

		if($dob == '') {
			return '';
		}
		
		$birthDate = $dob;
		//explode the date to get month, day and year
		$birthDate = explode("-", $birthDate);
		//get age from date or birthdate
		$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md")
			? ((date("Y") - $birthDate[0]) - 1)
			: (date("Y") - $birthDate[0]));

		return $age;

	}

	public function validateIfEmailIsTaken($email, $print = false) {

		$email = $this->eqDb->escape($email);

		$members = $this->eqDb->where('email', $email)
						->get(self::MEMBERTABLE, null, 'email');

		if(count($members)) {
			if($print) {
				echo 'false';
			} else {
				false;
			}
		} else {
			if($print){
				echo 'true';
			} else {
				true;
			}
		}
	}

	public function validateIfUsernameIsTaken($username, $print = false) {

		$username = $this->eqDb->escape($username);

		$members = $this->eqDb->where('username', $username)
						->get (self::MEMBERTABLE, null, 'username');

		if(count($members)) {
			if($print){
				echo "false";
			} else {
				false;
			}
		} else {
			if($print){
				echo "true";
			} else {
				true;
			}
		}
	}

	public function getMemberDetailsByReference($value, $reference = 'id') {

		$value = $this->eqDb->escape($value);
		$member = $this->eqDb->where ($reference, $value)
						->get (self::MEMBERTABLE, null, $this->memberrecords);

		return $member;
	}

	public function getMemberByUsername($a) {

		$a = $this->eqDb->escape($a);
		$this->eqDb->where ('username', $a);
		$results = $this->eqDb->get (self::MEMBERTABLE, null, $this->memberrecords);

		if($this->eqDb->count == 0){
			return [];
		}

		return $results;
	}
}