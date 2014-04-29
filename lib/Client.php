<?php
namespace Lessnichy;

class Client
{
    private $baseUrl;
    private $stylesheets;
    private $headPrinted = false;

    function __construct($baseUrl)
    {
        $baseUrl       = rtrim($baseUrl, '/');
        if(php_sapi_name() == 'cli-server'){
            $baseUrl .= '/index.php';
        }
        $this->baseUrl = $baseUrl;
    }

    public function add(array $lessStylesheets = [])
    {
        foreach ($lessStylesheets as $less) {
            $this->stylesheets[] = $less;
        }

        $baseurl = $this->baseUrl;
        $client  = $this;

        register_shutdown_function(
            function () use ($client, $baseurl) {
                // when Lessnichy used, register client watching libs and resources
                if ($client->isHeadPrinted()) {
                    print "<script src='{$baseurl}/js/lessnichy.js'></script>";
                }
            }
        );
    }

    public function head(array $options = [] /* todo optional stream handler besides stdout*/)
    {
        if (empty($this->stylesheets)) {
            throw new \LogicException("Add some stylesheets first");
        }
        if (isset($options[Lessnichy::JS])) {
            $lessJsUrl = $options[Lessnichy::JS];
            unset($options[Lessnichy::JS]);
        } else {
            $lessJsUrl = false;
        }

        $jsonDefaults  = [Lessnichy::WATCH_INTERVAL => 2500, Lessnichy::DEBUG => true];
        $optionsMerged = array_merge($jsonDefaults, $options);
        $json          = json_encode($optionsMerged);

        $optionsMerged['lessnichy'] = [
            'url' => $this->baseUrl
        ];

        //        setcookie('Lessnichy.')

        print "<script type='text/javascript'>var less = " . $json . ";</script>\n";

        foreach ($this->stylesheets as $url) {
            print "<link rel='stylesheet/less' type='text/css' href='$url' />\n";
        }
        // if no url provided, assume that less.js connected manually
        if ($lessJsUrl) {
            print "<script type='text/javascript' src='$lessJsUrl'></script>\n";
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
}
 