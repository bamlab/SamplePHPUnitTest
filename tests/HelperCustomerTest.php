<?php

namespace Test;

use DemoTest\Classes\HelperCustomer;
use DemoTest\Classes\User;

class HelperCustomerTest  extends \PHPUnit_Framework_TestCase
{
    public function testIsInternal()
    {
        $auth = $this->getMock('\DemoTest\Classes\AuthInterface');
        $handler = $this->getMock('DemoTest\Classes\HandlerCustomerInterface');

        $helper = new HelperCustomer($auth, $handler);

        $user = new User();
        $auth->expects(self::any())
            ->method('get_user')
            ->will($this->returnValue($user));

        $user->type = HelperCustomer::TYPE_ADMIN;
        $this->assertFalse($helper->is_internal());

        $user->type = HelperCustomer::TYPE_INTERNAL;
        $this->assertTrue($helper->is_internal());

        $user->type = HelperCustomer::TYPE_SUPERADMIN;
        $this->assertTrue($helper->is_internal());
    }
}
