<?php


namespace IgBot;

use \IgBot\Account\Account;


class ActionManager
{

    public static string $action_dir = __DIR__ . "/Actions";
    public static string $action_parent_class = "Action.php";
    public static string $actions_namespace = "\\igbot\\Actions";
    public static array $NON_USER_ACTIONS = ['Account Login'];

    private $ACTIONS;

    public function getAllActions()
    {

        if(empty($this->ACTIONS)) {
            $this->ACTIONS = [];
            $FILES = scandir(self::$action_dir);
            foreach($FILES as $file) {
                if(in_array($file, ['.', '..', self::$action_parent_class]))
                    continue;
                    
                $class = self::$actions_namespace . "\\" . explode(".", $file)[0];
                $this->ACTIONS[] = new $class(new Account("", "", "")); 
            }
        }

        return $this->ACTIONS;
    }


    public function getAllUserActions()
    {

        $USER_ACTIONS = [];
        foreach($this->getAllActions() as $Action)
            if(!in_array($Action->getTitle(), self::$NON_USER_ACTIONS))
                $USER_ACTIONS[] = $Action;

        return $USER_ACTIONS;
    }


    public function getActionByTitle(string $title)
    {

        foreach($this->getAllActions() as $Action)
            if($Action->getTitle() == $title)
                return $Action;
    }
}

