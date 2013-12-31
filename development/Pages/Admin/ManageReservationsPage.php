<?php
/**
Copyright 2011-2013 Nick Korbel

This file is part of Booked Scheduler.

Booked Scheduler is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Booked Scheduler is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Booked Scheduler.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once(ROOT_DIR . 'Pages/IPageable.php');
require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Pages/Ajax/AutoCompletePage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageReservationsPresenter.php');

interface IManageReservationsPage extends IPageable, IActionPage
{
	/**
	 * @param array|ReservationItemView[] $reservations
	 * @return void
	 */
	public function BindReservations($reservations);

	/**
	 * @return bool
	 */
	public function FilterButtonPressed();

	/**
	 * @return string
	 */
	public function GetStartDate();

	/**
	 * @return string
	 */
	public function GetEndDate();

	/**
	 * @return int
	 */
	public function GetUserId();

	/**
	 * @return string
	 */
	public function GetUserName();

	/**
	 * @return int
	 */
	public function GetScheduleId();

	/**
	 * @return int
	 */
	public function GetResourceId();

	/**
	 * @return string
	 */
	public function GetReferenceNumber();

	/**
	 * @param Date $date|null
	 * @return void
	 */
	public function SetStartDate($date);

	/**
	 * @param Date $date|null
	 * @return void
	 */
	public function SetEndDate($date);

	/**
	 * @param int $userId
	 * @return void
	 */
	public function SetUserId($userId);

	/**
	 * @param string $userName
	 * @return void
	 */
	public function SetUserName($userName);

	/**
	 * @param int $scheduleId
	 * @return void
	 */
	public function SetScheduleId($scheduleId);

	/**
	 * @param int $resourceId
	 * @return void
	 */
	public function SetResourceId($resourceId);


	/**
	 * @param string $referenceNumber
	 * @return void
	 */
	public function SetReferenceNumber($referenceNumber);

	/**
	 * @param array|Schedule[] $schedules
	 * @return void
	 */
	public function BindSchedules($schedules);

	/**
	 * @param array|BookableResource[] $resources
	 * @return void
	 */
	public function BindResources($resources);

	/**
	 * @return string
	 */
	public function GetDeleteReferenceNumber();

	/**
	 * @return string
	 */
	public function GetDeleteScope();

	/**
	 * @return int
	 */
	public function GetReservationStatusId();

	/**
	 * @param $reservationStatusId int
	 * @return void
	 */
	public function SetReservationStatusId($reservationStatusId);

	/**
	 * @return string
	 */
	public function GetApproveReferenceNumber();

	/**
	 * @return void
	 */
	public function ShowPage();

	/**
	 * @return void
	 */
	public function ShowCsv();

	/**
	 * @return string
	 */
	public function GetFormat();

	/**
	 * @param $attributeList IEntityAttributeList
	 */
	public function SetAttributes($attributeList);

	/**
	 * @param $statusReasons ResourceStatusReason[]
	 */
	public function BindResourceStatuses($statusReasons);

	/**
	 * @return int
	 */
	public function GetResourceStatus();

	/**
	 * @return int
	 */
	public function GetResourceStatusReason();

	/**
	 * @return string
	 */
	public function GetResourceStatusReferenceNumber();

	/**
	 * @return string
	 */
	public function GetUpdateScope();

	/**
	 * @return int
	 */
	public function GetUpdateResourceId();
}

class ManageReservationsPage extends ActionPage implements IManageReservationsPage
{
	/**
	 * @var \ManageReservationsPresenter
	 */
	protected $presenter;

	/**
	 * @var \PageablePage
	 */
	protected $pageablePage;

	public function __construct()
	{
	    parent::__construct('ManageReservations', 1);

		$this->presenter = new ManageReservationsPresenter($this,
			new ManageReservationsService(new ReservationViewRepository()),
			new ScheduleRepository(),
			new ResourceRepository(),
			new AttributeService(new AttributeRepository()),
			new UserPreferenceRepository());

		$this->pageablePage = new PageablePage($this);
	}

	public function ProcessAction()
	{
		$this->presenter->ProcessAction();
	}

	public function ProcessPageLoad()
	{
		$userTimezone = $this->server->GetUserSession()->Timezone;

		$this->Set('Timezone', $userTimezone);
		$this->Set('CsvExportUrl', ServiceLocator::GetServer()->GetUrl() . '&' . QueryStringKeys::FORMAT . '=csv');
		$this->presenter->PageLoad($userTimezone);
	}

	public function ShowPage()
	{
		$this->Display('Admin/Reservations/manage_reservations.tpl');
	}

	public function ShowCsv()
	{
		$this->DisplayCsv('Admin/Reservations/reservations_csv.tpl', 'reservations.csv');
	}

	public function BindReservations($reservations)
	{
		$this->Set('reservations', $reservations);
	}


	public function FilterButtonPressed()
	{
		return count($_GET)>0;
	}

	/**
	 * @return string
	 */
	public function GetStartDate()
	{
		return $this->server->GetQuerystring(QueryStringKeys::START_DATE);
	}

	/**
	 * @return string
	 */
	public function GetEndDate()
	{
		return $this->server->GetQuerystring(QueryStringKeys::END_DATE);
	}

