<?php
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'tests/Domain/Reservation/ExistingReservationSeriesBuilder.php');

class ExistingReservationTests extends TestBase
{
	public function setup()
	{
		parent::setup();
	}

	public function teardown()
	{
		parent::teardown();
	}
	
	public function testWhenApplyingSimpleUpdatesToSingleInstance()
	{
		$currentSeriesDate = new DateRange(Date::Now(), Date::Now());

		$oldDates = $currentSeriesDate->AddDays(-1);
		$oldReservation = new TestReservation('old', $oldDates);
		
		$currentInstance = new TestReservation('current', $currentSeriesDate);
		
		$futureDates1 = $currentSeriesDate->AddDays(1);
		$futureReservation1 = new TestReservation('new1', $futureDates1);
		
		$futureDates2 = $currentSeriesDate->AddDays(10);
		$futureReservation2 = new TestReservation('new2', $futureDates2);
		
		$currentRepeatOptions = new RepeatDaily(1, $currentSeriesDate->AddDays(50)->GetBegin());

		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithCurrentInstance($currentInstance);
		$builder->WithRepeatOptions($currentRepeatOptions);
		$builder->WithInstance($oldReservation);
		$builder->WithInstance($currentInstance);
		$builder->WithInstance($futureReservation1);
		$builder->WithInstance($futureReservation2);
		$series = $builder->Build();
		// updates
		$series->ApplyChangesTo(SeriesUpdateScope::ThisInstance);
		$series->Update(99, 999, 'new', 'new');
		$series->Repeats($currentRepeatOptions);

		$instances = $series->Instances();
		
		$this->assertEquals(1, count($instances), "should only be existing");
		
		$events = $series->GetEvents();
		
		// remove all future events
		$seriesBranchedEvent = new SeriesBranchedEvent($series);
		$this->assertTrue($series->RequiresNewSeries(), "should require new series if this instance in a series is altered");
		$this->assertEquals(1, count($events));
		$this->assertEquals($seriesBranchedEvent, $events[0], "should have been branched");
		$this->assertEquals(new RepeatNone(), $series->RepeatOptions(), "repeat options should be cleared for new instance");
	}
	
	public function testWhenApplyingRecurrenceUpdatesToFutureInstancesSeries()
	{
		$currentSeriesDate = DateRange::Create('2010-01-01 08:30:00', '2010-01-01 12:30:00', 'UTC');

		$oldDates = $currentSeriesDate->AddDays(-1);
		$oldReservation = new TestReservation('old', $oldDates);
		
		$currentInstance = new TestReservation('current', $currentSeriesDate);
		
		$futureDates1 = $currentSeriesDate->AddDays(1);
		$futureReservation1 = new TestReservation('new1', $futureDates1);
		
		$futureDates2 = $currentSeriesDate->AddDays(10);
		$futureReservation2 = new TestReservation('new2', $futureDates2);
		
		$currentRepeatOptions = new RepeatDaily(1, $currentSeriesDate->AddDays(50)->GetBegin());
		
		$repeatDaily = new RepeatDaily(1, $currentSeriesDate->AddDays(10)->GetBegin());

		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithRepeatOptions($currentRepeatOptions);
		$builder->WithInstance($oldReservation);
		$builder->WithInstance($currentInstance);
		$builder->WithInstance($futureReservation1);
		$builder->WithInstance($futureReservation2);
		$builder->WithCurrentInstance($currentInstance);
		$series = $builder->Build();		
		// updates
		$series->ApplyChangesTo(SeriesUpdateScope::FutureInstances);
		$series->Repeats($repeatDaily);

		$instances = $series->Instances();
		
		$this->assertEquals(11, count($instances), "1 existing, 10 repeated dates");
		
		$events = $series->GetEvents();
		
		$this->assertEquals(13, count($events), "1 branched, 10 created, 2 removed");
		// remove all future events
		$instanceRemovedEvent1 = new InstanceRemovedEvent($futureReservation1);
		$instanceRemovedEvent2 = new InstanceRemovedEvent($futureReservation2);
		$seriesBranchedEvent = new SeriesBranchedEvent($series);
		
		$this->assertTrue(in_array($instanceRemovedEvent1, $events), "missing ref {$futureReservation1->ReferenceNumber()}");
		$this->assertTrue(in_array($instanceRemovedEvent2, $events), "missing ref {$futureReservation2->ReferenceNumber()}");
		$this->assertTrue(in_array($seriesBranchedEvent, $events), "should have been branched");
		
		// recreate all future events
		foreach ($instances as $instance)
		{
			if ($instance == $currentInstance)
			{
				continue;
			}
			
			$instanceAddedEvent = new InstanceAddedEvent($instance);
			$this->assertTrue(in_array($instanceAddedEvent, $events), "missing ref num {$instance->ReferenceNumber()}");
		}
	}
	
