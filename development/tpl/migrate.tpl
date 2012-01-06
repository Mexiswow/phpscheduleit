{*
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
*}

{include file='globalheader.tpl'}

<h1>Migrate phpScheduleIt 1.2 to 2.0 (MySQL only)</h1>

<div>
{if $ShowResults}
    Migrated {$SchedulesMigratedCount} Schedules<br/>
    Migrated {$ResourcesMigratedCount} Resources<br/>
    Migrated {$AccessoriesMigratedCount} Accessories<br/>
    Migrated {$GroupsMigratedCount} Groups<br/>
    Migrated {$UsersMigratedCount} Users<br/>
    Migrated {$GroupsMigratedCount} Permissions<br/>
    Migrated {$GroupsMigratedCount} Reservations<br/>

    {else}

    <h5>This will copy all data from your phpScheduleIt v1.2 instance into 2.0. Due to changes in 2.0, this process will
        not be perfect. This process only migrates data which exists in your 1.2 instance but not in your 2.0 instance.
        Running this multiple times will not insert duplicate data.</h5>

    <h5>Known Issues</h5>
    <ul>
        <li>Recurring reservations will appear as single instances</li>
        <li>Application admin designations will not be migrated, only the user accounts</li>
        <li>Group admin designations will not be migrated, only the user accounts</li>
        <li>Open reservation invitations will be removed</li>
        <li>User timezones will all be set to the server's timezone</li>
        <li>At the time of writing, phpScheduleIt 2.0 is only available in English (US). User language preferences will be migrated but will have no effect</li>
        <li>User email preferences will not be migrated</li>
    </ul>

    <h3>There is no automated way to undo this process. Please check all migrated data for accuracy after the import
        completes.</h3>

    <form class="register" method="post" action="{$smarty.server.SCRIPT_NAME}">
        {if !$LegacyConnectionSucceeded}
            <div class="error">
                Could not connect to 1.2 database. Please confirm the settings below and try again.
            </div>
        {/if}

        <h4>phpScheduleIt 1.2 database settings</h4>
        <ul style="list-style: none;">
            <li>User: <input type="text" class="textbox" name="legacyUser"/></li>
            <li>Password: <input type="password" class="textbox" name="legacyPassword"/></li>
            <li>Hostspec: <input type="text" class="textbox" name="legacyHostSpec"/></li>
            <li>Database Name: <input type="text" class="textbox" name="legacyDatabaseName"/></li>
        </ul>

        <input type="submit" name="run" value="Run Migration" class="button"/>
    </form>
{/if}
</div>

{include file='globalfooter.tpl'}