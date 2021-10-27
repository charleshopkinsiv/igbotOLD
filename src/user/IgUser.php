<?php

namespace igbot\user;


class IgUser {

    private string $username, $name, $description, $url;

    public function __construct($username, $name, $description, $url) {

        $this->username         = $username;
        $this->name             = $name;
        $this->description      = $description;
        $this->url              = $url;
    }

    public function getUsername() { return $this->username; }

    public function setUsername(string $username) { $this->username = $username; }

    public function getName() { return $this->name; }

    public function setName(string $name) { $this->name = $name; }

    public function getDescription() { return $this->description; }

    public function setDescription(string $description) { $this->description = $description; }

    public function getUrl() { return $this->url; }

    public function setUrl(string $url) { $this->url = $url; }

}