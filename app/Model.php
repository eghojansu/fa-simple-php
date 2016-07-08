<?php

/**
 * You can put common helper that require db connection here
 * To make life easier
 */
class Model
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }
}