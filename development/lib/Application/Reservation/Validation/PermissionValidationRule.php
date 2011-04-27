<?php
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');

class PermissionValidationRule implements IReservationValidationRule
{
	/**
	 * @var IPermissionServiceFactory
	 */
	private $permissionServiceFactory;

	public function __construct(IPermissionServiceFactory $permissionServiceFactory)
	{
		$this->permissionServiceFactory = $permissionServiceFactory;
	}

	/**
	 * @see IReservationValidationRule::Validate()
	 */
	public function Validate($reservation)
	{		
		$permissionService = $this->permissionServiceFactory->GetPermissionService($reservation->UserId());

		$resourceId = $reservation->ResourceId();

		if ($permissionService->CanAccessResource(new ReservationResource($resourceId)))
		{
			foreach ($reservation->Resources() as $resourceId)
			{
				if (!$permissionService->CanAccessResource(new ReservationResource($resourceId)))
				{
					return new ReservationRuleResult(false, Resources::GetInstance()->GetString('NoResourcePermission'));
				}
			}
		}

		return new ReservationRuleResult(true);
	}
}
?>