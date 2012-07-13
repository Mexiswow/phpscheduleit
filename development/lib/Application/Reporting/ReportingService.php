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

require_once(ROOT_DIR . 'lib/Application/Reporting/namespace.php');

interface IReportingService
{
	/**
	 * @abstract
	 * @param Report_Usage $usage
	 * @param Report_ResultSelection $selection
	 * @param Report_GroupBy $groupBy
	 * @param Report_Range $range
	 * @param Report_Filter $filter
	 * @return IReport
	 */
	public function GenerateCustomReport(Report_Usage $usage, Report_ResultSelection $selection, Report_GroupBy $groupBy, Report_Range $range, Report_Filter $filter);

	/**
	 * @abstract
	 * @param string $reportName
	 * @param int $userId
	 * @param Report_Usage $usage
	 * @param Report_ResultSelection $selection
	 * @param Report_GroupBy $groupBy
	 * @param Report_Range $range
	 * @param Report_Filter $filter
	 */
	public function Save($reportName, $userId, Report_Usage $usage, Report_ResultSelection $selection, Report_GroupBy $groupBy, Report_Range $range, Report_Filter $filter);
}


class ReportingService implements IReportingService
{
	/**
	 * @var IReportingRepository
	 */
	private $repository;

	public function __construct(IReportingRepository $repository)
	{
		$this->repository = $repository;
	}

	public function GenerateCustomReport(Report_Usage $usage, Report_ResultSelection $selection, Report_GroupBy $groupBy, Report_Range $range, Report_Filter $filter)
	{
		$builder = new ReportCommandBuilder();

		$selection->Add($builder);
		if ($selection->Equals(Report_ResultSelection::FULL_LIST))
		{
			$usage->Add($builder);
		}
		$groupBy->Add($builder);
		$range->Add($builder);
		$filter->Add($builder);

		$data = $this->repository->GetCustomReport($builder);
		return new CustomReport($data);
	}

	/**
	 * @param string $reportName
	 * @param int $userId
	 * @param Report_Usage $usage
	 * @param Report_ResultSelection $selection
	 * @param Report_GroupBy $groupBy
	 * @param Report_Range $range
	 * @param Report_Filter $filter
	 */
	public function Save($reportName, $userId, Report_Usage $usage, Report_ResultSelection $selection, Report_GroupBy $groupBy, Report_Range $range, Report_Filter $filter)
	{
		$report = new SavedReport($reportName, $userId, $usage, $selection, $groupBy, $range, $filter);
		$this->repository->SaveCustomReport($report);
	}
}

?>