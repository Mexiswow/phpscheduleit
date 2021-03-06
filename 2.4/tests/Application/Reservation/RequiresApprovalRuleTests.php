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

class RequiresApprovalRuleTests extends TestBase
{
	/**
	 * @var IUserRepository|PHPUnit_Framework_MockObject_MockObject
	 */
	public $userRepository;

	/**
	 * @var IAuthorizationService|PHPUnit_Framework_MockObject_MockObject
	 */
	public $authorizationService;

	/**
	 * @var RequiresApprovalRule
	 */
	public $rule;

	public function setup()
	{
		parent::setup();

		$this->authorizationService = $this->getMock('IAuthorizationService');

		$this->rule = new RequiresApprovalRule($this->authorizationService);
	}

	public function testMarksReservationAsRequiringApprovalIfUserIsNotApprover()
	{
		$series = new TestReservationSeries();
		$resource = new FakeBookableResource(1);
		$resource->RequiresApproval(true);
		$series->WithResource($resource);
		$series->WithBookedBy($this->fakeUser);

		$this->authorizationService->expects($this->once())
							->method('CanApproveForResource')
							->with($this->equalTo($this->fakeUser), $this->equalTo($resource))
							->will($this->returnValue(false));

		$this->rule->Validate($series);

		$this->assertTrue($series->RequiresApproval());
	}

	public function testDoesNotMarkAsRequiringApprovalIfNoResourceNeedsIt()
	{
		$series = new TestReservationSeries();
		$resource = new FakeBookableResource(1);
		$resource->RequiresApproval(false);
		$series->WithResource($resource);
		$series->WithBookedBy($this->fakeUser);

		$this->rule->Validate($series);

		$this->assertFalse($series->RequiresApproval());
	}

	public function testDoesNotMarkAsRequiringApprovalIfUserCanApproveForResource()
	{
		$series = new TestReservationSeries();
		$resource = new FakeBookableResource(1);
		$resource->RequiresApproval(true);
		$series->WithResource($resource);
		$series->WithBookedBy($this->fakeUser);

		$this->authorizationService->expects($this->once())
									->method('CanApproveForResource')
									->with($this->equalTo($this->fakeUser), $this->equalTo($resource))
									->will($this->returnValue(true));

		$this->rule->Validate($series);

		$this->assertFalse($series->RequiresApproval());
	}
}

?>