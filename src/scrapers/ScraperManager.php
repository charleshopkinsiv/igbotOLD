<?php



namespace igbot\scrapers;



class ScraperManager 
{

    public static $TYPES = [
        "User Followers"    => "\\igbot\\scrapers\\UserFollowersScraper",
        "Location"          => "\\igbot\\scrapers\\LocationScraper"
    ];

    public function __construct()
    {


    }
}