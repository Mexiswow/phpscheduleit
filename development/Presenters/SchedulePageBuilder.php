<?php
/**
Copyright 2011-2013 Nick Korbel

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


interface ISchedulePageBuilder
{
	/**
	 * @param ISchedulePage $page
	 * @param array[int]ISchedule $schedules
	 * @param ISchedule $currentSchedule
	 */
	public function BindSchedules(ISchedulePage $page, $schedules, $currentSchedule);

	/**
	 * @param ISchedulePage $page
	 * @param ISchedule[] $schedules
	 * @param UserSession $user
	 * @return Schedule
	 */
	public function GetCurrentSchedule(ISchedulePage $page, $schedules, UserSession $user);

	/**
	 * Returns range of dates to bind in UTC
	 * @param UserSession $userSession
	 * @param ISchedule $schedule
	 * @param ISchedulePage $page
	 * @return DateRange
	 */
	public function GetScheduleDates(UserSession $userSession, ISchedule $schedule, ISchedulePage $page);

	/**
	 * @param ISchedulePage $page
	 * @param DateRange $dateRange display dates
	 * @param UserSession $userSession
	 * @param ISchedule $schedule
	 */
	public function BindDisplayDates(ISchedulePage $page, DateRange $dateRange, UserSession $userSession,
									 ISchedule $schedule);

	/**
	 * @param ISchedulePage $page
	 * @param array[int]ResourceDto $resources
	 * @param IDailyLayout $dailyLayout
	 */
	public function BindReservations(ISchedulePage $page, $resources, IDailyLayout $dailyLayout);

	/**
	 * @param ISchedulePage $page
	 * @param ResourceGroupTree $resourceGroupTree
	 */
	public function BindResourceGroups(ISchedulePage $page, ResourceGroupTree $resourceGroupTree);

	/**
	 * @param ISchedulePage $page
	 * @param ResourceType[] $resourceTypes
	 */
	public function BindResourceTypes(ISchedulePage $page, $resourceTypes);

	/**
	 * @param ISchedulePage $page
	 * @param Attribute[] $resourceCustomAttributes
	 * @param Attribute[] $resourceTypeCustomAttributes
	 */
	public function BindCustomAttributes(ISchedulePage $page, $resourceCustomAttributes, $resourceTypeCustomAttributes);

	/**
	 * @param int $scheduleId
	 * @param ISchedulePage $page
	 * @return int
	 */
	public function GetGroupId($scheduleId, ISchedulePage $page);

	/**
	 * @param int $scheduleId
	 * @param ISchedulePage $page
	 * @return int
	 */
	public function GetResourceId($scheduleId, ISchedulePage $page);

	/**
	 * @param int $scheduleId
	 * @param ISchedulePage $page
	 * @return ScheduleResourceFilter
	 */
	public function GetResourceFilter($scheduleId, ISchedulePage $page);

	/**
	 * @param ISchedulePage $page
	 * @param ScheduleResourceFilter $filter
	 */
	public function BindResourceFilter(ISchedulePage $page, ScheduleResourceFilter $filter);
}

class SchedulePageBuilder implements ISchedulePageBuilder
{
	/**
	 * @param ISchedulePage $page
	 * @param array[int]ISchedule $schedules
	 * @param ISchedule $currentSchedule
	 */
	public function BindSchedules(ISchedulePage $page, $schedules, $currentSchedule)
	{
		$scheduleId = $currentSchedule->GetId();
		$page->SetSchedules($schedules);
		$page->SetScheduleId($scheduleId);
		$page->SetScheduleName($currentSchedule->GetName());
		$page->SetFirstWeekday($currentSchedule->GetWeekdayStart());
		$style = $page->GetScheduleStyle($scheduleId);
		$page->SetScheduleStyle($style);
	}

