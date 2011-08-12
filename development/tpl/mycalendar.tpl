{include file='globalheader.tpl' cssFiles='css/calendar.css'}

Day Week Month List

<div>

	<h1>
		<a href="{Pages::MY_CALENDAR}?{QueryStringKeys::MONTH}={$PrevMonth}&{QueryStringKeys::YEAR}={$PrevYear}"><img src="img/arrow_large_left.png" alt="Back" /></a>
		{$MonthName} {$Year}
		<a href="{Pages::MY_CALENDAR}?{QueryStringKeys::MONTH}={$NextMonth}&{QueryStringKeys::YEAR}={$NextYear}"><img src="img/arrow_large_right.png" alt="Forward" /></a>
	</h1>

</div>

<table class="monthCalendar">
	<tr class="dayName">
		{foreach from=$HeaderLabels item=label}
			<th>{$label}</th>
		{/foreach}
	</tr>
{foreach from=$Month->Weeks() item=week}
	<tr>
		{foreach from=$week->Days() item=day}
			{assign var=class value='day'}

			{if $day->IsHighlighted()}
				{assign var=class value='today'}
			{/if}

			{if $day->IsUnimportant()}
				{assign var=class value='unimportant'}
			{/if}

			<td class="{$class}">
				<h3>{$day->DayOfMonth()}</h3>

				{foreach from=$day->Reservations() item=reservation}
					<div>
						<a href="{Pages::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}={$reservation->ReferenceNumber}">
							{formatdate format='h:i A' date=$day->GetAdjustedStartDate($reservation)} {$reservation->ResourceName}
						</a>
					</div>
				{/foreach}
			</td>
		{/foreach}
	</tr>
{/foreach}
</table>

<script type="text/javascript" src="scripts/calendar.js"></script>
<script type="text/javascript">
$(document).ready(function() {

	var calendar = new Calendar();
	calendar.init();

});
</script>

{include file='globalfooter.tpl'}