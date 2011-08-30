<?php
require_once(ROOT_DIR . 'lib/Database/SqlCommand.php');


//MPinnegar
//TO-DO: Move this into alphabetical order
class GetAllReservationsByUserCommand /*Give it a very literal name describing what the command is going to do*/extends SqlCommand
{
    //You need to build the constructor so that the command can be put together. The "heavy lifting" is done by SqlCommand.php and the info comes from DataConstant.php
    public function __contruct($userId) //List each parameter to your query. Generally if you are "getting" something from the database you'll only need to pass in whatever columns are the primary keys. Later on down the line these variables you pass in to the constructor will be used to replace @ symbol variables in the SQL query  
    {
        //Use the method on the superclass parent so that the query will be put together properly. The GET_ALL_RESERVATIONS_BY_USER is where the actual text of the SQL query is written
        parent::__construct(Queries::GET_ALL_RESERVATIONS_BY_USER);
        //Here we add the variable to the SQL statement. I assume behind the scenes that the @userId is replaced by the actual userId that we pass in as a string
        //The Parameter::USERNAME tells the Parameter constructor which of the @symbols to replace with the string contained inside of $userId. In this case it will replace @userId
        $this->AddParameter(new Parameter(ParameterNames::USERNAME, $userId));
        //Now you're done! You've essentially "built" the query by first constructing the SQL query with a bunch of empty variables in it, and then by using AddParameter() to replace those variables with actual values
    }
}

class AddGroupCommand extends SqlCommand
{
	public function __construct($groupName)
	{
		parent::__construct(Queries::ADD_GROUP);
		$this->AddParameter(new Parameter(ParameterNames::GROUP_NAME, $groupName));
	}
}

class AddGroupResourcePermission extends SqlCommand
{
	public function __construct($groupId, $resourceId)
	{
		parent::__construct(Queries::ADD_GROUP_RESOURCE_PERMISSION);
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));
	}
}


class AddLayoutCommand extends SqlCommand
{
	public function __construct($timezone)
	{
		parent::__construct(Queries::ADD_LAYOUT);	
		$this->AddParameter(new Parameter(ParameterNames::TIMEZONE_NAME, $timezone));	
	} 
}

class AddLayoutTimeCommand extends SqlCommand
{
	public function __construct($layoutId, Time $start, Time $end, $periodType, $label)
	{
		parent::__construct(Queries::ADD_LAYOUT_TIME);	
		$this->AddParameter(new Parameter(ParameterNames::LAYOUT_ID, $layoutId));	
		$this->AddParameter(new Parameter(ParameterNames::START_TIME, $start->ToDatabase()));	
		$this->AddParameter(new Parameter(ParameterNames::END_TIME, $end->ToDatabase()));	
		$this->AddParameter(new Parameter(ParameterNames::PERIOD_AVAILABILITY_TYPE, $periodType));	
		$this->AddParameter(new Parameter(ParameterNames::PERIOD_LABEL, $label));
	} 
}

class AddQuotaCommand extends SqlCommand
{
	public function __construct($duration, $limit, $unit, $resourceId, $groupId)
	{
		parent::__construct(Queries::ADD_QUOTA);
		$this->AddParameter(new Parameter(ParameterNames::QUOTA_DURATION, $duration));
		$this->AddParameter(new Parameter(ParameterNames::QUOTA_LIMIT, $limit));
		$this->AddParameter(new Parameter(ParameterNames::QUOTA_UNIT, $unit));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
	} 
}

class AddReservationSeriesCommand extends SqlCommand
{
	public function __construct(Date $dateCreatedUtc, 
								$title, 
								$description, 
								$repeatType,
								$repeatOptions,
								$scheduleId,
								$reservationTypeId,
								$statusId,
								$ownerId
								)
	{
		parent::__construct(Queries::ADD_RESERVATION_SERIES);	
		
		$this->AddParameter(new Parameter(ParameterNames::DATE_CREATED, $dateCreatedUtc->ToDatabase()));	
		$this->AddParameter(new Parameter(ParameterNames::TITLE, $title));	
		$this->AddParameter(new Parameter(ParameterNames::DESCRIPTION, $description));	
		$this->AddParameter(new Parameter(ParameterNames::REPEAT_TYPE, $repeatType));	
		$this->AddParameter(new Parameter(ParameterNames::REPEAT_OPTIONS, $repeatOptions));	
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));	
		$this->AddParameter(new Parameter(ParameterNames::TYPE_ID, $reservationTypeId));	
		$this->AddParameter(new Parameter(ParameterNames::STATUS_ID, $statusId));	
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $ownerId));	
	}
}

