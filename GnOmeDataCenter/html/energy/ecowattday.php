<?php
$nb_days=$_GET['days'];
if($nb_days==''){
	$nb_days=365;
}

/*chart constante*/
$chart_title='Ecowatt Jour';
$chart_subtitle='Consommation par jour';

/*Consommation*/
$watt_location="Consommation Electricité";
$watt_id=4;
$watt_color="#A9BCF5";


/*get yesteday raw file*/
$filename="kwattday.csv";

$txt_file    = file_get_contents("/home/pi/log_ecowatt/$filename");
$rows        = explode("\n", $txt_file);
array_shift($rows);
$index=0;
$arg_date=date("Y-m-d");
foreach($rows as $row => $data)
{
	//extract row data
	$row_data = explode(';', $data);

	$arrlength = count($row_data);
	if("$row_data[1]"==""){
		break;
	}
	
	if($nb_days==$index){
		break;
	}
	

	$array_watt[$index]['jour'] = $row_data[0];
	$array_watt[$index]['kwatt']= $row_data[1];
	

	$index++;

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
                format: '{value}kWatt',
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
<?php       echo "	    name: \"$watt_location\","; 
            echo "	    color: '$watt_color',";
?>
	    type: 'line',
            data: [

	       /****************** First Serie **********************************/
<?php
$watt_min=200.0;
$watt_max=-200.0;
$watt_nbdata=0;


$yesterday=date("Y-m-d",strtotime("-1 days"));
$hour=date("G");
$arrlength = count($array_watt);

for($row = 0; $row < $arrlength; $row++) {

    $date=$array_watt[$row]['jour'];
    $array_date  = explode ( "/" , $date );
    
    $year=$array_date[2];
    $month=$array_date[1]-1;
    $day=$array_date[0];
    $value=$array_watt[$row]['kwatt'];
    echo ("	       [ Date.UTC( 20$year , $month , $day , 0 , 0 , 0 ), $value ],\n") ;    
}
?>
            ],/*data*/
            tooltip: {
                valueSuffix: 'kWatt'
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
