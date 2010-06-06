<?php
class appController
{
	private $_db;
	private $_mail;
	private $_smarty;
	private $_firephp;
	private $_enc;
	private $_plugin;
	public $_settings;
	public $_urlVars;
	public $model;
	
	function __construct($sModel = null) {
		global $objDB, $objMail, $oFirePHP, $oEnc, $oSmarty, $site_public_root, $site_root, $aConfig, $sURL, $aUrl, $aURLVars;
		
		$this->_db = $objDB;
		$this->_mail = $objMail;
		$this->_firephp = $oFirePHP;
		$this->_enc = $oEnc;
		$this->_smarty = $oSmarty;
		$this->_settings = (object) array(
			"root" => $site_root
			,"rootPublic" => $site_public_root
			,"adminInfo" => $aConfig["admin_info"]
			,"debug" => $aConfig["options"]["debug"]
			,"surl" => $sURL
			,"url" => $aUrl
			,"encryptSalt" => $aConfig["encryption"]["salt"]
		);
		$this->_urlVars = $aURLVars;
		
		if(!empty($sModel)) {
			$this->_plugin = $sModel;
			$this->model = $this->loadModel($sModel);
		}
	}
	
	### Functions ####################
	function forward($url, $type = "") {
		switch($type) {
			case "403":
				header('HTTP/1.1 403 Forbidden');
				break;
			case "404":
				header("HTTP/1.1 404 Not Found");
				break;
			case "500":
				header("HTTP/1.1 500 Internal Server Error");
				break;
		}

		header("Location: ".$url);
		exit;
	}
	function siteInfo() {
		echo "<pre>";
		print_r($this->_settings);
		print_r($this->_db);
		print_r($this->_smarty);
		echo "</pre>";
		
		phpinfo();
	}
	function loadController($sController) {
		if(!class_exists($sController)) {
			if(substr($sController, -1) == "_")
				$sControllerFile = substr($sController, 0, -1);
			else
				$sControllerFile = $sController;
			
			if(is_file($this->_settings->root."controllers/".$sControllerFile.".php"))
				require($this->_settings->root."controllers/".$sControllerFile.".php");
			elseif(is_file($this->_settings->root."plugins/".preg_replace('/admin_(.*)$/i', "$1", $sControllerFile)."/controllers/".$sControllerFile.".php"))
				require($this->_settings->root."plugins/".preg_replace('/admin_(.*)$/i', "$1", $sControllerFile)."/controllers/".$sControllerFile.".php");
			else
				return false;
		}
		
		$oController = new $sController;
		
		return $oController;
	}
	function loadModel($sModel) {
		if(!class_exists("appModel"))
			require($this->_settings->root."appModel.php");
		
		if(!class_exists($sModel."_model")) {
			if(is_file($this->_settings->root."plugins/".$sModel."/model.php"))
				require($this->_settings->root."plugins/".$sModel."/model.php");
			else
				return false;
		}
		$sModel = $sModel."_model";
		
		$sModel = new $sModel;
		
		return $sModel;
	}
	function getSetting($sTag) {
		if(empty($sTag))
			$this->sendError("getSetting", "Setting tag not passed", null, debug_backtrace());
		
		$aSetting = $this->dbResults(
			"SELECT * FROM `settings`"
				." WHERE `tag` = ".$this->dbQuote($sTag, "text")
			,"row"
		);
		
		if(empty($aSetting))
			$this->sendError("getSetting", "Could not find setting", null, debug_backtrace());
		
		if(!class_exists("Form"))
			include($this->_settings->root."helpers/Form.php");
		
		$oField = new Form($aSetting);
		
		return $oField->setting->value();
	}
	function getUser($sId) {
		if(empty($sId))
			$this->sendError("getUser", "User id missing", null, debug_backtrace());
			
		$aUser = $this->dbResults(
			"SELECT * FROM `users`"
				." WHERE `id` = ".$this->dbQuote($sId, "integer")
			,"row"
		);
		
		if(empty($aUser))
			$this->sendError("getUser", "Could not find user", null, debug_backtrace());
			
		return $aUser;
	}
	function deleteDir($sFolder) {
		if(is_dir($sFolder)) {
			$oFolder  = opendir($sFolder);
			while (false !== ($sFile = readdir($oFolder))) {
				if($sFile != "." && $sFile != "..") {
					if(is_dir($sFolder."/".$sFile)) {
						$this->deleteTree($sFolder."/".$sFile);
					} else {
						unlink($sFolder."/".$sFile);
					}
				}
			}
			closedir($oFolder);
			
			rmdir($sFolder);
		}
	}
	##################################
	
	### Database #####################
	function dbResults($sSQL, $return = null) {
		// Prefix
		$sSQL = str_replace("{dbPrefix}", $this->_settings->dbPrefix, $sSQL);
		
		$oResult = $this->_db->query($sSQL);
		
		if(PEAR::isError($oResult))
			$this->sendError("dbResults", "dberror", $oResult, debug_backtrace());
			
		switch($return) {
			case "all":
				$aReturn = $oResult->fetchAll();
				break;
			case "row":
				$aReturn = $oResult->fetchRow();
				break;
			case "one":
				$aReturn = $oResult->fetchOne();
				break;
			case "col":
				$aReturn = $oResult->fetchCol();
				break;
			case "rows":
				$aReturn = $oResult->numRows();
				break;
			case "insert":
				$aReturn = $this->_db->lastInsertID();
				break;
			default:
				$aReturn = true;
		}
		
		$oResult->free();
		
		return $aReturn;
	}
	function dbQuote($sValue, $sType) {
		$sReturn = $this->_db->quote($sValue, $sType);
		
		if(PEAR::isError($sReturn))
			$this->sendError("dbQuote", $sReturn->userinfo, null, debug_backtrace());
		
		return $sReturn;
	}
	##################################
	
