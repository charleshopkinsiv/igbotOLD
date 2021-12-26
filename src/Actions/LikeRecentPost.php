<?php


namespace igbot\Actions;

use igbot\account\AccountDriver;
use igbot\AccountDriverUtil;

class LikeRecentPost extends Action
{

    protected string $action_title = "Like Recent Post";
    protected string $action_description = "Will like a users recent post if not already liked.";

    public function execute(AccountDriver $Driver, string $details = "")
    {

        // AccountDriverUtil::login($Driver);
    }
}