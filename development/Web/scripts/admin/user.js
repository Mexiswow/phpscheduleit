function UserManagement(opts) {
	var options = opts;

	var elements = {
		activeId: $('#activeId'),
		userList: $('table.list'),

		userAutocomplete: $('#userSearch'),

		permissionsDialog: $('#permissionsDialog'),
		passwordDialog: $('#passwordDialog'),

		permissionsForm: $('#permissionsForm'),
		passwordForm: $('#passwordForm'),

		userDialog: $('#userDialog'),
		userForm: $('#userForm'),

		addForm: $('#addScheduleForm')
	};

	var users = new Object();


	UserManagement.prototype.init = function() {

		ConfigureAdminDialog(elements.permissionsDialog, 400, 500);
		ConfigureAdminDialog(elements.passwordDialog, 400, 300);
		ConfigureAdminDialog(elements.userDialog, 400, 400);

		elements.userList.delegate('a.update', 'click', function(e) {
			setActiveUserElement($(this));
			e.preventDefault();
		});

		elements.userList.delegate('.changeStatus', 'click', function() {
			changeStatus();
		});

		elements.userList.delegate('.changeGroups', 'click', function() {
			changeGroups();
		});

		elements.userList.delegate('.changePermissions', 'click', function() {
			changePermissions();
		});

		elements.userList.delegate('.resetPassword', 'click', function() {
			elements.passwordDialog.find(':password').val('');
			elements.passwordDialog.dialog('open');
		});

		elements.userList.delegate('.editable', 'click', function() {
			var userId = $(this).find('input:hidden.id').val();
			setActiveUserId(userId);
			changeUserInfo();
		});

		elements.userAutocomplete.userAutoComplete(options.userAutocompleteUrl, function(ui) {
			elements.userAutocomplete.val( ui.item.label );
			window.location.href = options.selectUserUrl + ui.item.value
		});

		$(".save").click(function() {
			$(this).closest('form').submit();
		});

		$(".cancel").click(function() {
			$(this).closest('.dialog').dialog("close");
		});

		var hidePermissionsDialog = function() {
			elements.permissionsDialog.dialog('close');
		};

		var hidePasswordDialog = function() {
			hideDialog(elements.passwordDialog);
		};

		var hideDialog = function(dialogElement) {
			dialogElement.dialog('close');
		};
		
		var error = function(errorText) { alert(errorText);};
		
		ConfigureAdminForm(elements.permissionsForm, getSubmitCallback(options.actions.permissions), hidePermissionsDialog, error);
		ConfigureAdminForm(elements.passwordForm, getSubmitCallback(options.actions.password), hidePasswordDialog, error);
		ConfigureAdminForm(elements.userForm, getSubmitCallback(options.actions.updateUser), hideDialog(elements.userDialog), error);
	};

	UserManagement.prototype.addUser = function(user) {
		users[user.id] = user;
	};

	var getSubmitCallback = function(action) {
		return function() {
			return options.submitUrl + "?uid=" + getActiveUserId() + "&action=" + action;
		};
	};

	function setActiveUserElement(activeElement) {
		var id = activeElement.parents('td').siblings('td.id').find(':hidden').val();
		setActiveUserId(id);
	}

	function setActiveUserId(id) {
		elements.activeId.val(id);
	}

	function getActiveUserId() {
		return elements.activeId.val();
	}

	function getActiveUser() {
		return users[getActiveUserId()];
	}

	var changeStatus = function() {
		var user = getActiveUser();

		if (user.isActive) {
			PerformAsyncAction($(this), getSubmitCallback(options.actions.deactivate))
		}
		else {
			PerformAsyncAction($(this), getSubmitCallback(options.actions.activate))
		}
	};

	var changeGroups = function () {
		var user = getActiveUser();
		var data = {dr: 'groups', id: user.id};
		$.get(opts.groupsUrl, data, function(response) {
			//$('#privilegesDialog').html(response).show();
		});
	};

	var changePermissions = function () {
		var user = getActiveUser();
		var data = {dr: 'permissions', uid: user.id};
		$.get(opts.permissionsUrl, data, function(resourceIds) {
			elements.permissionsForm.find(':checkbox').attr('checked', false);
			$.each(resourceIds, function(index, value) {
				elements.permissionsForm.find(':checkbox[value="' + value + '"]').attr('checked',true);
			});

			elements.permissionsDialog.dialog('open');
		});
	};

	var changeUserInfo = function () {
		var user = getActiveUser();

		$('#username').val(user.username);
		$('#fname').val(user.first);
		$('#lname').val(user.last);
		$('#email').val(user.email);
		$('#timezone').val(user.timezone);

		elements.userDialog.dialog('open');
	};
}