<?php
class ParameterNames
{
	private function __construct()
	{}
	
	const EMAIL_ADDRESS = '@emailaddress';
	const FIRST_NAME = '@fname';
	const HOMEPAGE_ID = '@homepageid';
	const INSTITUTION = '@institution';
	const LAST_LOGIN = '@lastlogin';
	const LAST_NAME = '@lname';
	const PASSWORD = '@password';
	const PHONE = '@phone';
	const POSITION = '@position';
	const SALT = '@salt';
	const TIMEZONE = '@timezone';
	const USER_ID = '@userid';
	const USER_NAME = '@username';	
}

class Queries
{
	private function __construct()
	{}
	
	const AUTO_ASSIGN_PERMISSIONS = 
		'INSERT INTO resource_permission (resourceid, userid) 
		SELECT resourceid, @userid as userid 
		FROM resource WHERE autoassign=1';
	
	const CHECK_EMAIL = 
		'SELECT userid 
		FROM account
		WHERE email = @emailaddress';
		
	const CHECK_USERNAME = 
		'SELECT userid 
		FROM account
		WHERE username = @username';
		
	const CHECK_USER_EXISTANCE = 
		'SELECT userid 
		FROM account
		WHERE username = @username OR email = @emailaddress';
		
	const COOKIE_LOGIN = 
		'SELECT userid, lastlogin, email 
		FROM account 
		WHERE userid = @userid';
	
	const LOGIN_USER = 
		'SELECT userid, email, fname, lname, timezonename, lastlogin
		FROM account 
		WHERE (username = @username OR email = @username)';
	
	const GET_USER_ROLES = 
		'SELECT userid, isadmin 
		FROM accountrole
		WHERE (userid = @userid)';
	
	const MIGRATE_PASSWORD = 
		"UPDATE account 
		SET userpassword = @password, legacypassword = null, salt = @salt 
		WHERE userid = @userid";
	
	const REGISTER_USER = 
		'INSERT INTO account
		(email, userpassword, fname, lname, phone, institution, positionname, username, salt, timezonename, homepageid)
		VALUES
		(@emailaddress, @password, @fname, @lname, @phone, @institution, @position, @username, @salt, @timezone, @homepageid)
		';
		
	const UPDATE_LOGINTIME = 
		'UPDATE account 
		SET lastlogin = @lastlogin 
		WHERE userid = @userid';
		
	const UPDATE_USER_BY_USERNAME = 
		'UPDATE account SET 
			email = @email,
			userpassword = @password,
			salt = @salt,
			fname = @fname,
			lname = @lname,
			phone = @phone,
			institution = @institution,
			positionname = @position
		WHERE username = @username
		';
	
	const VALIDATE_USER = 
		'SELECT userid, userpassword, salt, legacypassword
		FROM account 
		WHERE (username = @username OR email = @username)';
	

	
}

class ColumnNames
{
	private function __construct()
	{}
	
	// USER TABLE //
	const EMAIL = 'email';
	const FIRST_NAME = 'fname';
	const HOMEPAGE_ID = 'homepageid';
	const IS_ADMIN = 'isadmin';
	const LAST_LOGIN = 'lastlogin';
	const LAST_NAME = 'lname';	
	const MATCH_COUNT = 'matchcount';
	const OLD_PASSWORD = 'legacypassword';
	const PASSWORD = 'userpassword';
	const TIMEZONE_NAME = 'timezonename';
	const SALT = 'salt';
	const USER_ID = 'userid';	
}
?>