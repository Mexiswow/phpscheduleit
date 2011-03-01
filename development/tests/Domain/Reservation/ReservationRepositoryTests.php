<?php
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'tests/Domain/Reservation/ExistingReservationSeriesBuilder.php');
require_once(ROOT_DIR . 'tests/Domain/Reservation/TestReservationSeries.php');

class ReservationRepositoryTests extends TestBase
{
	/**
	 * @var ReservationRepository
	 */
	private $repository;
	
	public function setup()
	{
		parent::setup();
		
		$this->repository = new ReservationRepository();
	}
	
	public function teardown()
	{
		parent::teardown();
		
		$this->repository = null;
	}
	
	public function testCanGetReservationsWithinDateRange()
	{
		$startDate = Date::Create(2008, 05, 20);
		$endDate = Date::Create(2008, 05, 25);
		$scheduleId = 1;
		
		$rows = FakeReservationRepository::GetReservationRows();
		$this->db->SetRow(0, $rows);
		
		$expected = array();
		foreach ($rows as $item)
		{
			$expected[] = ReservationFactory::CreateForSchedule($item);
		}

		$loaded = $this->repository->GetWithin($startDate, $endDate, $scheduleId);		
		
		$this->assertEquals(new GetReservationsCommand($startDate, $endDate, $scheduleId), $this->db->_Commands[0]);
		$this->assertTrue($this->db->GetReader(0)->_FreeCalled);
		$this->assertEquals(count($rows), count($loaded));
		$this->assertEquals($expected, $loaded);
	}
	
	public function testCanCreateScheduleReservation()
	{
		$rows = FakeReservationRepository::GetReservationRows();
		
		$r = $rows[0];
		$expected = new ScheduleReservation(
							$r[ColumnNames::RESERVATION_INSTANCE_ID],
							Date::Parse($r[ColumnNames::RESERVATION_START], 'UTC'),
							Date::Parse($r[ColumnNames::RESERVATION_END], 'UTC'),
							$r[ColumnNames::RESERVATION_TYPE],
							$r[ColumnNames::RESERVATION_DESCRIPTION],
							null, //$r[ColumnNames::RESERVATION_PARENT_ID],
							$r[ColumnNames::RESOURCE_ID],
							$r[ColumnNames::USER_ID],
							$r[ColumnNames::FIRST_NAME],
							$r[ColumnNames::LAST_NAME],
							$r[ColumnNames::REFERENCE_NUMBER]
						);
		
		$actual = ReservationFactory::CreateForSchedule($r);
		
		$startTime = Time::Parse($r[ColumnNames::RESERVATION_START], 'UTC');
		$endTime = Time::Parse($r[ColumnNames::RESERVATION_END], 'UTC');
		
		$startDate = Date::Parse($r[ColumnNames::RESERVATION_START], 'UTC');
		$endDate = Date::Parse($r[ColumnNames::RESERVATION_END], 'UTC');
		
		$this->assertEquals($expected, $actual);		
		
		$this->assertTrue($startDate->Equals($actual->GetStartDate()));
		$this->assertTrue($endDate->Equals($actual->GetEndDate()));
		
		$this->assertTrue($startTime->Equals($actual->GetStartTime()));
		$this->assertTrue($endTime->Equals($actual->GetEndTime()));
	}

