{*
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
*}
{include file='globalheader.tpl'}
<h1>Ajuda do phpScheduleIt</h1>

<div id="help">
<h2>Registro</h2>

<p>
	Registration is required in order to use phpScheduleIt if you administrator has enabled it. After your account
	has been registered
	you will be able to log in and access any resources that you have permission to.
</p>

<h2>Reservas</h2>

<p>
	Under the Schedule menu item you will find the Booking item. This will show you the available, reserved and
	blocked slots on the schedule and allow you to book
	resources that you have permission to.
</p>

<h3>Reserva R&aacute;pida</h3>

<p>
	On the Bookings page, find the resource, date and time you'd like to book. Clicking on the time slot will allow
	you change the details of the reservation. Clicking the
	Create button will check availability, book the reservation and send out any emails. You will be given a
	reference number to use for reservation follow-up.
</p>

<p>Any changes made to a reservation will not take effect until you save the reservation.</p>

<p>Only Application Administrators can create reservations in the past.</p>

<h3>M&uacute;ltiplos Recursos</h3>

<p>You can book all resources that you have permission as part of a single reservation. To add more resources to
	your reservation, click the More Resources link, displayed next to the name of the primary resource you are
	reserving. You will then able to add more resources by selecting them and clicking the Done button.</p>

<p>To remove additional resources from your reservation, click the More Resources link, deselect the resources you
	want to remove, and click the Done button.</p>

<p>Additional resources will be subject to the same rules as primary resources. For example, this means that if you
	attempt to create a 2 hour reservation with Resource 1, which has a maximum length of 3 hours and with Resource
	2, which
	has a maximum length of 1 hour, your reservation will be denied.</p>

<p>You can view the configuration details of a resource by hovering over the resource name.</p>

<h3>Datas Recorrentes</h3>

<p>A reservation can be configured to recur a number of different ways. For all repeat options the Until date is
	inclusive.</p>

<p>The repeat options allow for flexible recurrence possibilities. For example: Repeat Daily every 2 days will
	create a reservation every other day for your specified time. Repeat Weekly, every 1 week on Monday, Wednesday,
	Friday will create a reservation on each of those days every week at your specified time. If you were creating a
	reservation on 2011-01-15, repeating Monthly, every 3 months on the day of month would create a reservation
	every third month on the 15th. Since 2011-01-15 is the third Saturday of January, the same example with the day
	of week selected would repeat every third month on the third Saturday of that month.</p>

<h3>Participantes Adicionais</h3>

<p>You can either Add Participants or Invite Others when booking a reservation. Adding someone will include them on
	the reservation and will not send an invitation.
	The added user will receive an email. Inviting a user will send an invitation email and give the user an option
	to Accept or Decline the invitation. Accepting an
	invitation adds the user to the participants list. Declining an invitation removes the user from the invitees
	list.
</p>

<p>
	The total number of participants is limited by the resource's participant capacity.
</p>

<h3>Acess&oacute;rios</h3>

<p>Accessories can be thought of as objects used during a reservation. Examples may be projectors or chairs. To add
	accessories to your reservation, click the Add link to the right of the Accessories title. From there you will
	be able to select a quantity for each of the available accessories. The quantity available during your
	reservation time will depend on how many accessories are already reserved.</p>

<h3>Reserva em Nome de Outros</h3>

<p>Application Administrators and Group Administrators can book reservations on behalf of other users by clicking
	the Change link to the right of the user's name.</p>

<p>Application Administrators and Group Administrators can also modify and delete reservations owned by other
	users.</p>

<h2>Atualizando uma Reserva</h2>

<p>You can update any reservation that you have created or that was created on your behalf.</p>

<h3>Atualizando Inst&acirc;ncias Espec&iacute;ficas de uma S&eacute;rie</h3>

<p>
	If a reservation is set up to repeat, then a series is created. After you make changes and Update the
	reservation, you will be asked which instances of the series you want to apply the changes to. You can
	apply your changes to the instance that you are viewing (Only This Instance) and no other instances will be
	changed.
	You can update All Instances to apply the change to every reservation instance that has not yet occurred. You
	can also apply the change only to Future Instances, which will update all reservation instances including and
	after the instance you are currently viewing.
</p>

<p>Only Application Administrators can update reservations in the past.</p>

<h2>Excluindo uma Reserva</h2>

<p>Deleting a reservation completely removes it from the schedule. It will no longer be visible anywhere in
	phpScheduleIt</p>

<h3>Excluindo Inst&acirc;ncias Espec&iacute;ficas de uma S&eacute;rie</h3>

<p>Similar to updating a reservation, when deleting you can select which instances you want to delete.</p>

<p>Only Application Administrators can delete reservations in the past.</p>

<h2>Adicionando uma Reserva no Outlook &reg;</h2>

<p>When viewing or updating a reservation you will see a button to Add to Outlook. If Outlook is installed on your
	computer then you should be asked to add the meeting. If it is not installed you will be prompted to download an
	.ics file. This is a standard calendar format. You can use this file to add the reservation to any application
	that supports the iCalendar file format.</p>

