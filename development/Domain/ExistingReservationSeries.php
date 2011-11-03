<?php
require_once(ROOT_DIR . 'lib/Common/namespace.php');

class ExistingReservationSeries extends ReservationSeries
{
	/**
	 * @var ISeriesUpdateScope
	 */
	protected $seriesUpdateStrategy;

	/**
	 * @var array|SeriesEvent[]
	 */
	protected $events = array();

	private $_deleteRequestIds = array();
	private $_updateRequestIds = array();

	public function __construct()
	{
		parent::__construct();
		$this->ApplyChangesTo(SeriesUpdateScope::FullSeries);
	}

	public function SeriesUpdateScope()
	{
		return $this->seriesUpdateStrategy->GetScope();
	}

	/**
	 * @internal
	 */
	public function WithId($seriesId)
	{
		$this->SetSeriesId($seriesId);
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
	public function WithPrimaryResource(BookableResource $resource)
	{
		$this->_resource = $resource;
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
	public function WithResource(BookableResource $resource)
	{
		$this->AddResource($resource);
	}

	/**
	 * @var IRepeatOptions
	 * @internal
	 */
	private $_originalRepeatOptions;

	/**
	 * @internal
	 */
	public function WithRepeatOptions(IRepeatOptions $repeatOptions)
	{
		$this->_originalRepeatOptions = $repeatOptions;
		$this->_repeatOptions = $repeatOptions;
	}

	/**
	 * @internal
	 */
	public function WithCurrentInstance(Reservation $reservation)
	{
		$this->AddInstance($reservation);
		$this->SetCurrentInstance($reservation);
	}

	/**
	 * @internal
	 */
	public function WithInstance(Reservation $reservation)
	{
		$this->AddInstance($reservation);
	}

	protected $_statusId;

	/**
	 * @param $statusId int|ReservationStatus
	 * @return void
	 */
	public function WithStatus($statusId)
	{
		$this->_statusId = $statusId;
	}

	/**
	 * @internal
	 */
	public function RemoveInstance(Reservation $reservation)
	{
		if ($reservation == $this->CurrentInstance())
		{
			return; // never remove the current instance
		}

		$instanceKey = $this->GetNewKey($reservation);
		unset($this->instances[$instanceKey]);

		$this->AddEvent(new InstanceRemovedEvent($reservation, $this));
		$this->_deleteRequestIds[] = $reservation->ReservationId();
	}

	public function RequiresNewSeries()
	{
		return $this->seriesUpdateStrategy->RequiresNewSeries();
	}

	/**
	 * @return int|ReservationStatus
	 */
	public function StatusId()
	{
		return $this->_statusId;
	}

	/**
	 * @param int $userId
	 * @param BookableResource $resource
	 * @param string $title
	 * @param string $description
	 * @param UserSession $updatedBy
	 */
	public function Update($userId, BookableResource $resource, $title, $description, UserSession $updatedBy)
	{
		if ($this->_resource->GetId() != $resource->GetId())
		{
			$this->AddEvent(new ResourceRemovedEvent($this->_resource, $this));
			$this->AddEvent(new ResourceAddedEvent($resource, ResourceLevel::Primary, $this));
		}

		$this->_userId = $userId;
		$this->_resource = $resource;
		$this->_title = $title;
		$this->_description = $description;
		$this->_bookedBy = $updatedBy;
	}

	/**
	 * @param DateRange $reservationDate
	 */
	public function UpdateDuration(DateRange $reservationDate)
	{
		$currentDuration = $this->CurrentInstance()->Duration();

		if ($currentDuration->Equals($reservationDate))
		{
			return;
		}

		Log::Debug('Updating duration for series %s', $this->SeriesId());

		$currentBegin = $currentDuration->GetBegin();
		$currentEnd = $currentDuration->GetEnd();

		$startTimeAdjustment = $currentBegin->GetDifference($reservationDate->GetBegin());
		$endTimeAdjustment = $currentEnd->GetDifference($reservationDate->GetEnd());

		foreach ($this->Instances() as $instance)
		{
			$newStart = $instance->StartDate()->ApplyDifference($startTimeAdjustment);
			$newEnd = $instance->EndDate()->ApplyDifference($endTimeAdjustment);
				
			$this->UpdateInstance($instance, new DateRange($newStart, $newEnd));
		}
	}

	/**
	 * @param SeriesUpdateScope|string $seriesUpdateScope
	 */
	public function ApplyChangesTo($seriesUpdateScope)
	{
		$this->seriesUpdateStrategy = SeriesUpdateScope::CreateStrategy($seriesUpdateScope);
		
		if ($this->seriesUpdateStrategy->RequiresNewSeries())
		{
			$this->AddEvent(new SeriesBranchedEvent($this));
			$this->Repeats($this->seriesUpdateStrategy->GetRepeatOptions($this));
		}
	}

	/**
	 * @param IRepeatOptions $repeatOptions
	 */
	public function Repeats(IRepeatOptions $repeatOptions)
	{
		if ($this->seriesUpdateStrategy->CanChangeRepeatTo($this, $repeatOptions))
		{
			Log::Debug('Updating recurrence for series %s', $this->SeriesId());
			
			$this->_repeatOptions = $repeatOptions;
				
			foreach ($this->instances as $instance)
			{
				// delete all reservation instances which will be replaced
				if ($this->seriesUpdateStrategy->ShouldInstanceBeRemoved($this, $instance))
				{
					$this->RemoveInstance($instance);
				}
			}
				
			// create all future instances
			parent::Repeats($repeatOptions);
		}
	}
	/**
	 * @param $resources array|BookableResource([]
	 * @return void
	 */
	public function ChangeResources($resources)
	{
		$diff = new ArrayDiff($this->_additionalResources, $resources);

		$added = $diff->GetAddedToArray1();
		$removed = $diff->GetRemovedFromArray1();

		/** @var $resource BookableResource */
		foreach ($added as $resource)
		{
			$this->AddEvent(new ResourceAddedEvent($resource, ResourceLevel::Additional, $this));
		}

		/** @var $resource BookableResource */
		foreach ($removed as $resource)
		{
			$this->AddEvent(new ResourceRemovedEvent($resource, $this));
		}
		
		$this->_additionalResources = $resources;
	}

	/**
	 * @param UserSession $deletedBy
	 * @return void
	 */
	public function Delete(UserSession $deletedBy)
	{
		$this->_bookedBy = $deletedBy;
		
		if (!$this->AppliesToAllInstances())
		{
			$instances = $this->Instances();
			Log::Debug('Removing %s instances of series %s', count($instances), $this->SeriesId());
			
			foreach ($instances as $instance)
			{
				Log::Debug("Removing instance %s from series %s", $instance->ReferenceNumber(), $this->SeriesId());
				
				$this->AddEvent(new InstanceRemovedEvent($instance, $this));
			}
		}
		else
		{
			Log::Debug("Removing series %s", $this->SeriesId());
			
			$this->AddEvent(new SeriesDeletedEvent($this));
		}
	}

	/**
	 * @param UserSession $approvedBy
	 * @return void
	 */
	public function Approve(UserSession $approvedBy)
	{
		$this->_bookedBy = $approvedBy;

		$this->_statusId = ReservationStatus::Created;
		
		Log::Debug("Approving series %s", $this->SeriesId());

		$this->AddEvent(new SeriesApprovedEvent($this));
	}
	
	private function AppliesToAllInstances()
	{
		return count($this->instances) == count($this->Instances());
	}

	protected function AddNewInstance(DateRange $reservationDate)
	{
		if (!$this->InstanceStartsOnDate($reservationDate))
		{
			Log::Debug('Adding instance for series %s on %s', $this->SeriesId(), $reservationDate);

			$newInstance = parent::AddNewInstance($reservationDate);
			$this->AddEvent(new InstanceAddedEvent($newInstance, $this));
		}
	}

	/**
	 * @internal
	 */
	public function UpdateInstance(Reservation $instance, DateRange $newDate)
	{
		unset($this->instances[$this->CreateInstanceKey($instance)]);
		
		$instance->SetReservationDate($newDate);
		$this->AddInstance($instance);

		$this->RaiseInstanceUpdatedEvent($instance);

	}

	private function RaiseInstanceUpdatedEvent(Reservation $instance)
	{
		if (!$instance->IsNew())
		{
			$this->AddEvent(new InstanceUpdatedEvent($instance, $this));
			$this->_updateRequestIds[] = $instance->ReservationId();
		}
	}

	/**
	 * @return array|SeriesEvent[]
	 */
	public function GetEvents()
	{
		$uniqueEvents = array_unique($this->events);
		usort($uniqueEvents, array('SeriesEvent', 'Compare'));

		return $uniqueEvents;
	}

	public function Instances()
	{
		return $this->seriesUpdateStrategy->Instances($this);
	}

	/**
	 * @internal
	 */
	public function _Instances()
	{
		return $this->instances;
	}

	protected function AddEvent(SeriesEvent $event)
	{
		$this->events[] = $event;
	}

	public function IsMarkedForDelete($reservationId)
	{
		return in_array($reservationId, $this->_deleteRequestIds);
	}

	public function IsMarkedForUpdate($reservationId)
	{
		return in_array($reservationId, $this->_updateRequestIds);
	}

	/**
	 * @param int[] $participantIds
	 * @return void
	 */
	public function ChangeParticipants($participantIds)
	{
		/** @var Reservation $instance */
		foreach ($this->Instances() as $instance)
		{
			$instance->ChangeParticipants($participantIds);
			$this->RaiseInstanceUpdatedEvent($instance);
		}
	}

	/**
	 * @param int[] $inviteeIds
	 * @return void
	 */
	public function ChangeInvitees($inviteeIds)
	{
		/** @var Reservation $instance */
		foreach ($this->Instances() as $instance)
		{
			$instance->ChangeInvitees($inviteeIds);
			$this->RaiseInstanceUpdatedEvent($instance);
		}
	}

	/**
	 * @param int $inviteeId
	 * @return void
	 */
	public function AcceptInvitation($inviteeId)
	{
		/** @var Reservation $instance */
		foreach ($this->Instances() as $instance)
		{
			$wasAccepted = $instance->AcceptInvitation($inviteeId);
			if ($wasAccepted)
			{
				$this->RaiseInstanceUpdatedEvent($instance);
			}
		}
	}

	/**
	 * @param int $inviteeId
	 * @return void
	 */
	public function DeclineInvitation($inviteeId)
	{
		/** @var Reservation $instance */
		foreach ($this->Instances() as $instance)
		{
			$wasAccepted = $instance->DeclineInvitation($inviteeId);
			if ($wasAccepted)
			{
				$this->RaiseInstanceUpdatedEvent($instance);
			}
		}
	}

	/**
	 * @param int $participantId
	 * @return void
	 */
	public function CancelAllParticipation($participantId)
	{
		/** @var Reservation $instance */
		foreach ($this->Instances() as $instance)
		{
			$wasCancelled = $instance->CancelParticipation($participantId);
			if ($wasCancelled)
			{
				$this->RaiseInstanceUpdatedEvent($instance);
			}
		}
	}

	/**
	 * @param int $participantId
	 * @return void
	 */
	public function CancelInstanceParticipation($participantId)
	{
		if ($this->CurrentInstance()->CancelParticipation($participantId))
		{
			$this->RaiseInstanceUpdatedEvent($this->CurrentInstance());
		}
	}

}

class SeriesEventPriority
{
	const Highest = 10;
	const High = 7;
	const Normal = 5;
	const Low = 3;
	const Lowest = 1;

}
abstract class SeriesEvent
{
	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var \ReservationSeries
	 */
	protected $series;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @param int|SeriesEventPriority $priority
	 * @return void
	 */
	protected function SetPriority($priority)
	{
		$this->priority = $priority;
	}

	/**
	 * @return int|SeriesEventPriority
	 */
	public function GetPriority()
	{
		return $this->priority;
	}

	/**
	 * @return ReservationSeries
	 */
	public function Series()
	{
		return $this->series;
	}

	/**
	 * @param ReservationSeries $series
	 * @param int|SeriesEventPriority $priority
	 */
	public function __construct(ReservationSeries $series, $priority = SeriesEventPriority::Normal)
	{
		$this->priority = $priority;
		$this->series = $series;
		$this->id = $this->series->SeriesId();
	}

	public function __toString()
	{
		return sprintf("%s-%s", get_class($this), $this->id);
	}

	public static function Compare(SeriesEvent $event1, SeriesEvent $event2)
	{
		if ($event1->GetPriority() == $event2->GetPriority())
		{
			return 0;
		}

		// higher priority should be at the top
		return ($event1->GetPriority() > $event2->GetPriority()) ? -1 : 1;
	}
}

class InstanceAddedEvent extends SeriesEvent
{
	/**
	 * @var Reservation
	 */
	private $instance;

	/**
	 * @return Reservation
	 */
	public function Instance()
	{
		return $this->instance;
	}

	public function __construct(Reservation $reservationInstance, ExistingReservationSeries $series)
	{
		$this->instance = $reservationInstance;
		parent::__construct($series, SeriesEventPriority::Lowest);
	}
	
	public function __toString()
	{
        return sprintf("%s%s", get_class($this), $this->instance->ReferenceNumber());
    }
}

class InstanceRemovedEvent extends SeriesEvent
{
	/**
	 * @var Reservation
	 */
	private $instance;

	/**
	 * @return Reservation
	 */
	public function Instance()
	{
		return $this->instance;
	}

	public function __construct(Reservation $reservationInstance, ExistingReservationSeries $series)
	{
		$this->instance = $reservationInstance;
		parent::__construct($series, SeriesEventPriority::Highest);
	}

	public function __toString()
	{
        return sprintf("%s%s", get_class($this), $this->instance->ReferenceNumber());
    }
}

class InstanceUpdatedEvent extends SeriesEvent
{
	/**
	 * @var Reservation
	 */
	private $instance;

	/**
	 * @return Reservation
	 */
	public function Instance()
	{
		return $this->instance;
	}

	public function __construct(Reservation $reservationInstance, ExistingReservationSeries $series)
	{
		$this->instance = $reservationInstance;
		parent::__construct($series, SeriesEventPriority::Low);
	}

	public function __toString()
	{
        return sprintf("%s%s", get_class($this), $this->instance->ReferenceNumber());
    }
}

class SeriesBranchedEvent extends SeriesEvent
{
	public function __construct(ReservationSeries $series)
	{
		parent::__construct($series);
	}

	public function __toString()
	{
        return sprintf("%s%s", get_class($this), $this->series->SeriesId());
    }
}

class SeriesDeletedEvent extends SeriesEvent
{
	public function __construct(ExistingReservationSeries $series)
	{
		parent::__construct($series, SeriesEventPriority::Highest);
	}

	/**
	 * @return ExistingReservationSeries
	 */
	public function Series()
	{
		return $this->series;
	}

	public function __toString()
	{
        return sprintf("%s%s", get_class($this), $this->series->SeriesId());
    }
}

class ResourceRemovedEvent extends SeriesEvent
{
	/**
	 * @var BookableResource
	 */
	private $resource;

	public function __construct(BookableResource $resource, ExistingReservationSeries $series)
	{
		$this->resource = $resource;

		parent::__construct($series, SeriesEventPriority::Highest);
	}

	/**
	 * @return BookableResource
	 */
	public function Resource()
	{
		return $this->resource;
	}

	/**
	 * @return int
	 */
	public function ResourceId()
	{
		return $this->resource->GetResourceId();
	}
	
	/**
	 * @return ExistingReservationSeries
	 */
	public function Series()
	{
		return $this->series;
	}

	public function __toString()
	{
        return sprintf("%s%s%s", get_class($this), $this->ResourceId(), $this->series->SeriesId());
    }
}

class ResourceAddedEvent extends SeriesEvent
{
	/**
	 * @var BookableResource
	 */
	private $resource;

	/**
	 * @var int|ResourceLevel
	 */
	private $resourceLevel;

	/**
	 * @param BookableResource $resource
	 * @param int|ResourceLevel $resourceLevel
	 * @param ExistingReservationSeries $series
	 */
	public function __construct(BookableResource $resource, $resourceLevel, ExistingReservationSeries $series)
	{
		$this->resource = $resource;
		$this->resourceLevel = $resourceLevel;

		parent::__construct($series, SeriesEventPriority::Low);
	}

	/**
	 * @return BookableResource
	 */
	public function Resource()
	{
		return $this->resource;
	}

	public function ResourceId()
	{
		return $this->resource->GetResourceId();
	}

	/**
	 * @return ExistingReservationSeries
	 */
	public function Series()
	{
		return $this->series;
	}

	public function __toString()
	{
        return sprintf("%s%s%s", get_class($this), $this->ResourceId(), $this->series->SeriesId());
    }

	public function ResourceLevel()
	{
		return $this->resourceLevel;
	}
}

class SeriesApprovedEvent extends SeriesEvent
{
	public function __construct(ExistingReservationSeries $series)
	{
		parent::__construct($series);
	}

	public function __toString()
	{
        return sprintf("%s%s", get_class($this),  $this->series->SeriesId());
    }
}
?>