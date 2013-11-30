<?php
/**
Copyright 2013 Nick Korbel, Paul Menchini

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


require_once(ROOT_DIR . 'Domain/User.php');

interface IUserPreferenceRepository
{
	/**
	 * @abstract
	 * @param $userId int
	 * @return array|string[] values, indexed by name
	 */
	public function GetAllUserPreferences($userId);

	/**
	 * @abstract
	 * @param $userId int
	 * @param $preferenceName string
	 * @return string|null
	 */
	public function GetUserPreference($userId, $preferenceName);

	/**
	 * @abstract
	 * @param $userId int
	 * @param $preferenceName string
	 * @param $preferenceValue string
	 * @return void
	 */
	public function SetUserPreference($userId, $preferenceName, $preferenceValue);
}

class UserPreferenceRepository implements IUserPreferenceRepository
{
	/**
	 * @param $userId int
	 * @return array|string[] values, indexed by name
	 */
	public function GetAllUserPreferences($userId)
	{
		$reader = ServiceLocator::GetDatabase()->Query(new GetUserPreferencesCommand($userId));

		$rv = array();
		while ($row = $reader->GetRow())
			$rv[$row[ColumnNames::PrefName]] = $row[ColumnNames::PrefValue];

		return $rv;
	}

	/**
	 * @param $userId int
	 * @param $preferenceName string
	 * @return string|null
	 */
	public function GetUserPreference($userId, $preferenceName)
	{
		$reader = ServiceLocator::GetDatabase()->Query(new GetUserPreferenceCommand($userId, $preferenceName));

		if ($row = $reader->GetRow())
			return $row[ColumnNames::PrefValue];

		return null;
	}

	/**
	 * @param $userId int
	 * @param $preferenceName string
	 * @param $preferenceValue string
	 * @return void
	 */
	public function SetUserPreference($userId, $preferenceName, $preferenceValue)
	{
		$db = ServiceLocator::GetDatabase();

		if (is_null(self::GetUserPreference($userId, $preferenceName)))
			$db->ExecuteInsert(new SaveUserPreferenceCommand($userId, $preferenceName, $preferenceValue));
		else
			$db->Execute(new UpdateUserPreferenceCommand($userId, $preferenceName, $preferenceValue));
	}
}
?>