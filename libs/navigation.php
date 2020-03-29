<?php

require_once('libs/helpers/MemberHelper.php');

class Navigation {

	protected $pages = [
		'feed' => [
			'icon' => 'flaticon flaticon-home',
			'title' => 'Home',
			'class' => ''
		],
		'my-network' => [
			'icon' => 'flaticon flaticon-users',
			'title' => 'Network',
			'has-bubble' => true,
			'class' => ''
		],
		'notifications' => [
			'icon' => 'flaticon flaticon-notification',
			'title' => 'Notifications',
			'has-bubble' => true,
			'class' => ''
		],
		'mail' => [
			'icon' => 'flaticon flaticon-mail',
			'title' => 'Mail',
			'class' => ''
		]
	];


	public function render() {

		global $equrl;

		$search = '';
		$memberHelper = new MemberHelper();

		if($equrl[0] == 'search' && !empty($_GET['p'])) {
			$search = ' value="' . $_GET['p'] . '"';
		}

		$str = '<ul class="pull-right">';
		foreach($this->pages as $key => $val) :

			$icon = '';
			$keyCount = explode('*', $key);

			if(count($keyCount)) {
				$key = $keyCount[0];
				if(in_array('home', $keyCount)) {
					$key = $keyCount[1];
				}
			}

			if(!empty($val['icon'])) {
				$icon = '<i class="' . $val['icon'] . '"></i>';
				if(isset($val['has-bubble'])) {

					$hasNotif = $memberHelper->getAllNotifications();

					if($hasNotif) {
						$icon .= '<span class="fa fa-circle text-danger notification-bubble"></span>';
					}

				}
			}

			$str .= '<li class="nav-item ' . $this->current($key) . ' ' . $val['class'] . '">' .
				      '<a class="nav-link" href="/'. $key . '">' .
						$icon .
				      	'<span class="nav-item__title">' . $val['title'] . '</span>' .
				      '</a>' .
				    '</li>';
		endforeach;
		$str .= '</ul>';

		return $str;
	}

	public function current($currentPage) {

		global $equrl;

		if($currentPage == $equrl[0]){
			return 'active';
		}
	}
}