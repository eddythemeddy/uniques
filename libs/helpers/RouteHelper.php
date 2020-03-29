<?php

class RouteHelper {
	
	protected $pages = [
		'front' => [
			'home',
			'whoops'
		],
		'custom' => [
			'login',
			'register'
		],
		'admin' => [
			'in',
			'settings',
			'member', 
			'feed', 
			'search', 
			'forecast', 
			'logout', 
			'welcome', 
			'ingredients',
			'clients',
			'invoices',
			'sales',
			'recipes'
		]
	];

	public function pageType() {

		global $equrl;

		if($equrl[0]) {
			foreach($this->pages as $type => $page) {
				if(in_array($equrl[0], $page)) {
					return $type;
				}
			}
		}

		return 'front';
	}

	public function loadTopNav() {
		$topNavType = $this->topNavType();
		require('template/topnav-' . $topNavType . '.phtml');
	}
}