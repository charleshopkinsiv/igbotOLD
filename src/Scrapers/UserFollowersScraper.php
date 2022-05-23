<?php


namespace IgBot\Scrapers;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Interactions\WebDriverActions;
use \IgBot\Account\AccountDriver;
use \IgBot\User\IgUserManager;
use \IgBot\User\IgUser;

class UserFollowersScraper extends Scraper {

    private $USERS;
    private string $source;

    private static $max_users = 200;
    private static $url_base = "https://www.instagram.com/";

    // Follower button
    private static $followers_css_button = "#react-root > section > main > div > header > section > ul > li:nth-child(2) > a";
    private static $followers_modal_css_landmark = "body > div.RnEpo.Yx5HN > div > div > div:nth-child(1) > div > h1";

    // User list
    private static $users_list_scroll_css = "div.isgrP";
    private static $users_css = "ul > div.PZuss > li";

    // User
    private static $username_child_css = "div > div > span > a";
    private static $name_child_css = "div > div:nth-of-type(2) > div:nth-of-type(2)";
    private static $user_img_child_css = "img._6q-tv";
    
    public function execute(AccountDriver $Driver) {

        $this->USERS    = [];
        $this->Driver   = $Driver;
        $username       = $this->details;
        $url            = self::$url_base . $username . "/";
        

        if(empty($this->source))
            $this->source = "Users Followers: " . $username;

        // Load Users Page
        $Driver->get($url);
        if($Driver->getDebug()) 
            printf("%32sLoading %s\n", "", $url);

        $Driver->getLimiter()->increment();

        // Open followers modal
        try {

            $Driver->waitUntilCssSelector(self::$followers_css_button);
        }
        catch(\Exception $e) {

            return;
        }

        $Driver->click(self::$followers_css_button);
        if($Driver->getDebug())
            printf("%32sProfile loaded.\n", "", $username);

        $Driver->waitUntilCssSelector(self::$users_css);
        $USERS = $this->collectUsers($Driver);

        $previous_count = 0;

        while(!empty(count($USERS)) // Scroll until user limit, or end
        && count($USERS) < self::$max_users 
        && count($USERS) < $this->collectUsers($Driver)
        && count($USERS) != $previous_count) {

            $previous_count = count($USERS);

            $this->scrollFollowersModal($Driver);
            $USERS = $this->collectUsers($Driver);  // Find users that aren't already in array
            if($Driver->getDebug()) 
                printf("%32sthere are %d users collected so far.\n", "", count($USERS));

            $this->stuckOnTwelveErrorTest($USERS);
        }

        // Process Users
        if(!empty(count($USERS)))
            $this->saveUsers($USERS);
    }


    public function setSource(string $source)
    {

        $this->source = $source;
    }


    public function collectUsers(AccountDriver $Driver)
    {

        return $Driver->elementsCssSelector(self::$users_css);
    }


    public function scrollFollowersModal(AccountDriver $Driver)
    {

        $Driver->webDriver()->executeScript("document.querySelector('" . self::$users_list_scroll_css . "').scrollBy(0, document.querySelector('" . self::$users_list_scroll_css . "').scrollHeight - document.querySelector('" . self::$users_list_scroll_css . "').scrollTop);");
        sleep(3);
    }

    private function saveUsers(array $USERS)
    {

        $UserManager = new IgUserManager();

        foreach($USERS as $UserElement) {

            $username = $UserElement->findElement(WebDriverBy::cssSelector(self::$username_child_css))->getText();
            $name     = "";

            try {

                $name = $UserElement->findElement(WebDriverBy::cssSelector(self::$name_child_css))->getText();
            } catch(\Exception $e) {}

            $UserManager->saveUser(new IgUser(
                $username,
                $name,
                ""),
                $this->source
            );

            $UserManager->saveUserImage(
                $username,
                $UserElement->findElement(WebDriverBy::cssSelector(self::$user_img_child_css))->getAttribute("src")
            );
        }
    }

    private function stuckOnTwelveErrorTest($USERS)
    {

        if(count($USERS) == 12) {

            if(empty($this->twelve_count))
                $this->twelve_count = 0;

            $this->twelve_count++;

            if($this->twelve_count >= 5) {

                $this->Driver->screenshot();
                throw new \Exception("Stuck on 12 users, scroll not loading, taking screenshot");
            }
        }
    }
}