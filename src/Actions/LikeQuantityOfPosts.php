<?php


namespace IgBot\Actions;

use \IgBot\Account\AccountDriver;
use \IgBot\AccountDriverUtil;

class LikeQuantityOfPosts extends Action
{

    protected string $action_title = "Like a Quantity of Posts";
    protected string $action_description = "Will like a certian quantity of posts.";
    protected bool $requires_extra_info = true;

    public function execute(AccountDriver $Driver, string $details = "")
    {

        // AccountDriverUtil::login($Driver);
    }
}