	public function testAddReservationWithOneUserAndOneResource()
	{
		$seriesId = 100;
		$reservationId = 428;
		$userId = 232;
		$resourceId = 10978;
		$scheduleId = 123;
		$title = 'title';
		$description = 'description';
		$startCst = '2010-02-15 16:30';
		$endCst = '2010-02-16 17:00';
		$duration = DateRange::Create($startCst, $endCst, 'CST');
		$levelId = ReservationUserLevel::OWNER;
		$repeatOptions = new RepeatNone();
		
		$startUtc = Date::Parse($startCst, 'CST')->ToUtc();
		$endUtc = Date::Parse($endCst, 'CST')->ToUtc();
		
		$dateCreatedUtc = Date::Parse('2010-01-01 12:14:16', 'UTC');
		Date::_SetNow($dateCreatedUtc);
		
		$this->db->_ExpectedInsertIds[0] = $seriesId;
		$this->db->_ExpectedInsertIds[1] = $reservationId;
		
		$reservation = ReservationSeries::Create(
									$userId, 
									$resourceId, 
									$scheduleId, 
									$title, 
									$description, 
									$duration,
									$repeatOptions);
		
		$repeatType = $repeatOptions->RepeatType();
		$repeatOptionsString = $repeatOptions->ConfigurationString();
		$referenceNumber = $reservation->GetInstance($duration->GetBegin())->ReferenceNumber();
		
		$this->repository->Add($reservation);
		
		$insertReservationSeries = new AddReservationSeriesCommand(
				$dateCreatedUtc,
				$title, 
				$description, 
				$repeatType, 
				$repeatOptionsString, 
				$scheduleId, 
				ReservationTypes::Reservation,
				ReservationStatus::Created,
				$userId);
		
		$insertReservation = new AddReservationCommand(
				$startUtc, 
				$endUtc, 
				$referenceNumber, 
				$seriesId);
		
		$insertReservationResource = new AddReservationResourceCommand(
				$seriesId, 
				$resourceId, 
				ResourceLevel::Primary);
		
		$insertReservationUser = new AddReservationUserCommand(
				$reservationId, 
				$userId, 
				$levelId);
		
		$this->assertEquals($insertReservationSeries, $this->db->_Commands[0]);
		$this->assertEquals($insertReservationResource, $this->db->_Commands[1]);
		$this->assertEquals($insertReservation, $this->db->_Commands[2]);
		$this->assertEquals($insertReservationUser, $this->db->_Commands[3]);

		$this->assertEquals(4, count($this->db->_Commands));
	}
	
	public function testRepeatedDatesAreSaved()
	{
		$reservationSeriesId = 109;
		$reservationId = 918;
		$repeatId1 = 919;
		$repeatId2 = 920;
		$repeatId3 = 921;
		
		$timezone = 'UTC';
		
		$startUtc1 = Date::Parse('2010-02-03', $timezone);
		$startUtc2 = Date::Parse('2010-02-04', $timezone);
		$startUtc3 = Date::Parse('2010-02-05', $timezone);
		$endUtc1 = Date::Parse('2010-02-06', $timezone);
		$endUtc2 = Date::Parse('2010-02-07', $timezone);
		$endUtc3 = Date::Parse('2010-02-08', $timezone);
		
		$dates[] = new DateRange($startUtc1, $endUtc1, $timezone);
		$dates[] = new DateRange($startUtc2, $endUtc2, $timezone);
		$dates[] = new DateRange($startUtc3, $endUtc3, $timezone);
		
		$duration = new TestDateRange();
		
		$repeats = $this->getMock('IRepeatOptions');
	
		$repeats->expects($this->once())
			->method('GetDates')
			->with($this->anything())
			->will($this->returnValue($dates));
			
		$reservation = ReservationSeries::Create(1, 1, 1, null, null, $duration, $repeats);

		$this->db->_ExpectedInsertIds[0] = $reservationSeriesId;
		$this->db->_ExpectedInsertIds[1] = $reservationId;
		$this->db->_ExpectedInsertIds[2] = $repeatId1;
		$this->db->_ExpectedInsertIds[3] = $repeatId3;
		
		$this->repository->Add($reservation);
		
		$insertRepeatDate1 = new AddReservationCommand(
				$startUtc1, 
				$endUtc1, 				
				$reservation->GetInstance($startUtc1)->ReferenceNumber(), 
				$reservationSeriesId);
				
		$insertRepeatDate2 = new AddReservationCommand(
				$startUtc2, 
				$endUtc2, 
				$reservation->GetInstance($startUtc2)->ReferenceNumber(), 
				$reservationSeriesId);
				
		$insertRepeatDate3 = new AddReservationCommand(
				$startUtc3, 
				$endUtc3, 
				$reservation->GetInstance($startUtc3)->ReferenceNumber(), 
				$reservationSeriesId);
		
		$this->assertTrue(in_array($insertRepeatDate1, $this->db->_Commands));
		$this->assertTrue(in_array($insertRepeatDate2, $this->db->_Commands));
		$this->assertTrue(in_array($insertRepeatDate3, $this->db->_Commands));
	}
	
