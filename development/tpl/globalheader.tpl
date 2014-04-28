<!DOCTYPE html>
{*
Copyright 2011-2014 Nick Korbel

This file is part of Booked Scheduler.

Booked Scheduler is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Booked Scheduler is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Booked Scheduler.  If not, see <http://www.gnu.org/licenses/>.
*}
<html lang="{$HtmlLang}" dir="{$HtmlTextDirection}">
<head>
	<title>{if $TitleKey neq ''}{translate key=$TitleKey args=$TitleArgs}{else}{$Title}{/if}</title>
	<meta http-equiv="Content-Type" content="text/html; charset={$Charset}"/>
	<meta name="robots" content="noindex"/>
	{if $ShouldLogout}
		<meta http-equiv="REFRESH"
			  content="{$SessionTimeoutSeconds};URL={$Path}logout.php?{QueryStringKeys::REDIRECT}={$smarty.server.REQUEST_URI|urlencode}">
	{/if}
	<link rel="shortcut icon" href="{$Path}favicon.ico"/>
	<link rel="icon" href="{$Path}favicon.ico"/>
	<!-- JavaScript -->
	{if $UseLocalJquery}
		{jsfile src="js/jquery-1.8.2.min.js"}
		{jsfile src="js/jquery-ui-1.9.0.custom.min.js"}
	{else}
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>
		<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="//cdn.jsdelivr.net/qtip2/2.2.0/jquery.qtip.min.js"></script>
	{/if}
	{jsfile src="phpscheduleit.js"}
	<!-- End JavaScript -->

	<!-- CSS -->
	{cssfile src="normalize.css"}
	{if $UseLocalJquery}
		{cssfile src="scripts/css/smoothness/jquery-ui-1.9.0.custom.min.css"}
	{else}
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/smoothness/jquery-ui.css"
			  type="text/css"></link>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css"
			  type="text/css"></link>
		<link rel="stylesheet" href="//cdn.jsdelivr.net/qtip2/2.2.0/jquery.qtip.min.css" type="text/css"></link>
	{/if}
	{cssfile src="nav.css"}
	{cssfile src="main.css"}
	{if $cssFiles neq ''}
		{assign var='CssFileList' value=','|explode:$cssFiles}
		{foreach from=$CssFileList item=cssFile}
			{cssfile src=$cssFile}
		{/foreach}
	{/if}
	{cssfile src=$CssUrl}
	{if $CssExtensionFile neq ''}
		{cssfile src=$CssExtensionFile}
	{/if}

	{if $printCssFiles neq ''}
		{assign var='PrintCssFileList' value=','|explode:$printCssFiles}
		{foreach from=$PrintCssFileList item=cssFile}
			<link rel='stylesheet' type='text/css' href='{$Path}{$cssFile}' media='print'/>
		{/foreach}
	{/if}
	<!-- End CSS -->
</head>
<body>

