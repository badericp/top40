<?php
class Placing
{
    private int $place_id;
    private int $placing;
    private int $song_id;
    private DateTime $weekYear;
    const WEEK_FORMAT = 'o\WW';

    function __construct(int $id, int $placing, int $song_id, string $weekYear)
    {
        $this->place_id = $id;
        $this->placing = $placing;
        $this->song_id = $song_id;
        $this->weekYear = new DateTime($weekYear);
    }

    function getPlacingID(): int
    {
        return $this->place_id;
    }
    function setPlacingID(int $place_id): void
    {
        $this->place_id = $place_id;
    }
    function getPlacing(): int
    {
        return $this->placing;
    }
    function setPlacing(int $placing): void
    {
        $this->placing = $placing;
    }
    function getSongID(): int
    {
        return $this->song_id;
    }
    function setSongID(int $song_id): void
    {
        $this->song_id = $song_id;
    }
    function getDate(?string $format = null)
    {
        switch ($format) {
            case "year":
                return (int) $this->weekYear->format('o');
                break;
            case "week":
                return (int) $this->weekYear->format('W');
                break;
            default:
                return $this->weekYear->format(Placing::WEEK_FORMAT);
                break;
        }
    }
}
