<?php
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');

class RegistrationPresenter
{
	/**
	 * @var \IRegistrationPage
	 */
	private $_page;

	/**
	 * @var IRegistration
	 */
	private $_registration;

	/**
	 * @var IAuthentication
	 */
    private $_auth;
	
	public function __construct(IRegistrationPage $page, $registration = null, $authorization = null)
	{
		$this->_page = $page;
		$this->SetRegistration($registration);
		$this->SetAuthorization($authorization);
				
		if ($page->IsPostBack())
		{
			$this->LoadValidators();
		}
	}
	
	private function SetRegistration($registration)
	{
		if (is_null($registration))
		{
			$this->_registration = new Registration();
		}
		else
		{
			$this->_registration = $registration;
		}
	}
			
	private function SetAuthorization($authorization)
	{
		if (is_null($authorization))
		{
			$this->_auth = PluginManager::Instance()->LoadAuthentication();
		}
		else
		{
			$this->_auth = $authorization;
		}
	}
	
	public function PageLoad()
	{	
		$this->BounceIfNotAllowingRegistration();
		
		if ($this->_page->RegisterClicked())
		{
			$this->Register();
		}

		$this->PopulateTimezones();
		$this->PopulateHomepages();
	}
	
	public function Register()
	{
		if ($this->_page->IsValid())
	    {

    	$additionalFields = array('phone' => $this->_page->GetPhone(),
    							'organization' => $this->_page->GetOrganization(),
    							'position' => $this->_page->GetPosition());
    		
          $this->_registration->Register(
    			$this->_page->GetLoginName(), 
    			$this->_page->GetEmail(),
    			$this->_page->GetFirstName(),
    			$this->_page->GetLastName(),
    			$this->_page->GetPassword(),
    			$this->_page->GetTimezone(),
    			Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE),
    			intval($this->_page->GetHomepage()),
    			$additionalFields);
    			
    		$this->_auth->Login($this->_page->GetEmail(), false);
    		$this->_page->Redirect(Pages::UrlFromId($this->_page->GetHomepage()));
	    }
	}
	
	private function BounceIfNotAllowingRegistration()
	{
		if (!Configuration::Instance()->GetKey(ConfigKeys::ALLOW_REGISTRATION, new BooleanConverter()))
		{
			$this->_page->Redirect(Pages::LOGIN);
		}
	}
	
	private function PopulateTimezones()
	{
		$timezoneValues = array();
		$timezoneOutput = array();
		
		foreach($GLOBALS['APP_TIMEZONES'] as $timezone)
		{
			$timezoneValues[] = $timezone;			
			$timezoneOutput[] = $timezone;		
		}
				
		$this->_page->SetTimezones($timezoneValues, $timezoneOutput);
		
		$timezone = Configuration::Instance()->GetKey(ConfigKeys::SERVER_TIMEZONE);
		if ($this->_page->IsPostBack())
		{
			$timezone = $this->_page->GetTimezone();
		}
		
		$this->_page->SetTimezone($timezone);
	}
	
	private function PopulateHomepages()
	{
		$homepageValues = array();
		$homepageOutput = array();
		
		$pages = Pages::GetAvailablePages();
		foreach($pages as $pageid => $page)
		{
			$homepageValues[] = $pageid;
			$homepageOutput[] = Resources::GetInstance()->GetString($page['name']);
		}
		
		$this->_page->SetHomepages($homepageValues, $homepageOutput);
		
		$homepageId = 1;
		if ($this->_page->IsPostBack())
		{
			$homepageId = $this->_page->GetHomepage();
		}
		
		$this->_page->SetHomepage($homepageId);
	}
	
	private function FormatOffset($offset)
	{
		$hour = intval($offset);
		$decimalPartOfHour = abs($offset) - intval(abs($offset));
		$min = $decimalPartOfHour * 60;
		
		return sprintf("%+d:%02d", $hour, $min);
	}
	
	private function LoadValidators()
	{
		$this->_page->RegisterValidator('fname', new RequiredValidator($this->_page->GetFirstName()));
		$this->_page->RegisterValidator('lname', new RequiredValidator($this->_page->GetLastName()));
		$this->_page->RegisterValidator('username', new RequiredValidator($this->_page->GetLoginName()));
		$this->_page->RegisterValidator('passwordmatch', new EqualValidator($this->_page->GetPassword(), $this->_page->GetPasswordConfirm()));
		$this->_page->RegisterValidator('passwordcomplexity', new RegexValidator($this->_page->GetPassword(), Configuration::Instance()->GetKey(ConfigKeys::PASSWORD_PATTERN)));
		$this->_page->RegisterValidator('emailformat', new EmailValidator($this->_page->GetEmail()));
		$this->_page->RegisterValidator('uniqueemail', new UniqueEmailValidator($this->_page->GetEmail()));
		$this->_page->RegisterValidator('uniqueusername', new UniqueUserNameValidator($this->_page->GetLoginName()));
	}
}
?>