	/**
	 * @param Date $date
	 * @return void
	 */
	public function SetStartDate($date)
	{
		$this->Set('StartDate', $date);
	}

	/**
	 * @param Date $date
	 * @return void
	 */
	public function SetEndDate($date)
	{
		$this->Set('EndDate', $date);
	}

	/**
	 * @return int
	 */
	public function GetUserId()
	{
		return $this->GetQuerystring(QueryStringKeys::USER_ID);
	}

	/**
	 * @return string
	 */
	public function GetUserName()
	{
		return $this->GetQuerystring(QueryStringKeys::USER_NAME);
	}

	/**
	 * @return int
	 */
	public function GetScheduleId()
	{
		return $this->GetQuerystring(QueryStringKeys::SCHEDULE_ID);
	}

	/**
	 * @return int
	 */
	public function GetResourceId()
	{
		return $this->GetQuerystring(QueryStringKeys::RESOURCE_ID);
	}

	/**
	 * @param int $userId
	 * @return void
	 */
	public function SetUserId($userId)
	{
		$this->Set('UserIdFilter', $userId);
	}

	/**
	 * @param string $userName
	 * @return void
	 */
	public function SetUserName($userName)
	{
		$this->Set('UserNameFilter', $userName);
	}

	/**
	 * @param int $scheduleId
	 * @return void
	 */
	public function SetScheduleId($scheduleId)
	{
		$this->Set('ScheduleId', $scheduleId);
	}

	/**
	 * @param int $resourceId
	 * @return void
	 */
	public function SetResourceId($resourceId)
	{
		$this->Set('ResourceId', $resourceId);
	}

	public function BindSchedules($schedules)
	{
		$this->Set('Schedules', $schedules);
	}

	public function BindResources($resources)
	{
		$this->Set('Resources', $resources);
	}

	/**
	 * @return string
	 */
	public function GetReferenceNumber()
	{
		return $this->GetQuerystring(QueryStringKeys::REFERENCE_NUMBER);
	}

	/**
	 * @param string $referenceNumber
	 * @return void
	 */
	public function SetReferenceNumber($referenceNumber)
	{
		$this->Set('ReferenceNumber', $referenceNumber);
	}

	/**
	 * @return int
	 */
	function GetPageNumber()
	{
		return $this->pageablePage->GetPageNumber();
	}

	/**
	 * @return int
	 */
	function GetPageSize()
	{
		return $this->pageablePage->GetPageSize();
	}

	/**
	 * @param PageInfo $pageInfo
	 * @return void
	 */
	function BindPageInfo(PageInfo $pageInfo)
	{
		$this->pageablePage->BindPageInfo($pageInfo);
	}

	/**
	 * @return string
	 */
	public function GetDeleteReferenceNumber()
	{
		return $this->GetQuerystring(QueryStringKeys::REFERENCE_NUMBER);
	}

	/**
	 * @return string
	 */
	public function GetDeleteScope()
	{
		return $this->GetForm(FormKeys::SERIES_UPDATE_SCOPE);
	}

	/**
	 * @return int
	 */
	public function GetReservationStatusId()
	{
		return $this->GetQuerystring(QueryStringKeys::RESERVATION_STATUS_ID);
	}

	/**
	 * @param $reservationStatusId int
	 * @return void
	 */
	public function SetReservationStatusId($reservationStatusId)
	{
		$this->Set('ReservationStatusId', $reservationStatusId);
	}

	/**
	 * @return string
	 */
	public function GetApproveReferenceNumber()
	{
		return $this->GetQuerystring(QueryStringKeys::REFERENCE_NUMBER);
	}

	/**
	 * @return string
	 */
	public function GetFormat()
	{
		return $this->GetQuerystring(QueryStringKeys::FORMAT);
	}

	public function ProcessDataRequest($dataRequest)
	{
		// no-op
	}

	/**
	 * @param $attributeList IEntityAttributeList
	 */
	public function SetAttributes($attributeList)
	{
		$this->Set('AttributeList', $attributeList);
	}

	/**
	 * @param $statusReasons ResourceStatusReason[]
	 */
	public function BindResourceStatuses($statusReasons)
	{
		$this->Set('StatusReasons', $statusReasons);
	}

	/**
	 * @return int
	 */
	public function GetResourceStatus()
	{
		return $this->GetForm(FormKeys::RESOURCE_STATUS_ID);
	}

	/**
	 * @return int
	 */
	public function GetResourceStatusReason()
	{
		return $this->GetForm(FormKeys::RESOURCE_STATUS_REASON_ID);
	}

	/**
	 * @return string
	 */
	public function GetResourceStatusReferenceNumber()
	{
		return $this->GetForm(FormKeys::REFERENCE_NUMBER);
	}

	/**
	 * @return string
	 */
	public function GetUpdateScope()
	{
		return $this->GetForm(FormKeys::RESOURCE_STATUS_UPDATE_SCOPE);
	}

	/**
	 * @return int
	 */
	public function GetUpdateResourceId()
	{
		return $this->GetForm(FormKeys::RESOURCE_ID);
	}
}
?>