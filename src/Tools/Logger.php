<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 11.05.19
 * Time: 11:59
 */

namespace App\Tools;


use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class Logger
{
    /**
     * Detailed debug information
     */
    const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;

    /**
     * Uncommon events
     */
    const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;

    /**
     * Runtime errors
     */
    const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 550;

    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;

    /**
     * Monolog API version
     *
     * This is only bumped when API breaks are done and should
     * follow the major version of the library
     *
     * @var int
     */
    const API = 1;

    /**
     * @var string
     */
    private static $path;

    /**
     * @var \Monolog\Logger
     */
    private static $logger;

    private function __construct(){}
    private function __clone(){}

    /**
     * @param string $name
     * @param int $level
     * @param string $channel
     * @return \Monolog\Logger
     * @throws \Exception
     */
    public static function getLogger(string $name, int $level, string $channel): \Monolog\Logger
    {
        if(self::$logger === null){
            self::$path = $GLOBALS['kernel']->getLogDir();
            self::$logger = new \Monolog\Logger($channel);
            $location = self::$path . '/' . 'log' . '.log';
            self::$logger->pushHandler((new StreamHandler($location, $level))->setFormatter(new LineFormatter()));
        }

        return self::$logger;
    }
}