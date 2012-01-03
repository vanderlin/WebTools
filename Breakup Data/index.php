<?php

function getLink($u) {
	$base 	  = "http://seek.sing365.com/cgi-bin/s.cgi?q=";
	$u_name   = str_ireplace(" ", "+", $u[0]);
	$u_artist = str_ireplace(" ", "+", $u[1]);
	
	return $base.$u_artist."+".$u_name."&submit=go";
	
}
	

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
</head>
<body>
<?php
include_once("../libs/utils.php");

global $songs;
$songs = array();
$song_page = "http://www.emandlo.com/2009/07/the-top-100-breakup-songs-of-all-time-first-draft/";


// ------------------------------------------------------------
function getSongs($pageURL) {

	$c 	  		 = new Curl;
	$response 	 = $c->get($pageURL);
	$html 	  	 = str_get_html($response->body);
	global $songs;
	
	//echo $html;
	//print_r($response);

	//align="left"
	if(isset($response->body)) {
		//foreach( as $e) {
			//$preTag = $html->find('p[align="left"]')[0];
		//	echo $e;
		//}
		
		$i = 0;
		$str;
		foreach($html->find('.entry p') as $e) {
			$i ++;
			if($i > 2) {
				
				$str .= $e->plaintext;
			}
			//$name = $e->plaintext;
			//$url  = $e->href;
			//$item = array("name"=>$name, "url"=>$url);
			//array_push($songs, $item);
		}
		
		$lns = split("\n", $str);
		foreach($lns as &$n) {
			$n = preg_replace('/[^(\x20-\x7F)]*/','', $n);
			$n = str_replace("&#8220;", "", $n);		
			$n = str_replace("&#8221;", "", $n);
			
			$n = substr($n, strpos($n, " ", 1));
			if($n[0] == " ") $n = substr($n, 1);
		
			$song_artist = split("  ", $n);
			array_push($songs, $song_artist);

			//echo $pos;
		}
	}
	
	
	return $songs;	
}


		
// ------------------------------------------------------------
function getLyrics($u) {

	$link = getLyricsLink($u);

	if(isset($link)) {
		echo '<div class="node">';
		echo "url: <strong><a href='".$link."'>".$link."</a></strong><br><br>";
		echo '<input style="width:1000px" type="text" id="title" value="'.$u[0].'" /><br>';
		echo '<input style="width:1000px" type="text" id="artist" value="'.$u[1].'" /><br>';
		echo '<input style="width:1000px" type="text" id="url" value="'.getLink($u).'" /><br>';
		echo '<button type="button" id="linkbtn">Get Link!</button><br>';

		echo '<textarea rows="20" cols="100" id="lyrics">';
				
		$c 	  		 = new Curl;
		$response 	 = $c->get($link);
		if(isset($response->body)) {
			
			$html = str_get_html($response->body);
			foreach($html->find('td[class=TD]') as $e) {
				$str =  $e->plaintext;
				$str = substr($str, strpos($str, "Ringtones to Cell")+strlen("Ringtones to Cell"));
				$str = substr($str, 0, strpos($str, "If you find some error "));//+strlen("Ringtones to Cell"));
				$str = preg_replace('/\s\s+/', ' ', $str);
				$str = preg_replace('/[^(\x20-\x7F)]*/','', $str);
				$str = preg_replace("/&#?[a-z0-9]{2,8};/i","",$str);
				echo trim($str); 
			}
		}
	
	}
	echo '</textarea>';
	echo '</div>';
				
	echo "<br><hr><br>";
}
		
// ------------------------------------------------------------
function getLyricsLink($u) {
	
	$base 	  = "http://seek.sing365.com/cgi-bin/s.cgi?q=";
	$u_name   = str_ireplace(" ", "+", $u[0]);
	$u_artist = str_ireplace(" ", "+", $u[1]);
	
	$url  = $base.$u_artist."+".$u_name."&submit=go";
	
	$c 	  		 = new Curl;
	$response 	 = $c->get($url);
	if(isset($response->body)) {
		
		$html = str_get_html($response->body);
		$link = $html->find('td a', 0);
		return $link->href;
	
	}
}



// ------------------------------------------------------------

getSongs($song_page);
//printObj($songs);

$len = 2;//sizeof($songs);

for($i=0; $i<$len; $i++) {
	getLyrics($songs[$i]);
}
?>

<script>$
(function(){

	function getLink(u) {
		var base 	  = "http://seek.sing365.com/cgi-bin/s.cgi?q=";
		var u_name   = u[0].replace(' ', '+');
		var u_artist = u[1].replace(' ', '+');
		return base+u_artist+"+"+u_name+"&submit=go";
	}
	
	$("#xmlbtn").click(function() {
		console.log("Clikc");
		var str = "";
		$('.node').each(function(index) {
			
			var lyrics = $(this).find("#lyrics").html();
			var node = "<song>\n"
			node += "<artist><![CDATA["+$(this).find("#artist").attr("value")+"]]></artist>\n";
			node += "<title><![CDATA["+$(this).find("#title").attr("value")+"]]></title>\n";
			node += "<lyrics><![CDATA["+lyrics+"]]></lyrics>\n";
			
			node += '</song>\n';
			str += node+"\n";
			console.log(node);
		});
		
		$("#xml").html("<all_songs>"+str+"</all_songs>");


	});
	
	console.log("done loading");
	
	$("#linkbtn").click(function() {
		
		var u = Array($(this.parentNode).find("#artist").attr("value"),
					  $(this.parentNode).find("#title").attr("value"));
			console.log(getLink(u));
				
		
	});
	
	
});
</script>

XML<br>
<textarea rows="20" cols="100" id="xml"></textarea>
<button type="button" id="xmlbtn">Get XML!</button>
</body>
</html>