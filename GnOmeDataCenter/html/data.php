<?php

function get_val ( $date )
{
	return $_GET[$date];
}
$arg_date = get_val('date');
if($arg_date=='')
{
        $arg_date=date("Y-m-d");
}
$filename = $arg_date;
$filename .= "_data.txt";
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
             zoomType: 'xy'
        },
        title: {
            text: 'Temperature'
        },
        subtitle: {
            text: 'jardin'
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: 'Heure (UTC)'
            },
             crosshair: true
        },

        yAxis: [{ // Primary yAxis
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
            }
        }, { // Secondary yAxis
            title: {
                text: 'Pluie',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value} mm',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
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
            name: 'Terrasse',
	    type: 'line',
            data: [
<?php
$txt_file    = file_get_contents("/home/pi/log_rtl433/$filename");
$rows        = explode("\n", $txt_file);
array_shift($rows);
foreach($rows as $row => $data)
{
    //get row data
    $row_data = explode(';', $data);
    $info[$row]['id']         = $row_data[0];
    $info[$row]['date']       = $row_data[1];
    $info[$row]['hour']       = $row_data[2];
    $info[$row]['temp']       = $row_data[3];
    $info[$row]['pression']   = $row_data[4];
    $info[$row]['humidity']   = $row_data[5];
    $info[$row]['temp2']      = $row_data[6];
    $info[$row]['battery']    = $row_data[7];
    $info[$row]['rain']       = $row_data[8];
}

$terrasse_min=200.0;
$terrasse_max=-200.0;
$nbData198=0;
$rain_start=-1;
$rain_last=-1;
foreach($rows as $row => $data)
{
    if($info[$row]['id']==198){
        $array_hour  = explode ( ":" , $info[$row]['hour'] );
	$array_date  = explode ( "-" , $info[$row]['date'] );
	$data=$info[$row]['temp'];
	$rain=$info[$row]['rain'];

	if($rain_start == -1) {
		$rain_start=$rain;	
	}
	$rain_last=$rain;
        echo ("[ Date.UTC( $array_date[0] , $array_date[1]-1 , $array_date[2] , $array_hour[0] , $array_hour[1] , $array_hour[2] ), $data ],\n") ;    
    	$terrasse=$row;
	if($terrasse_min > $data)
		$terrasse_min=$data;
	if($terrasse_max < $data)
		$terrasse_max=$data;
	$nbData198++;
    }
}

?>
            ],
            tooltip: {
                valueSuffix: '°C'
            }
        }, {
            name: 'Pluie',
	    type: 'column',
            color: 'blue',
            
            data: [
<?php

$rainS=$rain_start;
$rain=$rain_start;
$rainT=0;
$current_hour=0;
foreach($rows as $row => $data)
{
    if($info[$row]['id']==198){
        $array_hour  = explode ( ":" , $info[$row]['hour'] );
	$array_date  = explode ( "-" , $info[$row]['date'] );
	
	//echo ("$current_hour  $array_hour[0] \n");
	if($current_hour != $array_hour[0]){
	
		$rainT=($rain-$rainS);
		$rainS=$rain;
		echo ("[ Date.UTC( $array_date[0] , $array_date[1] -1, $array_date[2] , $current_hour+1 , 0 , 0 ), $rainT ],\n") ;    
		$current_hour=$array_hour[0];
	}

	$rain=$info[$row]['rain'];
    }
}

?>
            ],
            tooltip: {
                valueSuffix: ' mm'
            }

        },{
            name: 'Jardin à l\'ombre',
            type: 'line',
	    color: '#00FF00',
            data: [
<?php
$jardin_min=200.0;
$jardin_max=-200.0;
$nbData111=0;
foreach($rows as $row => $data)
{
    if($info[$row]['id']==111){
        $array_hour  = explode ( ":" , $info[$row]['hour'] );
	$array_date  = explode ( "-" , $info[$row]['date'] );
	$data=$info[$row]['temp'];
        echo ("[ Date.UTC( $array_date[0] , $array_date[1]-1 , $array_date[2] , $array_hour[0] , $array_hour[1] , $array_hour[2] ), $data ],\n") ;    
    	$jardin=$row;
	if($jardin_min > $data)
		$jardin_min=$data;
	if($jardin_max < $data)
		$jardin_max=$data;
	 $nbData111++;
    }
}?>
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

<?php


echo ("<br><br><h2>Données des capteurs du $arg_date:</h2>");
$date=$info[$jardin]["date"];
$hour=$info[$jardin]["hour"];
$data=$info[$jardin]["temp"];

echo ("<FONT color=#00FF00> Dernière mise à jour : $hour<br> Température Jardin : $data minimal : $jardin_min maximal : $jardin_max</font>");
echo ("<br><br>");
echo ("<FONT color=#A9BCF5>Dernière mise à jour : $hour<br> Température Terrasse : $data minimal : $terrasse_min maximal : $terrasse_max</font>");
$rain=($rain_last-$rain_start)*0.4;
echo ("<br><br>");
echo ("<FONT color=#A9BCF5>Dernière mise à jour : $hour<br> Pluie : $rain</font>");




echo "<br><br><H2>Statistiques capteurs :</h2>";
$nbtotal=$nbData111+$nbData198;
echo "Nombre d'echantillons : $nbtotal<br>";
echo "<table border=1><tr><td bgcolor=black>Capteur</td><td>Nombre echantillons</td><td>Pourcentage echantillons</td></tr>";
$pct=round(($nbData111/$nbtotal)*100,2);
echo "<tr style='color: #00FF00'> <td> 111 </td><td> $nbData111 </td><td> $pct %</td></tr>";
$pct=round(($nbData198/$nbtotal)*100,2);
echo "<tr style='color: #A9BCF5'> <td> 198 </td><td> $nbData198 </td><td> $pct %</td></tr>";

echo "</table>";
?>
	</body>
</html>
