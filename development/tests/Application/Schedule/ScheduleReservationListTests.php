<?php

require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');

class ScheduleReservationListTests extends TestBase
{
	private $utc;
	private $userTz;
	private $dbDate;
	private $date;
	private $testDbLayout;
	
	public function setup()
	{
		parent::setup();
		
		$utc = 'UTC';
		$dbDate = Date::Parse('2008-11-11', $utc);
		$userTz = 'CST';
		
		$this->utc = $utc;
		$this->userTz = $userTz;
		
		$this->dbDate = $dbDate;
		$this->date = $dbDate;
		
		$layout = new ScheduleLayout($userTz);
		
		$layout->AppendBlockedPeriod(new Time(0, 0, 0, $utc), new Time(8, 0, 0, $utc));
		
		for ($hour = 8; $hour <= 20; $hour++)
		{
			$layout->AppendPeriod(new Time($hour, 0, 0, $utc), new Time($hour, 30, 0 , $utc));
			$layout->AppendPeriod(new Time($hour, 30, 0, $utc), new Time($hour + 1, 0, 0, $utc));
		}
		
		$layout->AppendBlockedPeriod(new Time(21, 0, 0, $utc), new Time(0, 0, 0, $utc));
		
		$this->testDbLayout = $layout;
	}
	
	public function teardown()
	{
		parent::teardown();
	}
	
	function testLayoutIsConvertedToUserTimezoneBeforeSlotsAreCreated()
	{
		$userTz = 'CST';
		$utc = 'UTC';
		
		$midnightUtc = new Time(0,0,0, $utc);
		$midnightCst = new Time(0, 0, 0, $userTz);
		
		$hourDifference = (0 - $midnightUtc->ToTimezone($userTz)->Hour() + 24) ;
		
		$layout = new ScheduleLayout($userTz);
		$layout->AppendBlockedPeriod(new Time(0, 0, 0, $utc), new Time(1, 0, 0, $utc));
		$layout->AppendPeriod(new Time(1, 0, 0, $utc), new Time(3, 0, 0, $utc));
		$layout->AppendBlockedPeriod(new Time(3, 0, 0, $utc), new Time(0, 0, 0, $utc));
		
		$layoutSlots = $layout->GetLayout();
		$this->assertEquals(4, count($layoutSlots));
		
		FakeScheduleReservations::Initialize();
		$r1 = FakeScheduleReservations::$Reservation1;
		$r1->SetStartDate($this->dbDate);
		$r1->SetStartTime(Time::Parse('01:00:00', $utc));
		$r1->SetEndDate(Date::Parse($this->dbDate, $utc));
		$r1->SetEndTime(Time::Parse('03:00:00', $utc));
		
		$scheduleList = new ScheduleReservationList(array($r1), $layout, $this->date);
		$slots = $scheduleList->BuildSlots();
		
		$t1s = new Time(0,0,0, $utc);
		$t1e = new Time(1,0,0, $utc);
		$t2e = new Time(3,0,0, $utc);
		$t3e = new Time(0,0,0, $utc);
		
		$slotDate = $this->date->ToTimezone($userTz)->GetDate();
		
		$slot1 = new EmptyReservationSlot(new Time(0,0,0, $userTz), $t1s->ToTimezone($userTz), $slotDate, false);
		$slot2 = new EmptyReservationSlot($t1s->ToTimezone($userTz), $t1e->ToTimezone($userTz), $slotDate, false);
		$slot3 = new ReservationSlot($t1e->ToTimezone($userTz), $t2e->ToTimezone($userTz), $slotDate, 1, $r1);
		$slot4 = new EmptyReservationSlot($t2e->ToTimezone($userTz), new Time(0,0,0, $userTz), $slotDate, false);
		
		$this->assertEquals(4, count($slots));
		$this->assertEquals($slot1, $slots[0]);
		$this->assertEquals($slot2, $slots[1]);
		$this->assertEquals($slot3, $slots[2]);
		$this->assertEquals($slot4, $slots[3]);	
	}
	
