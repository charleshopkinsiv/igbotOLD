<?php


namespace igbot\queue;

use \igbot\task\Task;
use \igbot\task\TaskLog;
use \igbot\task\TaskManager;
use \igbot\account\Account;
use \igbot\account\AccountManager;
use \igbot\account\AccountDriverManager;

class QueueManager 
{

    private $Mapper, $QUEUES;

    public function __construct()
    {

        $this->Mapper = \igbot\MapperFactory::getQueueMapper();        
        $this->QUEUES = $this->Mapper->getAccountQueues();
        $this->DriverManager = AccountDriverManager::instance();
    }


    /**
     * Populate Queue
     * Populates the queue for the next 7 days
     */
    public static function populateQueue() 
    {

        // Add all tasks from scrape routines
        $Scrape_Manager = new \igbot\scrapers\routine\ScrapeRoutineManager();
        $Scrape_Manager->populateQueue(new self);


        // Add all tasks from sequences
        // $Sequence_Manager = new \igbot\sequence\SequenceManager();
        // $Sequence_Manager->populateQueue();
    }


    /**
     * Already Added
     * Will check the log and live queue to make sure task hasn't already been added
     * 
     */
    public function alreadyAdded(Task $Task)
    {
    
        $QUEUE = [];
        if(isset($this->QUEUES[$Task->getAccount()->getUsername()]))
            $QUEUE = $this->QUEUES[$Task->getAccount()->getUsername()]->toArray();

        // See if item is already in account queue
        foreach($QUEUE as $QueueTask) {

            if($Task->getType() == $QueueTask->getType()
            && $Task->getDetails() == $QueueTask->getDetails())
                return true;
        }

        return false;
    }


    /**
     * Add Task
     * Will add an object implementing the Task interface to the queue
     */
    public function addTask(Task $Task)
    {

        if(empty($this->QUEUES[$Task->getAccount()->getUsername()]))
            $this->QUEUES[$Task->getAccount()->getUsername()] = new TaskQueue($Task->getAccount()->getUsername());

        $this->QUEUES[$Task->getAccount()->getUsername()]->push($Task);
        $this->Mapper->saveQueues($this->QUEUES);
    }


    /**
     * Handle Account Tasks
     * Will loop through each account that has tasks
     * And handle the tasks for each account
     */
    public function handleAccountTasks() 
    {

        if(empty($this->AccountManager))
            $this->AccountManager = new AccountManager();

        // Loop through all accounts with pending tasks
        foreach($this->QUEUES as $account => $Queue) {

            $Driver = $this->DriverManager->loadDriver($this->AccountManager->getByUsername($account));

            // Loop through all tasks for Accounts
            foreach($Queue->toArray() as $Task) {

                // Verify account hasn't hit send limit
                if(TaskManager::handleTask($Task, $Driver)) {
                 
                    $this->QUEUES[$account]->pop($Task);
                    $this->Mapper->saveQueues($this->QUEUES);
                }
            }
        }
    }

    public function getAccountsQueue(Account $Account)
    {

        return $this->Mapper->getAccountQueue($Account);
    }

    /**
     * Get Accounts With Tasks Due
     * Will return an array with all of the accounts that have tasks due
     * 
     */
    public function getAccountsWithTasksDue()
    {


    }
}