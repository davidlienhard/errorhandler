<?php
/**
 * contains ErrorHandling interface
 *
 * @package         tourBase
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @version         1.0.6, 05.01.2021
 * @since           1.0.0, 16.11.2020, created
 * @copyright       tourasia
 */

declare(strict_types=1);

namespace DavidLienhard\ErrorHandler;

/**
 * interface for improved error handling and logging
 *
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @version         1.0.6, 05.01.2021
 * @since           1.0.0, 16.11.2020, created
 * @copyright       tourasia
 */
interface ErrorHandlerInterface
{
    /**
     * sets itself as error handler
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 16.11.2020
     * @since           1.0.0, 16.11.2020, created
     * @copyright       tourasia
     * @return          void
     */
    public static function setHandler() : void;


    /**
     * writes the given data into a logfile
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 16.11.2020
     * @since           1.0.0, 16.11.2020, created
     * @copyright       tourasia
     * @param           string          $errstr         the error string
     * @param           string          $errfile        filename in which the error happened
     * @param           int             $errline        line in which the error happened
     * @param           int             $errno          an error number
     * @param           string          $errmessage     an optional errormessage
     * @return          bool
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
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.4, 16.12.2020
     * @since           1.0.0, 16.11.2020, created
     * @copyright       tourasia
     * @param           int             $errno          an error number
     * @param           string          $errstr         the error string
     * @param           string          $errfile        filename in which the error happened
     * @param           int             $errline        line in which the error happened
     * @param           array           $errcontext     an optional context data
     * @return          bool
     */
    public static function onError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline,
        array $errcontext = [ ]
    ) : bool;


    /**
     * callback function for set_error_handler. passes date on to logError
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 16.11.2020
     * @since           1.0.0, 16.11.2020, created
     * @copyright       tourasia
     * @return          void
     * @uses            self::logErrors()
     * @uses            self::getRequestUrl()
     */
    public static function onShutdown() : void;


    /**
     * method to log an exception
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 16.11.2020
     * @since           1.0.0, 16.11.2020, created
     * @copyright       tourasia
     * @param           \Throwable      $t              the exception/throwable to log
     * @return          bool
     */
    public static function logException(\Throwable $t) : bool;


    /**
     * method to log an a 404 request
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 16.11.2020
     * @since           1.0.0, 16.11.2020, created
     * @copyright       tourasia
     * @return          bool
     */
    public static function logNotFound();


    /**
     * method to log a failed login
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 16.11.2020
     * @since           1.0.0, 16.11.2020, created
     * @copyright       tourasia
     * @param           string          $username       username used for login
     * @return          bool
     */
    public static function logFailedLogin(string $username);


    /**
     * sets the folder to log to
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 16.11.2020
     * @since           1.0.0, 16.11.2020, created
     * @copyright       tourasia
     * @param           string          $logFolder      folder to write logfiles to
     * @return          void
     */
    public static function setLogFolder(string $logFolder) : void;


    /**
     * whether to print errors or not
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.6, 05.01.2021
     * @since           1.0.6, 05.01.2021, created
     * @copyright       tourasia
     * @param           bool            $printErrors    whether to print errors or not
     * @return          void
     * @uses            self::$printErrors
     */
    public static function printErrors(bool $printErrors) : void;
}
