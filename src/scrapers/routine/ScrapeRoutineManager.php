<?php



namespace igbot\scrapers\routine;

use \igbot\task\TaskManager;
use \igbot\queue\QueueManager;


class ScrapeRoutineManager
{

    private $mapper;

    public static $TYPES = [
        'User Followers' => '',
        'Location' => '',
        'Tag' => '',
        'User Likes' => '',
    ];

    public static $FREQUENCIES = [
        'Daily' => 0,
        'Weekly' => 7,
        'Thirty Days' => 30
    ];


    public function __construct()
    {

        $this->mapper = new ScrapeRoutineMapper();
    }


    public static function getTypes()
    {

        return self::$TYPES;
    }


    public static function getFrequencies()
    {

        return self::$FREQUENCIES;
    }


    public function getAllScrapeRoutines()
    {

        return $this->mapper->fetchAll();
    }


    public function editRoutineHttp()
    {

        $REQ_KEYS = ['id', 'account', 'type', 'details', 'frequency', 'sequence', 'status'];

        $DATA = [];

        foreach($REQ_KEYS as $key) {

            if(!isset($_POST[$key]))
                throw new \Exception("Missing required input " . $key);

            $DATA[$key] = $_POST[$key];
        }


        if(empty($DATA['id'])) {
                
            $this->mapper->insert(new ScrapeRoutine(
                $DATA['id'],
                $DATA['account'],
                $DATA['type'],
                $DATA['details'],
                $DATA['frequency'],
                $DATA['sequence'],
                $DATA['status']
            ));
        }
        else {

            $this->mapper->update(new ScrapeRoutine(
                $DATA['id'],
                $DATA['account'],
                $DATA['type'],
                $DATA['details'],
                $DATA['frequency'],
                $DATA['sequence'],
                $DATA['status']
            ));
        } 
    }


    public function deleteRoutineHttp()
    {

        if(!empty(\core\Registry::instance()->getRequest()->getProperty("patharg")[0])
        && is_numeric(\core\Registry::instance()->getRequest()->getProperty("patharg")[0])) {

            if($routine = $this->mapper->getById(\core\Registry::instance()->getRequest()->getProperty("patharg")[0])) {

                $this->mapper->delete($routine['id']);
            }

        }
    }


    /**
     * Populate Queue
     * Will load all of the routines, and add them for the account if it hasn't been sent yet
     * 
     */
    public function populateQueue(QueueManager $Queue_Manager) 
    {

        // Load all routines that are due
        $ROUTINES = $this->mapper->fetchAll();

        // Add the scrapes/tasks from the routines to the queue
        foreach($ROUTINES as $Routine) {

            // If already or inactive added skip
            if($Routine->getStatus() != "Active" 
            || $Queue_Manager->alreadyAdded($Routine->getTask())
            || TaskManager::taskOnLog($Routine->getTask(), self::$FREQUENCIES[$Routine->getFrequency()]))
                continue;

            $Queue_Manager->addTask($Routine->getTask());
            $this->debug($Routine);
        }
    }

    private function debug($Routine)
    {

        if(!empty(CLI)) 
            printf("\tAdding %s - %s to %s's queue\n\n", 
            get_class($Routine->getTask()),
            $Routine->getTask()->getDetails(),
            $Routine->getTask()->getAccount()->getUsername());
    }
}