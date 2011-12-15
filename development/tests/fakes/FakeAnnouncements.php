<?php
require_once(ROOT_DIR . 'Domain/Access/AnnouncementRepository.php');

class FakeAnnouncementRepository implements IAnnouncementRepository
{
	public $_GetFutureCalled = false;
	public $_ExpectedAnnouncements;
	
	public function __construct()
	{
		$this->_ExpectedAnnouncements = $this->GetExpectedRows();
	}
	
	public function GetRows()
	{
		return array(
			array (ColumnNames::ANNOUNCEMENT_TEXT => 'announcement 1'),
			array (ColumnNames::ANNOUNCEMENT_TEXT => 'announcement 2'),
			array (ColumnNames::ANNOUNCEMENT_TEXT => 'announcement 3')
		);
	}
	
	public function GetFuture()
	{
		$this->_GetFutureCalled = true;
		return $this->_ExpectedAnnouncements;
	}
	
	public function GetExpectedRows()
	{
		$expectedAnnouncements = array();
		$rows = $this->GetRows();
		foreach($rows as $item)
		{
			$expectedAnnouncements[] = $item[ColumnNames::ANNOUNCEMENT_TEXT];
		}
		
		return $expectedAnnouncements;
	}

    /**
     * @return Announcement[]|array
     */
    public function GetAll()
    {
        // TODO: Implement GetAll() method.
    }

	/**
	 * @param Announcement $announcement
	 */
	public function Add(Announcement $announcement)
	{
		// TODO: Implement Add() method.
	}

    /**
     * @param int $announcementId
     */
    public function Delete($announcementId)
    {
        // TODO: Implement Delete() method.
    }
}
?>