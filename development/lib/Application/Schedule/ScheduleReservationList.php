<?php

interface IScheduleReservationList
{
	/**
	 * @return array[int]IReservationSlot
	 */
	function BuildSlots();
}

class ScheduleReservationList implements IScheduleReservationList
{
	private $_reservations;
	/**
	 * @var IScheduleLayout
	 */
	private $_layout;
	private $_layoutDateStart;
	private $_layoutDateEnd;
	
	private $_layoutItems;
	
	private $_reservationsByStartTime = array();
	private $_layoutByEndTime = array();	
	
	private $_midnight;
	private $_destinationTimezone;
	
	/**
	 * @var Date
	 */
	private $_firstLayoutTime; 
	
	/**
	 * @param array[int]ScheduleReservation $reservations array of ScheduleReservation objects
	 * @param IScheduleLayout $layout
	 * @param Date $layoutDate
	 */
	public function __construct($reservations, IScheduleLayout $layout, Date $layoutDate)
	{
		$this->_reservations = $reservations;
		$this->_layout = $layout;
		$this->_destinationTimezone = $this->_layout->Timezone();
		$this->_layoutDateStart = $layoutDate->ToTimezone($this->_destinationTimezone)->GetDate();
		$this->_layoutDateEnd = $this->_layoutDateStart->AddDays(1);
		$this->_layoutItems = $this->_layout->GetLayout($layoutDate);
		$this->_midnight = new Time(0,0,0, $this->_destinationTimezone);
		
		$this->IndexLayout();
		$this->IndexReservations();
	}
	
	public function BuildSlots()
	{
		$slots = array();
		
		for ($currentIndex = 0; $currentIndex < count($this->_layoutItems); $currentIndex++)
		{
			$layoutItem = $this->_layoutItems[$currentIndex];
			$reservation = $this->GetReservationStartingAt($layoutItem->Begin());
			
			if ($reservation != null)
			{			
				if ($this->ReservationEndsOnFutureDate($reservation))
				{
					$endTime = $this->_midnight;
				}
				else
				{
					$endTime = $reservation->GetEndDate()->ToTimezone($this->_destinationTimezone)->GetTime();
				}
				
				$endingPeriodIndex = max($this->GetLayoutIndexEndingAt($endTime), $currentIndex);
				$span = ($endingPeriodIndex - $currentIndex) + 1;
				$slots[] = new ReservationSlot($layoutItem->Begin(), $this->_layoutItems[$endingPeriodIndex]->End(), $this->_layoutDateStart, $span, $reservation);
				
				$currentIndex = $endingPeriodIndex;
			}
			else
			{
				$slots[] = new EmptyReservationSlot($layoutItem->Begin(), $layoutItem->End(), $this->_layoutDateStart, $layoutItem->IsReservable());
			}
		}
	
		return $slots;
	}
	
	private function IndexReservations()
	{
		foreach ($this->_reservations as $reservation)
		{		
			$start = $reservation->GetStartDate()->ToTimezone($this->_destinationTimezone);
			
			$startsInPast = $this->ReservationStartsOnPastDate($reservation);
			if ($startsInPast || $start->Compare($this->_firstLayoutTime) < 0)
			{
				$start = $this->_firstLayoutTime;
			}
			
			$end = $reservation->GetEndDate()->ToTimezone($this->_destinationTimezone);
			
			$endsInTheFuture = $this->ReservationEndsOnFutureDate($reservation);
			if ($endsInTheFuture || $end->Compare($this->_firstLayoutTime) >=0)
			{
//				Log::Debug("Indexing reservation %s, date %s %s, end %s %s, key %s, layout date %s", 
//					$reservation->GetReferenceNumber(), 
//					$reservation->GetStartDate(), $reservation->GetStartTime(),
//					$reservation->GetEndDate(), $reservation->GetEndTime(),
//					$startTime->ToString(),
//					$this->_layoutDateStart);
				
				$this->_reservationsByStartTime[$start->GetTime()->ToString()] = $reservation;
			}
		}
	}
	
	private function ReservationStartsOnPastDate(ScheduleReservation $reservation)
	{
		//Log::Debug("PAST");
		return $reservation->GetStartDate()->LessThan($this->_layoutDateStart);
	}
	
	private function ReservationEndsOnFutureDate(ScheduleReservation $reservation)
	{
		//Log::Debug("%s %s %s", $reservation->GetReferenceNumber(), $reservation->GetEndDate()->GetDate(), $this->_layoutDateEnd->GetDate());
		return $reservation->GetEndDate()->Compare($this->_layoutDateEnd) >= 0;
	}
	
	private function IndexLayout()
	{
		$this->_firstLayoutTime =  $this->_layoutDateStart->SetTime(new Time(23, 59, 59, $this->_destinationTimezone));
		
		for ($i = 0; $i < count($this->_layoutItems); $i++)		
		{
			$itemBegin = $this->_layoutItems[$i]->BeginDate();// $this->_layoutDateStart->SetTime($this->_layoutItems[$i]->Begin());
			if ($itemBegin->LessThan($this->_firstLayoutTime))
			{
				$this->_firstLayoutTime =  $this->_layoutItems[$i]->BeginDate();
			}
			
			$this->_layoutByEndTime[$this->_layoutItems[$i]->End()->ToString()] = $i;
		}
	}
	
	/**
	 * @param Time $endingTime
	 * @return int index of $_layoutItems which has the corresponding $endingTime
	 */
	private function GetLayoutIndexEndingAt(Time $endingTime)
	{
		$timeKey = $endingTime->ToString();
		
		if (array_key_exists($timeKey, $this->_layoutByEndTime))
		{
			return $this->_layoutByEndTime[$timeKey];
		}
		
		return $this->FindClosestLayoutIndexBeforeEndingTime($endingTime);
	}
	
	/**
	 * @param Time $beginTime
	 * @return ScheduleReservation
	 */
	private function GetReservationStartingAt(Time $beginTime)
	{
		$timeKey = $beginTime->ToString();
		if (array_key_exists($timeKey, $this->_reservationsByStartTime))
		{
			return $this->_reservationsByStartTime[$timeKey];
		}
		return null;
	}
	
	/**
	 * @param Time $endingTime
	 * @return int index of $_layoutItems which has the closest ending time to $endingTime without going past it
	 */
	private function FindClosestLayoutIndexBeforeEndingTime(Time $endingTime)
	{	
		for ($i = 0; $i < count($this->_layoutItems); $i++)		
		{
			$currentItem = $this->_layoutItems[$i];
		
			if ($currentItem->End()->Compare($endingTime, $this->_layoutDateStart) > 0 )
			{
				return $i-1;
			}
		}
		
		return 0;
	}
}
?>