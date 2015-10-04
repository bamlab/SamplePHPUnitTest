<?php

namespace DemoTest\Classes;

use DemoTest\Classes\User;

interface AuthInterface {

    /**
     * Return the curent user instance
     * @return User
     */
    public function get_user();
}
