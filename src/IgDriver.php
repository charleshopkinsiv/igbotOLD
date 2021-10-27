<?php
////////////////////////////////////////////////////////////////////////////////////////
//
//  Instagram Driver
//  Uses the Selenium driver to navigate Instagram
//
////////////////////////////////////////////////////////////////////////////////////////


namespace igbot;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;



class IgDriver
{

    private $Account, $driver;

    private $headless = true;
    private $proxy_server = '';
    private $main_url = "https://instagram.com";
    private $user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36";
    
    // Login Info - Xpath (Should switch to CSS Selectors)
    private $login_page_element = '//*[@id="react-root"]/section/main/article/div[2]/div[1]/h1'; // Xpath to look out for 
    private $login_form_button = '//*[@id="loginForm"]/div/div[3]/button';
    private $password_form = '//*[@id="loginForm"]/div/div[2]/div/label/input';
    private $email_form = '//*[@id="loginForm"]/div/div[1]/div/label/input';
    private $successful_login_element = '//*[@id="react-root"]/section/nav/div[2]/div/div/div[1]/a/div/div/img';


    public function __construct(Account $Account)
    {

        $this->Account = $Account;

        // Init
        $this->cookie_file = __DIR__ . "/data/cookies/" . md5($Account->getEmail());

        // Prepare
        $this->driver = $this->prepareSelenium();

        if(!$this->login($Account)) throw Exception("Error logging in.");
    }


    /**
     * Prepare Selenium
     * Gets selenium ready to use (Make it work with both windows and linux)
     * 
     */
    private function prepareSelenium()
    {

        // Prepare Options
        $OPT_ARGS = [ 
            'window-size=1024,768',
            '--user-agent=' . $this->user_agent
        ];
        if(!empty($this->headless)) $OPT_ARGS[] = '--headless';
        if(!empty($this->proxy_server)) $OPT_ARGS[] = '--proxy-server:' . $this->proxy_server;
    
        $options = new ChromeOptions();
        $options->addArguments($OPT_ARGS);


        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability( ChromeOptions::CAPABILITY, $options );
        putenv('WEBDRIVER_CHROME_DRIVER=/var/chromedriver/chromedriver');


        return ChromeDriver::start( $capabilities );
    }



    /**
     * Login
     * Will login to the Instagram account
     */
    private function login(Account $Account) : void
    {

        //  Load Home
        $this->driver->get($this->url);


        //  Reload With Cookies
        if(is_file($this->cookie_file)) {

            $COOKIES = unserialize(file_get_contents($this->cookie_file));

            foreach($COOKIES as $cookie) {

                $this->driver->manage()->addCookie($cookie);
            }

            $this->driver->get($this->url);
        }


        if(!empty($this->driver->findElements(WebDriverBy::xpath($login_page_element)))) {

            // Check if home page was loaded / Successful cookie login


            // Else
            throw new Exception("Loaded a bad page after loading cookies.");
        }



        //  Handle Login Form
        $this->driver->findElement(WebDriverBy::xpath($this->email_form))
            ->sendKeys( $Account->get('email') );
            
        $this->driver->findElement(WebDriverBy::xpath($this->password_form))
            ->sendKeys( $Account->get('password') );

        $this->driver->findElement(WebDriverBy::xpath($this->login_form_button))
            ->click();


        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::xpath($this->successful_login_element)
            )
        );


        //  Handle the stay logged in page
        if(strpos($this->driver->getCurrentUrl(), 'https://www.instagram.com/accounts/onetap') !== false) {

            if($buttons = $this->driver->findElements(WebDriverBy::xpath('//*[@id="react-root"]/section/main/div/div/div/section/div/button'))) {

                $buttons[0]->click();
            }

            $this->driver->wait()->until(
                WebDriverExpectedCondition::urlIs('https://www.instagram.com/')
            );
        }


        //  If successful, Save cookies
        file_put_contents($this->cookie_file, serialize($this->driver->manage()->getCookies()));
    }


}