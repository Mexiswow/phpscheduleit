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

class ReservationListing implements IMutableReservationListing
{
	/**
	 * @param string $targetTimezone
	 */
	public function __construct($targetTimezone)
	{
		$this->timezone = $targetTimezone;
	}

	/**
	 * @var string
	 */
	protected $timezone;

	/**
	 * @var array|ReservationItemView[]
	 */
	protected $_reservations = array();
	
	/**
	 * @var array|ReservationItemView[]
	 */
	protected $_reservationByResource = array();

	/**
	 * @var array|ReservationItemView[]
	 */
	protected $_reservationsByDate = array();

	public function Add($reservation)
	{
		$this->AddItem(new ReservationListItem($reservation));
	}

	public function AddBlackout($blackout)
	{
		$this->AddItem(new BlackoutListItem($blackout));
	}

	protected function AddItem(ReservationListItem $item)
	{
		$currentDate = $item->StartDate()->ToTimezone($this->timezone);
		$lastDate = $item->EndDate()->ToTimezone($this->timezone);

		if ($currentDate->DateEquals($lastDate))
		{
			$this->AddOnDate($item, $currentDate);
		}
		else
		{
			while (!$currentDate->DateEquals($lastDate))
			{
				$this->AddOnDate($item, $currentDate);
				$currentDate = $currentDate->AddDays(1);
			}
			$this->AddOnDate($item, $lastDate);
		}

		$this->_reservations[] = $item;
		$this->_reservationByResource[$item->ResourceId()][] = $item;
	}

	protected function AddOnDate(ReservationListItem $item, Date $date)
	{
//		Log::Debug('Adding id %s on %s', $item->Id(), $date);
		$this->_reservationsByDate[$date->Format('Ymd')][] = $item;
	}
	
	public function Count()
	{
		return count($this->_reservations);
	}
	
	public function Reservations()
	{
		return $this->_reservations;
	}

	/**
	 * @param array|ReservationListItem[] $reservations
	 * @return ReservationListing
	 */
	private function Create($reservations)
	{
		$reservationListing = new ReservationListing($this->timezone);

		if ($reservations != null)
		{
			foreach($reservations as $reservation)
			{
				$reservationListing->AddItem($reservation);
			}
		}

		return $reservationListing;
	}

	/**
	 * @param Date $date
	 * @return ReservationListing
	 */
	public function OnDate($date)
	{
//		Log::Debug('Found %s reservations on %s', count($this->_reservationsByDate[$date->Format('Ymd')]), $date);
		return $this->Create($this->_reservationsByDate[$date->Format('Ymd')]);
	}
	
	public function ForResource($resourceId)
	{
		if (array_key_exists($resourceId, $this->_reservationByResource))
		{
			return $this->Create($this->_reservationByResource[$resourceId]);
		}
		
		return new ReservationListing($this->timezone);
	}
}

?>