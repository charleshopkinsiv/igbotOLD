<?php

namespace IgBot\Sequence;

use \IgBot\Account\Account;
use Action\SequenceActionCollection;
use \IgBot\User\IgUserCollection;
use \IgBot\User\IgUser;
use \IgBot\Actions\Action;


class Sequence
{

    private int $id;
    private Account $Account;
    private string $status;

    
    private $ACTIONS;
    private $USERS;

    public function __construct(int $id, Account $Account)
    {

        $this->id      = $id;
        $this->Account = $Account;
        $this->status  = SequenceManager::$SEQUENCE_STATUSES[0];
        $this->ACTIONS = [];
        $this->USERS   = [];
    }


    public function getId()
    {

        return $this->id;
    }


    public function setAccount(Account $account)
    {

        $this->Account = $account;
    }


    public function getAccount()
    {

        return $this->Account;
    }


    public function setStatus(string $status)
    {

        if(in_array($status, SequenceManager::$SEQUENCE_STATUSES))
            $this->status = $status;
        else
            throw new Exception("Not a valid status");
    }


    public function getStatus()
    {

        return $this->status;
    }


    public function getActionCount() : int
    {

        return count($this->ACTIONS);
    }


    public function getDuration() : int
    {

        $duration = !empty($this->ACTIONS) ? array_key_last($this->ACTIONS) : 0;
        return $duration;
    }


    public function getUserCount() : int
    {

        $user_count = 0;
        foreach($this->USERS as $USER_LIST)
            foreach($USER_LIST as $User)
                $user_count++;

        return $user_count;
    }


    public function getTasksDue()
    {

        $TASKS_DUE = [];

        // Get all signup dates that are ready for actions
            // Foreach Date, load users who signed up and iterate
            // Check log if user has already been sent
            // Check Task Queue to see if item is already added
            // Add to TaskCollection to return
        $DATES_DUE = [];
        foreach($this->ACTIONS as $days_from_signup => $Action) 
            $DATES_DUE[date("Y-m-d", strtotime("-" . $days_from_signup . " days"))] = $Action;

        foreach($DATES_DUE as $date => $ACTIONS) {
            foreach($ACTIONS as $Action) {

                if(!empty($this->USERS[$date])) {
                    foreach($this->USERS[$date] as $User) {

                        $class_name = get_class($Action);
                        $NewAction = new $class_name($this->Account, $User->getUsername());

                        if($Action->requiresExtraInfo())
                            $NewAction->setExtraInfo($Action->getExtraInfo());

                        $TASKS_DUE[] = $NewAction;
                    }
                }
            }
        }
        
        return $TASKS_DUE;
    }


    public function addUser(IgUser $User)
    {

        foreach($this->USERS as $UserList) // Don't add user if already in sequence
            foreach($UserList as $UserFromList)
                if($UserFromList->getUsername() == $User->getUsername())
                    return;

        $this->USERS[date("Y-m-d")][] = $User;
    }


    public function getUsers() : array
    {

        return $this->USERS;
    }

    
    public function addAction(int $days_from_signup, Action $Action) 
    {

        $this->ACTIONS[$days_from_signup][] = $Action;
        ksort($this->ACTIONS);
    }

    public function addActionIfNotAlready(int $days_from_signup, Action $Action) 
    {

        if(! $this->checkAction($days_from_signup, $Action)) {

            $this->addAction($days_from_signup, $Action);
        }
    }

    public function checkAction(int $days_from_signup, Action $Action)
    {

        foreach($this->ACTIONS as $days_f_su => $ACTS) {

            foreach($ACTS as $act) {

                if(get_class($Action) == get_class($Action)
                && $Action->getDetails() == $act->getDetails()) {

                    return $Action;
                }
            }
        }
    }

    public function clearActions()
    {

        $this->ACTIONS = [];
    }

    public function getActionsByDays()
    {

        return $this->ACTIONS;
    }
}