class AddReservationResourceCommand extends SqlCommand
{
	public function __construct($seriesId, $resourceId, $resourceLevelId)
	{
		parent::__construct(Queries::ADD_RESERVATION_RESOURCE);
		
		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $seriesId));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_LEVEL_ID, $resourceLevelId));
	}
}

class AddReservationCommand extends SqlCommand
{
	public function __construct(Date $startDateUtc, 
								Date $endDateUtc,
								$referenceNumber,
								$seriesId)
	{
		parent::__construct(Queries::ADD_RESERVATION);
		$this->AddParameter(new Parameter(ParameterNames::START_DATE, $startDateUtc->ToDatabase()));	
		$this->AddParameter(new Parameter(ParameterNames::END_DATE, $endDateUtc->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::REFERENCE_NUMBER, $referenceNumber));		
		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $seriesId));	
	}
}

class AddReservationUserCommand extends SqlCommand
{
	public function __construct($instanceId, $userId, $levelId)
	{
		parent::__construct(Queries::ADD_RESERVATION_USER);
		
		$this->AddParameter(new Parameter(ParameterNames::RESERVATION_INSTANCE_ID, $instanceId));
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));
		$this->AddParameter(new Parameter(ParameterNames::RESERVATION_USER_LEVEL_ID, $levelId));
	}
}

class AddResourceCommand extends SqlCommand 
{
	public function __construct($name, $location = null, $contact_info = null, $description = null, $notes = null, 
								$isactive = 1, $min_duration = null, $min_increment = null, $max_duration = null, 
								$unit_cost = null, $autoassign = 1, $requires_approval = 0, $allow_multiday = 1, 
								$max_participants = null, $min_notice_time = null, $max_notice_time = null)
	{
		parent::__construct(Queries::ADD_RESOURCE);
		
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_NAME, $name));	
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_LOCATION, $location));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_CONTACT, $contact_info));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_DESCRIPTION, $description));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_NOTES, $notes));
		$this->AddParameter(new Parameter(ParameterNames::IS_ACTIVE, $isactive));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MINDURATION, $min_duration));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MININCREMENT, $min_increment));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MAXDURATION, $max_duration));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_COST, $unit_cost));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_AUTOASSIGN, $autoassign));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_REQUIRES_APPROVAL, $requires_approval));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ALLOW_MULTIDAY, $allow_multiday));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MAX_PARTICIPANTS, $max_participants));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MINNOTICE, $min_notice_time));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MAXNOTICE, $max_notice_time));
	}
}

class AddResourceScheduleCommand extends SqlCommand
{
	public function __construct($resourceId, $scheduleId)
	{
		parent::__construct(Queries::ADD_RESOURCE_SCHEDULE);
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));	
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));	
	}
}

class AddScheduleCommand extends SqlCommand
{
	public function __construct($scheduleName, $isDefault, $weekdayStart, $daysVisible, $layoutId)
	{
		parent::__construct(Queries::ADD_SCHEDULE);
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_NAME, $scheduleName));	
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ISDEFAULT, $isDefault));	
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_WEEKDAYSTART, $weekdayStart));	
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_DAYSVISIBLE, $daysVisible));	
		$this->AddParameter(new Parameter(ParameterNames::LAYOUT_ID, $layoutId));	
	}
}

class AddUserGroupCommand extends SqlCommand
{
	public function __construct($userId, $groupId)
	{
		parent::__construct(Queries::ADD_USER_GROUP);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
		$this->AddParameter(new Parameter(ParameterNames::ROLE_ID, GroupRoles::User));
	}
}

class AddUserResourcePermission extends SqlCommand
{
	public function __construct($userId, $resourceId)
	{
		parent::__construct(Queries::ADD_USER_RESOURCE_PERMISSION);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));
	}
}

