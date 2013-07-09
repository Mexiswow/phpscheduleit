<?php
/**
Copyright 2011-2013 Nick Korbel

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

interface IResourceRepository
{
	/**
	 * Gets all Resources for the given scheduleId
	 *
	 * @param int $scheduleId
	 * @return array|BookableResource[]
	 */
	public function GetScheduleResources($scheduleId);

	/**
	 * @param int $resourceId
	 * @return BookableResource
	 */
	public function LoadById($resourceId);

	/**
	 * @param string $publicId
	 * @return BookableResource
	 */
	public function LoadByPublicId($publicId);

	/**
	 * @param BookableResource $resource
	 * @return int ID of created resource
	 */
	public function Add(BookableResource $resource);

	/**
	 * @param BookableResource $resource
	 */
	public function Update(BookableResource $resource);

	/**
	 * @param BookableResource $resource
	 */
	public function Delete(BookableResource $resource);

	/**
	 * @return array|BookableResource[] array of all resources
	 */
	public function GetResourceList();

	/**
	 * @abstract
	 * @return array|AccessoryDto[] all accessories
	 */
	public function GetAccessoryList();

	/**
	 * @param int|null $scheduleId
	 * @return ResourceGroupTree
	 */
	public function GetResourceGroups($scheduleId = null);

	/**
	 * @param int $resourceId
	 * @param int $groupId
	 */
	public function AddResourceToGroup($resourceId, $groupId);

	/**
	 * @param int $resourceId
	 * @param int $groupId
	 */
	public function RemoveResourceFromGroup($resourceId, $groupId);

	/**
	 * @param ResourceGroup $group
	 * @return ResourceGroup
	 */
	public function AddResourceGroup(ResourceGroup $group);

	/**
	 * @param int $groupId
	 * @return ResourceGroup
	 */
	public function LoadResourceGroup($groupId);

	/**
	 * @param ResourceGroup $group
	 */
	public function UpdateResourceGroup(ResourceGroup $group);
}

?>