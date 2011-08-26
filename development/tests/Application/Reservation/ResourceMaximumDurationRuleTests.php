<?php
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'tests/Domain/Reservation/TestReservationSeries.php');

class ResourceMaximumDurationRuleTests extends TestBase
{
	public function setup()
	{
		parent::setup();
	}

	public function teardown()
	{
		parent::teardown();
	}
	
	public function testNotValidIfTheReservationIsLongerThanTheMaximumDurationForAnyResource()
	{
		$resourceId1 = 1;
		$resourceId2 = 2;
		
		$resource1 = new FakeBookableResource($resourceId1, "1");
		$resource1->SetMaxLength(null);
		
		$resource2 = new FakeBookableResource($resourceId2, "2");
		$resource2->SetMaxLength("23:00");
		
		$reservation = new TestReservationSeries();
	
		$duration = new DateRange(Date::Now(), Date::Now()->AddDays(1));
		$reservation->WithDuration($duration);
		$reservation->WithResource($resource1);
		$reservation->AddResource($resource2);
		
		$rule = new ResourceMaximumDurationRule();
		$result = $rule->Validate($reservation);
		
		$this->assertFalse($result->IsValid());
	}
	
	public function testOkIfReservationIsShorterThanTheMaximumDuration()
	{
		$resource = new FakeBookableResource(1, "2");
		$resource->SetMaxLength("25:00");
			
		$reservation = new TestReservationSeries();
		$reservation->WithResource($resource);
		
		$duration = new DateRange(Date::Now(), Date::Now()->AddDays(1));
		$reservation->WithDuration($duration);
		
		$rule = new ResourceMaximumDurationRule();
		$result = $rule->Validate($reservation);
		
		$this->assertTrue($result->IsValid());
	}
}
?>