	/**
	 * @param ISchedulePage $page
	 * @param ISchedule[] $schedules
	 * @param UserSession $user
	 * @return Schedule
	 */
	public function GetCurrentSchedule(ISchedulePage $page, $schedules, UserSession $user)
	{
		$requestedScheduleId = $page->GetScheduleId();
		if (!empty($requestedScheduleId))
		{
			$schedule = $this->GetSchedule($schedules, $page->GetScheduleId());
		}
		elseif (!empty($user->ScheduleId))
		{
			$schedule = $this->GetSchedule($schedules, $user->ScheduleId);
			if ($schedule->GetId() != $user->ScheduleId)
			{
				$schedule = $this->GetDefaultSchedule($schedules);
			}
		}
		else
		{
			$schedule = $this->GetDefaultSchedule($schedules);
		}

		return $schedule;
	}

	/**
	 * @see ISchedulePageBuilder::GetScheduleDates()
	 */
	public function GetScheduleDates(UserSession $user, ISchedule $schedule, ISchedulePage $page)
	{
		$userTimezone = $user->Timezone;
		$providedDate = $page->GetSelectedDate();
		$date = empty($providedDate) ? Date::Now() : new Date($providedDate, $userTimezone);
		$selectedDate = $date
						->ToTimezone($userTimezone)
						->GetDate();
		$selectedWeekday = $selectedDate->Weekday();

		$scheduleLength = $schedule->GetDaysVisible();

		if ($page->GetShowFullWeek())
		{
			$scheduleLength = 7;
		}

		/**
		 *  Examples
		 *
		 *  if we are on 3 and we need to start on 6, we need to go back 4 days
		 *  if we are on 3 and we need to start on 5, we need to go back 5 days
		 *  if we are on 3 and we need to start on 4, we need to go back 6 days
		 *  if we are on 3 and we need to start on 3, we need to go back 0 days
		 *  if we are on 3 and we need to start on 2, we need to go back 1 days
		 *  if we are on 3 and we need to start on 1, we need to go back 2 days
		 *  if we are on 3 and we need to start on 0, we need to go back 3 days
		 */

		$startDay = $schedule->GetWeekdayStart();

		if ($startDay == Schedule::Today)
		{
			$startDate = $selectedDate;
		}
		else
		{
			$adjustedDays = ($startDay - $selectedWeekday);

			if ($selectedWeekday < $startDay)
			{
				$adjustedDays = $adjustedDays - 7;
			}

			$startDate = $selectedDate->AddDays($adjustedDays);
		}

		$applicableDates = new DateRange($startDate, $startDate->AddDays($scheduleLength));

		return $applicableDates;
	}

	/**
	 * @see ISchedulePageBuilder::BindDisplayDates()
	 */
	public function BindDisplayDates(ISchedulePage $page,
									 DateRange $dateRange,
									 UserSession $userSession,
									 ISchedule $schedule)
	{
		$scheduleLength = $schedule->GetDaysVisible();
		if ($page->GetShowFullWeek())
		{
			$scheduleLength = 7;
		}

		// we don't want to display the last date in the range (it will be midnight of the last day)
		$adjustedDateRange = new DateRange($dateRange
										   ->GetBegin()
										   ->ToTimezone($userSession->Timezone), $dateRange
																				 ->GetEnd()
																				 ->ToTimezone($userSession->Timezone)
																				 ->AddDays(-1));

		$page->SetDisplayDates($adjustedDateRange);

		$startDate = $adjustedDateRange->GetBegin();

		$startDay = $schedule->GetWeekdayStart();

		if ($startDay == Schedule::Today)
		{
			$adjustment = $scheduleLength;
			$prevAdjustment = $scheduleLength;
		}
		else
		{
			$adjustment = max($scheduleLength, 7);
			$prevAdjustment = 7 * floor($adjustment / 7); // ie, if 10, we only want to go back 7 days so there is overlap
		}

		$page->SetPreviousNextDates($startDate->AddDays(-$prevAdjustment), $startDate->AddDays($adjustment));
		$page->ShowFullWeekToggle($scheduleLength < 7);
	}

	/**
	 * @see ISchedulePageBuilder::BindReservations()
	 */
	public function BindReservations(ISchedulePage $page, $resources, IDailyLayout $dailyLayout)
	{
		$page->SetResources($resources);
		$page->SetDailyLayout($dailyLayout);
	}

