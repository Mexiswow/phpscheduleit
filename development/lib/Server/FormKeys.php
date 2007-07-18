<?php
require_once('namespace.php');

class FormKeys
{
	private function __construct()
	{}
	
	const EMAIL = 'email';
	const PASSWORD = 'password';
	const PERSIST_LOGIN = 'persistLogin';
	const LANGUAGE = 'language';
	const RESUME = 'resume';
}

class Actions
{
	private function __construct()
	{}
	
	const LOGIN = 'login';
}
?>