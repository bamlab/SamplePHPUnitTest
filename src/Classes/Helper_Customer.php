<?php defined('SYSPATH') or die('No direct access allowed.');

require_once(__DIR__.'/User.php');

/**
 * Classe Helper_Customer
 * @category Helper
 */
class Helper_Customer {

    const TYPE_INTERNAL   = 'INTERNAL';
    const TYPE_EXTERNAL   = 'EXTERNAL';
    const TYPE_ADMIN      = 'ADMIN';
    const TYPE_SUPERADMIN = 'SUPERADMIN';

    /**
     * Get an User instance
     * @return User
     */
    public static function getUserInstance()
    {
        return Auth::instance()->get_user();
    }

    /**
     * @return boolean
     */
    public static function is_internal()
    {
        $customer = static::getUserInstance();
        return ($customer->type == self::TYPE_INTERNAL || $customer->type == self::TYPE_SUPERADMIN);
    }

    /**
     * @return boolean
     */
    public static function is_admin()
    {
        $customer = static::getUserInstance();
        return ($customer->type == self::TYPE_ADMIN || $customer->type == self::TYPE_SUPERADMIN);
    }

    /**
     * @return boolean
     */
    public static function is_superadmin()
    {
        $customer = static::getUserInstance();
        return $customer->type == self::TYPE_SUPERADMIN;
    }

    /**
     * @return boolean
     */
    public static function is_only_admin()
    {
        $customer = static::getUserInstance();
        return $customer->type == self::TYPE_ADMIN;
    }

    /**
     * @return boolean
     */
    public static function has_parent()
    {
        $customer = static::getUserInstance();
        return $customer->parent_user_id !== NULL;
    }

    /**
     * @param  Model_Notice $notice
     * @return boolean
     */
    public static function can_edit_notice(Model_Notice $notice)
    {
        return Helper_Customer::has_rights_on_user($notice->customer_id);
    }

    /**
     * @param  $customer_id
     * @return boolean
     */
    public static function has_rights_on_user($customer_id)
    {
        $accessible_users = Helper_Customer::get_accessible_users();
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
    public static function get_accessible_users()
    {
        if (Helper_Customer::is_superadmin()) {
            return Handler_Customer::get_all();
        }
        if (Helper_Customer::is_only_admin()) {
            $users = Handler_Customer::get_children(Auth::instance()->get_user()->id);
            $users[] = static::getUserInstance();
            return $users;
        }
        if (Helper_Customer::has_parent()) {
            $users =  Handler_Customer::get_children(Auth::instance()->get_user()->parent_user_id);
            $users[] = static::getUserInstance();
            $users[] = Handler_Customer::get(Auth::instance()->get_user()->parent_user_id);
            return $users;
        }
        return array(Auth::instance()->get_user());
    }

    /**
     * @return array
     */
    public static function get_editable_types()
    {
        if (self::is_superadmin())
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
        if (self::is_only_admin())
        {
            return array(
                self::TYPE_EXTERNAL =>
                    UTF8::ucfirst(__(UTF8::strtolower(self::TYPE_EXTERNAL))),
            );
        }
        return array();
    }
}
