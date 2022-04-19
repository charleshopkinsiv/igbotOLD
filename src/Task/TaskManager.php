<?php




namespace IgBot\Task;
use \IgBot\Account\AccountDriver;
use \IgBot\Account\AccountLimiter;


class TaskManager
{

    // Will provide the correct manager for the task type
    public static $TASK_TYPES = [
        "Action" => "\\igbot\\action\\ActionManager",
        "Scrape" => "\\igbot\\scrapers\\ScraperManager",
    ];


    public static function handleTask(Task $Task, AccountDriver $Driver)
    {

        try {

            if($Driver->getLimiter()->overLimit($Task)
            || $Driver->checkFailToday($Task))
                return false;

            if(!empty($Driver->getDebug()))  
                printf("\tLeft for day: %s | Left for hour: %s\n\n", $Driver->getLimiter()->leftForDay($Task), $Driver->getLimiter()->leftForHour($Task));

            if(!empty($Driver->getDebug())) 
                printf("\tHandling Task: %s - %s\n\n", $Task->getTaskType(), $Task->getDetails());

            $Driver->checkLogin();

            $Task->execute($Driver);
            self::logTask($Task);

            return true;
        }

        catch(\Throwable $e) {

            throw $e;
        }
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
            $TASK_DATA['details']
        );

        return $task;
    }


    public static function logTask(Task $task)
    {

        TaskLog::i()->logTask($task);
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