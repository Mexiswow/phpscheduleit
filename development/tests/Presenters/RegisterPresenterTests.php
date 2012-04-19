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

require_once(ROOT_DIR . 'Presenters/RegistrationPresenter.php');
require_once(ROOT_DIR . 'Pages/RegistrationPage.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');

class RegisterPresenterTests extends TestBase
{
	/**
	 * @var FakeRegistrationPage
	 */
	private $page;

    /**
     * @var RegistrationPresenter
     */
	private $presenter;

    /**
     * @var FakeRegistration
     */
	private $fakeReg;

    /**
     * @var FakeAuth
     */
    private $fakeAuth;

    /**
     * @var ICaptchaService
     */
    private $captcha;
	
	private $login = 'testlogin';
	private $email = 'test@test.com';
	private $fname = 'First';
	private $lname = 'Last';
	private $phone = '123.123.1234';
	private $password = 'password';
	private $confirm = 'password';
	private $timezone = 'US/Eastern';
	private $homepageId = '1';
	
	public function setup()
	{
		parent::setup();
		
		$this->page = new FakeRegistrationPage();
		$this->fakeReg = new FakeRegistration();
        $this->fakeAuth = new FakeAuth();
        $this->captcha = $this->getMock('ICaptchaService');
		
		$this->presenter = new RegistrationPresenter($this->page, $this->fakeReg, $this->fakeAuth, $this->captcha);
	}
	
	public function teardown()
	{
		parent::teardown();
		
		$this->page = null;
        $this->fakeReg = null;
        $this->fakeAuth = null;
	}
	
	public function testSetsSelectedTimezoneToServerDefault()
	{
		$expectedTimezone = "America/Chicago";
		
		$this->fakeConfig->SetKey(ConfigKeys::SERVER_TIMEZONE, $expectedTimezone);
		$this->page->_IsPostBack = false;
		$this->presenter->PageLoad();
		
		$this->assertEquals($this->page->_Timezone, $expectedTimezone);
	}
	
	public function testSetsSelectedTimezoneToServerSubmitted()
	{
		$expectedTimezone = "America/New_York";
		$this->page->SetTimezone($expectedTimezone);
		$this->page->_IsPostBack = true;
		$this->presenter->PageLoad();
		
		$this->assertEquals($this->page->_Timezone, $expectedTimezone);
	}
	
	public function testLoadsAllTimezones()
	{
		$numberOfTimezones = count($GLOBALS[GlobalKeys::TIMEZONES]);
		$this->presenter->PageLoad();
		
		$this->assertEquals($numberOfTimezones, count($this->page->_TimezoneValues));
		$this->assertEquals($numberOfTimezones, count($this->page->_TimezoneOutput));
	}
	
	public function testLoadsAllHomepages()
	{
		$pages = Pages::GetAvailablePages();
		$numberOfPages = count($pages);
		$this->presenter->PageLoad();
		
		$this->assertEquals($numberOfPages, count($this->page->_HomepageValues));
		$this->assertEquals(1, $this->page->_HomepageValues[0]);
		$this->assertEquals($numberOfPages, count($this->page->_HomepageOutput));
		$this->assertEquals($pages[1]['name'], $this->page->_HomepageOutput[0]);
	}
	
	public function testSetsSelectedHomepageToDefault()
	{
		$expectedHomepage = 1;
		
		$this->page->_IsPostBack = false;
		$this->presenter->PageLoad();
		
		$this->assertEquals($this->page->_Homepage, $expectedHomepage);
	}
	
	public function testSetsSelectedHomepageToServerSubmitted()
	{
		$expectedHomepage = 2;
		$this->page->SetHomepage($expectedHomepage);
		$this->page->_IsPostBack = true;
		$this->presenter->PageLoad();
		
		$this->assertEquals($this->page->_Homepage, $expectedHomepage);
	}
    
    public function testSetsCaptchaUrl()
    {
        $url = "http://blah/blah/blah";

        $this->captcha->expects($this->once())
            ->method('GetImageUrl')
            ->will($this->returnValue($url));

        $this->presenter->PageLoad();

        $this->assertEquals($url, $this->page->_CaptchaUrl);
    }
	