	### Template #####################
	function tplExists($template_file) {
		$template_file = $this->_smarty->template_dir."/".$template_file;
		
		return is_file($template_file);
	}
	function tplAssign($sVariable, $sValue) {
		$this->_smarty->assign($sVariable, $sValue);
	}
	function tplDisplay($sTemplate) {
		if(!empty($this->_plugin)) {
			$sTemplate = $this->_settings->root."plugins/".$this->_plugin."/views/".$sTemplate;
			if(is_file($sTemplate)) {
				$this->tplAssign("sPluginView", $sTemplate);
				$this->_smarty->display("plugin.tpl");
			} else
				$this->sendError("appController->tplDisplay", "Can't find template - (".$sTemplate.")");
		} else {	
			if($this->tplExists($sTemplate))
				$this->_smarty->display($sTemplate);
			else
				$this->sendError("appController->tplDisplay", "Can't find template - (".$sTemplate.")");
		}
	}
	function tplVariableGet($sVariable) {
		return $this->_smarty->$sVariable;
	}
	function tplVariableSet($sVariable, $sValue) {
		$this->_smarty->$sVariable = $sValue;
	}
	###################################
	
	### Mail ##########################
	function mail($sRecipients, $aHeaders, $bodyText, $bodyHTML = null, $aAttachment = array()) {
		if(!class_exists("Mail_mime"))
			include("Mail/mime.php");
		$oMime = new Mail_mime("\n");
		
		// Set text for message body
		$oMime->setTXTBody($bodyText);
		
		// Set HTML message for body
		if(!empty($bodyHTML))
			$oMime->setHTMLBody($bodyHTML);
		
		// Add attachments to message
		foreach($aAttachment as $aFile)
			$oMime->addAttachment($aFile[0], $aFile[1]);
			
		$sBody = $oMime->get();
		$aHeaders = $oMime->headers($aHeaders);
		
		// Send message
		$oMail = $this->_mail->send($sRecipients, $aHeaders, $sBody);
		
		if(PEAR::iserror($oMail))
			$this->error("Mail - ".$aHeaders["Subject"], $oMail->message);
		else {
			unset($oMime, $sBody, $sHeaders, $oMail);
			return true;
		}
	}
	###################################
	
	### Encryption ####################
	function encrypt($text) {
		return $this->_enc->encrypt($text);
	}
	function decrypt($text) {
		return $this->_enc->decrypt($text);
	}
	##################################

	### Errors #######################
	function log($log) {
		$this->_fireftp->log($log);
	}
	function error($error = "404") {
		switch($error) {
			case "403":
				header('HTTP/1.1 403 Forbidden');
				$this->tplDisplay("error/403.tpl");
				break;
			case "404":
				header("HTTP/1.1 404 Not Found");
				$this->tplDisplay("error/404.tpl");
				break;
			case "500":
				header("HTTP/1.1 500 Internal Server Error");
				$this->tplDisplay("error/500.tpl");
				break;
		}
		exit;
	}
	protected function sendError($section, $error, $db = null, $aTrace = array()) {
		if(empty($aTrace))
			$aTrace = debug_backtrace();
		
		$recipients = $this->_settings->adminInfo["email"];
		$headers["To"] = $this->_settings->adminInfo["email"];
		$headers["From"] = $this->_settings->adminInfo["email"];
		$headers["Subject"] = "Website Error - ".$section;
		
		$body = "Where: ".$section."\n";
		if(!empty($db)) {
			$aUserInfo = preg_split('/\] \[/',str_replace(array("_doQuery: [", "]\n[", "]\n"),array(null, "] [", null),$db->userinfo));
			$aMessage = preg_split('/: /',$aUserInfo[3]);
			$body .= "Error: ".$db->message."\n";
			$body .= $aMessage[1]."\n";
			$body .= "Query: ".$this->_db->last_query."\n";
		} else
			$body .= "Error: ".$error."\n";
		
		$body .= "File: ".$aTrace[0]["file"]."\n";
		$body .= "Line: ".$aTrace[0]["line"]."\n";
		$body .= "User Agent: ".$_SERVER["HTTP_USER_AGENT"]."\n";
		$body .= "Referer: ".$_SERVER["HTTP_REFERER"]."\n";
		$body .= "Domain: ".$_SERVER["HTTP_HOST"]."\n";
		$body .= "URL: ".$_SERVER["REQUEST_URI"]."\n";
		$body .= "Time: ".date("M j,Y - h:i:s a")."\n";
		
		if($this->_settings->debug == true)
			die(str_replace("\n","<br />",$body));
		else
			$this->mail($recipients, $headers, $body);
		
		$this->error("500");
	}
	##################################
}