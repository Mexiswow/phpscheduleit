<?php 
require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/Ajax/IReservationSaveResultsPage.php');
require_once(ROOT_DIR . 'Presenters/ReservationSavePresenter.php');

interface IReservationSavePage extends IReservationSaveResultsPage
{
	public function GetUserId();
	public function GetResourceId();
	public function GetScheduleId();
	
	/**
	 * @return string
	 */
	public function GetTitle();
	
	/**
	 * @return string
	 */
	public function GetDescription();
	
	/**
	 * @return string
	 */
	public function GetStartDate();
	
	/**
	 * @return string
	 */
	public function GetEndDate();
	
	/**
	 * @return string
	 */
	public function GetStartTime();
	
	/**
	 * @return string
	 */
	public function GetEndTime();
	
	/**
	 * @return int[]
	 */
	public function GetResources();
	
	/**
	 * @return string
	 */
	public function GetRepeatType();
	public function GetRepeatInterval();
	public function GetRepeatWeekdays();
	public function GetRepeatMonthlyType();
	public function GetRepeatTerminationDate();
	
	/**
	 * @return IRepeatOptions
	 */
	public function GetRepeatOptions();
	
	/**
	 * @param string $referenceNumber
	 */
	public function SetReferenceNumber($referenceNumber);
}

class ReservationSavePage extends SecurePage implements IReservationSavePage
{
	/**
	 * @var ReservationSavePresenter
	 */
	private $_presenter;
	
	/**
	 * @var bool
	 */
	private $_reservationSavedSuccessfully = false;
	
	public function __construct()
	{
		parent::__construct();
		
		$persistenceFactory = new ReservationPersistenceFactory();
		$validationFactory = new ReservationValidationFactory();
		$notificationFactory = new ReservationNotificationFactory();

		$updateAction = ReservationAction::Create;
		
		$userSession = ServiceLocator::GetServer()->GetUserSession();
		$this->_presenter = new ReservationSavePresenter(
														$this, 
														$persistenceFactory->Create($updateAction),
														$validationFactory->Create($updateAction, $userSession),
														$notificationFactory->Create($updateAction)
														);
	}
	
	public function PageLoad()
	{
		$reservation = $this->_presenter->BuildReservation();
		$this->_presenter->HandleReservation($reservation);

		if ($this->_reservationSavedSuccessfully)
		{
			$this->smarty->display('Ajax/reservation/save_successful.tpl');
		}
		else
		{
			$this->smarty->display('Ajax/reservation/save_failed.tpl');
		}
	}
	
	public function SetSaveSuccessfulMessage($succeeded)
	{
		$this->_reservationSavedSuccessfully = $succeeded;
	}
	
	public function SetReferenceNumber($referenceNumber)
	{
		$this->Set('ReferenceNumber', $referenceNumber);
	}
	
	public function ShowErrors($errors)
	{
		$this->Set('Errors', $errors);
	}
	
	public function ShowWarnings($warnings)
	{
		// set warnings variable
	}
	
	public function GetReservationAction()
	{
		return $this->GetForm(FormKeys::RESERVATION_ACTION);
	}
	
	public function GetReservationId()
	{
		return $this->GetForm(FormKeys::RESERVATION_ID);
	}
	
	public function GetUserId()
	{
		return $this->GetForm(FormKeys::USER_ID);
	}
	
	public function GetResourceId()
	{
		return $this->GetForm(FormKeys::RESOURCE_ID);
	}
	
	public function GetScheduleId()
	{
		return $this->GetForm(FormKeys::SCHEDULE_ID);
	}
	
	public function GetTitle()
	{
		return $this->GetForm(FormKeys::RESERVATION_TITLE);
	}
	
	public function GetDescription()
	{
		return $this->GetForm(FormKeys::DESCRIPTION);
	}
	
	public function GetStartDate()
	{
		return $this->GetForm(FormKeys::BEGIN_DATE);
	}
	
	public function GetEndDate()
	{
		return $this->GetForm(FormKeys::END_DATE);
	}
	
	public function GetStartTime()
	{
		return $this->GetForm(FormKeys::BEGIN_PERIOD);
	}
	
	public function GetEndTime()
	{
		return $this->GetForm(FormKeys::END_PERIOD);
	}
	
	public function GetResources()
	{
		$resources =  $this->GetForm(FormKeys::ADDITIONAL_RESOURCES);		
		if (is_null($resources))
		{
			return array();
		}
		
		if (!is_array($resources))
		{
			return array($resources);
		}
		
		return $resources;
	}

	public function GetRepeatOptions()
	{
		return $this->_presenter->GetRepeatOptions();
	}
	
	public function GetRepeatType()
	{
		return $this->GetForm(FormKeys::REPEAT_OPTIONS);
	}
	
	public function GetRepeatInterval()
	{
		return $this->GetForm(FormKeys::REPEAT_EVERY);
	}
	
	public function GetRepeatWeekdays()
	{
		$days = array();
		
		$sun = $this->GetForm(FormKeys::REPEAT_SUNDAY);
		if (!empty($sun))
		{
			$days[] = 0;
		}
		
		$mon = $this->GetForm(FormKeys::REPEAT_MONDAY);
		if (!empty($mon))
		{
			$days[] = 1;
		}
		
		$tue = $this->GetForm(FormKeys::REPEAT_TUESDAY);
		if (!empty($tue))
		{
			$days[] = 2;
		}
		
		$wed = $this->GetForm(FormKeys::REPEAT_WEDNESDAY);
		if (!empty($wed))
		{
			$days[] = 3;
		}
		
		$thu = $this->GetForm(FormKeys::REPEAT_THURSDAY);
		if (!empty($thu))
		{
			$days[] = 4;
		}
		
		$fri = $this->GetForm(FormKeys::REPEAT_FRIDAY);
		if (!empty($fri))
		{
			$days[] = 5;
		}
		
		$sat = $this->GetForm(FormKeys::REPEAT_SATURDAY);
		if (!empty($sat))
		{
			$days[] = 6;
		}
		
		return $days;
	}
	
	public function GetRepeatMonthlyType()
	{
		return $this->GetForm(FormKeys::REPEAT_MONTHLY_TYPE);
	}
	
	public function GetRepeatTerminationDate()
	{
		return $this->GetForm(FormKeys::END_REPEAT_DATE);
	}
	
	public function GetSeriesUpdateScope()
	{
		return $this->GetForm(FormKeys::SERIES_UPDATE_SCOPE);
	}
}
?>