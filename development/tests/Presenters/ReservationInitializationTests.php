<?php 
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');

require_once(ROOT_DIR . 'Pages/ReservationPage.php');
require_once(ROOT_DIR . 'Pages/NewReservationPage.php');

require_once(ROOT_DIR . 'lib/Application/Reservation/NewReservationInitializer.php');

class ReservationInitializationTests extends TestBase
{
	/**
	 * @var UserSession
	 */
	private $_user;

	/**
	 * @var int
	 */
	private $_userId;
	
	/**
	 * @var IScheduleRepository|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_scheduleRepository;
	
	/**
	 * @var IScheduleUserRepository|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_scheduleUserRepository;

	/**
	 * @var IUserRepository|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_userRepository;
	
	public function setup()
	{
		parent::setup();

		$this->_user = $this->fakeServer->UserSession;
		$this->_userId = $this->_user->UserId;

		$this->_scheduleRepository = $this->getMock('IScheduleRepository');
		$this->_scheduleUserRepository = $this->getMock('IScheduleUserRepository');
		$this->_userRepository = $this->getMock('IUserRepository');
	}

	public function teardown()
	{
		parent::teardown();
	}
	
	public function testInitializeSetsDefaultsForSelectedUserAndResourceAndDate()
	{
		$timezone = $this->_user->Timezone;

		$resourceId = 10;
		$scheduleId = 100;
		$dateString = Date::Now()->AddDays(1)->SetTimeString('02:55:22')->Format('Y-m-d H:i:s');
		$dateInUserTimezone = Date::Parse($dateString, $timezone);

		$firstName = 'fname';
		$lastName = 'lastName';

		$expectedPeriod = new SchedulePeriod(
			$dateInUserTimezone->SetTime(new Time(3, 30, 0)), 
			$dateInUserTimezone->SetTime(new Time(4, 30, 0)));
		
		$startDate = Date::Parse($dateString, $timezone);
		$endDate = Date::Parse($dateString, $timezone);
			
		$page = $this->getMock('INewReservationPage');

		$page->expects($this->once())
			->method('GetRequestedResourceId')
			->will($this->returnValue($resourceId));
			
		$page->expects($this->once())
			->method('GetRequestedScheduleId')
			->will($this->returnValue($scheduleId));

		$page->expects($this->any())
			->method('GetReservationDate')
			->will($this->returnValue($dateInUserTimezone));
			
		$page->expects($this->any())
			->method('GetStartDate')
			->will($this->returnValue($startDate));
		
		$page->expects($this->any())
			->method('GetEndDate')
			->will($this->returnValue($endDate));
			
		// DATA
		// users
		$userDto = new UserDto($this->_userId, $firstName, $lastName, 'email');

		$this->_userRepository->expects($this->once())
			->method('GetById')
			->with($this->_userId)
			->will($this->returnValue($userDto));
			
		// resources
		$schedResource = new ScheduleResource($resourceId, 'resource 1');
		$otherResource = new ScheduleResource(2, 'resource 2');
		$resourceList = array($otherResource, $schedResource);
		$scheduleUser = $this->getMock('IScheduleUser');
		$scheduleUser->expects($this->once())
			->method('IsGroupAdmin')
			->will($this->returnValue(true));

		$this->_scheduleUserRepository->expects($this->once())
			->method('GetUser')
			->with($this->equalTo($this->_userId))
			->will($this->returnValue($scheduleUser));
			
		$scheduleUser->expects($this->once())
			->method('GetAllResources')
			->will($this->returnValue($resourceList));
			
		// periods
		$periods = array(
			new SchedulePeriod($dateInUserTimezone->SetTime(new Time(1, 0, 0)), $dateInUserTimezone->SetTime(new Time(2, 0, 0))),
			new SchedulePeriod($dateInUserTimezone->SetTime(new Time(2, 0, 0)), $dateInUserTimezone->SetTime(new Time(3, 0, 0))),
			new NonSchedulePeriod($dateInUserTimezone->SetTime(new Time(3, 0, 0)), $dateInUserTimezone->SetTime(new Time(3, 30, 0))),
			$expectedPeriod,
			new SchedulePeriod($dateInUserTimezone->SetTime(new Time(4, 30, 0)), $dateInUserTimezone->SetTime(new Time(7, 30, 0))),
			new SchedulePeriod($dateInUserTimezone->SetTime(new Time(7, 30, 0)), $dateInUserTimezone->SetTime(new Time(17, 30, 0))),
			new SchedulePeriod($dateInUserTimezone->SetTime(new Time(17, 30, 0)), $dateInUserTimezone->SetTime(new Time(0, 0, 0))),
		);
		$layout = $this->getMock('IScheduleLayout');

		$this->_scheduleRepository->expects($this->once())
			->method('GetLayout')
			->with($this->equalTo($scheduleId), $this->equalTo(new ReservationLayoutFactory($this->_user->Timezone)))
			->will($this->returnValue($layout));
			
		$layout->expects($this->once())
			->method('GetLayout')
			->with($this->equalTo($dateInUserTimezone))
			->will($this->returnValue($periods));

		// BINDING
		$page->expects($this->once())
			->method('BindPeriods')
			->with($this->equalTo($periods));

		$resourceListWithoutReservationResource = array($otherResource);
		$page->expects($this->once())
			->method('BindAvailableResources')
			->with($this->equalTo($resourceListWithoutReservationResource));
			
		// SETUP
		$page->expects($this->once())
			->method('SetSelectedStart')
			->with($this->equalTo($expectedPeriod->BeginDate()));
		
		$page->expects($this->once())
			->method('SetSelectedEnd')
			->with($this->equalTo($expectedPeriod->EndDate()));
		
		$page->expects($this->once())
			->method('SetRepeatTerminationDate')
			->with($this->equalTo($expectedPeriod->EndDate()));

		$page->expects($this->once())
			->method('SetReservationUser')
			->with($this->equalTo($userDto));

		$page->expects($this->once())
			->method('SetReservationResource')
			->with($this->equalTo($schedResource));	// may want this to be a real object

		$page->expects($this->once())
			->method('SetCanChangeUser')
			->with($this->equalTo(true));
		
		$initializer = new NewReservationInitializer($page, $this->_scheduleUserRepository, $this->_scheduleRepository, $this->_userRepository);

		$initializer->Initialize();
	}
}
?>