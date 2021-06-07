<?php declare(strict_types=1);

/**
 * contains ErrorHandling interface
 *
 * @package         tourBase
 * @author          David Lienhard <github@lienhard.win>
 * @copyright       David Lienhard
 */

namespace DavidLienhard\ErrorHandler;

/**
 * interface for improved error handling and logging
 *
 * @author          David Lienhard <github@lienhard.win>
 * @copyright       David Lienhard
 */
interface ErrorHandlerInterface
{
    /**
     * sets itself as error handler
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     */
    public static function setHandler() : void;


    /**
     * writes the given data into a logfile
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           string          $errstr         the error string
     * @param           string          $errfile        filename in which the error happened
     * @param           int             $errline        line in which the error happened
     * @param           int             $errno          an error number
     * @param           string          $errmessage     an optional errormessage
     */
    public static function logErrors(
        string $errstr,
        string $errfile,
        int $errline,
        int $errno,
        string $errmessage = ""
    ) : bool;


    /**
     * callback function for set_error_handler. passes date on to logError
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           int             $errno          an error number
     * @param           string          $errstr         the error string
     * @param           string          $errfile        filename in which the error happened
     * @param           int             $errline        line in which the error happened
     * @param           array           $errcontext     an optional context data
     */
    public static function onError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline,
        array $errcontext = []
    ) : bool;


    /**
     * callback function for set_error_handler. passes date on to logError
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @uses            self::logErrors()
     * @uses            self::getRequestUrl()
     */
    public static function onShutdown() : void;


    /**
     * method to log an exception
     *
     * @author          David Lienhard <github@lienhard.win>
     * @version         1.0.0, 16.11.2020
     * @since           1.0.0, 16.11.2020, created
     * @copyright       David Lienhard
     * @param           \Throwable      $t              the exception/throwable to log
     */
    public static function logException(\Throwable $t) : bool;


    /**
     * method to log an a 404 request
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     */
    public static function logNotFound(): bool;


    /**
     * method to log a failed login
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           string          $username       username used for login
     */
    public static function logFailedLogin(string $username): bool;


    /**
     * sets the folder to log to
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           string          $logFolder      folder to write logfiles to
     */
    public static function setLogFolder(string $logFolder) : void;


    /**
     * whether to print errors or not
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           bool            $printErrors    whether to print errors or not
     * @uses            self::$printErrors
     */
    public static function printErrors(bool $printErrors) : void;
}
