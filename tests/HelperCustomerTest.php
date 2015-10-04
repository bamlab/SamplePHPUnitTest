<?php

namespace Test;

use DemoTest\Classes\HelperCustomer;
use DemoTest\Classes\User;

class HelperCustomerRedefined extends HelperCustomer
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

class HelperCustomerTest  extends \PHPUnit_Framework_TestCase
{
    public function testIsInternal()
    {
        HelperCustomerRedefined::getUserInstance()->type = HelperCustomer::TYPE_ADMIN;
        $this->assertFalse(HelperCustomerRedefined::is_internal());

        HelperCustomerRedefined::getUserInstance()->type = HelperCustomer::TYPE_INTERNAL;
        $this->assertTrue(HelperCustomerRedefined::is_internal());

        HelperCustomerRedefined::getUserInstance()->type = HelperCustomer::TYPE_SUPERADMIN;
        $this->assertTrue(HelperCustomerRedefined::is_internal());
    }
}
