<?php
require_once('../Presenters/LoginPresenter.php');
require_once('../lib/Authorization/namespace.php');
require_once('../Pages/LoginPage.php');
require_once('../lib/Common/namespace.php');
require_once('fakes/FakeServer.php');

class LoginPresenterTests extends PHPUnit_Framework_TestCase
{
	private $auth;
	private $page;
	private $server;
	
	public function setup()
	{
		$this->auth = new FakeAuth();		
		$this->page = new FakeLoginPage();
		$this->server = new FakeServer();
		
		$this->page->_EmailAddress = 'nkorbel@phpscheduleit.org';
		$this->page->_Password = 'somepassword';
		$this->page->_PersistLogin = true;
	}
	
	public function teardown()
	{
		$this->auth = null;
		$this->page = null;
		$resources =& Resources::GetInstance();
		$resources = null;
	}
	
	public function testLoginCallsAuthValidate() 
	{	
		$presenter = new LoginPresenter($this->page, $this->server);
		$presenter->Login($this->auth);
		
		$this->assertEquals($this->page->_EmailAddress, $this->auth->_LastLogin);
		$this->assertEquals($this->page->_Password, $this->auth->_LastPassword);
	}
	
	public function testSuccessfulValidateCallsLogin()
	{
		$this->auth->_ValidateResult = true;
		$presenter = new LoginPresenter($this->page, $this->server);
		$presenter->Login($this->auth);
		
		$this->assertEquals($this->page->_EmailAddress, $this->auth->_LastLogin);
		$this->assertEquals($this->page->_PersistLogin, $this->auth->_LastPersist);
	}

	public function testSuccessfulValidateCallsRedirectToNormalPageWhenNoRequestedPage()
	{
		$this->auth->_ValidateResult = true;
		$presenter = new LoginPresenter($this->page, $this->server);
		$presenter->Login($this->auth);
		
		$this->assertEquals(Pages::DEFAULT_LOGIN, $this->page->_LastRedirect);
	}
	
	public function testRedirectsToRequestedPage()
	{
		$redirect = '/someurl/something.php';
		$this->page->_ResumeUrl = $redirect;
		
		$this->auth->_ValidateResult = true;
		$presenter = new LoginPresenter($this->page, $this->server);
		$presenter->Login($this->auth);
		
		$this->assertEquals($redirect, $this->page->_LastRedirect);
	}
	
	public function testPageLoadSetsVariablesCorrectly()
	{
		Configuration::SetKey(ConfigKeys::ALLOW_REGISTRATION, 'true');
		Configuration::SetKey(ConfigKeys::USE_LOGON_NAME, 'true');
		
		$presenter = new LoginPresenter($this->page, $this->server);
		$presenter->PageLoad();
		
		$this->assertEquals(true, $this->page->getShowRegisterLink());
		$this->assertEquals(true, $this->page->getUseLogonName());
	}
	
	public function testPageLoadSetsLanguagesCorrect()
	{
		$presenter = new LoginPresenter($this->page, $this->server);
		$presenter->PageLoad();
		
		$resources = Resources::GetInstance();
		$curLang = 'en_US';
		$resources->CurrentLanguage = $curLang;
		
		$langs = $this->page->_Languages;
		
		$this->assertEquals(count($langs), count($resources->AvailableLanguages));
		foreach ($resources->AvailableLanguages as $lang)
		{
			$this->assertEquals($langs[$lang->LanguageCode], $lang->DisplayName);
		}
	}	
		
	public function testErrorIsDisplayedIfValidationFails()
	{
		$this->auth->_ValidateResult = false;
		$presenter = new LoginPresenter($this->page, $this->server);
		$presenter->Login($this->auth);
		
		$this->assertEquals("", $this->page->_LastRedirect, "Does not redirect if auth fails");
		$this->assertTrue($this->page->_ShowLoginError, "Should show login error if auth fails");
	}
}

class FakeLoginPage implements ILoginPage
{
	public $_EmailAddress;
	public $_Password;
	public $_PersistLogin = false;
	public $_LastRedirect;
	public $_ShowRegisterLink;
	public $_PageLoadWasCalled = false;
	public $_Languages = array();
	public $_UseLogonName = false;
	public $_ResumeUrl = "";
	public $_ShowLoginError = false;
	
	public function PageLoad()
	{
		$this->_PageLoadWasCalled = true;
	}
	
	public function getEmailAddress()
	{ 
		return $this->_EmailAddress;
	}
	
	public function getPassword()
	{ 
		return $this->_Password;
	}
	
	public function getPersistLogin()
	{
		return $this->_PersistLogin;
	}
	
	public function getShowRegisterLink()
	{
		return $this->_ShowRegisterLink;
	}
	
	public function setShowRegisterLink($value)
	{
		$this->_ShowRegisterLink = $value;
	}
	
	public function Redirect($url)
	{
		$this->_LastRedirect = $url;
	}
	
	public function setAvailableLanguages($languages)
	{
		$this->_Languages = $languages;
	}
	
	public function getCurrentLanguage()
	{
		return $this->_CurrentCode;
	}
	
	public function getUseLogonName()
	{
		return $this->_UseLogonName;
	}
	
	public function setUseLogonName($value)
	{
		$this->_UseLogonName = $value;
	}
	
	public function setResumeUrl($value)
	{
		$this->_ResumeUrl = $value;
	}
	
	public function getResumeUrl()
	{
		return $this->_ResumeUrl;
	}
	
	public function setShowLoginError()
	{
		$this->_ShowLoginError = true;
	}
}

class FakeAuth implements IAuthorization
{
	public $_LastLogin;
	public $_LastPassword;
	public $_LastPersist;
	public $_LastLoginId;
	
	public $_ValidateResult = false;
	
	public function Validate($username, $password)
	{
		$this->_LastLogin = $username;
		$this->_LastPassword = $password;
		
		return $this->_ValidateResult;
	}
	
	public function Login($username, $persist)
	{
		$this->_LastLogin = $username;
		$this->_LastPersist = $persist;
		$this->_LastLoginId;
	}
}
?>