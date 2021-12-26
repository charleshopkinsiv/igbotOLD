<?php

namespace igbot\user;

use \Ui\Ui;
use \Ui\Catalog;
use \Ui\CatalogItem;

class IgUserManager
{

    public static int $default_item_limit = 24;

    private IgUserMapper $mapper;

    public function __construct() 
    {

        $this->mapper = new IgUserMapper();
    }


    public function saveUser(IgUser $User)
    {

        $this->mapper->insert($User);
    }

    public function saveUserImage(string $username, string $img_location)
    {

        $img_binary = file_get_contents($img_location);

        $this->mapper->saveUserImage($username, $img_binary);
    }

    public function loadCatalog(Ui $Ui) : Catalog
    {

        $Collection = $this->getUsersUi($Ui);
        
        foreach($Collection as $User) {

            $Ui->catalog()->add(new CatalogItem(
                $User->getUsername(),
                $User->getImageUrl()
            ));
        }
        
        return $Ui->catalog();
    }

    public function getUsersUi(Ui $Ui) : IgUserCollection
    {

        $Collection = new IgUserCollection();

        $item_limit = self::$default_item_limit;
        if(!empty($_POST['item_limit']))
            $item_limit = $_POST['item_limit'];

        $this->mapper->limit($item_limit);
        $Ui->setItemLimit($item_limit);

        $Ui->setTotalItemCount($this->mapper->count());

        $Collection = $this->mapper->getCollection();

        return $Collection;
    }
}