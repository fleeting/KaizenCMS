<?php
function smarty_function_getContent($aParams, &$oSmarty)
{
	$oApp = $oSmarty->get_registered_object("appController");
	
	if(!empty($aParams["tag"]))
		$aContent = $oApp->dbResults(
			"SELECT * FROM `content`"
				." WHERE `tag` = ".$oApp->dbQuote($aParams["tag"], "text")
			,"smarty->getContent->tag"
			,"row"
		);
	elseif(!empty($aParams["id"]))
		$aContent = $oApp->dbResults(
			"SELECT * FROM `content`"
				." WHERE `id` = ".$oApp->dbQuote($aParams["id"], "text")
			,"smarty->getContent->id"
			,"row"
		);
	
	if(empty($aParams["var"]))
		return stripslashes($aContent["content"]);
	else
		$oApp->tplAssign($aParams["var"], $aContent);
}