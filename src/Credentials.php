<?php

namespace Arkade\RetailDirections;

class Credentials
{
    /**
     * Username for authenticating against API.
     *
     * @var string
     */
    protected $username;

    /**
     * Password for authenticating against API.
     *
     * @var string
     */
    protected $password;

    /**
     * Credentials constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Return username for authenticating against API.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Return password for authenticating against API.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}