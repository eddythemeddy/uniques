<?php

class TalentHelper {

	protected $con;

	function __construct() {

		global $eqDb;

		$this->eqDb = $eqDb;
	}

	public function getAllTalent()
	{
		$branches = $this->eqDb->subQuery("b");
		$branches->get("company_branches", null, 'id, name');

		$departments = $this->eqDb->subQuery("d");
		$departments->get("company_departments", null, 'id, name');

		$this->eqDb->join($branches, "b.id = t.branch_id", "LEFT");
		$this->eqDb->join($departments, "d.id = t.department_id", "LEFT");

		$this->eqDb->where ('company_id', $_SESSION['talenitics_company_id']);//must set it to company ID **VERY IMPORTANT**
		$allTalent = $this->eqDb->get ( 'talent t' ,null,'t.firstname, t.lastname, t.email, t.company_id, t.id, role, branch_id, department_id, gender, b.name as branchName, d.name as departmentName, 
			YEAR(CURRENT_TIMESTAMP) - YEAR(dob) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(dob, 5)) as age, 
			YEAR(CURRENT_TIMESTAMP) - YEAR(date_joined) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(date_joined, 5)) as yearsEmployed');

		return $allTalent;
	}

	public function getTalent($record, $recordField = 'email') {

		$record = $this->eqDb->escape($record);

		$this->eqDb->where ('t.' . $recordField, $record);
		$this->eqDb->where ('company_id', $_SESSION['talenitics_company_id']);//must set it to company ID **VERY IMPORTANT**
		$existingTalent = $this->eqDb->getOne ( 'talent t' ,null,'t.firstname,t.lastname,t.email,branch_id, department_id,t.company_id, t.id, 
			YEAR(CURRENT_TIMESTAMP) - YEAR(dob) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(dob, 5)) as age, 
			YEAR(CURRENT_TIMESTAMP) - YEAR(date_joined) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(date_joined, 5)) as yearsEmployed');

		
		if (count($existingTalent)) {
		// echo "Last executed query was " . $this->eqDb->getLastQuery();
			return $existingTalent;
		}
		return false;
	}

	public function getAllManagers($id = false) {

		$record = $this->eqDb->escape($record);

		if($id) {
			$this->eqDb->where ('t.id', $id, '<>');
		}
		$this->eqDb->where ('t.role', 'Manager');
		$this->eqDb->where ('company_id', $_SESSION['talenitics_company_id']);//must set it to company ID **VERY IMPORTANT**
		$existingTalent = $this->eqDb->getOne ( 'talent t' ,null,'t.firstname,t.lastname,t.email,branch_id, department_id,t.company_id, t.id');

		// echo "Last executed query was " . $this->eqDb->getLastQuery();
		
		if (count($existingTalent)) {
			return $existingTalent;
		}
		return false;
	}

	public static function convertToSQLDate($date) {
		
		$date = explode('/', $date);
		$dateYear = $date[2];
		$dateMonth = $date[1];
		$dateDay = $date[0];

		return $dateYear . '-' . $dateMonth . '-' .$dateDay;
	}

	public static function convertDateSQLToUi($date) {
		
		$date = explode('-', $date);
		$dateYear = $date[0];
		$dateMonth = $date[1];
		$dateDay = $date[2];

		return $dateDay . '/' . $dateMonth . '/' .$dateYear;
	}
}