	function testCanGetReservationSlotsForSingleDay()
	{
		$userTz = $this->userTz;
		$dbDate = $this->dbDate;
		$utc = $this->utc;
		
		FakeScheduleReservations::Initialize();
		$r1 = FakeScheduleReservations::$Reservation1;
		$r2 = FakeScheduleReservations::$Reservation2;
		$r3 = FakeScheduleReservations::$Reservation3;
		
		$r1Start = Time::Parse('09:00:00', $utc);		// 03:00 CST
		$r1End = Time::Parse('10:30:00', $utc);		// 04:30 CST
		
		$r2Start = Time::Parse('11:00:00', $utc);		// 05:00 CST
		$r2End = Time::Parse('11:30:00', $utc);		// 05:30 CST
		
		$r3Start = Time::Parse('14:00:00', $utc);		// 08:00 CST
		$r3End = Time::Parse('18:00:00', $utc);		// 12:00 CST
		
		$r1->SetStartDate($dbDate);
		$r1->SetEndDate($dbDate);
		$r1->SetStartTime($r1Start);
		$r1->SetEndTime($r1End);
		
		$r2->SetStartDate($dbDate);
		$r2->SetEndDate($dbDate);
		$r2->SetStartTime($r2Start);
		$r2->SetEndTime($r2End);

		$r3->SetStartDate($dbDate);
		$r3->SetEndDate($dbDate);
		$r3->SetStartTime($r3Start);
		$r3->SetEndTime($r3End);
		
		$reservations = array($r1, $r2, $r3);
		
//		printf("%s\n", Date::Now()->ToString());
	
		$list = new ScheduleReservationList($reservations, $this->testDbLayout, $this->date);
		$slots = $list->BuildSlots();
		
//		printf("%s\n", Date::Now()->ToString());
		
		$slotDate = $this->date->ToTimezone($userTz)->GetDate();
		$expectedSlots = $this->testDbLayout->GetLayout();
		
		$slot1 = new EmptyReservationSlot($expectedSlots[0]->Begin(), $expectedSlots[0]->End(), $slotDate, $expectedSlots[0]->IsReservable());
		$slot2 = new EmptyReservationSlot($expectedSlots[1]->Begin(), $expectedSlots[1]->End(), $slotDate, $expectedSlots[1]->IsReservable());
		$slot3 = new EmptyReservationSlot($expectedSlots[2]->Begin(), $expectedSlots[2]->End(), $slotDate, $expectedSlots[2]->IsReservable());
		$slot4 = new ReservationSlot($expectedSlots[3]->Begin(), $expectedSlots[5]->End(), $slotDate, 3, $r1);
		$slot5 = new EmptyReservationSlot($expectedSlots[6]->Begin(), $expectedSlots[6]->End(), $slotDate, $expectedSlots[6]->IsReservable());
		$slot6 = new ReservationSlot($expectedSlots[7]->Begin(), $expectedSlots[7]->End(), $slotDate, 1, $r2);
		$slot7 = new EmptyReservationSlot($expectedSlots[8]->Begin(), $expectedSlots[8]->End(), $slotDate, $expectedSlots[8]->IsReservable());
		$slot8 = new EmptyReservationSlot($expectedSlots[9]->Begin(), $expectedSlots[9]->End(), $slotDate, $expectedSlots[9]->IsReservable());
		$slot9 = new EmptyReservationSlot($expectedSlots[10]->Begin(), $expectedSlots[10]->End(), $slotDate, $expectedSlots[10]->IsReservable());
		$slot10 = new EmptyReservationSlot($expectedSlots[11]->Begin(), $expectedSlots[11]->End(), $slotDate, $expectedSlots[11]->IsReservable());
		$slot11 = new EmptyReservationSlot($expectedSlots[12]->Begin(), $expectedSlots[12]->End(), $slotDate, $expectedSlots[12]->IsReservable());
		$slot12 = new ReservationSlot($expectedSlots[13]->Begin(), $expectedSlots[20]->End(), $slotDate, 8, $r3);
		$slot13 = new EmptyReservationSlot($expectedSlots[21]->Begin(), $expectedSlots[21]->End(), $slotDate, $expectedSlots[21]->IsReservable());
		$slot14 = new EmptyReservationSlot($expectedSlots[22]->Begin(), $expectedSlots[22]->End(), $slotDate, $expectedSlots[22]->IsReservable());
		$slot15 = new EmptyReservationSlot($expectedSlots[23]->Begin(), $expectedSlots[23]->End(), $slotDate, $expectedSlots[23]->IsReservable());
		$slot16 = new EmptyReservationSlot($expectedSlots[24]->Begin(), $expectedSlots[24]->End(), $slotDate, $expectedSlots[24]->IsReservable());
		$slot17 = new EmptyReservationSlot($expectedSlots[25]->Begin(), $expectedSlots[25]->End(), $slotDate, $expectedSlots[25]->IsReservable());
		$slot18 = new EmptyReservationSlot($expectedSlots[26]->Begin(), $expectedSlots[26]->End(), $slotDate, $expectedSlots[26]->IsReservable());
		$slot19 = new EmptyReservationSlot($expectedSlots[27]->Begin(), $expectedSlots[27]->End(), $slotDate, $expectedSlots[27]->IsReservable());
		$slot20 = new EmptyReservationSlot($expectedSlots[28]->Begin(), $expectedSlots[28]->End(), $slotDate, $expectedSlots[28]->IsReservable());

		$expectedNumberOfSlots = 20;	
		
		$this->assertEquals($expectedNumberOfSlots, count($slots));
		
		$this->assertEquals($slot1, $slots[0]);
		$this->assertEquals($slot2, $slots[1]);
		$this->assertEquals($slot3, $slots[2]);
		$this->assertEquals($slot4, $slots[3]);
		$this->assertEquals($slot5, $slots[4]);
		$this->assertEquals($slot6, $slots[5]);
		$this->assertEquals($slot7, $slots[6]);
		$this->assertEquals($slot8, $slots[7]);
		$this->assertEquals($slot9, $slots[8]);
		$this->assertEquals($slot10, $slots[9]);
		$this->assertEquals($slot11, $slots[10]);
		$this->assertEquals($slot12, $slots[11]);
		$this->assertEquals($slot13, $slots[12]);
		$this->assertEquals($slot14, $slots[13]);
		$this->assertEquals($slot15, $slots[14]);
		$this->assertEquals($slot16, $slots[15]);
		$this->assertEquals($slot17, $slots[16]);
		$this->assertEquals($slot18, $slots[17]);
		$this->assertEquals($slot19, $slots[18]);
		$this->assertEquals($slot20, $slots[19]);
	}
	
