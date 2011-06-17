function GroupManagement(opts) {
	var options = opts;

	var elements = {
		activeId: $('#activeId'),
		groupList: $('table.list'),

		autocompleteSearch: $('#groupSearch'),

//		permissionsDialog: $('#permissionsDialog'),
//		passwordDialog: $('#passwordDialog'),
//
//		permissionsForm: $('#permissionsForm'),
//		passwordForm: $('#passwordForm'),

		addForm: $('#addScheduleForm')
	};

	var groups = new Object();

	GroupManagement.prototype.init = function() {

//		ConfigureAdminDialog(elements.permissionsDialog, 'Resource Permissions', 400, 500);
//		ConfigureAdminDialog(elements.passwordDialog, 'Reset Password', 400, 300);

		elements.groupList.delegate('a.update', 'click', function(e) {
			setActiveUserId($(this));
			e.preventDefault();
		});

		elements.groupList.delegate('.changeStatus', 'click', function() {
			changeStatus();
		});

		elements.autocompleteSearch.autocomplete(
		{
			source: function( request, response ) {
				$.ajax({
					url: options.groupAutocompleteUrl,
					dataType: "json",
					data: {
						term: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.Name,
								value: item.Id
							}
						}));
					}
				});
			},
			focus: function( event, ui ) {
				elements.autocompleteSearch.val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				elements.autocompleteSearch.val( ui.item.label );
				window.location.href = options.selectGroupUrl + ui.item.value
				return false;
			}
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
			elements.passwordDialog.dialog('close');
		};
		
		var error = function(errorText) { alert(errorText);};
		
//		ConfigureAdminForm(elements.permissionsForm, getSubmitCallback(options.actions.permissions), hidePermissionsDialog, error);
//		ConfigureAdminForm(elements.passwordForm, getSubmitCallback(options.actions.password), hidePasswordDialog, error);
	};

	GroupManagement.prototype.addUser = function(user) {
		users[user.id] = user;
	};

	var getSubmitCallback = function(action) {
		return function() {
			return options.submitUrl + "?uid=" + getActiveUserId() + "&action=" + action;
		};
	};

	function setActiveUserId(activeElement) {
		var id = activeElement.parents('td').siblings('td.id').find(':hidden').val();
		elements.activeId.val(id);
	}

	function getActiveUserId() {
		return elements.activeId.val();
	}

	function getActiveUser() {
		return users[getActiveUserId()];
	}

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
}