<?php


namespace igbot\Actions;
use igbot\account\AccountDriver;
use igbot\AccountDriverUtil;

class SendMessage extends Action
{

    protected string $action_title = "Send Message";
    protected string $action_description = "This action will send a message to a user.";
    protected bool $requires_extra_info = true;

    public function execute(AccountDriver $Driver, string $details = "")
    {

        // AccountDriverUtil::login($Driver);
    }
}