class AuthorizationCommand extends SqlCommand
{
	public function __construct($username)
	{
		parent::__construct(Queries::VALIDATE_USER);
		$this->AddParameter(new Parameter(ParameterNames::USERNAME, strtolower($username)));	
	}
}

class AutoAssignPermissionsCommand extends SqlCommand 
{
	public function __construct($userId)
	{
		parent::__construct(Queries::AUTO_ASSIGN_PERMISSIONS);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));	
	}
}

class CheckEmailCommand extends SqlCommand
{
	public function __construct($emailAddress)
	{
		parent::__construct(Queries::CHECK_EMAIL);
		$this->AddParameter(new Parameter(ParameterNames::EMAIL_ADDRESS, strtolower($emailAddress)));	
	}
}

class CheckUserExistanceCommand extends SqlCommand
{
	public function __construct($username, $emailAddress)
	{
		parent::__construct(Queries::CHECK_USER_EXISTANCE);
		$this->AddParameter(new Parameter(ParameterNames::USERNAME, $username));	
		$this->AddParameter(new Parameter(ParameterNames::EMAIL_ADDRESS, $emailAddress));	
	}
}

class CheckUsernameCommand extends SqlCommand
{
	public function __construct($username)
	{
		parent::__construct(Queries::CHECK_USERNAME);
		$this->AddParameter(new Parameter(ParameterNames::USERNAME, $username));	
	}
}

class CookieLoginCommand extends SqlCommand
{
	public function __construct($userid)
	{
		parent::__construct(Queries::COOKIE_LOGIN);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userid));	
	}
}

class DeleteGroupCommand extends SqlCommand
{
	public function __construct($groupId)
	{
		parent::__construct(Queries::DELETE_GROUP);
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
	}
}

class DeleteGroupResourcePermission extends SqlCommand
{
	public function __construct($groupId, $resourceId)
	{
		parent::__construct(Queries::DELETE_GROUP_RESOURCE_PERMISSION);
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));
	}
}

class DeleteQuotaCommand extends SqlCommand
{
	public function __construct($quotaId)
	{
		parent::__construct(Queries::DELETE_QUOTA);
		$this->AddParameter(new Parameter(ParameterNames::QUOTA_ID, $quotaId));
	}
}

class DeleteResourceCommand extends SqlCommand
{
	public function __construct($resourceId)
	{
		parent::__construct(Queries::DELETE_RESOURCE_COMMAND);
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));	
	}
}

class DeleteResourceReservationsCommand extends SqlCommand
{
	public function __construct($resourceId)
	{
		parent::__construct(Queries::DELETE_RESOURCE_RESERVATIONS_COMMAND);
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));	
	}
}

class DeleteSeriesCommand extends SqlCommand
{
	public function __construct($seriesId)
	{
		parent::__construct(Queries::DELETE_SERIES);
		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $seriesId));
	}
}

class DeleteUserGroupCommand extends SqlCommand
{
	public function __construct($userId, $groupId)
	{
		parent::__construct(Queries::DELETE_USER_GROUP);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
	}
}
class DeleteUserResourcePermission extends SqlCommand
{
	public function __construct($userId, $resourceId)
	{
		parent::__construct(Queries::DELETE_USER_RESOURCE_PERMISSION);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));
	}
}

class GetAllGroupsCommand extends SqlCommand
{
	public function __construct()
	{
		parent::__construct(Queries::GET_ALL_GROUPS);
	}
}

class GetAllGroupUsersCommand extends SqlCommand
{
	public function __construct($groupId)
	{
		parent::__construct(Queries::GET_ALL_GROUP_USERS);
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
	}
}

class GetAllGroupPermissionsCommand extends SqlCommand
{
	public function __construct($groupId)
	{
		parent::__construct(Queries::GET_GROUP_RESOURCE_PERMISSIONS);
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
	}
}

class GetAllQuotasCommand extends SqlCommand
{
	public function __construct()
	{
		parent::__construct(Queries::GET_ALL_QUOTAS);
	}
}

class GetAllResourcesCommand extends SqlCommand
{
	public function __construct()
	{
		parent::__construct(Queries::GET_ALL_RESOURCES);
	}
}

