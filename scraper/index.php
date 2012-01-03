<?php 

require_once("../Tools/utils.php");


function scrapPitchFork() {
	$url  = "http://pitchfork.com/reviews/best/tracks/";
	$curl = new Curl;
	$res  = $curl->get($url);  
	
	if(isset($res->body)) {
		
		$html = str_get_html($res->body);
		
		// review-artwork
		$array = array();
		foreach($html->find('.review-artwork') as $e) {
			
			$img = $e->children(0)->children(0)->src;
			$obj = array();
			$obj["img"] = $img;
			
			array_push($array, $obj);
			//$str .= $img.",";
			
			
		}
		
		//printObj($array);
		
	}
}

// ----------------------------------------------------

function scrapCL() {
	$url  = "http://boston.craigslist.org/search/mis/?query=w4w";
	$curl = new Curl;
	$res  = $curl->get($url);  
	
	if(isset($res->body)) {
		
		$html = str_get_html($res->body);
		
		foreach($html->find('blockquote') as $e) {
			echo $e."<br>";
		}
				
	}
}



scrapCL();


?>