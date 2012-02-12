<?php
/**
Copyright 2011-2012 Nick Korbel

This file is part of phpScheduleIt.

phpScheduleIt is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

phpScheduleIt is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with phpScheduleIt.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
 *
 */
class Installer {

    private $user;
    private $password;

    public function __construct($user, $password) {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param $should_create_db bool
     * @param $should_create_user bool
     * @param $should_create_sample_data bool
     * @return array|InstallationResult[]
     */
    public function InstallFresh($should_create_db, $should_create_user, $should_create_sample_data) {
        $results = array();
        // Calling static method of class Configuration
        $config = Configuration::Instance();
        // Get and Set configuration values
        $hostname = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_HOSTSPEC);
        $database_name = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_NAME);
        $database_user = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_USER);
        $database_password = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_PASSWORD);
        // Instantiating create-db sql string
        $create_database = new MySqlScript(ROOT_DIR . 'database_schema/create-db.sql');
        // Replacing the above default values with configured values
        $create_database->Replace('phpscheduleit2', $database_name);
        // Instantiating create-user sql string
        $create_user = new MySqlScript(ROOT_DIR . 'database_schema/create-user.sql');
        // Replacing the above default values with configured values
        $create_user->Replace('phpscheduleit2', $database_name);
        $create_user->Replace('schedule_user', $database_user);
        $create_user->Replace('localhost', $hostname);
        $create_user->Replace('password', $database_password);
        // Instantiating sample-data-utf8 sql string
        $populate_sample_data = new MySqlScript(ROOT_DIR . 'database_schema/sample-data-utf8.sql');
        // Replacing the above default values with configured values
        $populate_sample_data->Replace('phpscheduleit2', $database_name);
        // Instantiating the rest
        $create_schema = new MySqlScript(ROOT_DIR . 'database_schema/schema-utf8.sql');
        $populate_data = new MySqlScript(ROOT_DIR . 'database_schema/data-utf8.sql');

        /**
         *
         */
        if ($should_create_db) {
            $results[] = $this->ExecuteScript($hostname, 'mysql', $this->user, $this->password, $create_database);
        }

        $results[] = $this->ExecuteScript($hostname, $database_name, $this->user, $this->password, $create_schema);

        /**
         *
         */
        if ($should_create_user) {
            $results[] = $this->ExecuteScript($hostname, $database_name, $this->user, $this->password, $create_user);
        }

        $results[] = $this->ExecuteScript($hostname, $database_name, $this->user, $this->password, $populate_data);

        /**
         * Populate sample data given in /phpScheduleIt/database_schema/sample-data-utf8.sql
         */
        if ($should_create_sample_data) {
            $results[] = $this->ExecuteScript($hostname, $database_name, $this->user, $this->password, $populate_sample_data);
        }

        return $results;
    }

    public function ExecuteScript($hostname, $database_name, $db_user, $db_password, MySqlScript $script) {
        $result = new InstallationResult($script->Name());

        $sqlErrorCode = 0;
        $sqlErrorText = null;
        $sqlStmt = null;

        $link = mysql_connect($hostname, $db_user, $db_password);
        if (!$link) {
            $result->SetConnectionError();
            return $result;
        }

        $select_db_result = mysql_select_db($database_name, $link);
        if (!$select_db_result) {

            $result->SetAuthenticationError();
            return $result;
        }

        $sqlArray = explode(';', $script->GetFullSql());
        foreach ($sqlArray as $stmt) {
            if (strlen($stmt) > 3 && substr(ltrim($stmt), 0, 2) != '/*') {
                $queryResult = mysql_query($stmt);
                if (!$queryResult) {
                    $sqlErrorCode = mysql_errno();
                    $sqlErrorText = mysql_error();
                    $sqlStmt = $stmt;
                    break;
                }
            }
        }

        $result->SetResult($sqlErrorCode, $sqlErrorText, $sqlStmt);

        return $result;
    }

}
?>
