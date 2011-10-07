<?php
require_once(ROOT_DIR . 'Presenters/SchedulePresenter.php');
require_once(ROOT_DIR . 'Pages/SchedulePage.php');

class SchedulePresenterTests extends TestBase
{
	/**
	 * @var SchedulePresenter
	 */
	private $presenter;
	private $page;
	private $reservationService;
	
	private $scheduleId;
	private $currentSchedule;
	private $schedules;
	private $numDaysVisible;
	private $showInaccessibleResources = 'true';

	public function setup()
	{
		parent::setup();

		$this->scheduleId = 1;
		$this->numDaysVisible = 10;
		$this->currentSchedule = new Schedule($this->scheduleId, 'default', 1, '08:30', '19:00', 1, 1, $this->numDaysVisible);
		$otherSchedule = new Schedule(2, 'not default', 0, '08:30', '19:00', 1, 1, $this->numDaysVisible);
		
		$this->schedules = array($this->currentSchedule, $otherSchedule);
		$this->fakeConfig->SetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_INACCESSIBLE_RESOURCES, $this->showInaccessibleResources);
	}

	public function teardown()
	{
		parent::teardown();
	}
	
	public function testPageLoadBindsAllSchedulesAndProperResourcesWhenNotPostingBack2()
	{
		$user = $this->fakeServer->GetUserSession();
		$userId = $user->UserId;
		$resources = array();
		$reservations = $this->getMock('IReservationListing');
		$layout = $this->getMock('IScheduleLayout');
		$bindingDates = new DateRange(Date::Now(), Date::Now());
		
		$page = $this->getMock('ISchedulePage');
		$scheduleRepository = $this->getMock('IScheduleRepository');
		$resourceService = $this->getMock('IResourceService');
		$pageBuilder = $this->getMock('ISchedulePageBuilder');
		$reservationService = $this->getMock('IReservationService');
		$dailyLayoutFactory = $this->getMock('IDailyLayoutFactory');
		$dailyLayout = $this->getMock('IDailyLayout');
				
		$presenter = new SchedulePresenter($page, $scheduleRepository, $resourceService, $pageBuilder, $reservationService, $dailyLayoutFactory);

		$scheduleRepository->expects($this->once())
			->method('GetAll')
			->will($this->returnValue($this->schedules));
		
		$scheduleRepository->expects($this->once())
			->method('GetLayout')
			->with($this->equalTo($this->scheduleId), $this->equalTo(new ScheduleLayoutFactory($user->Timezone)))
			->will($this->returnValue($layout));
		
		$pageBuilder->expects($this->once())
			->method('GetCurrentSchedule')
			->with($this->equalTo($page), $this->equalTo($this->schedules))
			->will($this->returnValue($this->currentSchedule));
		
		$pageBuilder->expects($this->once())
			->method('BindSchedules')
			->with($this->equalTo($page), $this->equalTo($this->schedules), $this->equalTo($this->currentSchedule));
			
		$resourceService->expects($this->once())
			->method('GetScheduleResources')
			->with($this->equalTo($this->scheduleId), $this->equalTo((bool)$this->showInaccessibleResources), $this->equalTo($user))
			->will($this->returnValue($resources));
		
		$pageBuilder->expects($this->once())
			->method('GetScheduleDates')
			->with($this->equalTo($user), $this->equalTo($this->currentSchedule), $this->equalTo($page))
			->will($this->returnValue($bindingDates));
			
		$pageBuilder->expects($this->once())
			->method('BindDisplayDates')
			->with($this->equalTo($page), $this->equalTo($bindingDates), $this->equalTo($user));
		
		$reservationService->expects($this->once())
			->method('GetReservations')
			->with($this->equalTo($bindingDates), $this->equalTo($this->scheduleId), $this->equalTo($user->Timezone))
			->will($this->returnValue($reservations));
		
		$pageBuilder->expects($this->once())
			->method('BindLayout')
			->with($this->equalTo($page), $this->equalTo($dailyLayout));
		
		$dailyLayoutFactory->expects($this->once())
			->method('Create')
			->with($this->equalTo($reservations), $this->equalTo($layout))
			->will($this->returnValue($dailyLayout));
		
		$pageBuilder->expects($this->once())
			->method('BindReservations')
			->with($this->equalTo($page), $this->equalTo($resources), $this->equalTo($dailyLayout));
			
		$presenter->PageLoad();
		
	}
	
	public function testScheduleBuilderBindsAllSchedulesAndSetsActive()
	{
		$activeId = 100;
		$activeName = 'super active';
		$weekdayStart = 4;
		
		$schedule = $this->getMock('ISchedule');
		$page = $this->getMock('ISchedulePage');
		
		$schedule->expects($this->once())
			->method('GetId')
			->will($this->returnValue($activeId));
			
		$schedule->expects($this->once())
			->method('GetName')
			->will($this->returnValue($activeName));
			
		$schedule->expects($this->once())
			->method('GetWeekdayStart')
			->will($this->returnValue($weekdayStart));
		
		$page->expects($this->once())
			->method('SetSchedules')
			->with($this->equalTo($this->schedules));
			
		$page->expects($this->once())
			->method('SetScheduleId')
			->with($this->equalTo($activeId));
			
		$page->expects($this->once())
			->method('SetScheduleName')
			->with($this->equalTo($activeName));
			
		$page->expects($this->once())
			->method('SetFirstWeekday')
			->with($this->equalTo($weekdayStart));
		
		$pageBuilder = new SchedulePageBuilder();
		$pageBuilder->BindSchedules($page, $this->schedules, $schedule);
	}
	
	public function testScheduleBuilderGetCurrentScheduleReturnsSelectedScheduleOnPostBack()
	{
		$s1 = $this->getMock('ISchedule');
		$s2 = $this->getMock('ISchedule');
		
		$s1->expects($this->once())
			->method('GetId')
			->will($this->returnValue(11));
			
		$s2->expects($this->once())
			->method('GetId')
			->will($this->returnValue(10));
		
		$schedules = array($s1, $s2);
				
		$page = $this->getMock('ISchedulePage');
		
		$page->expects($this->once())
			->method('IsPostBack')
			->will($this->returnValue(true));
			
		$page->expects($this->once())
			->method('GetScheduleId')
			->will($this->returnValue(10));
		
		$pageBuilder = new SchedulePageBuilder();
		$actual = $pageBuilder->GetCurrentSchedule($page, $schedules);
		
		$this->assertEquals($s2, $actual);
	}
	
	public function testScheduleBuilderGetCurrentScheduleReturnsDefaultScheduleWhenInitialLoad()
	{
		$s1 = $this->getMock('ISchedule');
		$s2 = $this->getMock('ISchedule');
		
		$s1->expects($this->once())
			->method('GetIsDefault')
			->will($this->returnValue(false));
			
		$s2->expects($this->once())
			->method('GetIsDefault')
			->will($this->returnValue(true));
		
		$schedules = array($s1, $s2);
		
		$page = $this->getMock('ISchedulePage');
		
		$page->expects($this->once())
			->method('IsPostBack')
			->will($this->returnValue(false));
		
		$pageBuilder = new SchedulePageBuilder();
		$actual = $pageBuilder->GetCurrentSchedule($page, $schedules);
		
		$this->assertEquals($s2, $actual);
	}

	public function testGetScheduleDatesStartsOnConfiguredDayOfWeekWhenStartDayIsPriorToToday()
	{
		$timezone = 'CST';
		
		// saturday
		$currentServerDate = Date::Create(2009, 07, 18, 11, 00, 00, 'CST');
		Date::_SetNow($currentServerDate);
		
		$startDay = 0;
		$daysVisible = 6;
		
		// previous sunday
		$expectedStart = Date::Create(2009, 07, 12, 00, 00, 00, $timezone);
		$expectedEnd = $expectedStart->AddDays($daysVisible);
		$expectedScheduleDates = new DateRange($expectedStart, $expectedEnd);
		
		$user = new UserSession(1);
		$user->Timezone = $timezone;
		$this->fakeConfig->SetTimezone('CST');
		
		$schedule = $this->getMock('ISchedule');
		$schedulePage = $this->getMock('ISchedulePage');
			
		$schedulePage->expects($this->once())
			->method('GetSelectedDate')
			->will($this->returnValue(null));	
			
		$schedule->expects($this->once())
			->method('GetWeekdayStart')
			->will($this->returnValue($startDay));	

		$schedule->expects($this->once())
			->method('GetDaysVisible')
			->will($this->returnValue($daysVisible));	
		
		$pageBuilder = new SchedulePageBuilder();
		$dates = $pageBuilder->GetScheduleDates($user, $schedule, $schedulePage);
		
		//echo $expectedScheduleDates->ToString();
		//echo $utcDates->ToString();
		
		$this->assertEquals($expectedScheduleDates, $dates);
	}
	
	public function testGetScheduleDatesStartsOnConfiguredDayOfWeekWhenStartDayIsAfterToday()
	{
		$timezone = 'CST';
		// tuesday
		$currentServerDate = Date::Create(2009, 07, 14, 11, 00, 00, 'CST');
		Date::_SetNow($currentServerDate);
		
		$startDay = 3;
		$daysVisible = 6;
		
		// previous wednesday
		$expectedStart = Date::Create(2009, 07, 8, 00, 00, 00, $timezone);
		$expectedEnd = $expectedStart->AddDays($daysVisible);
		$expectedScheduleDates = new DateRange($expectedStart, $expectedEnd);
		
		$user = new UserSession(1);
		$user->Timezone = $timezone;
		$this->fakeConfig->SetTimezone('CST');
		
		$schedule = $this->getMock('ISchedule');
		$schedulePage = $this->getMock('ISchedulePage');
			
		$schedulePage->expects($this->once())
			->method('GetSelectedDate')
			->will($this->returnValue(null));	
			
		$schedule->expects($this->once())
			->method('GetWeekdayStart')
			->will($this->returnValue($startDay));	

		$schedule->expects($this->once())
			->method('GetDaysVisible')
			->will($this->returnValue($daysVisible));	
		
		$pageBuilder = new SchedulePageBuilder();
		$dates = $pageBuilder->GetScheduleDates($user, $schedule, $schedulePage);
		
		//echo $expectedScheduleDates->ToString();
		//echo $utcDates->ToString();
		
		$this->assertEquals($expectedScheduleDates, $dates);
	}
	
	public function testCorrectDatesAreUsedWhenTheUsersTimezoneIsAheadByAWeek()
	{
		$timezone = 'EST';
		// saturday
		$currentServerDate = Date::Create(2009, 07, 18, 23, 00, 00, 'CST');
		Date::_SetNow($currentServerDate);
		
		$startDay = 0;
		$daysVisible = 5;
		
		// sunday of next week
		$expectedStart = Date::Create(2009, 07, 19, 00, 00, 00, $timezone);
		$expectedEnd = $expectedStart->AddDays($daysVisible);
		$expectedScheduleDates = new DateRange($expectedStart, $expectedEnd);
		
		$user = new UserSession(1);
		$user->Timezone = $timezone;
		$this->fakeConfig->SetTimezone('CST');
		
		$schedule = $this->getMock('ISchedule');
		$schedulePage = $this->getMock('ISchedulePage');
			
		$schedulePage->expects($this->once())
			->method('GetSelectedDate')
			->will($this->returnValue(null));	
			
		$schedule->expects($this->once())
			->method('GetWeekdayStart')
			->will($this->returnValue($startDay));	

		$schedule->expects($this->once())
			->method('GetDaysVisible')
			->will($this->returnValue($daysVisible));	
		
		$pageBuilder = new SchedulePageBuilder();
		$dates = $pageBuilder->GetScheduleDates($user, $schedule, $schedulePage);
		
		//echo $expectedScheduleDates->ToString();
		//echo $utcDates->ToString();
		
		$this->assertEquals($expectedScheduleDates, $dates);
	}
	
	public function testCorrectDatesAreUsedWhenTheUsersTimezoneIsBehindByAWeek()
	{
		$timezone = 'PST';
		// sunday
		$currentServerDate = Date::Create(2009, 07, 19, 00, 30, 00, 'CST');
		Date::_SetNow($currentServerDate);
		
		$startDay = 0;
		$daysVisible = 3;
		
		// previous sunday
		$expectedStart = Date::Create(2009, 07, 12, 00, 00, 00, $timezone);
		$expectedEnd = $expectedStart->AddDays($daysVisible);
		$expectedScheduleDates = new DateRange($expectedStart, $expectedEnd);
		
		$user = new UserSession(1);
		$user->Timezone = $timezone;
		$this->fakeConfig->SetTimezone('CST');
		
		$schedule = $this->getMock('ISchedule');
		$schedulePage = $this->getMock('ISchedulePage');
			
		$schedulePage->expects($this->once())
			->method('GetSelectedDate')
			->will($this->returnValue(null));	
			
		$schedule->expects($this->once())
			->method('GetWeekdayStart')
			->will($this->returnValue($startDay));	

		$schedule->expects($this->once())
			->method('GetDaysVisible')
			->will($this->returnValue($daysVisible));	
		
		$pageBuilder = new SchedulePageBuilder();
		$dates = $pageBuilder->GetScheduleDates($user, $schedule, $schedulePage);
		
		$this->assertEquals($expectedScheduleDates, $dates);
	}
	
	public function testProvidedStartDateIsUsedIfSpecified()
	{
		$timezone = 'CST';
		// saturday
		$selectedDate = Date::Create(2009, 07, 18, 00, 00, 00, 'CST');
		
		$startDay = 0;
		$daysVisible = 6;
		
		// previous sunday
		$expectedStart = Date::Create(2009, 07, 12, 00, 00, 00, $timezone);
		$expectedEnd = $expectedStart->AddDays($daysVisible);
		$expectedScheduleDates = new DateRange($expectedStart, $expectedEnd);
		
		$user = new UserSession(1);
		$user->Timezone = $timezone;
		$this->fakeConfig->SetTimezone('CST');
		
		$schedule = $this->getMock('ISchedule');
		$schedulePage = $this->getMock('ISchedulePage');
		
		$schedulePage->expects($this->once())
			->method('GetSelectedDate')
			->will($this->returnValue($selectedDate->Format("Y-m-d")));	
		
		$schedule->expects($this->once())
			->method('GetWeekdayStart')
			->will($this->returnValue($startDay));	

		$schedule->expects($this->once())
			->method('GetDaysVisible')
			->will($this->returnValue($daysVisible));	
		
		$pageBuilder = new SchedulePageBuilder();
		$dates = $pageBuilder->GetScheduleDates($user, $schedule, $schedulePage);
		
		//echo $expectedScheduleDates->ToString();
		//echo $utcDates->ToString();
		
		$this->assertEquals($expectedScheduleDates, $dates);
	}
	
	public function testBindReservationsSetsReservationsAndResourcesOnPage()
	{
		$page = $this->getMock('ISchedulePage');
		$dailyLayout = $this->getMock('IDailyLayout');
		$resources = array();
		
		$page->expects($this->once())
			->method('SetResources')
			->with($this->equalTo($resources));
			
		$page->expects($this->once())
			->method('SetDailyLayout')
			->with($this->equalTo($dailyLayout));
		
		$pageBuilder = new SchedulePageBuilder();
		$pageBuilder->BindReservations($page, $resources, $dailyLayout);
	}
	
	public function testBindDisplayDatesSetsPageToArrayOfDatesInUserTimezoneForRange()
	{
		$daysVisible = 7;
		$schedule = new Schedule(1, '', false, 4, $daysVisible);
		$user = new UserSession(1);
		$user->Timezone = 'EST';
		$page = $this->getMock('ISchedulePage');
		
		$start = Date::Now();
		$end = Date::Now()->AddDays(10);
		$displayRange = new DateRange($start, $end);
		$expectedRange = new DateRange($start->ToTimezone('EST'), $end->AddDays(-1)->ToTimezone('EST'));
		
		$expectedPrev = $expectedRange->GetBegin()->AddDays(-$daysVisible);
		$expectedNext = $expectedRange->GetBegin()->AddDays($daysVisible);
		
		$page->expects($this->once())
			->method('SetDisplayDates')
			->with($this->equalTo($expectedRange));
		
		$page->expects($this->once())
			->method('SetPreviousNextDates')
			->with($this->equalTo($expectedPrev), $this->equalTo($expectedNext));
			
		$pageBuilder = new SchedulePageBuilder();
		$pageBuilder->BindDisplayDates($page, $displayRange, $user, $schedule);
	}
	
	public function testBindLayoutSetsLayoutOnPage()
	{
		$start = Date::Now();
		$end = Date::Now()->AddDays(10);
		$displayRange = new DateRange($start, $end);
		
		$page = $this->getMock('ISchedulePage');
		$layout = $this->getMock('IDailyLayout');
		
		$periods = array();
		
		$layout->expects($this->once())
			->method('GetLabels')
			->with($start)
			->will($this->returnValue($periods));
			
		$page->expects($this->once())
			->method('SetLayout')
			->with($this->equalTo($periods));
		
		$pageBuilder = new SchedulePageBuilder();
		$pageBuilder->BindLayout($page, $layout, $displayRange);
	}
	
	public function testPreviousAndNextLinksWhenStartingOnMondayShowingFiveDays()
	{
		$tz = 'America/Chicago';
		$start = Date::Parse('2011-04-04', $tz);
		$end = Date::Parse('2011-04-08', $tz);
		
		$session = new UserSession(1);
		$session->Timezone = $tz;
		
		$schedule = new Schedule(1, null, true, 1, 5);
		
		$expectedPrevious = Date::Parse('2011-03-28', $tz);
		$expectedNext = Date::Parse('2011-04-11', $tz);
		
		$page = $this->getMock('ISchedulePage');
		$page->expects($this->once())
			->method('SetPreviousNextDates')
			->with($this->equalTo($expectedPrevious), $this->equalTo($expectedNext));
			
		$builder = new SchedulePageBuilder();
		$builder->BindDisplayDates($page, new DateRange($start, $end), $session, $schedule);
	}
	
	public function testPreviousAndNextLinksWhenStartingOnMondayShowingTenDays()
	{
		$tz = 'America/Chicago';
		$start = Date::Parse('2011-04-04', $tz);
		$end = Date::Parse('2011-04-13', $tz);
		
		$session = new UserSession(1);
		$session->Timezone = $tz;
		
		$schedule = new Schedule(1, null, true, 1, 10);
		
		$expectedPrevious = Date::Parse('2011-03-28', $tz);
		$expectedNext = Date::Parse('2011-04-14', $tz);
		
		$page = $this->getMock('ISchedulePage');
		$page->expects($this->once())
			->method('SetPreviousNextDates')
			->with($this->equalTo($expectedPrevious), $this->equalTo($expectedNext));
			
		$builder = new SchedulePageBuilder();
		$builder->BindDisplayDates($page, new DateRange($start, $end), $session, $schedule);
	}
}

?>