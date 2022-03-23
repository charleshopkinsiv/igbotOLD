<?php

namespace igbot\sequence;

use \igbot\queue\QueueManager;
use \igbot\account\AccountMapper;
use \igbot\ActionManager;
use \igbot\user\IgUserManager;
use \igbot\task\TaskManager;


class SequenceManager
{

    public static array $SEQUENCE_STATUSES = [
        'Active',
        'Inactive'
    ];

    private SequenceDataMapper $Mapper;

    public function __construct()
    {

        $this->Mapper = new SequenceDataMapper;
    }


    public function populateQueue(QueueManager $Queue_Manager)
    {
        
        foreach($this->Mapper->fetchAll() as $Sequence)
            foreach($Sequence->getTasksDue() as $Task) {

                if(! $Queue_Manager->alreadyAdded($Task)
                && ! TaskManager::taskOnLog($Task)) {

                    $Queue_Manager->addTask($Task);
                    if(!empty(CLI)) printf("\t\tAdding %s - %s to %s's queue\n\n", $Task->getTitle(), $Task->getDetails(), $Sequence->getAccount()->getUsername());
                }
            }
    }


    public function getAllSequences() : SequenceCollection
    {

        return $this->Mapper->fetchAll();
    }


    public function getSequenceById(int $id)
    {

        return $this->Mapper->getById($id);
    }


    public function editRoutineHttp()
    {

        
        $REQ_KEYS = ['id', 'account', 'status'];
        $OTHER_KEYS = ['DAYS_FROM_SIGNUP', 'ACTIONS', 'EXTRA_INFO'];

        $DATA = [];

        foreach($REQ_KEYS as $key)
            if(!isset($_POST[$key]))
                throw new \Exception("Missing required input " . $key);

        foreach(array_merge($REQ_KEYS, $OTHER_KEYS) as $key)
            if(isset($_POST[$key]))
                $DATA[$key] = $_POST[$key];
        
        $Account = new AccountMapper();
        $Account = $Account->getByUsername($DATA['account']);


        if($DATA['id']) {

            $Sequence = $this->getSequenceById($DATA['id']);    
            $Sequence->setAccount($Account);        
        }
        else {

            $Sequence = new Sequence(
                $this->Mapper->nextId(),
                $Account
            );
        }


        $ActionManager = new ActionManager();

        $Sequence->clearActions();

        if(isset($DATA['ACTIONS']))
            foreach($DATA['ACTIONS'] as $key => $action_name) {

                $Action = $ActionManager->getActionByTitle($action_name, $Account);
                if($Action->requiresExtraInfo())
                    $Action->setExtraInfo(array_shift($DATA['EXTRA_INFO']));

                $Sequence->addAction($DATA['DAYS_FROM_SIGNUP'][$key], $Action);
            }

        if(!empty($DATA['status'])) $Sequence->setStatus($DATA['status']);

        if(empty($DATA['id']))  
            $this->Mapper->insert($Sequence);
        else
            $this->Mapper->update($Sequence);
    }


    public function deleteRoutineHttp()
    {
        
        if(!empty(\core\Registry::instance()->getRequest()->getProperty("patharg")[0])
        && is_numeric(\core\Registry::instance()->getRequest()->getProperty("patharg")[0]))
            $this->Mapper->deleteById(\core\Registry::instance()->getRequest()->getProperty("patharg")[0]);
    }


    public function getActionsHttp()
    {

        $ACTIONS = [];
        $_POST = json_decode(file_get_contents("php://input"), 1);

        if(empty($_POST['id']))
            throw new \Exception("Missing data");

        $sequence_id = $_POST['id'];
        $ACTIONS_BY_DAYS = $this->Mapper->getById($sequence_id)->getActionsByDays();
        foreach($ACTIONS_BY_DAYS as $days_since_signup => $ACTIONS_FOR_DAY) {

            foreach($ACTIONS_FOR_DAY as $Action) {
                
                $NEXT_ACTION = [
                    'action_title' => $Action->getTitle(),
                    'days_after_signup' => $days_since_signup,
                ];

                if($Action->requiresExtraInfo())
                    $NEXT_ACTION['extra_info'] = $Action->getExtraInfo();
    
                $ACTIONS[] = $NEXT_ACTION;
            }
        }

        return $ACTIONS;
    }


    public function addUsersHttp()
    {

        $REQ_KEYS = ['id', 'USERS'];
        $DATA = [];
        $_POST = json_decode(file_get_contents("php://input"), 1);

        foreach($REQ_KEYS as $key) {
            if(!isset($_POST[$key])) {

                throw new \Exception("Missing required input " . $key);
            }

            $DATA[$key] = $_POST[$key];
        }

        $Sequence = $this->Mapper->getById($DATA['id']);

        $UserManager = new IgUserManager();

        foreach($DATA['USERS'] as $username) 
            $Sequence->addUser($UserManager->getByUsername($username));

        $this->Mapper->update($Sequence);
    }
}