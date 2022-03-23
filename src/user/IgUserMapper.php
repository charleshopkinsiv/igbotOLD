<?php

namespace igbot\user;

use \core\super\Mapper;


class IgUserMapper extends Mapper
{

    private int $limit;
    private int $count;

    public static string $users_file = __DIR__ . "/../../data/users.json";
    private static string $users_img_dir = __DIR__ . "/../../../../../public/images/users/icons";

    public function __construct()
    {
        $this->table = "users";
        $this->count = 0;
        parent::__construct();
    }


    public function insert(IgUser $User)
    {

        $sql = "INSERT INTO " . $this->table . 
                    " SET username = '" . addslashes($User->getUsername()) . "', 
                        name = '" . addslashes($User->getName()) . "',
                        description = '" . addslashes($User->getDescription()) . "',
                        scraper = " . addslashes($User->getScraper());
        $this->db->query($sql)->execute();
    }


    private function loadUsersArray() : array
    {

        $USERS = [];

        foreach($this->fetchAll() as $USER) {

            $USERS[] = [
                'name'          => $USER['name'],
                'username'      => $USER['username'],
                'description'   => $USER['description'],
                'scraper'       => $USER['scraper']
            ];
        }

        return $USERS;
    }


    public function getByUsername(string $username)
    {

        $sql = "SELECT * FROM " . $this->table . " WHERE username = '" . $username . "'";
        $USER = $this->db->query($sql)->single();

        if(!empty($USER))
            return new IgUser(
                $USER['username'],
                $USER['name'],
                $USER['description'],
                $USER['scraper']
            );
    }


    private function saveUsers(array $USERS)
    {

        file_put_contents(self::$users_file, json_encode($USERS));
    }


    public function saveUserImage(string $username, $img_binary)
    {
        $username_img_dir = self::userImageDir($username);
        

        if(!file_exists($username_img_dir)) // Create directory if it doesn't exist
            mkdir($username_img_dir, 0777, true);

        file_put_contents($username_img_dir . "/" . $username . ".jpg", $img_binary);
    }


    public function getCollection() : IgUserCollection
    {

        $Collection = new IgUserCollection();

        $USERS = $this->loadUsersArray();
        $this->count = count($USERS);

        foreach($USERS as $USER) {

            $Collection->add(new IgUser(
                $USER['username'],
                $USER['name'],
                $USER['description'],
                $USER['scraper']
            ));
        }

        return $Collection;
    }


    public static function userImageDir(string $username)
    {

        return self::$users_img_dir . "/" . substr($username, 0, 1);
    }
}