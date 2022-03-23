<?php



namespace igbot\queue;


use \core\super\Mapper;
use \igbot\task\TaskManager;
use \igbot\queue\TaskQueue;
use \igbot\account\Account;


class QueueMapper extends Mapper 
{

    private static $queue_file = __DIR__ . "/../../data/account_queues";
    private $QUEUES = []; // ["account" => TaskQueue, ...]

    public function getAccountQueues()
    {

        if(empty($this->QUEUES)) {

            $this->QUEUES = $this->loadQueues();
        }

        return $this->QUEUES;
    }


    public function loadQueues() // Optional: Provide account name to filter, or return all
    {

        if(file_exists(self::$queue_file))
            return unserialize(file_get_contents(self::$queue_file));
    }


    public function saveQueues(array $QUEUES)
    {

        file_put_contents(self::$queue_file, serialize($QUEUES));
    }

    public function clearQueue()
    {

        $this->saveQueues([]);
    }
}