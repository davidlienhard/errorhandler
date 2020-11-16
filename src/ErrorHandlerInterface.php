<?php
/**
 * contains ErrorHandling interface
 *
 * @package         tourBase
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @version         1.0.0, 16.11.2020
 * @since           1.0.0, 16.11.2020, created
 * @copyright       tourasia
 */

declare(strict_types=1);

namespace tourBase\Core\ErrorHandler;

/**
 * interface for improved error handling and logging
 *
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @version         1.0.0, 16.11.2020
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
     * @version         1.0.0, 16.11.2020
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
        array $errcontext
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
}