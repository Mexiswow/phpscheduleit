<?php
class PageableDataStore
{
	/**
	 * @param SqlCommand $command
	 * @param function $listBuilder callback to for each row of results
	 * @param int $pageNumber
	 * @param int $pageSize
	 * @param string $sortField
	 * @param string $sortDirection
	 * @return PageableData
	 */
	public function GetList($command, $listBuilder, $pageNumber, $pageSize, $sortField = null, $sortDirection = null)
	{
		$total = 0;
		$results = array();
		$db = ServiceLocator::GetDatabase();

		$totalReader = $db->Query(new CountCommand($command));
		if ($row = $totalReader->GetRow())
		{
			$total = $row['total'];
		}

		$resultReader = $db->LimitQuery($command, $pageSize, ($pageNumber - 1) * $pageSize);
		while ($row = $resultReader->GetRow())
		{
			$results[] = $listBuilder($row);
		}
		$resultReader->Free();

		return new PageableData($results, $total, $pageNumber, $pageSize);
	}
}

class PageableData
{
	private $results = array();

	public function __construct($results = array(), $total = 0, $pageNumber = 1, $pageSize = 1)
	{
		$this->results = $results;
		$this->pageInfo = new PageInfo($total, $pageNumber, $pageSize);
	}

	public function Results()
	{
		return $this->results;
	}

	public function PageInfo()
	{
		return $this->pageInfo;
	}
}

class PageInfo
{
	public $Total = 0;
	public $TotalPages = 0;
	public $PageSize = 0;
	public $CurrentPage = 0;

	public function __construct($totalResults, $pageNumber, $pageSize)
	{
		$this->Total = $totalResults;
		$this->CurrentPage = $pageNumber;
		$this->PageSize = $pageSize;
		$this->TotalPages = ceil($totalResults/$pageSize);
	}
}

?>
