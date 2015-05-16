<?php
$cmd = $_GET['wifi'];
?>
<!DOCTYPE html>
<html lang="fr">
        <head>
                <meta charset="utf-8"> 
                <title>Wifi config</title>
        </head>
        <body>
<?php
	if ($cmd == "on")
	{
        	exec("wifion");
	}
	if ($cmd == "off")
	{
	        echo exec("wifioff");
	}

	echo exec("wifistatus");
?>
        </body>
</html>