	public function testWhenApplyingSimpleUpdatesToFutureInstancesSeries()
	{
		$currentSeriesDate = new DateRange(Date::Now(), Date::Now());

		$oldDates = $currentSeriesDate->AddDays(-1);
		$oldReservation = new TestReservation('old', $oldDates);
		
		$currentInstance = new TestReservation('current', $currentSeriesDate);
		
		$futureDates1 = $currentSeriesDate->AddDays(1);
		$futureReservation1 = new TestReservation('new1', $futureDates1);
		
		$futureDates2 = $currentSeriesDate->AddDays(10);
		$futureReservation2 = new TestReservation('new2', $futureDates2);
		
		$currentRepeatOptions = new RepeatDaily(1, $currentSeriesDate->AddDays(50)->GetBegin());

		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithCurrentInstance($currentInstance);
		$builder->WithRepeatOptions($currentRepeatOptions);
		$builder->WithInstance($oldReservation);
		$builder->WithInstance($currentInstance);
		$builder->WithInstance($futureReservation1);
		$builder->WithInstance($futureReservation2);
		$series = $builder->Build();
		// updates
		$series->ApplyChangesTo(SeriesUpdateScope::FutureInstances);
		$series->Repeats($currentRepeatOptions);

		$instances = $series->Instances();
		
		$this->assertEquals(3, count($instances), "should only be existing and future instances");
		
		$events = $series->GetEvents();
		
		// remove all future events
		$seriesBranchedEvent = new SeriesBranchedEvent($series);
		$this->assertEquals(1, count($events));
		$this->assertEquals($seriesBranchedEvent, $events[0], "should have been branched");
	}
	
	public function testWhenApplyingRecurrenceUpdatesToFullSeries()
	{
		$today = new DateRange(Date::Now(), Date::Now());

		$oldDates = $today->AddDays(-1);
		$oldReservation = new TestReservation('old', $oldDates);
				
		$currentSeriesDate = $today->AddDays(5);
		$currentInstance = new TestReservation('current', $currentSeriesDate);
		
		$futureDates1 = $today->AddDays(1);
		$afterTodayButBeforeCurrent = new TestReservation('new1', $futureDates1);
		
		$futureDates2 = $today->AddDays(10);
		$afterCurrent = new TestReservation('new2', $futureDates2);
		
		$currentRepeatOptions = new RepeatYearly(1, $currentSeriesDate->AddDays(400)->GetBegin());
		
		$repeatDaily = new RepeatDaily(1, $currentSeriesDate->AddDays(10)->GetBegin());

		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithRepeatOptions($currentRepeatOptions);
		$builder->WithInstance($oldReservation);
		$builder->WithInstance($currentInstance);
		$builder->WithInstance($afterTodayButBeforeCurrent);
		$builder->WithInstance($afterCurrent);
		$builder->WithCurrentInstance($currentInstance);
		$series = $builder->Build();
		// updates
		$series->ApplyChangesTo(SeriesUpdateScope::FullSeries);
		$series->Repeats($repeatDaily);

		$instances = $series->Instances();
		
		$this->assertEquals(11, count($instances), "1 old, 1 current, 10 repeated dates");
		$this->assertTrue(in_array($currentInstance, $instances));
		
		$events = $series->GetEvents();
		
		$this->assertEquals(12, count($events), "2 removals, 10 adds");
		// remove all future events
		$instanceRemovedEvent1 = new InstanceRemovedEvent($afterTodayButBeforeCurrent);
		$instanceRemovedEvent2 = new InstanceRemovedEvent($afterCurrent);
		
		$this->assertTrue(in_array($instanceRemovedEvent1, $events), "missing ref {$afterTodayButBeforeCurrent->ReferenceNumber()}");
		$this->assertTrue(in_array($instanceRemovedEvent2, $events), "missing ref {$afterCurrent->ReferenceNumber()}");

		// recreate all future events
		foreach ($instances as $instance)
		{
			if ($instance == $currentInstance)
			{
				continue;
			}
			
			$instanceAddedEvent = new InstanceAddedEvent($instance);
			$this->assertTrue(in_array($instanceAddedEvent, $events), "missing ref num {$instance->ReferenceNumber()}");
		}
	}
	