<h2>Cotas</h2>

<p>Administrators have the ability to configure quota rules based on a variety of criteria. If your reservation
	would violate any quota, you will be notified and the reservation will be denied.</p>

<h2>Administra&ccedil;&atilde;o</h2>

<p>If you are in an Application Administrator role then you will see the Application Management menu item. All
	administrative tasks can be found here.</p>

<h3>Configurando Agendas</h3>

<p>
	When installing phpScheduleIt a default schedule will be created with out of the box settings. From the
	Schedules menu option you can view and edit attributes of the current schedules.
</p>

<p>Each schedule must have a layout defined for it. This controls the availability of the resources on that
	schedule. Clicking the Change Layout link will bring up the layout editor. Here you can create and change the
	time slots that are available for reservation and blocked from reservation. There is no restriction on the slot
	times, but you must provide slot values for all 24 hours of the day, one per line. Also, the time format must be
	in 24 hour time.
	You can also provide a display label for any or all slots, if you wish.</p>

<p>A slot without a label should be formatted like this: 10:25 - 16:50</p>

<p>A slot with a label should be formatted like this: 10:25 - 16:50 Schedule Period 4</p>

<p>Below the slot configuration windows is a slot creation wizard. This will set up available slots at the given
	interval between the start and end times.</p>

<h3>Configurando Recursos</h3>

<p>You can view and manage resources from the Resources menu option. Here you can change the attributes and usage
	configuration of a resource.
</p>

<p>Resources in phpScheduleIt can be anything you want to make bookable, such as rooms or equipment. Every resource
	must be assigned to a schedule in order for it to be bookable. The resource will inherit whatever layout the
	schedule uses.</p>

<p>Setting a minimum reservation duration will prevent booking from lasting longer than the set amount. The default is
	no minimum.</p>

<p>Setting a maximum reservation duration will prevent booking from lasting shorter than the set amount. The default is
	no maximum.</p>

<p>Setting a resource to require approval will place all bookings for that resource into a pending state until approved.
	The default is no approval required.</p>

<p>Setting a resource to automatically grant permission to it will grant all new users permission to access the resource
	at registration time. The default is to automatically grant permissions.</p>

<p>You can require a booking lead time by setting a resource to require a certain number of days/hours/minutes
	notification. For example, if it is currently 10:30 AM on a Monday and the resource requires 1 days notification,
	the resource will not be able to be booked until 10:30 AM on Sunday. The default is that reservations can be made up
	until the current time.</p>

<p>You can prevent resources from being booked too far into the future by requiring a maximum notification of
	days/hours/minutes. For example, if it is currently 10:30 AM on a Monday and the resource cannot end more than 1 day
	in the future, the resource will bot be able to be booked past 10:30 AM on Tuesday. The default is no maximum.</p>

<p>Certain resources cannot have a usage capacity. For example, some conference rooms may only hold up to 8 people.
	Setting the resource capacity will prevent any more than the configured number of participants at one time,
	excluding the organizer. The default is that resources have unlimited capacity.</p>

<p>Application Administrators are exempt from usage constraints.</p>

<h3>Imagens de Recursos</h3>

<p>You can set a resource image which will be displayed when viewing resource details from the reservation page. This
	requires php_gd2 to be installed and enabled in your php.ini file. <a href="http://www.php.net/manual/en/book.image.php">More Details</a></p>

<h3>Configurando Acess&oacute;rios</h3>

<p>Accessories can be thought of as objects used during a reservation. Examples may be projectors or chairs in a
	conference room.</p>

<p>Accessories can be viewed and managed from the Accessories menu item, under the Resources menu item. Setting a
	accessory quantity will prevent more than that number of accessories from being booked at a time.</p>

<h3>Configurando Cotas</h3>

<p>Quotas prevent reservations from being booked based on a configurable limit. The quota system in phpScheduleIt is
	very flexible, allowing you to build limits based on reservation length and number reservations. Also, quota limits
	"stack". For example, if a quota exists limiting a resource to 5 hours per day and another quota exists limiting to
	4 reservations per day a user would be able to make 4 hour-long reservations but would be restricting from making 3
	two-hour-long reservations. This allows powerful quota combinations to be built.</p>

<p>Application Administrators are exempt from quota limits.</p>

<h3>Configurando An&uacute;ncios</h3>

<p>Announcements are a very simple way to display notifications to phpScheduleIt users. From the Announcements menu item
	you can view and manage the announcements that are displayed on users dashboards. An announcement can be configured
	with an optional start and end date. An optional priority level is also available, which sorts announcements from 1
	to 10.</p>

<p>HTML is allows within the announcement text. This allows you to embed links or images from anywhere on the web.</p>

<h3>Configurando Grupos</h3>

<p>Groups in phpScheduleIt organize users, control resource access permissions and define roles within the
	application.</p>

<h3>Fun&ccedil;&otilde;es</h3>

<p>Roles give a group of users the authorization to perform certain actions.</p>

