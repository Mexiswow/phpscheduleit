<?php
require_once(ROOT_DIR . 'plugins/Authentication/Ldap/adLdap.php');

class AdLdapWrapper implements ILdap
{
	private $options;
	private $ldap;
	
	public function __construct($ldapOptions)
	{
		$this->options = $ldapOptions;
	}
	
	public function Connect()
	{
		$connected = false;
		$attempts = 0;
		$hosts = $this->options->Hosts();
		$options = $this->options->AdLdapOptions();
		
		while (!$connected && $attempts < count($hosts))
		{
			try 
			{
				$options['host'] = $hosts[$attempts];
				$attempts++;
				$this->ldap = new adLdap($options);
				$connected = true;
			}
			catch (Exception $ex)
			{
				// adLdap throws exception when cannot connect
			}			
		}
		
		return $connected;
	}
	
	public function Authenticate($username, $password)
	{
		return $this->ldap->authenticate($username, $password);
	}
	
	public function GetLdapUser($username)
	{
		$attributes = array( 'sn', 'givenname', 'mail', 'telephonenumber', 'physicaldeliveryofficename', 'title' );
		$entries = $this->ldap->user_info($username, $attributes);
		
		if (count($entries) > 0)
		{
			return new LdapUser($entries);
		}
		
		return null;
	}
}
?>