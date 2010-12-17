<?php
require_once(ROOT_DIR . 'lib/Common/namespace.php');

interface IRepeatOptions
{
	/**
	 * Gets array of DateRange objects
	 * 
	 * @return array[int]DateRange
	 */
	function GetDates();
	
	function ConfigurationString();
	
	function RepeatType();
}

abstract class RepeatOptionsAbstract implements IRepeatOptions
{		
	/**
	 * @var int
	 */
	protected $_interval;
	
	/**
	 * @var Date
	 */
	protected $_terminiationDate;
	
	/**
	 * @var DateRange
	 */
	protected $_duration;
	
	/**
	 * @param int $interval
	 * @param Date $terminiationDate
	 * @param DateRange $duration
	 */
	protected function __construct($interval, $terminiationDate, $duration)
	{
		$this->_interval = $interval;
		$this->_terminiationDate = $terminiationDate;
		$this->_duration = $duration;
	}
	
	public function ConfigurationString() 
	{
		return sprintf("interval=%s|termination=%s", $this->_interval, $this->_terminiationDate->ToUTC()->Timestamp());
	}
}
class RepeatType
{
	const None = 'none';
	const Daily = 'daily';
	const Weekly = 'weekly';
	const Monthly = 'monthly';
	const Yearly = 'yearly';
}

class NoRepetion implements IRepeatOptions
{
	public function GetDates()
	{
		return array();
	}
	
	public function RepeatType()
	{
		return RepeatType::None;
	}
	
	public function ConfigurationString() 
	{
		return '';
	}
}

class DailyRepeat extends RepeatOptionsAbstract
{
	/**
	 * @param int $interval
	 * @param Date $terminiationDate
	 * @param DateRange $duration
	 */
	public function __construct($interval, $terminiationDate, $duration)
	{
		parent::__construct($interval, $terminiationDate, $duration);
	}
	
	public function GetDates()
	{
		$dates = array();
		$startDate = $this->_duration->GetBegin()->AddDays($this->_interval);
		$endDate = $this->_duration->GetEnd()->AddDays($this->_interval);
		
		while ($startDate->Compare($this->_terminiationDate) <= 0)
		{
			$dates[] = new DateRange($startDate->ToUtc(), $endDate->ToUtc());
			$startDate = $startDate->AddDays($this->_interval);
			$endDate = $endDate->AddDays($this->_interval);
		}
		
		return $dates;
	}
	
	public function RepeatType()
	{
		return RepeatType::Weekly;
	}
}
	
class WeeklyRepeat extends RepeatOptionsAbstract
{
	/**
	 * @var array
	 */
	private $_daysOfWeek;
	
	/**
	 * @param int $interval
	 * @param Date $terminiationDate
	 * @param DateRange $duration
	 * @param array $daysOfWeek
	 */
	public function __construct($interval, $terminiationDate, $duration, $daysOfWeek)
	{
		parent::__construct($interval, $terminiationDate, $duration);
		
		$this->_daysOfWeek = $daysOfWeek;
		sort($this->_daysOfWeek);
	}
	
	public function GetDates()
	{
		$dates = array();
		
		$startDate = $this->_duration->GetBegin();
		$endDate = $this->_duration->GetEnd();
		
		$startWeekday = $startDate->Weekday();
		foreach ($this->_daysOfWeek as $weekday)
		{
			if ($startWeekday < $weekday)
			{
				$startDate = $startDate->AddDays($weekday - $startWeekday);
				$endDate = $endDate->AddDays($weekday - $startWeekday);
				
				$dates[] = new DateRange($startDate->ToUtc(), $endDate->ToUtc());
			}
		}
		
		$rawStart =  $this->_duration->GetBegin();
		$rawEnd =  $this->_duration->GetEnd();
		
		$week = 1;
		
		while ($startDate->Compare($this->_terminiationDate) <= 0)
		{
			$weekOffset = (7 * $this->_interval * $week);
			
			for ($day = 0; $day < count($this->_daysOfWeek); $day++)
			{
				$intervalOffset = $weekOffset + ($this->_daysOfWeek[$day] - $startWeekday);
				$startDate = $rawStart->AddDays($intervalOffset);
				$endDate = $rawEnd->AddDays($intervalOffset);
			
				if ($startDate->Compare($this->_terminiationDate) <= 0)
				{
					$dates[] = new DateRange($startDate->ToUtc(), $endDate->ToUtc());
				}
			}
			
			$week++;
		}

		return $dates;
	}
	
	public function RepeatType()
	{
		return RepeatType::Monthly;
	}
	
	public function ConfigurationString() 
	{
		$config = parent::ConfigurationString();
		return sprintf("%s|days=%s", $config, implode(',', $this->_daysOfWeek));
	}
}

class DayOfMonthRepeat extends RepeatOptionsAbstract
{
	/**
	 * @param int $interval
	 * @param Date $terminiationDate
	 * @param DateRange $duration
	 */
	public function __construct($interval, $terminiationDate, $duration)
	{
		parent::__construct($interval, $terminiationDate, $duration);
	}
	
