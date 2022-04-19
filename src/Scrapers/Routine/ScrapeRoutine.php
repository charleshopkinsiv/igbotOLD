<?php


namespace IgBot\Scrapers\Routine;


use \IgBot\Task\Task;
use \IgBot\Task\TaskManager;

class ScrapeRoutine 
{

    private $id, $account, $type, $details, $frequency, $sequence, $status;

    public function __construct($id, $account, $type, $details, $frequency, $sequence, $status)
    {

        $this->id =             $id;
        $this->account =        $account;
        $this->type =           $type;
        $this->details =        $details;
        $this->frequency =      $frequency;
        $this->sequence =       $sequence;
        $this->status =         $status;
    }

    
    public function getId() { return $this->id; }

    public function getAccount() { return $this->account; }
    
    public function getType() { return $this->type; }
    
    public function getDetails() { return $this->details; }
    
    public function getFrequency() { return $this->frequency; }

    public function getSequence() { return $this->sequence; }

    public function getStatus() { return $this->status; }

    public function getTask() : Task
    {

        $TASK = [
            "task type" => "Scrape",
            "type" => $this->type,
            "details" => $this->details,
        ];

        return TaskManager::loadTask($TASK, $this->account);
    }
}