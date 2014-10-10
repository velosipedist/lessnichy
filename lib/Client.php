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
     */
    function __construct($baseUrl)
    {
        $baseUrl = rtrim($baseUrl, '/');
        if (php_sapi_name() == 'cli-server') {
            $baseUrl .= '/index.php';
        }
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $stylesheetUrl
     * @return bool
     */
    public static function isLess($stylesheetUrl)
    {
        return (bool) preg_match('/\.less$/i', parse_url($stylesheetUrl, PHP_URL_PATH));
    }

    /**
     * @param string $stylesheetUrl
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
    }

    /**
     * @param array $extraOptions use LESS::* constants
     */
    public function head(array $extraOptions = array() /* todo optional stream handler besides stdout*/)
    {
        if ($this->headPrinted) {
            return;
        }
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
     * @param array $extraOptions
     */
    private function printLessStylesheets(array $extraOptions)
    {
        $lessJsOptions = array(
            Lessnichy::WATCH_INTERVAL => 2500,
            Lessnichy::WATCH_AUTOSTART => false,
            Lessnichy::WATCH => true,
            Lessnichy::RANDOMIZE_LESS_URL => true,
            Lessnichy::DUMP_LINES => 'mediaQuery',
        );

        $lessJsOptions = array_merge($lessJsOptions, $extraOptions);

        $lessJsUrl = $this->baseUrl . '/js/less-1.7.5.min.js';

        $watch = (bool) $lessJsOptions[Lessnichy::WATCH];
        unset($lessJsOptions[Lessnichy::WATCH]);

        $watchAutostart = (bool) $lessJsOptions[Lessnichy::WATCH_AUTOSTART];
        unset($lessJsOptions[Lessnichy::WATCH_AUTOSTART]);
        $randomize = (bool) $lessJsOptions[Lessnichy::RANDOMIZE_LESS_URL];
        unset($lessJsOptions[Lessnichy::RANDOMIZE_LESS_URL]);

        $lessJsOptions['postProcessor'] = 'Lessnichy.postProcess';
        $lessJsOptions['lessnichy'] = array(
            'url' => $this->baseUrl
        );
        $lessJsOptions['env'] = $watch ? 'development' : 'production';
        $this->js("var less = " . json_encode($lessJsOptions) . ";");

        foreach ($this->stylesheets as $lessStylesheetUrl) {
            if (self::isLess($lessStylesheetUrl)) {
                $lessStylesheetUrl .= ($randomize ? '?'.mt_rand(1, PHP_INT_MAX-1) : '');
                $this->lessFile($lessStylesheetUrl);
            } else {
                $this->cssFile($lessStylesheetUrl);
            }
        }
        $this->jsFile($lessJsUrl);
        if ($watch) {
            if ($watchAutostart) {
                $this->js("less.watch();\nless.env;\n");
            } else {
                $this->js("less.watch();\nless.env;\n;less.unwatch();");
            }
        }

        $client = $this;
        register_shutdown_function(
            function () use ($client) {
                // when Lessnichy used, register client watching libs and resources
                //todo link to gzipped glued js
                $baseurl = $client->getBaseUrl();
                $client->jsFile("{$baseurl}/js/clean-css.min.js");
                $client->jsFile("{$baseurl}/js/lessnichy.js");
            }
        );
    }

    /**
     * Outputs <link> tags to compiled css
     * @param array $extraOptions
     */
    private function printCssStylesheets(array $extraOptions = array())
    {
        foreach ($this->stylesheets as $lessStylesheetUrl) {
            $this->cssFile($lessStylesheetUrl . '.css');
        }
        register_shutdown_function(
            function () {
                print '<script>try{console.log("Lessnichy is in CSS mode")}catch(e){}</script>';
            }
        );
    }

    /**
     * Does client
     * @see $enableLessMode
     * @return bool
     */
    private function inLessMode()
    {
        //todo ability to sitch less mode on-the-fly from browser
        if (($trigger = $this->lessModeTrigger) instanceof Closure) {
            return (bool)$trigger();
        }
        if ($this->enableLessMode) {
            foreach ($this->stylesheets as $href) {
                if (substr($href, -4, 4) == '.css') {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param $stylesheetUrl
     */
    public function cssFile($stylesheetUrl)
    {
        print "<link rel='stylesheet' type='text/css' href='{$stylesheetUrl}'>";
    }

    /**
     * @param $js
     */
    public function js($js)
    {
        print "<script type='text/javascript'>\n" . $js . ";\n</script>\n";
    }

    /**
     * @param $stylesheetUrl
     */
    public function lessFile($stylesheetUrl)
    {
        print "<link rel='stylesheet/less' type='text/css' href='$stylesheetUrl' />\n";
    }

    /**
     * @param $scriptUrl
     */
    private function jsFile($scriptUrl)
    {
        print "<script type='text/javascript' src='{$scriptUrl}'></script>\n";
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}
 