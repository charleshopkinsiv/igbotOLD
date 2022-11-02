<?php
/*
    Action Parent Class

    - val_one = usernamee for action
    - Val_two = Description

*/


namespace IgBot\Actions;


use \IgBot\Task\Task;
use \IgBot\User\IgUser;


abstract class Action extends Task {

    protected           $task_type              = "Action";
    protected string    $action_title           = "";
    protected string    $action_description     = "";
    protected bool      $requires_extra_info    = false;
    protected string    $extra_info;
    protected IgUser    $User;


    public function addUser(IgUser $User) { $this->User = $User; }
    public function getUser() { return $this->User; }

    public function getValOne() { return $this->action_title; }
    public function getValTwo() { return $this->action_description; }

    // public function getTitle()          { return $this->action_title; }
    // public function getDescription()    { return $this->action_description; }

    public function requiresExtraInfo() : bool { return $this->requires_extra_info; }

    
    public function setExtraInfo(string $info) { $this->extra_info = $info; }
    public function getExtraInfo() : string { return $this->extra_info; }
}
