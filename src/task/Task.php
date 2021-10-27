<?php




namespace igbot\task;

use igbot\account\Account;
use igbot\account\AccountDriver;


abstract class Task {

    protected $Account, $type, $details;
    protected $task_type;

    public function __construct(Account $Account, string $type, string $details) {

        $this->Account = $Account;
        $this->type = $type;
        $this->details = $details;
    }


    public function getAccount()
    {

        return $this->Account;
    }


    public function getType()
    {

        return $this->type;
    }


    public function getDetails()
    {

        return $this->details;
    }
    

    public function getTaskType()
    {

        return $this->task_type;
    }

    public abstract function execute(AccountDriver $Driver);
}