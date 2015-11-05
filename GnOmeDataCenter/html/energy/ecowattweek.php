<?php

$num_week=$_GET['week'];
if($num_week==''){
	$num_week=1;
}
if($num_week>35){
	$num_week=35;
}
if($num_week<1){
	$num_week=1;
}

$chart_subtitle='Consommation heure par heure';

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
		$first_day=($num_week)*7+1;
		$array_date=explode('/',$date_read[$first_day]);
		$start=date("l jS \of F Y", mktime(0, 0, 0, $array_date[1], $array_date[0], 2000+$array_date[2]));
		$last_day=($num_week-1)*7+1;
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

	$current_week=$array_weeks[$week];
	$current_day=($num_week)*7+1;
	echo "\t\t\t{     name: \"$chart_title\", \n";
	echo "\t\t\t\tdata: [\n";
	for($day=0;$day<7;$day++){
		$arrlength = count($array_watt);

		for($row = 23 ; $row >=0 ; $row--) {
			$hour=$array_watt[$row]['heure'];
			$array_hour  = explode ( ":" , $hour );
			$hour_clean=$array_hour[0];
	
			$value=$array_watt[$row][$current_day+$day];
			if($value>$watt_max){
				$watt_max=$value;
				$watt_max_hour=$hour_clean;
				$watt_max_date=$date_read[$current_day+$day];
			}
			$total_watt+=$value;
			$array_date=explode("/",$date_read[$current_day+$day]);
			$date_day=$array_date[0];
			$date_month=$array_date[1];
			$date_year=$array_date[2];

		    echo ("\t\t\t\t\t[ Date.UTC( 20$date_year , $date_month-1 , $date_day , $hour_clean , 0 , 0 ), $value ],\n") ;    
		}
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
	$prev_week=$num_week+1;
	$next_week=$num_week-1;
	echo "<center><a href='ecowattweek.php?week=$prev_week'>prev</a>";
	if($next_week>0){
		echo "<a href='ecowattweek.php?week=$next_week'>next</a>";
	}
	
	echo "<br>Electricity : $total_watt kWh"; 
	$cost= $total_watt*0.164;
	echo "<br>Cost : $cost €"; 
	echo "<br>Max power : $watt_max kW on $watt_max_date at $watt_max_hour:00"; 
?>
	</body>
</html>
