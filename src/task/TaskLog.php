<?php



namespace igbot\task;



class TaskLog
{

    private static $instance;

    private function __construct()
    {

        $this->db = \core\util\Db::instance();
    }


    /**
     * Instance
     */
    public static function i()
    {

        if(empty(self::$instance)) 
            self::$instance = new self();

        return self::$instance;
    }


    public function logTask(Task $task)
    {


        $sql = "INSERT INTO task_log 
                SET account = '" . $task->getAccount()->getUsername() . "', 
                    type = '" . $task->getTaskType() . "',
                    details = '" . $task->getDetails() . "'";
        $this->db->query($sql)->execute();
    }

    /**
     * Task Days Back
     * Returns true if the task is on the log that many days back
     */
    public function taskDaysBack(Task $task, int $days_back = 0)
    {

        $sql = "SELECT * FROM task_log 
                WHERE account = '" . $task->getAccount()->getUsername() . "'  
                    AND type = '" . $task->getTaskType() . "' 
                    AND details = '" . $task->getDetails() . "' 
                    AND datetime >= DATE(DATE_SUB(NOW(), INTERVAL " . $days_back . " DAY))";

        return $this->db->query($sql)->single();
    }


    public function getRecentItems(int $count = 100)
    {

        $sql = "SELECT * FROM task_log
                ORDER BY datetime DESC 
                LIMIT " . $count;
        return $this->db->query($sql)->resultSet();
    }
}
