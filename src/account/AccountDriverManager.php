<?php
//////////////////////////////////////////////////////////
//
//  Account Driver Manager
//  Handles the Instagram account driver loading, windows,
//  cookies, verifies accounts are logged in
//
//////////////////////////////////////////////////////////



namespace igbot\account;


class AccountDriverManager
{

    private array $DRIVERS = [];

    private static self $instance;

    public function __construct()
    {


    }


    public static function instance(): self
    {

        if(empty($instance))
            $instance = new self();

        return $instance;
    }


    public function loadDriver(Account $Account)
    {

        if(empty($DRIVERS[$Account->getUsername()]))
            $DRIVERS[$Account->getUsername()] = new AccountDriver($Account, $this);

        $DRIVERS[$Account->getUsername()]->checkLogin();

        return $DRIVERS[$Account->getUsername()];
    }
}