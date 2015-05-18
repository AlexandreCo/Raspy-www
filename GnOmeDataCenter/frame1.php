<HTML>
  <HEAD>
  </HEAD>

<body style="background-color:black;">
<center>
<table style="font-family:verdana;color:white;">
	<tr>
		<td>
			<table style="font-family:verdana;color:white;">
<?php

	$dirname = '/var/www/GnOmeDataCenter/config/menu/';
	$dir = opendir($dirname); 
echo "<table style='font-family:verdana;color:white;' align='center' border=0><tr>";
	while($file = readdir($dir)) {
		if($file != '.' && $file != '..' && !is_dir($dirname.$file))
		{
			if ($fp = fopen("$dirname$file","r"))
			{
				
				while (!feof($fp)) 
				{ 
					$line = fgets($fp, 255);
					$array = explode ( "%" , $line );
					if($array[0]=="D")
					{
						echo "
							<td>
								<a	style='font-family:verdana;color:white;' 
									href='$array[1]' 
									target='droite'>
										$array[2]
								</a>
							</td>";
					}
					else
					{
						if($array[0]=="R")
						{
							echo "</tr><tr>";
						}
						else
						{
							if($array[0]=="L")
	                                                {
								echo "<td><HR></td></tr><tr>";
							}
                                                	else
                                                	{
                                                        	if($array[0]=="T")
                                                        	{
                                                                	echo "
										<td>
											<span style='font-weight:bold;'>$array[1]</span> 
										</td>
									</tr>
									<tr>";
                                                        	}
                                                        	else
                                                        	{

                                                        	}
                                                	}

						}
					}
				}
				fclose($fp);

			}
			else
			{
				echo "<br>pas de fichier $dirname.$file<br>";	
			}
		}
	}
				echo "</tr></table>";

	closedir($dir);


?>
		</td>
	</tr>
</table>
</BODY>
</HTML>


