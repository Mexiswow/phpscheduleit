<?php
require_once('namespace.php');

class IReader
{
	/**
	* To be implemented by child
	*/
	function Reader() { }
	
	/**
	* To be implemented by child
	* @return array
	*/
	function &GetRow() { }
	
	/**
	* To be implemented by child
	* @return int
	*/
	function NumRows() { }
}

?>