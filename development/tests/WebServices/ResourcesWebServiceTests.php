<?php
/**
Copyright 2012 Nick Korbel

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

require_once(ROOT_DIR . 'WebServices/ResourcesWebService.php');

class ResourcesWebServiceTests extends TestBase
{
	/**
	 * @var FakeRestServer
	 */
	private $server;

	/**
	 * @var IResourceRepository|PHPUnit_Framework_MockObject_MockObject
	 */
	private $repository;

	/**
	 * @var IAttributeService|PHPUnit_Framework_MockObject_MockObject
	 */
	private $attributeService;

	/**
	 * @var ResourcesWebService
	 */
	private $service;

	public function setup()
	{
		parent::setup();

		$this->server = new FakeRestServer();
		$this->repository = $this->getMock('IResourceRepository');
		$this->attributeService = $this->getMock('IAttributeService');

		$this->service = new ResourcesWebService($this->server, $this->repository, $this->attributeService);
	}

	public function testGetsResourceById()
	{
		$resourceId = 8282;
		$resource = new FakeBookableResource($resourceId);
		$attributes = $this->getMock('IEntityAttributeList');

		$this->repository->expects($this->once())
				->method('LoadById')
				->with($this->equalTo($resourceId))
				->will($this->returnValue($resource));

		$this->attributeService->expects($this->once())
				->method('GetAttributes')
				->with($this->equalTo(CustomAttributeCategory::RESOURCE), $this->equalTo(array($resourceId)))
				->will($this->returnValue($attributes));

		$this->service->GetResource($resourceId);

		$this->assertEquals(ResourceResponse::Create($this->server, $resource, $attributes), $this->server->_LastResponse);
	}

	public function testWhenNotFound()
	{
		$resourceId = 8282;
		$this->repository->expects($this->once())
				->method('LoadById')
				->with($this->equalTo($resourceId))
				->will($this->returnValue(BookableResource::Null()));

		$this->service->GetResource($resourceId);

		$this->assertEquals(RestResponse::NotFound(), $this->server->_LastResponse);
	}

	public function testGetsResourceList()
	{
		$resourceId = 123;
		$resources[] = new FakeBookableResource($resourceId);
		$attributes = $this->getMock('IEntityAttributeList');

		$this->repository->expects($this->once())
				->method('GetResourceList')
				->will($this->returnValue($resources));

		$this->attributeService->expects($this->once())
				->method('GetAttributes')
				->with($this->equalTo(CustomAttributeCategory::RESOURCE), $this->equalTo(array($resourceId)))
				->will($this->returnValue($attributes));

		$this->service->GetAll();

		$this->assertEquals(ResourcesResponse::Create($this->server, $resources, $attributes), $this->server->_LastResponse);
	}
}

?>