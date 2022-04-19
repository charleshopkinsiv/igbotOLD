<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Task Queue
//  Desc: This queue holds all of the tasks for users 


namespace IgBot\Queue;

use \IgBot\Task\Task;


class TaskQueue
{

    private $account, $QUEUE;

    public function __construct(string $account)
    {

        $this->account = $account;
        $this->QUEUE   = [];
    }

    
    public function push(Task $Task)
    {

        $this->QUEUE[] = $Task;
    }


    public function peek()
    {
        if(empty($this->QUEUE))
            return 0;
        else
            return $this->QUEUE[end(array_keys($this->QUEUE))];
    }


    public function dequeue()
    {

        return array_shift($this->QUEUE);
    }


    public function toArray()
    {
        
        return $this->QUEUE;
    }

    public function count() : int
    {

        return count($this->QUEUE);
    }
}

