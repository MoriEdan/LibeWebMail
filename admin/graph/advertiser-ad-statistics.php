<?php


include(__DIR__ . "/graphutils.inc.php");

$flag_time=0;
$show = $_GET['statistics'] ?? "day";


if ($show=="day") {
    $flag_time=-1;
    $showmessage="Today";
    $time=mktime(0,0,0,date("m",time()),date("d",time()),date("y",time()));
    $end_time=mktime(date("H",time()),0,0,date("m",time()),date("d",time()),date("y",time()));
    $beg_time=$time;
    //echo date("d M h a",$end_time);
} elseif ($show=="week") {
    $showmessage="Last 14 Days";
    $time=mktime(0,0,0,date("m",time()),date("d",time())-12,date("Y",time()));
    //$end_time-(14*24*60*60);
    $end_time=mktime(0,0,0,date("m",time()),date("d",time())+1,date("y",time()));
    $beg_time=$time;
} elseif ($show=="month") {
    $showmessage="Last 30 days";
    $time=mktime(0,0,0,date("m",time()),date("d",time())-28,date("Y",time()));
    //mktime(0,0,0,date("m",time()),1,date("y",time()));
    //$end_time=mktime(0,0,0,date("m",time()),date("d",time())+1,date("y",time()));
    $end_time=mktime(0,0,0,date("m",time()),date("d",time())+1,date("y",time()));
    $beg_time=$time;
} elseif ($show=="year") {
    $flag_time=1;
    $showmessage="Last 12 months";
    $time=mktime(0,0,0,date("m",time())+1-11,1,date("Y",time()));
    //$end_time-(365*24*60*60); //mktime(0,0,0,1,1,date("y",time()));
    $end_time=mktime(0,0,0,date("m",time())+1,1,date("y",time()));
    $beg_time=$time;
} elseif ($show=="all") {
    $flag_time=2;
    $showmessage="All Time";
    $time=0;
    $end_time=mktime(0,0,0,1,1,date("y",time())+1);
    $beg_time=$time;
} else
{
$showmessage="Last 14 Days";
	$time=mktime(0,0,0,date("m",time()),date("d",time())-12,date("Y",time()));//$end_time-(14*24*60*60);
$end_time=mktime(0,0,0,date("m",time()),date("d",time())+1,date("y",time()));
$beg_time=$time;
}



$returnVal=plotAdvertiserGraphs($show,$flag_time,$beg_time,$end_time);

$FC=$returnVal[0];
//$template->setValue("{FUSIONCHART1}",$FC->renderChart());

$FD=$returnVal[1];
//$template->setValue("{FUSIONCHART2}",$FD->renderChart());






?>