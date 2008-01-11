<?php
/*
 All dates are stored as GMT
 */
class Date
{
	private $date;
	private $parts;
	private $timezone;
	
	public function __construct($timestamp = null, $timezone = 'GMT')
	{
		if ($timestamp == null)
		{
			$timestamp = time();
		}
		$this->timezone = $timezone;
		$this->date = new DateTime(strtotime($timestamp), new DateTimeZone($this->timezone));
		$this->parts = date_parse($this->date->format(DATE_W3C));	
	}
	
	public static function Now()
	{
		return new Date(mktime());
	}
	
	public function Format($format)
	{
		return $this->date->format($format);
	}
	
	public function ToTimezone($timezone)
	{
		return new Date($this->Timestamp(), $timezone);
	}
	
	public function Timestamp()
	{
		return mktime(
					$this->Hour(), 
					$this->Minute(), 
					$this->Second(), 
					$this->Month(), 
					$this->Day(), 
					$this->Year()
					);
	}
	
	public function AddDays($days)
	{		
		$timestamp = mktime(
							$this->Hour(), 
							$this->Minute(), 
							$this->Second(), 
							$this->Month(), 
							$this->Day() + $days, 
							$this->Year()
							);
			
		return new Date($timestamp);
	}	
	
	public function Hour()
	{
		return $this->parts['hour'];		
	}
	
	public function Minute()
	{
		return $this->parts['minute'];
	}
	
	public function Second()
	{
		return $this->parts['second'];
	}
	
	public function Month()
	{
		return $this->parts['month'];
	}
	
	public function Day()
	{
		return $this->parts['day'];
	}
	
	public function Year()
	{
		return $this->parts['year'];
	}
//	
//	public function DayOfYear()
//	{
//		return $this->parts['yday'];
//	}
//	
//	public function DayOfWeek()
//	{
//		return $this->parts['wday'];
//	}
}
?>