class GetAllSchedulesCommand extends SqlCommand 
{
	public function __construct()
	{
		parent::__construct(Queries::GET_ALL_SCHEDULES);
	}
}

class GetAllUsersByStatusCommand extends SqlCommand
{
	/**
	 * @param int $userStatusId defaults to getting all users regardless of status
	 */
	public function __construct($userStatusId = AccountStatus::ALL)
	{
		parent::__construct(Queries::GET_ALL_USERS_BY_STATUS);
		$this->AddParameter(new Parameter(ParameterNames::USER_STATUS_ID, $userStatusId));
	}
}

class GetDashboardAnnouncementsCommand extends SqlCommand 
{
	public function __construct($currentDate)
	{
		parent::__construct(Queries::GET_DASHBOARD_ANNOUNCEMENTS);
		$this->AddParameter(new Parameter(ParameterNames::CURRENT_DATE, $currentDate->ToDatabase()));
	}
}

class GetGroupByIdCommand extends SqlCommand
{
	public function __construct($groupId)
	{
		parent::__construct(Queries::GET_GROUP_BY_ID);
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
	}
}

class GetLayoutCommand extends SqlCommand
{
	public function __construct($scheduleId)
	{
		parent::__construct(Queries::GET_SCHEDULE_TIME_BLOCK_GROUPS);
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));
	}
}

class GetReservationForEditingCommand extends SqlCommand
{
	public function __construct($referenceNumber)
	{
		parent::__construct(Queries::GET_RESERVATION_FOR_EDITING);
		$this->AddParameter(new Parameter(ParameterNames::REFERENCE_NUMBER, $referenceNumber));
		$this->AddParameter(new Parameter(ParameterNames::RESERVATION_USER_LEVEL_ID, ReservationUserLevel::OWNER));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_LEVEL_ID, ResourceLevel::Primary));
	}
}

class GetReservationListCommand extends SqlCommand
{
	public function __construct(Date $startDate, Date $endDate, $userId, $userLevelId)
	{
		parent::__construct(Queries::GET_RESERVATION_LIST);
		$this->AddParameter(new Parameter(ParameterNames::START_DATE, $startDate->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::END_DATE, $endDate->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));
		$this->AddParameter(new Parameter(ParameterNames::RESERVATION_USER_LEVEL_ID, $userLevelId));
	}
}

class GetReservationParticipantsCommand extends SqlCommand
{
	public function __construct($instanceId)
	{
		parent::__construct(Queries::GET_RESERVATION_PARTICIPANTS);
		$this->AddParameter(new Parameter(ParameterNames::RESERVATION_INSTANCE_ID, $instanceId));
	}
}

class GetReservationResourcesCommand extends SqlCommand
{
	public function __construct($seriesId)
	{
		parent::__construct(Queries::GET_RESERVATION_RESOURCES);
		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $seriesId));
	}
}

class GetReservationsCommand extends SqlCommand
{
	public function __construct(Date $startDate, Date $endDate, $scheduleId, $resourceId)
	{
		parent::__construct(Queries::GET_RESERVATIONS_COMMAND);
		$this->AddParameter(new Parameter(ParameterNames::START_DATE, $startDate->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::END_DATE, $endDate->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));
	}
}

class GetReservationByIdCommand extends SqlCommand
{
	public function __construct($reservationId)
	{
		parent::__construct(Queries::GET_RESERVATION_BY_ID);
		$this->AddParameter(new Parameter(ParameterNames::RESERVATION_INSTANCE_ID, $reservationId));
	}
}

class GetReservationByReferenceNumberCommand extends SqlCommand
{
	public function __construct($referenceNumber)
	{
		parent::__construct(Queries::GET_RESERVATION_BY_REFERENCE_NUMBER);
		$this->AddParameter(new Parameter(ParameterNames::REFERENCE_NUMBER, $referenceNumber));
	}
}

class GetReservationSeriesInstances extends SqlCommand
{
	public function __construct($seriesId)
	{
		parent::__construct(Queries::GET_RESERVATION_SERIES_INSTANCES);
		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $seriesId));
	}
}

class GetReservationSeriesParticipantsCommand extends SqlCommand
{
	public function __construct($seriesId)
	{
		parent::__construct(Queries::GET_RESERVATION_SERIES_PARTICIPANTS);
		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $seriesId));
	}
}

