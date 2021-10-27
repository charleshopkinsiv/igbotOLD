<?php

namespace igbot\actions;


use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Follow implements Action {
    
    private $Bot, $driver;

    public function __construct(\igbot\IgBot $Bot) {

        $this->Bot = $Bot;

        $this->driver = $Bot->driver();

    }

    public function execute($DATA) {

        if(empty($DATA['username'])) {

            exit('must provide username');

        }


        //  Load users page
        $this->driver->get("https://instagram.com/" . $DATA['username'] . "/");

        $this->Bot->get("https://instagram.com/" . $DATA['username'] . "/");

        $this->Bot->screenshot('load user');

        //  Click follow button
        $follow_button_xpath = '//*[@id="react-root"]/section/main/div/header/section/div[1]/div[1]/div/div/div/span/span[1]/button';

        $this->driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::xpath($follow_button_xpath)
            )
        );


        $this->Bot->screenshot('follow');


        $this->driver->findElement(WebDriverBy::xpath($follow_button_xpath))->click();

    }

}