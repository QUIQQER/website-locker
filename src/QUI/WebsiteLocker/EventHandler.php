<?php
/**
 * Created by PhpStorm.
 * User: gerd
 * Date: 21.05.19
 * Time: 12:26
 */
namespace QUI\WebsiteLocker;

use QUI\System\Log;

class EventHandler
{
    public static function onRequest(){
//        Log::writeRecursive($Rewrite);
//        Log::writeRecursive($url);

        Log::writeRecursive('test');
    }
}