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

require_once(ROOT_DIR . 'Presenters/CalendarExportPresenter.php');

class CalendarExportPresenterTests extends TestBase
{
	/**
	 * @var IReservationViewRepository|PHPUnit_Framework_MockObject_MockObject
	 */
	private $repo;

	/**
	 * @var ICalendarExportPage|PHPUnit_Framework_MockObject_MockObject
	 */
	private $page;

	/**
	 * @var CalendarExportPresenter
	 */
	private $presenter;

	public function setup()
	{
		parent::setup();

		$this->repo = $this->getMock('IReservationViewRepository');
		$this->page = $this->getMock('ICalendarExportPage');

		$this->presenter = new CalendarExportPresenter($this->page, $this->repo);
	}

	public function testLoadsReservationByReferenceNumber()
	{
		$referenceNumber = 'ref';
        $reservationResult = new ReservationView();

		$this->page->expects($this->once())
				->method('GetReferenceNumber')
				->will($this->returnValue($referenceNumber));

		$this->repo->expects($this->once())
				->method('GetReservationForEditing')
				->with($this->equalTo($referenceNumber))
				->will($this->returnValue($reservationResult));

		$this->page->expects($this->once())
						->method('SetReservations')
						->with($this->arrayHasKey(0));

		$this->presenter->PageLoad();
	}
}

?>