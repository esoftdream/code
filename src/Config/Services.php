<?php

namespace Esoftdream\Code\Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function generator()
    {
        return new \Esoftdream\Code\Generator();
    }
}
