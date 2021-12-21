<?php

namespace igbot\user;


class IgUserMapper
{

    private int $limit;

    public static string $users_file = __DIR__ . "/../../data/users.json";
    private static string $users_img_dir = __DIR__ . "/../../../../../public/images/users/icons";

    public function __construct()
    {


    }

    public function insert(IgUser $User)
    {

        $USERS = $this->loadUsersArray();
        $USERS[$User->getUsername()] = $User->toArray();
        $this->saveUsers($USERS);
    }

    private function loadUsersArray() : array
    {

        $USERS = [];

        if(file_exists(self::$users_file))
            $USERS = (array)json_decode(file_get_contents(self::$users_file), 1);

        return $USERS;
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

    public function limit(int $limit)
    {

        $this->limit = $limit;
    }

    public function getCollection() : IgUserCollection
    {

        $Collection = new IgUserCollection();

        $USERS = json_decode(file_get_contents(self::$users_file), 1);

        $i = 0;
        foreach($USERS as $USER) {

            $Collection->add(new IgUser(
                $USER['username'],
                $USER['name'],
                $USER['description']
            ));

            $i++;
            if($i >= $this->limit) break;
        }

        return $Collection;
    }


    public static function userImageDir(string $username)
    {

        return self::$users_img_dir . "/" . substr($username, 0, 1);
    }
}