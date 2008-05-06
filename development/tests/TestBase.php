<?php

class TestBase extends PHPUnit_Framework_TestCase
{
	public $db;
	public $fakeServer;
	public $fakeConfig;
	public $fakeResources;
	
	public function setup()
	{
		$this->db = new FakeDatabase();
		$this->fakeServer = new FakeServer();
		$this->fakeConfig = new FakeConfig();
		$this->fakeResources = new FakeResources();
		
		ServiceLocator::SetDatabase($this->db);
		ServiceLocator::SetServer($this->fakeServer);
		Configuration::SetInstance($this->fakeConfig);
		Resources::SetInstance($this->fakeResources);
	}
	
	public function teardown()
	{
		$this->db = null;
		$this->fakeServer = null;
		Configuration::SetInstance(null);
		$this->fakeResources = null;
	}
}
?>