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

require_once(ROOT_DIR . 'WebServices/ReservationWriteWebService.php');

class ReservationWriteWebServiceTests extends TestBase
{
	/**
	 * @var ReservationWriteWebService
	 */
	private $service;

	/**
	 * @var FakeRestServer
	 */
	private $server;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|IReservationSaveController
	 */
	private $controller;

	public function setup()
	{
		parent::setup();

		$this->server = new FakeRestServer();
		$this->controller = $this->getMock('IReservationSaveController');

		$this->service = new ReservationWriteWebService($this->server, $this->controller);
	}

	public function testCreatesNewReservation()
	{
		$reservationRequest = $this->GetReservationRequest();
		$this->server->SetRequest($reservationRequest);

		$referenceNumber = '12323';
		$controllerResult = new ReservationControllerResult($reservationRequest);
		$controllerResult->SetReferenceNumber($referenceNumber);

		$this->controller->expects($this->once())
				->method('Create')
				->with($this->equalTo($reservationRequest), $this->equalTo($this->server->GetSession()))
				->will($this->returnValue($controllerResult));

		$this->service->Create();

		$expectedResponse = new ReservationCreatedResponse($this->server, $referenceNumber);
		$this->assertEquals($expectedResponse, $this->server->_LastResponse);
		$this->assertEquals(RestResponse::CREATED_CODE, $this->server->_LastResponseCode);
	}

	public function testWhenCreationValidationFails()
	{
		$reservationRequest = new ReservationRequest();
		$this->server->SetRequest($reservationRequest);

		$errors = array('error');
		$controllerResult = new ReservationControllerResult($reservationRequest);
		$controllerResult->SetErrors($errors);

		$this->controller->expects($this->once())
				->method('Create')
				->with($this->equalTo($reservationRequest), $this->equalTo($this->server->GetSession()))
				->will($this->returnValue($controllerResult));

		$this->service->Create();

		$expectedResponse = new ReservationFailedResponse($this->server, $errors);
		$this->assertEquals($expectedResponse, $this->server->_LastResponse);
		$this->assertEquals(RestResponse::BAD_REQUEST_CODE, $this->server->_LastResponseCode);
	}

	private function GetReservationRequest()
	{
		$request = new ReservationRequest();
		$endDate = Date::Parse('2012-11-20 05:30', 'UTC');
		$startDate = Date::Parse('2012-11-18 02:30', 'UTC');
		$repeatTerminationDate = Date::Parse('2012-12-13', 'UTC');

		$accessoryId = 8912;
		$quantity = 1232;
		$attributeId = 3393;
		$attributeValue = '23232';
		$description = 'reservation description';
		$invitees = array(9,8);
		$participants = array(99,88);
		$repeatInterval = 1;
		$repeatMonthlyType = null;
		$repeatType = RepeatType::Weekly;
		$repeatWeekdays = array(0,4,5);
		$resourceId = 122;
		$resources = array(22,23,33);
		$title = 'reservation title';
		$userId = 1;

		$request->accessories = array(new ReservationAccessoryRequest($accessoryId, $quantity));
		$request->attributes = array(new AttributeValueRequest($attributeId, $attributeValue));
		$request->description = $description;
		$request->endDateTime = $endDate->ToIso();
		$request->invitees = $invitees;
		$request->participants = $participants;
		$request->repeatInterval = $repeatInterval;
		$request->repeatMonthlyType = $repeatMonthlyType;
		$request->repeatType = $repeatType;
		$request->repeatWeekdays = $repeatWeekdays;
		$request->repeatTerminationDate = $repeatTerminationDate->ToIso();
		$request->resourceId = $resourceId;
		$request->resources = $resources;
		$request->startDateTime = $startDate->ToIso();
		$request->title = $title;
		$request->userId = $userId;

		return $request;
	}
}

?>