	public function testWhenExtendingEndDateOfRepeatOptionsOnFullSeries()
	{
		$currentSeriesDate = new DateRange(Date::Now()->AddDays(1), Date::Now()->AddDays(1));
		$currentInstance = new TestReservation('current', $currentSeriesDate);
		$futureInstance = new TestReservation('future', $currentSeriesDate->AddDays(11));
		$repeatDaily = new RepeatDaily(1, $currentSeriesDate->AddDays(10)->GetBegin());

		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithRepeatOptions($repeatDaily);
		$builder->WithInstance(new TestReservation('past', $currentSeriesDate->AddDays(-1)));
		$builder->WithInstance($currentInstance);
		$builder->WithInstance($futureInstance);
		$builder->WithCurrentInstance($currentInstance);
		
		$series = $builder->Build();
		$series->ApplyChangesTo(SeriesUpdateScope::FullSeries);
		$series->Repeats(new RepeatDaily(1, $currentSeriesDate->AddDays(20)->GetBegin()));
		
		$instances = $series->Instances();
		$this->assertEquals(22, count($instances), "1 past, 1 current, 20 future (including existing instance)");
		$this->assertTrue(in_array($currentInstance, $instances), "current should not have been altered");
		$this->assertTrue(in_array($futureInstance, $instances), "existing future should not have been altered");
		
		$events = $series->GetEvents();
		$this->assertEquals(19, count($events), "should have nothing other than new instance created events");
	}
	
	public function testWhenReducingEndDateOfRepeatOptionsOnFullSeries()
	{
		$currentSeriesDate = new DateRange(Date::Now()->AddDays(1), Date::Now()->AddDays(1));
		$currentInstance = new TestReservation('current', $currentSeriesDate);
		$futureInstance = new TestReservation('future', $currentSeriesDate->AddDays(20));
		$repeatDaily = new RepeatDaily(1, $currentSeriesDate->AddDays(10)->GetBegin());

		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithRepeatOptions($repeatDaily);
		$builder->WithInstance(new TestReservation('past', $currentSeriesDate->AddDays(-1)));
		$builder->WithInstance($currentInstance);
		$builder->WithInstance($futureInstance);
		$builder->WithCurrentInstance($currentInstance);
		
		$series = $builder->Build();
		$series->ApplyChangesTo(SeriesUpdateScope::FullSeries);
		$series->Repeats(new RepeatDaily(1, $currentSeriesDate->AddDays(19)->GetBegin()));
		
		$instances = $series->Instances();
		$this->assertEquals(21, count($instances), "1 past, 1 current, 19 future (including existing instance)");
		$this->assertTrue(in_array($currentInstance, $instances), "current should not have been altered");
		$this->assertFalse(in_array($futureInstance, $instances), "existing future should not have been altered");
		
		$events = $series->GetEvents();
		$this->assertEquals(20, count($events), "19 created, 1 deleted");
		$this->assertTrue(in_array(new InstanceRemovedEvent($futureInstance), $events));
	}
	
	public function testWhenApplyingSimpleUpdatesToFullSeries()
	{
		$repeatOptions = new RepeatDaily(1, Date::Now());
		$dateRange = new TestDateRange();
		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithRepeatOptions($repeatOptions);
		$builder->WithInstance(new TestReservation('123', $dateRange));
		$builder->WithCurrentInstance(new TestReservation('1', $dateRange->AddDays(5)));
		
		$series = $builder->Build();
		$series->ApplyChangesTo(SeriesUpdateScope::FullSeries);
		$series->Update(9, 10, 'new', 'new');
		$series->Repeats($repeatOptions);
		
		$events = $series->GetEvents();
		
		$this->assertEquals(2, count($series->Instances()));
		$this->assertEquals(0, count($events));
	}
	
	public function testChangingTimeOnlyAppliesTimeDifferenceToAllInstances()
	{
		throw new Exception('todo');
	}
	
	public function testChangingDateOnlyAppliesToSingleInstance()
	{
		throw new Exception('todo');
	}
}
?>