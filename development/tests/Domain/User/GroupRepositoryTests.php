<?php

require_once(ROOT_DIR . 'Domain/Access/namespace.php');

class GroupRepositoryTests extends TestBase
{
	/**
	 * @var GroupRepository
	 */
	private $repository;
	
	public function setup()
	{
		parent::setup();

		$this->repository = new GroupRepository();
	}

	public function teardown()
	{
		parent::teardown();
	}

	public function testCanGetPageableListOfGroups()
	{
		$filter = new EqualsSqlFilter(null, null);
		$pageNum = 10;
		$pageSize = 100;
		$count = 1000;

		$countRow = array(ColumnNames::TOTAL => $count);
		$row1 = self::GetRow(1, 'g1');
		$row2 = self::GetRow(2, 'g2');
		$rows = array($row1, $row2);

		$this->db->SetRow(0, array($countRow));
		$this->db->SetRow(1, $rows);

		$baseCommand = new GetAllGroupsCommand();
		$expected = new FilterCommand($baseCommand, $filter);

		$list = $this->repository->GetList($pageNum, $pageSize, null, null, $filter);
		
		$results = $list->Results();
		$this->assertEquals(GroupItemView::Create($row1), $results[0]);
		$this->assertEquals(GroupItemView::Create($row2), $results[1]);
		$this->assertEquals($this->db->ContainsCommand($expected), "missing select group command");

		$pageInfo = $list->PageInfo();
		$this->assertEquals($count, $pageInfo->Total);
		$this->assertEquals($pageNum, $pageInfo->CurrentPage);
	}

	public function testCanGetGroupUsers()
	{
		$rows[] = $this->GetGroupUserRow(1, 'f', 'l', 1);
		$rows[] = $this->GetGroupUserRow(2, '2f', '2l', 2);
		$this->db->SetRow(0, array(array(ColumnNames::TOTAL => 20)));
		$this->db->SetRow(1, $rows);

		$groupId = 50;
		$users = $this->repository->GetUsersInGroup($groupId);

		$actualCommand = $this->db->_LastCommand;

		$this->assertEquals(new GetAllGroupUsersCommand($groupId), $actualCommand);

		$results = $users->Results();
		$this->assertEquals(2, count($results));
		$this->assertEquals(1, $results[0]->UserId);
	}

	public function testCanLoadById()
	{
		$groupId = 98282;
		$groupName = 'gn';

		$rows = array();
		$rows[] = $this->GetRow($groupId, $groupName);
		$groupUsers = array(
			array(ColumnNames::USER_ID => 1, ColumnNames::ROLE_ID => GroupRoles::User),
			array(ColumnNames::USER_ID => 2, ColumnNames::ROLE_ID => GroupRoles::Admin),
		);
		$permissions = array (
			array(ColumnNames::GROUP_ID => 1, ColumnNames::RESOURCE_ID => 1),
			array(ColumnNames::GROUP_ID => 1, ColumnNames::RESOURCE_ID => 2),

		);
		$this->db->SetRow(0, $rows);
		$this->db->SetRow(1, $groupUsers);
		$this->db->SetRow(2, $permissions);

		$group = $this->repository->LoadById($groupId);

		$expectedGroupCommand = new GetGroupByIdCommand($groupId);
		$expectedUsersCommand = new GetAllGroupUsersCommand($groupId);
		$expectedPermissionsCommand = new GetAllGroupPermissionsCommand($groupId);

		$this->assertTrue($this->db->ContainsCommand($expectedGroupCommand));
		$this->assertTrue($this->db->ContainsCommand($expectedUsersCommand));
		$this->assertTrue($this->db->ContainsCommand($expectedPermissionsCommand));
		$this->assertEquals($groupId, $group->Id());
		$this->assertEquals($groupName, $group->Name());
		$this->assertTrue($group->HasMember(1));
		$this->assertFalse($group->HasMember(3));
		$this->assertTrue(in_array(1, $group->AllowedResourceIds()));
		$this->assertFalse(in_array(3, $group->AllowedResourceIds()));
	}

	public function testUpdateRemovesAllUsersMarked()
	{
		$user1 = 100;
		$user2 = 200;
		$groupId = 9298;

		$group = new Group($groupId, '');
		$group->WithUser($user1);
		$group->WithUser($user2);

		$group->RemoveUser($user1);
		$group->RemoveUser($user2);

		$this->repository->Update($group);

		$removeCommand1 = new DeleteUserGroupCommand($user1, $groupId);
		$removeCommand2 = new DeleteUserGroupCommand($user2, $groupId);

		$this->assertTrue($this->db->ContainsCommand($removeCommand1));
		$this->assertTrue($this->db->ContainsCommand($removeCommand2));
	}

	public function testUpdateAddsAllUsersMarked()
	{
		$user1 = 100;
		$user2 = 200;
		$groupId = 9298;

		$group = new Group($groupId, '');

		$group->AddUser($user1);
		$group->AddUser($user2);

		$this->repository->Update($group);

		$command1 = new AddUserGroupCommand($user1, $groupId);
		$command2 = new AddUserGroupCommand($user2, $groupId);

		$this->assertTrue($this->db->ContainsCommand($command1));
		$this->assertTrue($this->db->ContainsCommand($command2));
	}
	
	public function testUpdateAddsAllNewAndRemovesAllDeletedPermissions()
	{
		$resource1 = 100;
		$resource2 = 200;
		
		$groupId = 9298;

		$group = new Group($groupId, '');
		$group->WithPermission($resource1);

		$group->ChangePermissions(array($resource2));

		$this->repository->Update($group);

		$removeCommand1 = new DeleteGroupResourcePermission($groupId, $resource1);
		$removeCommand2 = new AddGroupResourcePermission($groupId, $resource2);

		$this->assertTrue($this->db->ContainsCommand($removeCommand1));
		$this->assertTrue($this->db->ContainsCommand($removeCommand2));
	}
	
	public static function GetRow($groupId, $groupName)
	{
		return array(ColumnNames::GROUP_ID => $groupId, ColumnNames::GROUP_NAME => $groupName);
	}

	private function GetGroupUserRow($userId, $firstName, $lastName, $roleId)
	{
		return array(
			ColumnNames::USER_ID => $userId,
			ColumnNames::FIRST_NAME => $firstName,
			ColumnNames::LAST_NAME => $lastName,
			ColumnNames::ROLE_ID => $roleId,
		);
	}
}
?>