{include file="inc_header.tpl" page_title="Services :: Edit Service" menu="services" page_style="halfContent"}
{head}
<script src="/scripts/jquery-iphone-checkboxes/jquery.iphone-style-checkboxes.js"></script>
<link rel="stylesheet" href="/scripts/jquery-iphone-checkboxes/style.css" type="text/css">
{/head}
{assign var=subMenu value="Services"}

<form method="post" action="/admin/services/edit/s/" enctype="multipart/form-data">
	<section id="content" class="content">
		<header>
			<h2>Manage Services &raquo; Edit Service</h2>
		</header>

		<section class="inner-content">
			<label>*Title:</label><br />
			<input type="text" name="title" maxlength="100" value="{$aService.title}"><br />

			<label>Short Content:</label><br />
			<textarea name="short_content" style="height:115px;">{$aService.short_content}</textarea><br />

			<label>*Content:</label><br />
			{html_editor content=$aService.content name="content"}<br />

			<input type="submit" name="submit" value="Save Changes">
			<a class="cancel" href="/admin/services/" title="Cancel">Cancel</a>
			<input type="hidden" name="id" value="{$aService.id}">
		</section>
	</section> <!-- #content -->

	<section id="sidebar" class="sidebar">
		<header>
			<h2>Service Options</h2>
		</header>

		<section>
			{if $aService.photo_x2 > 0}
			<figure class="itemImage" style="max-width: 300px;">
				<img src="/image/services/{$aService.id}/?width=165&rand={$randnum}" alt="{$aService.title} Image"><br />
				<input name="submit" type="image" src="/images/admin/icons/pencil.png" value="edit">
				<input name="submit" type="image" src="/images/admin/icons/bin_closed.png" value="delete">
			</figure>
			{/if}

			<fieldset>
				<legend>Status</legend>
				<input type="checkbox" name="active" value="1"{if $aService.active == 1} checked="checked"{/if}>
			</fieldset>

			{if $sUseImage && $aService.photo_x2 == 0}
				<fieldset>
					<legend>Service Image</legend>

					<label>Upload Image:</label><br />
					<input type="file" name="image"><br />
					<ul style="font-size:0.8em;">
						<li>File must be a .jpg</li>
						<li>Minimum width is {$minWidth}px</li>
						<li>Minimum height is {$minHeight}px</li>
					</ul>
				</fieldset>
			{/if}
		</section>
	</section>
</form>
<script type="text/javascript">
$(function(){ldelim}
	$('input[name=active]').iphoneStyle({ldelim}
		checkedLabel: 'On',
		uncheckedLabel: 'Off'
	{rdelim});

	$("form").validateForm([
		"required,title,Service title is required"
	]);
{rdelim});
</script>
{include file="inc_footer.tpl"}