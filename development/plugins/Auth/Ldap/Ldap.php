<?php
require_once(ROOT_DIR . 'lib/Application/Authorization/namespace.php');
require_once(ROOT_DIR . 'plugins/Auth/Ldap/namespace.php');
require_once(ROOT_DIR . 'plugins/Auth/Ldap/ldap.config.php');

/**
 * Provides LDAP authentication/synchronization for phpScheduleit
 * @see IAuthorization
 */
class Ldap implements IAuthorization
{
	/**
	 * @var IAuthorization
	 */
	private $authToDecorate;
	
	/**
	 * @var AdLdapWrapper
	 */
	private $ldap;
	
	/**
	 * @var LdapOptions
	 */
	private $options;
	
	/**
	 * @var IRegistration
	 */
	private $_registration;
	
	/**
	 * @var PasswordEncryption
	 */
	private $_encryption;
	
	private $user;
	private $password;
	
	public function SetRegistration($registration)
	{
		$this->_registration = $registration;
	}
	
	private function GetRegistration()
	{
		if ($this->_registration == null)
		{
			$this->_registration = new Registration();
		}
		
		return $this->_registration;
	}
	
	public function SetEncryption($passwordEncryption)
	{
		$this->_encryption = $passwordEncryption;
	}
	
	private function GetEncryption()
	{
		if ($this->_encryption == null)
		{
			$this->_encryption = new PasswordEncryption();
		}
		
		return $this->_encryption;
	}

	
	/**
	 * @param IAuthorization $authorization Authorization class to decorate
	 * @param ILdap $ldapImplementation The actual LDAP implemenation to work against
	 * @param LdapOptions $ldapOptions Options to use for LDAP configuration
	 */
	public function __construct($authorization = null, $ldapImplementation = null, $ldapOptions = null)
	{
		$this->authToDecorate = $authorization; 
		if ($authorization == null)
		{
			$this->authToDecorate = new Authorization();
		}

		$this->options = $ldapOptions;
		if ($ldapOptions == null)
		{
			$this->options = new LdapOptions();
		}
		
		$this->ldap = $ldapImplementation;
		if ($ldapImplementation == null)
		{
			$this->ldap = new AdLdapWrapper($this->options);
		}
	}
	
	/**
	 * @see IAuthorization::Validate()
	 */
	public function Validate($username, $password)
	{
		$this->password = $password;
		
		$this->ldap->Connect();
		
		$this->user = $this->ldap->GetLdapUser($username);
		
		$isValid = false;
		
		if ($this->LdapUserExists())
		{
			$isValid = $this->ldap->Authenticate($username, $password);
		}
		else
		{
			if ($this->options->RetryAgainstDatabase())
			{
				$isValid = $this->authToDecorate->Validate($username, $password);
			}
		}
		
		return $isValid;
	}
	
	/**
	 * @see IAuthorization::Login()
	 */
	public function Login($username, $persist)
	{
		if ($this->LdapUserExists())
		{
			$this->Synchronize($username);
		}
		
		$this->authToDecorate->Login($username, $persist);
	}
	
	/**
	 * @see IAuthorization::CookieLogin()
	 */
	public function CookieLogin($cookieValue)
	{
		$this->authToDecorate->CookieLogin($cookieValue);
	}
	
	/**
	 * @see IAuthorization::AreCredentialsKnown()
	 */
	public function AreCredentialsKnown()
	{
		return false;
	}
	
	/**
	 * @see IAuthorization::HandleLoginFailure()
	 */
	public function HandleLoginFailure(ILoginPage $loginPage)
	{
		$this->authToDecorate->HandleLoginFailure($loginPage);
	}
	
	private function LdapUserExists()
	{
		return $this->user != null;
	}
	
	private function Synchronize($username)
	{
		$registration = $this->GetRegistration();
		$encryption = $this->GetEncryption();
		
		$salt = $encryption->Salt();
		$encrypedPassword = $encryption->Encrypt($this->password, $salt);
		
		$email = $this->user->GetEmail();
		$fname = $this->user->GetFirstName();
		$lname = $this->user->GetLastName();
		$phone = $this->user->GetPhone();
		$inst = $this->user->GetInstitution();
		$title = $this->user->GetTitle();
		
		if ($registration->UserExists($username, $this->user->GetEmail()))
		{
			$command = new UpdateUserFromLdapCommand($username, 
							$email,
							$fname,
							$lname,
							$encrypedPassword,
							$salt,
							$phone,
							$inst,
							$title);
							
			ServiceLocator::GetDatabase()->Execute($command);
		}
		else
		{
			$additionalFields = array('phone' => $phone, 'institution' => $inst, 'position' => $title);
			$registration->Register($username, $email, $fname, $lname, $this->password, Configuration::Instance()->GetKey(ConfigKeys::SERVER_TIMEZONE), Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE), Pages::DEFAULT_HOMEPAGE_ID, $additionalFields);
		}
	}
}
?>