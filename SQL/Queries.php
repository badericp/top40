<?php
//new
#region
class Builder
{
    private mysqli $conn;
    const SQL_DATE_FORMAT = 'Y-m-d';
    function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }
    function buildTableByDate(DateTimeInterface $date)
    {
        $sqlDate = $date->format(Builder::SQL_DATE_FORMAT);
        $query = "SELECT p.*, s.* 
        FROM placings p
        JOIN songs s ON s.song_id = p.song_id
        WHERE p.weekYear = ? 
        ORDER BY p.placing ASC
        LIMIT 40";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $sqlDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $placings = [];
        $songs = [];
        while ($row = $result->fetch_assoc()) {
            $placings[(int)$row['placing']] = new Placing((int)$row['place_id'], (int)$row['placing'], (int)$row['song_id'], $row['weekYear']);
            $songs[(int)$row['song_id']] = new Song((int)$row['song_id'], $row['song_name'], $row['artist'], $row['cover_image'] ?? null);
        }
        return ['placings' => $placings, 'songs' => $songs];
    }

    function buildKWlist(): ?array
    {
        $query = "SELECT DISTINCT weekYear FROM placings ORDER BY weekYear ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $return = [];
        while ($row = $result->fetch_assoc()) {
            $return[] = new DateTime($row['weekYear']);
        }
        return $return;
    }
    function buildKWdropdown(array $kwList)
    {
        $drop = '<select name="kwWeekSelect">';
        foreach ($kwList as $e) {
            $f = $e->format(Placing::WEEK_FORMAT);
            $drop .= '<option value="' . $f . '">' . $f . '</option>';
        }
        $drop .= '</select>';
        return $drop;
    }
    static function convertDate(string $seperator, string $weekYear, bool $immutable = false): DateTime
    {
        $immutable ? $a = new DateTimeImmutable() : $a = new DateTime();
        $date = explode($seperator, $weekYear);
        $a->setISODate($date[0], $date[1]);
        return $a;
    }
    static function getPrevWeek(array $haystack, DateTimeInterface $needle)
    {
        if ($needle instanceof DateTime) {
            $prevWeek = clone $needle;
            $prevWeek->modify("sunday last week");
        }
        $needle instanceof DateTimeImmutable ? $prevWeek = $needle->modify("sunday last week") : null;
        foreach ($haystack as $week) {
            if ($week <= $prevWeek) {
                $return = $week;
            }
        }
        return $return;
    }
}
#endregion
