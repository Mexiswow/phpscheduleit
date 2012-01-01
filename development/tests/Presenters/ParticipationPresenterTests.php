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

require_once(ROOT_DIR . 'Presenters/ParticipationPresenter.php');

class ParticipationPresenterTests extends TestBase
{
	/**
	 * @var IParticipationPage
	 */
	private $page;

	/**
	 * @var IReservationRepository
	 */
	private $reservationRepo;

	/**
	 * @var IReservationViewRepository
	 */
	private $reservationViewRepo;

	/**
	 * @var ParticipationPresenter
	 */
	private $presenter;

	public function setup()
	{
		parent::setup();
		
		$this->page = $this->getMock('IParticipationPage');
		$this->reservationRepo = $this->getMock('IReservationRepository');
		$this->reservationViewRepo = $this->getMock('IReservationViewRepository');

		$this->presenter = new ParticipationPresenter($this->page, $this->reservationRepo, $this->reservationViewRepo);
	}

	public function teardown()
	{
		parent::teardown();
	}

	public function testWhenUserAcceptsInvite()
	{
		$invitationAction = InvitationAction::Accept;
		$seriesMethod = 'AcceptInvitation';

		$this->assertUpdatesSeriesParticipation($invitationAction, $seriesMethod);
	}

	public function testWhenUserDeclinesInvite()
	{
		$invitationAction = InvitationAction::Decline;
		$seriesMethod = 'DeclineInvitation';

		$this->assertUpdatesSeriesParticipation($invitationAction, $seriesMethod);
	}

	public function testWhenUserCancelsAllParticipation()
	{
		$invitationAction = InvitationAction::CancelAll;
		$seriesMethod = 'CancelAllParticipation';

		$this->assertUpdatesSeriesParticipation($invitationAction, $seriesMethod);
	}

	public function testWhenUserCancelsInstanceParticipation()
	{
		$invitationAction = InvitationAction::CancelInstance;
		$seriesMethod = 'CancelInstanceParticipation';

		$this->assertUpdatesSeriesParticipation($invitationAction, $seriesMethod);
	}

	public function testWhenViewingOpenInvites()
	{
		$startDate = Date::Now();
		$endDate = $startDate->AddDays(30);
		$userId = $this->fakeUser->UserId;
		$inviteeLevel = ReservationUserLevel::INVITEE;

		$reservations[] = new ReservationItemView();
		$reservations[] = new ReservationItemView();
		$reservations[] = new ReservationItemView();

		$this->reservationViewRepo->expects($this->once())
				->method('GetReservationList')
				->with($this->equalTo($startDate), $this->equalTo($endDate), $this->equalTo($userId), $this->equalTo($inviteeLevel))
				->will($this->returnValue($reservations));

		$this->page->expects($this->once())
				->method('BindReservations')
				->with($this->equalTo($reservations));

		$this->presenter->PageLoad();
	}

	private function assertUpdatesSeriesParticipation($invitationAction, $seriesMethod)
	{
		$currentUserId = 1029;
		$referenceNumber = 'abc123';
		$series = $this->getMock('ExistingReservationSeries');

		$this->page->expects($this->once())
			->method('GetResponseType')
			->will($this->returnValue('json'));

		$this->page->expects($this->once())
			->method('GetInvitationAction')
			->will($this->returnValue($invitationAction));

		$this->page->expects($this->once())
			->method('GetInvitationReferenceNumber')
			->will($this->returnValue($referenceNumber));

		$this->page->expects($this->once())
			->method('GetUserId')
			->will($this->returnValue($currentUserId));

		$this->reservationRepo->expects($this->once())
			->method('LoadByReferenceNumber')
			->with($this->equalTo($referenceNumber))
			->will($this->returnValue($series));

		$series->expects($this->once())
			->method($seriesMethod)
			->with($this->equalTo($currentUserId));

		$this->reservationRepo->expects($this->once())
			->method('Update')
			->with($this->equalTo($series));

		$this->page->expects($this->once())
			->method('DisplayResult')
			->with($this->equalTo(null));
		
		$this->presenter->PageLoad();
	}
}
?>