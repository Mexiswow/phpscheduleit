<?php
/**
Copyright 2013 Nick Korbel

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

class ResourceGroupTree
{
	/**
	 * @var $references ResourceGroup[]
	 */
	protected $references = array();

	/**
	 * @var array|ResourceGroup[]
	 */
	protected $groups = array();

	/**
	 * @var array|ResourceDto[]
	 */
	protected $resources = array();

	public function AddGroup(ResourceGroup $group)
	{
		$this->references[$group->id] = $group;

		// It it's a root node, we add it directly to the tree
		$parent_id = $group->parent_id;
		if (empty($parent_id))
		{
			$this->groups[] = $group;
		}
		else
		{
			// It was not a root node, add this node as a reference in the parent.
			$this->references[$parent_id]->AddChild($group);
		}
	}

	public function AddAssignment(ResourceGroupAssignment $assignment)
	{
		if (array_key_exists($assignment->group_id, $this->references))
		{
			$this->resources[$assignment->resource_id] = new ResourceDto($assignment->resource_id, $assignment->resource_name);
			$this->references[$assignment->group_id]->AddResource($assignment);
		}
	}

	/**
	 * @param bool $includeDefaultGroup
	 * @return array|ResourceGroup[]
	 */
	public function GetGroups($includeDefaultGroup = true)
	{
		if ($includeDefaultGroup)
		{
			return $this->groups;
		}
		else
		{
			return array_slice($this->groups, 1);
		}
	}

	/**
	 * @param int $groupId
	 * @param int[] $resourceIds
	 * @return int[]
	 */
	public function GetResourceIds($groupId, &$resourceIds = array())
	{
		$group = $this->references[$groupId];

		if (empty($group->children))
		{
			return $resourceIds;
		}

		foreach ($group->children as $child)
		{
			if ($child->type == ResourceGroup::RESOURCE_TYPE)
			{
				$resourceIds[] = $child->resource_id;
			}
			else
			{
				$this->GetResourceIds($child->id, $resourceIds);
			}
		}

		return $resourceIds;
	}

	/**
	 * @return ResourceDto[] array of resources keyed by their ids
	 */
	public function GetAllResources()
	{
		return $this->resources;
	}
}

class ResourceGroup
{
	const RESOURCE_TYPE = 'resource';
	const GROUP_TYPE = 'group';

	public $id;
	public $name;
	public $label;
	public $parent;
	public $parent_id;
	/**
	 * @var ResourceGroup[]|ResourceGroupAssignment[]
	 */
	public $children = array();
	public $type = ResourceGroup::GROUP_TYPE;

	public function __construct($id, $name, $parentId = null)
	{
		$this->WithId($id);
		$this->SetName($name);
		$this->parent_id = $parentId;
	}

	/**
	 * @param $resourceGroup ResourceGroup
	 */
	public function AddChild(ResourceGroup &$resourceGroup)
	{
		$resourceGroup->parent_id = $this->id;
		$this->children[] = $resourceGroup;
	}

	/**
	 * @param $assignment ResourceGroupAssignment
	 */
	public function AddResource(ResourceGroupAssignment &$assignment)
	{
		$this->children[] = $assignment;
	}

	/**
	 * @param string $groupName
	 * @param int $parentId
	 * @return ResourceGroup
	 */
	public static function Create($groupName, $parentId = null)
	{
		return new ResourceGroup(null, $groupName, $parentId);
	}

	/**
	 * @param int $id
	 */
	public function WithId($id)
	{
		$this->id = $id;
	}

	public function SetName($name)
	{
		$this->name = $name;
		$this->label = $name;
	}

	/**
	 * @param int $targetId
	 */
	public function MoveTo($targetId)
	{
		$this->parent_id = $targetId;
	}

	public function Rename($newName) {
	$this->SetName($newName);
	}
}

class ResourceGroupAssignment
{
	public $type = ResourceGroup::RESOURCE_TYPE;
	public $group_id;
	public $resource_name;
	public $id;
	public $label;
	public $resource_id;

	public function __construct($group_id, $resource_name, $resource_id)
	{
		$this->group_id = $group_id;
		$this->resource_name = $resource_name;
		$this->id = "{$this->type}-{$group_id}-{$resource_id}";
		$this->label = $resource_name;
		$this->resource_id = $resource_id;
	}
}

class EmptyResourceGroupTree extends ResourceGroupTree
{
	/**
	 * @param $resources ResourceDto[]
	 */
	public function __construct($resources)
	{
		$this->AddGroup(new ResourceGroup(0, Resources::GetInstance()->GetString('All')));
		foreach($resources as $resource)
		{
			$this->AddAssignment(new ResourceGroupAssignment(0, $resource->GetName(), $resource->GetId()));
		}
	}
}
?>