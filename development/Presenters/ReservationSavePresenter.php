<?php 
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Server/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/Persistence/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/Validation/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/Notification/namespace.php');
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ReservationHandler.php');

class ReservationSavePresenter
{
	/**
	 * @var IReservationSavePage
	 */
	private $_page;
	
	/**
	 * @var IReservationPersistenceService
	 */
	private $_persistenceService;
	
	/**
	 * @var IReservationValidationService
	 */
	private $_validationService;
	
	/**
	 * @var IReservationNotificationService
	 */
	private $_notificationService;

	/**
	 * @var IResourceRepository
	 */
	private $_resourceRepository;
	
	public function __construct(
		IReservationSavePage $page, 
		IReservationPersistenceService $persistenceService,
		IReservationValidationService $validationService,
		IReservationNotificationService $notificationService,
		IResourceRepository $resourceRepository)
	{
		$this->_page = $page;
		$this->_persistenceService = $persistenceService;
		$this->_validationService = $validationService;
		$this->_notificationService = $notificationService;
		$this->_resourceRepository = $resourceRepository;
	}
	
	public function BuildReservation()
	{
		// accessories
		// reminder
		
		$userId = $this->_page->GetUserId();
		$resource = $this->_resourceRepository->LoadById($this->_page->GetResourceId());
		$scheduleId = $this->_page->GetScheduleId();
		$title = $this->_page->GetTitle();
		$description = $this->_page->GetDescription();
		$repeatOptions = $this->_page->GetRepeatOptions();
		$duration = $this->GetReservationDuration();
		
		$reservationSeries = ReservationSeries::Create($userId, $resource, $scheduleId, $title, $description, $duration, $repeatOptions, ServiceLocator::GetServer()->GetUserSession());
		
		$resourceIds = $this->_page->GetResources();
		
		foreach ($resourceIds as $resourceId)
		{
			$reservationSeries->AddResource($this->_resourceRepository->LoadById($resourceId));
		}

		$participantIds = $this->_page->GetParticipants();
		$reservationSeries->ChangeParticipants($participantIds);

		$inviteeIds = $this->_page->GetInvitees();
		$reservationSeries->ChangeInvitees($inviteeIds);

		return $reservationSeries;
	}
	
	/**
	 * @param ReservationSeries $reservationSeries
	 */
	public function HandleReservation($reservationSeries)
	{		
		$handler = new ReservationHandler();
		
		$successfullySaved = $handler->Handle(
					$reservationSeries,
					$this->_page, 
					$this->_persistenceService, 
					$this->_validationService, 
					$this->_notificationService);
			
		if ($successfullySaved)
		{
			$this->_page->SetReferenceNumber($reservationSeries->CurrentInstance()->ReferenceNumber());
		}
	}
	
	/**
	 * @return DateRange
	 */
	private function GetReservationDuration()
	{
		$startDate = $this->_page->GetStartDate();
		$startTime = $this->_page->GetStartTime();
		$endDate = $this->_page->GetEndDate();
		$endTime = $this->_page->GetEndTime();
		
		$timezone = ServiceLocator::GetServer()->GetUserSession()->Timezone;
		return DateRange::Create($startDate . ' ' . $startTime, $endDate . ' ' . $endTime, $timezone);
	}
	
	/**s
	 * @return IRepeatOptions
	 */
	public function GetRepeatOptions()
	{
		$timezone = ServiceLocator::GetServer()->GetUserSession()->Timezone;
		$factory = new RepeatOptionsFactory();
		
		$repeatType = $this->_page->GetRepeatType();
		$interval = $this->_page->GetRepeatInterval();
		$weekdays = $this->_page->GetRepeatWeekdays();
		$monthlyType = $this->_page->GetRepeatMonthlyType();
		$terminationDate = Date::Parse($this->_page->GetRepeatTerminationDate(), $timezone);
		
		return $factory->Create($repeatType, $interval, $terminationDate, $weekdays, $monthlyType);
	}
}
?>