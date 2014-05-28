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
{include file='globalheader.tpl'}

<div id="page-login">
{if $ShowLoginError}
	<div id="loginError" class="alert alert-danger">
		{translate key='LoginError'}
	</div>
{/if}

<div class="row">
	<div id="loginbox" class="col-md-offset-4 col-md-4 default-box">
		<form role="form" name="login" id="login" class="login form-horizontal" method="post"  action="{$smarty.server.SCRIPT_NAME}">
			<div>
				{if $ShowUsernamePrompt}
					<div class="form-group">
						<label class="col-xs-3 control-label" for="email">{translate key='Email'}</label>

						<div class="col-xs-9"><input type="text" required="" class="form-control"
													  id="email" {formname key=EMAIL}
													  placeholder="{translate key=UsernameOrEmail}"/>
						</div>
					</div>
				{/if}

				{if $ShowPasswordPrompt}
					<div class="form-group">
						<label class="col-xs-3 control-label" for="password">{translate key='Password'}</label>

						<div class="col-xs-9">
							<input type="password" required="" id="password" {formname key=PASSWORD}
								   class="form-control"
								   value="" placeholder="{translate key=Password}"/>
						</div>
					</div>
				{/if}

				{if $ShowPersistLoginPrompt}
					<div class="form-group">
						<div class="col-xs-offset-3 col-xs-9">
							<div class="checkbox">
								<label>
									<input id="rememberMe" type="checkbox"
										   name="{FormKeys::PERSIST_LOGIN}"
										   value="true"/> {translate key='RememberMe'}
								</label>
							</div>
						</div>
					</div>
				{/if}

				<div class="form-group">
					<div class="col-xs-12">
						<button type="submit" class="btn btn-primary col-xs-12" name="{Actions::LOGIN}"
								value="submit">{translate key='LogIn'}</button>

						<input type="hidden" {formname key=RESUME} value="{$ResumeUrl}"/>
					</div>

				</div>
				<div class="login-links center-block">
					{if $ShowForgotPasswordPrompt}
						<a href="forgot.php">{translate key='ForgotMyPassword'}</a>
					{/if}
					{if $ShowForgotPasswordPrompt && $ShowRegisterLink}|{/if}
					{if $ShowRegisterLink}
						<a href="register.php">{translate key='CreateAnAccount'}</a>
					{/if}
				</div>

				<div class="login-links center-block">
					<button type="button" class="btn btn-link" data-toggle="collapse" data-target="#change-language-options">{translate key=ChangeLanguage}</button>
				</div>

				<div id="change-language-options" class="form-group collapse">
					<label class="col-xs-3 control-label" for="languageDropDown">{translate key='Language'}</label>

					<div class="col-xs-9">
						<select {formname key=LANGUAGE} class="form-control input-sm" id="languageDropDown">
							{object_html_options options=$Languages key='GetLanguageCode' label='GetDisplayName' selected=$SelectedLanguage}
						</select>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

</div>
{setfocus key='EMAIL'}

<script type="text/javascript">
	var url = 'index.php?{QueryStringKeys::LANGUAGE}=';
	$(document).ready(function ()
	{
		$('#languageDropDown').change(function ()
		{
			window.location.href = url + $(this).val();
		});

		var langCode = readCookie('{CookieKeys::LANGUAGE}');

		if (!langCode)
		{
		}
	});
</script>
{include file='globalfooter.tpl'}