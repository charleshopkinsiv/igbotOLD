<?php


namespace igbot\scrapers;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use igbot\account\AccountDriver;

class LocationScraper extends Scraper {

    private $title = "Location Scraper";
    private $description = "Gathers users from the location feed";

    
    public function execute(AccountDriver $Driver) 
    {
        
        $this->USERS = [];

        //  Required Data
        $REQ = ['city'];


        //
        //  Load URL
        //
        $this->driver->get($this->returnUrl($DATA['city']));



        while(count($this->USERS) < 20) {

            //
            // Load Users from Image rows in feed
            //
            $recent = $this->driver->findElement(WebDriverBy::xpath('//*[@id="react-root"]/section/main/article/div[2]'));

            $IMG_ROWS = $recent->findElements(WebDriverBy::cssSelector('div.Nnq7C'));

            
            // Loop through each row and get data on users
            foreach($IMG_ROWS as $ROW) {

                echo "Row\n\n";


                //  Get each image on the row
                try {

                    $ITEMS = $ROW->findElements(WebDriverBy::cssSelector('div.v1Nh3'));
                } 
                catch(\Facebook\WebDriver\Exception\StaleElementReferenceException $e) {

                    echo $e->getMessage() . "\n\n";
                    break;
                }


                // Loop through each image on the row to extract data on the users
                foreach($ITEMS as $ITEM) {

                    echo "Item\n\n";

                    // Click the users image, to get data from popup
                    //  If it fails, reload
                    try {
                            
                        $ITEM->click();

                    } catch(\Facebook\WebDriver\Exception\ElementNotInteractableException $e) {

                        echo $e->getMessage() . "\n\n";
                        break;
                    }


                    // Verify that popup loaded
                    try{

                        $this->driver->wait()->until(
                            WebDriverExpectedCondition::presenceOfElementLocated(
                                WebDriverBy::xpath('/html/body/div[5]/div[2]/div/article/header/div[2]/div[1]/div[1]/span/a')
                            )
                        );

                    } catch(\Facebook\WebDriver\Exception\TimeoutException $e) {

                        echo $e->getMessage() . "\n\n";

                        $this->driver->findElement(WebDriverBy::xpath('/html/body/div[5]/div[3]/button'))->click();

                        break(2);

                    }


                    $this->Bot->screenshot(8);


                    //  Url / Username
                    $username = $this->driver->findElement(WebDriverBy::xpath('/html/body/div[5]/div[2]/div/article/header/div[2]/div[1]/div[1]/span/a'))->getText();

                    //  Skip if already in array
                    if(array_key_exists($username, $this->USERS)) {

                        echo "Already added\n\n";

                        $this->driver->findElement(WebDriverBy::xpath('/html/body/div[5]/div[3]/button'))->click();

                        continue;

                    }


                    //  Photo
                    $img = $this->driver->findElement(WebDriverBy::xpath('/html/body/div[5]/div[2]/div/article/header/div[1]/div/a/img'))->getAttribute('src');

                    $this->USERS[$username] = $img;

                    $this->driver->findElement(WebDriverBy::xpath('/html/body/div[5]/div[3]/button'))->click();

                }

            }

            echo count($this->USERS) .  " Scroll\n\n";

            $this->driver->executeScript('window.scrollBy(0, 2000);');

            $this->Bot->screenshot(99);

            sleep(5);

            $this->Bot->screenshot(98);

            $this->driver->executeScript('window.scrollBy(0, 2000);');

            sleep(5);

            $this->driver->executeScript('window.scrollBy(0, 2000);');

            sleep(5);
            
            $this->driver->executeScript('window.scrollBy(0, 2000);');

            sleep(5);

            $this->Bot->screenshot(9);

        }

        //  Add Users to DB and save Images
        $this->processUsers();

    }


    /**
     * Load URL
     * Loads the correct url for the city
     * 
     */
    public function returnUrl($city) {

        $url = '';

        switch($city) {

            case 'Los Angeles':
                $url = 'https://www.instagram.com/explore/locations/212999109/los-angeles-california/';
                break;

        }

        return $url;

    }


    /**
     * Process users
     * Add users to the DB and save their image
     * 
     */
    public function processUsers() {

        $mapper = new \igbot\user\IgUserMapper;

        print_r($this->USERS);

        foreach($this->USERS as $username => $img_url) {

            // $username = explode("/", $username)[3];

            if(VERBOSE) echo "\n\t\tAdding " . $username . "\n\n";

            $user = new \igbot\user\IgUser($username, []);

            $user->set('suspect', 1);

            $mapper->where("username = '" . $username . "'");

            // print_r($mapper->select());

            if(empty($mapper->select())) {

                if($mapper->insert($user)) {

                    //  Save Image
                    $img_bin = file_get_contents($img_url);

                    $file_name = __DIR__ . "/../data/images/user_avatars/" . $username . ".png";

                    file_put_contents($file_name, $img_bin);

                }

            }

        }

    }

}