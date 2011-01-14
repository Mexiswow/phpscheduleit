<?php
require_once(ROOT_DIR . 'lib/Common/namespace.php');

class ExistingReservationSeries extends ReservationSeries
{
	/**
	 * @var int
	 */
	private $seriesId;
	
	/**
	 * @var ISeriesUpdateScope
	 */
	protected $seriesUpdateStrategy;
	
	protected $events = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->ApplyChangesTo(SeriesUpdateScope::FullSeries);
	}

	public function SeriesId()
	{
		return $this->seriesId;
	}
	
	public function SeriesRepeatOptions()
	{
		return $this->_repeatOptions;
	}
	
	/**
	 * @internal
	 */
	public function WithId($seriesId)
	{
		$this->seriesId = $seriesId;
	}
	
	/**
	 * @internal
	 */
	public function WithOwner($userId)
	{
		$this->_userId = $userId;
	}
	
	/**
	 * @internal
	 */
	public function WithPrimaryResource($resourceId)
	{
		$this->_resourceId = $resourceId;
	}
	
	/**
	 * @internal
	 */
	public function WithSchedule($scheduleId)
	{
		$this->_scheduleId = $scheduleId;
	}
	
	/**
	 * @internal
	 */
	public function WithTitle($title)
	{
		$this->_title = $title;
	}
	
	/**
	 * @internal
	 */
	public function WithDescription($description)
	{
		$this->_description = $description;
	}
	
	/**
	 * @internal
	 */
	public function WithResource($resourceId)
	{
		$this->AddResource($resourceId);
	}
	
	/**
	 * @internal
	 */
	public function WithRepeatOptions(IRepeatOptions $repeatOptions)
	{
		$this->_repeatOptions = $repeatOptions;
	}

	/**
	 * @internal
	 */
	public function WithCurrentInstance(Reservation $reservation)
	{
		$this->AddInstance($reservation);
		$this->currentInstanceDate = $reservation->StartDate();
	}
	
	/**
	 * @internal
	 */
	public function WithInstance(Reservation $reservation)
	{
		$this->AddInstance($reservation);
	}
	
	/**
	 * @internal
	 */
	public function RemoveInstance(Reservation $reservation)
	{
		unset($this->instances[$reservation->StartDate()->Timestamp()]);
	}
	
	public function RequiresNewSeries()
	{
		return $this->seriesUpdateStrategy->RequiresNewSeries();
	}
	
	/**
	 * @param int $userId
	 * @param int $resourceId
	 * @param string $title
	 * @param string $description
	 */
	public function Update($userId, $resourceId, $title, $description)
	{
		$this->_userId = $userId;
		$this->_resourceId = $resourceId;
		$this->_title = $title;
		$this->_description = $description;
	}
	
	/**
	 * @param SeriesUpdateScope $seriesUpdateScope
	 */
	public function ApplyChangesTo($seriesUpdateScope)
	{
		$this->seriesUpdateStrategy = SeriesUpdateScope::CreateStrategy($seriesUpdateScope);
	}
	
	/**
	 * @param IRepeatOptions $repeatOptions
	 */
	public function Repeats(IRepeatOptions $repeatOptions)
	{
		parent::Repeats($repeatOptions);
	}
	
	protected function AddNewInstance(DateRange $reservationDate)
	{
		parent::AddNewInstance($reservationDate);
		
		$this->AddEvent(new InstanceAddedEvent($this->GetInstance($reservationDate->GetBegin())));
	}
	
	public function GetEvents()
	{
		//$updateEvents = $this->seriesUpdateStrategy->GetEvents($this);
		return $this->events;
	}
	
	public function Instances()
	{
		return $this->seriesUpdateStrategy->Instances($this);
	}
	
	protected function AddEvent($event)
	{
		$this->events[] = $event;
	}
}

class InstanceAddedEvent
{
	public function __construct(Reservation $reservationInstance)
	{
		$this->instance = $reservationInstance;
	}
}

class InstanceRemovedEvent
{
	public function __construct(Reservation $reservationInstance)
	{
		$this->instance = $reservationInstance;
	}
}

class SeriesBranchedEvent
{
	public function __construct(ReservationSeries $series)
	{
		$this->series = $series;
	}
}
?>