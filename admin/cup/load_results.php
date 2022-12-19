<?php
// Github Repo: https://github.com/rasifix/orienteering.api/blob/master/services/solv-loader.js
require('../../database.php');
ini_set('display_errors', 1);

$race = $_POST['solv_id'];
$cupId = $_POST['cup_id'];

$categories = $pdo->query("SELECT name FROM `cups_categories` WHERE `cup_id` = $cupId");
$categories = $categories->fetchAll(PDO::FETCH_COLUMN, 0);

// *** FETCH RESULTS FROM SOLV ***
$curl = curl_init();
$url = "https://o-l.ch/cgi-bin/results?type=rang&rl_id={$race}&kind=all&csv=1";
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$csv = curl_exec($curl);

curl_close($curl);

$csv = utf8_encode($csv);
$arr = str_getcsv($csv, "\n"); //parse the rows



$existing_runners = $pdo->query("SELECT CONCAT(name, '-', category, '-', year) AS RUNNER_CODE FROM `runners` WHERE `cupId` = $cupId");
$existing_runners = $existing_runners->fetchAll(PDO::FETCH_COLUMN, 0);



//Loop over the array
foreach($arr as $row){
    $row = str_getcsv($row, ";"); //parse the items in rows
    $catecory = $row[0];
    // Skip if category is not in cup
    if(!in_array($catecory, $categories)) {
        continue;
    }

    // Detect two equal runners by name, category and birthyear
    $name = $row[5];
    $birthyear = $row[6];
    // assign null if variable is empty

    if ($birthyear == "") {
        $birthyear = 0;
    }
    $runnerCode = $name . '-' . $catecory . '-' . $birthyear;
    if(!in_array($runnerCode, $existing_runners)) {
        // Get necessary Infos
        $club = $row[8];

        // Add to Runner's database
        $pdo->query("INSERT INTO `runners` (`name`, `year`, `category`, `club`, `cupId`) VALUES ('{$name}', '{$birthyear}', '{$catecory}', '{$club}', '{$cupId}')");

    }


    // Get runner's id
    $runnerId = $pdo->query("SELECT id FROM `runners` WHERE `name` = '{$name}' AND `year` = '{$birthyear}' AND `category` = '{$catecory}' AND `cupId` = '{$cupId}'") -> fetchColumn();
    // calculate points
    $rank = $row[4];
    $points = calculatePoints($rank);

    // Insert points if entry doesn't exist
    $pdo->query("INSERT INTO `results` (`runnerId`, `raceId`, `points`) 
    SELECT '{$runnerId}', '{$race}', '{$points}'
    FROM DUAL
    WHERE NOT EXISTS (
      SELECT 1 FROM `results` 
      WHERE `runnerId` = '{$runnerId}' AND `raceId` = '{$race}'
    )
    ");

    
    $rank = $row[4];
    echo $name . " " . $rank . "<br>";
}

// Method to calculate points
function calculatePoints($rank) {
    if($rank == "") {
        return 0;
    }
    $points = 31 - $rank;
    if($points < 0) {
        $points = 0;
    }
    return $points;
}

header("Location: index.php?id=$cupId");

?>