	public function testFitsReservationToLayoutIfReservationEndingTimeIsNotAtSlotBreak()
	{
		$userTz = $this->userTz;
		$dbDate = $this->dbDate;
		$utc = $this->utc;
		
		FakeScheduleReservations::Initialize();
		$r1 = FakeScheduleReservations::$Reservation1;
		$r2 = FakeScheduleReservations::$Reservation2;
		$r3 = FakeScheduleReservations::$Reservation3;
		
		$r1Start = Time::Parse('09:00:00', $utc);		// 03:00 CST
		$r1End = Time::Parse('10:40:00', $utc);		// 04:40 CST
		
		$r2Start = Time::Parse('11:00:00', $utc);		// 05:00 CST
		$r2End = Time::Parse('11:20:00', $utc);		// 05:20 CST
		
		$r3Start = Time::Parse('14:00:00', $utc);		// 08:00 CST
		$r3End = Time::Parse('18:05:00', $utc);		// 12:05 CST
		
		$r1->SetStartDate($dbDate);
		$r1->SetEndDate($dbDate);
		$r1->SetStartTime($r1Start);
		$r1->SetEndTime($r1End);
		
		$r2->SetStartDate($dbDate);
		$r2->SetEndDate($dbDate);
		$r2->SetStartTime($r2Start);
		$r2->SetEndTime($r2End);
		
		$r3->SetStartDate($dbDate);
		$r3->SetEndDate($dbDate);
		$r3->SetStartTime($r3Start);
		$r3->SetEndTime($r3End);
		
		$reservations = array($r1, $r2, $r3);
		
		$list = new ScheduleReservationList($reservations, $this->testDbLayout, $this->date);
		$slots = $list->BuildSlots();
		
		$slotDate = $this->date->ToTimezone($userTz)->GetDate();
		$expectedSlots = $this->testDbLayout->GetLayout();
		
		$slot1 = new EmptyReservationSlot($expectedSlots[0]->Begin(), $expectedSlots[0]->End(), $slotDate, $expectedSlots[0]->IsReservable());
		$slot2 = new EmptyReservationSlot($expectedSlots[1]->Begin(), $expectedSlots[1]->End(), $slotDate, $expectedSlots[1]->IsReservable());
		$slot3 = new EmptyReservationSlot($expectedSlots[2]->Begin(), $expectedSlots[2]->End(), $slotDate, $expectedSlots[2]->IsReservable());
		$slot4 = new ReservationSlot($expectedSlots[3]->Begin(), $expectedSlots[5]->End(), $slotDate, 3, $r1);
		$slot5 = new EmptyReservationSlot($expectedSlots[6]->Begin(), $expectedSlots[6]->End(), $slotDate, $expectedSlots[6]->IsReservable());
		$slot6 = new ReservationSlot($expectedSlots[7]->Begin(), $expectedSlots[7]->End(), $slotDate, 1, $r2);
		$slot7 = new EmptyReservationSlot($expectedSlots[8]->Begin(), $expectedSlots[8]->End(), $slotDate, $expectedSlots[8]->IsReservable());
		$slot8 = new EmptyReservationSlot($expectedSlots[9]->Begin(), $expectedSlots[9]->End(), $slotDate, $expectedSlots[9]->IsReservable());
		$slot9 = new EmptyReservationSlot($expectedSlots[10]->Begin(), $expectedSlots[10]->End(), $slotDate, $expectedSlots[10]->IsReservable());
		$slot10 = new EmptyReservationSlot($expectedSlots[11]->Begin(), $expectedSlots[11]->End(), $slotDate, $expectedSlots[11]->IsReservable());
		$slot11 = new EmptyReservationSlot($expectedSlots[12]->Begin(), $expectedSlots[12]->End(), $slotDate, $expectedSlots[12]->IsReservable());
		$slot12 = new ReservationSlot($expectedSlots[13]->Begin(), $expectedSlots[20]->End(), $slotDate, 8, $r3);
		$slot13 = new EmptyReservationSlot($expectedSlots[21]->Begin(), $expectedSlots[21]->End(), $slotDate, $expectedSlots[21]->IsReservable());
		$slot14 = new EmptyReservationSlot($expectedSlots[22]->Begin(), $expectedSlots[22]->End(), $slotDate, $expectedSlots[22]->IsReservable());
		$slot15 = new EmptyReservationSlot($expectedSlots[23]->Begin(), $expectedSlots[23]->End(), $slotDate, $expectedSlots[23]->IsReservable());
		$slot16 = new EmptyReservationSlot($expectedSlots[24]->Begin(), $expectedSlots[24]->End(), $slotDate, $expectedSlots[24]->IsReservable());
		$slot17 = new EmptyReservationSlot($expectedSlots[25]->Begin(), $expectedSlots[25]->End(), $slotDate, $expectedSlots[25]->IsReservable());
		$slot18 = new EmptyReservationSlot($expectedSlots[26]->Begin(), $expectedSlots[26]->End(), $slotDate, $expectedSlots[26]->IsReservable());
		$slot19 = new EmptyReservationSlot($expectedSlots[27]->Begin(), $expectedSlots[27]->End(), $slotDate, $expectedSlots[27]->IsReservable());
		$slot20 = new EmptyReservationSlot($expectedSlots[28]->Begin(), $expectedSlots[28]->End(), $slotDate, $expectedSlots[28]->IsReservable());

		$expectedNumberOfSlots = 20;	
		
		$this->assertEquals($expectedNumberOfSlots, count($slots));
		
		$this->assertEquals($slot1, $slots[0]);
		$this->assertEquals($slot2, $slots[1]);
		$this->assertEquals($slot3, $slots[2]);
		$this->assertEquals($slot4, $slots[3], 'reservation does not end on a slot break, so it should scale back to the closest ending slot time');
		$this->assertEquals($slot5, $slots[4]);
		$this->assertEquals($slot6, $slots[5], 'reservation does not end on a slot break, but it needs to span at least 1 cell');
		$this->assertEquals($slot7, $slots[6]);
		$this->assertEquals($slot8, $slots[7]);
		$this->assertEquals($slot9, $slots[8]);
		$this->assertEquals($slot10, $slots[9]);
		$this->assertEquals($slot11, $slots[10]);
		$this->assertEquals($slot12, $slots[11], 'reservation does not end on a slot break, so it should scale back to the closest ending slot time');
		$this->assertEquals($slot13, $slots[12]);
		$this->assertEquals($slot14, $slots[13]);
		$this->assertEquals($slot15, $slots[14]);
		$this->assertEquals($slot16, $slots[15]);
		$this->assertEquals($slot17, $slots[16]);
		$this->assertEquals($slot18, $slots[17]);
		$this->assertEquals($slot19, $slots[18]);
		$this->assertEquals($slot20, $slots[19]);
	}
	
