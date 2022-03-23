<?php
///////////////////////////////////////////////////////////////////////////////////
//
//  Instagram Account Driver Utility
//  A central place for IG functionality and different strings for locators
//
///////////////////////////////////////////////////////////////////////////////////

namespace igbot\account;

use Facebook\WebDriver\Exception\DriverServerDiedException;

class AccountDriverUtil
{

    // Check Login
    private static $logged_in_landmark_css = "span._2dbep.qNELH > img._6q-tv";

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

            return $Driver->elementCssSelector(self::$logged_in_landmark_css);
        }
        catch(\Facebook\WebDriver\Exception\DriverServerDiedException $e) {

            exec("pkill chromedriver | pkill chromium-browse");
        }
        catch(\Exception $e) {

        }

        return false;
    }


    public static function login(AccountDriver $Driver)
    {

        $Driver->get(AccountDriver::$default_home_url);
        
        $Driver->waitUntilCssSelector(self::$login_form_username_css);
        $Driver->fillInput(self::$login_form_username_css, $Driver->getAccount()->getUsername());
        $Driver->fillInput(self::$login_form_password_css, $Driver->getAccount()->getPassword());
        $Driver->click(self::$login_form_button_css);
        $Driver->waitUntilCssSelector(self::$logged_in_landmark_css);
    }


    public static function logout(AccountDriver $Driver)
    {


    }
}