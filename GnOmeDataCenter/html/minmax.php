<?php

/*chart constante*/
$chart_title='Relevé Météo';
$chart_subtitle='Minima et maxima';

$temp_id=$_GET['sensor'];
if($temp_id==''){
	$temp_id=111;
}
$sensor=4;
/*Temperture 1*/
$temp1_location="Jardin à l'ombre minima";
$temp1_color="blue";
/*Temperture 2*/
$temp2_location="Jardin à l'ombre maxima";
$temp2_color="red";


/******************Get Data from Files ****************************/
/*get yesteday raw file*/
$filename="$temp_id-$sensor-minmax_days.dat";
$txt_file    = file_get_contents("/home/pi/log_rtl433/minmax/$filename");
$rows        = explode("\n", $txt_file);
array_shift($rows);
foreach($rows as $row => $data)
{
    //extract row data
    $row_data = explode(';', $data);
    $info[$row]['date']       = $row_data[0];
    $info[$row]['min']       = $row_data[1];
    $info[$row]['max']       = $row_data[2];
	
}

/******************HTML ******************************************/
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Metéo</title>

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
<?php           echo "text: '$chart_title'"; ?>
        },
        subtitle: {
<?php           echo "text: '$chart_subtitle'"; ?>
        },
        xAxis: {
            startOnTick: true,
            type: 'datetime',
/*            dateTimeLabelFormats: { // don't display the dummy year
		hour: '%H:%M',                
		month: '%e. %b',
                year: '%b'
            },*/
            title: {
                text: 'Date'
            },
             crosshair: true
        },

        yAxis: [
        { // Primary yAxis
            labels: {
                format: '{value}°C',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Temperature',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
        }],
        tooltip: {
        /*    headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%e. %b}: {point.y:.2f} °C'*/
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
<?php       echo "    name: \"$temp1_location\","; 
            echo "    color: '$temp1_color',";
?>
	    type: 'line',
            data: [

/****************** First Serie **********************************/
<?php
$arrlength = count($info)-1;
for($row = 0; $row < $arrlength; $row++) {

	$array_date  = explode ( "-" , $info[$row]['date'] );
	$data=$info[$row]['min'];
	if ($data!=''){
		echo ("[ Date.UTC( $array_date[0] , $array_date[1]-1 , $array_date[2] , 0 , 0 , 0 ), $data ],\n") ;    
	}
}
?>
            ],
            tooltip: {
                valueSuffix: '°C'
            }
        },{
<?php       echo "    name: \"$temp2_location\","; 
            echo "    color: '$temp2_color',";
?>
            type: 'line',
	   
            data: [
/****************** Second Serie **********************************/
<?php
$arrlength = count($info)-1;
for($row = 0; $row < $arrlength; $row++) {

	$array_date  = explode ( "-" , $info[$row]['date'] );
	$data=$info[$row]['max'];
	if ($data!=''){
		echo ("[ Date.UTC( $array_date[0] , $array_date[1]-1 , $array_date[2] , 0 , 0 , 0 ), $data ],\n") ;    
	}
}
?>
            ],
            tooltip: {
                valueSuffix: '°C'
            }
        }]
    });
});
		</script>
	</head>
	<body bgcolor=black text=white link=blue vlink=yellow>
<script src="/highcharts/js/highcharts.js"></script>
<script src="/highcharts/js/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<center><a href=data.php>retour</a>
</body>
</html>
