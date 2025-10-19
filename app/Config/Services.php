<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use App\Libraries\ValidadorEc;
use App\Libraries\User;
use App\Libraries\Logger;
use App\Models\CcModel;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService {
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    public static function validadorEc(bool $getShared = true) {
        if ($getShared) {
            return static::getSharedInstance('validadorEc');
        }
        return new ValidadorEc();
    }

    public static function ccModel(bool $getShared = true) {
        if ($getShared) {
            return static::getSharedInstance('ccModel');
        }
        return new CcModel();
    }

    public static function userSecion(bool $getShared = true) {
        if ($getShared) {
            return static::getSharedInstance('userSecion');
        }
        return new User();
    }

    public static function logs305(bool $getShared = true) {
        if ($getShared) {
            return static::getSharedInstance('logs305');
        }
        return new Logger();
    }
}
