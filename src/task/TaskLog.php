<?php



namespace igbot\task;



class TaskLog
{

    private static $instance;
    public static $log_file = __DIR__ . "/../../../data/task_log.json";

    private $LOG;

    private function __construct()
    {

        $this->LOG = [];

        if(is_file(self::$log_file)) {
                
            $this->LOG = json_decode(file_get_contents(self::$log_file), 1);
            $this->LOG = $this->mapByDates($this->LOG);
        }
    }


    /**
     * Instance
     */
    public static function i()
    {

        if(empty(self::$instance)) 
            $instance = new self();

        return $instance;
    }


    /**
     * Task Days Back
     * Returns true if the task is on the log that many days back
     */
    public function taskDaysBack(Task $Task, int $days_back = 0)
    {

        // Get Starting Date
        $starting_date = date("Y-m-d", strtotime("-" . $days_back . " day"));

        // Check all of the logs after the starting date
        foreach($this->LOG as $date => $LOGS)
            if(strtotime($date) >= strtotime($starting_date))
                foreach($LOGS as $LOG)
                    if($LOG['account'] == $Task->getAccount()->getUsername()
                    || $LOG['type'] == $Task->getType()
                    || $LOG['details'] == $Task->getDetails())
                        return true;

        return false;
    }


    /**
     * Map By Dates
     * Will use dates as keys to create a map of arrays
     * 
     */
    private function mapByDates(array $LOG)
    {

        $DATE_MAP = [];
        foreach($LOG as $ENTRY)
            $DATE_MAP[date("Y-m-d", strtotime($ENTRY['datetime']))][] = $ENTRY;

        return $DATE_MAP;
    }
}