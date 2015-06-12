<?php

/*chart constante*/
$chart_title='Relevé Météo';
$chart_subtitle='Temperature et Pluie des dernières 24h';

/*rain*/
$rain_quantum=0.45;
$rain_color="blue";
/*Temperture 1*/
$temp1_location="Terrasse";
$temp1_id=198;
$temp1_color="#A9BCF5";
/*Temperture 2*/
$temp2_location="Jardin à l'ombre";
$temp2_id=111;
$temp2_color="#00FF00";

/******************Get Args **************************************/
/*
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
*/
$arg_date=date("Y-m-d");

/******************Get Data from Files ****************************/
/*get yesteday raw file*/
$filename=date("Y-m-d",strtotime("-1 days"));
$filename .= "_data.txt";
$txt_file    = file_get_contents("/home/pi/log_rtl433/$filename");
$rows        = explode("\n", $txt_file);
array_shift($rows);
foreach($rows as $row => $data)
{
    //extract row data
    $row_data = explode(';', $data);
    $yesterday[$row]['id']         = $row_data[0];
    $yesterday[$row]['date']       = $row_data[1];
    $yesterday[$row]['hour']       = $row_data[2];
    $yesterday[$row]['temp']       = $row_data[3];
    $yesterday[$row]['pression']   = $row_data[4];
    $yesterday[$row]['humidity']   = $row_data[5];
    $yesterday[$row]['temp2']      = $row_data[6];
    $yesterday[$row]['battery']    = $row_data[7];
    $yesterday[$row]['rain']       = $row_data[8];
}
/*get today raw file*/
$filename=date("Y-m-d");
$filename .= "_data.txt";
$txt_file    = file_get_contents("/home/pi/log_rtl433/$filename");
$rows        = explode("\n", $txt_file);
array_shift($rows);
foreach($rows as $row => $data)
{
    //extract row data
    $row_data = explode(';', $data);
    $today[$row]['id']         = $row_data[0];
    $today[$row]['date']       = $row_data[1];
    $today[$row]['hour']       = $row_data[2];
    $today[$row]['temp']       = $row_data[3];
    $today[$row]['pression']   = $row_data[4];
    $today[$row]['humidity']   = $row_data[5];
    $today[$row]['temp2']      = $row_data[6];
    $today[$row]['battery']    = $row_data[7];
    $today[$row]['rain']       = $row_data[8];
}

