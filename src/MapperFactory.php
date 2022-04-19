<?php



namespace IgBot;


class MapperFactory
{

    private static $accountMapper, $scrapeRoutineMapper, $queueMapper;

    public static function getAccountMapper()
    {

        if(empty(self::$accountMapper)) {

            self::$accountMapper = new \IgBot\account\AccountMapper();
        }

        return self::$accountMapper;
    }

    public static function getScrapeRoutineMapper()
    {

        if(empty(self::$scrapeRoutineMapper)) {

            self::$scrapeRoutineMapper = new \IgBot\scrapers\routine\ScrapeRoutineMapper();
        }

        return self::$scrapeRoutineMapper;
    }

    public static function getQueueMapper()
    {

        if(empty(self::$queueMapper)) {

            self::$queueMapper = new \IgBot\queue\QueueMapper();
        }

        return self::$queueMapper;
    }
}