class GetResourceByIdCommand extends SqlCommand
{
	public function __construct($resourceId)
	{
		parent::__construct(Queries::GET_RESOURCE_BY_ID);
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));
	}
}

class GetScheduleByIdCommand extends SqlCommand
{
public function __construct($scheduleId)
	{
		parent::__construct(Queries::GET_SCHEDULE_BY_ID);
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));
	}
}

class GetScheduleResourcesCommand extends SqlCommand
{
	public function __construct($scheduleId)
	{
		parent::__construct(Queries::GET_SCHEDULE_RESOURCES);
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));
	}
}

class GetUserByIdCommand extends SqlCommand
{
	public function __construct($userid)
	{
		parent::__construct(Queries::GET_USER_BY_ID);		
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userid));
	}
}

class GetUserEmailPreferencesCommand extends SqlCommand
{
	public function __construct($userid)
	{
		parent::__construct(Queries::GET_USER_EMAIL_PREFERENCES);		
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userid));
	}
}

class GetUserGroupsCommand extends SqlCommand
{
	public function __construct($userid)
	{
		parent::__construct(Queries::GET_USER_GROUPS);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userid));
	}
}

class GetUserRoleCommand extends SqlCommand
{
	public function __construct($userid)
	{
		parent::__construct(Queries::GET_USER_ROLES);		
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userid));
	}
}

class LoginCommand extends SqlCommand
{
	public function __construct($username)
	{
		parent::__construct(Queries::LOGIN_USER);
		$this->AddParameter(new Parameter(ParameterNames::USERNAME, strtolower($username)));		
	}
}

class MigratePasswordCommand extends SqlCommand 
{
	public function __construct($userid, $password, $salt)
	{
		parent::__construct(Queries::MIGRATE_PASSWORD);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userid));
		$this->AddParameter(new Parameter(ParameterNames::PASSWORD, $password));
		$this->AddParameter(new Parameter(ParameterNames::SALT, $salt));
	}
}

class RegisterFormSettingsCommand extends SqlCommand 
{
	public function __construct($firstName, $lastName, $username, $email, $password, $organization, $group, $position, $address, $phone, $homepage, $timezone)
	{
		parent::__construct(Queries::REGISTER_FORM_SETTINGS);
		
		$this->AddParameter(new Parameter(ParameterNames::FIRST_NAME_SETTING, $firstName));
		$this->AddParameter(new Parameter(ParameterNames::LAST_NAME_SETTING, $lastName));
		$this->AddParameter(new Parameter(ParameterNames::USERNAME_SETTING, $username));	
		$this->AddParameter(new Parameter(ParameterNames::EMAIL_ADDRESS_SETTING, $email));
		$this->AddParameter(new Parameter(ParameterNames::PASSWORD_SETTING, $password));
		$this->AddParameter(new Parameter(ParameterNames::ORGANIZATION_SELECTION_SETTING, $organization));
		$this->AddParameter(new Parameter(ParameterNames::GROUP_SETTING, $group));
		$this->AddParameter(new Parameter(ParameterNames::POSITION_SETTING, $position));
		$this->AddParameter(new Parameter(ParameterNames::ADDRESS_SETTING, $address));
		$this->AddParameter(new Parameter(ParameterNames::PHONE_SETTING, $phone));
		$this->AddParameter(new Parameter(ParameterNames::HOMEPAGE_SELECTION_SETTING, $homepage));
		$this->AddParameter(new Parameter(ParameterNames::TIMEZONE_SELECTION_SETTING, $timezone));
	}
}

