<?php




namespace IgBot\Task;

use \IgBot\Account\Account;
use \IgBot\Account\AccountDriver;


abstract class Task {

    protected $Account;
    protected $task_type;
    protected $val_one;
    protected $val_two;
    protected $val_three;

    
    public function __construct(Account $Account) {

        $this->Account = $Account;
    }


    public function getAccount() { return $this->Account; }


    public function setValOne(string $val) { $this->val_one = $val; }
    public function getValOne() { return $this->val_one; }


    public function setValTwo(string $val) { $this->val_two = $val; }
    public function getValTwo() { return $this->val_two; }


    public function setValThree(string $val) { $this->val_three = $val; }
    public function getValThree() { return $this->val_three; }


    public function getTaskType() { return $this->task_type; }


    public abstract function execute(AccountDriver $Driver);
}
