<?php



namespace IgBot\Scrapers;



class ScraperManager 
{

    public static $TYPES = [
        "User Followers"    => "\\IgBot\\Scrapers\\UserFollowersScraper",
        "Location"          => "\\IgBot\\Scrapers\\LocationScraper",
        "Location2L"        => "\\IgBot\\Scrapers\\LocationScraperTwoL"
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