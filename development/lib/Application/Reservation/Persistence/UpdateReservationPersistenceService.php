<?php

require_once(ROOT_DIR . 'lib/Application/Reservation/Persistence/IReservationPersistenceService.php');

interface IUpdateReservationPersistenceService extends IReservationPersistenceService
{
	/**
	 * @return ExistingReservationSeries
	 */
	function LoadByInstanceId($reservationInstanceId);
}

class UpdateReservationPersistenceService implements IUpdateReservationPersistenceService
{
	/**
	 * @var IReservationRepository
	 */
	private $_repository;
	
	public function __construct(IReservationRepository $repository)
	{
		$this->_repository = $repository;
	}
	
	public function LoadByInstanceId($reservationInstanceId)
	{
		return $this->_repository->LoadById($reservationInstanceId);
	}
	
	public function Persist($existingReservationSeries)
	{
		$this->_repository->Update($existingReservationSeries);
	}
}

?>