<?php
/**
  * Reports error during API RPC request
  */
class ApiRequestException extends Exception {}
/**
  * Returns DOM object representing request for information about all
available domains
  * @return DOMDocument
  */
function domainsInfoRequest()
{
   
}
/**
  * Prepares CURL to perform Plesk API request
  * @return resource
  */
function curlInit($host, $login, $password)
{
       $curl = curl_init();
       curl_setopt($curl, CURLOPT_URL,
"https://{$host}:8443/enterprise/control/agent.php");
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($curl, CURLOPT_POST,           true);
       curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
       curl_setopt($curl, CURLOPT_HTTPHEADER,
              ["HTTP_AUTH_LOGIN: {$login}",
                     "HTTP_AUTH_PASSWD: {$password}",
                     "HTTP_PRETTY_PRINT: TRUE",
                     "Content-Type: text/xml"]
       );
       return $curl;
}
/**
  * Performs a Plesk API request, returns raw API response text
  *
  * @return string
  * @throws ApiRequestException
  */
function sendRequest($curl, $packet)
{

       curl_setopt($curl, CURLOPT_POSTFIELDS, $packet);
       $result = curl_exec($curl);
	
       if (curl_errno($curl) !== 0) {
              $errmsg = curl_error($curl);
              $errcode = curl_errno($curl);
              curl_close($curl);
              throw new ApiRequestException($errmsg, $errcode);
       }
       curl_close($curl);
       return $result;
}
                                                
/**
  * Looks if API responded with correct data
  *
  * @return SimpleXMLElement
  * @throws ApiRequestException
  */
function parseResponse($response_string)
{

       $xml = new SimpleXMLElement($response_string);
       if (!is_a($xml, 'SimpleXMLElement')) {
           throw new ApiRequestException("Cannot parse server
response: {$response_string}");
       }
       return $xml;
}
/**
  * Check data in API response
  * @return void
  * @throws ApiRequestException
  */
function checkResponse(SimpleXMLElement $response)
{
       $resultNode = $response->domain->get->result;
       // check if request was successful 
       if ('error' === (string)$resultNode->status) {
           throw new ApiRequestException("Plesk API returned error:
" . $resultNode->result->errtext);
       }
}
//
// int main()
//




?>
