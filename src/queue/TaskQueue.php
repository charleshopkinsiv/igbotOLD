<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Task Queue
//  Desc: This queue holds all of the tasks for users 


namespace igbot\queue;

use \igbot\task\Task;


class TaskQueue
{

    private $account, $QUEUE;

    public function __construct(string $account)
    {

        $this->account = $account;
    }

    
    public function push(Task $Task)
    {

        $this->QUEUE[] = $Task;
    }


    public function peek()
    {

        return $this->QUEUE[end(array_keys($this->QUEUE))];
    }


    public function pop()
    {

        return array_pop($this->QUEUE);
    }


    public function toArray()
    {
        
        return $this->QUEUE;
    }
}