	public function testCanAddAdditionalResources()
	{
		$seriesId = 999;
		$id1 = 1;
		$id2 = 2;
		
		$reservation = new TestReservationSeries();
		$reservation->AddResource($id1);
		$reservation->AddResource($id2);
		
		$this->db->_ExpectedInsertId = $seriesId;
				
		$this->repository->Add($reservation);
		
		$insertResource1 = new AddReservationResourceCommand($seriesId, $id1, ResourceLevel::Additional);
		$insertResource2 = new AddReservationResourceCommand($seriesId, $id2, ResourceLevel::Additional);
		
		$this->assertTrue(in_array($insertResource1, $this->db->_Commands));
		$this->assertTrue(in_array($insertResource2, $this->db->_Commands));
	}
	
	public function testLoadByIdFullyHydratesReservationSeriesObject()
	{
		$seriesId = 10;
		$reservationId = 1;
		$referenceNumber = 'currentInstanceRefNum';
		$userId = 10;
		$resourceId = 100;
		$scheduleId = 1000;
		$title = 'title';
		$description = 'description';
		$resourceId1 = 99;
		$resourceId2 = 999;
		$begin = '2010-01-05 12:30:00';
		$end = '2010-01-05 18:30:00';
		$duration = DateRange::Create($begin, $end, 'UTC');
		$interval = 3;
		$repeatType = RepeatType::Daily;
		$terminiationDateString = '2010-01-20 12:30:00'; 
		$terminationDate = Date::FromDatabase($terminiationDateString);
		$repeatOptions = new RepeatDaily($interval, $terminationDate);
		
		$expected = new ExistingReservationSeries();
		$expected->WithId($seriesId);
		$expected->WithOwner($userId);
		$expected->WithPrimaryResource($resourceId);
		$expected->WithSchedule($scheduleId);
		$expected->WithTitle($title);
		$expected->WithDescription($description);
		$expected->WithResource($resourceId1);
		$expected->WithResource($resourceId2);
		$expected->WithRepeatOptions($repeatOptions);
		$instance1 = new Reservation($expected, $duration->AddDays(10));
		$instance1->SetReferenceNumber('instance1');
		$instance1->SetReservationId(909);
		$instance2 = new Reservation($expected, $duration->AddDays(20));
		$instance2->SetReferenceNumber('instance2');
		$instance2->SetReservationId(1909);
		$expected->WithInstance($instance1);
		$expected->WithInstance($instance2);
			
		$expectedInstance = new Reservation($expected, $duration);
		$expectedInstance->SetReferenceNumber($referenceNumber);
		$expectedInstance->SetReservationId($reservationId);
		$expected->WithCurrentInstance($expectedInstance);

		$reservationRow = new ReservationRow(
			$reservationId,
			$begin,
			$end,
			$title,
			$description,
			$repeatType,
			$repeatOptions->ConfigurationString(),
			$referenceNumber,
			$scheduleId,
			$seriesId
			);
			
		$reservationInstanceRow = new ReservationInstanceRow($seriesId);
		$reservationInstanceRow
			->WithInstance($instance1->ReservationId(), $instance1->ReferenceNumber(), $instance1->Duration())
			->WithInstance($instance2->ReservationId(), $instance2->ReferenceNumber(), $instance2->Duration())
			->WithInstance($reservationId, $expectedInstance->ReferenceNumber(), $expectedInstance->Duration());
			
		$reservationResourceRow = new ReservationResourceRow($reservationId);
		$reservationResourceRow
			->WithPrimary($resourceId)
			->WithAdditional($resourceId1)
			->WithAdditional($resourceId2);
			
		$reservationUserRow = new ReservationUserRow($reservationId);
		$reservationUserRow
			->WithOwner($userId);
		
		$this->db->SetRow(0, $reservationRow->Rows());
		$this->db->SetRow(1, $reservationInstanceRow->Rows());
		$this->db->SetRow(2, $reservationResourceRow->Rows());
		$this->db->SetRow(3, $reservationUserRow->Rows());
		
		$actualReservation = $this->repository->LoadById($reservationId);
		
		$this->assertEquals($expected, $actualReservation);
		
		$getReservation = new GetReservationByIdCommand($reservationId);
		$getInstances = new GetReservationSeriesInstances($seriesId);
		$getResources = new GetReservationResourcesCommand($seriesId);
		$getParticipants = new GetReservationParticipantsCommand($reservationId);
		
		$this->assertTrue(in_array($getReservation, $this->db->_Commands));
		$this->assertTrue(in_array($getInstances, $this->db->_Commands));
		$this->assertTrue(in_array($getResources, $this->db->_Commands));
		$this->assertTrue(in_array($getParticipants, $this->db->_Commands));
	}
	
