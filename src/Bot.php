<?php

namespace IgBot;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use \IgBot\Queue\QueueManager;
use \IgBot\Task\TaskManager;
use \CharlesHopkinsIV\Core\Registry;
use \CharlesHopkinsIV\Core\Requests\CliRequest;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class Bot {

    private $QueueManager;
    private $keep_running = true;
    private int $sleep_time;
    private bool $debug;

    public function __construct() {

        $this->QueueManager     = new QueueManager();
        $this->sleep_time       = 60;
        $this->debug            = true;
        Registry::setDebug($this->debug);
    }


    public function run()
    {

        try {
                    
            // if(Registry::instance()->getRequest() instanceof CliRequest)
            //     define("CLI", true);
            // else define("CLI", false);

            while($this->keep_running) {

                if(isset($this->debug)) 
                    printf("%-'.32s\033[34mPopulating queue\033[39m\n", date("Y-m-d H:i:s"));
                $this->QueueManager->populateQueue();

                if(isset($this->debug)) 
                    printf("%-'.32s\033[34mHandling tasks\033[39m\n", date("Y-m-d H:i:s"));
                $this->QueueManager->handleAccountTasks();
                
                if(isset($this->debug)) 
                    printf("%-'.32s\033[94mSleeping for %s seconds\033[39m\n", date("Y-m-d H:i:s"), $this->sleep_time);

                sleep($this->sleep_time);
            }
        }
        catch(\Exception $e) { // Fatal errors

            $id = md5(strtotime("now"));
            printf("%-'.32s\033[31mError: %s - %s\033[39m\n", date("Y-m-d H:i:s"), $id, $e->getMessage() . $e->getFile() . $e->getLine() . $e->getTraceAsString());
            $log = new Logger("task");
            $log->pushHandler(new StreamHandler(TaskManager::$log_file), Logger::ERROR);
            $log->error($id . " - " . $e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
        }
    }
}
