<?php

require_once(ROOT_DIR . 'lib/Application/Reservation/Persistence/IReservationPersistenceService.php');

interface IDeleteReservationPersistenceService extends IReservationPersistenceService
{
	/**
	 * @return ExistingReservationSeries
	 */
	function LoadByInstanceId($reservationInstanceId);
}

class DeleteReservationPersistenceService implements IDeleteReservationPersistenceService
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
		return $this->_repository->Delete($existingReservationSeries);
	}
}

?>