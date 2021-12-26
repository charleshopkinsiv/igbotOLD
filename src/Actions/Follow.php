<?php


namespace igbot\Actions;

use igbot\account\AccountDriver;
use igbot\AccountDriverUtil;

class Follow extends Action
{

    protected string $action_title = "Follow User";
    protected string $action_description = "This action will follow a users account.";

    public function execute(AccountDriver $Driver, string $details = "")
    {

        // AccountDriverUtil::login($Driver);
    }
}