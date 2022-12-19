<?php
// create & initialize a curl session
$curl = curl_init();

$year = $_GET["year"];

curl_setopt($curl, CURLOPT_URL, "https://o-l.ch/cgi-bin/fixtures?year={$year}&csv=1");

// return the transfer as a string, also with setopt()
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

// curl_exec() executes the started curl session
// $output contains the output string
$csv = curl_exec($curl);

// close curl resource to free up system resources
// (deletes the variable made by curl_init)
curl_close($curl);

$arr = str_getcsv($csv, ";"," ");

$index = 0;
$json = '[';
foreach($arr as &$value) {
    if(($index % 17) == 1) {
        $json .="{\"date\":\"{$value}\"},";
    }
    $index++;
}
$json .= "\n";

$json .= "]";
echo $json;


?>
