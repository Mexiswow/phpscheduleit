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

require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Pages/Reports/IDisplayableReportPage.php');

interface ICommonReportsPage extends IDisplayableReportPage, IActionPage
{

}

class CommonReportsPage extends ActionPage implements ICommonReportsPage
{
	public function __construct()
	{
		parent::__construct('CommonReports', 1);


	}

	/**
	 * @return void
	 */
	public function ProcessAction()
	{
	}

	/**
	 * @param $dataRequest string
	 * @return void
	 */
	public function ProcessDataRequest($dataRequest)
	{
		// no-op
	}

	/**
	 * @return void
	 */
	public function ProcessPageLoad()
	{
		$this->Display('Reports/common-reports.tpl');
	}

	/**
	 * @param SavedReport[] $reportList
	 */
	public function BindReportList($reportList)
	{
	}

	public function BindReport(IReport $report, IReportDefinition $definition)
	{
		$this->Set('HideSave', true);
		$this->Set('Definition', $definition);
		$this->Set('Report', $report);
	}

	public function ShowCsv()
	{
		header("Content-Type: text/csv");
		header("Content-Disposition: inline; filename=report.csv");
		$this->Display('Reports/custom-csv.tpl');
	}

	public function DisplayError()
	{
		$this->Display('Reports/error.tpl');
	}

	public function ShowResults()
	{
		$this->Display('Reports/results-custom.tpl');
	}

	public function PrintReport()
	{
		$this->Display('Reports/print-custom-report.tpl');
	}

	/**
	 * @return int
	 */
	public function GetReportId()
	{
		return $this->GetQuerystring(QueryStringKeys::REPORT_ID);
	}

	/**
	 * @param string $emailAddress
	 */
	public function SetEmailAddress($emailAddress)
	{
		$this->Set('UserEmail', $emailAddress);
	}

	/**
	 * @return string
	 */
	public function GetEmailAddress()
	{
		return $this->GetForm(FormKeys::EMAIL);
	}
}

?>