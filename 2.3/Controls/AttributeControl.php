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

require_once(ROOT_DIR . 'Controls/Control.php');

class AttributeControl extends Control
{
	public function __construct(SmartyPage $smarty)
	{
		parent::__construct($smarty);
	}
	
	public function PageLoad()
	{
		$templates[CustomAttributeTypes::CHECKBOX] = 'Checkbox.tpl';
		$templates[CustomAttributeTypes::MULTI_LINE_TEXTBOX] = 'MultiLineTextbox.tpl';
		$templates[CustomAttributeTypes::SELECT_LIST] = 'SelectList.tpl';
		$templates[CustomAttributeTypes::SINGLE_LINE_TEXTBOX] = 'SingleLineTextbox.tpl';
		/** @var $attribute Attribute */
		$attribute = $this->Get('attribute');

		$this->Set('attributeName', sprintf('%s[%s]', FormKeys::ATTRIBUTE_PREFIX, $attribute->Id()));
		$this->Display('Controls/Attributes/' . $templates[$attribute->Type()]);
	}
}
?>