<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="{$HomeUrl}">{html_image src="$LogoUrl"}</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				{if $LoggedIn}
				<li><a href="{$Path}{Pages::DASHBOARD}">{translate key="Dashboard"}</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{translate key="MyAccount"} <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="{$Path}{Pages::PROFILE}">{translate key="Profile"}</a></li>
						<li><a href="{$Path}{Pages::PASSWORD}">{translate key="ChangePassword"}</a></li>
						<li>
							<a href="{$Path}{Pages::NOTIFICATION_PREFERENCES}">{translate key="NotificationPreferences"}</a>
						</li>
						{if $ShowParticipation}
							<li><a href="{$Path}{Pages::PARTICIPATION}">{translate key="OpenInvitations"}</a></li>
						{/if}
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{translate key="Schedule"} <b
								class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="{$Path}{Pages::SCHEDULE}">{translate key="Bookings"}</a></li>
						<li><a href="{$Path}{Pages::MY_CALENDAR}">{translate key="MyCalendar"}</a></li>
						<li><a href="{$Path}{Pages::CALENDAR}">{translate key="ResourceCalendar"}</a></li>
						<!--<li class="menuitem"><a href="#">{translate key="Current Status"}</a></li>-->
						<!--<li class="menuitem"><a href="{$Path}{Pages::OPENINGS}">{translate key="FindAnOpening"}</a></li>-->
					</ul>
				</li>
				{if $CanViewAdmin}
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{translate key="ApplicationManagement"}
						<b class="caret"></b></a>
					<ul class="dropdown-menu">
						{*<li class="title">{translate key="ManageReservations"}</li>*}
						<li><a href="{$Path}admin/manage_reservations.php" class="dropdown-toggle"
							   data-toggle="dropdown">{translate key="ManageReservations"}</a></li>
						<li><a href="{$Path}admin/manage_blackouts.php">{translate key="ManageBlackouts"}</a></li>

				</li>
				<li class="divider"></li>
				<li><a href="{$Path}admin/manage_resources.php" class="dropdown-toggle"
					   data-toggle="dropdown">{translate key="ManageResources"}</a></li>
				{*<ul class="dropdown-menu">*}
				<li><a href="{$Path}admin/manage_resource_groups.php">{translate key="ManageGroups"}</a></li>
				<li><a href="{$Path}admin/manage_accessories.php">{translate key="ManageAccessories"}</a></li>
				{*</ul>*}
				<li class="divider"></li>
				<li><a href="{$Path}admin/manage_schedules.php">{translate key="ManageSchedules"}</a>
				<li class="divider"></li>
				<li><a href="{$Path}admin/manage_users.php">{translate key="ManageUsers"}</a>
				</li>
				<li><a href="{$Path}admin/manage_groups.php">{translate key="ManageGroups"}</a>
				</li>
				<li><a href="{$Path}admin/manage_quotas.php">{translate key="ManageQuotas"}</a>
				</li>
				<li><a href="{$Path}admin/manage_announcements.php">{translate key="ManageAnnouncements"}</a>
				</li>
				<li class="dropdown-submenu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{translate key="Customization"}</a>
					<ul class="dropdown-menu">
						<li><a href="{$Path}admin/manage_attributes.php">{translate key="Attributes"}</a></li>
						{if $EnableConfigurationPage}
							<li>
								<a href="{$Path}admin/manage_configuration.php">{translate key="ManageConfiguration"}</a>
							</li>
						{/if}
						<li><a href="{$Path}admin/manage_theme.php">{translate key="LookAndFeel"}</a></li>
					</ul>
				</li>
				<li><a href="{$Path}admin/server_settings.php">{translate key="ServerSettings"}</a></li>
			</ul>
			</li>
			{/if}
			{if $CanViewResponsibilities}
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{translate key="Responsibilities"} <b
								class="caret"></b></a>
					<ul class="dropdown-menu">
						{if $CanViewGroupAdmin}
							<li><a href="{$Path}admin/manage_group_users.php">{translate key="ManageUsers"}</a></li>
							<li>
								<a href="{$Path}admin/manage_group_reservations.php">{translate key=GroupReservations}</a>
							</li>
							<li><a href="{$Path}admin/manage_admin_groups.php">{translate key="ManageGroups"}</a></li>
						{/if}
						{if $CanViewResourceAdmin || $CanViewScheduleAdmin}
							<li><a href="{$Path}admin/manage_admin_resources.php">{translate key="ManageResources"}</a>
							</li>
							<li><a href="{$Path}admin/manage_blackouts.php">{translate key="ManageBlackouts"}</a></li>
						{/if}
						{if $CanViewResourceAdmin}
							<li>
								<a href="{$Path}admin/manage_resource_reservations.php">{translate key=ResourceReservations}</a>
							</li>
						{/if}
						{if $CanViewScheduleAdmin}
							<li><a href="{$Path}admin/manage_admin_schedules.php">{translate key="ManageSchedules"}</a>
							</li>
							<li>
								<a href="{$Path}admin/manage_schedule_reservations.php">{translate key=ScheduleReservations}</a>
							</li>
						{/if}
					</ul>
				</li>
			{/if}
			{if $CanViewReports}
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{translate key="Reports"} <b
								class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="{$Path}reports/{Pages::REPORTS_GENERATE}">{translate key=GenerateReport}</a></li>
						<li><a href="{$Path}reports/{Pages::REPORTS_SAVED}">{translate key=MySavedReports}</a></li>
						<li><a href="{$Path}reports/{Pages::REPORTS_COMMON}">{translate key=CommonReports}</a></li>
					</ul>
				</li>
			{/if}
			{/if}

			</ul>
			<ul class="nav navbar-nav navbar-right">
				{if $ShowScheduleLink}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">{translate key="Schedule"} <b
									class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="view-schedule.php">{translate key='ViewSchedule'}</a></li>
						</ul>
					</li>
				{/if}
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{translate key="Help"} <b
								class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="{$Path}help.php">{translate key=Help}</a></li>
						{if $CanViewAdmin}
							<li><a href="{$Path}help.php?ht=admin">{translate key=Administration}</a></li>{/if}
						<li><a href="{$Path}help.php?ht=about">{translate key=About}</a></li>
					</ul>
				</li>
				{if $LoggedIn}
					<li><a href="{$Path}logout.php">{translate key="SignOut"}</a></li>
				{else}
					<li><a href="{$Path}index.php">{translate key="LogIn"}</a></li>
				{/if}
			</ul>
		</div>
	</div>
</nav>

<div id="main" class="container-fluid">