class RegisterMiniUserCommand extends SqlCommand 
{
	public function __construct($username, $email, $fname, $lname, $password, $salt, $timezone, $userStatusId, $userRoleId, $language)
	{
		parent::__construct(Queries::REGISTER_MINI_USER);
		
		$this->AddParameter(new Parameter(ParameterNames::USERNAME, $username));	
		$this->AddParameter(new Parameter(ParameterNames::EMAIL_ADDRESS, $email));
		$this->AddParameter(new Parameter(ParameterNames::FIRST_NAME, $fname));
		$this->AddParameter(new Parameter(ParameterNames::LAST_NAME, $lname));
		$this->AddParameter(new Parameter(ParameterNames::PASSWORD, $password));
		$this->AddParameter(new Parameter(ParameterNames::SALT, $salt));
		$this->AddParameter(new Parameter(ParameterNames::TIMEZONE_NAME, $timezone));
		$this->AddParameter(new Parameter(ParameterNames::LANGUAGE, $language));
		$this->AddParameter(new Parameter(ParameterNames::USER_STATUS_ID, $userStatusId));
		$this->AddParameter(new Parameter(ParameterNames::USER_ROLE_ID, $userRoleId));
	}
}

class RegisterUserCommand extends SqlCommand 
{
	public function __construct($username, $email, $fname, $lname, $password, $salt, $timezone, $language, $homepageId, $phone, $organization, $position, $userStatusId)
	{
		parent::__construct(Queries::REGISTER_USER);
		
		$this->AddParameter(new Parameter(ParameterNames::USERNAME, $username));	
		$this->AddParameter(new Parameter(ParameterNames::EMAIL_ADDRESS, $email));
		$this->AddParameter(new Parameter(ParameterNames::FIRST_NAME, $fname));
		$this->AddParameter(new Parameter(ParameterNames::LAST_NAME, $lname));
		$this->AddParameter(new Parameter(ParameterNames::PASSWORD, $password));
		$this->AddParameter(new Parameter(ParameterNames::SALT, $salt));
		$this->AddParameter(new Parameter(ParameterNames::TIMEZONE_NAME, $timezone));
		$this->AddParameter(new Parameter(ParameterNames::LANGUAGE, $language));
		$this->AddParameter(new Parameter(ParameterNames::HOMEPAGE_ID, $homepageId));
		$this->AddParameter(new Parameter(ParameterNames::PHONE, $phone));
		$this->AddParameter(new Parameter(ParameterNames::ORGANIZATION, $organization));
		$this->AddParameter(new Parameter(ParameterNames::POSITION, $position));	
		$this->AddParameter(new Parameter(ParameterNames::USER_STATUS_ID, $userStatusId));
		$this->AddParameter(new Parameter(ParameterNames::DATE_CREATED, Date::Now()->ToDatabase()));
	}
}

class RemoveReservationCommand extends SqlCommand
{
	public function __construct($referenceNumber)
	{
		parent::__construct(Queries::REMOVE_RESERVATION_INSTANCE);
		
		$this->AddParameter(new Parameter(ParameterNames::REFERENCE_NUMBER, $referenceNumber));
	}
}

class RemoveReservationResourceCommand extends SqlCommand
{
	public function __construct($seriesId, $resourceId)
	{
		parent::__construct(Queries::REMOVE_RESERVATION_RESOURCE);

		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $seriesId));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $resourceId));

	}
}

class RemoveReservationUserCommand extends SqlCommand
{
	public function __construct($instanceId, $userId)
	{
		parent::__construct(Queries::REMOVE_RESERVATION_USER);

		$this->AddParameter(new Parameter(ParameterNames::RESERVATION_INSTANCE_ID, $instanceId));
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));
	}
}

class ReportingCommand extends SqlCommand 
{
	public function __construct($fname, $lname, $username, $organization, $group)
	{
		parent::__construct(Queries::GET_REPORT);
		
		$this->AddParameter(new Parameter(ParameterNames::FIRST_NAME, $fname));
		$this->AddParameter(new Parameter(ParameterNames::LAST_NAME, $lname));
		$this->AddParameter(new Parameter(ParameterNames::USERNAME, $username));	
		$this->AddParameter(new Parameter(ParameterNames::ORGANIZATION, $organization));
		$this->AddParameter(new Parameter(ParameterNames::GROUP, $group));	
	}
}

class GetUserPermissionsCommand extends SqlCommand
{
	public function __construct($userId)
	{
		parent::__construct(Queries::GET_USER_RESOURCE_PERMISSIONS);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));	
	}
}

class SelectUserGroupPermissions extends SqlCommand
{
	public function __construct($userId)
	{
		parent::__construct(Queries::GET_USER_GROUP_RESOURCE_PERMISSIONS);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));	
	}
}

