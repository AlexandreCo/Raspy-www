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
        	exec("wifi.sh on");
	}
	if ($cmd == "off")
	{
	        echo exec("wifi.sh off");
	}

	echo exec("wifi.sh status");
?>
        </body>
</html>










