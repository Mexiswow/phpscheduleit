<?php
require_once('Page.php');
require_once(dirname(__FILE__) . '/../lib/Common/SmartyPage.php');
require_once(dirname(__FILE__) . '/../lib/Server/namespace.php');

interface ILoginPage extends IPage
{
	public function getEmailAddress();
	public function getPassword();
	public function getPersistLogin();
	public function getShowRegisterLink();
	public function setShowRegisterLink($value);
	public function setAvailableLanguages($languages);
	public function getCurrentLanguage();
	public function setUseLogonName($value);
}

class LoginPage extends Page implements ILoginPage
{
	private $_presenter = null;

	public function __construct(Server &$server = null, SmartyPage $smarty = null)
	{
		$title = sprintf('phpScheduleIt - %s', Resources::GetInstance($server)->GetString('Log In'));
		parent::__construct($title, $server, $smarty);
		
		$this->_presenter = new LoginPresenter($this, $server);
	}

	public function PageLoad()
	{
		$this->_presenter->PageLoad();
		$this->smarty->display('login.tpl');		
	}

	public function getEmailAddress()
	{
		return $this->server->GetForm(FormKeys::EMAIL);
	}

	public function getPassword()
	{
		return $this->server->GetForm(FormKeys::PASSWORD);
	}

	public function getPersistLogin()
	{
		return $this->server->GetForm(FormKeys::PERSIST_LOGIN);
	}
	
	public function getShowRegisterLink()
	{
		return $this->smarty->get_template_vars('ShowRegisterLink');
	}
	
	public function setShowRegisterLink($value)
	{
		$this->smarty->assign('ShowRegisterLink', $value);	
	}
	
	public function setAvailableLanguages($languages)
	{
		$this->smarty->assign('Languages', $languages);
	}
	
	public function getCurrentLanguage()
	{
		return $this->server->GetForm(FormKeys::LANGUAGE);
	}
	
	public function setUseLogonName($value)
	{
		$this->smarty->assign('UseLogonName', $value);
	}
	
	public function DisplayWelcome()
	{
		return false;
	}
}
?>