<?php
$nb_hours=$_GET['hours'];
if($nb_hours==''){
	$nb_hours=48;
}

$num_day=$_GET['day'];
if($num_day==''){
	$num_day=1;
}


$chart_subtitle='Consommation données brutes';

/*Consommation*/
$watt_location="Consommation Electricité";
$watt_id=4;
$watt_color1="#A9BCF5";
$watt_color2="#AAAAAA";
$watt_max=0;
$total_watt=0;
/*get yesteday raw file*/
$arg_date=date("ymd");
$filename=$arg_date."_ecowatt";

$txt_file    = file_get_contents("/home/pi/log_ecowatt_brut/$filename");
$rows        = explode("\n", $txt_file);
array_shift($rows);
$index=0;
$arg_date=date("Y-m-d");
foreach($rows as $row => $data)
{

	//extract row data
	$row_data = explode(',', $data);


	$arrlength = count($row_data);

	$array_watt[$index]['jour'] = $row_data[0];
	$array_watt[$index]['heure'] = $row_data[1];
	$array_watt[$index]['watt']= $row_data[2];
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
		/*type: 'spline'*/
		zoomType: 'x'
	},
        title: {
<?php 
		
          	$array_days=explode (",",$num_day);
		$nbdays = count($array_days);
		
		$current_day=$array_watt[$index]['jour'] ;
		$array_date=explode('/',$current_day);
		
		//echo "date(\"l\", mktime(0, 0, 0, $array_date[0], $array_date[1], 2000+$array_date[2]));";

		
		$chart_title=$arg_date;/*("l jS \of F Y", mktime(0, 0, 0, $array_date[1], $array_date[0], 2000+$array_date[2]));*/
		
		echo "text: '$chart_title'"; ?>
        },
        subtitle: {
<?php           echo "text: '$chart_subtitle'"; ?>
        },
        xAxis: {
            //startOnTick: true,
            type: 'datetime',

            title: {
                text: 'Date'
            },
            labels: {
                overflow: 'justify'
            }/*,
            crosshair: true*/
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
	    min: 0,
        }],
        tooltip: {
	    shared: true
        },

        plotOptions: {
            spline: {
		lineWidth: 4,
                states: {
                    hover: {
                        lineWidth: 5
                    }
                },
                marker: {
                    enabled: false
                },
                pointInterval: 3600000, // one hour

            }
        },

	series: [
<?php
	$array_days=explode (",",$num_day);
	$nbdays = count($array_days);


	echo "\t\t\t{     name: \"$arg_date\", \n";
	echo "\t\t\t\tdata: [\n";

	$arrlength = count($array_watt);

	for($row = 0; $row < $arrlength-1; $row++) {
		$hours=$array_watt[$row]['heure'];
		$array_hour  = explode ( ":" , $hours );
		$hour=$array_hour[0];
		$min=$array_hour[1];
		$sec=$array_hour[2];
		
		$date=$array_watt[$row]['jour'];
		$array_date  = explode ( "/" , $date );
		$month=$array_date[0];
		$day=$array_date[1];
		$year=$array_date[2];

		$value=$array_watt[$row]['watt'];

		if($value>$watt_max){
			$watt_max=$value;
			$watt_max_hour=$hours;
			$watt_max_date=$date;
		}
		$total_watt+=$value;
	    echo ("\t\t\t\t\t[ Date.UTC( $year , $month-1 , $day , $hour , $min , $sec ), $value ],\n") ;    
	}
	echo "\t\t\t\t], tooltip: {valueSuffix: 'kWatt'}\n";
	echo "\t\t\t},/*serie*/\n";

?>
       
	]/*series*/

    });/*highchart*/
});/*function*/
		</script>
	</head>
	<body bgcolor=black text=white link=blue vlink=yellow>
<script src="/highcharts/js/highcharts.js"></script>
<script src="/highcharts/js/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<?php 
	echo "<center>";
	echo "<br>Electricity current : $value Wh"; 
	echo "<br>Max power : $watt_max W at $watt_max_hour"; 
?>
	</body>
</html>
