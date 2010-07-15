<?php 
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Server/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
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
	
	public function PageLoad()
	{
		$action = ReservationAction::Create;
		$reservationId = $this->_page->GetReservationId();
			
		$persistenceService = $this->_persistenceFactory->Create($action);
		$reservation = $persistenceService->Load($reservationId);

		// user, resource, start, end, repeat options, title, description
		// additional resources, accessories, participants, invitations
		// reminder

		$userId = $this->_page->GetUserId();
		$resourceId = $this->_page->GetResourceId();
		$title = $this->_page->GetTitle();
		$description = $this->_page->GetDescription();
		
		$reservation->Update(
			$userId, 
			$resourceId, 
			$title,
			$description);
		
		$startDate = $this->_page->GetStartDate();
		$startTime = $this->_page->GetStartTime();
		$endDate = $this->_page->GetEndDate();
		$endTime = $this->_page->GetEndTime();
		
		$timezone = ServiceLocator::GetServer()->GetUserSession()->Timezone;
		$duration = DateRange::Create($startDate . ' ' . $startTime, $endDate . ' ' . $endTime, $timezone);
		$reservation->UpdateDuration($duration);
		
		$repeatOptions = $this->_page->GetRepeatOptions($duration);
		$reservation->Repeats($repeatOptions);
//		
//		$reservation->AddResource();
//		$reservation->AddAccessory();
//		$reservation->AddParticipant();
//		
//		$reservation->RemoveResource();
//		$reservation->RemoveAccessory();
//		$reservation->RemoveParticipant();
		
		$validationService = $this->_validationFactory->Create($action);
		$validationResult = $validationService->Validate($reservation);
		
		if ($validationResult->CanBeSaved())
		{
			$persistenceService->Persist($reservation);
			
			$notificationService = $this->_notificationFactory->Create($action);
			$notificationService->Notify($reservation);
			
			$this->_page->SetSaveSuccessfulMessage(true);
		}
		else
		{
			//TODO 
		}
		
		$this->_page->ShowWarnings($validationResult->GetWarnings());
	}
	
	/**
	 * @param DateRange $intialReservationDates
	 * @return IRepeatOptions
	 */
	public function GetRepeatOptions($intialReservationDates)
	{
		$factory = new RepeatOptionsFactory();
		
		$repeatType = $this->_page->GetRepeatType();
		$interval = $this->_page->GetRepeatInterval();
		$weekdays = $this->_page->GetRepeatWeekdays();
		$monthlyType = $this->_page->GetRepeatMonthlyType();
		$terminationDate = Date::Parse($this->_page->GetRepeatTerminationDate(), $intialReservationDates->Timezone());
		
		return $factory->Create($repeatType, $interval, $terminationDate, $initialReservationDates, $weekdays, $monthlyType);
	}
}

interface IReservationPersistenceFactory
{
	/**
	 * @param ReservationAction $reservationAction
	 * @return IReservationPersistenceService
	 */
	function Create($reservationAction);
}

class ReservationPersistenceFactory implements IReservationPersistenceFactory 
{
	public function Create($reservationAction)
	{
		return new AddReservationPersistenceService(new ReservationRepository());
	}
}

interface IReservationPersistenceService
{
	/**
	 * @return Reservation
	 */
	function Load($reservationId);

	function Persist($reservation);
}

class AddReservationPersistenceService implements IReservationPersistenceService 
{
	/**
	 * @var IReservationRepository
	 */
	private $_repository;
	
	public function __construct(IReservationRepository $repository)
	{
		$this->_repository = $repository;
	}
	
	public function Load($reservationId)
	{
		return new Reservation();
	}
	
	public function Persist($reservation)
	{
		$this->_repository->Add($reservation);
	}
}

interface IReservationValidationFactory
{
	/**
	 * @param ReservationAction $reservationAction
	 * @return IReservationValidationService
	 */
	function Create($reservationAction);
}

class ReservationValidationFactory implements IReservationValidationFactory
{
	public function Create($reservationAction)
	{
		return new AddReservationValidationService();
	}
}

interface IReservationValidationService
{
	/**
	 * @param $reservation
	 * @return IReservationValidationResult
	 */
	function Validate($reservation);
}

class AddReservationValidationService implements IReservationValidationService
{
	public function Validate($reservation)
	{
		return new ReservationValidResult();
		throw new Exception('not implemented');
	}
}

interface IReservationNotificationFactory
{
	/**
	 * @param ReservationAction $reservationAction
	 * @return IReservationNotificationService
	 */
	function Create($reservationAction);
}

class ReservationNotificationFactory implements IReservationNotificationFactory
{
	public function Create($reservationAction)
	{
		return new AddReservationNotificationService();
	}
}

interface IReservationNotificationService
{
	/**
	 * @param $reservation
	 */
	function Notify($reservation);
}

class AddReservationNotificationService implements IReservationNotificationService 
{
	public function Notify($reservation)
	{
		throw new Exception('not impelemented');
	}
}

interface IReservationValidationResult
{
	/**
	 * @return bool
	 */
	public function CanBeSaved();
	
	/**
	 * @return array[int]string
	 */
	public function GetErrors();
	
	/**
	 * @return array[int]string
	 */
	public function GetWarnings(); 
}

class ReservationAction
{
	const Create = 'create';
}

class ReservationValidResult implements IReservationValidationResult
{
	private $_canBeSaved;
	private $_errors;
	private $_warnings;
	
	public function __construct($canBeSaved = true, $errors = null, $warnings = null)
	{
		$this->_canBeSaved = $canBeSaved;
		$this->_errors = $errors == null ? array() : $errors;
		$this->_warnings = $warnings == null ? array() : $warnings;
	}
	
	public function CanBeSaved()
	{
		return $this->_canBeSaved;
	}
	
	public function GetErrors()
	{
		return $this->_errors;
	}
	
	public function GetWarnings()
	{
		return $this->_warnings;
	}
}
?>