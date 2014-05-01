<?php
namespace Lessnichy;

use Closure;

/**
 * Class Client
 * @package Lessnichy
 */
class Client
{
    /**
     * @var string API base url
     */
    private $baseUrl;
    /**
     * @var string[] Less stylesheets urls
     */
    private $stylesheets;
    /**
     * @var bool Does LESS head scripts printed
     */
    private $headPrinted = false;
    /**
     * @var bool Whether to use .less sources and browser compilation. Otherwise just print compiled .css
     */
    private $enableLessMode = true;
    /**
     * @var callable Callback to define {@link enableLessMode}
     */
    private $lessModeTrigger;

    /**
     * @param              $baseUrl
     * @param bool|Closure $lessMode
     */
    function __construct($baseUrl, $lessMode = true)
    {
        $baseUrl = rtrim($baseUrl, '/');
        if (php_sapi_name() == 'cli-server') {
            $baseUrl .= '/index.php';
        }
        $this->baseUrl = $baseUrl;
        //todo ability to sitch less mode on-the-fly from browser
        if ($lessMode instanceof Closure) {
            $this->lessModeTrigger = $lessMode;
        } else {
            $this->enableLessMode = (bool) $lessMode;
        }
    }

    /**
     * @param $stylesheetUrl
     * @return bool
     */
    public static function isLess($stylesheetUrl)
    {
        return (bool) preg_match('/\.less$/i', parse_url($stylesheetUrl, PHP_URL_PATH));
    }

    /**
     * @param $stylesheetUrl
     * @return bool
     */
    public static function isCss($stylesheetUrl)
    {
        return (bool) preg_match('/\.css$/i', parse_url($stylesheetUrl, PHP_URL_PATH));
    }

    /**
     * @param string[] $lessStylesheets full urls to .less files
     */
    public function add(array $lessStylesheets = array())
    {
        foreach ($lessStylesheets as $less) {
            $this->stylesheets[] = $less;
        }

        $baseurl = $this->baseUrl;
        $client  = $this;

        register_shutdown_function(
            function () use ($client, $baseurl) {
                // when Lessnichy used, register client watching libs and resources
                if ($client->isHeadPrinted() && $client->inLessMode()) {
                    //todo link to gzipped glued js
                    print "<script src='{$baseurl}/js/clean-css.min.js'></script>\n";
                    print "<script src='{$baseurl}/js/lessnichy.js'></script>";
                }
            }
        );
    }

    /**
     * @param array $extraOptions use LESS::* constants
     */
    public function head(array $extraOptions = array() /* todo optional stream handler besides stdout*/)
    {
        if (empty($this->stylesheets)) {
            throw new \LogicException("Add some stylesheets first");
        }
        if ($this->inLessMode()) {
            $this->printLessStylesheets($extraOptions);
        } else {
            $this->printCssStylesheets($extraOptions);
        }

        $this->headPrinted = true;
    }

    /**
     * @return boolean
     */
    public function isHeadPrinted()
    {
        return $this->headPrinted;
    }

    /**
     * @param array $extraOptions
     */
    private function printLessStylesheets(array $extraOptions)
    {
        if (isset($extraOptions[ Lessnichy::JS ])) {
            $lessJsUrl = $extraOptions[ Lessnichy::JS ];
            unset($extraOptions[ Lessnichy::JS ]);
        } else {
            $lessJsUrl = $this->baseUrl . '/js/less-1.7.0.min.js';
        }

        $lessJsOptions = array(Lessnichy::WATCH_INTERVAL => 2500, Lessnichy::DEBUG => true);
        $lessJsOptions = array_merge($lessJsOptions, $extraOptions);

        $lessJsOptions['lessnichy'] = array(
            'url' => $this->baseUrl
        );
        $watch = (bool) $lessJsOptions[ Lessnichy::DEBUG ];
        unset($lessJsOptions[ Lessnichy::DEBUG ]);
        $lessJsOptions['env'] = $watch ? 'development' : 'production';

        print "<script type='text/javascript'>"
              . "var less = " . json_encode($lessJsOptions) . ";\n"
              . "</script>\n";

        foreach ($this->stylesheets as $lessStylesheetUrl) {
            if (self::isLess($lessStylesheetUrl)) {
                print "<link rel='stylesheet/less' type='text/css' href='$lessStylesheetUrl' />\n";
            } else {
                print "<link rel='stylesheet' type='text/css' href='{$lessStylesheetUrl}'>";
            }
        }
        // if no url provided, assume that less.js connected manually
        if ($lessJsUrl) {
            print "<script type='text/javascript' src='$lessJsUrl'></script>\n";
            if ($watch) {
                print "<script type='text/javascript'> less.watch();\nless.env;\n"."</script>\n";
            }
        }
    }

    /**
     * @see $enableLessMode
     * @return bool
     */
    private function inLessMode()
    {
        if (($trigger = $this->lessModeTrigger) instanceof Closure) {
            return $trigger();
        }
        return $this->enableLessMode;
    }

    /**
     * Outputs <link> tags to compiled css
     * @param array $extraOptions
     */
    private function printCssStylesheets(array $extraOptions = array())
    {
        foreach ($this->stylesheets as $lessStylesheetUrl) {
            print "<link rel='stylesheet' type='text/css' href='{$lessStylesheetUrl}.css'>";
        }
    }
}
 