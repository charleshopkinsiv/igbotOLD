<?php


namespace igbot\Actions;
use igbot\account\AccountDriver;
use igbot\AccountDriverUtil;

class Login extends Action
{

    protected string $action_description = "This action handles logging in to the users account.";
    protected string $action_title = "Account Login";

    public function execute(AccountDriver $Driver)
    {

        AccountDriverUtil::login($Driver);
    }
}