	public function testReservaionEndingAfterLayoutPeriodAndStartingWithinIsCreatedProperly()
	{
		$utc = $this->utc;
		$userTz = 'CST';
		$dbDate = $this->dbDate;
		
		FakeScheduleReservations::Initialize();
		$r1 = FakeScheduleReservations::$Reservation1;
		$r2 = FakeScheduleReservations::$Reservation2;
		$r3 = FakeScheduleReservations::$Reservation3;
		
		$r1Start = Time::Parse('09:00:00', $utc);		// 03:00 CST
		$r1End = Time::Parse('10:40:00', $utc);		// 04:40 CST
		
		$r2Start = Time::Parse('11:00:00', $utc);		// 05:00 CST
		$r2End = Time::Parse('11:20:00', $utc);		// 05:20 CST
		
		$r3Start = Time::Parse('14:00:00', $utc);		// 08:00 CST
		$r3End = Time::Parse('18:05:00', $utc);		// 12:05 CST
		
		$r1->SetStartDate($dbDate);
		$r1->SetEndDate($dbDate);
		$r1->SetStartTime($r1Start);
		$r1->SetEndTime($r1End);
		
		$r2->SetStartDate($dbDate);
		$r2->SetEndDate($dbDate);
		$r2->SetStartTime($r2Start);
		$r2->SetEndTime($r2End);
		
		$r3->SetStartDate($dbDate);
		$r3->SetEndDate(Date::Parse($dbDate)->AddDays(2));
		$r3->SetStartTime($r3Start);
		$r3->SetEndTime($r3End);
		
		$reservations = array($r1, $r2, $r3);
		
		$list = new ScheduleReservationList($reservations, $this->testDbLayout, $this->date);
		$slots = $list->BuildSlots();
		$expectedSlots = $this->testDbLayout->GetLayout();
		
		$slotDate = $this->date->ToTimezone($userTz)->GetDate();
		
		$slot1 = new EmptyReservationSlot(new Time(0, 0, 0, $userTz), $expectedSlots[0]->End(), $slotDate, $expectedSlots[0]->IsReservable());
		$slot2 = new EmptyReservationSlot($expectedSlots[1]->Begin(), $expectedSlots[1]->End(), $slotDate, $expectedSlots[1]->IsReservable());
		$slot3 = new EmptyReservationSlot($expectedSlots[2]->Begin(), $expectedSlots[2]->End(), $slotDate, $expectedSlots[2]->IsReservable());
		$slot4 = new ReservationSlot($expectedSlots[3]->Begin(), $expectedSlots[5]->End(), $slotDate, 3, $r1);
		$slot5 = new EmptyReservationSlot($expectedSlots[6]->Begin(), $expectedSlots[6]->End(), $slotDate, $expectedSlots[6]->IsReservable());
		$slot6 = new ReservationSlot($expectedSlots[7]->Begin(), $expectedSlots[7]->End(), $slotDate, 1, $r2);
		$slot7 = new EmptyReservationSlot($expectedSlots[8]->Begin(), $expectedSlots[8]->End(), $slotDate, $expectedSlots[8]->IsReservable());
		$slot8 = new EmptyReservationSlot($expectedSlots[9]->Begin(), $expectedSlots[9]->End(), $slotDate, $expectedSlots[9]->IsReservable());
		$slot9 = new EmptyReservationSlot($expectedSlots[10]->Begin(), $expectedSlots[10]->End(), $slotDate, $expectedSlots[10]->IsReservable());
		$slot10 = new EmptyReservationSlot($expectedSlots[11]->Begin(), $expectedSlots[11]->End(), $slotDate, $expectedSlots[11]->IsReservable());
		$slot11 = new EmptyReservationSlot($expectedSlots[12]->Begin(), $expectedSlots[12]->End(), $slotDate, $expectedSlots[12]->IsReservable());
		$slot12 = new ReservationSlot($expectedSlots[13]->Begin(), new Time(0, 0, 0, $userTz), $slotDate, 16, $r3);
		
		$this->assertEquals(12, count($slots));
		
		$this->assertEquals($slot1, $slots[0]);
		$this->assertEquals($slot2, $slots[1]);
		$this->assertEquals($slot3, $slots[2]);
		$this->assertEquals($slot4, $slots[3]);
		$this->assertEquals($slot5, $slots[4]);
		$this->assertEquals($slot6, $slots[5]);
		$this->assertEquals($slot7, $slots[6]);
		$this->assertEquals($slot8, $slots[7]);
		$this->assertEquals($slot9, $slots[8]);
		$this->assertEquals($slot10, $slots[9]);
		$this->assertEquals($slot11, $slots[10]);
		$this->assertEquals($slot12, $slots[11], $slot12 . ' ' . $slots[11]);
	}
	
