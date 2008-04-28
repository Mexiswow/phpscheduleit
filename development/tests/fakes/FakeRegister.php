<?php
class FakeRegistration implements IRegistration
{
	public $_RegisterCalled = false;
	public $_UserExists = true;
	public $_ExistsCalled = false;
	public $_LastLogin;
	public $_LastEmail;
	public $_Login;
	public $_Email;
	public $_First;
	public $_Last;
	public $_Password;
	public $_Timezone;
	public $_AdditionalFields;
	
	public function Register($login, $email, $firstName, $lastName, $password, $timezone, $additionalFields = array())
	{
		$this->_RegisterCalled = true;
		$this->_Login = $login;
		$this->_Email = $email;
		$this->_First = $firstName;
		$this->_Last = $lastName;
		$this->_Password = $password;
		$this->_Timezone = $timezone;
		$this->_AdditionalFields = $additionalFields;
	}
	
	public function UserExists($loginName, $emailAddress)
	{
		$this->_ExistsCalled = true;
		$this->_LastLogin = $loginName;
		$this->_LastEmail = $emailAddress;
		
		return $this->_UserExists;
	}
}
?>