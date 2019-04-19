<?php
/**
 * Created by PhpStorm.
 * User: wangbin
 * Date: 2019/4/11
 * Time: 16:42
 */

$log['master'] = array(
    'type' => 'FileLog',
    'file' => WEBPATH . '/logs/app.log',
);
$log['test'] = array(
    'type' => 'FileLog',
    'file' => WEBPATH . '/logs/test.log',
);
return $log;