<?php
require_once(ROOT_DIR . 'lib/Common/namespace.php');

class Reservation
{
	/**
	 * @var string
	 */
	protected $_referenceNumber;
	
	/**
	 * @return string
	 */
	public function ReferenceNumber()
	{
		return $this->_referenceNumber;
	}
	
	/**
	 * @var int
	 */
	protected $_userId;

	/**
	 * @return int
	 */
	public function UserId()
	{
		return $this->_userId;
	}

	/**
	 * @var int
	 */
	protected $_resourceId;

	/**
	 * @return int
	 */
	public function ResourceId()
	{
		return $this->_resourceId;
	}
	
	/**
	 * @var int
	 */
	protected $_scheduleId;
	
	/**
	 * @return int
	 */
	public function ScheduleId()
	{
		return $this->_scheduleId;
	}

	/**
	 * @var string
	 */
	protected $_title;

	/**
	 * @return string
	 */
	public function Title()
	{
		return $this->_title;
	}

	/**
	 * @var string
	 */
	protected $_description;

	/**
	 * @return string
	 */
	public function Description()
	{
		return $this->_description;
	}

	/**
	 * @var Date
	 */
	protected $_startDate;
	
	/**
	 * @return Date
	 */
	public function StartDate()
	{
		return $this->_startDate;
	}
	
	/**
	 * @var Date
	 */
	protected $_endDate;
	
	/**
	 * @return Date
	 */
	public function EndDate()
	{
		return $this->_endDate;
	}
	
	protected $_repeatOptions;
	
	/**
	 * @return IRepeatOptions
	 */
	public function RepeatOptions()
	{
		return $this->_repeatOptions;
	}
	
	protected $_repeatedDates = array();
	
	/**
	 * @return DateRange[]
	 */
	public function RepeatedDates()
	{
		return $this->_repeatedDates;
	}
	
	protected $_resources = array();
	
	/**
	 * @return int[]
	 */
	public function Resources()
	{
		return $this->_resources;
	}
	
	public function __construct()
	{
		$this->_repeatOptions = new NoRepetion();
		$this->_referenceNumber = uniqid();
	}
	
	/**
	 * @param int $userId
	 * @param int $resourceId
	 * @param int $scheduleId
	 * @param string $title
	 * @param string $description
	 */
	public function Update($userId, $resourceId, $scheduleId, $title, $description)
	{
		$this->_userId = $userId;
		$this->_resourceId = $resourceId;
		$this->_scheduleId = $scheduleId;
		$this->_title = $title;
		$this->_description = $description;
	}

	/**
	 * @param DateRange $duration
	 */
	public function UpdateDuration(DateRange $duration)
	{
		$this->_startDate = $duration->GetBegin()->ToUtc();
		$this->_endDate = $duration->GetEnd()->ToUtc();
	}
	
	/**
	 * @param IRepeatOptions $repeatOptions
	 */
	public function Repeats(IRepeatOptions $repeatOptions)
	{
		$this->_repeatOptions = $repeatOptions;
		$this->_repeatedDates = $repeatOptions->GetDates();
	}
	
	public function AddResource($resourceId)
	{
		$this->_resources[] = $resourceId;
	}
}

?>