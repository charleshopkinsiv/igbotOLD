<?php


namespace igbot\account;



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

        $REQ_KEYS = ['name', 'email', 'password', 'proxy', 'status'];

        $DATA = [];

        foreach($REQ_KEYS as $key) {

            $DATA[$key] = $_POST[$key];
        }

        $this->mapper->insert(new Account(
            $DATA['email'],
            $DATA['name'],
            $DATA['password'],
            $DATA['proxy'],
            $DATA['status']
        )); 
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