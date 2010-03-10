<?php 
require_once(ROOT_DIR . 'lib/Reservation/namespace.php');
require_once(ROOT_DIR . 'Pages/ReservationPage.php');

class ReservationPreconditionServiceTests extends TestBase
{
	/**
	 * @var UserSession
	 */
	private $_user;

	/**
	 * @var int
	 */
	private $_userId;
	
	/**
	 * @var IPermissionServiceFactory
	 */
	private $_permissionServiceFactory;
	
	public function setup()
	{
		parent::setup();

		$this->_user = $this->fakeServer->UserSession;
		$this->_userId = $this->_user->UserId;
		
		$this->_permissionServiceFactory = $this->getMock('IPermissionServiceFactory');
	}
	
	public function teardown()
	{
		parent::teardown();
	}
	
	public function testRedirectsWithErrorMessageIfUserDoesNotHavePermission()
	{
		$resourceId = 123;
		$resource = new ReservationResource($resourceId);
				
		$lastPage = 'last/page.php?a=b&c=d';
		$errorMessage = ErrorMessages::INSUFFICIENT_PERMISSIONS;
		
		$page = $this->getMock('IReservationPage');

		$page->expects($this->once())
			->method('GetRequestedResourceId')
			->will($this->returnValue($resourceId));

		$permissionService = $this->getMock('IPermissionService');
		
			
		$this->_permissionServiceFactory->expects($this->once())
			->method('GetPermissionService')
			->with($this->equalTo($this->_userId))
			->will($this->returnValue($permissionService));			
			
		$permissionService->expects($this->once())
			->method('CanAccessResource')
			->with($this->equalTo($resource))
			->will($this->returnValue(false));

		$page->expects($this->once())
			->method('GetLastPage')
			->will($this->returnValue($lastPage));
			
		$page->expects($this->once())
			->method('RedirectToError')
			->with($this->equalTo($errorMessage), $this->equalTo($lastPage));
			
		$preconditionService = new ReservationPreconditionService($this->_permissionServiceFactory);
		$preconditionService->CheckAll($page);
	}
	
}