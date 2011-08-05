<?php

class Quota
{
	/**
	 * @var int
	 */
	private $quotaId;

	/**
	 * @var \IQuotaDuration
	 */
	private $duration;

	/**
	 * @var \IQuotaLimit
	 */
	private $limit;

	public function __construct($quotaId, $duration, $limit)
	{
	    $this->quotaId = $quotaId;
		$this->duration = $duration;
		$this->limit = $limit;
	}

	/**
	 * @param ReservationSeries $reservationSeries
	 * @param IReservationViewRepository $reservationViewRepository
	 * @param string $timezone
	 * @return bool
	 */
	public function ExceedsQuota($reservationSeries, IReservationViewRepository $reservationViewRepository, $timezone)
	{
		$dates = $this->duration->GetSearchDates($reservationSeries, $timezone);
		$reservationsWithinRange = $reservationViewRepository->GetReservationList($dates->Start(), $dates->End(), $reservationSeries->UserId(), ReservationUserLevel::OWNER);

		try
		{
			$this->CheckAll($reservationsWithinRange, $reservationSeries, $timezone);
		}
		catch (QuotaExceededException $ex)
		{
			return true;
		}

		return false;
	}

	public function __toString()
	{
		return $this->quotaId . '';
	}

	/**
	 * @param array|ReservationItemView[] $reservationsWithinRange
	 * @param ReservationSeries $series
	 * @param string $timezone
	 * @throws QuotaExceededException
	 */
	private function CheckAll($reservationsWithinRange, $series, $timezone)
	{
		/** @var $instance Reservation */
		foreach ($series->Instances() as $instance)
		{
			$this->AddInstance($instance, $timezone);
		}

		/** @var $reservation ReservationItemView */
		foreach ($reservationsWithinRange as $reservation)
		{
			if ($series->ContainsResource($reservation->ResourceId))
			{
				$this->AddExisting($reservation, $timezone);
			}
		}
	}

	public function AddExisting(ReservationItemView $reservation, $timezone)
	{
		$this->_breakAndAdd($reservation->StartDate, $reservation->EndDate, $timezone);
	}

	public function AddInstance(Reservation $reservation, $timezone)
	{
		$this->_breakAndAdd($reservation->StartDate(), $reservation->EndDate(), $timezone);
	}

	private function _breakAndAdd(Date $startDate, Date $endDate, $timezone)
	{

		$start = $startDate->ToTimezone($timezone);
		$end = $endDate->ToTimezone($timezone);

		$range = new DateRange($start, $end);

		$ranges = $this->duration->Split($range);

		foreach ($ranges as $dr)
		{
			$this->_add($dr);
		}
	}

	private function _add(DateRange $dateRange)
	{
		$durationKey = $this->duration->GetDurationKey($dateRange->GetBegin());

		$this->limit->TryAdd($dateRange->GetBegin(), $dateRange->GetEnd(), $durationKey);
	}
}

interface IQuotaDuration
{
	/**
	 * @param ReservationSeries $reservationSeries
	 * @param string $timezone
	 * @return QuotaSearchDates
	 */
	public function GetSearchDates(ReservationSeries $reservationSeries, $timezone);

	/**
	 * @abstract
	 * @param DateRange $dateRange
	 * @return array|DateRange[]
	 */
	public function Split(DateRange $dateRange);

	/**
	 * @abstract
	 * @param Date $date
	 * @return void
	 */
	public function GetDurationKey(Date $date);
}

class QuotaSearchDates
{
	/**
	 * @var \Date
	 */
	private $start;

	/**
	 * @var \Date
	 */
	private $end;
	
	public function __construct(Date $start, Date $end)
	{
		$this->start = $start;
		$this->end = $end;
	}

	/**
	 * @return Date
	 */
	public function Start()
	{
		return $this->start;
	}

	/**
	 * @return Date
	 */
	public function End()
	{
		return $this->end;
	}
}

class QuotaDurationDay extends QuotaDuration implements IQuotaDuration
{
	/**
	 * @param ReservationSeries $reservationSeries
	 * @param string $timezone
	 * @return QuotaSearchDates
	 */
	public function GetSearchDates(ReservationSeries $reservationSeries, $timezone)
	{
		$dates = $this->GetFirstAndLastReservationDates($reservationSeries);
		
		$startDate = $dates[0]->ToTimezone($timezone)->GetDate();
		$endDate = $dates[1]->ToTimezone($timezone)->AddDays(1)->GetDate();

		return new QuotaSearchDates($startDate, $endDate);
	}

	public function Split(DateRange $dateRange)
	{
		$start = $dateRange->GetBegin();
		$end = $dateRange->GetEnd();
		
		$ranges = array();

		if (!$start->DateEquals($end))
		{
			$beginningOfNextDay = $start->AddDays(1)->GetDate();
			$ranges[] = new DateRange($start, $beginningOfNextDay);

			$currentDate = $beginningOfNextDay;

			for ($i = 1; $currentDate->LessThan($end) < 0; $i++)
			{
				$currentDate = $start->AddDays($i);
				$ranges[] = new DateRange($currentDate, $currentDate->AddDays(1)->GetDate());
			}

			$ranges[] = new DateRange($currentDate, $end);
		}
		else
		{
			$ranges[] = new DateRange($start, $end);
		}

		return $ranges;
	}

	/**
	 * @param Date $date
	 * @return string
	 */
	public function GetDurationKey(Date $date)
	{
		return sprintf("%s%s%s", $date->Year(), $date->Month(), $date->Day());
	}
}

