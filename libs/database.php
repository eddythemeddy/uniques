<?php

class Database {

	public function __construct(){

		global $config;	
		global $eqDb;

		$eqDb = new MysqliDb($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
	}
}
