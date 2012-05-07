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

interface IAttributeService
{
	/**
	 * @abstract
	 * @param $category CustomAttributeCategory|int
	 * @param $entityIds array|int[]
	 * @return IEntityAttributeList
	 */
	public function GetAttributes($category, $entityIds);
}

class AttributeService implements IAttributeService
{
	/**
	 * @var IAttributeRepository
	 */
	private $attributeRepository;

	public function __construct(IAttributeRepository $attributeRepository)
	{
		$this->attributeRepository = $attributeRepository;
	}

	public function GetAttributes($category, $entityIds)
	{
		$attributeList = new AttributeList();
		$attributes = $this->attributeRepository->GetByCategory($category);
		$values = $this->attributeRepository->GetEntityValues($category, $entityIds);

		foreach ($attributes as $attribute)
		{
			$attributeList->AddDefinition($attribute);
		}

		foreach ($values as $value)
		{
			$attributeList->AddValue($value);
		}

		return $attributeList;
	}
}

?>