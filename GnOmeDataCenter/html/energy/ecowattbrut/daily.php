<?php
$arg_date=$_GET['date'];
if($arg_date==''){
	$arg_date=date("ymd");
}

$nb_day=$_GET['nb'];
if($nb_day==''){
	$nb_day=1;
}
//echo "$nb_day<br>\n";

$chart_subtitle='Consommation par jour';

/*Consommation*/
$watt_location="Consommation Electricité";
$watt_id=4;
$watt_color1="#A9BCF5";
$watt_color2="#AAAAAA";
$watt_max=0;
$total_watt=0;
$nb=$nb_day-1;
$watt_lastyear=8.18;
$watt_objectif=7.52;
$day=date('ymd', strtotime("20$arg_date - $nb day"));

$lasttime=0;
for( $index=0;$index<$nb_day;$index++){

	/*get yesteday raw file*/
	$filename=$day."_ecowatt";
	$txt_file    = file_get_contents("/home/pi/log_ecowatt_brut/$filename");
	$rows        = explode("\n", $txt_file);
	array_shift($rows);
	
	$watt=0;
	$nbsample=0;
	foreach($rows as $row => $data)
	{
		//extract row data
		$row_data = explode(',', $data);
		$arrlength = count($row_data);
		if(($arrlength>5 and $row_data[5]=="ok") or ($arrlength==3)){
			$sample=$row_data[2];
			if($sample<20000){
			
				$array_time= explode('/', $row_data[0]);
				//echo "$array_time[0], $array_time[1], $array_time[2]";
				$array_hour= explode(':', $row_data[1]);
				$time=mktime($array_hour[0], $array_hour[1], $array_hour[2], $array_time[0], $array_time[1], $array_time[2]);	

				if($lasttime){
					$delay=$time-$lasttime;						
				}else{
					$firsttime=$time;
				}

				$lasttime=$time;
				//echo date(DATE_RFC2822, $time);
				//echo "\t$delay\t$sample\t$watt<br>\n";
				$watt+=(($sample*$delay)/3600);
			}	
		}
	}


	$array_watt[$index]['kwatt']=($watt)/1000;
	$array_watt[$index]['jour']=date('Y-m-d', strtotime("20$day"));
	$total_watt+=($watt/1000);
	//echo date('Y-m-d', strtotime("20$day"));
	//echo " $watt<br>\n";

	//echo "$day :  $value <br>\n";
	$day=date('ymd', strtotime("20$day + 1 day"));
	
}
$nb_day_real=($time-$firsttime)/86400;
$watt_mean=($total_watt)/($nb_day_real);


/******************HTML ******************************************/
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Energie</title>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<style type="text/css">
${demo.css}
		</style>
		<script type="text/javascript">
$(function () {
    $('#container').highcharts({
	chart: {
		type: 'column'
		/*zoomType: 'x'*/
	},
        title: {
<?php 
		
		$current_day=$array_watt[$index]['jour'] ;
		$array_date=explode('/',$current_day);
		
		$dayfirst=date('l jS \of F Y', strtotime("20$arg_date - $nb day"));
		$daylast=date('l jS \of F Y', strtotime("20$arg_date"));
		
		$chart_title="$dayfirst $daylast";
		
		echo "text: '$chart_title'"; ?>
        },
        subtitle: {
<?php           echo "text: '$chart_subtitle'"; ?>
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

        yAxis: [
        { // Primary yAxis
            labels: {
                format: '{value}KWatt',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Consommation',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            plotLines: [
		{
<?php echo "			value: $watt_lastyear,\n"; ?>
		        color: 'red',
		        width: 1,
		        label: {
		            text: 'Moyenne 2014',
		            align: 'center',
		            style: {
		                color: 'gray'
		            }
			}
		},
		{
<?php echo "			value: $watt_objectif,\n"; ?>
		        color: 'orange',
		        width: 1,
		        label: {
		            text: 'Objectif 2015',
		            align: 'center',
		            style: {
		                color: 'gray'
		            }
			}
		},
		{
<?php echo "			value: $watt_mean,\n"; ?>
		        color: 'green',
		        width: 1,
		        label: {
		            text: 'Moyenne',
		            align: 'center',
		            style: {
		                color: 'gray'
		            }
			}
		}
            ],
	    min: 0,
        }],
        tooltip: {
	    shared: true
        },
	plotOptions : {
		series : {
			marker     : {enabled : true},
			stacking   : 'normal',
			pointRange : 24 * 3600 * 1000 // one month
		}
	},

	series: [
<?php
	$array_days=explode (",",$num_day);
	


	echo "\t\t\t{     name: \"Ecowatt\", \n";
	echo "\t\t\t\tdata: [\n";

	$arrlength = count($array_watt);
	
	for($row = 0; $row < $arrlength; $row++) {
		$date=$array_watt[$row]['jour'];
		$array_date  = explode ( "-" , $date );
		$month=$array_date[1];
		$day=$array_date[2];
		$year=$array_date[0];

		$value=$array_watt[$row]['kwatt'];

		if($value>$watt_max){
			$watt_max=$value;
			$watt_max_date=$date;
		}
		
	  //  echo ("\t\t\t\t\t[ Date.UTC( $year , $month-1 , $day , 0 , 0 , 0 ), { y:$value } ],\n") ;  
  		if($value>$watt_lastyear){
			$color='red';
		}else{
	  		if($value>$watt_objectif){
				$color='orange';
			}else{
				$color='green';
			}
		}
		echo ("\t\t\t\t\t{x: Date.UTC( $year , $month-1 , $day , 0 , 0 , 0 ), y: $value , color: '$color'} ,\n") ;    
	
	}
	echo "\t\t\t\t], tooltip: {valueSuffix: 'kWatt'}\n";
	echo "\t\t\t},/*serie*/\n";

?>
       
	]/*series*/

    });/*highchart*/
});/*function*/
		</script>
	</head>
	<body>
<script src="/highcharts/js/highcharts.js"></script>
<script src="/highcharts/js/modules/exporting.js"></script>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<?php 
	echo "<center>\n";
	echo "<br>Max power : $watt_max KWatt at $watt_max_date <br>\n"; 
	echo "<br>Total : $total_watt KWatt <br>\n"; 
	echo "<br>Moyenne : $watt_mean KWatt <br>\n"; 
	echo "</center>\n";

?>
	</body>
</html>