	public function testChangingOnlySharedInformationForFullSeriesJustUpdatesSeriesTable()
	{
		$userId = 10;
		$resourceId = 11;
		$title = "new title";
		$description = "new description";
		
		$builder = new ExistingReservationSeriesBuilder();
		$existingReservation = $builder->Build();
		
		$existingReservation->Update($userId, $resourceId, $title, $description);
		$repeatOptions = $existingReservation->RepeatOptions();
		$repeatType = $repeatOptions->RepeatType();
		$repeatConfiguration = $repeatOptions->ConfigurationString();
		
		$this->repository->Update($existingReservation);
		
		$updateSeriesCommand = new UpdateReservationSeriesCommand(
			$existingReservation->SeriesId(), 
			$title, 
			$description, 
			$repeatType, 
			$repeatConfiguration, 
			Date::Now());
		$this->assertEquals(1, count($this->db->_Commands));
		$this->assertEquals($updateSeriesCommand, $this->db->_Commands[0]);
	}

	public function testBranchedSingleInstance()
	{
		$seriesId = 10909;
		$userId = 10;
		$resourceId = 11;
		$scheduleId = 12;
		$title = "new title";
		$description = "new description";
		$expectedRepeat = new RepeatNone();
		$referenceNumber = 'ref number current';

		$currentReservation = new TestReservation($referenceNumber, new TestDateRange());
		
		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithRequiresNewSeries(true);
		$builder->WithRepeatOptions($expectedRepeat);
		$builder->WithCurrentInstance($currentReservation);
		
		$existingReservation = $builder->BuildTestVersion();		
		$existingReservation->Update($userId, $resourceId, $title, $description);
		$existingReservation->WithSchedule($scheduleId);
		
		
		$this->db->_ExpectedInsertId = $seriesId;
		
		$this->repository->Update($existingReservation);
			
		$addNewSeriesCommand = new AddReservationSeriesCommand(
									Date::Now(), 
									$title, 
									$description, 
									$expectedRepeat->RepeatType(), 
									$expectedRepeat->ConfigurationString(), 
									$scheduleId,
									ReservationTypes::Reservation,
									ReservationStatus::Created,
									$userId);
		
		$updateReservationCommand = $this->GetUpdateReservationCommand($seriesId, $currentReservation);

		$this->assertTrue(in_array($addNewSeriesCommand, $this->db->_Commands));
		$this->assertTrue(in_array($updateReservationCommand, $this->db->_Commands));
	}
	
