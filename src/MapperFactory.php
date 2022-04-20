<?php

namespace IgBot;

use \IgBot\Account\AccountMapper;
use \IgBot\Scrapers\Routine\ScrapeRoutineMapper;
use \IgBot\Queue\QueueMapper;


class MapperFactory
{

    private static $accountMapper, $scrapeRoutineMapper, $queueMapper;

    public static function getAccountMapper()
    {

        if(empty(self::$accountMapper)) {

            self::$accountMapper = new AccountMapper();
        }

        return self::$accountMapper;
    }

    public static function getScrapeRoutineMapper()
    {

        if(empty(self::$scrapeRoutineMapper)) {

            self::$scrapeRoutineMapper = new ScrapeRoutineMapper();
        }

        return self::$scrapeRoutineMapper;
    }

    public static function getQueueMapper()
    {

        if(empty(self::$queueMapper)) {

            self::$queueMapper = new QueueMapper();
        }

        return self::$queueMapper;
    }
}