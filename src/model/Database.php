<?php

namespace DatabaseImporter\model;

class Database
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $name
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function __construct(string $name, string $host, string $user, string $password)
    {
        $this->name = $name;
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}