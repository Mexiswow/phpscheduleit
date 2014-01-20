<?php
/**
Copyright 2012-2014 Nick Korbel

This file is part of Booked Scheduler.

Booked Scheduler is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Booked Scheduler is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Booked Scheduler.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');
require_once(ROOT_DIR . 'WebServices/Responses/ResourceResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/ResourcesResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/CustomAttributeResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/Resource/ResourceStatusResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/Resource/ResourceStatusReasonsResponse.php');

class ResourcesWebService
{
	/**
	 * @var IRestServer
	 */
	private $server;

	/**
	 * @var IResourceRepository
	 */
	private $resourceRepository;

	/**
	 * @var IAttributeService
	 */
	private $attributeService;

	public function __construct(IRestServer $server, IResourceRepository $resourceRepository,
								IAttributeService $attributeService)
	{
		$this->server = $server;
		$this->resourceRepository = $resourceRepository;
		$this->attributeService = $attributeService;
	}

	/**
	 * @name GetAllResources
	 * @description Loads all resources
	 * @response ResourcesResponse
	 * @return void
	 */
	public function GetAll()
	{
		$resources = $this->resourceRepository->GetResourceList();
		$resourceIds = array();
		foreach ($resources as $resource)
		{
			$resourceIds[] = $resource->GetId();
		}
		$attributes = $this->attributeService->GetAttributes(CustomAttributeCategory::RESOURCE, $resourceIds);
		$this->server->WriteResponse(new ResourcesResponse($this->server, $resources, $attributes));
	}

	/**
	 * @name GetResource
	 * @description Loads a specific resource by id
	 * @param int $resourceId
	 * @response ResourceResponse
	 * @return void
	 */
	public function GetResource($resourceId)
	{
		$resource = $this->resourceRepository->LoadById($resourceId);

		$resourceId = $resource->GetResourceId();
		if (empty($resourceId))
		{
			$this->server->WriteResponse(RestResponse::NotFound(), RestResponse::NOT_FOUND_CODE);
		}
		else
		{
			$attributes = $this->attributeService->GetAttributes(CustomAttributeCategory::RESOURCE, array($resourceId));
			$this->server->WriteResponse(new ResourceResponse($this->server, $resource, $attributes));
		}

	}

	/**
	 * @name GetStatuses
	 * @description Returns all available resource statuses
	 * @response ResourceStatusResponse
	 * @return void
	 */
	public function GetStatuses()
	{
		$this->server->WriteResponse(new ResourceStatusResponse());
	}

	/**
	 * @name GetStatusReasons
	 * @description Returns all available resource status reasons
	 * @response ResourceStatusReasonsResponse
	 * @return void
	 */
	public function GetStatusReasons()
	{
		$reasons = $this->resourceRepository->GetStatusReasons();

		$this->server->WriteResponse(new ResourceStatusReasonsResponse($this->server, $reasons));
	}
}

?>