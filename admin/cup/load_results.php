<?php
// Github Repo: https://github.com/rasifix/orienteering.api/blob/master/services/solv-loader.js
require('../../database.php');
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

$raceId = $_POST['raceId'];
$solvId = $_POST['solv_id'];
$cupId = $_POST['cup_id'];
$calculation = $_POST['calculation'];

$categories = $pdo->query("SELECT name FROM `cups_categories` WHERE `cup_id` = $cupId");
$categories = $categories->fetchAll(PDO::FETCH_COLUMN, 0);

// *** FETCH RESULTS FROM SOLV ***
$curl = curl_init();
$url = "https://o-l.ch/cgi-bin/results?unique_id=$solvId&kind=all&csv=1";
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$csv = curl_exec($curl);

curl_close($curl);

$csv = utf8_encode($csv);
$arr = str_getcsv($csv, "\n"); //parse the rows



$existing_runners = $pdo->query("SELECT CONCAT(name, '-', category, '-', year) AS RUNNER_CODE FROM `runners` WHERE cupId = {$cupId}");
$existing_runners = $existing_runners->fetchAll(PDO::FETCH_COLUMN, 0);


$cup_clubs = $pdo->query("SELECT name FROM `cups_clubs` join clubs on cups_clubs.clubId = clubs.id WHERE cupId = $cupId")->fetchAll(PDO::FETCH_COLUMN, 0);

$regional_rank = 1;
$current_category = "";
//Loop over the array
foreach($arr as $row){
    $row = str_getcsv($row, ";"); //parse the items in rows
    $catecory = $row[0];
    // Skip if category is not in cup
    if(!in_array($catecory, $categories)) {
        continue;
    }

    // Reset regional rank if category changed
    if ($current_category != $catecory) {
        $regional_rank = 1;
        $current_category = $catecory;
    }

    $runners_club = $row[8];
    // Skip if runner not in this region
    if(isClubIn($runners_club, $cup_clubs) === false) {
        if(mb_stripos($runners_club, "/") !== false) {
            // Double club name
            $runners_clubs = explode("/", $runners_club);
            if(isClubIn($runners_clubs[0], $cup_clubs) === false && isClubIn($runners_clubs[1], $cup_clubs) === false) {
                continue;
            }
        } else {
            continue;
        }
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

        // Add to runer's database if not already exists
        $pdo->query("INSERT INTO `runners` (`name`, `year`, `category`, `club`, `cupId`) select '{$name}', '{$birthyear}', '{$catecory}', '{$club}', '{$cupId}' from dual where not exists (select 1 from runners where name = '{$name}' and year = '{$birthyear}' and category = '{$catecory}' and cupId = '{$cupId}')");
        

    }


    // Get runner's id
    $runnerId = $pdo->query("SELECT id FROM `runners` WHERE `name` = '{$name}' AND `year` = '{$birthyear}' AND `category` = '{$catecory}' AND `cupId` = '{$cupId}'") -> fetchColumn();
    // calculate points
    $rank = $row[4];
    $points = calculatePoints($regional_rank, $calculation);
    echo $name . " " . $points . " " . $regional_rank . " " . $club  . "<br>";


    $pdo->query("INSERT INTO `results` (`runnerId`, `raceId`, `cupId`, `points`) 
    VALUES ('{$runnerId}', '{$raceId}', '{$cupId}', '{$points}')
    ON DUPLICATE KEY UPDATE `points` = '{$points}'
    ");

    // Get races and points for runner with runnerId
    $bestSix = $pdo->query("SELECT runnerId, raceId, cupId, points, striked from results where runnerId = $runnerId and cupId = $cupId order by points desc limit 0, 6")->fetchAll();
    if (sizeof($bestSix) == 6) {
        $pdo->query("UPDATE `results` SET `striked` = 1 WHERE `runnerId` = '{$runnerId}' and `cupId` = '{$cupId}'");
        foreach ($bestSix as $row) {
            $pdo->query("UPDATE `results` SET `striked` = 0 WHERE `runnerId` = '{$row[0]}' AND `raceId` = '{$row[1]}' and `cupId` = '{$cupId}'");
        }
    }
    // Increase rank
    $regional_rank++;
}

// Method to calculate points
function calculatePoints($rank, $calculation) {
    if($rank == "") {
        return 0;
    }

    $maxPoints = 30;
    if ($calculation == 'NACHWUCHSCUP_SCHLUSSLAUF') {
        $maxPoints = 40;
    }

    $points = $maxPoints + 1 - $rank;
    if($points < 0) {
        $points = 0;
    }
    return $points;
}

function isClubIn($club, $clubs)
{
    return mb_stripos(implode($clubs), $club);
}

header("Location: index.php?id=$cupId");

?>
