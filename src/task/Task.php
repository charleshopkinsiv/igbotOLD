<?php




namespace igbot\task;

use igbot\account\Account;
use igbot\account\AccountDriver;


abstract class Task {

    protected $Account;
    protected $details;
    protected $task_type;

    public function __construct(Account $Account, string $details = "") {

        $this->Account = $Account;
        if(!empty($details))
            $this->details = $details;
    }


    public function getAccount()
    {

        return $this->Account;
    }


    public function getDetails()
    {

        return $this->details;
    }

    public function getTaskType()
    {

        return $this->task_type;
    }


    public abstract function execute(AccountDriver $Driver, string $details = "");
}