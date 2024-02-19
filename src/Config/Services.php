<?php

namespace Esoftdream\Syloer\Config;

use Config\Logger as ConfigLogger;
use Config\Services as BaseService;
use Esoftdream\Syloer\Libraries\Logger;

class Services extends BaseService
{
    public static function logger(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('logger');
        }

        return new Logger(config(ConfigLogger::class));
    }
}
