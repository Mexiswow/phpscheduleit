{include file='header.tpl'}
<form name="login" method="post" action="{$smarty.server.SCRIPT_NAME}">
    <table width="450" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr>
            <td class="login_table_border">
                <table width="100%" border="0" cellspacing="0" cellpadding="3" class="login_table">
                    <tr id="header">
                        <td colspan="2">
                            <h5 align="center">{translate key='Log In'}</h5>
                        </td>
                    </tr>
                    <tr>
                        <td width="150">
                            <p>
                                <b>{if $UseLogonName} {translate key='Logon name'} {else} {translate key='Email address'} {/if}</b>
                            </p>
                        </td>
                        <td>
                            <input type="text" name="email" class="textbox" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>
                                <b>{translate key='Password'}</b>
                            </p>
                        </td>
                        <td>
                            <input type="password" name="password" class="textbox" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>
                                <b>{translate key='Language'}</b>
                            </p>
                        </td>
                        <td>                      
							<select name="{constant echo='FormKeys::LANGUAGE'}" class="textbox">
								{html_options options=$Languages selected=$CurrentLanguage}	
							</select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>
                                <b>{translate key='Keep me logged in'}</b>
                            </p>
                        </td>
                        <td>
                            <input type="checkbox" name="setCookie" value="true" />
                        </td>
                    </tr>
                    <tr id="footer">
                        <td colspan="2">
	                        <p align="center">
	                            <input type="submit" name="login" value="{translate key='Log In'}" class="button" />
	                            <input type="hidden" name="resume" value="{$ResumeUrl}" />
	                        </p>
	                        {if $ShowRegisterLink} 
	                        <h4 align="center" style="margin-bottom:1px;">
	                            <b>{translate key='First time user'}</b>
	                            {html_link href="register.php" key="Click here to register"}
	                        </h4>
                        {/if}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p align="center">
    <a href="roschedule.php">{translate key='View Schedule'}</a>
    | 
    <a href="forgot_pwd.php">{translate key='I Forgot My Password'}</a>
    | 
    <a href="javascript: help();">{translate key='Help'}</a>
    </p>
</form>
{include file='footer.tpl'}