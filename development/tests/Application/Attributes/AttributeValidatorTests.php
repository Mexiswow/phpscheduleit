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

require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');

class AttributeValidatorTests extends TestBase
{
	public function testChecksAttributesAgainstService()
	{

		$service = $this->getMock('IAttributeService');
		$category = CustomAttributeCategory::RESOURCE;
		$attributes = array();

		$errors = array('error1', 'error2');

		$serviceResult = new AttributeServiceValidationResult(false, $errors);

		$service->expects($this->once())
				->method('Validate')
				->with($this->equalTo($category), $this->equalTo($attributes))
				->will($this->returnValue($serviceResult));

		$validator = new AttributeValidator($service, $category, $attributes);
		$validator->Validate();

		$this->assertFalse($validator->IsValid());
		$this->assertEquals($errors, $validator->Messages());

	}
}
?>