<?php

namespace igbot\user;

use \core\super\Collection;


class IgUserCollection extends Collection 
{

    public function add(IgUser $User)
    {

        $this->ITEMS[] = $User;
    }
}