	public function testReservaionStartingBeforeLayoutPeriodAndEndingAfterLayoutPeriodIsCreatedProperly()
	{
		$utc = $this->utc;
		$dbDate = $this->dbDate;
		$userTz = 'CST';
		
		$r1 = new TestScheduleReservation(1,
					Date::Parse('2008-11-10 09:00:00', $utc),
					Date::Parse('2008-11-14 10:40:00', $utc));
		
		$reservations = array($r1);
		
		$date = Date::Parse('2008-11-12', $userTz);
		$list = new ScheduleReservationList($reservations, $this->testDbLayout, $date);
		$slots = $list->BuildSlots();
		$expectedSlots = $this->testDbLayout->GetLayout();
		
		$slot1 = new ReservationSlot(new Time(0,0,0, $userTz), new Time(0,0,0, $userTz), $date, count($expectedSlots), $r1);
		
		$this->assertEquals(1, count($slots));
		
		$this->assertEquals($slot1, $slots[0]);
	}
	
	public function testReservationStartingOnSameDayOutsideOfLayoutStartsAtFirstSlot()
	{
		$utc = $this->utc;
		$layout = new ScheduleLayout($utc);
		$layout->AppendPeriod(new Time(2,0,0, $utc), new Time(3,0,0, $utc));
		
		FakeScheduleReservations::Initialize();
		$r1 = FakeScheduleReservations::$Reservation1;
		$r1->SetStartDate($this->date);
		$r1->SetEndDate($this->date);
		$r1->SetStartTime(new Time(0,0,0, $utc));
		$r1->SetEndTime(new Time(1,0,0, $utc));
		
		$r2 = FakeScheduleReservations::$Reservation2;
		$r2->SetStartDate($this->date);
		$r2->SetEndDate($this->date);
		$r2->SetStartTime(new Time(1,0,0, $utc));
		$r2->SetEndTime(new Time(3,0,0, $utc));
		
		$list = new ScheduleReservationList(array($r1, $r2), $layout, $this->date);
		$slots = $list->BuildSlots();
		
		$slot1 = new ReservationSlot(new Time(2,0,0, $utc), new Time(3,0,0, $utc), $this->date, 1, $r2);
		
		$this->assertEquals(1, count($slots));
		$this->assertEquals($slot1, $slots[0]);
	}
	
