<?php
/////////////////////////////////////////////////////////////////
//
//  Account Driver
//  Handles the instagram account utilizing Selenium web driver.
//  This driver has instagram functionality, data, account limits
//  tracks logged in open browser window. Saves and loads state 
//  from storage.
//
//////////////////////////////////////////////////////////////////


namespace IgBot\Account;
use \IgBot\Action\Login;
use \IgBot\Task\Task;
use \WebDriverLoader\ChromeDriverLoader;

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;


class AccountDriver
{

    private int     $login_retries;
    private bool    $logged_in;
    private string  $home_url;
    private bool    $debug;
    private array   $failed_tasks;
    private array   $cookies;

    private Account         $Account;
    private ChromeDriver    $WebDriver;
    private AccountLimiter  $Limiter;

    private     static  string  $data_folder        = __DIR__ . "/../../data";
    public      static  string  $default_home_url   = "https://www.instagram.com/";

    public function __construct(Account $Account, AccountDriverManager $DriverManager)
    {

        $this->login_retries            = 4;
        $this->Account                  = $Account;
        $this->Limiter                  = new AccountLimiter($this->Account);
        $this->home_url                 = self::$default_home_url;
        $this->debug                    = true;
        $this->failed_tasks             = [];
        $this->cookies                  = [];

        // Check to see if valid past driver was stored
        $this->data_file = self::$data_folder . "/account_drivers/" . md5($Account->getUsername());
        if(file_exists($this->data_file)
        && unserialize(file_get_contents($this->data_file)) instanceof AccountDriver)
            return unserialize(file_get_contents($this->data_file));

    }


    public function __destruct()
    {

        if(!empty($this->WebDriver)) {
            
            $this->webDriver()->quit();
            unset($this->WebDriver);
        }
        
        $this->saveState();
    }


    public function saveState()
    {

        unset($this->WebDriver);
        file_put_contents( // Save the object state
        $this->data_file,
        serialize($this));
    }


    public function getAccount() : Account
    {

        return $this->Account;
    }


    public function getDebug() : bool { return $this->debug; }


    public function getLimiter() : AccountLimiter { return $this->Limiter; }


    public function webDriver()
    {

        if(empty($this->WebDriver)) {

            $this->WebDriver = ChromeDriverLoader::load();
            $this->get($this->home_url);        
        }
        return $this->WebDriver;
    }


    public function getCookieFileLoc() : string
    {

        return __DIR__ . "/../../data/account_cookies/" . md5($this->getAccount()->getUsername());
    }

    public function loadCookies() : bool
    {

        if(empty($this->cookies)
        && is_file($this->getCookieFileLoc())) {

            $this->cookies = unserialize(
                file_get_contents($this->getCookieFileLoc())
            );

        }

        if(!empty($this->cookies)) {

            foreach($this->cookies as $cookie) {

                if(substr($cookie->getDomain(), 0, 1) == ".") // Throws error when period on beginning of domain
                    $cookie->setDomain(substr($cookie->getDomain(), 1, strlen($cookie->getDomain()) - 1));

                $this->webDriver()->manage()->addCookie($cookie);
            }
            return true;
        }
        
        return false;
    }


    public function saveCookies()
    {

        $this->cookies = [];
        $this->cookies = $this->webDriver()->manage()->getCookies();
        file_put_contents(
            $this->getCookieFileLoc(),
            serialize($this->cookies)
        );
    }


    public function checkLogin() // Will attempt to log in x # of times
    {
        
        while(!AccountDriverUtil::checkLogin($this)) {
            if(!empty($this->getDebug())) 
                printf("%32sLogging in : %s\n", "", $this->Account->getUsername());
                
            
            if(!AccountDriverUtil::cookieLogin($this))
                AccountDriverUtil::login($this);
            
            if(empty($counter)) $counter =  1; 
            else $counter++;

            if($counter > $this->login_retries)
                throw new Exception("Login failed after trying " . $this->$login_retries . " times");
        }

        return true;
    }


    public function get(String $url)
    {

        $this->webDriver()->get($url);
    }


    public function elementCssSelector(String $css_selector)
    {

        return $this->webDriver()->findElement(WebDriverBy::cssSelector($css_selector));
    }


    public function elementsCssSelector(String $css_selector)
    {

        return $this->webDriver()->findElements(WebDriverBy::cssSelector($css_selector));
    }


    public function fillInput(String $css_selector, String $value)
    {

        $this->elementCssSelector($css_selector)
            ->sendKeys($value);
    }


    public function click(String $css_selector)
    {

        $this->elementCssSelector($css_selector)
            ->click();
    }


    public function waitUntilCssSelector(String $css_selector, int $time = 30)
    {

        try{

            $this->webDriver()->wait($time)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector($css_selector))
            );        
        }
        catch(\Exception $e) {
            
            throw new \Exception("Css selector not found: " . $css_selector . " - \t" . $e->getMessage());
        }
    }

    
    public function waitUntilXpath(String $xpath)
    {

        try{

            $this->webDriver()->wait()->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath($xpath))
            );        
        }
        catch(\Exception $e) {

            throw new \Exception("Xpath not found " . $xpath . " " . $e->getMessage());
        }
    }


    public function screenshot(String $name = "screenshot")
    {

        $this->WebDriver()->takeScreenshot(__DIR__ . "/../../data/screenshots/" . $name . date("Y_m_d_H_i_s") . ".png");
    }


    public function failedTask(Task $task)
    {
        
        $cur_date = date("Y-m-d");

        if(array_key_first($this->failed_tasks) != $cur_date)
            $this->failed_tasks = [];

        $this->failed_tasks[$cur_date][] = $task;
    }


    public function checkFailToday(Task $task) : bool
    {

        $cur_date = date("Y-m-d");

        if(!empty($this->failed_tasks[$cur_date])) {

            foreach($this->failed_tasks[$cur_date] as $past_task) {

                if(get_class($past_task) == get_class($task)
                && $past_task->getDetails() == $task->getDetails()) {

                    return true;
                }
            }
        }

        return false;
    }
}

