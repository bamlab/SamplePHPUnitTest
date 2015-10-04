<?php

namespace DemoTest\Classes;

use DemoTest\Classes\User;
use DemoTest\Classes\AuthInterface;
use DemoTest\Classes\HandlerCustomerInterface;
/**
 * Classe HelperCustomer
 * @category Helper
 */
class HelperCustomer {

    const TYPE_INTERNAL   = 'INTERNAL';
    const TYPE_EXTERNAL   = 'EXTERNAL';
    const TYPE_ADMIN      = 'ADMIN';
    const TYPE_SUPERADMIN = 'SUPERADMIN';

    /**
     * The Auth service
     * @var AuthInterface
     */
    private $auth;

    /**
     * The handler customer
     * @var HandlerCustomerInterface
     */
    private $handlerCustomer;

    /**
     * Construct the class
     * @param AuthInterface
     * @param HandlerCustomerInterface
     */
    public function __construct(AuthInterface $auth, HandlerCustomerInterface $handlerCustomer)
    {
        $this->auth = $auth;
        $this->handlerCustomer = $handlerCustomer;
    }

    /**
     * @return boolean
     */
    public function is_internal()
    {
        $customer = $this->auth->get_user();
        return ($customer->type == self::TYPE_INTERNAL || $customer->type == self::TYPE_SUPERADMIN);
    }

    /**
     * @return boolean
     */
    public function is_admin()
    {
        $customer = $this->auth->get_user();
        return ($customer->type == self::TYPE_ADMIN || $customer->type == self::TYPE_SUPERADMIN);
    }

    /**
     * @return boolean
     */
    public function is_superadmin()
    {
        $customer = $this->auth->get_user();
        return $customer->type == self::TYPE_SUPERADMIN;
    }

    /**
     * @return boolean
     */
    public function is_only_admin()
    {
        $customer = $this->auth->get_user();
        return $customer->type == self::TYPE_ADMIN;
    }

    /**
     * @return boolean
     */
    public function has_parent()
    {
        $customer = $this->auth->get_user();
        return $customer->parent_user_id !== NULL;
    }

    /**
     * @param  Model_Notice $notice
     * @return boolean
     */
    public function can_edit_notice(Model_Notice $notice)
    {
        return $this->has_rights_on_user($notice->customer_id);
    }

    /**
     * @param  $customer_id
     * @return boolean
     */
    public function has_rights_on_user($customer_id)
    {
        $accessible_users = $this->get_accessible_users();
        $accessible_users_id = array();
        foreach ($accessible_users as $user) {
            $accessible_users_id[] = $user->id;
        }
        return in_array($customer_id, $accessible_users_id);
    }

    /**
     * If superadmin, return all users
     * @return Model_User[]
     */
    public function get_accessible_users()
    {
        if ($this->is_superadmin()) {
            return $this->handlerCustomer->get_all();
        }
        if ($this->is_only_admin()) {
            $users = $this->handlerCustomer->get_children($this->auth->get_user()->id);
            $users[] = $this->auth->get_user();
            return $users;
        }
        if ($this->has_parent()) {
            $users =  $this->handlerCustomer->get_children($this->auth->get_user()->parent_user_id);
            $users[] = $this->auth->get_user();
            $users[] = $this->handlerCustomer->get($this->auth->get_user()->parent_user_id);
            return $users;
        }
        return array($this->auth->get_user());
    }

    /**
     * @return array
     */
    public function get_editable_types()
    {
        if ($this->is_superadmin())
        {
            return array(
                self::TYPE_SUPERADMIN =>
                    UTF8::ucfirst(__(UTF8::strtolower(self::TYPE_SUPERADMIN))),
                self::TYPE_ADMIN      =>
                    UTF8::ucfirst(__(UTF8::strtolower(self::TYPE_ADMIN))),
                self::TYPE_INTERNAL   =>
                    UTF8::ucfirst(__(UTF8::strtolower(self::TYPE_INTERNAL))),
            );
        }
        if ($this->is_only_admin())
        {
            return array(
                self::TYPE_EXTERNAL =>
                    UTF8::ucfirst(__(UTF8::strtolower(self::TYPE_EXTERNAL))),
            );
        }
        return array();
    }
}
