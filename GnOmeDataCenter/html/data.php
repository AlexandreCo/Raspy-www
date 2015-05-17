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
		<title>Highcharts Example</title>

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
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: 'Temperature (°C)'
            },
            min: 0
        },
        tooltip: {
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%e. %b}: {point.y:.2f} °C'
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
	    
            data: [
<?php
if ($fp = fopen("/var/www/GnOmeDataCenter/log_brut/$filename","r"))
{
	while (!feof($fp)) 
	{ 
		$line = fgets($fp, 255);
		$array = explode ( ";" , $line );
		switch($array[0])
		{
                        case 198:
				$hour = explode ( ":" , $array[2] );
				$date = explode ( "-" , $array[1] );
                        	echo ("[ Date.UTC($date[0],$date[1]-1,$date[2],$hour[0],$hour[1],$hour[2]), $array[3]   ],\n");
                        break;
			
			default:
			break;
		}
	}
	fclose($fp);
}
else
{
	echo ("[ $array[2], $array[3]   ],");
}
?>
            ]
        }, {
            name: 'Jardin à l\'ombre',
	    color: '#00FF00',
            data: [
<?php
if ($fp = fopen("/var/www/GnOmeDataCenter/log_brut/$filename","r"))
{
	while (!feof($fp)) 
	{ 
		$line = fgets($fp, 255);
		$array = explode ( ";" , $line );
		switch($array[0])
		{
                        case 111:
                             	$hour = explode ( ":" , $array[2] );
				$date = explode ( "-" , $array[1] );
                        	echo ("[ Date.UTC($date[0],$date[1]-1,$date[2],$hour[0],$hour[1],$hour[2]), $array[3]   ],\n");
                        break;
			
			default:
			break;
		}
	}
	fclose($fp);
}
else
{
	echo ("[ $array[2], $array[3]   ],");
}
?>
            ]
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
if ($fp = fopen("/var/www/GnOmeDataCenter/log_brut/$filename","r"))
{
	echo "<br><br><center><H3>Donn&eacute;es capteurs du $arg_date:<br>";
	echo "<center><table border=1><tr><td>Capteur</td><td>Date</td><td>Heure(GMT)</td><td>Temp&eacute;rature 1</td><td>Pression</td><td>Humidit&eacute;</td><td>Temp&eacute;rature 2</td><td>Batterie</td><td>Rain</td></tr>";
	
	while (!feof($fp)) 
	{ 

		$line = fgets($fp, 255);
		$array = explode ( ";" , $line );
		switch($array[0])
		{
			case 0:
				$color="#008000";
				$nbData0++;
			break;
			case 1:
				$color="#4B0082";
				$nbData1++;
			break;
			case 11:
				$color="#B22222";
				$nbData11++;
			break;
			case 21:
				$color="#D2691E";
				$nbData21++;
			break;
                        case 111:
                                $color="#D269FF";
                                $nbData111++;
                        break;
                        case 198:
                                $color="#D20000";
                                $nbData198++;
                        break;


		}
		$nbtotal++;
		echo "<tr bgcolor=$color>";
		foreach ($array as $value) 
		{
    			echo "<td>$value</td>";
		}
 		echo "</tr>";
	}
	fclose($fp);
	echo "</table>";
	echo "<br><br><H3>Statistiques capteurs :<br>";
	echo "<H5>Nombre d'echantillons : $nbtotal<br>";
	
	echo "<table border=1><tr><td>Capteur</td><td>Nombre echantillons</td><td>Pourcentage echantillons</td></tr>";

	$pct=round(($nbData0/$nbtotal)*100,2);
	echo "<tr bgcolor=#008000> <td> 0 </td><td> $nbData0 </td><td> $pct %</td></tr>";
	$pct=round(($nbData1/$nbtotal)*100,2);
	echo "<tr bgcolor=#4B0082> <td> 1 </td><td> $nbData1 </td><td> $pct %</td></tr>";
	$pct=round(($nbData11/$nbtotal)*100,2);
	echo "<tr bgcolor=#B22222> <td> 11 </td><td> $nbData11 </td><td> $pct %</td></tr>";
	$pct=round(($nbData21/$nbtotal)*100,2);
	echo "<tr bgcolor=#D2691E> <td> 21 </td><td> $nbData21 </td><td> $pct %</td></tr>";
        $pct=round(($nbData111/$nbtotal)*100,2);
        echo "<tr bgcolor=#D269FF> <td> 111 </td><td> $nbData111 </td><td> $pct %</td></tr>";
        $pct=round(($nbData198/$nbtotal)*100,2);
        echo "<tr bgcolor=#D20000> <td> 198 </td><td> $nbData198 </td><td> $pct %</td></tr>";

	echo "</table>";

	
}
else
{
	echo ("Pas de Donn&eacute;es dans le fichier $filename");
}
?>


	</body>
</html>
