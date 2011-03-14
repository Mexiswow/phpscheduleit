<?php
class Time
{
	private $_hour;
	private $_minute;
	private $_second;
	private $_timezone;
	
	public function __construct($hour, $minute, $second = null, $timezone = null)
	{
		$this->_hour = intval($hour);
		$this->_minute =  intval($minute);
		$this->_second = is_null($second) ? 0 : intval($second);
		$this->_timezone = $timezone;
		
		if (empty($timezone))
    	{
    		$this->_timezone = Configuration::Instance()->GetKey(ConfigKeys::SERVER_TIMEZONE);
    	}
	}

    private function GetDate()
    {
    	$parts = getdate(strtotime("$this->_hour:$this->_minute:$this->_second"));
    	return new Date("{$parts['year']}-{$parts['mon']}-{$parts['mday']} $this->_hour:$this->_minute:$this->_second", $this->_timezone);
    }
    
    /**
     * @param string $time
     * @param string $timezone, defaults to server timezone if not provided
     * @return Time
     */
    public static function Parse($time, $timezone = null)
    {
    	$date = new Date($time, $timezone);
  
    	return new Time($date->Hour(), $date->Minute(), $date->Second(), $timezone);
    }
	
	public function Hour()
	{
		return $this->_hour;
	}
	
	public function Minute()
	{
		return $this->_minute;
	}
	
	public function Second()
	{
		return $this->_second;
	}
	
	public function Timezone()
	{
		return $this->_timezone;
	}

	public function Format($format)
	{
		return $this->GetDate()->Format($format);
	}
	
	/**
	 * Compares this time to the one passed in
	 * Returns:
	 * -1 if this time is less than the passed in time
	 * 0 if the times are equal
	 * 1 if this time is greater than the passed in time
	 * @param Time $time
	 * @param Date $comparisonDate date to be used for time comparison
	 * @return int comparison result
	 */
	public function Compare(Time $time, Date $comparisonDate)
	{
		if ($comparisonDate != null)
		{
			$myDate = Date::Create($comparisonDate->Year(), $comparisonDate->Month(), $comparisonDate->Day(), $this->Hour(), $this->Minute(), $this->Second(), $this->Timezone());
			$otherDate = Date::Create($comparisonDate->Year(), $comparisonDate->Month(), $comparisonDate->Day(), $time->Hour(), $time->Minute(), $time->Second(), $time->Timezone());
			
			return ($myDate->Compare($otherDate));
		}
		
		return $this->GetDate()->Compare($time->GetDate());
	}
	
	/**
	 * Compares this time to the one passed in
	 * @param Time $time
	 * @return bool if the current object is greater than the one passed in
	 */
//	public function GreaterThan(Time $time, Date $comparisonDate = null)
//	{
//		return $this->Compare($time, $comparisonDate) > 0;
//	}
	
	/**
	 * Compares this time to the one passed in
	 * @param Time $time
	 * @return bool if the current object is less than the one passed in
	 */
//	public function LessThan(Time $time, Date $comparisonDate = null)
//	{
//		return $this->Compare($time, $comparisonDate) < 0;
//	}
	
	/**
	 * Compare the 2 times
	 *
	 * @param Time $time
	 * @return bool
	 */
//	public function Equals(Time $time, Date $comparisonDate = null)
//	{
//		return $this->Compare($time, $comparisonDate) == 0;
//	}
	
	public function ToString()
	{
		return sprintf("%d:%02d:%02d", $this->_hour, $this->_minute, $this->_second);
	}
	
	public function __toString() 
	{
      return $this->ToString();
  	}
}
?>