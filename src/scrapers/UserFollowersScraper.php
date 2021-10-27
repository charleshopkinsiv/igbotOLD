<?php


namespace igbot\scrapers;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Interactions\WebDriverActions;
use igbot\account\AccountDriver;

class UserFollowersScraper extends Scraper {

    private $USERS;

    private static $max_users = 1000;
    private static $url_base = "https://www.instagram.com/";
    private static $followers_css_button = "#react-root > section > main > div > header > section > ul > li:nth-child(2) > a";
    private static $followers_modal_css_landmark = "body > div.RnEpo.Yx5HN > div > div > div:nth-child(1) > div > h1";
    private static $users_list_scroll_css = "body > div.RnEpo.Yx5HN > div > div > div.isgrP";
    private static $users_css = "ul.jSC57._6xe7A > div.PZuss > li";
    
    public function execute(AccountDriver $Driver) {

        $this->USERS    = [];
        $username       = $this->getDetails();
        $url            = self::$url_base . $username . "/";


        // Load Users Page
        $Driver->get($url);
        if($Driver->getDebug()) 
            printf("\n\tLoading %s\n\n", $url);

        // Open followers modal
        $Driver->waitUntilCssSelector(self::$followers_css_button);
        $Driver->click(self::$followers_css_button);
        $Driver->waitUntilCssSelector(self::$followers_modal_css_landmark);
        if($Driver->getDebug())
            printf("\n\t%s Profile loaded.\n\n", $username);

        $Driver->screenshot();

        // Scroll until user limit, or end
        $USERS = $this->collectUsers($Driver);
        while(!empty(count($USERS))
        && count($USERS) < self::$max_users 
        && count($USERS) < $this->collectUsers($Driver)) {

            $Driver->screenshot();

            $this->scrollFollowersModal($Driver);
            $USERS = $this->collectUsers($Driver);  // Find users that aren't already in array
            if($Driver->getDebug()) 
                printf("\n\tThere are %d users collected so far.\n\n", count($USERS));
        }

        // Process Users
        if(!empty(count($USERS))) {

            $this->saveUsers($USERS);
        }
    }


    public function collectUsers(AccountDriver $Driver)
    {

        return $Driver->elementsCssSelector(self::$users_css);
    }


    public function scrollFollowersModal(AccountDriver $Driver)
    {

        $Driver->webDriver()->executeScript("document.querySelector('" . self::$users_list_scroll_css . "').scrollBy(0, 1000)");
        sleep(3);
    }

    private function saveUsers(array $USERS)
    {

        foreach($USERS as $UserElements) {

            $this->UserManager->saveUserFromElement($UserElement);
        }
    }
}