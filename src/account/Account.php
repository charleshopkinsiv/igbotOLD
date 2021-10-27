<?php

namespace igbot\account;


class Account {

    private $email, $username, $password, $proxy, $status;
    
    public function __construct($email, $username, $password, $proxy = '', $status = '') {

        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->proxy = $proxy;
        $this->status = $status;
    }


    public function getEmail()
    {

        return $this->email;
    }

    public function getPassword()
    {

        return $this->password;
    }

    public function getUsername()
    {

        return $this->username;
    }

    public function getProxy()
    {

        return $this->proxy;
    }

    public function getStatus()
    {

        return $this->status;
    }

    public function setStatus($status)
    {

        $this->status = $status;
    }

}