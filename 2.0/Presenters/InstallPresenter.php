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
require_once(ROOT_DIR . 'Presenters/Installer.php');
require_once(ROOT_DIR . 'Presenters/MySqlScript.php');
require_once(ROOT_DIR . 'Presenters/InstallationResult.php');

/**
 * Installation Presenter class
 */
class InstallPresenter {

    /**
     * @var \IInstallPage
     */
    private $page;

    const VALIDATED_INSTALL = 'validated_install';

    public function __construct(IInstallPage $page) {
        //ServiceLocator::GetServer()->SetSession(SessionKeys::INSTALLATION, null);
        $this->page = $page;
    }

    /**
     * Get and Set data to be process by template engine
     * @return type
     */
    public function PageLoad() {
        if ($this->page->RunningInstall()) {
            $this->RunInstall();
            return;
        }

        $dbname = Configuration::Instance()->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_NAME);
        $dbuser = Configuration::Instance()->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_USER);
        $dbhost = Configuration::Instance()->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_HOSTSPEC);

        $this->page->SetDatabaseConfig($dbname, $dbuser, $dbhost);

        $this->CheckForInstallPasswordInConfig();
        $this->CheckForInstallPasswordProvided();
        $this->CheckForAuthentication();
    }

    public function CheckForInstallPasswordInConfig() {
        $installPassword = Configuration::Instance()->GetKey(ConfigKeys::INSTALLATION_PASSWORD);

        if (empty($installPassword)) {
            $this->page->SetInstallPasswordMissing(true);
            return;
        }

        $this->page->SetInstallPasswordMissing(false);
    }

    private function CheckForInstallPasswordProvided() {
        if ($this->IsAuthenticated()) {
            return;
        }

        $installPassword = $this->page->GetInstallPassword();

        if (empty($installPassword)) {
            $this->page->SetShowPasswordPrompt(true);
            return;
        }

        $validated = $this->Validate($installPassword);
        if (!$validated) {
            $this->page->SetShowPasswordPrompt(true);
            $this->page->SetShowInvalidPassword(true);
            return;
        }

        $this->page->SetShowPasswordPrompt(false);
        $this->page->SetShowInvalidPassword(false);
    }

    private function CheckForAuthentication() {
        if ($this->IsAuthenticated()) {
            $this->page->SetShowDatabasePrompt(true);
            return;
        }

        $this->page->SetShowDatabasePrompt(false);
    }

    private function IsAuthenticated() {
        return ServiceLocator::GetServer()->GetSession(SessionKeys::INSTALLATION) == self::VALIDATED_INSTALL;
    }

    private function Validate($installPassword) {
        $validated = $installPassword == Configuration::Instance()->GetKey(ConfigKeys::INSTALLATION_PASSWORD);

        if ($validated) {
            ServiceLocator::GetServer()->SetSession(SessionKeys::INSTALLATION, self::VALIDATED_INSTALL);
        } else {
            ServiceLocator::GetServer()->SetSession(SessionKeys::INSTALLATION, null);
        }

        return $validated;
    }

    private function RunInstall() {
        $install = new Installer($this->page->GetInstallUser(), $this->page->GetInstallUserPassword());

        $results = $install->InstallFresh($this->page->GetShouldCreateDatabase(), $this->page->GetShouldCreateUser(), $this->page->GetShouldCreateSampleData());

        $this->page->SetInstallResults($results);
    }

}
?>