	/**
	 * @param array|Schedule[] $schedules
	 * @return Schedule
	 */
	private function GetDefaultSchedule($schedules)
	{
		foreach ($schedules as $schedule)
		{
			if ($schedule->GetIsDefault())
			{
				return $schedule;
			}
		}

		return $schedules[0];
	}

	/**
	 * @param array|Schedule[] $schedules
	 * @param int $scheduleId
	 * @return Schedule
	 */
	private function GetSchedule($schedules, $scheduleId)
	{
		foreach ($schedules as $schedule)
		{
			/** @var $schedule Schedule */
			if ($schedule->GetId() == $scheduleId)
			{
				return $schedule;
			}
		}

		return $schedules[0];
	}

	public function BindResourceGroups(ISchedulePage $page, ResourceGroupTree $resourceGroupTree)
	{
		$page->SetResourceGroupTree($resourceGroupTree);
	}

	public function GetGroupId($scheduleId, ISchedulePage $page)
	{
		$groupId = $page->GetGroupId();
		if (!empty($groupId))
		{
			return $groupId;
		}

		$cookie = $this->getTreeCookie($scheduleId);

		if (!empty($cookie))
		{
			if (strpos($cookie, '-') === false)
			{
				return $groupId;
			}
		}

		return null;
	}

	public function GetResourceId($scheduleId, ISchedulePage $page)
	{
		$resourceId = $page->GetResourceId();

		if (!empty($resourceId))
		{
			return $resourceId;
		}

		$cookie = $this->getTreeCookie($scheduleId);

		if (!empty($cookie))
		{
			if (strpos($cookie, '-') !== false)
			{
				$parts = explode('-', $cookie);
				return $parts[2];
			}
		}

		return null;
	}

	private function getTreeCookie($scheduleId)
	{
		$cookie = ServiceLocator::GetServer()
				  ->GetCookie('tree' . $scheduleId);
		if (!empty($cookie))
		{
			$val = json_decode($cookie, true);
			return $val['selected_node'];
		}

		return null;
	}

	public function BindResourceTypes(ISchedulePage $page, $resourceTypes)
	{
		$page->SetResourceTypes($resourceTypes);
	}

	public function BindCustomAttributes(ISchedulePage $page, $resourceCustomAttributes, $resourceTypeCustomAttributes)
	{
		$page->SetResourceCustomAttributes($resourceCustomAttributes);
		$page->SetResourceTypeCustomAttributes($resourceTypeCustomAttributes);
	}

	/**
	 * @param int $scheduleId
	 * @param ISchedulePage $page
	 * @return ScheduleResourceFilter
	 */
	public function GetResourceFilter($scheduleId, ISchedulePage $page)
	{
		$filter = new ScheduleResourceFilter();
		if ($page->FilterSubmitted())
		{
			$filter = new ScheduleResourceFilter($scheduleId,
											  $page->GetResourceTypeId(),
											  $page->GetMaxParticipants(),
											  $page->GetResourceAttributes(),
											  $page->GetResourceTypeAttributes());
		}
		else
		{
			$cookie = ServiceLocator::GetServer()
					  ->GetCookie('resource_filter' . $scheduleId);
			if (!empty($cookie))
			{
				$val = json_decode($cookie);
				$filter = ScheduleResourceFilter::FromCookie($val);
			}
		}

		$filter->ResourceId = $this->GetResourceId($scheduleId, $page);
		$filter->GroupId = $this->GetGroupId($scheduleId, $page);

		return $filter;
	}

	/**
	 * @param ISchedulePage $page
	 * @param ScheduleResourceFilter $filter
	 */
	public function BindResourceFilter(ISchedulePage $page, ScheduleResourceFilter $filter)
	{
		ServiceLocator::GetServer()
		->SetCookie(new Cookie('resource_filter' . $filter->ScheduleId, json_encode($filter)));
		$page->SetFilter($filter);
	}
}

?>