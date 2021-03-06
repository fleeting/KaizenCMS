<?php
$aPluginInfo = array(
	/* Plugin Details */
	"name" => "Services",
	"version" => "1.0",
	"author" => "Crane | West",
	"website" => "http://crane-west.com/",
	"email" => "support@crane-west.com",

	/* Plugin Configuration */
	"config" => array(
		"useImage" => true,
		"imageMinWidth" => 140,
		"imageMinHeight" => 87,
		"imageFolder" => "/uploads/services/",
		"shortContentCharacters" => 250, // max characters for short content
		"perPage" => 2,
		"sort" => "manual", // manual, name, created, updated, random - asc, desc
	)
);