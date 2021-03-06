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
{include file='..\..\tpl\Email\emailheader.tpl'}
	Резервационна информация:
	<br/>
	<br/>
	
	Начало: {formatdate date=$StartDate key=reservation_email}<br/>
	Край: {formatdate date=$EndDate key=reservation_email}<br/>
	{if $ResourceNames|count > 1}
		Ресурси:<br/>
		{foreach from=$ResourceNames item=resourceName}
			{$resourceName}<br/>
		{/foreach}
		{else}
		Ресурс: {$ResourceName}<br/>
	{/if}
	Заглавие: {$Title}<br/>
	Описание: {$Description|nl2br}<br/>
	
	{if count($RepeatDates) gt 0}
		<br/>
		Резервацията се отнася за следните дати:
		<br/>
	{/if}
	
	{foreach from=$RepeatDates item=date name=dates}
		{formatdate date=$date}<br/>
	{/foreach}

	{if $Accessories|count > 0}
		<br/>Accessories:<br/>
		{foreach from=$Accessories item=accessory}
			({$accessory->QuantityReserved}) {$accessory->Name}<br/>
		{/foreach}
	{/if}

	{if $RequiresApproval}
		<br/>
		Един или повече от ресурсите изискват одобрение преди употреба. Тази резервация ще чака докато бъде одобрена.
	{/if}
	
	<br/>
	Участие? <a href="{$ScriptUrl}/{$AcceptUrl}">Да</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Не</a>
	<br/>

	<a href="{$ScriptUrl}/{$ReservationUrl}">Разгледай тази резервация</a> |
	<a href="{$ScriptUrl}/{$ICalUrl}">Добави в Outlook</a> |
	<a href="{$ScriptUrl}">Влизане в phpScheduleIt</a>
	
{include file='..\..\tpl\Email\emailfooter.tpl'}