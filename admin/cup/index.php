<!DOCTYPE html>

<html>

<head>

    <title>Freude Herrscht Cup</title>
    <script src="/jquery-3.6.1.min.js"></script>
    <link rel="stylesheet" href="/nachwuchscup/css/pico.min.css">
    <link rel="stylesheet" href="/nachwuchscup/css/styles.css">

</head>

<body>
    <div class="header">
        <nav>
            <ul>
                <li><strong>BOLV Cup manager</strong></li>
            </ul>
            <ul>
                <li><a href="/nachwuchscup/admin">Home</a></li>
            </ul>
        </nav>
    </div>
    <div class="container">
        <div class="horizontal-grid">
            <div>
                <h2>Kategorien</h2>
                <?php
                require('../../database.php');
                ini_set('display_errors', 1);
                $cupId = $_GET['id'];
                $category = 'D10';
                if (isset($_GET['category'])) {
                    $category = $_GET['category'];

                }

                $categories = $pdo->query("SELECT name, cup_id FROM cups_categories
                    WHERE cup_id = $cupId
                    ORDER BY name ASC");
                while ($row = $categories->fetch()) {
                    echo "<a href='?category={$row['name']}&id=$cupId'>{$row['name']}</a> <br>";
                }
                ?>
            </div>
            <div>
                <h2>Resultate</h2>
                <?php

                $resultsSql = $pdo->query("
                SELECT
                    results.runnerId as runnerId,
                    results.raceId as raceId,
                    results.points as points,
                    races.cupId as cupId,
                    races.name as raceName
                FROM
                    results
                LEFT JOIN
                    races
                ON
                    races.id = results.raceId
                WHERE races.cupId = {$cupId}   
                ");
                $results = $resultsSql->fetchAll();

                
                // Turn results into a matrix with raceId and runnerId as key and points as value
                $points = array_reduce($results, function ($carry, $item) {
                    $carry[$item['raceId']][$item['runnerId']] = $item['points'];
                    return $carry;
                }, []);

                $racesSql = $pdo->query("
                SELECT
                    cupId,
                    id,
                    name,
                    date
                FROM
                    races
                WHERE cupId = {$cupId}
                ORDER BY date ASC
                ");
                $races = $racesSql->fetchAll();
                function getPointsFor($runner, $id): int
                {
                    global $points;
                    return $points[$runner][$id] ?? 0;
                }
                

                $pdo->query("SET sql_mode = ''");
                $total_points = $pdo->query("SELECT
                    runners.name as runner,
                    runners.id as runnerId,
                    runners.year as birthyear, 
                    runners.club as club,
                    runners.canton as canton,
                    runners.category as category,
                    SUM(case when results.striked = 0 then results.points end) as points,
                    races.name as race,
                    races.id as raceId,
                    races.cupId as cupId
                  FROM results
                  RIGHT JOIN races ON results.raceId = races.id
                  LEFT JOIN runners ON runners.id = results.runnerId
                  WHERE races.cupId = '$cupId' AND category = '$category'
                  GROUP BY runner
                  ORDER BY points DESC");
                echo "<table>
                  <tr>
                      <th>Name</th>
                      <th>Club</th>
                      <th>Geburtsjahr</th>
                      <th>Kanton</th>
                      ";
                        foreach ($races as $race) {
                            echo "<th class='vertical'>{$race['name']}</th>";
                        }
                        echo "
                      <th>Total</th>
                      </tr>";

                while ($row = $total_points->fetch()) {
                    echo "
                        <tr> 
                        <td>{$row['runner']}
                         <td>CLUB</td>
                         <td>Jahrgang</td>
                         <td>Kanton</td>
                            ";
                            foreach ($races as $race) {
                        $racePoints = getPointsFor($race['id'], $row['runnerId']);
                                echo "<td>{$racePoints}</td>";
                            }
                            echo "
                         <td>{$row['points']}</td>
                         </tr>
                         ";

                }
                ?>
                </table>
            </div>
        </div>
        <div>
            <h2>Läufe</h2>
            <div>
        <?php
            ini_set('display_errors', 1);


            $season = $pdo->query("SELECT season FROM cups WHERE id = $cupId")->fetchColumn();

            $races = $pdo->query(
                "SELECT races.solv_id as solv_id, races.id as raceId, races.name as name, races.club as club, races.date as date, races.cupId as cupId, races.calculation as calculation
        FROM races
        WHERE cupId = $cupId"
            );
            $races->execute();

            echo '<table>
        <tr>
            <th>Lauf</th>
            <th>Klub</th>
            <th>Datum</th>
            <th>SOLV ID</th>
            <th>Laden</th>
            <th>Löschen</th>
            <th>Bearbeiten</th>
        </tr>';

            while ($row = $races->fetch()) {
                echo
                    "<tr class='races_table'>
                <td>{$row['name']}</td>
                <td>{$row['club']}</td>
                <td>{$row['date']}</td>
                <td>{$row['solv_id']}</td>
                <td>
                <form method='post' action='load_results.php'>
                    <input type='submit' name='Load Results' value='Laden' />
                    <input type='hidden' name='raceId' value='{$row['raceId']}' />
                    <input type='hidden' name='cup_id' value='{$cupId}' />
                    <input type='hidden' name='solv_id' value='{$row['solv_id']}' />
                    <input type='hidden' name='calculation' value='{$row['calculation']}' />
                 </form>
                </td>
                <td>
                <form method='post' action='delete_race.php'>
                    <input type='submit' title='delete' name='Delete' value='Löschen' />
                    <input type='hidden' name='race_id' value='{$row['raceId']}' />
                    <input type='hidden' name='cup_id' value='{$cupId}' />
                 </form>
                 </td>
                <td>
                 <form method='get' action='edit_race.php'>
                    <input type='submit' name='Delete' value='Edit' />
                    <input type='hidden' name='race_id' value='{$row['raceId']}' />
                    <input type='hidden' name='cup_id' value='{$cupId}' />
                 </form>
                </td>
                </tr>
                ";
            }
            ?>
            </table>

            </div>

        <article class="not-so-tall">
            <h2>Rennen hinzufügen</h2>
            <?php
                echo "<form method='get' action='add_race.php'>
                <input type='hidden' name='cupId' value='${cupId}'>
                <input type='hidden' name='season' value='${season}'>
                Berechnung
                <select name='calculation'>
                    <option value='NACHWUCHSCUP_STANDARD'>Nachwuchscup Standard</option>
                    <option value='NACHWUCHSCUP_SCHLUSSLAUF'>Nachwuchscup Schlusslauf</option>
                </select>
                <button type='submit' value='Rennen hinzufügen' id='submit'>Rennen hinzufügen</button>
                </form>"
            ?>
        </article>
        <div>
            <h2>Danger zone</h2>
            <div>
                <form method="post" action="delete_cup.php">
                    <?php
                    echo '<input style="display: none;" name="cup_id" value="' . $cupId . '">';
                    ?>
                    <button type="submit" value="Delete" id="submit">Delete Cup</button>
                </form>
            </div>
        </div>

    </div>
</body>

</html>