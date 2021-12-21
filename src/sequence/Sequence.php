<?php

namespace igbot\sequence;

use \igbot\account\Account;
use action\SequenceActionCollection;
use \igbot\user\IgUserCollection;


class Sequence
{

    private int $id;
    private Account $Account;
    private boolean $status;

    
    private SequenceActionMap $ActionCollection;
    private IgUserCollection $UserCollection;

    public function __construct(int $id, Account $Account)
    {

        $this->id      = $id;
        $this->Account = $Account;
    }

    public function getId()
    {

        return $this->id;
    }

    public function getAccount()
    {

        return $this->Account;
    }

    public function getTasksDue() : SequenceCollection
    {


    }
}
