<?php
class Song
{
    private int $song_id;
    private string $song_name;
    private string $artist;
    private ?string $cover_image;
    function __construct(int $id, string $name, string $artist, ?string $cover = null)
    {
        $this->song_id = $id;
        $this->song_name = $name;
        $this->artist = $artist;
        $this->cover_image = $cover;
    }

    // Get/Set
    #region 
    function getId(): int
    {
        return $this->getSongId();
    }
    function getName(): string
    {
        return $this->getSongName();
    }
    function getSongId(): int
    {
        return $this->song_id;
    }
    function setSongId(int $input)
    {
        $this->song_id = $input;
    }
    function getSongName(): string
    {
        return $this->song_name;
    }
    function setSongName(string $input)
    {
        $this->song_name = $input;
    }
    function getArtist(): string
    {
        return $this->artist;
    }
    function setArtist(string $input)
    {
        $this->artist = $input;
    }
    function getCover(): ?string
    {
        return $this->cover_image;
    }
    function setCover(string $input)
    {
        $this->cover_image = $input;
    }
    #endregion
}
