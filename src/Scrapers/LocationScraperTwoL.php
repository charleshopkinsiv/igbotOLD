<?php


namespace IgBot\scrapers;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Interactions\WebDriverActions;
use \IgBot\Account\AccountDriver;
use \IgBot\User\IgUserManager;
use \IgBot\User\IgUser;

class LocationScraperTwoL extends Scraper {

    private $USERS;
    private $USER_IMG;
    private int $max_users;

    private static $max_users_start = 25;
    private static $url_base = "https://www.instagram.com/explore/locations/";

    // Users
    private static $users_css           = "article.vY_QD > div:not([class]) > div > div.Nnq7C.weEfm > div.v1Nh3.kIKUG._bz0w";

    // User
    private static $username_child_css  = "span > a.sqdOP.yWX7d._8A5w5.ZIAjV";
    private static $name_child_css      = "div.ZyFrc > div > div.KL4Bh > img"; // Extract from img alt text
    private static $user_img_child_css  = "a._2dbep.qNELH.kIKUG > img";
    private static $user_close_btn      = "div.NOTWr > button.wpO6b > div.QBdPU > svg._8-yf5";
    
    public function execute(AccountDriver $Driver) {

        $this->USERS    = [];
        $this->Driver   = $Driver;
        $location       = $this->details;
        $url            = self::$url_base . $location;
        $this->max_users = self::$max_users_start;


        // Load Users Page
        $Driver->get($url);
        if($Driver->getDebug()) 
            printf("\n\tLoading %s\n\n", $url);

        // Open followers modal
        $Driver->waitUntilCssSelector(self::$users_css);
        if($Driver->getDebug())
            printf("\n\t%s Location loaded.\n\n", $location);


        $this->collectUsers($Driver);

        $previous_count = 0;

        while(!empty(count($this->USERS)) // Scroll until user limit, or end
        && count($this->USERS) < $this->max_users 
        && count($this->USERS) != $previous_count) {

            $previous_count = count($this->USERS);

            $this->scrollFollowersModal($Driver);
            $this->collectUsers($Driver);
            if($Driver->getDebug()) 
                printf("\n\tThere are %d users collected so far.\n\n", count($this->USERS));
        }

        // Process Users
        if(!empty(count($this->USERS)))
            $this->saveUsers($this->USERS);


        // Scrape users followers
        $this->scrapeUsersFollowers();
    }


    public function collectUsers(AccountDriver $Driver)
    {

        $raw_users = $Driver->elementsCssSelector(self::$users_css);

        $fail_count = 0;

        foreach($raw_users as $user_element) {

            try {

                $user_element->findElement(WebDriverBy::cssSelector("a"))->click();
            }
            catch(\Exception $e) {}
            


            try {

                $Driver->waitUntilCssSelector(self::$username_child_css);

                $username   = $Driver->elementCssSelector(self::$username_child_css)->getText();
                
            } catch(\Exception $e) {

                if($fail_count >= 2){

                    $this->max_users = 0;
                    return false;
                }

                $fail_count++;
                continue;
            }


            try {

                $Driver->waitUntilCssSelector(self::$name_child_css, 5);
                $name = $Driver->elementsCssSelector(self::$name_child_css)[0]->getAttribute("alt");
                if(strpos($name, "by") !== false)
                    $name = explode("by ", $name)[1];
                if(strpos($name, " in") !== false)
                    $name = explode(" in", $name)[0];

                $name = substr($name, 0, 64);
            } catch(\Exception $e) {}

            if(empty($name))
                $name = "";


            foreach($this->USERS as $past_user) { // If already added skip

                if($past_user->getUsername() == $username
                && $past_user->getName()     == $name) {

                    continue 2;
                }
            }


            $this->USERS[] = new IgUser(
                $username,
                $name
            );

            $this->USER_IMG[$username] = $Driver->elementCssSelector(self::$user_img_child_css)->getAttribute("src");

            try {

                $Driver->elementCssSelector(self::$user_close_btn)->click();
            }
            catch(\Exception $e) {}
        }
    }


    public function scrollFollowersModal(AccountDriver $Driver)
    {

        $Driver->webDriver()->executeScript("window.scrollTo(0, document.body.scrollHeight - window.innerHeight);");
        sleep(3);
    }

    private function saveUsers(array $USERS)
    {

        $UserManager = new IgUserManager();

        foreach($USERS as $user) {

            $UserManager->saveUser(
                $user,
                "Location: " . $this->details
            );

            $UserManager->saveUserImage(
                $user->getUsername(),
                $this->USER_IMG[$user->getUsername()]
            );
        }
    }


    public function scrapeUsersFollowers()
    {

        $error_count = 0;

        foreach($this->USERS as $user) {

            try {

                $scraper = new UserFollowersScraper($this->Driver->getAccount(), $user->getUsername());
                $scraper->setSource("Location: " . $this->details);
                $scraper->execute($this->Driver);
            }

            catch(\Exception $e) {

                if($error_count >= 5)
                    throw new \Exception("Max errors while going on second layer for LocationScraperTwoL.");

                $error_count++;
            }
        }
    }
}
