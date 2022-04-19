<?php


namespace IgBot\Account;



class AccountManager
{

    private $mapper;

    public function __construct()
    {

        $this->mapper = \igbot\MapperFactory::getAccountMapper();
    }

    public function getAllAccounts()
    {

        return $this->mapper->fetchAll();
    }


    public function addAccountHttp()
    {

        $REQ_KEYS = ['name', 'password', 'proxy'];

        $DATA = [];

        foreach($REQ_KEYS as $key) {

            $DATA[$key] = $_POST[$key];
        }

        if(empty($DATA["name"]))
            return;

        $past_account = $this->getByUsername($DATA["name"]);


        if(empty($past_account)) {

            $this->mapper->insert(new Account(
                "",
                $DATA['name'],
                $DATA['password'],
                $DATA['proxy'],
                ""
            )); 
        }

        else {

            $past_account->setUsername($DATA['name']);
            $past_account->setProxy($DATA['proxy']);

            if(!empty($DATA['password']))
                $past_account->setPassword($DATA['password']);

            $this->mapper->update($past_account); 
        }
    }


    public function deleteAccountHttp()
    {
        
        if(!empty(\core\Registry::instance()->getRequest()->getProperty("patharg")[0]))
            $this->mapper->deleteByUsername(\core\Registry::instance()->getRequest()->getProperty("patharg")[0]);
    }


    public function getByUsername($username)
    {

        return $this->mapper->getByUsername($username);
    }
}