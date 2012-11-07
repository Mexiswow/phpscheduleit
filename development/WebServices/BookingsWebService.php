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

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class BookingsWebService
{
	/**
	 * @var IRestServer
	 */
	private $server;

	/**
	 * @var IReservationViewRepository
	 */
	private $reservationViewRepository;

	/**
	 * @var IPublicProfileLoader
	 */
	private $publicProfileLoader;

	public function __construct(IRestServer $server, IReservationViewRepository $reservationViewRepository,
								IPublicProfileLoader $publicProfileLoader)
	{
		$this->server = $server;
		$this->reservationViewRepository = $reservationViewRepository;
		$this->publicProfileLoader = $publicProfileLoader;
	}

	/**
	 * @name GetBookings
	 * @description Gets a list of bookings for the specified parameters.
	 * Optional query string parameters: userId, resourceId, scheduleId, startDateTime, endDateTime.
	 * If no query string parameters are provided, the current user will be used.
	 * If no dates are provided, reservations for the next two weeks will be returned.
	 * If dates do not include the timezone offset, the timezone of the authenticated user will be assumed.
	 * @response BookingsResponse
	 * @return void
	 */
	public function GetBookings()
	{
		$startDate = $this->GetStartDate();
		$endDate = $this->GetEndDate();
		$userId = $this->GetUserId();
		if (empty($userId))
		{
			$userId = $this->server->GetSession()->UserId;
		}

		$reservations = $this->reservationViewRepository->GetReservationList($startDate, $endDate, $userId);
		$this->server->WriteResponse(new BookingsResponse($reservations));
	}

	/**
	 * @return Date
	 */
	private function GetStartDate()
	{
		return $this->GetBaseDate(WebServiceQueryStringKeys::START_DATE_TIME);
	}

	/**
	 * @return Date
	 */
	private function GetEndDate()
	{
		return $this->GetBaseDate(WebServiceQueryStringKeys::END_DATE_TIME)->AddDays(14);
	}

	/**
	 * @param string $queryStringKey
	 * @return Date
	 */
	private function GetBaseDate($queryStringKey)
	{
		$dateQueryString = $this->server->GetQueryString($queryStringKey);
		if (empty($dateQueryString))
		{
			return Date::Now();
		}

		if (StringHelper::Contains($dateQueryString, 'T'))
		{
			return Date::ParseExact($dateQueryString);
		}

		return Date::Parse($dateQueryString, $this->server->GetSession()->Timezone);
	}

	/**
	 * @return int
	 */
	private function GetUserId()
	{
		$userIdQueryString = $this->server->GetQueryString(WebServiceQueryStringKeys::USER_ID);
		if (empty($userIdQueryString))
		{
			return null;
		}

		if ($this->server->GetSession()->PublicId == $userIdQueryString)
		{
			return $userIdQueryString;
		}

		return $this->publicProfileLoader->LoadUser($userIdQueryString)->Id();
	}
}

class BookingsResponse extends RestResponse
{
	private $reservations;

	/**
	 * @var array|ReservationItemResponse[]
	 */
	public $Reservations = array();

	/**
	 * @param array|ReservationItemView[] $reservations
	 */
	public function __construct($reservations = array())
	{
		$this->reservations = $reservations;

		foreach ($reservations as $reservation)
		{
			$this->Reservations[] = new ReservationItemResponse($reservation);
		}
	}
}

class ReservationItemResponse
{
	public $ReferenceNumber;
	public $StartDate;
	public $EndDate;
	public $FirstName;
	public $LastName;
	public $ResourceName;
	public $Title;
	public $Description;
	public $RequiresApproval;
	public $IsRecurring;

	public function __construct(ReservationItemView $reservationItemView)
	{
		$this->ReferenceNumber = $reservationItemView->ReferenceNumber;
		$this->StartDate = $reservationItemView->StartDate->ToIso();
		$this->EndDate = $reservationItemView->EndDate->ToIso();
		$this->FirstName = $reservationItemView->FirstName;
		$this->LastName = $reservationItemView->LastName;
		$this->ResourceName = $reservationItemView->ResourceName;
		$this->Title = $reservationItemView->Title;
		$this->Description = $reservationItemView->Description;
		$this->RequiresApproval = $reservationItemView->RequiresApproval;
		$this->IsRecurring = $reservationItemView->IsRecurring;

	}
}

?>