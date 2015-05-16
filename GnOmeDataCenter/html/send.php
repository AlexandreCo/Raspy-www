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
if ($fp = fopen("../log_brut/$filename","a"))
{
	fprintf($fp,"%d;%s;%s;%f;%d;%d;%f;%f;%d\n",$_GET['capteur'], 
			$date,
			date("G:i:s"),
			$_GET['temp1'],
			$_GET['pression'],
			$_GET['humi'],
			$_GET['temp2'],
			$_GET['bat'],
			$_GET['rain']
	);
	fclose($fp);
	echo("ok");
}
else
{
	echo ("Probleme avec le fichier $filename");
}
?>

</body>
</HTML>

