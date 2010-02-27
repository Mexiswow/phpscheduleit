<?php 
class ErrorMessages
{
	const UNKNOWN_ERROR = 0;
	const INSUFFICIENT_PERMISSIONS = 1;
	
	private $_resourceKeys = array();
	private static $_instance;
		
	private function __construct()
	{
		$this->SetKey(ErrorMessages::INSUFFICIENT_PERMISSIONS, 'InsufficientPermissionsError');
	}
	
	public static function Instance()
	{
		if (self::$_instance == null)
		{
			self::$_instance = new ErrorMessages();
		}
		
		return self::$_instance;
	}
	
	private function SetKey($errorMessageId, $resourceKey)
	{
		$this->_resourceKeys[$errorMessageId] = $resourceKey;
	}
	
	public function GetResourceKey($errorMessageId)
	{
		if (!isset($this->_resourceKeys[$errorMessageId]))
		{
			return 'UnknownError';
		}
		
		return $this->_resourceKeys[$errorMessageId];
	}
}
?>