	public function testCanTellIfReservationOccursOnSpecifiedDates()
	{
		$startDate = Date::Parse('2009-11-1 0:0:0', 'UTC');
		$endDate = Date::Parse('2009-11-10 1:0:0', 'UTC');
		
		FakeScheduleReservations::Initialize();
		
		$reservation = FakeScheduleReservations::$Reservation1;
		$reservation->SetStartDate($startDate);
		$reservation->SetEndDate($endDate);
		
		$this->assertTrue($reservation->OccursOn($startDate));
		$this->assertTrue($reservation->OccursOn($startDate->AddDays(2)));
		$this->assertTrue($reservation->OccursOn($endDate));
		
		$this->assertFalse($reservation->OccursOn($startDate->AddDays(-2)));
		$this->assertFalse($reservation->OccursOn($endDate->AddDays(2)));
		
		$this->assertFalse($reservation->OccursOn($startDate->AddDays(-2)));
		$this->assertFalse($reservation->OccursOn($endDate->AddDays(2)));
		
		$this->assertTrue($reservation->OccursOn($startDate->ToTimezone('US/Central')));
		$this->assertTrue($reservation->OccursOn($endDate->ToTimezone('US/Central')));
	}
	
	public function testReservationShouldOccurOnDateIfTheReservationStartsAtAnyTimeOnThatDate()
	{
		$d1 = Date::Parse('2009-10-10 05:00:00', 'UTC');
		$d2 = Date::Parse('2009-10-10 00:00:00', 'CST');
		
		$this->assertEquals($d1->Timestamp(), $d2->Timestamp());
		
		$res1 = new ScheduleReservation(1, Date::Parse('2009-10-09 22:00:00', 'UTC'), Date::Parse('2009-10-09 23:00:00', 'UTC'), 1, null, null, 1, 1, null, null, null);
		// 2009-10-09 17:00:00 - 2009-10-09 18:00:00 CST
		
		$this->assertTrue($res1->OccursOn(Date::Parse('2009-10-09', 'CST')));
	}
	
