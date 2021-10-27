<?php



namespace igbot\queue;


use \core\super\Mapper;
use \igbot\task\TaskManager;
use \igbot\queue\TaskQueue;
use \igbot\account\Account;


class QueueMapper extends Mapper 
{

    private $queue_file = __DIR__ . "/../../../../data/app/account_queues.json";
    private $QUEUES = [];

    /**
     * Get Account Queues
     * Will return an array with the account name as key and accounts task queue as the value
     */
    public function getAccountQueues(string $account_name = "")
    {

        if(empty($this->QUEUES)) {

            $this->loadQueues($account_name);
        }

        return $this->QUEUES;
    }


    public function loadQueues(string $account_name = "")
    {

        $DATA = json_decode(file_get_contents($this->queue_file), 1);

        // Instantiate a queue for each account and add tasks
        foreach($DATA as $account => $TASKS) {

            if(!empty($account_name))
                if($account != $account_name) continue;

            $Queue = new TaskQueue($account);
            foreach($TASKS as $TASK) {

                $Queue->push(TaskManager::loadTask($TASK, $account));
            }

            $this->QUEUES[$account] = $Queue;
        }
    }

    public function getAccountQueue(Account $Account) : TaskQueue
    {

        return $this->getAccountQueues($Account->getUsername())[$Account->getUsername()];
    }

    public function saveQueues(array $QUEUES)
    {

        $DATA = [];
        foreach($QUEUES as $account => $Queue) {

            foreach($Queue->toArray() as $Task) {

                $DATA[$account][] = [
                    "task type" => $Task->getTaskType(),
                    "type" => $Task->getType(),
                    "details" => $Task->getDetails(),
                ];
            }
        }

        file_put_contents($this->queue_file, json_encode($DATA));
    }
}