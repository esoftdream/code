<?php

namespace Esoftdream\Code\Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function code()
    {
        return new \Esoftdream\Code\Generator();
    }
}