	public function GetDates()
	{
		$dates = array();
		
		$startDate = $this->_duration->GetBegin();
		$endDate = $this->_duration->GetEnd();

		$rawStart = $this->_duration->GetBegin();
		$rawEnd = $this->_duration->GetEnd();
		
		$monthsFromStart = 1;
		while ($startDate->Compare($this->_terminiationDate) <= 0)
		{
			$monthAdjustment = $monthsFromStart * $this->_interval;
			if ($this->DayExistsInNextMonth($rawStart, $monthAdjustment))
			{
				$startDate = $this->GetNextMonth($rawStart, $monthAdjustment);
				$endDate = $this->GetNextMonth($rawEnd, $monthAdjustment);
				if ($startDate->Compare($this->_terminiationDate) <= 0)
				{
					$dates[] = new DateRange($startDate->ToUtc(), $endDate->ToUtc());
				}
			}
			$monthsFromStart++;
		}
		
		return $dates;
	}
	
	public function RepeatType()
	{
		return RepeatType::Monthly;
	}
	
	public function ConfigurationString() 
	{
		$config = parent::ConfigurationString();
		return sprintf("%s|type=%s", $config, 'repeatMonthDay');
	}
	
	private function DayExistsInNextMonth($date, $monthsFromStart)
	{
		$dateToCheck = Date::Create($date->Year(), $date->Month(), 1, 0, 0, 0, $date->Timezone());
		$nextMonth = $this->GetNextMonth($dateToCheck, $monthsFromStart);
		
		$daysInMonth = $nextMonth->Format('t');
		return $date->Day() <= $daysInMonth;
	}
	
	/**
	 * @var Date $date
	 * @return Date
	 */
	private function GetNextMonth($date, $monthsFromStart)
	{
		$yearOffset = 0;
		$computedMonth = $date->Month() + $monthsFromStart;
		$month = $computedMonth;
		
		if ($computedMonth > 12)
		{	
			$yearOffset = (int)$computedMonth/12;
			$month = $computedMonth % 12 + 1;
		}

		return Date::Create($date->Year() + $yearOffset, $month, $date->Day(), $date->Hour(), $date->Minute(), $date->Second(), $date->Timezone());
	}
}

