<!DOCTYPE html>
<html>
<head>
    <title>Freude Herrscht Cup</title>
    <script src="/jquery-3.6.1.min.js"></script>
    <link rel="stylesheet" href="/nachwuchscup/css/pico.min.css">
    <link rel="stylesheet" href="/nachwuchscup/css/styles.css">
</head>
<body>
    <h1>Rennen hinzufügen</h1>

<?php
require('../../database.php');
ini_set('display_errors', 1);

$cupId = $_GET["cupId"];
$season = $_GET["season"];
$calculation = $_GET["calculation"];
print($calculation);


// Fetch races from solv
$curl = curl_init();
$url = "https://o-l.ch/cgi-bin/fixtures?&year=2022&csv=1";
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$csv = curl_exec($curl);

curl_close($curl);

$csv = utf8_encode($csv);
$arr = str_getcsv($csv, "\n"); //parse the rows

echo "<table>
        <tr>
            <th>Aktion</th>
            <th>Solv ID</th>
            <th>Name</th>
            <th>Datum</th>
            <th>Club</th>
        </tr>
";
foreach ($arr as $row) {
    $row = str_getcsv($row, ";"); //parse the items in rows
    $name = $row[8];
    $solv_id = $row[0];
    $club = $row[10];
    $date = $row[1];

    echo "<tr>";
    echo "<form method='post' action='add_race_action.php'>
            <input type='hidden' name='name' value='{$name}' />
            <input type='hidden' name='solv_id' value='{$solv_id}' />
            <input type='hidden' name='club' value='{$club}' />
            <input type='hidden' name='date' value='{$date}' />
            <input type='hidden' name='cupId' value='{$cupId}' />
            <input type='hidden' name='calculation' value='{$calculation}' />
            <td><input type='submit' value='Hinzufügen' /></td>
        </form>
    ";
    echo "<td>{$name}</td>";
    echo "<td>{$solv_id}</td>";
    echo "<td>{$club}</td>";
    echo "<td>{$date}</td>";
    echo "</tr>";
}
echo "</table>";


function addRace($solv_id, $name, $club, $date, $calculation){
global $pdo;
    global $cupId;
try {
    $pdo->beginTransaction();
    $sql = $pdo->query("INSERT INTO `races` (`solv_id`, `name`, `club`, `date`, `cupId`, `calculation`) VALUES ('$solv_id', '$name', '$club', '$date', $cupId, '$calculation')");

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
}

header("Location: index.php?id=$cupId");
}
?>
