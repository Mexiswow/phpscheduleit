<?php
/**
Copyright 2011-2012 Nick Korbel

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

require_once(ROOT_DIR . 'Domain/Access/AttributeRepository.php');

class AttributeRepositoryTests extends TestBase
{
	/**
	 * @var AttributeRepository
	 */
	private $repository;

	public function setup()
	{
		parent::setup();

		$this->repository = new AttributeRepository();
	}

	public function teardown()
	{
		parent::teardown();
	}

	public function testAddsAttribute()
	{
		$label = 'label';
		$type = CustomAttributeTypes::SINGLE_LINE_TEXTBOX;
		$category = CustomAttributeCategory::RESERVATION;
		$regex = 'regex';
		$required = false;
		$possibleValues = '';

		$attribute = CustomAttribute::Create($label, $type, $category, $regex, $required, $possibleValues);

		$this->repository->Add($attribute);
		$this->assertEquals(new AddAttributeCommand($label, $type, $category, $regex, $required, $possibleValues), $this->db->_LastCommand);
	}

	public function testLoadsAttributeById()
	{
		$id = 12098;
		$label = 'label';
		$type = CustomAttributeTypes::SINGLE_LINE_TEXTBOX;
		$category = CustomAttributeCategory::RESERVATION;
		$regex = 'regex';
		$required = false;
		$possibleValues = 'val1,val2,val3';

		$row1 = $this->GetAttributeRow($id, $label, $type, $category, $regex, $required, $possibleValues);

		$this->db->SetRows(array($row1));

		$attribute = $this->repository->LoadById($id);

		$expectedFirstAttribute = new CustomAttribute($id, $label, $type, $category, $regex, $required, $possibleValues);

		$this->assertEquals($expectedFirstAttribute, $attribute);
		$this->assertEquals(new GetAttributeByIdCommand($id), $this->db->_LastCommand);

	}

	public function testUpdatesAttribute()
	{
		$id = 12098;
		$label = 'label';
		$type = CustomAttributeTypes::SINGLE_LINE_TEXTBOX;
		$category = CustomAttributeCategory::RESERVATION;
		$regex = 'regex';
		$required = false;
		$possibleValues = 'val1,val2,val3';

		$attribute = new CustomAttribute($id, $label, $type, $category, $regex, $required, $possibleValues);

		$this->repository->Update($attribute);

		$this->assertEquals(new UpdateAttributeCommand($id, $label, $type, $category, $regex, $required, $possibleValues), $this->db->_LastCommand);
	}

	public function testLoadsAttributesByCategory()
	{
		$id = 12098;
		$label = 'label';
		$type = CustomAttributeTypes::SINGLE_LINE_TEXTBOX;
		$category = CustomAttributeCategory::RESERVATION;
		$regex = 'regex';
		$required = false;
		$possibleValues = 'val1,val2,val3';

		$row1 = $this->GetAttributeRow($id, $label, $type, $category, $regex, $required, $possibleValues);
		$row2 = $this->GetAttributeRow(2);
		
		$this->db->SetRows(array($row1, $row2));

		$attributes = $this->repository->GetByCategory(CustomAttributeCategory::RESERVATION);

		$expectedFirstAttribute = new CustomAttribute($id, $label, $type, $category, $regex, $required, $possibleValues);

		$this->assertEquals(2, count($attributes));
		$this->assertEquals($expectedFirstAttribute, $attributes[0]);
		$this->assertEquals(new GetAttributesByCategoryCommand(CustomAttributeCategory::RESERVATION), $this->db->_LastCommand);
	}

	private function GetAttributeRow($id,
									 $label = '',
									 $type = CustomAttributeTypes::SINGLE_LINE_TEXTBOX,
									 $category = CustomAttributeCategory::RESERVATION,
									 $regex = null,
									 $required = true,
									 $possibleValues = null)
	{
		return array(
			ColumnNames::ATTRIBUTE_ID => $id,
			ColumnNames::ATTRIBUTE_LABEL => $label,
			ColumnNames::ATTRIBUTE_TYPE => $type,
			ColumnNames::ATTRIBUTE_CATEGORY => $category,
			ColumnNames::ATTRIBUTE_CONSTRAINT => $regex,
			ColumnNames::ATTRIBUTE_REQUIRED => $required,
			ColumnNames::ATTRIBUTE_POSSIBLE_VALUES => $possibleValues);
	}
}

?>