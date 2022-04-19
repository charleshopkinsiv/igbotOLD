<?php


namespace IgBot\Account;

use \CharlesHopkinsIV\Core\Super\Mapper;


class AccountMapper extends Mapper
{

    private $accounts_file = __DIR__ . "/../../data/accounts.json";

    public function fetchAll()
    {

        $ACCOUNTS = [];

        if(file_exists($this->accounts_file))
            $DATA = json_decode(file_get_contents($this->accounts_file) ,1);
        if(empty($DATA)) 
            $DATA = [];

        foreach($DATA as $acct) {

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

        if(is_file($this->accounts_file))
            $ACCOUNTS = json_decode(file_get_contents($this->accounts_file) ,1);
        if(empty($ACCOUNTS))
            $ACCOUNTS = [];


        $ACCOUNTS[] = [
            'email' => $Account->getEmail(),
            'username' => $Account->getUsername(),
            'password' => $Account->getPassword(),
            'proxy' => $Account->getProxy(),
            'status' => $Account->getStatus()
        ];

        file_put_contents($this->accounts_file, json_encode($ACCOUNTS));
    }


    public function update(Account $Account)
    {

        if(is_file($this->accounts_file))
            $ACCOUNTS = json_decode(file_get_contents($this->accounts_file) ,1);
        if(empty($ACCOUNTS))
            $ACCOUNTS = [];

        foreach($ACCOUNTS as $key => $acc) {

            if($acc['username'] == $Account->getUsername()) {

                $ACCOUNTS[$key] = [
                    'email' => $Account->getEmail(),
                    'username' => $Account->getUsername(),
                    'password' => $Account->getPassword(),
                    'proxy' => $Account->getProxy(),
                    'status' => $Account->getStatus()
                ];
                break;
            }
        }

        file_put_contents($this->accounts_file, json_encode($ACCOUNTS));
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


    public function deleteByUsername(string $username)
    {

        if(is_file($this->accounts_file))
                $ACCOUNTS = json_decode(file_get_contents($this->accounts_file) ,1);

        if(empty($ACCOUNTS))
            $ACCOUNTS = [];

        foreach($ACCOUNTS as $key => $acc) {

            if($acc['username'] == $username) {

                unset($ACCOUNTS[$key]);
                break;
            }
        }

        file_put_contents($this->accounts_file, json_encode($ACCOUNTS));
    }
}