<?php

namespace Esoftdream\Code\Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function code($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('code');
        }

        return new \Esoftdream\Code\Generator();
    }
}
