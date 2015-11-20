<?php
$arg_date=$_GET['date'];
if($arg_date==''){
	$arg_date=date("ymd");
}

$nb_day=$_GET['nb'];
if($nb_day==''){
	$nb_day=1;
}
//echo "$nb_day<br>\n";


$chart_subtitle='Consommation par jour';

/*Consommation*/
$watt_location="Consommation Electricité";
$watt_id=4;
$watt_color1="#A9BCF5";
$watt_color2="#AAAAAA";
$watt_max=0;
$total_watt=0;
$day=$arg_date;

for( $index=0;$index<$nb_day;$index++){

	/*get yesteday raw file*/
	$filename=$day."_ecowatt";
	$txt_file    = file_get_contents("/home/pi/log_ecowatt_brut/$filename");
	$rows        = explode("\n", $txt_file);
	array_shift($rows);
	
	$watt=0;
	$nbsample=0;
	foreach($rows as $row => $data)
	{
		//extract row data
		$row_data = explode(',', $data);
		$arrlength = count($row_data);
		if(($arrlength>5 and $row_data[5]=="ok") or ($arrlength==3)){

			$sample=$row_data[2];
			if($sample<20000){
				$watt+=$sample;
				$nbsample++;
			}	
		}
	}


	if($nbsample){
		$value=($watt/$nbsample);
	}
	else{
		$value=0;
	}
	$array_watt[$index]['kwatt']=($value*24)/1000;
	$array_watt[$index]['jour']=date('Y-m-d', strtotime("20$day"));
	

	//echo "$day :  $value <br>\n";
	$day=date('ymd', strtotime("20$day - 1 day"));
	
}

/******************HTML ******************************************/
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Energie</title>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<style type="text/css">
${demo.css}
		</style>
		<script type="text/javascript">
$(function () {
    $('#container').highcharts({
	chart: {
		type: 'column'
		/*zoomType: 'x'*/
	},
        title: {
<?php 
		
		$current_day=$array_watt[$index]['jour'] ;
		$array_date=explode('/',$current_day);
		
		//echo "date(\"l\", mktime(0, 0, 0, $array_date[0], $array_date[1], 2000+$array_date[2]));";
		$dayfirst=date('l jS \of F Y', strtotime("20$day"));
		$daylast=date('l jS \of F Y', strtotime("20$arg_date"));
		
		$chart_title="$dayfirst $daylast";
		
		echo "text: '$chart_title'"; ?>
        },
        subtitle: {
<?php           echo "text: '$chart_subtitle'"; ?>
        },
        xAxis: {
                 type: 'datetime',
                dateTimeLabelFormats: { // don't display the dummy year
                    month: '%e. %b',
                    year: '%b'
                },
                title: {
                    text: 'Date'
                }
        },

        yAxis: [
        { // Primary yAxis
            labels: {
                format: '{value}KWatt',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Consommation',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
	    min: 0,
        }],
        tooltip: {
	    shared: true
        },
	plotOptions : {
		series : {
			marker     : {enabled : true},
			stacking   : 'normal',
			pointRange : 24 * 3600 * 1000 // one month
		}
	},

	series: [
<?php
	$array_days=explode (",",$num_day);
	


	echo "\t\t\t{     name: \"Ecowatt\", \n";
	echo "\t\t\t\tdata: [\n";

	$arrlength = count($array_watt);
	
	for($row = 0; $row < $arrlength; $row++) {
		$date=$array_watt[$row]['jour'];
		$array_date  = explode ( "-" , $date );
		$month=$array_date[1];
		$day=$array_date[2];
		$year=$array_date[0];

		$value=$array_watt[$row]['kwatt'];

		if($value>$watt_max){
			$watt_max=$value;
			$watt_max_date=$date;
		}
		$total_watt+=$value;
	    echo ("\t\t\t\t\t[ Date.UTC( $year , $month-1 , $day , 0 , 0 , 0 ), $value ],\n") ;    
	}
	echo "\t\t\t\t], tooltip: {valueSuffix: 'kWatt'}\n";
	echo "\t\t\t},/*serie*/\n";

?>
       
	]/*series*/

    });/*highchart*/
});/*function*/
		</script>
	</head>
	<body>
<script src="/highcharts/js/highcharts.js"></script>
<script src="/highcharts/js/modules/exporting.js"></script>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<?php 
	echo "<center>\n";
	echo "<br>Max power : $watt_max KWatt at $watt_max_date <br>\n"; 
	echo "<br>Total : $total_watt KWatt <br>\n"; 
	echo "</center>\n";

?>
	</body>
</html>
