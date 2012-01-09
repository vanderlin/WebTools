<?php

global $debug;
global $format;

require_once("simple_html_dom.php");
require_once('curl.php');

// --------------------------------------------------------- prin obj
function printObj(&$obj) {
	echo "<pre>";
	print_r($obj);
	echo "</pre>";
}

// --------------------------------------------------------- simple get URL
function getURL(&$url) {
	$c = new Curl();
	$res = $c->get($url);
	$c = NULL;
	return $res;
}

// --------------------------------------------------------- simple post URL
function postURL(&$url, &$params) {
	$c = new Curl();
	$res = $c->post($url, $params);
	$c = NULL;
	return $res;
}

// --------------------------------------------------------- check if work in a string
function isWordInString($word, $str) {
	$wordsArray = explode(" ", $str);
	return in_array($word, $wordsArray);
}
// --------------------------------------------------------- nice outut
function output($obj) {
	global $format;
	if($format == 'php') {
		echo "<pre>";
		print_r($obj);
		echo "</pre>";
	}
	if($format == 'raw') {
		foreach($obj as $s) echo $s;
	}
	if($format == 'json') {
		echo json_encode($obj);
	}

}
// --------------------------------------------------------- clean white-sapce
function cleanWhiteSpace($str) {
	$clean = preg_replace('/(\s\s+|\t|\n)/', ' ', $str);
	if($clean[0] == ' ') $clean = substr($clean, 1);
	if($clean[strlen($clean)-1] == ' ') $clean = substr($clean, 0, strlen($clean)-1);
	
	return $clean;
}
// --------------------------------------------------------- check for imsdb  base url
function checkForBaseURL($url, $base) {
	$url	 = str_replace(' ', '%20', $url);
	$new_url = $url;
	if(!stristr($url, "http://")) {
		if($url[0] != "/") $url = "/".$url;
		$new_url = $base.htmlentities($url);
	}
	
	return $new_url;
}

// --------------------------------------------------------- Zip Files
/* creates a compressed zip file */
function createZip($files = array(), $destination = '', $overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,$file);
		}
		//debug
		//echo 'The zip archive contains ',$zip-&gt;numFiles,' files with a status of ',$zip-&gt;status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

?>