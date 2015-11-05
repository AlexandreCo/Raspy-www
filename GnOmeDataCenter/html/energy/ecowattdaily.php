<?php

$last_day=$_GET['day'];
if($last_day==''){
	$last_day=1;
}

if($last_day<1){
	$last_day=1;
}

$nb_day=$_GET['nb'];
if($nb_day==''){
	$nb_day=30;
}

if($last_day+$nb_day>255){
	$last_day=255-$nb_day;
}



$chart_subtitle='Consommation jour par jour';

/*Consommation*/
$watt_location="Consommation Electricité";
$watt_id=4;
$watt_color1="#A9BCF5";
$watt_color2="#AAAAAA";
$watt_max=0;
$total_watt=0;
/*get yesteday raw file*/
$filename="kwatthour.csv";

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
	
	if("$row_data[0]"==""){

		$date_read[0]="date";
		for( $days=1;$days<$arrlength;$days++){
			$date_read[$days]= $row_data[$days];
		}

	}else{
		if(24==$index){
			break;
		}


		$array_watt[$index]['heure'] = $row_data[0];
		for( $days=1;$days<$arrlength;$days++){
			$array_watt[$index][$days]= $row_data[$days];
		}
		$index++;
	}
	


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
		type: 'spline'
		/*zoomType: 'x'*/
	},
        title: {
<?php 
		
          	
		
		$current_day=$array_days[0];
		$array_date=explode('/',$date_read[$current_day]);


		$array_date=explode('/',$date_read[$last_day+$nb_day]);
		$start=date("l jS \of F Y", mktime(0, 0, 0, $array_date[1], $array_date[0], 2000+$array_date[2]));
		$array_date=explode('/',$date_read[$last_day]);
		$end=date("l jS \of F Y", mktime(0, 0, 0, $array_date[1], $array_date[0], 2000+$array_date[2]));

		$chart_title="$start - $end";

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
	

	echo "\t\t\t{     name: \"$chart_title\", \n";
	echo "\t\t\t\tdata: [\n";
	for($day=$last_day;$day<$last_day+$nb_day;$day++){
		$current_day=$day;

		$arrlength = count($array_watt);
		$value=0;
		for($row = 0; $row < $arrlength; $row++) {
			$hour=$array_watt[$row]['heure'];
			$array_hour  = explode ( ":" , $hour );
			$hour=$array_hour[0];
			$value+=$array_watt[$row][$current_day];
			if($value>$watt_max){
				$watt_max=$value;
				$watt_max_hour=$array_hour[0];
				$watt_max_date=$date_read[$current_day];
			}
			$total_watt+=$value;
		      
		}
		$array_date=explode('/',$date_read[$current_day]);
		$date_day=$array_date[0];
		$date_month=$array_date[1];
		$date_year=$array_date[2];

 		echo ("\t\t\t\t\t[ Date.UTC( 20$date_year , $date_month-1 , $date_day , 0 , 0 , 0 ), $value ],\n") ; 

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
	$prev_day=$last_day+$nb_day;
	$next_day=$last_day-$nb_day;
	echo "<center><a href='ecowattdaily.php?day=$prev_day&nb=$nb_day'>prev</a>";
	if($next_day>0){
		echo "<a href='ecowattdaily.php?day=$next_day&nb=$nb_day'>next</a>";
	}else{
		echo "<a href='ecowattdaily.php?day=1&nb=$nb_day'>next</a>";		
	}
	
	echo "<br>Electricity : $total_watt kWh"; 
	$cost= $total_watt*0.164;
	echo "<br>Cost : $cost €"; 
	echo "<br>Max power : $watt_max kW on $watt_max_date at $watt_max_hour:00"; 
?>
	</body>
</html>
