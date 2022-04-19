<?php



namespace IgBot\Scrapers;



class ScraperManager 
{

    public static $TYPES = [
        "User Followers"    => "\\igbot\\scrapers\\UserFollowersScraper",
        "Location"          => "\\igbot\\scrapers\\LocationScraper",
        "Location2L"        => "\\igbot\\scrapers\\LocationScraperTwoL"
    ];

    public function __construct()
    {


    }


    public function getScraperByTitle(string $title, $account, $details)
    {

        if(!empty(self::$TYPES[$title])) {

            return new self::$TYPES[$title]($account, $details);
        }
    }
}