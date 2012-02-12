<?php
/**
Copyright 2012 Nick Korbel

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

require_once(ROOT_DIR . 'lang/AvailableLanguage.php');

class AvailableLanguages
{
    /**
     * @return array|AvailableLanguage[]
     */
    public static function GetAvailableLanguages()
    {
        return array(
        			'en_us' => new AvailableLanguage('en_us', 'en_us.php', 'English US'),
        			'en_gb' => new AvailableLanguage('en_gb', 'en_gb.php', 'English GB'),
        			'fr_fr' => new AvailableLanguage('fr_fr', 'fr_fr.php', 'Fran&ccedil;ais'),
        		);
    }
}

?>