
<html>
<body style="background-color:black;">
<center><p style="font-family:verdana;color:white;">
GnOme Data Center

<?php
	$config=isset($_GET["config"]);
	if($config)
	{
		$filename = $_GET["config"];
	} 
	else
	{
	    $filename = "config.ini";
	}
	        
	exec("df -h| grep var | grep -v none | awk '{ print $5 }'",$resultat);
	echo "<br>Memoire : $resultat[0]<br>";
	if($filename=="")
	{
		$filename = "config.ini";
	}
	if ($fp = fopen("../config/$filename","r"))
	{
		echo "<table style='font-family:verdana;color:white;' align='center' border=1><tr>";
		while (!feof($fp)) 
		{ 
			$nbLine++;
			$line = fgets($fp, 255);
			$array = explode ( "%" , $line );
			if($array[0]=="D")
			{
				echo "<td>
					<div align=center>
						<a href='../out/$array[1]'>
							<img src='../out/$array[1]' alt='$array[2]'  style='width:200' >
						</a>
						<p class='todo' align=center>
							$array[2]
						</p>
					</div>
				</td>";
			}
			else
			{
				if($array[0]=="R")
				{
					echo "</tr><tr>";
				}
			}
		}
		fclose($fp);
		echo "</tr></table>";
	}
	else
	{
		echo ("<h4>Pas de Donn&eacute;es");
	}


?>

</p>
</body>
</html>
