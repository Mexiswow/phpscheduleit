<?php 
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Server/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Reservation/namespace.php');
require_once(ROOT_DIR . 'lib/Reservation/Persistence/namespace.php');
require_once(ROOT_DIR . 'lib/Reservation/Validation/namespace.php');
require_once(ROOT_DIR . 'lib/Reservation/Notification/namespace.php');
require_once(ROOT_DIR . 'lib/Domain/namespace.php');
require_once(ROOT_DIR . 'lib/Domain/Access/namespace.php');

class ReservationSavePresenter
{
	/**
	 * @var IReservationSavePage
	 */
	private $_page;
	
	/**
	 * @var IReservationPersistenceFactory
	 */
	private $_persistenceFactory;
	
	/**
	 * @var IReservationValidationFactory
	 */
	private $_validationFactory;
	
	/**
	 * @var IReservationNotificationFactory
	 */
	private $_notificationFactory;
	
	public function __construct(
		IReservationSavePage $page, 
		IReservationPersistenceFactory $persistenceFactory,
		IReservationValidationFactory $validationFactory,
		IReservationNotificationFactory $notificationFactory)
	{
		$this->_page = $page;
		$this->_persistenceFactory = $persistenceFactory;
		$this->_validationFactory = $validationFactory;
		$this->_notificationFactory = $notificationFactory;
	}
	
	public function BuildReservation()
	{
//		$reservation->AddAccessory();
//		$reservation->AddParticipant();
//		
//		$reservation->RemoveResource();
//		$reservation->RemoveAccessory();
//		$reservation->RemoveParticipant();

		$action = $this->_page->GetReservationAction();
		
		$reservationId = $this->_page->GetReservationId();	
		$persistenceService = $this->_persistenceFactory->Create($action);
		$reservation = $persistenceService->Load($reservationId);
		
		// accessories?, participants, invitations
		// reminder

		$userId = $this->_page->GetUserId();
		$resourceId = $this->_page->GetResourceId();
		$scheduleId = $this->_page->GetScheduleId();
		$title = $this->_page->GetTitle();
		$description = $this->_page->GetDescription();

		$reservation->Update(
			$userId, 
			$resourceId, 
			$scheduleId,
			$title,
			$description);
		
		$duration = $this->GetReservationDuration();
		$reservation->UpdateDuration($duration);
		
		$repeatOptions = $this->_page->GetRepeatOptions($duration);
		$reservation->Repeats($repeatOptions);
		
		$resourceIds = $this->_page->GetResources();
		foreach ($resourceIds as $resourceId)
		{
			$reservation->AddResource($resourceId);
		}
		
		return $reservation;
	}
	
	/**
	 * @param Reservation $reservation
	 */
	public function HandleReservation($reservation)
	{		
		$action = $this->_page->GetReservationAction();
		
		$validationService = $this->_validationFactory->Create($action);
		$validationResult = $validationService->Validate($reservation);
		
		if ($validationResult->CanBeSaved())
		{
			$persistenceService = $this->_persistenceFactory->Create($action);
			$persistenceService->Persist($reservation);
			
			$notificationService = $this->_notificationFactory->Create($action);
			$notificationService->Notify($reservation);
			
			$this->_page->SetReferenceNumber($reservation->ReferenceNumber());
			$this->_page->SetSaveSuccessfulMessage(true);
		}
		else
		{
			$this->_page->SetSaveSuccessfulMessage(false);
			$this->_page->ShowErrors($validationResult->GetErrors());
		}
		
		$this->_page->ShowWarnings($validationResult->GetWarnings());
	}
	
	/**
	 * @return Reservation
	 */
	private function GetReservation($action)
	{
		
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
	
	/**
	 * @param DateRange $initialReservationDates
	 * @return IRepeatOptions
	 */
	public function GetRepeatOptions($initialReservationDates)
	{
		$factory = new RepeatOptionsFactory();
		
		$repeatType = $this->_page->GetRepeatType();
		$interval = $this->_page->GetRepeatInterval();
		$weekdays = $this->_page->GetRepeatWeekdays();
		$monthlyType = $this->_page->GetRepeatMonthlyType();
		$terminationDate = Date::Parse($this->_page->GetRepeatTerminationDate(), $initialReservationDates->GetBegin()->Timezone());
		
		return $factory->Create($repeatType, $interval, $terminationDate, $initialReservationDates, $weekdays, $monthlyType);
	}
}
?>