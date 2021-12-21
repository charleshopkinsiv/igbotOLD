<?php

namespace igbot\sequence;

use \igbot\queue\QueueManager;
use \igbot\account\AccountMapper;


class SequenceManager
{

    private SequenceDataMapper $Mapper;

    public function __construct()
    {

        $this->Mapper = new SequenceDataMapper;
    }


    public function populateQueue(QueueManager $Queue_Manager)
    {
        
        foreach($this->SequenceMapper->fetchAll() as $Sequence)
            foreach($Sequence->getTasksDue() as $Task)
                $Queue_Manager->addTask($Task);
    }


    public function getAllSequences() : SequenceCollection
    {

        return $this->Mapper->fetchAll();
    }


    public function editRoutineHttp()
    {

        
        $REQ_KEYS = ['account', 'status'];

        $DATA = [];

        foreach($REQ_KEYS as $key) {

            if(!isset($_POST[$key]))
                throw new \Exception("Missing required input " . $key);

            $DATA[$key] = $_POST[$key];
        }

        
        $Account = new AccountMapper();
        $Account = $Account->getByUsername($DATA['account']);
        if(empty($DATA['id'])) {
                
            $this->Mapper->insert(new Sequence(
                $this->Mapper->nextId(),
                $Account
            ));
        }
        else {

            $this->Mapper->update(new Sequence(
                $DATA['id'],
                $Account
            ));
        }
    }


    public function deleteRoutineHttp()
    {

        $this->Mapper->delete();
    }
}