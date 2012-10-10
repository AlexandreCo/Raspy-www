<HTML>
<HEAD>
<TITLE>Gestion des donnees transport</TITLE>
</HEAD>
<BODY>
<form method=POST action=transport.php > 
Mois :<INPUT type=text size=2 name=mois>
Train :<INPUT type=text size=6 name=train>
Tram :<INPUT type=text size=6 name=tram>
V&eacute;lo :<INPUT type=text size=6 name=velo>
Voiture :<INPUT type=text size=6 name=voiture>

<INPUT type=submit value=Envoyer>
</FORM>


<?php
	$train= $_POST['train']; 
	$tram= $_POST['tram']; 
	$velo= $_POST['velo']; 
	$voiture= $_POST['voiture']; 
	$mois=$_POST['mois'];
	if (($train=="")||($tram=="")||($velo=="")||($voiture=="")||($mois==""))
	{
		print("Veuillez saisir ");
		if($mois=="") 
			print("mois ");
		if($train=="") 
			print("train ");
		if($tram=="") 
			print("tram ");
		if($velo=="") 
			print("velo ");
		if($voiture=="") 
			print("voiture ");

		print("\n");
	}
	else 
	{
		echo "R&eacute;capitulatif des informations saisies<BR>\n
		<UL>
		<LI>mois: $mois</LI>
		<LI>train: $train</LI>
		<LI>tram: $tram</LI>
		<LI>v&eacute;lo: $velo</LI>
		<LI>voiture: $voiture</LI>
		</UL>
		";

		if ($f = @fopen('../log_brut/2012_transport.txt', 'a+')) 
		{
			$data = array($mois,$train,$tram,$velo,$voiture);
			fputcsv($f,$data);
			fclose($f);
		}
		else 
		{
			echo "Impossible d'acc&eacute;der au fichier.";
		}
	}

?>
</BODY>
</HTML>
