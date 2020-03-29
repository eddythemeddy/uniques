<?php

class Login_Model extends Model {

	const MEMBERTABLE = 'users';
	const MEMBERRECORDS = 'id,username,email,firstname,lastname,password,company_id,menu_pin,active';
	
	public function __construct() {
		global $eqDb;
		$this->eqDb = $eqDb;
		parent::__construct();
	}

	public function process() {

		$username = $this->eqDb->escape($_POST['username']);
		$password = $this->eqDb->escape($_POST['password']);
		$password = md5($password);
		$this->eqDb->where ("(username = ? or email = ?)", [$username, $username]);
		$this->eqDb->where ('password', $password);
		$members = $this->eqDb->get ( 
			self::MEMBERTABLE,
			null,
			self::MEMBERRECORDS);

		if(count($members) == 1) {
			
			$row = $members[0];

			if($row['active'] == 0) {

				return [ 
					"r"	      => "fail", 
					"type"    => "danger", 
					"message" => "<strong>Error: </strong> Sorry cannot access at this moment."
				];
			}

			$this->eqDb->where ('id', $row['company_id']);
			$company = $this->eqDb->getOne ('company', null, '*');

			if(count($company) == 0) {

				return [ 
					"r"	      => "fail", 
					"type"    => "danger", 
					"message" => "<strong>Error: </strong> Sorry internal error (21222)."
				];
			}

			$_SESSION['scouty_user_id']         = $row['id'];
			$_SESSION['scouty_email']           = $row['email'];
			$_SESSION['scouty_username']        = $row['username'];
			$_SESSION['scouty_name']            = $row['firstname'] .' '. $row['lastname'];
			$_SESSION['scouty_firstname']       = $row['firstname'];
			$_SESSION['scouty_lastname']        = $row['lastname'];
			$_SESSION['scouty_company_id']      = $row['company_id'];
			$_SESSION['scouty_menu_status']     = $row['menu_pin'];
			$_SESSION['scouty_company_name']    = $company['name'];
			$_SESSION['scouty_company_add']     = $company['address'];
			$_SESSION['scouty_company_email']   = $company['email'];
			$_SESSION['scouty_company_phone']   = $company['phone_number'];
			$_SESSION['scouty_company_city']    = $company['city'];
			$_SESSION['scouty_company_country'] = $company['country'];
			$_SESSION['scouty_company_tz']      = $company['timezone'];

			return  [ 
				"r" 	   => "success", 
				"redirect" => _SITEROOT_ . "forecast/calendar"
			];
		}
			
		return [ 
			"r"       => "fail", 
			"type"    => "danger", 
			"message" => "<strong>Error: </strong> Wrong Email or Password"
		];
	}
}