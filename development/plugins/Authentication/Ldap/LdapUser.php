<?php
/**
Copyright 2012 Nick Korbel

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

class LdapUser
{
	private $fname;
	private $lname;
	private $mail;
	private $phone;
	private $institution;
	private $title;
	private $dn;

	/**
	 * @param $entry Net_LDAP2_Entry
	 */
	public function __construct($entry)
	{
		$this->fname = $this->Get($entry->getValue('givenname'));
		$this->lname = $this->Get($entry->getValue('sn'));
		$this->mail = strtolower($this->Get($entry->getValue('mail')));
		$this->phone = $this->Get($entry->getValue('telephonenumber'));
		$this->institution = $this->Get($entry->getValue('physicaldeliveryofficename'));
		$this->title = $this->Get($entry->getValue('title'));
		$this->dn = $entry->dn();
	}

	public function GetFirstName()
	{
		return $this->fname;
	}

	public function GetLastName()
	{
		return $this->lname;
	}

	public function GetEmail()
	{
		return $this->mail;
	}

	public function GetPhone()
	{
		return $this->phone;
	}

	public function GetInstitution()
	{
		return $this->institution;
	}

	public function GetTitle()
	{
		return $this->title;
	}

	public function GetDn()
	{
		return $this->dn;
	}

	/**
	 * @param string|array $value
	 * @return string
	 */
	private function Get($value)
	{
		if (is_array($value))
		{
			return $value[0];
		}

		return $value;
	}
}

?>