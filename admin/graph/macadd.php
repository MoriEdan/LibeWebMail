<script language='javascript' src='FusionCharts/FusionCharts.js'></script>
<?php

	$con=mysql_connect("localhost","root","root");
		mysql_select_db("webmail");

		if(!$con)
		{
		  	die('Error'.mysql_error());
		}
		
		
		//echo mktime(0,0,0,11,date("d",time()),2013);
	$show="month";	
		$month="";
		$year="";
		$table="nesote_email_ip_102011";
if ($show === "today") {
    $flag_time=-1;
    $showmessage="month";
    $time=mktime(0,0,0,date("m",time()),date("d",time()),date("y",time()));
    $end_time=mktime(date("H",time()),0,0,date("m",time()),date("d",time()),date("y",time()));
    $beg_time=$time;
    //echo date("d M h a",$end_time);
} elseif ($show === "month") {
    $showmessage="Last 30 days";
    $time=mktime(0,0,0,date("m",time()),1,date("Y",time()));
    //mktime(0,0,0,date("m",time()),1,date("y",time()));
    //$end_time=mktime(0,0,0,date("m",time()),date("d",time())+1,date("y",time()));
    $end_time=mktime(0,0,0,date("m",time()),31,date("y",time()));
    $beg_time=$time;
} elseif ($show=="year") {
    $flag_time=1;
    $showmessage="Last 12 months";
    $time=mktime(0,0,0,1,1,date("Y",time()));
    //$end_time-(365*24*60*60); //mktime(0,0,0,1,1,date("y",time()));
    $end_time=mktime(0,0,0,12,31,2013);
    $beg_time=$time;
} elseif ($show=="all") {
    $flag_time=2;
    $showmessage="All Time";
    $time=0;
    $end_time=mktime(0,0,0,1,1,date("y",time())+1);
    $beg_time=$time;
}



 $returnVal=plotAdvertiserGraphs($show,-1,$beg_time,$end_time);		
		
	//	print_r($returnVal);
		
	$FC=$returnVal[0];
	$FC->renderChart();
		
		
		
		
		
		
		
		
		
		
function plotAdvertiserGraphs($show,$flag_time,$beg_time,$end_time)
{




	




$flag_time=1;






		
		
		
		if($show=="today")
{


$stat_result=mysql_query("select time from nesote_email_ip_102011 where time<=$end_time and time>=$beg_time    order by time asc " );

}
		if($show=="month")
{


$stat_result=mysql_query("select time from nesote_email_ip_102011   order by time asc " );

}
		if($show=="year")
{


$stat_result=mysql_query("select time from nesote_email_ip_102011    union select time from nesote_email_ip_092011  order by time asc" );

}
//echo mysql_num_rows($stat_result);
				
			
$res_array=[];
	
			while($row=mysql_fetch_row($stat_result))
			{
			
				if($show=="month")
				{
				$tm=mktime(0,0,0,date("m",$row[0]),date("d",$row[0]),date("Y",$row[0]));
			
			$res_array[$tm] = isset($res_array[$tm]) ? $res_array[$tm]+1 : 1;
				}
			
			
				if($show=="today")
				{
				
			$tm=mktime(date("H",$row[0]),0,0,date("m",$row[0]),date("d",$row[0]),date("Y",$row[0]));
			
			$res_array[$tm] = isset($res_array[$tm]) ? $res_array[$tm]+1 : 1;
				}
				
				if($show=="year")
				{
				
			$tm=mktime(0,0,0,1,1,date("Y",$row[0]));
			
			$res_array[$tm] = isset($res_array[$tm]) ? $res_array[$tm]+1 : 1;
				}
			
			
			
			
			}
			
			
		
		include_once(__DIR__ . '/FusionCharts/Class/FusionCharts_Gen.php');
			
		
		# Create Multiseries Column3D chart object using FusionCharts PHP Class
		$FC = new FusionCharts("MSColumn3DLineDY","1000","600");
		
		# Set the relative path of the swf file
		
		
	  
			$FC->setSWFPath("FusionCharts/");
			
		# Store chart attributes in a variable
		//$strParam="caption=Clicks and Impressions;subcaption=Comparison;xAxisName=Duration;yAxisName=Statistics; pYAxisName=Count;sYAxisName=CTR (%);decimalPrecision=$no_of_decimalplaces;rotateNames=1; showvalues=0;";
		
		
		
	$strParam="caption=Clicks and Impressions;subcaption=Comparison;xAxisName=Duration;yAxisName=Statistics; pYAxisName=Count;sYAxisName=CTR (%);decimalPrecision=0;rotateNames=1; showvalues=0;";
		
		
		# Set chart attributes
		$FC->setChartParams($strParam);
		
	
		

		
		//
		
			
			//$temp_time=mktime(0,0,0,date("n",time()),date("j",time()),date("Y",time()));
			$temp_time=$beg_time;
			
		
			
			//$end_time=mktime(date("H",time()),0,0,date("n",time()),date("j",time()),date("Y",time()));
	//	echo $flag_time;	
		while($temp_time<=$end_time)
		{
			if ($flag_time==0) {
                //within month
                $str=date("M d",$temp_time);
                $temp_time=mktime(0,0,0,date("n",$temp_time),date("j",$temp_time)+1,date("Y",$temp_time));
                $show_duration="$str";
            } elseif ($flag_time === 1) {
                //within year
                $str=date("M",$temp_time);
                $temp_time=mktime(0,0,0,date("n",$temp_time)+1,date("j",$temp_time),date("Y",$temp_time));
                $show_duration="$str";
            } elseif ($flag_time==2) {
                //yearwise
                $str=date("Y",$temp_time);
                $temp_time=mktime(0,0,0,date("n",$temp_time),date("j",$temp_time),date("Y",$temp_time)+1);
                $show_duration="$str";
            }
			if($flag_time==-1)
			{//today
					$str=date("d M h a",$temp_time);
					$temp_time=mktime(date("H",$temp_time)+1,0,0,date("n",$temp_time),date("j",$temp_time),date("Y",$temp_time));
					$show_duration="$str";
			}
			
			

			$FC->addCategory("$show_duration");

			
		}
	
					
		$temp_time=$beg_time;
		$FC->addDataset("Logins","showvalues=0");

		
	
		while( $temp_time<=$end_time)
		{

			
						if ($res_array[$temp_time] == 0 || $res_array[$temp_time] == 0) {
                            $FC->addChartData('');
                        } else {
                            $FC->addChartData("$res_array[$temp_time]");
                        }
						//break;

			
				
			if ($flag_time==0) {
                $temp_time=mktime(0,0,0,date("n",$temp_time),date("j",$temp_time)+1,date("Y",$temp_time));
            } elseif ($flag_time === 1) {
                $temp_time=mktime(0,0,0,date("n",$temp_time)+1,date("j",$temp_time),date("Y",$temp_time));
            } elseif ($flag_time==2) {
                $temp_time=mktime(0,0,0,date("n",$temp_time),date("j",$temp_time),date("Y",$temp_time)+1);
            }
		if($flag_time==-1)
			{
			$temp_time=mktime(date("H",$temp_time)+1,0,0,date("n",$temp_time),date("j",$temp_time),date("Y",$temp_time));
			}		
		}

	
		
	
$retVal[0]=$FC;
	
		return $retVal;
		
		
		
}	
		
		
		
		
		
		
		
?>