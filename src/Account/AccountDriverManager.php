<?php
//////////////////////////////////////////////////////////
//
//  Account Driver Manager
//  Handles the Instagram account driver loading, windows,
//  cookies, verifies accounts are logged in
//
//////////////////////////////////////////////////////////



namespace IgBot\Account;


class AccountDriverManager
{

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

        $driver = new AccountDriver($Account, $this);
        return $driver;
    }
}