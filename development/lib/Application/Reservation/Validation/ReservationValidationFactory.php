<?php
class ReservationValidationFactory implements IReservationValidationFactory
{
	public function Create($reservationAction, $userSession)
	{
		$dateTimeRule = new ReservationDateTimeRule();
		$permissionRule = new PermissionValidationRule(new PermissionServiceFactory(), $userSession);
		$reservationRepository = new ReservationRepository();
		
		if ($reservationAction == ReservationAction::Update)
		{
			$rules = array(
				$dateTimeRule,
				$permissionRule,
				new ExistingResourceAvailabilityRule($reservationRepository, $userSession->Timezone),
			);
			return new UpdateReservationValidationService($rules);	
		}
		else if ($reservationAction == ReservationAction::Delete)
		{
			$rules = array();
			return new DeleteReservationValidationService($rules);
		}	
		else 
		{
			$rules = array(
				$dateTimeRule,
				$permissionRule,
				new ResourceAvailabilityRule($reservationRepository, $userSession->Timezone),
			);
			//length, start time buffer, end time buffer (quota?)
			//$rules[] = new QuotaRule();
			//$rules[] = new AccessoryAvailabilityRule();
			
			return new AddReservationValidationService($rules);
		}
	}
}
?>