<?php




namespace igbot\task;
use \igbot\account\AccountDriver;


class TaskManager
{

    // Will provide the correct manager for the task type
    public static $TASK_TYPES = [
        "Action" => "\\igbot\\action\\ActionManager",
        "Scrape" => "\\igbot\\scrapers\\ScraperManager",
    ];


    public static function handleTask(Task $Task, AccountDriver $Driver)
    {

        return $Task->execute($Driver);
    }


    /**
     * Load Task
     * Will load the correct task object
     * 
     */
    public static function loadTask(array $TASK_DATA, string $account) : Task
    {

        // Figure out the task type and get manager
        $manager = new self::$TASK_TYPES[$TASK_DATA['task type']];


        // Find the specific task from the type
        $class = $manager::$TYPES[$TASK_DATA['type']];

        // Instantiate the class
        $task = new $class(
            \igbot\MapperFactory::getAccountMapper()->getByUsername($account),
            $TASK_DATA['type'],
            $TASK_DATA['details']
        );

        return $task;
    }


    /**
     * Task On Log
     * Will check if a task has already been executed
     *
     */
    public static function taskOnLog(Task $Task, int $days_back = 0)
    {

        // Load the log
        if(TaskLog::i()->taskDaysBack($Task, $days_back))
            return true;

        return false;
    }
}