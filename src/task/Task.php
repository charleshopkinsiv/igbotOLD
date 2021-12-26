<?php




namespace igbot\task;

use igbot\account\Account;
use igbot\account\AccountDriver;


abstract class Task {

    protected $Account;
    protected $task_type;

    public function __construct(Account $Account, string $type = "") {

        $this->Account = $Account;
        if(!empty($type))
            $this->task_type = $type;
    }


    public function getAccount()
    {

        return $this->Account;
    }


    public function getTaskType()
    {

        return $this->task_type;
    }


    public abstract function execute(AccountDriver $Driver, string $details = "");
}