	public function testReservationDoesNotOccurOnDateIfNoneOfTheReservationOccursAtAnyTimeOnThatDate()
	{
		$d1 = new Date('2009-10-09 22:00:00', 'UTC');	// 2009-10-09 17:00:00 CST
		$d2 = new Date('2009-10-09 23:00:00', 'UTC'); 	// 2009-10-09 18:00:00 CST
		
		$d1Cst = $d1->ToTimezone('CST');
		$d2Cst = $d2->ToTimezone('CST');
		
		$res1 = new ScheduleReservation(1, $d1, $d2, 1, null, null, 1, 1, null, null, null);
		
		$this->assertFalse($res1->OccursOn(Date::Parse('2009-10-10', 'CST')));
	}
	
	public function testDoesNotOccurOnDateIfEndsAtMidnightOfThatDate()
	{
		$date = new Date('2010-01-01 00:00:00', 'UTC');
		
		$builder = new ScheduleReservationBuilder();
		$builder
			->WithStartDate($date->AddDays(-1))
			->WithEndDate($date);
		
		$res = $builder->Build();
		
		$this->assertTrue($res->OccursOn($date->AddDays(-1)));
		$this->assertFalse($res->OccursOn($date));
	}
	
	public function testTwentyFourHourReservationsCrossDay()
	{
		$timezone = 'America/Chicago';
		$layout = new ScheduleLayout($timezone);
		$layout->AppendPeriod(Time::Parse('0:00', $timezone), Time::Parse('4:00', $timezone));
		$layout->AppendPeriod(Time::Parse('4:00', $timezone), Time::Parse('12:00', $timezone));
		$layout->AppendPeriod(Time::Parse('12:00', $timezone), Time::Parse('18:00', $timezone));
		$layout->AppendPeriod(Time::Parse('18:00', $timezone), Time::Parse('20:00', $timezone));
		$layout->AppendPeriod(Time::Parse('20:00', $timezone), Time::Parse('0:00', $timezone));
		
		$date = new Date('2010-01-02', $timezone);
		
		$r1 = new TestScheduleReservation(
			1, 
			Date::Parse('2010-01-01 00:00:00', 'UTC'),
			Date::Parse('2010-01-02 00:00:00', 'UTC'),
			1);
		$r2 = new TestScheduleReservation(
			2, 
			Date::Parse('2010-01-02 00:00:00', 'UTC'),
			Date::Parse('2010-01-03 00:00:00', 'UTC'), 
			1);
		$r3 = new TestScheduleReservation(
			3, 
			Date::Parse('2010-01-03 00:00:00', 'UTC'),
			Date::Parse('2010-01-04 00:00:00', 'UTC'), 
			1);
			
		$list = new ScheduleReservationList(array($r1, $r2, $r3), $layout, $date);

		$slots = $list->BuildSlots();
		
		$this->assertEquals(2, count($slots));
		$this->assertTrue($slots[0]->Begin()->Equals(new Time(0, 0, 0, $timezone)));
		$this->assertTrue($slots[1]->Begin()->Equals(new Time(18, 0, 0, $timezone)));
	}
	
