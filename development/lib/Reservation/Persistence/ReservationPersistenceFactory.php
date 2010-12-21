<?php
class ReservationPersistenceFactory implements IReservationPersistenceFactory 
{
	private $services = array();
	private $creationStrategies = array();
	
	public function __construct()
	{
		$this->creationStrategies[ReservationAction::Create] = 'CreateAddService';
		$this->creationStrategies[ReservationAction::Update] = 'CreateUpdateService';
	}
	
	/**
	 * @param string $reservationAction 
	 * @return IReservationPersistenceService
	 */
	public function Create($reservationAction)
	{
		if (!array_key_exists($reservationActionm, $this->services))
		{
			$this->AddCachedService($reservationAction);
		}
		
		return $this->services[$reservationAction];
	}
	
	private function AddCachedService($reservationAction)
	{
		$createMethod = $this->creationStrategies[$reservationAction];
		$this->services[$reservationAction] = $this->{$createMethod}();
	}
	
	private function CreateAddService()
	{
		return new AddReservationPersistenceService(new ReservationRepository()); 
	}
	
	private function CreateUpdateService()
	{
		return UpdateReservationPersistenceService(new ReservationRepository());
	}
}
?>