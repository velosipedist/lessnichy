<?php
namespace Lessnichy {
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
         * @param $baseUrl
         * @return Client
         */
        public static function connect($baseUrl)
        {
            if (is_null(self::$client)) {
                self::$client = new Client($baseUrl);
            }
            return self::$client;
        }

        /**
         * @param $lessStylesheets
         */
        public static function add($lessStylesheets)
        {
            self::ensureClient();
            return self::$client->add($lessStylesheets);
        }

        /**
         * @see LessnichyClient::head()
         */
        public static function head(array $options = [] /* todo optional stream handler besides stdout*/)
        {
            self::ensureClient();
            return self::$client->head($options);
        }

        /**
         *
         */
        private static function ensureClient()
        {
            if (is_null(self::$client)) {
                throw new \LogicException("Lessnichy must be bootstrapped using wakeup()");
            }
        }

        /**
         * @return Server
         */
        public static function listen()
        {
            if (is_null(self::$server)) {
                self::$server = new Server();
            }
            return self::$server;
        }
    }
}

namespace {
    use Lessnichy\Lessnichy;

    if (! class_exists('LESS')) {
        class LESS extends Lessnichy
        {
        }
    }
}