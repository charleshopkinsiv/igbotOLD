<?php


namespace igbot\account;

use \core\super\Mapper;


class AccountMapper extends Mapper
{

    private $stub_file = __DIR__ . "/../../../../data/stubs/accounts.json";

    public function fetchAll()
    {

        $ACCOUNTS = [];

        foreach(json_decode(file_get_contents($this->stub_file) ,1) as $acct) {

            $ACCOUNTS[] = new Account(
                $acct['email'],
                $acct['username'],
                $acct['password'],
                $acct['proxy'],
                $acct['status']
            );
        }

        return $ACCOUNTS;
    }


    public function insert(Account $Account)
    {

        $ACCOUNTS = json_decode(file_get_contents($this->stub_file) ,1);

        $ACCOUNTS[] = [
            'email' => $Account->getEmail(),
            'username' => $Account->getUsername(),
            'password' => $Account->getPassword(),
            'proxy' => $Account->getProxy(),
            'status' => $Account->getStatus()
        ];

        file_put_contents($this->stub_file, json_encode($ACCOUNTS));
    }


    /**
     * Get By Username
     * Returns the Account with that username
     * 
     */
    public function getByUsername($username)
    {
        
        foreach($this->fetchAll() as $Account)
            if($Account->getUsername() == $username) 
                return $Account;

        return false;
    }
}