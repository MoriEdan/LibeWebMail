<?php 

function plotAdvertiserGraphs($show,$flag_time,$beg_time,$end_time,$selected_colorcode,$uid,$id =0)
{

if ($flag_time == 0) {
    $minus_time=mktime(0,0,0,date("n",$end_time),date("j",$end_time)-1,date("Y",$end_time));
}





$spec_time_limits=mktime(date("H",time())-24,0,0,date("m",time()),date("d",time()),date("y",time()));
$spec_time_limits_imps=mktime(0,0,0,date("m",time()),date("d",time()),date("y",time()));

$endtimes=mktime(date("H",time())+1,0,0,date("m",time()),date("d",time()),date("y",time()));
$admin_flag=0;

$path_str=substr(getcwd(),strlen(getcwd())-5);
if("admin" === $path_str)
	{
	$admin_flag=1;
	
	}
	global $mysql;
		$ctr_flag=0;
			$money_flag=0;
$aid_str=" and aid=$id ";
	if($id==0)
		{
		$aid_str="";
		}

$idsstr = $id == 0 ? "NA" : $id;

		
		$uid_str=" uid=$uid "; 
		
		
	if ($flag_time==0) {
        $table_name="advertiser_daily_statistics";
    } elseif ($flag_time==2) {
        $table_name="advertiser_yearly_statistics";
        $beg_time=$mysql->echo_one("select min(time) from $table_name where uid=$uid");
    } elseif ($flag_time==1) {
        $table_name="advertiser_monthly_statistics";
    }
		if($flag_time==-1)
			{
$impression_result=mysql_query("select COALESCE(sum(cnt),0), time from table   where $uid_str $aid_str and time>$beg_time group by time order by time");




				
			

			}
	
			
		



		if(1==$admin_flag)
			{
		# Include FusionCharts PHP Class
			include_once(__DIR__ . '/../FusionCharts/Class/FusionCharts_Gen.php');
			}
		else
			{
			include_once(__DIR__ . '/FusionCharts/Class/FusionCharts_Gen.php');
			}
		
		# Create Multiseries Column3D chart object using FusionCharts PHP Class
		$FC = new FusionCharts("MSColumn3DLineDY","750","300");
		
		# Set the relative path of the swf file
		
		
	    if(1==$admin_flag)
			{
			$FC->setSWFPath("../FusionCharts/");
			}
		else
			{
			$FC->setSWFPath("FusionCharts/");
			}
		# Store chart attributes in a variable
		$strParam="caption=Clicks and Impressions;subcaption=Comparison;xAxisName=Duration;yAxisName=Statistics; pYAxisName=Count;sYAxisName=CTR (%);decimalPrecision=$no_of_decimalplaces;rotateNames=1; showvalues=0;";
		
		# Set chart attributes
		$FC->setChartParams($strParam);
		
		$curr_impr=0;
		$curr_clk=0;
		$curr_ctr=0;
		$curr_money=0;
		
		
	
		
		
		
		
	
		
		$temp_time=$beg_time;
		
			
			
			
		while($temp_time<=$end_time)
		{
	//	echo  "$temp_time=$beg_time=$end_time<br>";flush();
			if ($flag_time==0) {
                $str=dateformat($temp_time-1,"%b %d");
                $temp_time=mktime(0,0,0,date("n",$temp_time),date("j",$temp_time)+1,date("Y",$temp_time));
                $show_duration="$str";
            } elseif ($flag_time==1) {
                $str=dateformat($temp_time-86400,"%b");
                //"M",$temp_time-86400);
                $temp_time=mktime(0,0,0,date("n",$temp_time)+1,date("j",$temp_time),date("Y",$temp_time));
                $show_duration="$str";
            } elseif ($flag_time==2) {
                $str=dateformat($temp_time-86400,"%Y");
                //("Y",$temp_time-86400);
                $temp_time=mktime(0,0,0,date("n",$temp_time),date("j",$temp_time),date("Y",$temp_time)+1);
                $show_duration="$str";
            }
			if($flag_time==-1)
			{
					$str=datetimeformat($temp_time,"%d %b %l %p");//("d M h a",$temp_time);
					$temp_time=mktime(date("H",$temp_time)+1,0,0,date("n",$temp_time),date("j",$temp_time),date("Y",$temp_time));
					$show_duration="$str";
			}
			
			
			
		//echo "$show_duration <br>";
//	 $show_duration;
//	exit;
			$FC->addCategory("$show_duration");
		
			


			
			
			
		//echo "$show_duration";
		
		}
		
					
		$temp_time=$beg_time;
		$FC->addDataset("Impressions","showvalues=0");
		$loop_flag=0;
		
		
		while( $temp_time<=$end_time)
		{
		
	
	

			$row=mysql_fetch_row($stat_result);
				
				
				//echo $end_time."==".$temp_time."==".$minus_time."<br>";
				
				if($end_time==$temp_time)
				{
			
						
						if ($curr_impr === 0) {
                            $FC->addChartData('');
                        } else {
                            $FC->addChartData("$curr_impr");
                        }
						break;
					
				
				}
				
				
				
				
				
				
		
		
			
			
			if($row[3]==$temp_time)
			{
		
				$loop_flag=0;
				$FC->addChartData("$row[0]");
						
	
				
			}
		
			
				

		
		
					//echo $row[3]."==".$temp_time.$row[0]."<br>";
			if ($flag_time==0) {
                $temp_time=mktime(0,0,0,date("n",$temp_time),date("j",$temp_time)+1,date("Y",$temp_time));
            } elseif ($flag_time==1) {
                $temp_time=mktime(0,0,0,date("n",$temp_time)+1,date("j",$temp_time),date("Y",$temp_time));
            } elseif ($flag_time==2) {
                $temp_time=mktime(0,0,0,date("n",$temp_time),date("j",$temp_time),date("Y",$temp_time)+1);
            }
		if($flag_time==-1)
			{
			$temp_time=mktime(date("H",$temp_time)+1,0,0,date("n",$temp_time),date("j",$temp_time),date("Y",$temp_time));
			}		
		}
	//	............
	

	
		return $retVal;
}	




?>