	public function testPresenterRegistersIfAllFieldsAreValid()
	{		
		$this->LoadPageValues();
		
		$additionalFields = array(
					'phone' => $this->phone,
					'instituntion' => '',
					'position' => ''
					);
		
		$this->page->_IsValid = true;
		
		$this->presenter = new RegistrationPresenter($this->page, $this->fakeReg, $this->fakeAuth);
		$this->presenter->Register();
		
		$this->assertTrue($this->fakeReg->_RegisterCalled);
		$this->assertEquals($this->login, $this->fakeReg->_Login);
		$this->assertEquals($this->email, $this->fakeReg->_Email);
		$this->assertEquals($this->fname, $this->fakeReg->_First);
		$this->assertEquals($this->lname, $this->fakeReg->_Last);
		$this->assertEquals($this->password, $this->fakeReg->_Password);
		$this->assertEquals($this->timezone, $this->fakeReg->_Timezone);
		$this->assertEquals(intval($this->homepageId), $this->fakeReg->_HomepageId);
		
		$this->assertEquals($additionalFields['phone'], $this->fakeReg->_AdditionalFields['phone']);
	}
	
	public function testRegistersAllValidators()
	{
		$username = 'un';
		$pattern = '/^[^\s]{6,}$/i';
		$this->fakeConfig->SetKey(ConfigKeys::PASSWORD_PATTERN, '/^[^\s]{6,}$/i');
		
		$this->LoadPageValues();
		$this->page->_IsPostBack = true;
		$this->presenter = new RegistrationPresenter($this->page, $this->fakeReg, $this->fakeAuth);
		
		$v = $this->page->_Validators;
		
		$this->assertEquals(9, count($v));
		$this->assertEquals($v['fname'], new RequiredValidator($this->fname));
		$this->assertEquals($v['lname'], new RequiredValidator($this->lname));
		$this->assertEquals($v['username'], new RequiredValidator($this->login));
		$this->assertEquals($v['passwordmatch'], new EqualValidator($this->password, $this->confirm));
		$this->assertEquals($v['passwordcomplexity'], new RegexValidator($this->password, $pattern));
		$this->assertEquals($v['emailformat'], new EmailValidator($this->email));
		$this->assertEquals($v['uniqueemail'], new UniqueEmailValidator($this->email));
		$this->assertEquals($v['uniqueusername'], new UniqueUserNameValidator($this->login));
	}
    
    public function testDoesNotRegisterIfPageIsNotValid()
    {   
        $this->page->_IsValid = false;
        $this->presenter = new RegistrationPresenter($this->page, $this->fakeReg, $this->fakeAuth);
        $this->presenter->Register();
        
        $this->assertFalse($this->fakeReg->_RegisterCalled);
        $this->assertFalse($this->fakeAuth->_LoginCalled);      
    }
	
	public function testAuthorizesUserAfterRegister()
	{
	    $this->LoadPageValues();		
		$this->page->_IsValid = true;
		$this->page->_Email = $this->email;
		$this->page->_Homepage = 2;
		
		$this->presenter = new RegistrationPresenter($this->page, $this->fakeReg, $this->fakeAuth);
		$this->presenter->Register();
		
		$this->assertTrue($this->fakeReg->_RegisterCalled);
		$this->assertTrue($this->fakeAuth->_LoginCalled);
        $this->assertEquals($this->email, $this->fakeAuth->_LastLogin);
        $this->assertFalse($this->fakeAuth->_LastLoginContext->GetData()->Persist);
        
        $this->assertEquals(Pages::UrlFromId(2), $this->page->_RedirectDestination);
	}
	
	public function testRedirectsToLoginIfAllowSelfRegistrationIsOff()
	{
		$this->fakeConfig->SetKey(ConfigKeys::ALLOW_REGISTRATION, 'false');
		
		$this->presenter = new RegistrationPresenter($this->page, $this->fakeReg, $this->fakeAuth);
		$this->presenter->PageLoad();
		
		$this->assertEquals(Pages::LOGIN, $this->page->_RedirectDestination);
	}
	
