<?php
require_once('PHPUnit/Framework.php');
require_once('../lib/Authorization/namespace.php');
require_once('../lib/Common/namespace.php');
require_once('../lib/Server/namespace.php');
require_once('../lib/Config/namespace.php');
require_once('fakes/DBFakes.php');
require_once('fakes/FakeServer.php');

class AuthorizationTests extends PHPUnit_Framework_TestCase
{
	var $username;
	var $password;
	var $db;
	var $id;
	var $fname;
	var $lname;
	var $email;
	var $isAdmin;
	var $timeOffset;
	var $fakeServer;
	var $timezone;
	var $lastLogin;

	var $auth;
	var $fakePassword;
	var $fakeMigration;

	function setup()
	{
		$this->username = 'LoGInName';
		$this->password = 'password';
		$this->id = 'someexpectedid';
		$this->fname = 'Test';
		$this->lname = 'Name';
		$this->email = 'my@email.com';
		$this->isAdmin = true;
		$this->timezone = "US/Central";
		$this->lastLogin = mktime();

		$this->db = new FakeDatabase();
		$this->fakeServer = new FakeServer();

		ServiceLocator::SetDatabase($this->db);
		ServiceLocator::SetServer($this->fakeServer);

		$this->fakePassword = new FakePassword();
		$this->fakeMigration = new FakeMigration();
		$this->fakeMigration->_Password = $this->fakePassword;

		$this->auth = new Authorization();
		$this->auth->SetMigration($this->fakeMigration);
	}

	function teardown()
	{
		$this->db = null;
		$this->fakeServer = null;
		Configuration::Reset();
	}

	function testValidateChecksAgainstDB()
	{
		$id = 10;
		$oldPassword = 'oldpassword';
		
		$rows = array(array(ColumnNames::USER_ID => $id, ColumnNames::PASSWORD => null, ColumnNames::SALT => null, ColumnNames::OLD_PASSWORD => $oldPassword));
		$this->db->SetRows($rows);

		$this->authenticated = $this->auth->Validate($this->username, $this->password);

		$command = new AuthorizationCommand(strtolower($this->username), $this->password);

		$this->assertEquals($command, $this->db->_LastCommand);
	}

	function testLoginGetsUserDataFromDatabase()
	{
		LoginTime::$Now = time();

		$rows = $this->GetRows();
		$this->db->SetRows($rows);

		$this->authenticated = $this->auth->Login(strtolower($this->username), false);

		$command1 = new LoginCommand(strtolower($this->username));
		$command2 = new UpdateLoginTimeCommand($this->id, LoginTime::Now());

		$this->assertEquals(2, count($this->db->_Commands));
		$this->assertEquals($command1, $this->db->_Commands[0]);
		$this->assertEquals($command2, $this->db->_Commands[1]);
	}

	function testLoginSetsUserInSession()
	{
		$user = new UserSession($this->id);
		$user->FirstName = $this->fname;
		$user->LastName = $this->lname;
		$user->Email = $this->email;
		$user->IsAdmin = $this->isAdmin;
		$user->Timezone = $this->timezone;

		$rows = $this->GetRows();
		$reader = new Mdb2Reader(new FakeDBResult($rows));
		$this->db->SetReader($reader);

		$this->authenticated = $this->auth->Login(strtolower($this->username), false);

		$this->assertEquals($user, $this->fakeServer->GetSession(SessionKeys::USER_SESSION));
	}

	function testUserIsAdminIfEmailMatchesConfigEmail()
	{
		Configuration::SetKey(ConfigKeys::ADMIN_EMAIL, $this->email);

		$this->isAdmin = false;

		$rows = $this->GetRows();
		$this->db->SetRows($rows);

		$this->authenticated = $this->auth->Login(strtolower($this->username), false);

		$user = $this->fakeServer->GetSession(SessionKeys::USER_SESSION);
		$this->assertTrue($user->IsAdmin);
	}