abstract class QuotaDuration
{
	/**
	 * @param ReservationSeries $reservationSeries
	 * @return array|Date[]
	 */
	protected function GetFirstAndLastReservationDates(ReservationSeries $reservationSeries)
	{
		/** @var $instances Reservation[] */
		$instances = $reservationSeries->Instances();
		usort($instances, array('Reservation', 'Compare'));

		return array($instances[0]->StartDate(), $instances[count($instances)-1]->EndDate());
	}
}

class QuotaDurationWeek extends QuotaDuration implements IQuotaDuration
{
	/**
	 * @param ReservationSeries $reservationSeries
	 * @param string $timezone
	 * @return QuotaSearchDates
	 */
	public function GetSearchDates(ReservationSeries $reservationSeries, $timezone)
	{
		$dates = $this->GetFirstAndLastReservationDates($reservationSeries);
		
		$startDate = $dates[0]->ToTimezone($timezone);
		$daysFromWeekStart = $startDate->Weekday();
		$startDate = $startDate->AddDays(-$daysFromWeekStart)->GetDate();

		$endDate = $dates[1]->ToTimezone($timezone);
		$daysFromWeekEnd = 7 - $endDate->Weekday();
		$endDate = $endDate->AddDays($daysFromWeekEnd)->GetDate();

		return new QuotaSearchDates($startDate, $endDate);
	}

	/**
	 * @param Date $date
	 * @return void
	 */
	public function GetDurationKey(Date $date)
	{
		$daysFromWeekStart = $date->Weekday();
		$firstDayOfWeek = $date->AddDays(-$daysFromWeekStart)->GetDate();
		return sprintf("%s%s%s", $firstDayOfWeek->Year(), $firstDayOfWeek->Month(), $firstDayOfWeek->Day());
	}

	/**
	 * @param DateRange $dateRange
	 * @return array|DateRange[]
	 */
	public function Split(DateRange $dateRange)
	{
		$start = $dateRange->GetBegin();
		$end = $dateRange->GetEnd();

		$ranges = array();

		if (!$start->DateEquals($end))
		{
			$nextWeek = $this->GetStartOfNextWeek($start);

			if ($nextWeek->LessThan($end))
			{
				$ranges[] = new DateRange($start, $nextWeek);
				while ($nextWeek->LessThan($end))
				{
					$thisEnd = $this->GetStartOfNextWeek($nextWeek);

					if ($thisEnd->LessThan($end))
					{
						$ranges[] = new DateRange($nextWeek, $thisEnd);
					}
					else
					{
						$ranges[] = new DateRange($nextWeek, $end);
					}
					
					$nextWeek = $thisEnd;
				}
			}
			else
			{
				$ranges[] = new DateRange($start, $end);
			}
		}
		else
		{
			$ranges[] = new DateRange($start, $end);
		}


		return $ranges;
	}

	/**
	 * @param Date $start
	 * @return Date
	 */
	private function GetStartOfNextWeek(Date $date)
	{
		$daysFromWeekEnd = 7 - $date->Weekday();
		return $date->AddDays($daysFromWeekEnd)->GetDate();
	}
}

interface IQuotaLimit
{
	/**
	 * @abstract
	 * @param Date $start
	 * @param Date $end
	 * @param string $key
	 * @return void
	 * @throws QuotaExceededException
	 */
	public function TryAdd($start, $end, $key);
}

class QuotaLimitCount implements IQuotaLimit
{
	/**
	 * @var array|int[]
	 */
	var $aggregateCounts = array();

	/**
	 * @param int $totalAllowed
	 */
	public function __construct($totalAllowed)
	{
		$this->totalAllowed = $totalAllowed;
	}
	
	public function TryAdd($start, $end, $key)
	{
		if (array_key_exists($key, $this->aggregateCounts))
		{
			$this->aggregateCounts[$key] = $this->aggregateCounts[$key]+1;

			if ($this->aggregateCounts[$key] > $this->totalAllowed)
			{
				throw new QuotaExceededException("Only {$this->totalAllowed} reservations are allowed for this duration");
			}
		}
		else
		{
			$this->aggregateCounts[$key] = 1;
		}
	}
}

class QuotaLimitHours implements IQuotaLimit
{
	/**
	 * @var array|DateDiff[]
	 */
	var $aggregateCounts = array();

	/**
	 * @var \DateDiff
	 */
	var $allowedDuration;

	/**
	 * @var decimal
	 */
	var $allowedHours;
	
	/**
	 * @param int $allowedHours
	 */
	public function __construct($allowedHours)
	{
		$this->allowedHours = $allowedHours;
		$this->allowedDuration = new DateDiff($allowedHours * 3600);
	}

	/**
	 * @param Date $start
	 * @param Date $end
	 * @param string $key
	 * @return void
	 * @throws QuotaExceededException
	 */
	public function TryAdd($start, $end, $key)
	{
		$diff = $start->GetDifference($end);
		if (array_key_exists($key, $this->aggregateCounts))
		{
			$this->aggregateCounts[$key] = $this->aggregateCounts[$key]->Add($diff);

			if ($this->aggregateCounts[$key]->GreaterThan($this->allowedDuration))
			{
				throw new QuotaExceededException("Cumulative reservation length cannot exceed {$this->allowedHours} hours for this duration");
			}
		}
		else
		{
			$this->aggregateCounts[$key] = $diff;
		}
	}
}
class QuotaExceededException extends Exception
{
	/**
	 * @param string $message
	 */
	public function __construct($message)
	{
		parent::__construct($message);
	}
}

?>