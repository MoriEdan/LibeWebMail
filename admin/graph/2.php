<script language='javascript' src='FusionCharts/FusionCharts.js'></script>
<?php
function plotAdvertiserGraphs($show,$flag_time,$beg_time,$end_time)	
{	
include_once(__DIR__ . '/FusionCharts/Class/FusionCharts_Gen.php');
		

# Create Multiseries Column3D chart object using FusionCharts PHP Class
$FC = new FusionCharts("MSColumn3DLineDY","750","300");
		

# Set the relative path of the swf file
$FC->setSWFPath("FusionCharts/");

			
# Store chart attributes in a variable
$strParam="caption=ccccccccc;subcaption=Comparison;xAxisName=Duration;yAxisName=Statistics; pYAxisName=Count;sYAxisName=CTR (%);rotateNames=1; showvalues=0;";
		

# Set chart attributes
$FC->setChartParams($strParam);




$FC->addCategory(33000);
$FC->addDataset("comparision","showvalues=10");
$FC->addChartData(10000);


$retVal[0]=$FC;
return $retVal;

}



$returnVal=plotAdvertiserGraphs($show,$flag_time,$beg_time,$end_time);
$FC=$returnVal[0];



$FC->renderChart();
?>
