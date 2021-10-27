<?php


namespace igbot\actions;
use igbot\account\AccountDriver;
use igbot\AccountDriverUtil;

class Login extends Action
{

    public function execute(AccountDriver $Driver)
    {

        AccountDriverUtil::login($Driver);
    }
}