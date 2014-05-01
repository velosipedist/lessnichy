<?php
namespace Lessnichy {
    use Closure;

    /**
     * Lessnichy magic facade
     */
    class Lessnichy
    {
        /**
         * Option for less.js script url
         */
        const JS = 'less';
        /**
         * Option for enable/disable watch mode on start
         */
        const DEBUG = 'watch';
        /**
         * Option for set watch interval in milliseconds
         */
        const WATCH_INTERVAL = 'poll';
        /**
         * @var Client
         */
        private static $client;
        /**
         * @var Server
         */
        private static $server;

        /**
         * @param string $baseUrl full base url to Lessnicy API listener dir
         * @param bool|Closure   $lessMode
         * @return Client
         */
        public static function connect($baseUrl, $lessMode = true)
        {
            if (is_null(self::$client)) {
                self::$client = new Client($baseUrl, $lessMode);
            }
            return self::$client;
        }

        /**
         * @param $lessStylesheets
         */
        public static function add(array $lessStylesheets)
        {
            self::ensureClient();
            return self::$client->add($lessStylesheets);
        }

        /**
         * @see LessnichyClient::head()
         */
        public static function head(array $options = array() /* todo optional stream handler besides stdout*/)
        {
            self::ensureClient();
            return self::$client->head($options);
        }

        /**
         * Check that API client is started up
         */
        private static function ensureClient()
        {
            if (is_null(self::$client)) {
                throw new \LogicException("Lessnichy must be bootstrapped using wakeup()");
            }
        }

        /**
         * Catch http requests in current folder
         * @return Server
         */
        public static function listen()
        {
            //todo auto-bootstrap .htaccess
            if (is_null(self::$server)) {
                self::$server = new Server();
            }
            return self::$server;
        }
    }
}

namespace {
    use Lessnichy\Lessnichy;
    try {
        $exists = @class_exists('LESS');
    } catch(\Exception $e) {
        $exists = false;
    }
    if (! $exists ) {
        /**
         * Shortut facade to {@link Lessnichy}
         */
        class LESS extends Lessnichy
        {
        }
    }
}