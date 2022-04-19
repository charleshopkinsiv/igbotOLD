<?php


namespace IgBot\Actions;

use \IgBot\Account\AccountDriver;
use \IgBot\AccountDriverUtil;

class Login extends Action
{

    protected string $action_description = "This action handles logging in to the users account.";
    protected string $action_title = "Account Login";

    public function execute(AccountDriver $Driver, string $details = "")
    {

        AccountDriverUtil::login($Driver);
    }
}