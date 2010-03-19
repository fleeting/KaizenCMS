<?php
class Form_textarea extends Form_Field
{
	private $_options;
	private $_setting;
	
	/* __constructor */
	public function Form_textarea($aSetting) {
		$this->_setting = $aSetting;
		$this->_options = $this->getOptions($aSetting["type"]);
	}
	
	public function html() {
		$sHTML = $this->getLabel($this->_setting["title"])."\n";
		$sHTML .= "<textarea name=\"settings[".$this->_setting["tag"]."]\"";
		$sHTML .= ">".$this->value()."</textarea><br>\n";
		
		return $sHTML;
	}
	public function value() {
		return stripslashes($this->_setting["value"]);
	}
	public function save($value) {
		return addslashes(trim($value));
	}
}