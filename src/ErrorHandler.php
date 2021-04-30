<?php
/**
 * contains ErrorHandling class
 *
 * @package         tourBase
 * @author          David Lienhard <github@lienhard.win>
 * @copyright       David Lienhard
 */

declare(strict_types=1);

namespace DavidLienhard\ErrorHandler;

use DavidLienhard\ErrorHandler\ErrorHandlerInterface;

/**
 * class for improved error handling and logging
 *
 * @author          David Lienhard <github@lienhard.win>
 * @copyright       David Lienhard
 */
class ErrorHandler implements ErrorHandlerInterface
{
    /**
     * main logfolder
     * defaults to current folder
     */
    public static string $logFolder = ".";

    /**
     * whether to print errors or not
     */
    public static bool $printErrors = false;

    /**
     * sets itself as error handler
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     */
    public static function setHandler() : void
    {
        set_error_handler("\\".__CLASS__."::onError", E_ALL);
        register_shutdown_function("\\".__CLASS__."::onShutdown");
    }


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
     * @uses            ISDEV
     * @uses            self::$logFolder
     * @uses            self::getRequestUrl()
     * @uses            self::getErrorCode()
     */
    public static function logErrors(
        string $errstr,
        string $errfile,
        int $errline,
        int $errno,
        string $errmessage = ""
    ) : bool {
        $logFolder = self::$logFolder."/php/".date("Y")."/".date("m")."/";
        $logFile = "php_".date("Y_m_d").".log";

        $client = self::getIp() !== "" ? "[ client ".self::getIp()." ] " : "";
        $referer = isset($_SERVER['HTTP_REFERER']) ? ", referer: ".$_SERVER['HTTP_REFERER']." " : "";
        $requestUrl = self::getRequestUrl();
        $requestUrl = $requestUrl !== "" ? "\t".$requestUrl."\n" : "";
        $errorMessage = ($requestUrl !== "" || $errmessage !== "" ? "\n" : "").$requestUrl.$errmessage;

        $line =
            "[".date("r")."] ".
            $client.
            "PHP ".self::getErrorCode($errno).": ".
            $errstr.($errstr !== "" ? " " : "").
            "in ".$errfile.":".
            $errline." ".
            "(".$errno.")".
            $referer.
            $errorMessage."\n";

        if (self::$printErrors) {
            echo $line;
        }

        // create folder if necessary and return false if not possible to create folder
        if (!is_dir($logFolder) && mkdir($logFolder, 0744, true) === false) {
            return false;
        }

        // open logfile or return false if failed
        $fp = fopen($logFolder.$logFile, "a+");
        if ($fp === false) {
            return false;
        }

        // write data to logfile or return false if failed
        if (fwrite($fp, $line) === false) {
            return false;
        }

        fclose($fp);
        return true;
    }


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
     * @uses            self::getTraceAsArray()
     * @uses            self::logErrors()
     */
    public static function onError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline,
        array $errcontext = []
    ) : bool {
        if (error_reporting() === 0) {
            return true;
        }

        // get stack trace
        $e = new \Exception();
        $errMessage = "\tStack trace:\n\t".implode("\n\t", self::getTraceAsArray($e, true));
        return self::logErrors(
            $errstr,
            $errfile,
            $errline,
            $errno,
            $errMessage
        );
    }


    /**
     * callback function for set_error_handler. passes date on to logError
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @uses            self::getErrorCode()
     * @uses            self::logErrors()
     */
    public static function onShutdown() : void
    {
        $lastError = error_get_last();

        if ($lastError !== null && is_array($lastError)) {
            $errorMessage = explode("\n", $lastError['message']);
            foreach ($errorMessage as $key => $value) {
                $errorMessage[$key] = "\t".$value;
            }

            $errMessage = implode("\n", $errorMessage);

            self::logErrors(
                "",
                $lastError['file'],
                $lastError['line'],
                $lastError['type'],
                $errMessage
            );
        }
    }


    /**
     * method to log an exception
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           \Throwable      $t              the exception/throwable to log
     * @uses            self::getTraceAsArray()
     * @uses            self::logErrors()
     */
    public static function logException(\Throwable $t) : bool
    {
        $errMessage = "\tStack trace:\n\t".implode("\n\t", self::getTraceAsArray($t));
        return self::logErrors(
            $t->getMessage(),
            $t->getFile(),
            $t->getLine(),
            $t->getCode(),
            $errMessage
        );
    }


    /**
     * method to log an a 404 request
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @uses            self::$logFolder
     * @uses            self::getRequestUrl()
     */
    public static function logNotFound(): bool
    {
        $logFolder = self::$logFolder."/404/".date("Y")."/".date("m")."/";
        $logFile = "404_".date("Y_m_d").".log";

        $client = self::getIp() !== "" ? "[ client ".self::getIp()." ] " : "";
        $requestUrl = self::getRequestUrl();
        $requestMethod = isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== "" ? " (".$_SERVER['REQUEST_METHOD'].")" : "";
        $referer = isset($_SERVER['HTTP_REFERER']) ? ", referer: ".$_SERVER['HTTP_REFERER']." " : "";
        $ua = isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] !== "" ? ", useragent: ".$_SERVER['HTTP_USER_AGENT']." " : "";

        $line =
            "[".date("r")."] ".
            $client.
            $requestUrl.
            $requestMethod.
            $referer.
            $ua."\n";

        // create folder if necessary and return false if not possible to create folder
        if (!is_dir($logFolder) && mkdir($logFolder, 0744, true) === false) {
            return false;
        }

        // open logfile or return false if failed
        $fp = fopen($logFolder.$logFile, "a+");
        if ($fp === false) {
            return false;
        }

        // write data to logfile or return false if failed
        if (fwrite($fp, $line) === false) {
            return false;
        }

        fclose($fp);
        return true;
    }


    /**
     * method to log a failed login
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           string          $username       username used for login
     * @uses            self::$logFolder
     * @uses            self::getRequestUrl()
     */
    public static function logFailedLogin(string $username): bool
    {
        $logFolder = self::$logFolder."/login/".date("Y")."/".date("m")."/";
        $logFile = "login_".date("Y_m_d").".log";

        $client = self::getIp() !== "" ? "[ client ".self::getIp()." ] " : "";
        $referer = isset($_SERVER['HTTP_REFERER']) ? " referer: ".$_SERVER['HTTP_REFERER']." " : "";

        $line =
            "[".date("r")."] ".
            $client.
            "User: '".$username."'".
            $referer."\n";

        // create folder if necessary and return false if not possible to create folder
        if (!is_dir($logFolder) && mkdir($logFolder, 0744, true) === false) {
            return false;
        }

        // open logfile or return false if failed
        $fp = fopen($logFolder.$logFile, "a+");
        if ($fp === false) {
            return false;
        }

        // write data to logfile or return false if failed
        if (fwrite($fp, $line) === false) {
            return false;
        }

        fclose($fp);
        return true;
    }


    /**
     * sets the folder to log to
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           string          $logFolder      folder to write logfiles to
     * @uses            self::$logFolder
     */
    public static function setLogFolder(string $logFolder) : void
    {
        self::$logFolder = $logFolder;
    }


    /**
     * whether to print errors or not
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           bool            $printErrors    whether to print errors or not
     * @uses            self::$printErrors
     */
    public static function printErrors(bool $printErrors) : void
    {
        self::$printErrors = $printErrors;
    }


    /**
     * callback function for set_error_handler. passes date on to logError
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           \Throwable      $t              returns the complete stack trace of a throwable including previous
     * @param           bool            $isError        if set to true the first line of the trace will be remove (call to trigger_error)
     * @param           int             $level          level of recursion
     * @return          array
     * @uses            self::getTraceAsArray()
     */
    private static function getTraceAsArray(\Throwable $t, bool $isError = false, int $level = 0) : array
    {
        $space = str_repeat("  ", $level);
        $trace = explode("\n", $t->getTraceAsString());
        if ($isError) {
            array_shift($trace);                                // remove call to this method
        }
        $length = count($trace);
        $result = [];

        for ($i = 0; $i < $length; $i++) {
            $result[] = $space."#".$i.substr($trace[$i], strpos($trace[$i], " "));  // replace '#someNum' with '#$i', set the right ordering
        }

        $trace = $level !== 0 ? array_merge(
            [ $space.$t->getMessage()." in ".$t->getFile().":".$t->getLine()." (".get_class($t).")" ],
            $result
        ) : $result;

        $previous = $t->getPrevious();
        if ($previous !== null) {
            $trace = array_merge(
                $trace,
                self::getTraceAsArray($previous, false, $level + 1)
            );
        }
        return $trace;
    }


    /**
     * returns the request url if possible
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     */
    private static function getRequestUrl() : string
    {
        if (isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])) {
            return "request url: ".
                (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://".
                $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            return "request url: ".$_SERVER['REQUEST_URI'];
        }

        return "";
    }


    /**
     * returns the error code from a number
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     * @param           int             $errno          an error number
     */
    private static function getErrorCode(int $errno) : string
    {
        $errorTranslation = [
            1     => "Error",
            2     => "Warning",
            4     => "Parse",
            8     => "Notice",
            16    => "Core error",
            32    => "Core Warning",
            64    => "Compile Error",
            128   => "Compile Warning",
            256   => "User error",
            512   => "User warning",
            1024  => "User notice",
            2048  => "Strict",
            4096  => "Recoverable error",
            8192  => "Deprecated",
            16384 => "User Deprecated",
            32767 => "All"
        ];

        return $errorTranslation[$errno] ?? "";
    }


    /**
     * returns the clients ip-address
     *
     * @author          David Lienhard <github@lienhard.win>
     * @copyright       David Lienhard
     */
    private static function getIp() : string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? "";
    }
}
