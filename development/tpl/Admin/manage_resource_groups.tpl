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
{include file='globalheader.tpl' cssFiles='scripts/css/jqtree.css,css/admin.css'}

<h1>{translate key='ManageResourceGroups'}</h1>

<div id="globalError" class="error" style="display:none"></div>
<div class="admin">
	<div class="title">
		{translate key='ResourceGroups'}
	</div>

	<div>
		<div id="new-group">
			<form method="post" id="addGroupForm" ajaxAction="{ManageResourceGroupsActions::AddGroup}">
				<input type="text" name="{FormKeys::GROUP_NAME}" class="textbox new-group" size="30"/>
				<input type="hidden" name="{FormKeys::PARENT_ID}" />
				{html_image src="plus-circle.png" class="image-button" id="btnAddGroup"}
			</form>
		</div>
		<div id="group-tree"></div>
		<div id="resource-list">
			<ul>
				{foreach from=$Resources item=resource}
					<li class="resource-draggable" resource-name="{$resource->GetName()|escape:javascript}"
						resource-id="{$resource->GetId()}">{$resource->GetName()}</li>
				{/foreach}
			</ul>
		</div>
		<div class="clear">&nbsp;</div>
	</div>
</div>

<input type="hidden" id="activeId" value=""/>

<div id="renameDialog" class="dialog" style="display:none;" title="{translate key=Rename}">
	<form id="renameForm" method="post">
		{translate key='Name'}: <input id="editName" type="text" class="textbox required" maxlength="85"
									   style="width:250px" {formname key=RESOURCE_NAME} />
		<br/><br/>
		<button type="button" class="button save">{html_image src="disk-black.png"} {translate key='Rename'}</button>
		<button type="button" class="button cancel">{html_image src="slash.png"} {translate key='Cancel'}</button>
	</form>
</div>

<div id="deleteDialog" class="dialog" style="display:none;" title="{translate key=Delete}">
	<form id="deleteForm" method="post">
		<div class="error" style="margin-bottom: 25px;">
			<h3>{translate key=DeleteWarning}</h3>
			<br/>{translate key=DeleteResourceWarning}:
			<ul>
				<li>{translate key=DeleteResourceWarningReservations}</li>
				<li>{translate key=DeleteResourceWarningPermissions}</li>
			</ul>
			<br/>
			{translate key=DeleteResourceWarningReassign}
		</div>

		<button type="button" class="button save">{html_image src="cross-button.png"} {translate key='Delete'}</button>
		<button type="button" class="button cancel">{html_image src="slash.png"} {translate key='Cancel'}</button>
	</form>
</div>

{html_image src="admin-ajax-indicator.gif" class="indicator" style="display:none;"}
<script type="text/javascript" src="{$Path}scripts/js/jquery.watermark.min.js"></script>
<script type="text/javascript" src="{$Path}scripts/admin/edit.js"></script>
<script type="text/javascript" src="{$Path}scripts/admin/resource-groups.js"></script>
<script type="text/javascript" src="{$Path}scripts/js/jquery.form-3.09.min.js"></script>
<script type="text/javascript" src="{$Path}scripts/js/tree.jquery.js"></script>
<script type="text/javascript" src="{$Path}scripts/js/jquery.cookie.js"></script>

<script type="text/javascript">

	$(document).ready(function ()
	{
		var actions = {
			addResource: '{ManageResourceGroupsActions::AddResource}',
			removeResource: '{ManageResourceGroupsActions::RemoveResource}'
		};

		var groupOptions = {
			submitUrl: '{$smarty.server.SCRIPT_NAME}',
			actions: actions,
			newGroupText: '{translate key=AddNewGroup}'
		};

		var groupManagement = new ResourceGroupManagement(groupOptions);
		groupManagement.init({$ResourceGroups});
	});

</script>

{include file='globalfooter.tpl'}