<?php

namespace IgBot;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;


class Bot {

    private $QueueManager;

    private $keep_running = true;

    public function __construct() {

        $this->QueueManager = new \igbot\queue\QueueManager();
    }


    /**
     * Run
     * This method will handle adding items to the queue, and handling current items
     * 
     */
    public function run()
    {

        try {
                    
            if(\core\Registry::instance()->getRequest() instanceof \core\requests\CliRequest)
                define("CLI", true);
            else define("CLI", false);

            while($this->keep_running) {

                if(!empty(CLI)) printf("\tPopulating queue. . .\n\n");
                $this->QueueManager->populateQueue();

                if(!empty(CLI)) printf("\tHandling tasks. . .\n\n");
                $this->QueueManager->handleAccountTasks();
                
                $sleep_time = mt_rand(60, 300);
                if(!empty(CLI)) printf("\tSleeping for %s seconds\n\n", $sleep_time);
                sleep($sleep_time);
            }
        }
        catch(\Exception $e) { // Fatal errors

            printf("\n\t%s\n\t%s\n\t%s\n\t%s\n", $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
        }
    }
}