	public function testBranchedWithMovedAndAddedAndRemovedInstances()
	{
		$existingSeriesId = 10909;
		$newSeriesId = 10910;
		$userId = 10;
		$resourceId = 11;
		$scheduleId = 12;
		$title = "new title";
		$description = "new description";
		
		$dateRange = DateRange::Create('2010-01-10 05:30:00', '2010-01-10 08:30:00', 'UTC');
		$existingInstance1 = new TestReservation('123', $dateRange->AddDays(1));
		$existingInstance2 = new TestReservation('223', $dateRange->AddDays(2));
		$newInstance1 = new TestReservation('323', $dateRange->AddDays(3));
		$newInstance2 = new TestReservation('423', $dateRange->AddDays(4));
		$removedInstance1 = new TestReservation('523', $dateRange->AddDays(5));
		$removedInstance2 = new TestReservation('623', $dateRange->AddDays(6));
		$currentInstance = new TestReservation('999', $dateRange);
		
		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithEvent(new InstanceAddedEvent($newInstance1));
		$builder->WithEvent(new InstanceAddedEvent($newInstance2));
		$builder->WithEvent(new InstanceRemovedEvent($removedInstance1));
		$builder->WithEvent(new InstanceRemovedEvent($removedInstance2));
		$builder->WithEvent(new SeriesBranchedEvent($builder->series));
		$builder->WithRequiresNewSeries(true);
		$builder->WithInstance($existingInstance1);
		$builder->WithInstance($existingInstance2);
		$builder->WithCurrentInstance($currentInstance);
		
		$existingReservation = $builder->BuildTestVersion();
		$existingReservation->Update($userId, $resourceId, $title, $description);		
		$existingReservation->WithSchedule($scheduleId);
		
		$expectedRepeat = $existingReservation->RepeatOptions();
		
		$this->db->_ExpectedInsertId = $newSeriesId;
		
		$this->repository->Update($existingReservation);
		
		$addNewSeriesCommand = new AddReservationSeriesCommand(
									Date::Now(), 
									$title, 
									$description, 
									$expectedRepeat->RepeatType(), 
									$expectedRepeat->ConfigurationString(), 
									$scheduleId,
									ReservationTypes::Reservation,
									ReservationStatus::Created,
									$userId);
		
		$updateReservationCommand1 = $this->GetUpdateReservationCommand($newSeriesId, $existingInstance1);
		$updateReservationCommand2 = $this->GetUpdateReservationCommand($newSeriesId, $existingInstance2);
		$updateReservationCommand3 = $this->GetUpdateReservationCommand($newSeriesId, $currentInstance);
		
		$addReservationCommand1 = $this->GetAddReservationCommand($newSeriesId, $newInstance1);
		$addReservationCommand2 = $this->GetAddReservationCommand($newSeriesId, $newInstance2);
		
		$removeReservationCommand1 = new RemoveReservationCommand($removedInstance1->ReferenceNumber());
		$removeReservationCommand2 = new RemoveReservationCommand($removedInstance2->ReferenceNumber());
		
		$this->assertEquals($addNewSeriesCommand, $this->db->_Commands[0]);
		
		$commands = $this->db->GetCommandsOfType('UpdateReservationCommand');
		$this->assertEquals(3, count($commands));
		
		$this->assertTrue(in_array($updateReservationCommand1, $this->db->_Commands));
		$this->assertTrue(in_array($updateReservationCommand2, $this->db->_Commands));
		$this->assertTrue(in_array($updateReservationCommand3, $this->db->_Commands));
		
		$this->assertTrue(in_array($addReservationCommand1, $this->db->_Commands));
		$this->assertTrue(in_array($addReservationCommand2, $this->db->_Commands));
		
		$this->assertTrue(in_array($removeReservationCommand1, $this->db->_Commands));
		$this->assertTrue(in_array($removeReservationCommand2, $this->db->_Commands));
	}
	
	public function testWithUpdatedInstances()
	{
		$seriesId = 3929;
		$dateRange = new TestDateRange();
		
		$instance1 = new TestReservation('323', $dateRange->AddDays(3));
		$instance2 = new TestReservation('423', $dateRange->AddDays(4));
		
		$builder = new ExistingReservationSeriesBuilder();
		$builder->WithEvent(new InstanceUpdatedEvent($instance1));
		$builder->WithEvent(new InstanceUpdatedEvent($instance2));
		$series = $builder->BuildTestVersion();
		$series->WithId($seriesId);
		$this->repository->Update($series);
		
		$updateReservationCommand1 = $this->GetUpdateReservationCommand($seriesId, $instance1);
		$updateReservationCommand2 = $this->GetUpdateReservationCommand($seriesId, $instance2);
			
		$commands = $this->db->GetCommandsOfType('UpdateReservationCommand');
		
		$this->assertEquals(2, count($commands));
		$this->assertTrue(in_array($updateReservationCommand1, $this->db->_Commands));
		$this->assertTrue(in_array($updateReservationCommand2, $this->db->_Commands));
	}
	
