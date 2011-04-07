<?php
require_once(ROOT_DIR . 'Presenters/Admin/ManageSchedulesPresenter.php');
require_once(ROOT_DIR . 'Pages/Admin/ManageSchedulesPage.php');

class ManageSchedulesPresenterTests extends TestBase
{
	/**
	 * @var IUpdateSchedulePage
	 */
	private $page;
	
	/**
	 * @var IScheduleRepository
	 */
	private $scheduleRepo;
	
	public function setup()
	{
		parent::setup();
		
		$this->page = $this->getMock('IManageSchedulesPage');
		$this->scheduleRepo = $this->getMock('IScheduleRepository');
	}
	
	public function teardown()
	{
		parent::teardown();
	}
	
	public function testLayoutIsParsedFromPage()
	{
		$scheduleId = 98;
		$timezone = 'America/Chicago';
		$reservableSlots = '00:00 - 01:00 Label 1 A\n1:00- 2:00\r\n02:00 -3:30\n03:30-12:00\r\n';
		$blockedSlots = '00:00 - 01:00 Label 1 A\n1:00- 2:00\r\n02:00 -3:30\n03:30-12:00\r\n';
		
		$expectedLayout = ScheduleLayout::Parse($timezone, $reservableSlots, $blockedSlots);
		
		$this->page->expects($this->once())
			->method('GetScheduleId')
			->will($this->returnValue($scheduleId));
			
		$this->page->expects($this->once())
			->method('GetLayoutTimezone')
			->will($this->returnValue($timezone));
			
		$this->page->expects($this->once())
			->method('GetReservableSlots')
			->will($this->returnValue($reservableSlots));
			
		$this->page->expects($this->once())
			->method('GetBlockedSlots')
			->will($this->returnValue($blockedSlots));
			
		$this->scheduleRepo->expects($this->once())
			->method('AddScheduleLayout')
			->with($this->equalTo($scheduleId), $this->equalTo($expectedLayout));
			
		$presenter = new ManageSchedulesPresenter($this->page, $this->scheduleRepo);
		$presenter->ChangeLayout();
	}
	
	public function testNewScheduleIsAdded()
	{
		$sourceScheduleId = 198;
		$name = 'new name';
		$startDay = '3';
		$daysVisible = '7';
		
		$expectedSchedule = new Schedule(null, $name, false, $startDay, $daysVisible);
		
		$this->page->expects($this->once())
			->method('GetSourceScheduleId')
			->will($this->returnValue($sourceScheduleId));
			
		$this->page->expects($this->once())
			->method('GetScheduleName')
			->will($this->returnValue($name));
			
		$this->page->expects($this->once())
			->method('GetStartDay')
			->will($this->returnValue($startDay));
			
		$this->page->expects($this->once())
			->method('GetDaysVisible')
			->will($this->returnValue($daysVisible));
			
		$this->scheduleRepo->expects($this->once())
			->method('Add')
			->with($this->equalTo($expectedSchedule), $this->equalTo($sourceScheduleId));
			
		$presenter = new ManageSchedulesPresenter($this->page, $this->scheduleRepo);
		$presenter->Add();
	}
}
?>