class WeekDayOfMonthRepeat extends RepeatOptionsAbstract
{	
	private $_typeList = array (1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth', 5 => 'fifth');
	private $_dayList = array(0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday');
	
	private $_dayOfWeek;
	private $_startMonth;
	private $_startYear;
	private $_weekNumber;
	
	/**
	 * @param int $interval
	 * @param Date $terminiationDate
	 * @param DateRange $duration
	 */
	public function __construct($interval, $terminiationDate, $duration)
	{
		parent::__construct($interval, $terminiationDate, $duration);
		
		$durationStart = $this->_duration->GetBegin();
		$firstWeekdayOfMonth = date('w', mktime(0, 0, 0, $durationStart->Month(), 1, $durationStart->Year()));
		
		$this->_weekNumber = $this->GetWeekNumber($durationStart, $firstWeekdayOfMonth);
		$this->_dayOfWeek = $durationStart->Weekday();
		$this->_startMonth = $durationStart->Month();
		$this->_startYear = $durationStart->Year();
	}
	
	public function GetDates()
	{
		$dates = array();
		
		$startDate = $this->_duration->GetBegin();
		$endDate = $this->_duration->GetEnd();
		
		$monthsFromStart = 1;
		while ($startDate->Compare($this->_terminiationDate) <= 0)
		{
			$monthAdjustment = $this->_startMonth + $monthsFromStart * $this->_interval;
			$month = $monthAdjustment % 12;
			$year = $this->_startYear + floor($monthAdjustment/12);
			
			$weekNumber = $this->GetWeekNumberOfMonth($this->_weekNumber, $month, $year, $this->_dayOfWeek);

			$dayOfMonth = strtotime("{$this->_typeList[$weekNumber]} {$this->_dayList[$this->_dayOfWeek]} $year-$month-01");
			$calculatedDate =  date('Y-m-d', $dayOfMonth);
			$calculatedMonth = explode('-', $calculatedDate);
			
			$startDateString = $calculatedDate . " {$startDate->Hour()}:{$startDate->Minute()}:{$startDate->Second()}";
			$startDate = Date::Parse($startDateString, $startDate->Timezone());
				
			if ($month == $calculatedMonth[1])
			{
				if ($startDate->Compare($this->_terminiationDate) <= 0)
				{
					$endDateString =  $calculatedDate . " {$endDate->Hour()}:{$endDate->Minute()}:{$endDate->Second()}";
					$endDate = Date::Parse($endDateString, $endDate->Timezone());
			
					$dates[] = new DateRange($startDate->ToUtc(), $endDate->ToUtc());
				}
			}

			$monthsFromStart++;
		}
		
		return $dates;
	}
	
	public function RepeatType()
	{
		return RepeatType::Monthly;
	}
	
	public function ConfigurationString() 
	{
		$config = parent::ConfigurationString();
		return sprintf("%s|type=%s", $config, 'repeatMonthWeek');
	}
	
	private function GetWeekNumber(Date $firstDate, $firstWeekdayOfMonth)
	{
		$week = ceil($firstDate->Day()/7);
		if ($firstWeekdayOfMonth > $firstDate->Weekday())
		{
			$week++;
		}
		
		return $week;
	}
	
	private function GetWeekNumberOfMonth($week, $month, $year, $desiredDayOfWeek)
	{
		$firstWeekdayOfMonth = date('w', mktime(0, 0, 0, $month, 1, $year));
	
		$weekNumber = $week;
		if ($firstWeekdayOfMonth == $desiredDayOfWeek)
		{
			$weekNumber--;
		}
		
		return $weekNumber;
	}
}

class YearlyRepeat extends RepeatOptionsAbstract
{
	/**
	 * @param int $interval
	 * @param Date $terminiationDate
	 * @param DateRange $duration
	 */
	public function __construct($interval, $terminiationDate, $duration)
	{
		parent::__construct($interval, $terminiationDate, $duration);
	}
	
	public function GetDates()
	{
		$dates = array();
		$begin = $this->_duration->GetBegin();		
		$end = $this->_duration->GetEnd();
		
		$nextStartYear = $begin->Year();
		$nextEndYear = $end->Year();
		$timezone = $begin->Timezone();
		
		$startDate = $begin;
		
		while ($startDate->Compare($this->_terminiationDate) <= 0)
		{
			$nextStartYear = $nextStartYear + $this->_interval;
			$nextEndYear = $nextEndYear + $this->_interval;
			
			$startDate = Date::Create($nextStartYear, $begin->Month(), $begin->Day(), $begin->Hour(), $begin->Minute(), $begin->Second(), $timezone);
			$endDate = Date::Create($nextEndYear, $end->Month(), $end->Day(), $end->Hour(), $end->Minute(), $end->Second(), $timezone);
			
			if ($startDate->Compare($this->_terminiationDate) <= 0)
			{
				$dates[] = new DateRange($startDate->ToUtc(), $endDate->ToUtc());
			}
		}
		
		return $dates;
	}
	
	public function RepeatType()
	{
		return RepeatType::Yearly;
	}
}

class RepeatOptionsFactory
{
	/**
	 * @param string $repeatType must be option in RepeatType enum
	 * @param int $interval
	 * @param Date $terminationDate
	 * @param DateRange $initialReservationDates
	 * @param array $weekdays
	 * @param string $monthlyType
	 * @return IRepeatOptions
	 */
	public function Create($repeatType, $interval, $terminationDate, $initialReservationDates, $weekdays, $monthlyType)
	{
		switch ($repeatType)
		{
			case RepeatType::Daily : 
				return new DailyRepeat($interval, $terminationDate, $initialReservationDates);
				
			case RepeatType::Weekly : 
				return new WeeklyRepeat($interval, $terminationDate, $initialReservationDates, $weekdays);
				
			case RepeatType::Monthly : 
				return ($monthlyType == "dayOfMonth") ? 
					new DayOfMonthRepeat($interval, $terminationDate, $initialReservationDates) : 
					new WeekDayOfMonthRepeat($interval, $terminationDate, $initialReservationDates);
					
			case RepeatType::Yearly : 
				return new YearlyRepeat($interval, $terminationDate, $initialReservationDates);
		}
		
		return new NoRepetion();
	}
	
	
}

class RepeatConfiguration
{
	/**
	 * @var string
	 */
	public $Type;
	
	/**
	 * @var string
	 */
	public $Interval;
	
	/**
	 * @var Date
	 */
	public $TerminationDate;
	
	/**
	 * @param string $repeatType
	 * @param string $configurationString
	 * @return RepeatConfiguration
	 */
	public static function Create($repeatType, $configurationString)
	{
		$allparts = explode('|', $configurationString);
		$configParts = array();
		
		if (!empty($allparts[0]))
		{
			foreach($allparts as $part)
			{
				$keyValue = explode('=', $part);
				
				if (!empty($keyValue[0]))
				{
					$configParts[$keyValue[0]] = $keyValue[1];
				}
			}
		}
		
		$config = new RepeatConfiguration();
		$config->Type = $repeatType;
		
		$config->Interval = self::Get($configParts, 'interval');
		$config->SetTerminationDate(self::Get($configParts, 'termination'));

		return $config;
	}
	
	protected function __construct()
	{}
	
	private function Get($array, $key)
	{
		if (isset($array[$key]))
		{
			return $array[$key];
		}
		
		return null;
	}
	
	private function SetTerminationDate($terminationDateString)
	{
		echo "blah = $terminationDateString";
		if (!empty($terminationDateString))
		{
			$this->TerminiationDate = Date::FromDatabase($terminationDateString);
		}
	}
}
?>