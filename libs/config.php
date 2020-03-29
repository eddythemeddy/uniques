<?php

class Config {

	protected $file = 'libs/config/routes.config-';
	protected $env = 'prod';
	protected $siteTitle = 'Ghost Kitchen';

	function __construct() {

		global $config;

		$config = [];

		$this->setEnv();
		$this->bundle = include($this->file);

		define('_SITETITLE_', $this->siteTitle);
		define('_SITEROOT_', $this->bundle['routes']['siteroot']);

		define('_TMPL_', $this->bundle['routes']['template']);
		define('_RES_', _SITEROOT_ . $this->bundle['routes']['resources']);
		define('_PLUG_', _SITEROOT_ . $this->bundle['routes']['plugins']);
		define('_UPLOADS_', _SITEROOT_ . $this->bundle['routes']['siteroot']);
		define('_ASSETS_', _SITEROOT_ . $this->bundle['routes']['assets']);
		define('_SITEBODY_', _TMPL_ . $this->bundle['routes']['sitebody']);
		define('_SITEFOOT_', _TMPL_ . $this->bundle['routes']['sitefooter']);

		/*database stuff*/
		$config['db_host'] = $this->bundle['db']['db_host'];
		$config['db_name'] = $this->bundle['db']['db_name'];
		$config['db_user'] = $this->bundle['db']['db_user'];
		$config['db_pass'] = $this->bundle['db']['db_pass'];
	}

	public function setEnv() {

		if(!isset($_SERVER['HTTP_HOST'])) {
			$host = $_SERVER['HTTP_HOST'];
		} else {
			$host = $_SERVER['SERVER_NAME'];
		}

		if($host === 'localhost') {
			$this->env = 'dev';
		}

		$this->file = $this->file . $this->env . '.php';
	}
}