class SetDefaultScheduleCommand extends SqlCommand
{
	public function __construct($scheduleId)
	{
		parent::__construct(Queries::SET_DEFAULT_SCHEDULE);
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));	
	}
}

class UpdateGroupCommand extends SqlCommand
{
	public function __construct($groupId, $groupName)
	{
		parent::__construct(Queries::UPDATE_GROUP);
		$this->AddParameter(new Parameter(ParameterNames::GROUP_ID, $groupId));
		$this->AddParameter(new Parameter(ParameterNames::GROUP_NAME, $groupName));
	}
}

class UpdateLoginTimeCommand extends SqlCommand
{
	public function __construct($userid, $lastlogin)
	{
		parent::__construct(Queries::UPDATE_LOGINTIME);
		$this->AddParameter(new Parameter(ParameterNames::LAST_LOGIN, $lastlogin));
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userid));	
	}
}

class UpdateFutureReservationsCommand extends SqlCommand
{
	public function __construct($referenceNumber, $newSeriesId, $currentSeriesId)
	{
		parent::__construct(Queries::UPDATE_FUTURE_RESERVATION_INSTANCES);	
		
		$this->AddParameter(new Parameter(ParameterNames::REFERENCE_NUMBER, $referenceNumber));
		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $currentSeriesId));
		$this->AddParameter(new Parameter(ParameterNames::CURRENT_SERIES_ID, $currentSeriesId));
	}
}

class UpdateReservationCommand extends SqlCommand
{
	public function __construct($referenceNumber,
								$seriesId,
								Date $startDate, 
								Date $endDate)
	{
		parent::__construct(Queries::UPDATE_RESERVATION_INSTANCE);	
		
		$this->AddParameter(new Parameter(ParameterNames::REFERENCE_NUMBER, $referenceNumber));
		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $seriesId));
		$this->AddParameter(new Parameter(ParameterNames::START_DATE, $startDate->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::END_DATE, $endDate->ToDatabase()));
	}
}

class UpdateReservationSeriesCommand extends SqlCommand
{
	public function __construct($seriesId,
								$title, 
								$description, 
								$repeatType,
								$repeatOptions,
								Date $dateModified,
								$statusId
								)
	{
		parent::__construct(Queries::UPDATE_RESERVATION_SERIES);	
		
		$this->AddParameter(new Parameter(ParameterNames::SERIES_ID, $seriesId));
		$this->AddParameter(new Parameter(ParameterNames::TITLE, $title));	
		$this->AddParameter(new Parameter(ParameterNames::DESCRIPTION, $description));	
		$this->AddParameter(new Parameter(ParameterNames::REPEAT_TYPE, $repeatType));	
		$this->AddParameter(new Parameter(ParameterNames::REPEAT_OPTIONS, $repeatOptions));
		$this->AddParameter(new Parameter(ParameterNames::DATE_MODIFIED, $dateModified->ToDatabase()));	
		$this->AddParameter(new Parameter(ParameterNames::STATUS_ID, $statusId));
	}
}

class UpdateResourceCommand extends SqlCommand
{
	public function __construct($id, 
								$name, 
								$location, 
								$contact, 
								$notes, 
								TimeInterval $minDuration, 
								TimeInterval $maxDuration, 
								$autoAssign, 
								$requiresApproval, 
								$allowMultiday,
								$maxParticipants,
								TimeInterval $minNoticeTime,
								TimeInterval $maxNoticeTime,
								$description,
								$imageName,
								$isActive)
	{
		parent::__construct(Queries::UPDATE_RESOURCE);
		
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $id));	
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_NAME, $name));	
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_LOCATION, $location));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_CONTACT, $contact));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_DESCRIPTION, $description));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_NOTES, $notes));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MINDURATION, $minDuration->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MAXDURATION, $maxDuration->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_AUTOASSIGN, $autoAssign));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_REQUIRES_APPROVAL, $requiresApproval));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ALLOW_MULTIDAY, $allowMultiday));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MAX_PARTICIPANTS, $maxParticipants));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MINNOTICE, $minNoticeTime->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_MAXNOTICE, $maxNoticeTime->ToDatabase()));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_IMAGE_NAME, $imageName));
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ISACTIVE, (int)$isActive));
	}
}

