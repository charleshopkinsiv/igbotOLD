<?php


namespace IgBot\Scrapers;

use \IgBot\Task\Task;


abstract class Scraper extends Task
{
    
    protected $task_type = "Scrape";
}