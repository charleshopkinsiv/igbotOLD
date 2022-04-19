<?php

namespace IgBot\User;

use \CharlesHopkinsIV\Core\Super\Collection;


class IgUserCollection extends Collection 
{

    public function add(IgUser $User)
    {

        $this->ITEMS[] = $User;
    }
}