<p>Users that belong to a group that is given the Application Administrator role are open to full administrative
	privileges. This role has nearly zero restrictions on what resources can be booked. It can manage all aspects of the
	application.</p>

<p>Users that belong to a group that is given the Group Administrator role are able to reserve on behalf of and manage
	users within that group.</p>

<h3>Exibindo e Gerenciando Reservas</h3>

<p>You can view and manage reservations from the Reservations menu item. By default you will see the last 7 days and the
	next 7 days worth of reservations. This can be filtered more or less granular depending on what you are looking for.
	This tool allows you to quickly find an act on a reservation. You can also export the list of filtered reservations
	to CSV format for further reporting.</p>

<h3>Aprova&ccedil;&atilde;o de Reserva</h3>

<p>From the Reservations admin tool you will be able to view and approve pending reservations. Pending reservations will
	be highlighted.</p>

<h3>Exibindo e Gerenciando Usu&aacute;rios</h3>

<p>You can add, view, and manage all registered users from the Users menu item. This tool allows you to change resource
	access permissions of individual users, deactivate or delete accounts, reset user passwords, and edit user details.
	You can also add new users to phpScheduleIt. This is especially useful if self-registration is turned off.</p>

<h2>Configura&ccedil;&atilde;o</h2>

<p>Some of phpScheduleIt's functionality can only be controlled by editing the config file.</p>

<p class="setting"><span>server.timezone</span>This must reflect the timezone of the server that phpScheduleIt is hosted
	on. You can view the current timezone from the Server Settings menu item. Possible values are located here:
	http://php.net/manual/en/timezones.php</p>

<p class="setting"><span>allow.self.registration</span>If users are allowed to register new accounts.</p>

<p class="setting"><span>admin.email</span>The email address of the main application administrator</p>

<p class="setting"><span>default.page.size</span>The initial number of rows for any page that displays a list of data
</p>

<p class="setting"><span>enable.email</span>Whether or not any emails are sent out of phpScheduleIt</p>

<p class="setting"><span>default.language</span>Default language for all users. This can be any language in the
	phpScheduleIt lang directory</p>

<p class="setting"><span>script.url</span>The full public URL to the root of this instance of phpScheduleIt. This should
	be the Web directory which contains index.php</p>

<p class="setting"><span>password.pattern</span>A regular expression to enforce password complexity during user account
	registration</p>

<p class="setting"><span>show.inaccessible.resources</span>Whether or not resources that are not accessible to the user
	are displayed in the schedule</p>

<p class="setting"><span>notify.created</span>Whether or not application administrators should receive emails when new
	reservations are booked</p>

<p class="setting"><span>image.upload.directory</span>The physical directory relative to the phpScheduleIt directory to
	store images. This directory will need to be writable.</p>

<p class="setting"><span>image.upload.url</span>The URL relative to script.url where uploaded images can be viewed from
</p>

<p class="setting"><span>cache.templates</span>Whether or not templates are cached. It is recommended to set this to
	true, as long as tpl_c is writable</p>

<p class="setting"><span>registration.captcha.enabled</span>Whether or not captcha image security is enabled during user
	account registration</p>

<p class="setting"><span>inactivity.timeout</span>Number of minutes before the user is automatically logged out</p>

<p class="setting"><span>['database']['type']</span>Any PEAR::MDB2 supported type</p>

<p class="setting"><span>['database']['user']</span>Database user with access to the configured database</p>

<p class="setting"><span>['database']['password']</span>Password for the database user</p>

<p class="setting"><span>['database']['hostspec']</span>Database host URL or named pipe</p>

<p class="setting"><span>['database']['name']</span>Name of phpScheduleIt database</p>

<p class="setting"><span>['phpmailer']['mailer']</span>PHP email library. Options are mail, smtp, sendmail, qmail</p>

<h2>Plugins</h2>

<p>The following components are currently pluggable:</p>

<ul>
	<li>Authentication - Who is allowed to log in</li>
	<li>Authorization - What a user can do when you are logged in</li>
	<li>Permission - What resources a user has access to</li>
	<li>Pre Reservation - What happens before a reservation is booked</li>
	<li>Post Reservation - What happens after a reservation is booked</li>
</ul>

<p>
	To enable a plugin, set the value of the config setting to the name of the plugin folder. For example, to enable
	LDAP
	authentication, set
	$conf['settings']['plugins']['Authentication'] = 'Ldap';</p>

<p>Plugins may have their own configuration files. For LDAP, rename or copy
	/plugins/Authentication/Ldap/Ldap.config.dist to /plugins/Authentication/Ldap/Ldap.config and edit all values that
	are applicable to your environment.</p>

<h3>Instalalando Plugins</h3>

<p>To install a new plugin copy the folder to either the Authentication, Authorization and Permission directory. Then
	change either $conf['settings']['plugins']['Authentication'], $conf['settings']['plugins']['Authorization'] or
	$conf['settings']['plugins']['Permission'] in config.php to the name of that folder.</p>

{include file="support-and-credits.tpl"}
</div>

{include file='globalfooter.tpl'}