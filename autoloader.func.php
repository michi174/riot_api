<?php
function __autoload($klasse)
{
    $include	= $_SERVER["DOCUMENT_ROOT"]."/riot_api/".str_replace("\\", "/", $klasse).".php";
	
	if(file_exists($include))
	{
		include_once $include;
	}
	else 
	{
		die("Fehler: Die Datei " . $include . " konnte nicht eingebunden werden, da die Datei nicht gefunden wurde.");
	}
}

?>