<?php
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

class ScheduleRepositoryTests extends TestBase
{
	private $scheduleRepository;
	
	public function setup()
	{
		parent::setup();
		
		$this->scheduleRepository = new ScheduleRepository();
	}
	
	public function teardown()
	{
		parent::teardown();
		
		$this->scheduleRepository = null;
	}
	
	public function testCanGetAllSchedules()
	{
		$fakeSchedules = new FakeScheduleRepository();
				
		$rows = $fakeSchedules->GetRows();
		$this->db->SetRow(0, $rows);
		
		$expected = array();
		foreach ($rows as $item)
		{
			$expected[] = new Schedule(
							$item[ColumnNames::SCHEDULE_ID],
							$item[ColumnNames::SCHEDULE_NAME],
							$item[ColumnNames::SCHEDULE_DEFAULT],
							$item[ColumnNames::SCHEDULE_WEEKDAY_START],
							$item[ColumnNames::SCHEDULE_DAYS_VISIBLE],
							$item[ColumnNames::TIMEZONE_NAME]
						);
		}
		
		$actualSchedules = $this->scheduleRepository->GetAll();
		
		$this->assertEquals(new GetAllSchedulesCommand(), $this->db->_Commands[0]);
		$this->assertTrue($this->db->GetReader(0)->_FreeCalled);
		$this->assertEquals($expected, $actualSchedules);
	}
	
	public function testCanGetLayoutForSchedule()
	{
		$timezone = 'America/New_York';
		
		$layoutRows[] = array(
						ColumnNames::BLOCK_START => '02:00:00',
						ColumnNames::BLOCK_END => '03:00:00',
						ColumnNames::BLOCK_LABEL => 'PERIOD1',
						//ColumnNames::BLOCK_LABEL_END => 'END PERIOD1',
						ColumnNames::BLOCK_CODE => PeriodTypes::RESERVABLE,
						ColumnNames::BLOCK_TIMEZONE => $timezone,
						);
		
		$layoutRows[] = array(
						ColumnNames::BLOCK_START => '03:00:00',
						ColumnNames::BLOCK_END => '04:00:00',
						ColumnNames::BLOCK_LABEL => 'PERIOD2',
						//ColumnNames::BLOCK_LABEL_END => 'END PERIOD2',
						ColumnNames::BLOCK_CODE => PeriodTypes::RESERVABLE,
						ColumnNames::BLOCK_TIMEZONE => $timezone,
						);
						
		$layoutRows[] = array(
						ColumnNames::BLOCK_START => '04:00:00',
						ColumnNames::BLOCK_END => '05:00:00',
						ColumnNames::BLOCK_LABEL => 'PERIOD3',
						//ColumnNames::BLOCK_LABEL_END => 'END PERIOD3',
						ColumnNames::BLOCK_CODE => PeriodTypes::NONRESERVABLE,
						ColumnNames::BLOCK_TIMEZONE => $timezone,
						);
		
		$this->db->SetRows($layoutRows);
		
		$scheduleId = 109;
		$targetTimezone = 'America/Chicago';
		
		$layoutFactory = $this->getMock('ILayoutFactory');
		$expectedLayout = new ScheduleLayout($timezone);
		
		$layoutFactory->expects($this->once())
			->method('CreateLayout')
			->will($this->returnValue($expectedLayout));
		
		$layout = $this->scheduleRepository->GetLayout($scheduleId, $layoutFactory);
		
		$this->assertEquals(new GetLayoutCommand($scheduleId), $this->db->_Commands[0]);
		$this->assertTrue($this->db->GetReader(0)->_FreeCalled);
		
		$layoutDate = Date::Parse("2010-01-01", $timezone);
		$periods = $layout->GetLayout($layoutDate);
		$this->assertEquals(3, count($periods));
		
		$start = $layoutDate->SetTime(new Time(2,0,0));
		$end = $layoutDate->SetTime(new Time(3,0,0));
		
		$period = new SchedulePeriod($start, $end, 'PERIOD1');
		$this->assertEquals($period, $periods[0]);
	}
	
	public function testCanGetScheduleById()
	{
		$id = 10;
		$name = 'super schedule';
		$isDefault = 0;
		$weekdayStart = 5;
		$daysVisible = 3;
		$timezone = 'America/Chicago';
		
		$fakeSchedules = new FakeScheduleRepository();
		$expectedSchedule = new Schedule($id, 
									$name, 
									$isDefault, 
									$weekdayStart, 
									$daysVisible,
									$timezone);
									
		$this->db->SetRows(array($fakeSchedules->GetRow($id, $name, $isDefault, $weekdayStart, $daysVisible, $timezone)));
		$actualSchedule = $this->scheduleRepository->LoadById($id);
		
		$this->assertEquals($expectedSchedule, $actualSchedule);
		$this->assertEquals(new GetScheduleByIdCommand($id), $this->db->_LastCommand);
	}
	
	public function testCanUpdateSchedule()
	{
		$id = 10;
		$name = 'super schedule';
		$isDefault = 0;
		$weekdayStart = 5;
		$daysVisible = 3;
		
		$schedule = new Schedule($id, 
								$name, 
								$isDefault, 
								$weekdayStart, 
								$daysVisible);
									
		$actualSchedule = $this->scheduleRepository->Update($schedule);
		
		$this->assertEquals(new UpdateScheduleCommand($id, $name, $isDefault, $weekdayStart, $daysVisible), $this->db->_LastCommand);
	}
}

?>