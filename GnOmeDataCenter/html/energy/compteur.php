<?php
$type=$_GET['type'];
if($type==''){
	$type="gaz";
}

$chart_subtitle='Consommation moyenne par jour';

if ($type=="gaz") {
	$chart_title='Compteur gaz';
	$filename="gaz.csv";
	$grandeur="m3";
	$serie_name="gaz";
	$serie_type="spline";
#	$serie_type="line";
#	$serie_type="column";
	$yaxis_name="Consommation moyenne par jour";

}

if ($type=="elec") {
	$chart_title='Compteur Electricité';
	$filename="electricite.csv";
	$grandeur="kwatt";
	$serie_name="electricité";
	$serie_type="spline";
	$yaxis_name="Consommation moyenne par jour";
}

if ($type=="eau") {
	$chart_title='Compteur Electricité';
	$filename="eau.csv";
	$grandeur="m3 ";
	$serie_name="eau";
	$serie_type="spline";
	$yaxis_name="Consommation moyenne par jour (1m3=1000l)";
}

$serie_color="#A9BCF5";

$xaxis_name="date";



$txt_file    = file_get_contents("/home/pi/log_compteurs/$filename");
$rows        = explode("\n", $txt_file);
array_shift($rows);
$line=0;
$arg_date=date("Y-m-d");
$lastvalue=-1;
$lastdate=-1;
foreach($rows as $row => $data)
{
	
	//extract row data
	$row_data = explode(';', $data);

	$arrlength = count($row_data);
	if("$row_data[1]"==""){
		break;
	}


	
	$array_date  = explode ( "/" , $row_data[0] );
	$year=$array_date[2];
	$month=$array_date[1];
	$day=$array_date[0];
	$date=mktime(0, 0, 0, $month, $day, $year);

	$currentindex=$row_data[1];

	if($lastdate!=-1){


		$periode_sec=$date-$lastdate;
		$newindex=$currentindex-$lastindex;
		$value=$newindex/($periode_sec/86400);
		$array_serie[$line]['jour'] = $row_data[0];
		$array_serie[$line]['value']= $value;
		$line++;
	}
	$lastindex=$currentindex;
	$lastdate=$date;


	


	

}
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
             zoomType: 'x'
        },
        title: {
<?php           echo "\t\t\ttext: '$chart_title'"; ?>
        },
        subtitle: {
<?php           echo "\t\t\ttext: '$chart_subtitle'"; ?>
        },
        xAxis: {
            startOnTick: true,
            type: 'datetime',
            title: {
<?php echo("\t\t\t\ttext: '$xaxis_name',\n");?>
            },
             crosshair: true
        },

        yAxis: [
        { // Primary yAxis
            labels: {
<?php echo("\t\t\tformat: '{value}$grandeur',\n");?>
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
<?php echo("\t\t\ttext: '$yaxis_name',\n");?>
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
        }],
        tooltip: {
	    shared: true
        },

        plotOptions: {
            spline: {
                marker: {
                    enabled: true
                }
            }
        },

        series: [{
<?php       echo "\t\t\tname: \"$serie_name\","; 
            echo "\t\t\tcolor: '$serie_color',";

            echo "\t\t\ttype: '$serie_type',\n";
?>
            data: [
<?php

$arrlength = count($array_serie);

for($row = 0; $row < $arrlength; $row++) {

    $date=$array_serie[$row]['jour'];
    $value=$array_serie[$row]['value'];

    $array_date  = explode ( "/" , $date );
    $year=$array_date[2];
    $month=$array_date[1]-1;
    $day=$array_date[0];

    echo ("\t\t\t[ Date.UTC( $year , $month , $day , 0 , 0 , 0 ), $value ],\n") ;    
}
?>
            ],/*data*/
            tooltip: {
<?php echo("\t\t\t\tvalueSuffix: ' $grandeur'\n");?>
            }
        }]/*serie*/
    });/*highchart*/
});/*function*/
		</script>
	</head>
	<body bgcolor=black text=white link=blue vlink=yellow>
<script src="/highcharts/js/highcharts.js"></script>
<script src="/highcharts/js/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>


	</body>
</html>