	public function testBindsSingleReservation()
	{
		$tz = 'CST';
		$listDate = Date::Parse('2011-02-06', $tz);
		
		$layout = new ScheduleLayout($tz);
		$layout->AppendPeriod(Time::Parse('0:00', $tz), Time::Parse('2:00', $tz));
		$layout->AppendPeriod(Time::Parse('2:00', $tz), Time::Parse('6:00', $tz));
		$layout->AppendPeriod(Time::Parse('6:00', $tz), Time::Parse('12:00', $tz));
		$layout->AppendPeriod(Time::Parse('12:00', $tz), Time::Parse('18:00', $tz));
		$layout->AppendPeriod(Time::Parse('18:00', $tz), Time::Parse('0:00', $tz));		
		
		$r1 = new TestScheduleReservation(
			1, 
			Date::Parse('2011-02-06 12:00:00', 'UTC'),
			Date::Parse('2011-02-06 18:00:00', 'UTC'),
			1);
		
		$list = new ScheduleReservationList(array($r1), $layout, $listDate);

		$slots = $list->BuildSlots();
		
		$this->assertEquals(5, count($slots));
		$this->assertEquals(new ReservationSlot(Time::Parse('6:00', $tz), Time::Parse('12:00', $tz), $listDate, 1, $r1), $slots[2]);
	}
}

class ScheduleReservationBuilder
{
	public $reservationId;
	public $startDate;
	public $endDate;
	public $reservationType;
	public $summary;
	public $resourceId;
	public $userId;
	public $firstName;
	public $lastName;
	public $referenceNumber;
	
	public function __construct()
	{
		$this->reservationId = 1;
		$this->startDate = Date::Now();
		$this->endDate = Date::Now();
		$this->reservationType = ReservationTypes::Reservation;
		$this->summary = 'summary';
		$this->resourceId = 10;
		$this->userId = 100;
		$this->firstName = 'first';
		$this->lastName = 'last';
		$this->referenceNumber = 'referenceNumber';
	}
	
	public function WithStartDate(Date $startDate)
	{
		$this->startDate = $startDate;
		return $this;
	}
	
	public function WithEndDate(Date $endDate)
	{
		$this->endDate = $endDate;
		return $this;
	}	
	
	public function Build()
	{
		return new ScheduleReservation(
				$this->reservationId,
				$this->startDate,
				$this->endDate,
				$this->reservationType,
				$this->summary,
				null,
				$this->resourceId,
				$this->userId,
				$this->firstName ,
				$this->lastName,
				$this->referenceNumber);
	}
}