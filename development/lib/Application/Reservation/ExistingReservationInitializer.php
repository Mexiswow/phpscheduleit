<?php
require_once(ROOT_DIR . 'lib/Application/Reservation/ReservationInitializerBase.php');
require_once(ROOT_DIR . 'Pages/ExistingReservationPage.php');

class ExistingReservationInitializer extends ReservationInitializerBase
{
	/**
	 * @var IExistingReservationPage
	 */
	private $page;
	
	/**
	 * @var ReservationView 
	 */
	private $reservationView;
	
	/**
	 * @var IEditableCriteria
	 */
	private $editableCriteria;
	
	/**
	 * @param IExistingReservationPage $page
	 * @param IScheduleUserRepository $scheduleUserRepository
	 * @param IScheduleRepository $scheduleRepository
	 * @param IUserRepository $userRepository
	 * @param ReservationView $reservationView
	 * @param IEditableCriteria $editableCriteria defaults to new EditableViewCriteria
	 */
	public function __construct(
		IExistingReservationPage $page, 
		IScheduleUserRepository $scheduleUserRepository,
		IScheduleRepository $scheduleRepository,
		IUserRepository $userRepository,
		ReservationView $reservationView,
		$editableCriteria = null
		)
	{
		$this->page = $page;
		$this->reservationView = $reservationView;
		$this->editableCriteria = ($editableCriteria == null) ? new EditableViewCriteria() : $editableCriteria;
		
		parent::__construct(
						$page, 
						$scheduleUserRepository, 
						$scheduleRepository, 
						$userRepository);
	}
	
	public function Initialize()
	{
		parent::Initialize();
		
		$timezone = $this->GetTimezone();
		
		$this->page->SetStartTime($this->reservationView->StartDate->ToTimezone($timezone)->GetTime());
		$this->page->SetEndTime($this->reservationView->EndDate->ToTimezone($timezone)->GetTime());
		
		$this->page->SetAdditionalResources($this->reservationView->AdditionalResourceIds);
		$this->page->SetParticipants($this->reservationView->ParticipantIds);
		$this->page->SetTitle($this->reservationView->Title);
		$this->page->SetDescription($this->reservationView->Description);
		$this->page->SetReferenceNumber($this->reservationView->ReferenceNumber);
		$this->page->SetReservationId($this->reservationView->ReservationId);
		
		$this->page->SetIsRecurring($this->reservationView->IsRecurring());
		$this->page->SetRepeatType($this->reservationView->RepeatType);
		$this->page->SetRepeatInterval($this->reservationView->RepeatInterval);
		$this->page->SetRepeatMonthlyType($this->reservationView->RepeatMonthlyType);
		if ($this->reservationView->RepeatTerminationDate != null)
		{
			$this->page->SetRepeatTerminationDate($this->reservationView->RepeatTerminationDate->ToTimezone($this->GetTimezone()));
		}
		$this->page->SetRepeatWeekdays($this->reservationView->RepeatWeekdays);
		
		$this->page->SetIsEditable($this->editableCriteria->IsEditable($this->reservationView));
	}
	
	protected function GetOwnerId()
	{
		return $this->reservationView->OwnerId;
	}
	
	protected function GetResourceId()
	{
		return $this->reservationView->ResourceId;
	}
	
	protected function GetScheduleId()
	{
		return $this->reservationView->ScheduleId;
	}
	
	protected function GetStartDate()
	{
		return $this->reservationView->StartDate;
	}
	
	protected function GetEndDate()
	{
		return $this->reservationView->EndDate;
	}
	
	protected function GetTimezone()
	{
		return ServiceLocator::GetServer()->GetUserSession()->Timezone;
	}
}

interface IEditableCriteria
{
	function IsEditable(ReservationView $reservationView);
}

class EditableViewCriteria implements IEditableCriteria
{
	public function IsEditable(ReservationView $reservationView)
	{
		$currentUser = ServiceLocator::GetServer()->GetUserSession();
		
		if ($currentUser->IsAdmin)
		{
			return true;
		}
		
		if ($reservationView->OwnerId != $currentUser->UserId)
		{
			return false;
		}
		
		return Date::Now()->LessThan($reservationView->EndDate);
		
	}
}
?>