{*
Copyright 2011-2013 Nick Korbel

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

{extends file="schedule.tpl"}

{$Timezone}

{block name="reservations"}

	{assign var=TodaysDate value=Date::Now()}
<div id="reservations">
    <table class="reservations" border="1" cellpadding="0" width="100%">
		{capture name="resources"}
            <tr>
                <td>&nbsp</td>
				{foreach from=$Resources item=resource name=resource_loop}
					{assign var=resourceId value=$resource->Id}
					{assign var=href value="{Pages::RESERVATION}?rid={$resource->Id}&sid={$ScheduleId}"}

                    <td class="resourcename" resourceId="{$resource->Id}">
						{if $resource->CanAccess}
                            <a href="{$href}" resourceId="{$resource->Id}"
                               class="resourceNameSelector">{$resource->Name}</a>
							{else}
							{$resource->Name}
						{/if}
                    </td>
				{/foreach}
            </tr>
		{/capture}

		{foreach from=$BoundDates item=date}
			{if $TodaysDate->DateEquals($date)}
                <tr class="today">{else}
            <tr>{/if}

			{$smarty.capture.resources}
            <td class="resdate" colspan="{$Resources|@count+1}">{formatdate date=$date key="schedule_daily"}</td></tr>
			{foreach from=$DailyLayout->GetPeriods($date) item=period name=period_loop}
                <tr class="slots" id="{$period->Id()}">
                    <td class="reslabel">{$period->Label($date)}</td>
                </tr>
			{/foreach}
		{/foreach}
    </table>
</div>

<input type="hidden" value="{$ScheduleId}" id="scheduleId"/>

{/block}

{block name="scripts"}

<script type="text/javascript" src="{$Path}scripts/js/jquery.qtip.min.js"></script>
<script type="text/javascript" src="{$Path}scripts/schedule.js"></script>
<script type="text/javascript" src="{$Path}scripts/resourcePopup.js"></script>

<script type="text/javascript">

    $(document).ready(function ()
    {
        var scheduleOpts = {
            reservationUrlTemplate:"{$Path}{Pages::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}=[referenceNumber]",
            summaryPopupUrl:"{$Path}ajax/respopup.php",
            setDefaultScheduleUrl:"{$Path}{Pages::PROFILE}?action=changeDefaultSchedule&{QueryStringKeys::SCHEDULE_ID}=[scheduleId]",
            cookieName:"{$CookieName}",
            cookieValue:"{$CookieValue}"
        };

        var table = $('#reservations table');
		{foreach from=$Resources item=resource name=resource_loop}
			{foreach from=$BoundDates item=date}
				{assign var=resourceId value=$resource->Id}
				{assign var=slots value=$DailyLayout->GetLayout($date, $resourceId)}
				{assign var=href value="{Pages::RESERVATION}?rid={$resource->Id}&sid={$ScheduleId}&rd={formatdate date=$date key=url}"}

				{foreach from=$slots item=slot name=slot_loop}
					{assign var=slotRef value="{$slot->BeginDate()->Format('YmdHis')}{$resourceId}"}
                    var tr = $('#' +{$slot->BeginDate()->Format('YmdHis')});
                    var td = tr.find('td:last');
					{capture assign="slotContent"}
						{displaySlot Slot=$slot Href="$href" AccessAllowed=$resource->CanAccess SlotRef=$slotRef spantype='row'}
					{/capture}
                    //td.after('{$slotContent|trim|regex_replace:"/[\r\t\n]/":" "}');
				{/foreach}
			{/foreach}
		{/foreach}

        var schedule = new Schedule(scheduleOpts);
        schedule.init();
    })
</script>

{/block}