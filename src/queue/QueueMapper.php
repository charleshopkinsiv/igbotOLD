<?php



namespace igbot\queue;


use \core\super\Mapper;
use \igbot\task\TaskManager;
use \igbot\queue\TaskQueue;
use \igbot\account\Account;


class QueueMapper extends Mapper 
{

    private static $queue_file = __DIR__ . "/../../data/account_queues";
    private $QUEUES = [];

    /**
     * Get Account Queues
     * Will return an array with the account name as key and accounts task queue as the value
     */
    public function getAccountQueues()
    {

        if(empty($this->QUEUES)) {

            $this->QUEUES = $this->loadQueues();
        }

        return $this->QUEUES;
    }


    public function loadQueues() // Optional: Provide account name to filter, or return all
    {

        // if(file_exists($this->queue_file))
        //     $DATA = json_decode(file_get_contents($this->queue_file), 1);
        // if(empty($DATA))
        //     $DATA = [];

        // // Instantiate a queue for each account and add tasks
        // foreach($DATA as $account => $TASKS) {

        //     if(!empty($account_name))
        //         if($account != $account_name) continue;

        //     $Queue = new TaskQueue($account);
        //     foreach($TASKS as $TASK) {

        //         $Queue->push(TaskManager::loadTask($TASK, $account));
        //     }

        //     $this->QUEUES[$account] = $Queue;

        if(file_exists(self::$queue_file))
            return unserialize(file_get_contents(self::$queue_file));
    }

    // public function getAccountQueue(Account $Account) : TaskQueue
    // {

    //     return $this->getAccountQueues($Account->getUsername())[$Account->getUsername()];
    // }

    public function saveQueues(array $QUEUES)
    {

        // $DATA = [];
        // foreach($QUEUES as $account => $Queue) {

        //     foreach($Queue->toArray() as $Task) {

        //         $DATA[$account][] = [
        //             "task type" => $Task->getTaskType(),
        //             "details" => $Task->getDetails(),
        //         ];
        //     }
        // }   

        // file_put_contents($this->queue_file, json_encode($DATA));
        file_put_contents(self::$queue_file, serialize($QUEUES));
    }
}