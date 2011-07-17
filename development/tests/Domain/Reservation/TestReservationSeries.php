<?php
require_once ROOT_DIR . '/Domain/namespace.php';

class TestReservationSeries extends ReservationSeries
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function WithOwnerId($ownerId)
	{
		$this->_userId = $ownerId;
	}
	
	public function WithResourceId($resourceId)
	{
		$this->_resourceId = $resourceId;
	}
	
	public function WithDuration(DateRange $duration)
	{
		$this->UpdateDuration($duration);
	}
	
	public function WithRepeatOptions(IRepeatOptions $repeatOptions)
	{
		$this->Repeats($repeatOptions);
	}
	
	public function WithCurrentInstance(Reservation $currentInstance)
	{
		$this->SetCurrentInstance($currentInstance);
		$this->AddInstance($currentInstance);
	}

	/**
	 * @param int[] $addedParticipantIds
	 * @return void
	 */
	public function WithAddedParticipants($addedParticipantIds)
	{
		$this->addedParticipants = $addedParticipantIds;
	}

	public function WithAddedInvitees($addedInviteeIds)
	{
		$this->addedInvitees = $addedInviteeIds;
	}
}
?>