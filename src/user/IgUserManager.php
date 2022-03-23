<?php

namespace igbot\user;

use \Ui\Ui;
use \Ui\Catalog;
use \Ui\CatalogItem;

class IgUserManager
{

    public static int $default_item_limit = 24;
    public static array $SORT_COLUMNS = [
        "Recent Added" => [
            "date_created" => "DESC",
        ],
        "Oldest Added" => [
            "date_created" => "ASC",
        ],
    ];

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


    public function getByUsername(string $username)
    {

        return $this->mapper->getByUsername($username);
    }


    public function getUsersUi(Ui $Ui) : IgUserCollection
    {

        $Collection = new IgUserCollection();

        $item_limit = self::$default_item_limit; // Items per page
        if(isset($_POST['item_limit']))
            $item_limit = $_POST['item_limit'];
        $Ui->setItemLimit($item_limit);

        $page = 1;
        if(isset($_POST['page'])) // Page number
            $page = $_POST['page'];
        $Ui->setPage($page);
        $offset = ($page - 1) * $item_limit;
        $this->mapper->limit($item_limit, $offset);

        $this->mapper->order("date_added");

        $Ui->setTotalItemCount($this->mapper->count()); // Total items for current query

        $Collection = $this->mapper->getCollection();

        return $Collection;
    }
}