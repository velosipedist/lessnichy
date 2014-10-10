<?php
namespace Lessnichy {

    /**
     * Lessnichy magic facade
     */
    class Lessnichy
    {
        /**
         * Option for enable/disable watch mode on start, boolean
         */
        const WATCH = 'watch';
        /**
         * Option for set watch interval in milliseconds, int
         */
        const WATCH_INTERVAL = 'poll';
        /**
         * Whether to auto-start less.watch(), boolean
         */
        const WATCH_AUTOSTART = 'watch.autostart';
        /**
         * Whether to add ?randomnumber after .less url
         */
        const RANDOMIZE_LESS_URL = 'less.randomize';
        /**
         * Which debugging source line nums to dump
         */
        const DUMP_LINES = 'dumpLineNumbers';
        /**
         * @var $this
         */
        private static $instance;
        /**
         * @var Client
         */
        private $client;
        /**
         * @var Server
         */
        private static $server;

        function __construct($baseUrl)
        {
            $this->client = new Client($baseUrl);
        }


        /**
         * @param string $baseUrl full base url to Lessnicy API listener dir
         * @internal param bool|callable $lessMode
         * @return static
         */
        public static function connect($baseUrl)
        {
            if (is_null(self::$instance)) {
                self::$instance = new static($baseUrl);
            }
            return self::$instance;
        }

        /**
         * @param $lessStylesheets
         * @return $this
         */
        public function add(array $lessStylesheets)
        {
            $this->client->add($lessStylesheets);
            return $this;
        }

        /**
         * @see LessnichyClient::head()
         * @return $this
         */
        public function head(array $options = array() /* todo optional stream handler besides stdout*/)
        {
            $this->client->head($options);
            return $this;
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