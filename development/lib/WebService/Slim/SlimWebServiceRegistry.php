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

require_once(ROOT_DIR . 'lib/external/Slim/Slim.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');

class SlimWebServiceRegistry
{
	/**
	 * @var Slim\Slim
	 */
	private $slim;

	/**
	 * @var array|SlimWebServiceRegistryCategory[]
	 */
	private $categories = array();

	/**
	 * @var array
	 */
	private $secureRoutes = array();

	public function __construct(Slim\Slim $slim)
	{
		$this->slim = $slim;
	}

	/**
	 * @param SlimWebServiceRegistryCategory $category
	 */
	public function AddCategory(SlimWebServiceRegistryCategory $category)
	{
		foreach ($category->Gets() as $registration)
		{
			$this->slim->get($registration->Route(), $registration->Callback())->name($registration->RouteName());
			if ($registration->IsSecure())
			{
				$this->secureRoutes[$registration->RouteName()] = true;
			}
		}

		foreach ($category->Posts() as $registration)
		{
			$this->slim->post($registration->Route(), $registration->Callback())->name($registration->RouteName());
			if ($registration->IsSecure())
			{
				$this->secureRoutes[$registration->RouteName()] = true;
			}
		}

		$this->categories[] = $category;
	}

	/**
	 * @return array|SlimWebServiceRegistryCategory[]
	 */
	public function Categories()
	{
		$categories = $this->categories;

		usort($categories,  function ($a, $b) {
			/**
			 * @var $a SlimWebServiceRegistryCategory
			 * @var $b SlimWebServiceRegistryCategory
			 */

			return ($a->Name() < $b->Name()) ? -1 : 1;
		});

		return $categories;
	}

	/**
	 * @param string $routeName
	 * @return bool
	 */
	public function IsSecure($routeName)
	{
		return array_key_exists($routeName, $this->secureRoutes);
	}
}

?>