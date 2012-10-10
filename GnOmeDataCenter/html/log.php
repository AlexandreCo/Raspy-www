<HTML>
<HEAD>
</HEAD>
<body bgcolor=black text=white link=blue vlink=yellow> 
<style type="text/css">
<!--
a.redlink { color: red; }
a.redlink:hover { color: yellow;}
-->
</style>
<?php
function get_val ( $date )
{
	return $_GET[$date];
}
	
	$acDate = get_val('date');
	if($acDate=='')
	{
		$acDate=date("ymd");
	}

	$filename = $acDate."_archive.tar.gz";
	echo "<H3>Archive du $acDate : ";
	if ($fp = fopen("../log/$filename","r"))
	{
		echo "<a href='../log/$filename' class='redlink'>$filename</a><br>";
		fclose($fp);
	}
	else
	{
		echo ("pas de d'archive");
	}

	$filename = $acDate;
	$filename .= "_rf2txt.log";
	echo "<H3>Log processus du $acDate:<br>";
	$nbLine=0;
	if ($fp = fopen("../log/$filename","r"))
	{
		echo "<table border=1><tr><td>Date</td><td>Heure</td><td>Log</td></tr>";
		while (!feof($fp)) 
		{ 
			$nbLine++;
			$line = fgets($fp, 255);
			$array = explode ( " : " , $line );
			if(($nbLine % 2)==0)
			{
				$color="#008000";
			}
			else
			{
				$color="#4B0082";
			}
			$date = explode ( " " , $array[0] );
	    		echo "<tr bgcolor=$color><td>$date[0]</td><td>$date[1]</td><td>$array[1]</td><tr>";
		}
		fclose($fp);
		echo "</table>";
	}
	else
	{
		echo ("<h4>Pas de Donn&eacute;es");
	}

	$filename = $acDate;
	$filename .= "_access_log";
	echo "<br><br><H3>Log Apache du $acDate :<br>";
	$nbLine=0;
	if ($fp = fopen("../log/$filename","r"))
	{
		echo "<table border=1><tr><td>Ip</td><td>Log</td></tr>";
		while (!feof($fp)) 
		{ 
			$nbLine++;
			$line = fgets($fp, 255);
			$array = explode ( " - " , $line );
			
			if(($nbLine % 2)==0)
			{
				$color="#008000";
			}
			else
			{
				$color="#4B0082";
			}
			
	    		echo "<tr bgcolor=$color><td>$array[0]</td><td>$array[1]</td><tr>";
		}
		fclose($fp);
		echo "</table>";
	}
	else
	{
		echo ("<h4>Pas de Donn&eacute;es : $filename");
	}
	echo "<br><br><h4>Clef de stockage : ";
	exec("df -h| grep var | grep -v none | awk '{ print $5 }'",$resultat);
	echo "$resultat[0] utilis&eacute;";

?>

</body>
</HTML>
