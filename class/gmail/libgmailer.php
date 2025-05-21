<?php
#Copyright 2006 Svetlozar Petrov
#All Rights Reserved
#svetlozar@svetlozar.net
#http://svetlozar.net

#Script to import the names and emails from gmail contact list

class GMailer extends baseFunction
{
public $location = "";
public $cookiearr = [];

#Globals Section, $location and $cookiearr should be used in any script that uses
#getAddressbook function
#function getAddressbook, accepts as arguments $login (the username) and $password
#returns array of: array of the names and array of the emails if login successful
#otherwise returns 1 if login is invalid and 2 if username or password was not specified

function getAddressbook($login, $password)
{

#the globals will be updated/used in the read_header function
global $location;
global $cookiearr;
global $ch;

#check if username and password was given:
if ((isset($login) && trim($login) === "") || (isset($password) && trim($password) === ""))
{
#return error code if they weren't
return 2;
}

#initialize the curl session
$ch = curl_init();

#submit the login form:
curl_setopt($ch, CURLOPT_URL,"https://www.google.com/accounts/ClientLogin");
curl_setopt($ch, CURLOPT_REFERER, "");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ["Email"=> $login, "Passwd" => $password, "service" => "cp", "source" => "testCo-myApp-1"]);

$html = curl_exec($ch);

$Auth = strstr($html, "Auth=");
#test if login was successful:
if(!$Auth) {return 1;}
$Auth = substr($Auth, 5);
$Auth = trim($Auth);

#this is the contact url:
curl_setopt($ch, CURLOPT_URL, "http://www.google.com/m8/feeds/contacts/".urlencode((string) $login)."/full?max-results=10000");

curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: GoogleLogin auth='.$Auth]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_HTTPGET, 1);

 $html = curl_exec($ch);

$rows = explode("<entry>", $html);
array_shift($rows);

$result = [];
$result['name'] = [];
$result['email'] = [];
foreach($rows as $contents){

$result['name'][] = text_extract($contents, "<title type='text'>", "</title>");
$result['email'][] = text_extract($contents, "address='", "' primary='");

}
print_r($result);

return $result;
}

}

function text_extract($string,$ot,$ct)
{
$string = trim((string) $string);
$start = intval(strpos($string,(string) $ot) + strlen((string) $ot));
return substr($string,$start,intval(strpos($string,(string) $ct) - $start));
} 


?>