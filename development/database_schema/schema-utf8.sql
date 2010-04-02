﻿DROP DATABASE IF EXISTS phpscheduleit2;

CREATE DATABASE phpscheduleit2;

USE phpscheduleit2;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
 `announcementid` mediumint(8) unsigned NOT NULL auto_increment,
 `title` varchar(85) NOT NULL,
 `announcement_text` text,
 `priority` mediumint(8) NOT NULL,
 `start_datetime` datetime,
 `end_datetime` datetime,
 PRIMARY KEY (`announcementid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `time_block_groups`
--

DROP TABLE IF EXISTS `time_block_groups`;
CREATE TABLE `time_block_groups` (
 `block_groupid` tinyint(2) unsigned NOT NULL,
 `label` varchar(85) NOT NULL,
 PRIMARY KEY (`block_groupid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `time_blocks`
--

DROP TABLE IF EXISTS `time_blocks`;
CREATE TABLE `time_blocks` (
 `blockid` tinyint(2) unsigned NOT NULL,
 `label` varchar(85) NOT NULL,
 `availability_code` tinyint(2) unsigned NOT NULL,
 `cost_multiplier` numeric(7,2),
 `constraint_function` text,
 `block_group_id` tinyint(2) unsigned NOT NULL,
 `start_time` time NOT NULL,
 `end_time` time NOT NULL,
 PRIMARY KEY (`blockid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `long_quotas`
--

DROP TABLE IF EXISTS `long_quotas`;
CREATE TABLE `long_quotas` (
 `long_quotaid` smallint(5) unsigned NOT NULL auto_increment,
 `label` varchar(85),
 `max_count` smallint(5) unsigned,
 `max_total_hours` smallint(5) unsigned,
 `quota_window_duration` time,
 PRIMARY KEY (`long_quotaid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `block_long_quotas`
--

DROP TABLE IF EXISTS `block_long_quotas`;
CREATE TABLE `block_long_quotas` (
 `block_id` tinyint(2) unsigned,
 `long_quota_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`block_id`, `long_quota_id`),
 INDEX (`block_id`),
 FOREIGN KEY (`block_id`) 
	REFERENCES time_blocks(`blockid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`long_quota_id`),
 FOREIGN KEY (`long_quota_id`) 
	REFERENCES long_quotas(`long_quotaid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `day_quotas`
--

DROP TABLE IF EXISTS `day_quotas`;
CREATE TABLE `day_quotas` (
 `day_quotaid` smallint(5) unsigned NOT NULL auto_increment,
 `label` varchar(85),
 `max_total_hours` tinyint(2) unsigned,
 `max_count` tinyint(2) unsigned,
 `max_continuous` tinyint(2) unsigned,
 PRIMARY KEY (`day_quotaid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `block_day_quotas`
--

DROP TABLE IF EXISTS `block_day_quotas`;
CREATE TABLE `block_day_quotas` (
 `block_id` tinyint(2) unsigned,
 `day_quota_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`block_id`, `day_quota_id`),
 INDEX (`block_id`),
 FOREIGN KEY (`block_id`) 
	REFERENCES time_blocks(`blockid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`day_quota_id`),
 FOREIGN KEY (`day_quota_id`) 
	REFERENCES day_quotas(`day_quotaid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
CREATE TABLE `organizations` (
 `organizationid` smallint(5) unsigned NOT NULL auto_increment,
 `name` varchar(85) NOT NULL,
 PRIMARY KEY (`organizationid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
 `groupid` smallint(5) unsigned NOT NULL auto_increment,
 `name` varchar(85) NOT NULL,
 PRIMARY KEY (`groupid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
 `roleid` tinyint(2) unsigned NOT NULL,
 `name` varchar(85),
 `user_level` tinyint(1) unsigned,
 PRIMARY KEY (`roleid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `user_statuses`
--

DROP TABLE IF EXISTS `user_statuses`;
CREATE TABLE `user_statuses` (
 `statusid` tinyint(2) unsigned NOT NULL,
 `description` varchar(85),
 PRIMARY KEY (`statusid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
 `addressid` tinyint(2) unsigned NOT NULL auto_increment,
 `label` varchar(85),
 `address_text` text NOT NULL,
 PRIMARY KEY (`addressid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `registration_form_settings`
--

DROP TABLE IF EXISTS `registration_form_settings`;
CREATE TABLE `registration_form_settings` (
 `formid` mediumint(8) unsigned NOT NULL auto_increment,
 `fname_setting` tinyint(1) NOT NULL default '1',
 `lname_setting` tinyint(1) NOT NULL default '1',
 `username_setting` tinyint(1) NOT NULL default '1',
 `email_setting` tinyint(1) NOT NULL default '1',
 `password_setting` tinyint(1) NOT NULL default '1',
 `organization_setting` tinyint(1) NOT NULL default '2',
 `group_setting` tinyint(1) NOT NULL default '2',
 `position_setting` tinyint(1) NOT NULL default '2',
 `address_setting` tinyint(1) NOT NULL default '2',
 `phone_setting` tinyint(1) NOT NULL default '2',
 `homepage_setting`  tinyint(1) NOT NULL default '2',
 `timezone_setting` tinyint(1) NOT NULL default '3',
 PRIMARY KEY (`formid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
 `userid` mediumint(8) unsigned NOT NULL auto_increment,
 `username` varchar(85),
 `email` varchar(85) NOT NULL,
 `password` varchar(85) NOT NULL,
 `salt` varchar(85) NOT NULL,
 `fname` varchar(85),
 `lname` varchar(85),
 `phone` varchar(85),
 `position` varchar(85),
 `timezone` varchar(85) NOT NULL,
 `lastlogin` datetime,
 `homepageid` tinyint(2) unsigned NOT NULL default '1',
 `date_created` datetime NOT NULL,
 `last_modified` timestamp,
 `role_id` tinyint(2) unsigned,
 `status_id` tinyint(2) unsigned NOT NULL,
 `legacyid` char(16),
 `legacypassword` varchar(32),
 PRIMARY KEY (`userid`),
 INDEX (`role_id`),
 FOREIGN KEY (`role_id`) 
	REFERENCES user_roles(`roleid`)
	ON UPDATE CASCADE ON DELETE RESTRICT,
 INDEX (`status_id`),
 FOREIGN KEY (`status_id`) 
	REFERENCES user_statuses(`statusid`)
	ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `user_organizations`
--

DROP TABLE IF EXISTS `user_organizations`;
CREATE TABLE `user_organizations` (
 `user_id` mediumint(8) unsigned NOT NULL,
 `organization_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`user_id`, `organization_id`),
 INDEX (`user_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`userid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`organization_id`),
 FOREIGN KEY (`organization_id`) 
	REFERENCES organizations(`organizationid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
 `group_id` smallint(5) unsigned NOT NULL,
 `user_id` mediumint(8) unsigned NOT NULL,
 PRIMARY KEY (`group_id`, `user_id`),
 INDEX (`user_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`userid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`group_id`),
 FOREIGN KEY (`group_id`) 
	REFERENCES groups(`groupid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `user_addresses`
--

DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE `user_addresses` (
 `user_id` mediumint(8) unsigned NOT NULL,
 `address_id` tinyint(2) unsigned NOT NULL,
 PRIMARY KEY (`user_id`, `address_id`),
 INDEX (`user_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`userid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`address_id`),
 FOREIGN KEY (`address_id`) 
	REFERENCES addresses(`addressid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `user_day_quotas`
--

DROP TABLE IF EXISTS `user_day_quotas`;
CREATE TABLE `user_day_quotas` (
 `user_id` mediumint(8) unsigned NOT NULL,
 `day_quota_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`user_id`, `day_quota_id`),
 INDEX (`user_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`userid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`day_quota_id`),
 FOREIGN KEY (`day_quota_id`) 
	REFERENCES day_quotas(`day_quotaid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `user_long_quotas`
--

DROP TABLE IF EXISTS `user_long_quotas`;
CREATE TABLE `user_long_quotas` (
 `user_id` mediumint(8) unsigned NOT NULL,
 `long_quota_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`user_id`, `long_quota_id`),
 INDEX (`user_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`userid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`long_quota_id`),
 FOREIGN KEY (`long_quota_id`) 
	REFERENCES long_quotas(`long_quotaid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `constraint_functions`
--

DROP TABLE IF EXISTS `constraint_functions`;
CREATE TABLE `constraint_functions` (
 `constraintid` smallint(5) unsigned NOT NULL,
 `constraint_function` text,
 PRIMARY KEY (`constraintid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
CREATE TABLE `resources` (
 `resourceid` smallint(5) unsigned NOT NULL auto_increment,
 `name` varchar(85) NOT NULL,
 `location` varchar(85),
 `contact_info` varchar(85),
 `description` text,
 `notes` text,
 `isactive` tinyint(1) unsigned NOT NULL default '1',
 `min_duration` time,
 `min_increment` time,
 `max_duration` time,
 `unit_cost` dec(7,2),
 `autoassign` tinyint(1) unsigned NOT NULL default '1',
 `requires_approval` tinyint(1) unsigned NOT NULL,
 `allow_multiday_reservations` tinyint(1) unsigned NOT NULL default '1',
 `max_participants` mediumint(8) unsigned,
 `min_notice_time` time,
 `max_notice_time` time,
 `legacyid` char(16),
 PRIMARY KEY (`resourceid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `resource_day_quotas`
--

DROP TABLE IF EXISTS `resource_day_quotas`;
CREATE TABLE `resource_day_quotas` (
 `resource_id` smallint(5) unsigned NOT NULL,
 `day_quota_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`resource_id`, `day_quota_id`),
 INDEX (`resource_id`),
 FOREIGN KEY (`resource_id`) 
	REFERENCES resources(`resourceid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`day_quota_id`),
 FOREIGN KEY (`day_quota_id`) 
	REFERENCES day_quotas(`day_quotaid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `resource_long_quotas`
--

DROP TABLE IF EXISTS `resource_long_quotas`;
CREATE TABLE `resource_long_quotas` (
 `resource_id` smallint(5) unsigned NOT NULL,
 `long_quota_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`resource_id`, `long_quota_id`),
 INDEX (`resource_id`),
 FOREIGN KEY (`resource_id`) 
	REFERENCES resources(`resourceid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`long_quota_id`),
 FOREIGN KEY (`long_quota_id`) 
	REFERENCES long_quotas(`long_quotaid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `resource_constraints`
--

DROP TABLE IF EXISTS `resource_constraints`;
CREATE TABLE `resource_constraints` (
 `resource_id` smallint(5) unsigned,
 `constraint_id` smallint(5) unsigned,
 PRIMARY KEY (`resource_id`, `constraint_id`),
 INDEX (`resource_id`),
 FOREIGN KEY (`resource_id`) 
	REFERENCES resources(`resourceid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`constraint_id`),
 FOREIGN KEY (`constraint_id`) 
	REFERENCES constraint_functions(`constraintid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `user_resource_permissions`
--

DROP TABLE IF EXISTS `user_resource_permissions`;
CREATE TABLE `user_resource_permissions` (
 `user_id` mediumint(8) unsigned NOT NULL,
 `resource_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`user_id`, `resource_id`),
 INDEX (`user_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`userid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`resource_id`),
 FOREIGN KEY (`resource_id`) 
	REFERENCES resources(`resourceid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `group_resource_permissions`
--

DROP TABLE IF EXISTS `group_resource_permissions`;
CREATE TABLE `group_resource_permissions` (
 `group_id` smallint(5) unsigned NOT NULL,
 `resource_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`group_id`, `resource_id`),
 INDEX (`group_id`),
 FOREIGN KEY (`group_id`) 
	REFERENCES groups(`groupid`) 
	ON UPDATE CASCADE ON DELETE CASCADE,
INDEX (`resource_id`),
FOREIGN KEY (`resource_id`) 
	REFERENCES resources(`resourceid`) 
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
 `scheduleid` smallint(5) unsigned NOT NULL auto_increment,
 `name` varchar(85) NOT NULL,
 `isdefault` tinyint(1) unsigned NOT NULL,
 `weekdaystart` tinyint(2) unsigned NOT NULL,
 `daysvisible` tinyint(2) unsigned NOT NULL default '7',
 PRIMARY KEY (`scheduleid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `schedule_admins`
--

DROP TABLE IF EXISTS `schedule_admins`;
CREATE TABLE `schedule_admins` (
 `schedule_id` smallint(5) unsigned NOT NULL,
 `user_id` mediumint(8) unsigned NOT NULL,
 PRIMARY KEY (`schedule_id`, `user_id`),
 INDEX (`schedule_id`),
 FOREIGN KEY (`schedule_id`) 
	REFERENCES schedules(`scheduleid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`user_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`userid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `schedule_time_block_groups`
--

DROP TABLE IF EXISTS `schedule_time_block_groups`;
CREATE TABLE `schedule_time_block_groups` (
 `schedule_id` smallint(5) unsigned NOT NULL,
 `block_group_id` tinyint(2) unsigned NOT NULL,
 PRIMARY KEY (`schedule_id`, `block_group_id`),
 INDEX (`schedule_id`),
 FOREIGN KEY (`schedule_id`) 
	REFERENCES schedules(`scheduleid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`block_group_id`),
 FOREIGN KEY (`block_group_id`) 
	REFERENCES time_block_groups(`block_groupid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `resource_schedules`
--

DROP TABLE IF EXISTS `resource_schedules`;
CREATE TABLE `resource_schedules` (
 `resource_id` smallint(5) unsigned NOT NULL,
 `schedule_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`resource_id`, `schedule_id`),
 INDEX (`resource_id`),
 FOREIGN KEY (`resource_id`) 
	REFERENCES resources(`resourceid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`schedule_id`),
 FOREIGN KEY (`schedule_id`) 
	REFERENCES schedules(`scheduleid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `reservation_types`
--

DROP TABLE IF EXISTS `reservation_types`;
CREATE TABLE `reservation_types` (
 `typeid` tinyint(2) unsigned NOT NULL auto_increment,
 `label` varchar(85) NOT NULL,
 PRIMARY KEY (`typeid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `reservation_statuses`
--

DROP TABLE IF EXISTS `reservation_statuses`;
CREATE TABLE `reservation_statuses` (
 `statusid` tinyint(2) unsigned NOT NULL auto_increment,
 `label` varchar(85) NOT NULL,
 PRIMARY KEY (`statusid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
 `reservationid` mediumint(8) unsigned NOT NULL auto_increment,
 `start_date` datetime NOT NULL,
 `end_date` datetime NOT NULL,
 `date_created` datetime NOT NULL,
 `last_modified` timestamp,
 `title` varchar(85) NOT NULL,
 `description` text,
 `allow_participation` tinyint(1) unsigned NOT NULL,
 `allow_anon_participation` tinyint(1) unsigned NOT NULL,
 `user_id` mediumint(8) unsigned NOT NULL,
 `group_id` smallint(5) unsigned,
 `type_id` tinyint(2) unsigned,
 `status_id` tinyint(2) unsigned,
 `total_cost` dec(7,2),
 PRIMARY KEY (`reservationid`),
 INDEX (`user_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`userid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`group_id`),
 FOREIGN KEY (`group_id`) 
	REFERENCES groups(`groupid`)
	ON UPDATE CASCADE ON DELETE SET NULL,
 INDEX (`type_id`),
 FOREIGN KEY (`type_id`) 
	REFERENCES reservation_types(`typeid`)
	ON UPDATE CASCADE ON DELETE SET NULL,
 INDEX (`status_id`),
 FOREIGN KEY (`status_id`) 
	REFERENCES reservation_statuses(`statusid`)
	ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `reservation_time_blocks`
--

DROP TABLE IF EXISTS `reservation_time_blocks`;
CREATE TABLE `reservation_time_blocks` (
 `reservation_id` mediumint(8) unsigned NOT NULL,
 `block_id` tinyint(2) unsigned NOT NULL,
 PRIMARY KEY (`reservation_id`, `block_id`),
 INDEX (`reservation_id`),
 FOREIGN KEY (`reservation_id`) 
	REFERENCES reservations(`reservationid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`block_id`),
 FOREIGN KEY (`block_id`) 
	REFERENCES time_blocks(`blockid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

--
-- Table structure for table `reservation_resources`
--

DROP TABLE IF EXISTS `reservation_resources`;
CREATE TABLE `reservation_resources` (
 `reservation_id` mediumint(8) unsigned NOT NULL,
 `resource_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`reservation_id`, `resource_id`),
 INDEX (`resource_id`),
 FOREIGN KEY (`resource_id`) 
	REFERENCES resources(`resourceid`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 INDEX (`reservation_id`),
 FOREIGN KEY (`reservation_id`) 
	REFERENCES reservations(`reservationid`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

GRANT ALL on phpscheduleit2.* to 'schedule_user'@'localhost' identified by 'password';
