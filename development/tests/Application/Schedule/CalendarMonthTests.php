<?php
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

class CalendarMonthTests extends TestBase
{
	public function testAddsReservationsToCalendar()
	{
		$startsBeforeMonth = new ReservationItemView();
		$startsBeforeMonth->StartDate = Date::Parse('2011-11-25', 'UTC');
		$startsBeforeMonth->EndDate = Date::Parse('2011-12-01 12:25', 'UTC');
		$startsBeforeMonth->ResourceName = 'Something Fun';
		$startsBeforeMonth->UserLevelId = ReservationUserLevel::OWNER;

		$endsAfterMonth = new ReservationItemView();;
		$endsAfterMonth->StartDate = Date::Parse('2011-12-25', 'UTC');
		$endsAfterMonth->EndDate = Date::Parse('2012-01-25', 'UTC');
		$endsAfterMonth->ResourceName = 'Something Fun';
		$endsAfterMonth->UserLevelId = ReservationUserLevel::PARTICIPANT;

		$firstDayOnly = new ReservationItemView();
		$firstDayOnly->StartDate = Date::Parse('2011-12-01 14:00', 'UTC');
		$firstDayOnly->EndDate = Date::Parse('2011-12-01 16:25', 'UTC');
		$firstDayOnly->ResourceName = 'Something Fun';
		$firstDayOnly->UserLevelId = ReservationUserLevel::OWNER;

		$secondAndThirdDay = new ReservationItemView();
		$secondAndThirdDay->StartDate = Date::Parse('2011-12-02 14:00', 'UTC');
		$secondAndThirdDay->EndDate = Date::Parse('2011-12-03 16:25', 'UTC');
		$secondAndThirdDay->ResourceName = 'Something Fun';
		$secondAndThirdDay->UserLevelId = ReservationUserLevel::INVITEE;

		$notInMonth = new ReservationItemView();
		$notInMonth->StartDate = Date::Parse('2011-11-02 14:00', 'UTC');
		$notInMonth->EndDate = Date::Parse('2011-11-03 16:25', 'UTC');
		$notInMonth->ResourceName = 'Something Fun';
		$notInMonth->UserLevelId = ReservationUserLevel::OWNER;

		$reservations = array($startsBeforeMonth, $endsAfterMonth, $firstDayOnly, $secondAndThirdDay, $notInMonth);

		$timezone = 'America/Chicago';

		$month = new CalendarMonth(12, 2011, $timezone);

		$month->AddReservations($reservations);

		$expectedFirstDay = Date::Parse('2011-12-01', $timezone);
		$expectedLastDay = Date::Parse('2012-01-01', $timezone);

		$this->assertEquals($expectedFirstDay, $month->FirstDay());
		$this->assertEquals($expectedLastDay, $month->LastDay());

		$nullDay = CalendarDay::Null();
		$day1 = new CalendarDay($expectedFirstDay);
		$day1->AddReservation(CalendarReservation::FromView($startsBeforeMonth, $timezone));
		$day1->AddReservation(CalendarReservation::FromView($firstDayOnly, $timezone));
		$day2 = new CalendarDay($expectedFirstDay->AddDays(1));
		$day2->AddReservation(CalendarReservation::FromView($secondAndThirdDay, $timezone));
		$day3 = new CalendarDay($expectedFirstDay->AddDays(2));
		$day3->AddReservation(CalendarReservation::FromView($secondAndThirdDay, $timezone));

		$weeks = $month->Weeks();
		/** @var $actualWeek1 CalendarWeek */
		$actualWeek1 = $weeks[0];
		/** @var $actualDays array|CalendarDay[] */
		$actualDays = $actualWeek1->Days();

		$this->assertEquals(5, count($weeks));
		$this->assertEquals(7, count($actualDays));
		$this->assertEquals($nullDay, $actualDays[0]);
		$this->assertEquals($nullDay, $actualDays[1]);
		$this->assertEquals($nullDay, $actualDays[2]);
		$this->assertEquals($nullDay, $actualDays[3]);

		$this->assertEquals(2, count($actualDays[4]->Reservations()));
		$this->assertEquals($day1, $actualDays[4]);
		$this->assertEquals($day2, $actualDays[5]);
		$this->assertEquals($day3, $actualDays[6]);

		$lastWeekDays = $weeks[4]->Days();
		$lastDayReservations = $lastWeekDays[6]->Reservations();
		$this->assertEquals(CalendarReservation::FromView($endsAfterMonth, $timezone), $lastDayReservations[0]);
	}
}
