<?php

require_once __DIR__.'/../src/Classes/Helper_Customer.php';

class Helper_Customer_Redefined extends Helper_Customer
{
    private static $user = null;
    public static function getUserInstance()
    {
        if (null === self::$user) {
            self::$user = new User();
        }

        return self::$user;
    }
}

class Helper_Customer_Test  extends \PHPUnit_Framework_TestCase
{
    public function testIsInternal()
    {
        Helper_Customer_Redefined::getUserInstance()->type = Helper_Customer::TYPE_ADMIN;
        $this->assertFalse(Helper_Customer_Redefined::is_internal());

        Helper_Customer_Redefined::getUserInstance()->type = Helper_Customer::TYPE_INTERNAL;
        $this->assertTrue(Helper_Customer_Redefined::is_internal());

        Helper_Customer_Redefined::getUserInstance()->type = Helper_Customer::TYPE_SUPERADMIN;
        $this->assertTrue(Helper_Customer_Redefined::is_internal());
    }
}
