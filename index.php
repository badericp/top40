<?php
require_once "SqlConnection.php";
require __DIR__ . "/SQL/Queries.php";
require __DIR__ . "/classes/Placing.php";
require __DIR__ . "/classes/Song.php";
// Open database connection
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

//old
#region
// Sort ascending by year and week
// usort($kwList, function ($a, $b) {
//     if ($a['year'] === $b['year']) {
//         return (int)$a['kw'] <=> (int)$b['kw'];
//     }
//     return (int)$a['year'] <=> (int)$b['year'];
// });

// $selectedKw = null;
// if (isset($_POST['kwYearDropDown']))
//     $selectedKw = $_POST['kwYearDropDown'];

// if ($selectedKw) {
//     [$year, $kw] = explode('-', $selectedKw);
//     $year = (int)$year;
//     $kw = (int)$kw;
// } else {
//     // Selected year/week from dropdown or fallback to latest        
//     $latest = end($kwList);
//     $year = (int)$latest['year'];
//     $kw = (int)$latest['kw'];
// }

// $titel = null;
// $interpret = null;

// if (isset($_FILES['coverFile']) && isset($_POST['entryId'])) {
//     // Check if the button was clicked
//     $coverData = file_get_contents($_FILES['coverFile']['tmp_name']);
//     $entryId = (int)$_POST['entryId'];

//     if ($coverData && $entryId) {
//         // Update DB
//         $stmt = $openDbConnection->prepare("UPDATE top40 set cover = ? WHERE platz = ?");
//         $stmt->send_long_data(0, $coverData);
//         $stmt->bind_param("si", $coverData, $entryId);
//         $stmt->execute();
//         $stmt->close();
//     }
// }

// // Fetch data for selected week
// $data = getData4KW($openDbConnection, $year, $kw, $kwList);

// // Get previous week label
// $prevWeekLabel = getPrevWeekLabel4Header($openDbConnection, $year, $kw);

// // Label for current week
// $selectedLabel = "KW" . str_pad($kw, 2, '0', STR_PAD_LEFT) . " / $year";

// // Show warning if no previous week exists
// $showWarning = hasNoPreviousWeek($openDbConnection, $year, $kw);
#endregion
?>

<!-- HTML starts here ------------->

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Top 40</title>
    <link href="stlyes.css" rel="stylesheet">
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