<?php
/**
Copyright 2011-2012 Nick Korbel

This file is part of phpScheduleIt.

phpScheduleIt is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

phpScheduleIt is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with phpScheduleIt.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once(ROOT_DIR . 'Presenters/LoginPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'Pages/LoginPage.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');

class LoginPresenterTests extends TestBase
{
    /**
     * @var FakeAuth
     */
	private $auth;

    /**
     * @var FakeLoginPage
     */
	private $page;

    /**
     * @var LoginPresenter
     */
    private $presenter;
	
	public function setup()
	{
		parent::setup();
		
		$this->auth = new FakeAuth();		
		$this->page = new FakeLoginPage();
		
		$this->page->_EmailAddress = 'nkorbel@phpscheduleit.org';
		$this->page->_Password = 'somepassword';
		$this->page->_PersistLogin = true;
		
		$this->fakeServer->SetSession(SessionKeys::USER_SESSION, new UserSession(1));

        $this->presenter = new LoginPresenter($this->page, $this->auth);
	}
	
	public function teardown()
	{
		parent::teardown();
		
		$this->auth = null;
		$this->page = null;
	}
	
	public function testLoginCallsAuthValidate() 
	{	
		$this->presenter->Login();
		
		$this->assertEquals($this->page->_EmailAddress, $this->auth->_LastLogin);
		$this->assertEquals($this->page->_Password, $this->auth->_LastPassword);
	}
	
	public function testSuccessfulValidateCallsLogin()
	{
		$this->auth->_ValidateResult = true;
        $this->presenter->Login();
		
		$this->assertEquals($this->page->_EmailAddress, $this->auth->_LastLogin);
		$data = $this->auth->_LastLoginContext->GetData();
		$this->assertEquals($this->page->_PersistLogin, $data->Persist);
	}

	public function testSuccessfulValidateCallsRedirectToNormalPageWhenNoRequestedPage()
	{
		$userSession = new UserSession(1);
		$userSession->HomepageId = 2;
		
		$this->fakeServer->UserSession = $userSession;
		$this->auth->_ValidateResult = true;
		$this->presenter->Login();
		
		$this->assertEquals(Pages::UrlFromId(2), $this->page->_LastRedirect);
	}
	
	public function testRedirectsToRequestedPage()
	{
		$redirect = '/someurl/something.php';
		$this->page->_ResumeUrl = $redirect;
		
		$this->auth->_ValidateResult = true;
        $this->presenter->Login();
		
		$this->assertEquals($redirect, $this->page->_LastRedirect);
	}
	
	public function testPageLoadSetsVariablesCorrectly()
	{
		$this->fakeConfig->SetKey(ConfigKeys::ALLOW_REGISTRATION, 'true');

        $this->presenter->PageLoad();
		
		$this->assertEquals(true, $this->page->GetShowRegisterLink());
	}
	
	public function testPageLoadSetsLanguagesCorrect()
	{
		$this->presenter->PageLoad();
		
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
		$this->presenter->Login();
		
		$this->assertEquals("", $this->page->_LastRedirect, "Does not redirect if auth fails");
		$this->assertTrue($this->page->_ShowLoginError, "Should show login error if auth fails");
	}
	
	public function testAutoLoginIfCookieIsSet()
	{
		$this->page->_ResumeUrl = '/autologin/page/whatever.html';
		$cookie = new Cookie(CookieKeys::PERSIST_LOGIN, "part1|part2");
		$this->fakeServer->SetCookie($cookie);
		
		$this->auth->_CookieValidateResult = true;
		
		$this->presenter->PageLoad();
		
		$this->assertTrue($this->auth->_CookieLoginCalled, "should try to auto login if persist cookie is set");
		$this->assertEquals($cookie->Value, $this->auth->_LastLoginCookie);
		$this->assertEquals($this->page->_ResumeUrl, $this->page->_LastRedirect);
	}
	
	public function testDoesNotAutoLoginIfCookieNotSet()
	{
		$this->page->_ResumeUrl = '/autologin/page/whatever.html';	
		$this->presenter->PageLoad();
		
		$this->assertFalse($this->auth->_CookieLoginCalled, "should not try to auto login without persist cookie");
	}

    public function testCanChangeToKnownLanguage()
    {
        $this->page->_requestedLanguage = 'en_gb';
        $this->fakeResources->_SetCurrentLanguageResult = true;

        $this->presenter->ChangeLanguage();

        $cookie = $this->fakeServer->GetCookie(CookieKeys::LANGUAGE);
        $this->assertEquals('en_gb', $cookie);
        $this->assertEquals('en_gb', $this->page->_selectedLanguage);
    }
}

class FakeLoginPage extends FakePageBase implements ILoginPage
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
    public $_requestedLanguage;
    public $_selectedLanguage;
    public $_CurrentCode = '';

    public function PageLoad()
	{
		$this->_PageLoadWasCalled = true;
	}
	
	public function GetEmailAddress()
	{ 
		return $this->_EmailAddress;
	}
	
	public function GetPassword()
	{ 
		return $this->_Password;
	}
	
	public function GetPersistLogin()
	{
		return $this->_PersistLogin;
	}
	
	public function GetShowRegisterLink()
	{
		return $this->_ShowRegisterLink;
	}
	
	public function SetShowRegisterLink($value)
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
	
	public function GetSelectedLanguage()
	{
		return $this->_CurrentCode;
	}
	
	public function getUseLogonName()
	{
		return $this->_UseLogonName;
	}
	
	public function SetUseLogonName($value)
	{
		$this->_UseLogonName = $value;
	}
	
	public function SetResumeUrl($value)
	{
		$this->_ResumeUrl = $value;
	}
	
	public function GetResumeUrl()
	{
		return $this->_ResumeUrl;
	}
	
	public function SetShowLoginError()
	{
		$this->_ShowLoginError = true;
	}

    public function GetRequestedLanguage()
    {
       return $this->_requestedLanguage;
    }

    public function SetSelectedLanguage($languageCode)
    {
        $this->_selectedLanguage = $languageCode;
    }
}
?>