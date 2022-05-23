<?php
namespace IgBot\Task;
use \IgBot\Account\AccountDriver;
use \IgBot\Account\AccountLimiter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use IgBot\Exceptions\BadUserException;
use IgBot\User\IgUserMapper;

class TaskManager
{

    // Will provide the correct manager for the task type
    public static $TASK_TYPES = [
        "Action" => "\\IgBot\\Action\\ActionManager",
        "Scrape" => "\\IgBot\\Scrapers\\ScraperManager",
    ];

    public static $log_file = __DIR__ . "/../../../../../data/logs/errors.log";

    public static function handleTask(Task $Task, AccountDriver $Driver)
    {

        try {

            if($Driver->getLimiter()->overLimit($Task)
            || $Driver->checkFailToday($Task))
                return false;

            if(!empty($Driver->getDebug()))  
                printf("%-'.32s\033[33m%s Left for day: %s | Left for hour: %s\033[39m\n", date("Y-m-d H:i:s"), $Driver->getLimiter()->getLimitType($Task), $Driver->getLimiter()->leftForDay($Task), $Driver->getLimiter()->leftForHour($Task));

            if(!empty($Driver->getDebug())) 
                printf("%-'.32s\033[32mHandling Task: %s - %s\033[39m\n", date("Y-m-d H:i:s"), $Task->getTaskType(), $Task->getDetails());

            $Driver->checkLogin();

            $Task->execute($Driver);
            self::logTask($Task);

            return true;
        }

        catch(LoginException $e) {

            throw $e;
        }

        catch(BadUserException $e) {

            $mapper = new IgUserMapper();
            if($user = $mapper->getByUsername($Task->getDetails())) {

                $mapper->remove($user);
            }

            return true;
        }

        //  CATCH DRIVER SERVER HAS DIED AND RESTART DRIVER

        // catch(\Throwable $e) {

        //     $id = md5(strtotime("now"));
        //     printf("%-'.32s\033[31mError: %s - %s - %s - %s\033[39m\n", date("Y-m-d H:i:s"), $id, $Task->getTaskType(), $Task->getDetails(), $e->getMessage());
        //     $log = new Logger("task");
        //     $log->pushHandler(new StreamHandler(self::$log_file), Logger::ERROR);
        //     $log->error($id . " - " . $Task->getTaskType() . " " . $Task->getDetails() . " - " . $e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
        //     $Driver->screenshot($id);
        // }
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