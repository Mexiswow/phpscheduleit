<?php
/**
Copyright 2011-2013 Nick Korbel

This file is part of phpScheduleIt.

phpScheduleIt is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

phpScheduleIt is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with phpScheduleIt.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once(ROOT_DIR . 'Presenters/Reservation/ReservationHandler.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');

class ReservationApprovalPresenter
{
	/**
	 * @var IReservationApprovalPage
	 */
	private $page;

	/**
	 * @var \IUpdateReservationPersistenceService
	 */
	private $persistenceService;

	/**
	 * @var \IReservationHandler
	 */
	private $handler;
	/**
	 * @var IReservationAuthorization
	 */
	private $authorization;

	public function __construct(
		IReservationApprovalPage $page,
		IUpdateReservationPersistenceService $persistenceService,
		IReservationHandler $handler,
		IReservationAuthorization $authorizationService)
	{
		$this->page = $page;
		$this->persistenceService = $persistenceService;
		$this->handler = $handler;
		$this->authorization = $authorizationService;
	}

	public function PageLoad()
	{
		$referenceNumber = $this->page->GetReferenceNumber();
		$userSession = ServiceLocator::GetServer()->GetUserSession();

		Log::Debug('User: %s, Approving reservation with reference number %s', $userSession->UserId, $referenceNumber);

		$series = $this->persistenceService->LoadByReferenceNumber($referenceNumber);
		if($this->authorization->CanApprove(new ReservationViewAdapter($series), $userSession))
		{
			$series->Approve($userSession);
			$this->handler->Handle($series, $this->page);
		}
	}
}

class ReservationViewAdapter extends ReservationView
{
	public function __construct(ExistingReservationSeries $series)
	{
		foreach ($series->Accessories() as $accessory)
		{
			$this->Accessories[] = new ReservationAccessoryView($accessory->AccessoryId, $accessory->QuantityReserved, $accessory->Name, null);
		}

		foreach($series->AdditionalResources() as $resource)
		{
			$this->AdditionalResourceIds[] = $resource->GetId();
		}

		foreach($series->AddedAttachments() as $attachment)
		{
			$this->Attachments[] = new ReservationAttachmentView($attachment->FileId(), $series->SeriesId(), $attachment->FileName());
		}

		foreach($series->AttributeValues() as $av)
		{
			$this->Attributes[] = $av;
		}

		$this->Description = $series->Description();
		$this->EndDate = $series->CurrentInstance()->EndDate();
		$this->OwnerId = $series->UserId();
		$this->ReferenceNumber = $series->CurrentInstance()->ReferenceNumber();
		$this->ReservationId = $series->CurrentInstance()->ReservationId();
		$this->ResourceId = $series->ResourceId();

		foreach($series->AllResources() as $resource)
		{
			$this->Resources[] = new ReservationResourceView($resource->GetId(), $resource->GetName(), $resource->GetAdminGroupId(), $resource->GetScheduleId(), $resource->GetScheduleAdminGroupId(), $resource->GetStatusId());
		}

		$this->ScheduleId = $series->ScheduleId();
		$this->SeriesId = $series->SeriesId();
		$this->StartDate = $series->CurrentInstance()->StartDate();
		$this->StatusId = $series->StatusId();
	}
}

?>