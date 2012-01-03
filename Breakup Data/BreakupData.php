<?php
include_once("../Tools/php/utils.php");

class BreakupData {
	
	public $songs = array();
	
	// ------------------------------------------------------------
	public function BreakupData() {
	}
	
	// ------------------------------------------------------------
	public function getSongs() {
		
		if(empty($this->songs)) {
			$song_page   = "http://www.emandlo.com/2009/07/the-top-100-breakup-songs-of-all-time-first-draft/";					
			$c 	  		 = new Curl;
			$response 	 = $c->get($song_page);
			$html 	  	 = str_get_html($response->body);
			global $songs;
			
			if(isset($response->body)) {
				$i = 0;
				$str;
				foreach($html->find('.entry p') as $e) {
					$i ++;
					if($i > 2) {
						$str .= $e->plaintext;
					}
				}
				$lns = split("\n", $str);
				foreach($lns as &$n) {
					$n = preg_replace('/[^(\x20-\x7F)]*/','', $n);
					$n = str_replace("&#8220;", "", $n);		
					$n = str_replace("&#8221;", "", $n);
					$n = substr($n, strpos($n, " ", 1));
					if($n[0] == " ") $n = substr($n, 1);
					$song_artist = array("artist"=>"", "title"=>"", "lyrics"=>"");
					$sa = split("  ", $n);
					$song_artist["title"] = $sa[1];
					$song_artist["artist"] = $sa[0];
					$song_artist["url"] = $this->getLink($sa);
					if(!empty($song_artist["url"])) {
						//$song_artist["lyrics"] = $this->getLyrics($song_artist["url"]);
					}
					
					
					array_push($this->songs, $song_artist);
						
				}
			}
		}
		return $this->songs;	
	}
	
	// ------------------------------------------------------------
	public function getLyricsFromURL($url) {
		$url = str_ireplace(" ", "+", $url);
		$link = $this->getLyricsLinkFromURL($url);
		$str;
		if(isset($link)) {
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
					$str = trim($str); 
				}
			}
		
		}
		return $str;
	}
	
	// ------------------------------------------------------------
	public function getLyricsLinkFromURL($url) {
		$c 	  		 = new Curl;
		$response 	 = $c->get($url);
		if(isset($response->body)) {
			
			$html = str_get_html($response->body);
			$link = $html->find('td a', 0);
			return $link->href;
		
		}
	}
	
	// ------------------------------------------------------------
	public function getLyrics($artist, $title) {
		$link = $this->getLyricsLink($artist, $title);
		$str;
		if(isset($link)) {
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
					$str = trim($str); 
				}
			}
		
		}
		return $str;
	}
		
		
	// ------------------------------------------------------------
	public function getLyricsLink($artist, $title) {
		
		$base 	  = "http://seek.sing365.com/cgi-bin/s.cgi?q=";
		$u_name   = str_ireplace(" ", "+", $title);
		$u_artist = str_ireplace(" ", "+", $artist);
		
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
	public function getLink($u) {
		$base 	  = "http://seek.sing365.com/cgi-bin/s.cgi?q=";
		$u_name   = str_ireplace(" ", "+", $u["title"]);
		$u_artist = str_ireplace(" ", "+", $u["artist"]);
		return $base.$u_artist."+".$u_name."&submit=go";	
	}
	
}

if($_GET['m'] == 'getLyrics' && $_GET['artist'] != "" && $_GET['title'] != "") {
	$bd    = new BreakupData();
	echo $bd->getLyrics($_GET['artist'], $_GET['title']);
}

if($_GET['m'] == 'getLyricsURL' && $_GET['url']) {
	$bd    = new BreakupData();
	echo $bd->getLyricsFromURL($_GET['url']);
}

?>