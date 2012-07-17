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

require_once(ROOT_DIR . 'Presenters/Reports/ReportActions.php');
require_once(ROOT_DIR . 'Presenters/Reports/ReportDefinition.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Pages/Reports/SavedReportsPage.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/namespace.php');

class SavedReportsPresenter extends ActionPresenter
{
	/**
	 * @var IReportingService
	 */
	private $service;
	/**
	 * @var UserSession
	 */
	private $user;
	/**
	 * @var ISavedReportsPage
	 */
	private $page;

	public function __construct(ISavedReportsPage $page, UserSession $user, IReportingService $service)
	{
		parent::__construct($page);

		$this->service = $service;
		$this->user = $user;
		$this->page = $page;

		$this->AddAction(ReportActions::Generate, 'GenerateReport');
	}

	public function PageLoad()
	{
		$this->page->BindReportList($this->service->GetSavedReports($this->user->UserId));
	}

	public function GenerateReport()
	{
		$reportId = $this->page->GetReportId();
		$userId = $this->user->UserId;

		$savedReport = $this->service->GetSavedReport($reportId, $userId);

		if ($savedReport != null)
		{
			Log::Debug('Loading saved report for userId: %s, reportId %s', $userId, $reportId);
			$report = $this->service->GenerateCustomReport($savedReport->Usage(), $savedReport->Selection(), $savedReport->GroupBy(), $savedReport->Range(), $savedReport->Filter());

			$this->page->BindReport($report, new ReportDefinition($report, $this->user->Timezone));
			$this->page->ShowResults();
		}
		else
		{
			Log::Debug('Report not found for userId: %s, reportId %s', $userId, $reportId);
			$this->page->DisplayError();
		}
	}
}

?>