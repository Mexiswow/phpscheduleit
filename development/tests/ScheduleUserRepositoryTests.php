<?php 
class ScheduleUserRepositoryTests extends TestBase
{
	public function setup()
	{
		parent::setup();
	}
	
	public function teardown()
	{
		parent::teardown();
	}
	
	public function testPullsAllResourcesAndGroupsForUser()
	{
		$userId = 10;
		
		$userResourceRoles = array
		(
			array(ColumnNames::USER_ID => $userId, ColumnNames::RESOURCE_ID => 1, ColumnNames::RESOURCE_NAME => 'r1'),
			array(ColumnNames::USER_ID => $userId, ColumnNames::RESOURCE_ID => 2, ColumnNames::RESOURCE_NAME => 'r2'),
			array(ColumnNames::USER_ID => $userId, ColumnNames::RESOURCE_ID => 3, ColumnNames::RESOURCE_NAME => 'r3'),
		);
		
		$groupResourceRoles = array
		(
			array(ColumnNames::GROUP_ID => 200, ColumnNames::RESOURCE_ID => 2, ColumnNames::RESOURCE_NAME => 'r2'),
			array(ColumnNames::GROUP_ID => 100, ColumnNames::RESOURCE_ID => 3, ColumnNames::RESOURCE_NAME => 'r3'),
			array(ColumnNames::GROUP_ID => 100, ColumnNames::RESOURCE_ID => 4, ColumnNames::RESOURCE_NAME => 'r4'),
			array(ColumnNames::GROUP_ID => 200, ColumnNames::RESOURCE_ID => 5, ColumnNames::RESOURCE_NAME => 'r5'),
		);
		
		$this->db->SetRow(0, $userResourceRoles);
		$this->db->SetRow(1, $groupResourceRoles);
		
		$repo = new ScheduleUserRepository();
		$user = $repo->GetUser($userId);
		
		$userCommand = new SelectUserPermissions($userId);
		$groupCommand = new SelectUserGroupPermissions($userId);
		
		$this->assertEquals(2, count($this->db->_Commands));
		$this->assertEquals($userCommand, $this->db->_Commands[0]);
		$this->assertEquals($groupCommand, $this->db->_Commands[1]);
	}
	
	public function testGetsAllUniqueResourcesForUserAndGroup()
	{
		$userId = 99;
		
		$rid1 = 1;
		$rid2 = 2;
		$r1 = new ScheduleResource($rid1, 'resource 1');
		$r2 = new ScheduleResource($rid2, 'resource 2');
		$resources = array($r1, $r2);
		
		$rid3 = 3;
		$rid4 = 4;
		$r3 = new ScheduleResource($rid3, 'resource 3');
		$r4 = new ScheduleResource($rid4, 'resource 4');
		
		$g1 = new ScheduleGroup(100, array($r1, $r3));
		$g2 = new ScheduleGroup(200, array($r1, $r4, $r3));
		$groups = array($g1, $g2);
		
		$user = new ScheduleUser($userId, $resources, $groups);
		
		$permittedResources = $user->GetAllResources();
		
		$this->assertEquals(4, count($permittedResources));
		$this->assertContains($r1, $permittedResources);
		$this->assertContains($r2, $permittedResources);
		$this->assertContains($r3, $permittedResources);
		$this->assertContains($r4, $permittedResources);
	}
}
?>