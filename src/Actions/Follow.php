<?php


namespace IgBot\Actions;

use IgBot\Account\AccountDriver;
use IgBot\AccountDriverUtil;
use IgBot\Exceptions\BadUserException;

class Follow extends Action
{

    private static string $url_base                 = "https://instagram.com/";
    private static string $follow_btn_css_selector  = "div.XBGH5 button:first-of-type";

    protected string $action_title                  = "Follow User";
    protected string $action_description            = "This action will follow a users account.";


    public function execute(AccountDriver $Driver)
    {

        if($Driver->getDebug())
            printf("%32sFollowing user %s\n", "", $this->val_one);

        $Driver->getLimiter()->increment();
        $Driver->get(self::$url_base . $this->val_one . "/");
        
        try {

            $Driver->waitUntilCssSelector(self::$follow_btn_css_selector, 10);
            $Driver->click(self::$follow_btn_css_selector);   
        }

        catch(\Exception $e) {

            throw new BadUserException("Bad user");
        }
    }
}
