<?php


namespace IgBot\Actions;

use \IgBot\Account\AccountDriver;
use \IgBot\AccountDriverUtil;

class Login extends Action
{

    protected string $action_description = "This action handles logging in to the users account.";
    protected string $action_title = "Account Login";

    public function execute(AccountDriver $Driver)
    {


        $Driver->get(AccountDriver::$default_home_url);
        
        // Load cookies
        $Driver->loadCookies();
        $Driver->get(AccountDriver::$default_home_url);

        if(AccountDriverUtil::checkLogin($Driver)) {

            if($Driver->getDebug()) 
                printf("%32s\033[32mSuccessful cookie login - %s\033[39m\n", "", $Driver->getAccount()->getUsername());
            return true;
        }

        $Driver->waitUntilCssSelector(self::$login_form_username_css);
        $Driver->fillInput(self::$login_form_username_css, $Driver->getAccount()->getUsername());
        $Driver->fillInput(self::$login_form_password_css, $Driver->getAccount()->getPassword());
        $Driver->click(self::$login_form_button_css);

        if(AccountDriverUtil::checkLogin($Driver)) {

            if($Driver->getDebug()) 
                printf("%32sSuccessful login - %s\n", "", $Driver->getAccount()->getUsername());
                
            $Driver->saveCookies();

            return true;
        }

        return false;
    }
}