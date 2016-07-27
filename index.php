<?php
include "../Scripts/link.php";
$cap = isset($_GET['display']) && $_GET['display'] == 'cap' ? true : false;
$file = file_get_contents('caption_C.txt');
$str = array_filter(explode(PHP_EOL, $file));
$display = $_GET['captionMode'] ? $_GET['captionMode'] : null;
$station = isset($_GET['station']) && trim($_GET['station']) != '' ? trim($_GET['station']) : 1;
//$hour = date('i') >= 5 ? date('H') - 1 : date('H') - 2;
$sql =	"SELECT TRUNCATE(Mvalue,2) FROM hour_data WHERE MStation = ".$station." AND MDate =  (SELECT max(Mdate) FROM hour_data WHERE MStation = ".$station.")";
$result=mysql_db_query($dblink,$sql,$link) or die ("錯誤<br>".$sql); 
$db_data = mysql_fetch_row($result);


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>花蓮縣環境保護局</title>
	<style>
	body{
		width:1920px;
		background-color:black;
		color:white;
		overflow:hidden;
		white-space: nowrap;
	}
	#caption{
		/*left:1920px;*/
		height:1080px;
		position:absolute;
		text-align:center;
	}
	.sections{
		min-width:1920px;
		height:1080px;
		text-align:center;
		font-size:9cm;
		display: inline-block;
		vertical-align: middle;
	}
	img{
			width: 850px;
			height: 500px;
		}
	<?php
	if($cap){
	?>
		#section_1 {
			font-size: 10cm;
			margin-top: 0px;
			margin-left: 0px;
		}
		
		#section_2{
			font-size: 10cm;
			margin-top: 10px;
			margin-left: 0px;
		}
		#section_3 div{
			font-size: 14cm;
			margin-top: 80px;
			margin-left: 0px;
		}
		#section_4 div{
			font-size: 10cm;
			margin-top: 0px;
			margin-left: 0px;
		}
		#section_5 div{
			font-size: 8cm;
			margin-top: 170px;
			margin-left: 0px;
		}
		#section_6 div{
			font-size: 9.5cm;
			margin-top: 0px;
			margin-left: 0px;
		}
		#section_7 div{
			font-size: 10cm;
			margin-top: 0px;
			margin-left: 0px;
		}
		#section_8 div{
			font-size: 10cm;
			margin-top: 10px;
			margin-left: 0px;
		}
		#section_9 div{
			font-size: 7.5cm;
			margin-top: 70px;
			margin-left: 0px;
		}
		
		#section_10 div{
			font-size: 7.1cm;
			margin-top: 0px;
			margin-left: 0px;
		}
	
		
		#for_sec_2{
			display: table-cell;
			
		}
		#for_sec_2 .container{
			display:table-cell !important;
			vertical-align:middle;
		}
	<?php
	}
	?>
	</style>
	<script src="jquery-1.12.4.min.js"></script>
	<script>
		var sect = 1;
		var width = 0;
		
		function refresh(){
			console.log('refreshed');
			var today = new Date();
			var currentMin = today.getMinutes() * 60 + today.getSeconds();
			var currentSec = today.getSeconds();
			var refresh_Min = 300;
			if(currentMin >= refresh_Min){
				
				var timeLeft = ((3600 - currentMin) + refresh_Min) * 1000; 
				
			}else if(currentMin < refresh_Min){
				
				var timeLeft = (refresh_Min - currentMin) * 1000;
				
				
			}
				setTimeout(function(){location.reload();}, timeLeft);
				//setInterval(function(){console.log(new Date().getMinutes() * 60 + new Date().getSeconds())}, 1000);
		}
		
		
		
		function go(sect, width){
			
			var max_sect = $("#caption > div").length;
			var cur_sect = '#section_'+sect;
			$('#caption').show();
			$('#caption').animate({
			   'left': width
			}, 1000, 'linear', function(){
				sect++;
				width -= $(cur_sect).width();
				if(sect > max_sect){
					setTimeout(function(){
						$('#caption').animate(
								{'left': width}, 
								1000, 
								'linear', 
								function(){
									$('#caption').hide(0,'swing', 
										function(){ 
											$('#caption').css('left','1920px');
											sect = 1;
											width = 0;
											go(sect, width);
										})
								})
					}, 10000);
				}else{
					setTimeout(function(){go(sect, width);}, 10000);
					
				}
			});
			
		}
		function set_time(){
			var today = new Date();
			var currentHr = ('0'+today.getHours()).slice(-2);
			var currentMin = ('0'+today.getMinutes()).slice(-2);
			var currentSec = today.getSeconds();
			$('#time').html(currentHr +':'+ currentMin);
			var next_refresh_time = 60 - currentSec;
			setTimeout(function(){
				set_time();
			}, next_refresh_time * 1000);
			
		}
		$(document).ready(function(){
			set_time();
			refresh();
			go(sect, width);
		})
	</script>
