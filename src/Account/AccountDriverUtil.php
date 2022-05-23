<?php
///////////////////////////////////////////////////////////////////////////////////
//
//  Instagram Account Driver Utility
//  A central place for IG functionality and different strings for locators
//
///////////////////////////////////////////////////////////////////////////////////

namespace IgBot\Account;

use Facebook\WebDriver\Exception\DriverServerDiedException;

class AccountDriverUtil
{

    // Check Login
    private static $logged_in_landmark_css = "div.cq2ai > img.s4Iyt";

    // Login
    private static $login_url = "";
    private static $login_form_username_css = "#loginForm > div > div:nth-child(1) > div > label > input";
    private static $login_form_password_css = "#loginForm > div > div:nth-child(2) > div > label > input";
    private static $login_form_button_css = "#loginForm > div > div:nth-child(3) > button";
    

    // Logout button
    private static $logout_button_css = "";


    public static function checkLogin(AccountDriver $Driver)
    {

        try{

            $Driver->waitUntilCssSelector(self::$logged_in_landmark_css, 20);
            return $Driver->elementCssSelector(self::$logged_in_landmark_css);
        }
        catch(\Facebook\WebDriver\Exception\DriverServerDiedException $e) {

            exec("pkill chromedriver | pkill chromium-browse");
        }
        catch(\Exception $e) {}

        return false;
    }
}