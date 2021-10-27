<?php


namespace igbot\scrapers;

use \igbot\task\Task;


abstract class Scraper extends Task
{
    
    protected $task_type = "Scrape";
}