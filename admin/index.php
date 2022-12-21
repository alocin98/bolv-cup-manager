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
<?php
ini_set('display_errors', 1);

require('../database.php');

$getCupsSql = $pdo->query("SELECT
    cups.season as season,
    cups.name as name,
    cups.id as id
    FROM cups");
$getCupsSql->execute();



echo 
"<table>
    <tr>
        <th>Saison</th>
        <th>Name</th>
        <th>Aktionen</th>
    </tr>";

while($row = $getCupsSql->fetch()) {
    echo
    "<tr>
        <td>{$row['season']}</td>
        <td>{$row['name']}</td>
        <td><a href='/nachwuchscup/admin/cup?id={$row['id']}'>Edit</a></td>
    </tr>";
}
echo
"</table>";

?>

<div class="grid">
<article class="not-so-tall">
    <h2>Neuer Cup</h2>
    <form method="post" action="add_cup.php">
    Saison<input type="text" name="season"><br>
    Name<input type="text" name="name"><br>
                </select>
    Kategorien <select name="categories[]" multiple>
                <?php
                $getCategoriesSql = $pdo->query("SELECT id, name FROM categories");
                $getCategoriesSql->execute();
                while($row = $getCategoriesSql->fetch()) {
                    echo "<option value='{$row['name']}'>{$row['name']}</option>";
                }
                ?>
                </select>
    Klubs <select name="clubs[]" multiple>
                <?php
                $getClubsSql = $pdo->query("SELECT id, name FROM clubs");
                $getClubsSql->execute();
                while($row = $getClubsSql->fetch()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
                </select>
    <button type="submit" value="Add" id="submit">send</button>
</form>   
</article> 

</div>
</div>

</body>

</html>