</head>
<body>

<div id="caption">
<?php
$sectionCount = 1;
$db_value = $db_data[0]; // value taken from database

if($db_value >= 100){
	$condition = '不良';
}elseif($db_value < 100 && $db_value > 59){
	$condition = '普通';
}elseif($db_value <= 59){
	$condition = '良好';
}
$value = '<span style="color:yellow;">'.$db_value.'</span>';
$airCondition = '<span style="color:yellow;">'.$condition.'</span>';
if($cap){
	foreach($str AS $section){
		$paragraphs = explode('|', $section);
		$div =  "<div id='section_".$sectionCount."' class='sections'>";
		foreach($paragraphs AS $paragraph){
		
		
		if(preg_match("/%s年/", $paragraph, $matches) === 1){
			$year 		= '<span style="color:yellow;">'.date('Y').'</span>';
			$paragraph 	= sprintf($paragraph, $year);
		}
		if(preg_match("/%s月%s日/", $paragraph, $matches) === 1){
			$mth  		= '<span style="color:yellow;">'.date('m').'</span>';
			$day  		= '<span style="color:yellow;">'.date('d').'</span>';
			$paragraph 	= sprintf($paragraph, $mth, $day);
		}
		if(preg_match("/{TIME}/", $paragraph, $matches) === 1){
			$time  		= '<span id="time" style="color:yellow;">'.date('H:i A').'</span>';
			$paragraph 	= preg_replace('/{TIME}/', $time, $paragraph);
		}
		if(preg_match('/{VALUE}/', $paragraph, $matches) === 1){
			$value 		= '<span style="color:yellow;">'.$value.'µg/m<sup>3</sup></span>';
			$paragraph 	= preg_replace('/{VALUE}/', $value, $paragraph);
			$div =  "<div id='section_".$sectionCount."' class='sections' style='font-size:6cm'>";
		}
		if(preg_match('/{AIRCONDITION}/', $paragraph, $matches) === 1){
			$paragraph = preg_replace('/{AIRCONDITION}/', $airCondition, $paragraph);
		}
		if(preg_match("/container/", $paragraph, $matches) === 1){
			$div .= "<div id='for_sec_2'>".$paragraph."</div>";
			
		}else{
			$div .=	"<div>".$paragraph."</div>";
		}
		
		
		}
		echo $div."</div>";
		$sectionCount++;
	}
}else{
foreach($str AS $section){
	$paragraphs = explode('|', $section);
	foreach($paragraphs AS $paragraph){
	$div =  "<div id='section_".$sectionCount."' class='sections'>";
	
		if(preg_match("/%s年/", $paragraph, $matches) === 1){
			$year 		= '<span style="color:yellow;">'.date('Y').'</span>';
			$paragraph 	= sprintf($paragraph, $year);
		}
		if(preg_match("/%s月%s日/", $paragraph, $matches) === 1){
			$mth  		= '<span style="color:yellow;">'.date('m').'</span>';
			$day  		= '<span style="color:yellow;">'.date('d').'</span>';
			$paragraph 	= sprintf($paragraph, $mth, $day);
		}
		if(preg_match("/{TIME}/", $paragraph, $matches) === 1){
			$time  		= '<span id="time" style="color:yellow;">'.date('H:i A').'</span>';
			$paragraph 	= preg_replace('/{TIME}/', $time, $paragraph);
		}
		if(preg_match('/{VALUE}/', $paragraph, $matches) === 1){
			$value 		= '<span style="color:yellow;">'.$value.'µg/m<sup>3</sup></span>';
			$paragraph 	= preg_replace('/{VALUE}/', $value, $paragraph);
			$div =  "<div id='section_".$sectionCount."' class='sections' style='font-size:6cm'>";
		}
		if(preg_match('/{AIRCONDITION}/', $paragraph, $matches) === 1){
			$paragraph = preg_replace('/{AIRCONDITION}/', $airCondition, $paragraph);
		}
		if(preg_match("/container/", $paragraph, $matches) === 1){
			$div .= "<div id='for_sec_2' style='font-size:5cm;'>".$paragraph."</div>";
			
		}else{
			$div .=	"<div>".$paragraph."</div>";
		}
	
	
	echo $div."</div>";
	$sectionCount++;
	}
	
}
}
?>

</div>
</body>
</html>
