<?php



namespace igbot;


class MapperFactory
{

    private static $accountMapper, $scrapeRoutineMapper, $queueMapper;

    public static function getAccountMapper()
    {

        if(empty(self::$accountMapper)) {

            self::$accountMapper = new \igbot\account\AccountMapper();
        }

        return self::$accountMapper;
    }

    public static function getScrapeRoutineMapper()
    {

        if(empty(self::$scrapeRoutineMapper)) {

            self::$scrapeRoutineMapper = new \igbot\scrapers\routine\ScrapeRoutineMapper();
        }

        return self::$scrapeRoutineMapper;
    }

    public static function getQueueMapper()
    {

        if(empty(self::$queueMapper)) {

            self::$queueMapper = new \igbot\queue\QueueMapper();
        }

        return self::$queueMapper;
    }
}