	function testMigratesPasswordNewPasswordHasNotBeenSet()
	{
		$id = 1;
		$password = 'plaintext';
		$username = 'user';

		$oldPassword = md5($password);

		$rows = array(array(ColumnNames::USER_ID => $id, ColumnNames::PASSWORD => null, ColumnNames::SALT => null, ColumnNames::OLD_PASSWORD => $oldPassword));
		$this->db->SetRows($rows);

		$this->fakePassword->_ValidateResult = true;

		$this->auth->Validate($username, $password);

		$this->assertTrue($this->fakeMigration->_CreateCalled);
		$this->assertEquals($password, $this->fakeMigration->_LastPlainText);
		$this->assertEquals($oldPassword, $this->fakeMigration->_LastOldPassword);
		$this->assertEquals(null, $this->fakeMigration->_LastNewPassword);

		$this->assertTrue($this->fakePassword->_ValidateCalled);
		$this->assertTrue($this->fakePassword->_MigrateCalled);
		$this->assertEquals(null, $this->fakePassword->_LastSalt);
		$this->assertEquals($id, $this->fakePassword->_LastUserId);
	}

	function testCanPersistLoginWhenValidLogin()
	{	
		$now = mktime(10, 11, 12, 1, 2, 2000);	
		LoginTime::$Now = $now;
		$rows = $this->GetRows();
		$this->db->SetRows($rows);
		
		$hashedValue = sprintf("%s|%s", $rows[0][ColumnNames::USER_ID], LoginTime::Now());
		
		$this->auth->Login($this->username, true);

		$expectedCookie = new Cookie(CookieKeys::PERSIST_LOGIN, $hashedValue);
		$this->assertEquals($expectedCookie->Value, $this->fakeServer->GetCookie(CookieKeys::PERSIST_LOGIN));
	}
	
	function testCanAutoLoginWithCookie()
	{
		$userid = 'userid';
		$lastLogin = LoginTime::Now();
		$email = 'email@address.com';
		$cookie = new LoginCookie($userid, $lastLogin);		
		
		$rows = array(array(
					ColumnNames::USER_ID => $userid,
					ColumnNames::LAST_LOGIN => $lastLogin,
					ColumnNames::EMAIL => $email
					));
		$this->db->SetRows($rows);
		
		$valid = $this->auth->CookieLogin($cookie->Value);
		
		$cookieValidateCommand = new CookieLoginCommand($userid);
		$loginCommand = new LoginCommand($email);
		
		$this->assertTrue($valid, 'should be valid if cookie matches');
		$this->assertEquals($cookieValidateCommand, $this->db->_Commands[0]);
		$this->assertEquals($loginCommand, $this->db->_Commands[1], 'should login if cookie login is valid');
	}
	
	function testDoesNotAutoLoginIfCookieNotValid()
	{
		$userid = 'userid';
		$lastLogin = LoginTime::Now();
		$email = 'email@address.com';
		$cookie = new LoginCookie($userid, $lastLogin);		
		
		$rows = array(array(
					ColumnNames::USER_ID => $userid,
					ColumnNames::LAST_LOGIN => 'not the same thing',
					ColumnNames::EMAIL => $email
					));
		$this->db->SetRows($rows);
		
		$valid = $this->auth->CookieLogin($cookie->Value);
		
		$this->assertFalse($valid, 'should not be valid if cookie does not match');
		$this->assertEquals(1, count($this->db->_Commands));
	}

	function GetRows()
	{
		$row = array(
					ColumnNames::USER_ID => $this->id,
					ColumnNames::FIRST_NAME => $this->fname,
					ColumnNames::LAST_LOGIN => $this->lastLogin,
					ColumnNames::LAST_NAME => $this->lname,
					ColumnNames::EMAIL => $this->email,
					ColumnNames::IS_ADMIN => $this->isAdmin,
					ColumnNames::TIMEZONE_NAME => $this->timezone
					);

		return array($row);
	}

}

class FakeMigration extends PasswordMigration
{
	public $_Password;
	public $_CreateCalled = false;
	public $_LastOldPassword;
	public $_LastNewPassword;

	public function Create($plaintext, $old, $new)
	{
		$this->_CreateCalled = true;
		$this->_LastPlainText = $plaintext;
		$this->_LastOldPassword = $old;
		$this->_LastNewPassword = $new;

		return $this->_Password;
	}
}

class FakePassword implements IPassword
{
	public $_ValidateCalled = false;
	public $_MigrateCalled = false;
	public $_LastSalt;
	public $_LastUserId;
	public $_ValidateResult = false;

	public function Validate($salt)
	{
		$this->_ValidateCalled = true;
		$this->_LastSalt = $salt;

		return $this->_ValidateResult;
	}

	public function Migrate($userid)
	{
		$this->_MigrateCalled = true;
		$this->_LastUserId = $userid;
	}
}
?>