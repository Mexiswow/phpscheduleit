<?php 
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Graphics/namespace.php');

class ManageResourcesActions
{
	const ActionAdd = 'add';
	const ActionChangeDescription = 'description';
	const ActionChangeImage = 'image';
	const ActionChangeLocation = 'location';
	const ActionChangeNotes = 'notes';
	const ActionChangeSchedule = 'schedule';
	const ActionRemoveImage = 'removeImage';
	const ActionRename = 'rename';
}

class ManageResourcesPresenter
{
	/**
	 * @var IManageResourcesPage
	 */
	private $page;
	
	/**
	 * @var IResourceRepository
	 */
	private $resourceRepository;
	
	/**
	 * @var IScheduleRepository
	 */
	private $scheduleRepository;
	
	/**
	 * @var IImageFactory
	 */
	private $imageFactory;
	
	private $actions = array();
	
	public function __construct(
		IManageResourcesPage $page, 
		IResourceRepository $resourceRepository, 
		IScheduleRepository $scheduleRepository,
		IImageFactory $imageFactory)
	{
		$this->page = $page;
		$this->resourceRepository = $resourceRepository;
		$this->scheduleRepository = $scheduleRepository;
		$this->imageFactory = $imageFactory;
		
		$this->actions[ManageResourcesActions::ActionAdd] = 'Add';
		$this->actions[ManageResourcesActions::ActionChangeDescription] = 'ChangeDescription';
		$this->actions[ManageResourcesActions::ActionChangeImage] = 'ChangeImage';
		$this->actions[ManageResourcesActions::ActionChangeLocation] = 'ChangeLocation';
		$this->actions[ManageResourcesActions::ActionChangeNotes] = 'ChangeNotes';
		$this->actions[ManageResourcesActions::ActionChangeSchedule] = 'ChangeSchedule';
		$this->actions[ManageResourcesActions::ActionRemoveImage] = 'RemoveImage';
		$this->actions[ManageResourcesActions::ActionRename] = 'Rename';
	}
	
	public function PageLoad()
	{
		$resources = $this->resourceRepository->GetResourceList();
		$schedules = $this->scheduleRepository->GetAll();
		
		$this->page->BindResources($resources);
		
		$scheduleList = array();
		
		/* @var $schedule Schedule */
		foreach ($schedules as $schedule)
		{
			$scheduleList[$schedule->GetId()] = $schedule->GetName();
		}
		$this->page->BindSchedules($scheduleList);
	}
	
	public function ProcessAction()
	{
		$action = $this->page->GetAction();
		
		if ($this->ActionIsKnown($action))
		{
			$method = $this->actions[$action];
			try 
			{
				$this->$method();
			}
			catch(Exception $ex)
			{
				Log::Error("Error managing resources. Action %s, Error %s", $action, $ex);
			}
		}
		else 
		{
			Log::Error("Unknown manage resource action %s", $action);
		}
	}	
	
	/**
	 * @internal should only be used for testing
	 */
	public function Add()
	{
		
	}
	
	/**
	 * @internal should only be used for testing
	 */
	public function Rename()
	{
		$resource = $this->resourceRepository->LoadById($this->page->GetResourceId());
		
		$resource->SetName($this->page->GetResourceName());
		
		$this->resourceRepository->Update($resource);
	}
	
	/**
	 * @internal should only be used for testing
	 */
	public function ChangeSettings()
	{
	
	}
	
	public function ChangeImage()
	{
		$uploadedImage = $this->page->GetUploadedImage();
		
		if ($uploadedImage->IsError())
		{
			die("Image error: "  . $uploadedImage->Error());
		}
		
		$fileType = strtolower($uploadedImage->Extension());
		
		$supportedTypes = array('jpeg', 'gif', 'png', 'jpg');
		
		if (!in_array($fileType, $supportedTypes))
		{
			die("Invalid image type: $fileType"); 
		}
		
		$image = $this->imageFactory->Load($uploadedImage->TemporaryName());
		$image->ResizeToWidth(300);
	
		$fileName = "resource{$this->page->GetResourceId()}.$fileType";
		$path = ROOT_DIR . 'Web/' . Configuration::Instance()->GetKey(ConfigKeys::IMAGE_UPLOAD_DIRECTORY) . "/$fileName";
		$image->Save($path);
		
		$this->SaveResourceImage($fileName);
	}
	
	public function RemoveImage()
	{
		$this->SaveResourceImage(null);
	}
	
	private function SaveResourceImage($fileName)
	{
		$resource = $this->resourceRepository->LoadById($this->page->GetResourceId());
		
		$resource->SetImage($fileName);
		
		$this->resourceRepository->Update($resource);
	}
	
	private function ActionIsKnown($action)
	{
		return isset($this->actions[$action]);
	}
}
?>