	private function LoadPageValues()
	{
		$this->page->SetLoginName($this->login);
		$this->page->SetEmail($this->email);
		$this->page->SetFirstName($this->fname);
		$this->page->SetLastName($this->lname);
		$this->page->SetPhone($this->phone);
		$this->page->SetPassword($this->password);
		$this->page->SetPasswordConfirm($this->confirm);
		$this->page->SetTimezone($this->timezone);
		$this->page->SetHomepage($this->homepageId);
	}
}

class FakeRegistrationPage extends FakePageBase implements IRegistrationPage
{	
	public $_Timezone;
	public $_TimezoneValues;
	public $_TimezoneOutput;
	public $_HomepageValues;
	public $_HomepageOutput;
	public $_Homepage;
	public $_LoginName;
	public $_Email;
	public $_FirstName;
	public $_LastName;
	public $_PhoneNumber;
	public $_Password;
	public $_PasswordConfirm;
	public $_UseLoginName;
    public $_CaptchaUrl;
	
	public function RegisterClicked()
	{
		return false;	
	}
	
	public function SetUseLoginName($useLoginName)
	{
		$this->_UseLoginName = $useLoginName;
	}
	
	public function SetTimezone($timezone)
	{
		$this->_Timezone = $timezone;
	}
	
	public function SetTimezones($timezoneValues, $timezoneOutput)
	{
		$this->_TimezoneValues = $timezoneValues;
		$this->_TimezoneOutput = $timezoneOutput;
	}
	
	public function SetHomepages($hompeageValues, $homepageOutput)
	{
		$this->_HomepageValues = $hompeageValues;
		$this->_HomepageOutput = $homepageOutput;
	}
	
	public function SetHomepage($homepage)
	{
		$this->_Homepage = $homepage;
	}
		
	public function SetLoginName($loginName)
	{
		$this->_LoginName = $loginName;	
	}
	
	public function SetEmail($email)
	{
		$this->_Email = $email;	
	}	
	
	public function SetFirstName($firstName)
	{
		$this->_FirstName = $firstName;	
	}	
	
	public function SetLastName($lastName)
	{
		$this->_LastName = $lastName;	
	}	
	
	public function SetPhone($phoneNumber)
	{
		$this->_PhoneNumber = $phoneNumber;	
	}

	public function SetInstitution($institution)
	{
		
	}
	
	public function SetPosition($positon)
	{
		
	}
	
	public function SetPassword($password)
	{
		$this->_Password = $password;	
	}	
	
	public function SetPasswordConfirm($passwordConfirm)
	{
		$this->_PasswordConfirm = $passwordConfirm;	
	}
	
	public function GetTimezone()
	{
		return $this->_Timezone;
	}
	
	public function GetHomepage()
	{
		return $this->_Homepage;
	}
	
	public function GetLoginName()
	{
		return $this->_LoginName;
	}
	
	public function GetEmail()
	{
		return $this->_Email;
	}	
	
	public function GetFirstName()
	{
		return $this->_FirstName;
	}	
	
	public function GetLastName()
	{
		return $this->_LastName;
	}	
	
	public function GetPhone()
	{
		return $this->_PhoneNumber;
	}
	
	public function GetInstitution()
	{
		return '';
	}
	
	public function GetPosition()
	{
		return '';
	}
	
	public function GetPassword()
	{
		return $this->_Password;
	}	
	
	public function GetPasswordConfirm()
	{
		return $this->_PasswordConfirm;
	}

	public function SetOrganization($organization)
	{
		// TODO: Implement SetOrganization() method.
	}

	public function GetOrganization()
	{
		// TODO: Implement GetOrganization() method.
	}

    public function SetCaptchaImageUrl($captchaUrl)
    {
        $this->_CaptchaUrl = $captchaUrl;
    }

    public function GetCaptcha()
    {
        // TODO: Implement GetCaptcha() method.
    }
}
?>