	private function GetUpdateReservationCommand($expectedSeriesId, Reservation $expectedInstance)
	{
		return new UpdateReservationCommand(
									$expectedInstance->ReferenceNumber(), 
									$expectedSeriesId,
									$expectedInstance->StartDate(),
									$expectedInstance->EndDate());
	}
	
	private function GetAddReservationCommand($expectedSeriesId, Reservation $expectedInstance)
	{
		return new AddReservationCommand(
								$expectedInstance->StartDate(), 
								$expectedInstance->EndDate(),
								$expectedInstance->ReferenceNumber(), 
								$expectedSeriesId);
	}
}


class ReservationRow
{
	private $row = array();
	
	public function Rows()
	{
		return array($this->row);
	}
	
	public function __construct(
		$reservationId, 
		$startDate, 
		$endDate,
		$title,
		$description,
		$repeatType,
		$repeatOptions,
		$referenceNumber,
		$scheduleId,
		$seriesId
		)
	{
		$this->row =  array(
			ColumnNames::RESERVATION_INSTANCE_ID => $reservationId,
			ColumnNames::RESERVATION_START => $startDate,
			ColumnNames::RESERVATION_END => $endDate,
			ColumnNames::RESERVATION_TITLE => $title,
			ColumnNames::RESERVATION_DESCRIPTION => $description,
			ColumnNames::RESERVATION_TYPE => ReservationTypes::Reservation,
			ColumnNames::REPEAT_TYPE => $repeatType,
			ColumnNames::REPEAT_OPTIONS => $repeatOptions,
			ColumnNames::REFERENCE_NUMBER => $referenceNumber,
			ColumnNames::SCHEDULE_ID => $scheduleId,
			ColumnNames::SERIES_ID => $seriesId
		);
	}
}

class ReservationInstanceRow
{
	private $seriesId;
	private $rows = array();
	
	public function Rows()
	{
		return $this->rows;
	}
	
	public function __construct($seriesId)
	{
		$this->seriesId = $seriesId;
	}
	
	/**
	 * @param int $instanceId
	 * @param string $referenceNum
	 * @param DateRange $duration
	 */
	public function WithInstance($instanceId, $referenceNum, $duration)
	{
		$this->rows[] = array(
			ColumnNames::SERIES_ID => $this->seriesId, 
			ColumnNames::RESERVATION_INSTANCE_ID => $instanceId, 
			ColumnNames::REFERENCE_NUMBER => $referenceNum, 
			ColumnNames::RESERVATION_START => $duration->GetBegin()->ToDatabase(),
			ColumnNames::RESERVATION_END => $duration->GetEnd()->ToDatabase(),
			);
		
		return $this;
	}
}

class ReservationResourceRow
{
	private $seriesId;
	private $rows = array();
	
	public function Rows()
	{
		return $this->rows;
	}
	
	public function __construct($seriesId)
	{
		$this->seriesId = $seriesId;
	}
	
	public function WithPrimary($resourceId)
	{
		$this->AddRow($resourceId, ResourceLevel::Primary);
		return $this;
	}
	
	public function WithAdditional($resourceId)
	{
		$this->AddRow($resourceId, ResourceLevel::Additional);
		return $this;
	}
	
	private function AddRow($resourceId, $levelId)
	{
		$this->rows[] = array(ColumnNames::SERIES_ID => $this->seriesId, ColumnNames::RESOURCE_ID => $resourceId, ColumnNames::RESOURCE_LEVEL_ID => $levelId);
	}
}

class ReservationUserRow
{
	private $seriesId;
	private $rows = array();
	
	public function Rows()
	{
		return $this->rows;
	}
	
	public function __construct($seriesId)
	{
		$this->seriesId = $seriesId;
	}
	
	public function WithOwner($userId)
	{
		$this->AddRow($userId, ReservationUserLevel::OWNER);
		return $this;
	}
	
	private function AddRow($userId, $levelId)
	{
		$this->rows[] = array(ColumnNames::SERIES_ID => $this->seriesId, ColumnNames::USER_ID => $userId, ColumnNames::RESERVATION_USER_LEVEL => $levelId);
	}
}
?>