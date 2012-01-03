<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
</head>
<body>

<?php
include_once("BreakupData.php");	
$bd    = new BreakupData();
$songs = $bd->getSongs();
$i = 1;
foreach($songs as $s) : ?>
	<?php printObj($s);?>
	<div class="node">
	<h2><?php echo $i ?></h2>
	Artist: <input style="width:600px" type="text" id="artist" value="<?php echo $s["title"] ?>" /><br>
	Title:  <input style="width:600px" type="text" id="title" value="<?php echo $s["artist"] ?>" /><br>
	URL: 	<input style="width:600px" type="text" id="url" value="<?php echo $bd->getLink($s) ?>" /><br>
	<button type="button" class="linkbtn">Get Link</button><br>
	<button type="button" class="lyrics-visit">Goto Lyrics Page</button><br>
	<button type="button" class="re-lyrics">Reload Lyrics</button><br>

	<textarea rows="20" cols="100" id="lyrics">
	<?php //echo $bd->getLyrics($s["artist"], $s["title"]) ?>
	</textarea>
	<hr>
	</div>
	<?php $i++ ?>
<?php endforeach; ?> 

<script>
$(function(){

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
	
	// -----------------------------------------
	$(".linkbtn").click(function() {
		var u = Array($(this.parentNode).find("#artist").attr("value"),
					  $(this.parentNode).find("#title").attr("value"));
		$(this.parentNode).find("#url").attr("value", getLink(u));
		  
			console.log(getLink(u));
	});
	
	// -----------------------------------------
	$(".lyrics-visit").click(function() {
		var u = Array($(this.parentNode).find("#artist").attr("value"),
					  $(this.parentNode).find("#title").attr("value"));
		window.open(getLink(u), "_blank");
	});
	
	// -----------------------------------------
	$(".re-lyrics").click(function() {
		console.log("Load");
		var url = $(this.parentNode).find("#url").attr("value");
		var me 	= this.parentNode;
		$.get("BreakupData.php?m=getLyricsURL&url="+url, function(data) {		    
		    $(me).find("#lyrics").html(data);
		});
	});		
});
</script>

XML<br>
<textarea rows="20" cols="100" id="xml"></textarea>
<button type="button" id="xmlbtn">Get XML!</button>
</body>
</html>