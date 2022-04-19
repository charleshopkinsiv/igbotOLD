<?php


namespace IgBot\Queue;

use \IgBot\Task\Task;
use \IgBot\Task\TaskLog;
use \IgBot\Task\TaskManager;
use \IgBot\Account\Account;
use \IgBot\Account\AccountManager;
use \IgBot\Account\AccountDriverManager;

class QueueManager 
{

    private $Mapper, $QUEUES;

    public function __construct()
    {

        $this->Mapper = \IgBot\MapperFactory::getQueueMapper();        
        $this->QUEUES = $this->Mapper->getAccountQueues();
        $this->DriverManager = AccountDriverManager::instance();

    }


    /**
     * Populate Queue
     * Populates the queue for the next 7 days
     */
    public function populateQueue() 
    {

        // Add all tasks from scrape routines
        $Scrape_Manager = new \IgBot\scrapers\routine\ScrapeRoutineManager();
        $Scrape_Manager->populateQueue($this);


        // Add all tasks from sequences
        $Sequence_Manager = new \IgBot\sequence\SequenceManager();
        $Sequence_Manager->populateQueue($this);
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

            if(get_class($Task) == get_class($QueueTask)
            && $Task->getDetails() == $QueueTask->getDetails()
            && $Task->getAccount() == $QueueTask->getAccount()
            && ($Task->getTaskType() != "Action" 
                || !$Task->requiresExtraInfo() 
                || $Task->getExtraInfo() == $QueueTask->getExtraInfo())
            )
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

            if($Queue->count() > 0)
                $Driver = $this->DriverManager->loadDriver($this->AccountManager->getByUsername($account));

            // Loop through all tasks for Accounts
            foreach($Queue->toArray() as $task_i => $Task) {

                if(TaskManager::handleTask($Task, $Driver)) {

                    $this->QUEUES[$account]->dequeue();
                    $this->Mapper->saveQueues($this->QUEUES);
                }
            }

            unset($Driver);
        }
    }

    public function getAccountQueues()
    {

        return $this->Mapper->getAccountQueues();
    }

    public function clearQueue()
    {

        $this->Mapper->clearQueue();
    }
}