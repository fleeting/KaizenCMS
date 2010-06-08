{include file="inc_header.tpl" page_title="Gallery :: Photos :: Edit Photo" menu="galleries"}

<h2>{$aGallery.name|stripslashes}</h2>
<form method="post" action="/admin/galleries/{$aGallery.id}/photos/edit/s/">
	<label>Title:</label>
	<input type="text" name="title" maxlength="100" value="{$aPhoto.title|clean_html}"><br>
	<label>Description:</label>
	<textarea name="description" class="elastic">{$aPhoto.description|clean_html}</textarea><br>
	<input type="submit" value="Save Changes"> <input type="button" value="Cancel" onclick="location.href = '/admin/galleries/{$aGallery.id}/photos/';">
	<input type="hidden" name="id" value="{$aPhoto.id}">
</form>
{include file="inc_footer.tpl"}