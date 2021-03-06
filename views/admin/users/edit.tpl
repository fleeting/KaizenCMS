{include file="inc_header.tpl" page_title="Users :: Edit User" menu="users" page_style="fullContent"}

<section id="content" class="content">
	<header>
		<h2>Manage Users &raquo; Edit User</h2>
	</header>

	<section class="inner-content">
		<form method="post" action="/admin/users/edit/s/">
			<fieldset>
				<legend>{$aUser.fname|clean_html} {$aUser.lname|clean_html}</legend>
				<label>*Username:</label><br />
				<input type="text" name="username" maxlength="100" value="{$aUser.username|clean_html}"><br />
				<label>Password: (New password)</label><br />
				<input type="text" name="password" maxlength="100"><br />
				<label>*Email:</label><br />
				<input type="text" name="email_address" maxlength="100" value="{$aUser.email_address|clean_html}"><br />
				<label>*First Name:</label><br />
				<input type="text" name="fname" maxlength="100" value="{$aUser.fname|clean_html}"><br />
				<label>*Last Name:</label><br />
				<input type="text" name="lname" maxlength="100" value="{$aUser.lname|clean_html}"><br />
				{if $sSuperAdmin && $aUser.id != 1}
					<input type="checkbox" name="super" id="form_super" value="1"{if $aUser.super == 1} checked="checked"{/if}> <label for="form_super">Super Admin</label><br />
				{else}
					<input type="hidden" name="super" id="form_super" value="{$aUser.super}">
				{/if}
			</fieldset>
			
			{if $aUser.id != 1}
			<fieldset id="fieldset_categories">
				<legend>Privileges:</legend>
				<p class="selectOptions">Select: <a href="#" class="checkAll">All</a>, <a href="#" class="uncheckAll">None</a></p>
				<ul class="categories">
					{foreach from=$aAdminFullMenu item=aMenu key=x}
						<li>
							<input id="menu_{$aMenu.id}" type="checkbox" name="privileges[]" value="{$x}"
								{if in_array($x, $aUser.privileges)} checked="checked"{/if}>
							<label style="display: inline;" for="menu_{$aMenu.id}">{$aMenu.title|clean_html}</label>
						</li>
					{/foreach}
				</ul>
			</fieldset><br />
			{/if}
			<input type="submit" value="Save Changes">
			<a class="cancel" href="/admin/users/" title="Cancel">Cancel</a>
			<input type="hidden" name="id" value="{$aUser.id}">
		</form>
	</section>
</section>
<script type="text/javascript">
$(function(){ldelim}
	$("form").validateForm([
		"required,username,Username is required",
		"required,email_address,An email address is required",
		"valid_email,email_address,A valid email address is required",
		"required,fname,First Name is required",
		"required,lname,Last Name is required"{if $aUser.id != 1},
		"required,privileges[],You must select at least one privilege"{/if}
	]);
{rdelim});
</script>
{include file="inc_footer.tpl"}