class UpdateResourceScheduleCommand extends SqlCommand
{
	public function __construct($id, $scheduleId)
	{
		parent::__construct(Queries::UPDATE_RESOURCE_SCHEDULE);
		
		$this->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $id));	
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));	
	}
}

class UpdateScheduleCommand extends SqlCommand
{
	public function __construct($scheduleId,
								$name, 
								$isDefault, 
								$weekdayStart, 
								$daysVisible)
	{
		parent::__construct(Queries::UPDATE_SCHEDULE);	
		
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_NAME, $name));	
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ISDEFAULT, $isDefault));	
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_WEEKDAYSTART, (int)$weekdayStart));	
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_DAYSVISIBLE, (int)$daysVisible));
	}
}

class UpdateScheduleLayoutCommand extends SqlCommand
{
	public function __construct($scheduleId, $layoutId)
	{
		parent::__construct(Queries::UPDATE_SCHEDULE_LAYOUT);	
		
		$this->AddParameter(new Parameter(ParameterNames::SCHEDULE_ID, $scheduleId));
		$this->AddParameter(new Parameter(ParameterNames::LAYOUT_ID, $layoutId));
	}
}

class UpdateUserCommand extends SqlCommand
{
	public function __construct(
		$userId,
		$statusId,
		$encryptedPassword,
		$passwordSalt,
		$firstName,
		$lastName,
		$emailAddress,
		$username,
		$homepageId,
		$timezoneName)
	{
		parent::__construct(Queries::UPDATE_USER);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));
		$this->AddParameter(new Parameter(ParameterNames::USER_STATUS_ID, $statusId));
		$this->AddParameter(new Parameter(ParameterNames::PASSWORD, $encryptedPassword));
		$this->AddParameter(new Parameter(ParameterNames::SALT, $passwordSalt));
		$this->AddParameter(new Parameter(ParameterNames::FIRST_NAME, $firstName));
		$this->AddParameter(new Parameter(ParameterNames::LAST_NAME, $lastName));
		$this->AddParameter(new Parameter(ParameterNames::EMAIL_ADDRESS, $emailAddress));
		$this->AddParameter(new Parameter(ParameterNames::USERNAME, $username));
		$this->AddParameter(new Parameter(ParameterNames::HOMEPAGE_ID, $homepageId));
		$this->AddParameter(new Parameter(ParameterNames::TIMEZONE_NAME, $timezoneName));
		$this->AddParameter(new Parameter(ParameterNames::DATE_MODIFIED, Date::Now()->ToDatabase()));
	}
}

class UpdateUserAttributesCommand extends SqlCommand
{
	public function __construct(
		$userId,
		$phoneNumber,
		$organization,
		$position
	)
	{
		parent::__construct(Queries::UPDATE_USER_ATTRIBUTES);
		$this->AddParameter(new Parameter(ParameterNames::USER_ID, $userId));
		$this->AddParameter(new Parameter(ParameterNames::PHONE, $phoneNumber));
		$this->AddParameter(new Parameter(ParameterNames::POSITION, $position));
		$this->AddParameter(new Parameter(ParameterNames::ORGANIZATION, $organization));
	}
}


class UpdateUserFromLdapCommand extends SqlCommand 
{
	public function __construct($username, $email, $fname, $lname, $password, $salt, $phone, $organization, $position)
	{
		parent::__construct(Queries::UPDATE_USER_BY_USERNAME);
		$this->AddParameter(new Parameter(ParameterNames::USERNAME, $username));	
		$this->AddParameter(new Parameter(ParameterNames::EMAIL_ADDRESS, $email));
		$this->AddParameter(new Parameter(ParameterNames::FIRST_NAME, $fname));
		$this->AddParameter(new Parameter(ParameterNames::LAST_NAME, $lname));
		$this->AddParameter(new Parameter(ParameterNames::PASSWORD, $password));
		$this->AddParameter(new Parameter(ParameterNames::SALT, $salt));
		$this->AddParameter(new Parameter(ParameterNames::PHONE, $phone));
		$this->AddParameter(new Parameter(ParameterNames::ORGANIZATION, $organization));
		$this->AddParameter(new Parameter(ParameterNames::POSITION, $position));
	}
}
?>