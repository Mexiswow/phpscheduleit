<?php
/**
 * Created by JetBrains PhpStorm.
 * User: NKorbel
 * Date: 10/27/11
 * Time: 12:50 PM
 * To change this template use File | Settings | File Templates.
 */
 
class ResourceServiceTests extends TestBase
{

	public function testResourceServiceChecksPermissionForEachResource()
	{
		$scheduleId = 100;
		$user = $this->fakeUser;

		$permissionService = $this->getMock('IPermissionService');
		$resourceRepository = $this->getMock('IResourceRepository');

		$resourceService = new ResourceService($resourceRepository, $permissionService);

		$resource1 = new FakeBookableResource(1, 'resource1');
		$resource2 = new FakeBookableResource(2, 'resource2');
		$resource3 = new FakeBookableResource(3, 'resource3');
		$resource4 = new FakeBookableResource(4, 'resource4');
		$resources = array($resource1, $resource2, $resource3, $resource4);

		$resourceRepository->expects($this->once())
			->method('GetScheduleResources')
			->with($this->equalTo($scheduleId))
			->will($this->returnValue($resources));

		$permissionService->expects($this->at(0))
			->method('CanAccessResource')
			->with($this->equalTo($resource1), $this->equalTo($user))
			->will($this->returnValue(true));

		$permissionService->expects($this->at(1))
			->method('CanAccessResource')
			->with($this->equalTo($resource2), $this->equalTo($user))
			->will($this->returnValue(true));

		$permissionService->expects($this->at(2))
			->method('CanAccessResource')
			->with($this->equalTo($resource3), $this->equalTo($user))
			->will($this->returnValue(true));

		$permissionService->expects($this->at(3))
			->method('CanAccessResource')
			->with($this->equalTo($resource4), $this->equalTo($user))
			->will($this->returnValue(false));

		$resourceDto1 = new ResourceDto(1, 'resource1', true);
		$resourceDto2 = new ResourceDto(2, 'resource2', true);
		$resourceDto3 = new ResourceDto(3, 'resource3', true);
		$resourceDto4 = new ResourceDto(4, 'resource4', false);

		$expected = array($resourceDto1, $resourceDto2, $resourceDto3, $resourceDto4);

		$actual = $resourceService->GetScheduleResources($scheduleId, true, $user);

		$this->assertEquals($expected, $actual);
	}

	public function testResourcesAreNotReturnedIfNotIncludingInaccessibleResources()
	{
		$scheduleId = 100;
		$user = $this->fakeUser;

		$permissionService = $this->getMock('IPermissionService');
		$resourceRepository = $this->getMock('IResourceRepository');

		$resourceService = new ResourceService($resourceRepository, $permissionService);

		$resource1 = new FakeBookableResource(1, 'resource1');

		$resourceRepository->expects($this->once())
			->method('GetScheduleResources')
			->with($this->equalTo($scheduleId))
			->will($this->returnValue(array($resource1)));

		$permissionService->expects($this->at(0))
			->method('CanAccessResource')
			->with($this->equalTo($resource1))
			->will($this->returnValue(false));

		$includeInaccessibleResources = false;
		$actual = $resourceService->GetScheduleResources($scheduleId, $includeInaccessibleResources, $user);

		$this->assertEquals(0, count($actual));
	}


	public function testGetsAccessoriesFromRepository()
	{
		$accessoryDtos = array(new AccessoryDto(4, "lksjdf", 23));
		
		$resourceRepository = $this->getMock('IResourceRepository');
		
		$resourceService = new ResourceService($resourceRepository, null);

		$resourceRepository->expects($this->once())
				->method('GetAccessoryList')
				->will($this->returnValue($accessoryDtos));

		$actualAccessories = $resourceService->GetAccessories();

		$this->assertEquals($accessoryDtos, $actualAccessories);
	}
}


?>