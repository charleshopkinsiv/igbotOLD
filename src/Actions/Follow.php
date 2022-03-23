<?php


namespace igbot\Actions;

use igbot\account\AccountDriver;
use igbot\AccountDriverUtil;

class Follow extends Action
{

    private static string $url_base = "https://instagram.com/";
    private static string $follow_css_selector = "div.XBGH5 button:first-of-type";

    protected string $action_title = "Follow User";
    protected string $action_description = "This action will follow a users account.";

    public function execute(AccountDriver $Driver)
    {

        if($Driver->getDebug())
            printf("\tFollowing user %s\n", $this->details);

        $Driver->get(self::$url_base . $this->details . "/");
        $Driver->waitUntilCssSelector(self::$follow_css_selector, 10);
        $Driver->click(self::$follow_css_selector);
        $Driver->getLimiter()->increment();
    }
}