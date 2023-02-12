<!DOCTYPE html>

<html>

<head>

    <title>Freude Herrscht Cup</title>
    <script src="/jquery-3.6.1.min.js"></script>
    <link rel="stylesheet" href="/nachwuchscup/css/pico.min.css">
    <link rel="stylesheet" href="/nachwuchscup/css/styles.css">

</head>

<body>
    <?php
    require('../../database.php');
    ini_set('display_errors', 1);
    $cupId = $_GET["cup_id"];
    $raceId = $_GET["race_id"];
    $category = 'D10';
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
    }

    $results = $pdo->query("
    SELECT
        results.runnerId as runnerId,
        results.raceId as raceId,
        results.points as points,
        runners.club as runnerClub,
        runners.year as runnerYear,
        runners.canton as runnerCanton,
        races.cupId as cupId,
        races.name as raceName,
        runners.name as runnerName,
        runners.category as runnerCategory
    FROM results

    LEFT JOIN races ON races.id = results.raceId
    LEFT JOIN runners ON runners.id = results.runnerId

    WHERE races.cupId = {$cupId}   
    AND races.id = {$raceId}
    AND runners.category = '{$category}'
    ");

    ?>
    <div class="header">
        <nav>
            <ul>
                <li><strong>Rennen bearbeiten</strong></li>
            </ul>
            <ul>
                <li><a href="/nachwuchscup/admin">Home</a></li>
            </ul>
        </nav>
    </div>
    <div class="container">
        <div class="horizontal-grid">

            <div>
                <table id="employee_grid" class="table table-condensed table-hover table-striped bootgrid-table"
                    width="60%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Club</th>
                            <th>Geburtsjahr</th>
                            <th>Kanton</th>
                            <th>Punkte</th>
                            <th>Ändern</th>
                        </tr>
                    </thead>
                    <tbody id="_editable_table">
                        <?php foreach ($results as $res): ?>
                            <tr data-row-id="<?php echo $res['id']; ?>">
                                <td class="editable-col" contenteditable="true" col-index="0"
                                    oldval="<?php echo $res['runnerName']; ?>"><?php echo $res['runnerName']; ?></td>
                                <td class="editable-col" contenteditable="true" col-index="1"
                                    oldval="<?php echo $res['runnerClub']; ?>"><?php echo $res['runnerClub']; ?></td>
                                <td class="editable-col" contenteditable="true" col-index="2"
                                    oldval="<?php echo $res['runnerCanton']; ?>"><?php echo $res['runnerCanton']; ?></td>
                                <td class="editable-col" contenteditable="true" col-index="3"
                                    oldval="<?php echo $res['points']; ?>"><?php echo $res['points']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>



                <script type="text/javascript">
                    $(document).ready(function () {
                        $('td.editable-col').on('focusout', function () {
                            data = {};
                            data['val'] = $(this).text();
                            data['id'] = $(this).parent('tr').attr('data-row-id');
                            data['index'] = $(this).attr('col-index');
                            if ($(this).attr('oldVal') === data['val'])
                                return false;

                            $.ajax({

                                type: "POST",
                                url: "server.php",
                                cache: false,
                                data: data,
                                dataType: "json",
                                success: function (response) {
                                    //$("#loading").hide();
                                    if (response.status) {
                                        $("#msg").removeClass('alert-danger');
                                        $("#msg").addClass('alert-success').html(response.msg);
                                    } else {
                                        $("#msg").removeClass('alert-success');
                                        $("#msg").addClass('alert-danger').html(response.msg);
                                    }
                                }
                            });
                        });
                    });

                </script>













            </div>
            <div>
                <h2>Kategorien</h2>
                <?php
                require('../../database.php');
                ini_set('display_errors', 1);
                $cupId = $_GET["cup_id"];
                $raceId = $_GET["race_id"];

                $category = 'D10';
                if (isset($_GET['category'])) {
                    $category = $_GET['category'];
                }

                $categories = $pdo->query("SELECT name, cup_id FROM cups_categories
                    WHERE cup_id = $cupId
                    ORDER BY name ASC");
                while ($row = $categories->fetch()) {
                    echo "<a href='?category={$row['name']}&cup_id=$cupId&race_id=$raceId'>{$row['name']}</a> <br>";
                }
                ?>
            </div>
            <div>
                <h2>Resultate</h2>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Club</th>
                        <th>Geburtsjahr</th>
                        <th>Kanton</th>
                        <th>Punkte</th>
                        <th>Ändern</th>
                    </tr>
                    <?php

                    $resultsSql = $pdo->query("
                SELECT
                    results.runnerId as runnerId,
                    results.raceId as raceId,
                    results.points as points,
                    races.cupId as cupId,
                    races.name as raceName,
                    runners.name as runnerName,
                    runners.category as runnerCategory
                FROM results

                LEFT JOIN races ON races.id = results.raceId
                LEFT JOIN runners ON runners.id = results.runnerId

                WHERE races.cupId = {$cupId}   
                AND races.id = {$raceId}
                AND runners.category = '{$category}'
                ");

                    while ($row = $resultsSql->fetch()) {

                        echo "
                        <tr> 
                        <td>{$row['runnerName']}
                         <td>CLUB</td>
                         <td>Jahrgang</td>
                         <td>Kanton</td>
                         <td>{$row['points']}</td>
                         </tr>
                         ";

                    }
                    ?>
                    <tr>
                        <?php
                        echo "
                    <form method='post' action='add_runner.php'>
                    <td><input type='text' name='runner_name'></td>
                    <td><input type='text' name='runner_club'></td>
                    <td><input type='text' name='runner_year'></td>
                    <td><input type='text' name='runner_canton'></td>
                    <td><input type='text' name='runner_points'></td>
                    <input style='display: none;' name='cup_id' value='$cupId'>
                    <input style='display: none;' name='race_id' value='$raceId'>
                    <input style='display: none;' name='runner_category' value='$category'>
                    <td><input type='submit' value='Add'></td>
                    </form>
                    "
                            ?>

                    </tr>
                </table>
            </div>
        </div>

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