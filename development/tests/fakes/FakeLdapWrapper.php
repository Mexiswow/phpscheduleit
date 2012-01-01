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

require_once(ROOT_DIR . 'plugins/Authentication/Ldap/ILdap.php');

class FakeLdapWrapper implements ILdap
{
	public $_ExpectedConnect = true;
	public $_ConnectCalled = true;
	
	public $_ExpectedAuthenticate = true;
	public $_AuthenticateCalled = false;
	public $_LastUsername;
	public $_LastPassword;
	
	public $_GetLdapUserCalled = false;
	public $_ExpectedLdapUser;
	
	public function Connect()
	{
		$this->_ConnectCalled = true;
		return $this->_ExpectedConnect;
	}
	
	public function Authenticate($username, $password)
	{
		$this->_AuthenticateCalled = true;
		$this->_LastUsername = $username;
		$this->_LastPassword = $password;
		
		return $this->_ExpectedAuthenticate;
	}
	
	public function GetLdapUser($username)
	{
		$this->_GetLdapUserCalled = true;
		
		return $this->_ExpectedLdapUser;
	}
}

?>