/*merge yesterday and today*/
$info = array_merge( $yesterday , $today);

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
                text: 'Heure (UTC)'
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
        }, 
        { // Secondary yAxis
                min: 0,
                max: 10,
            opposite: true,
           labels: {
                format: '{value}mm',
                style: {
<?php           echo "color: '$rain_color'"; ?>   

                }
            },
            title: {
                text: 'Pluie',
                style: {
<?php           echo "color: '$rain_color'"; ?>    
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
$temp1_min=200.0;
$temp1_max=-200.0;
$temp1_nbdata=0;


$yesterday=date("Y-m-d",strtotime("-1 days"));
$hour=date("G");
$arrlength = count($info);

for($row = 0; $row < $arrlength; $row++) {

    $array_hour  = explode ( ":" , $info[$row]['hour'] );
    if(($info[$row]['date'] == $yesterday )&& ($array_hour[0] < $hour )) {
        	continue;
    }

    if($info[$row]['id']==$temp1_id){
	$array_date  = explode ( "-" , $info[$row]['date'] );
	$data=$info[$row]['temp'];
        echo ("[ Date.UTC( $array_date[0] , $array_date[1]-1 , $array_date[2] , $array_hour[0] , $array_hour[1] , $array_hour[2] ), $data ],\n") ;    
    	$temp1_last_row=$row;
	if($temp1_min > $data)
		$temp1_min=$data;
	if($temp1_max < $data)
		$temp1_max=$data;
	$temp1_nbdata++;
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
            pointWidth: 15,
<?php           echo "color: '$rain_color',"; ?>            
            yAxis: 1,
            data: [
<?php
/****************** Rain Serie **********************************/
$rain_start=-1;
$rainT=0;
$current_hour=0;
$arrlength = count($info);
for($row = 0; $row < $arrlength-1; $row++) {
    $array_hour  = explode ( ":" , $info[$row]['hour'] );
    if(($info[$row]['date'] == $yesterday )&& ($array_hour[0] <= $hour )) {
        	$current_array_hour=$array_hour;
		$current_array_date=$array_date;
		continue;
    }

    if($info[$row]['id']==198){
       	$array_date  = explode ( "-" , $info[$row]['date'] );
	if($rain_start == -1) {
		$rainS=$rain_start=$rain=$info[$row]['rain'];
	}	
	//echo ("$current_hour  $array_hour[0] \n");
	if($current_array_hour[0] != $array_hour[0]){
	
		$rainT=($rain-$rainS);
		if($rainT<0){
			$rainT+=256;
		}
		$rain_total+=$rainT;
		$rainS=$rain;
		echo ("[ Date.UTC( $current_array_date[0] , $current_array_date[1] -1, $current_array_date[2] , $current_array_hour[0] , 30 , 0 ), $rainT*$rain_quantum ],\n") ;    
		$current_array_hour=$array_hour;
		$current_array_date=$array_date;
	}

	$rain=$info[$row]['rain'];
    }
}
/* current rain*/
$rainT=($rain-$rainS);
/*$rain_total=($rain-$rain_start)*$rain_quantum;*/
$h=$array_hour[0]+1;
$d=$array_date[2];
if($h == 23){
	$h=0;
	$d++;
}
echo ("[ Date.UTC( $array_date[0] , $array_date[1] -1, $array_date[2] ,  $array_hour[0] , 30 , 0 ), $rainT*$rain_quantum ],\n") ;  
?>
            ],
            tooltip: {
                valueSuffix: ' mm'
            }

        },{
<?php       echo "    name: \"$temp2_location\","; 
            echo "    color: '$temp2_color',";
?>
            type: 'line',
	   
            data: [
<?php
/****************** Second Serie **********************************/
$temp2_min=200.0;
$temp2_max=-200.0;
$temp2_nbdata=0;
$arrlength = count($info);
for($row = 0; $row < $arrlength; $row++) {
    $array_hour  = explode ( ":" , $info[$row]['hour'] );
    if(($info[$row]['date'] == $yesterday )&& ($array_hour[0] < $hour )) {
        	continue;
    }
    if($info[$row]['id']==$temp2_id){
       	$array_date  = explode ( "-" , $info[$row]['date'] );
	$data=$info[$row]['temp'];
        echo ("[ Date.UTC( $array_date[0] , $array_date[1]-1 , $array_date[2] , $array_hour[0] , $array_hour[1] , $array_hour[2] ), $data ],\n") ;    
    	$temp2_last_row=$row;
	if($temp2_min > $data)
		$temp2_min=$data;
	if($temp2_max < $data)
		$temp2_max=$data;
	 $temp2_nbdata++;
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
$date=$info[$temp2_last_row]["date"];
$hour=$info[$temp2_last_row]["hour"];
$data=$info[$temp2_last_row]["temp"];
echo ("<FONT color=$temp2_color> Dernière mise à jour : $hour<br> Température $temp2_location : $data °C, minimal : $temp2_min °C maximal : $temp2_max °C</font>");
echo ("<br>");
echo ("<a href=minmax.php?sensor=$temp2_id color=$temp2_color>Minima et Maxima</a>");
echo ("<br><br>");
$date=$info[$temp1_last_row]["date"];
$hour=$info[$temp1_last_row]["hour"];
$data=$info[$temp1_last_row]["temp"];
echo ("<FONT color=$temp1_color>Dernière mise à jour : $hour<br> Température $temp1_location : $data °C, minimal : $temp1_min °C, maximal : $temp1_max °C</font>");
echo ("<br>");
echo ("<a href=minmax.php?sensor=$temp1_id color=$temp1_color>Minima et Maxima</a>");
echo ("<br><br>");
$rain_total*=$rain_quantum;
echo ("<FONT color=$rain_color>Dernière mise à jour : $hour<br> Pluie : $rain_total mm</font>");

echo "<br><br><H2>Statistiques capteurs :</h2>";
$nbtotal=$temp2_nbdata+$temp1_nbdata;
$arrlength = count($info);
echo "Nombre d'echantillons : $nbtotal <br>";
echo "<table border=1><tr><td bgcolor=black>Capteur</td><td>Nombre echantillons</td><td>Pourcentage echantillons</td></tr>";
$pct=round(($temp2_nbdata/$nbtotal)*100,2);
echo "<tr style='color: $temp2_color'> <td> $temp2_id </td><td> $temp2_nbdata </td><td> $pct %</td></tr>";
$pct=round(($temp1_nbdata/$nbtotal)*100,2);
echo "<tr style='color: $temp1_color'> <td> $temp1_id </td><td> $temp1_nbdata </td><td> $pct %</td></tr>";

echo "</table>";
?>
	</body>
</html>
