<?php



namespace igbot;

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

        /**
         * @todo 
         */

        try {
                    
            if(\core\Registry::instance()->getRequest() instanceof \core\requests\CliRequest)
                define("CLI", true);
            else define("CLI", false);

            while($this->keep_running) {

                if(CLI) printf("\tPopulating queue. . .\n\n");
                $this->QueueManager->populateQueue();

                if(!empty(CLI)) printf("\tHandling tasks. . .\n\n");
                $this->QueueManager->handleAccountTasks();
                
                $sleep_time = mt_rand(1800, 7200);
                if(!empty(CLI)) printf("\tSleeping for %s seconds\n\n", $sleep_time);
                sleep($sleep_time);
            }
        }
        catch(Exception $e) {

            // Log $e->getMessage
        }
    }
}