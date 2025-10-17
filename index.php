<?php
require_once "SqlConnection.php";
require __DIR__ . "/SQL/Queries.php";
require __DIR__ . "/classes/Placing.php";
require __DIR__ . "/classes/Song.php";
$openDbConnection = getSqlConnection();

$KWselect = $_POST['kwWeekSelect'] ?? "2025W02";
$KWdateTime = Builder::convertDate("W", $KWselect);
$builder = new Builder($openDbConnection);
$kwList = $builder->buildKWlist();
$kwDropdrown = $builder->buildKWdropdown($kwList);
$prevWeek = Builder::getPrevWeek($kwList, $KWdateTime);
$curWeekArr = $builder->buildTableByDate($KWdateTime);
$prevWeekArr = $builder->buildTableByDate($prevWeek);

$curSongs = $curWeekArr['songs'];
$curPlacings = $curWeekArr['placings'];
$prevSongs = $prevWeekArr['songs'];
$prevPlacings = $prevWeekArr['placings'];

$table = [];
for ($i = 1; $i <= count($curPlacings); $i++) {
    $dif = 0;
    $prevPlace = 0;
    $curSongID = $curPlacings[$i]->getSongID();
    $prevSongID = $prevPlacings[$i]->getSongID();
    $curPlace = $curPlacings[$i]->getPlacing();
    $curSong = $curSongs[$curSongID];
    if (!isset($prevSongs[$curSongID])) {
        $dif = "new!";
        $prevPlace = "new!";
    } elseif ($curSongID != $prevSongID) {
        foreach ($prevPlacings as $prPl) {
            $prPl->getSongID() == $curSongID ? $prevPlace = $prPl->getPlacing() : null;
        }
        $dif = $curPlace - $prevPlace;
    }

    $base64 = base64_encode($curSong->getCover());
    $curSong->getCover() ? $base64 = base64_encode($curSong->getCover()) : $base64 = null;
    $coverHTML = '<img src="data:image/avif;base64,' . $base64 . '" alt="Cover" width="100">';

    $table[$i] = "<tr>
    <td>{$curPlace}</td>
    <td>{$curSong->getName()}</td>
    <td>{$curSong->getArtist()}</td>
    <td>{$prevPlace}</td>
    <td>{$dif}</td>
    <td>{$coverHTML}</td>
    </tr>";
}

?>

<!-- HTML starts here ------------->

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Top 40</title>
    <link href="styles.css" rel="stylesheet">
</head>

<!-- php-Teile des bodys in requires auslagern-->

<body>
    <div>
        <form action="#" method="post">
            <?= $kwDropdrown ?>
            <button type="submit">OK</button>
        </form>
    </div>
    <table>
        <tr>
            <th>Place</th>
            <th>Name</th>
            <th>Artist</th>
            <th>Prev. Place</th>
            <th>Dif.</th>
            <th>Cover</th>
        </tr>
        <?php
        foreach ($table as $row) {
            echo $row;
        }
        ?>
    </table>
</body>

</html>