<HTML>
<HEAD>
</HEAD>
<body bgcolor=black text=white link=blue vlink=yellow> 


<?php

function get_val ( $date )
{
	return $_GET[$date];
}
$date = get_val('date');
if($date=='')
{
        $date=date("Y-m-d");
}


$filename = $date;
$filename .= "_data.txt";
if ($fp = fopen("../log_brut/$filename","r"))
{
	echo "<br><br><H3>Donn&eacute;es capteurs du $date:<br>";
	echo "<table border=1><tr><td>Capteur</td><td>Date</td><td>Heure(GMT)</td><td>Temp&eacute;rature 1</td><td>Pression</td><td>Humidit&eacute;</td><td>Temp&eacute;rature 2</td><td>Batterie</td></tr>";
	
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

		}

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
	$nbtotal=$nbData0+$nbData1+$nbData11+$nbData21;
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
	echo "</table>";


	
}
else
{
	echo ("Pas de Donn&eacute;es dans le fichier $filename");
}
?>

</body>
</HTML>

