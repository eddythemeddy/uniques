<?php

class CompanyHelper {

	protected $eqDb;
	
	public function __construct() {

		global $eqDb;

		$this->eqDb = $eqDb;
	}

	public function getHomeData() {

		$talentHelper = new TalentHelper();

		$departments = $this->getCompanyChild('departments');
		$branches = $this->getCompanyChild('branches');
		$talents = $talentHelper->getAllTalent();

		return [
			'departments' => $departments,
			'branches' => $branches,
			'talent' => $talents
		];
	}

	public function getCompanyChild($key = 'branches') {

	    $companyId = $_SESSION['talenitics_company_id'];

	    switch($key) {

	    	case 'branches':
	    		$table = 'branches';
	    		$rel = 'branch';
	    		$records = 'm.id, m.name, m.location';
	    		break;

	    	case 'departments':
	    		$table = 'departments';
	    		$rel = 'department';
	    		$records = 'm.id, m.name';
	    		break;
	    }

	    $talent = $this->eqDb->subQuery("t");
		$talent->get("talent", null, 'id,' . $rel . '_id,count(*) as cnt');

		$this->eqDb->join($talent, 't.' . $rel . '_id = m.id', 'LEFT');
	    $this->eqDb->where ('company_id', $companyId);
	    $company = $this->eqDb->get ('company_' . $table . ' m', null, $records . ', IFNULL(t.cnt, "No") as totalTalent,
	    	(
			    CASE 
			        WHEN t.cnt > 0 THEN ""
			        ELSE "s"
			    END
			) AS totalTalentSuffix');
	    
	    return $company;
	}
	
}