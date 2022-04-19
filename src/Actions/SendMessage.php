<?php


namespace IgBot\Actions;
use \IgBot\Account\AccountDriver;
use \IgBot\AccountDriverUtil;

class SendMessage extends Action
{

    protected static string $message_url = "https://www.instagram.com/direct/new/";
    // protected static string $open_message_send_btn = "div._2NzhO.EQ1Mr > button > div > svg";
    protected static string $user_search_input = "div.TGYkm > div.hzPAB > div.HeuYH > input";
    protected static string $first_user_toggle = "div.qF0y9.Igw0E.IwRSH.eGOV_.vwCYk._3wFWr > div > div > div > button.wpO6b > div.QBdPU > svg";
    protected static string $next_button = "button.sqdOP.yWX7d.y3zKF.cB_4K";
    protected static string $message_input = "div.qF0y9.Igw0E.IwRSH.eGOV_.vwCYk.ItkAi > textarea";
    public static string $send_btn = "div.qF0y9.Igw0E.IwRSH.eGOV_._4EzTm.JI_ht > button";

    protected string $action_title = "Send Message";
    protected string $action_description = "This action will send a message to a user.";
    protected bool $requires_extra_info = true;

    public function execute(AccountDriver $Driver)
    {

        $Driver->get(self::$message_url);

        // $Driver->waitForCssSelector(self::$open_message_send_btn); // Open User select
        // $Driver->click(self::$open_message_send_btn);

        $Driver->waitUntilCssSelector(self::$user_search_input, 5); // Search for user
        $Driver->fillInput(self::$user_search_input, $this->details);

        $Driver->waitUntilCssSelector(self::$first_user_toggle); // Select First user
        $Driver->click(self::$first_user_toggle);

        $Driver->waitUntilCssSelector(self::$next_button); // Click next button
        $Driver->click(self::$next_button);

        $Driver->waitUntilCssSelector(self::$message_input); // Fill message input
        $Driver->fillInput(self::$message_input, $this->extra_info);

        $Driver->waitUntilCssSelector(self::$send_btn); // Send message
        $Driver->click(self::$send_btn);

        $Driver->getLimiter()->increment("message");
    }
}