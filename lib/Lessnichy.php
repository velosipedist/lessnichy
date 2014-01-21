<?php

/**
 * Lessnichy magic facade
 */
class Lessnichy
{
    private static $started;

    // site webroot
    private static $webroot;
    private static $headPrinted = false;

    // on which url lessnichy is located
    private static $baseUrl;
    private static $stylesheets;

    public static function add(array $stylesheets = [])
    {
        if(!self::$started){
            self::yodle();
        }
        foreach ($stylesheets as $less) {
            self::$stylesheets[] = $less;
        }

        $webroot = self::$webroot;
        $baseurl = self::$baseUrl;


        register_shutdown_function(function() use ($webroot, $baseurl){
            if(Lessnichy::getHeadPrinted()){
                print "<script src='{$baseurl}/js/lessnichy.js'></script>";
            }
        });
    }

    private static function yodle()
    {
        self::$webroot = self::detectWebRoot();
        self::$baseUrl = substr(realpath(__DIR__), strlen(self::$webroot));
//        $path = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
        //todo detect site webroot
//        $siteBaseUrl = substr(self::$baseUrl, );
    }

    private static function detectWebRoot()
    {
        //todo make more reliable detection
        $webroot = $startDir = dirname($_SERVER['SCRIPT_FILENAME']);
        while(file_exists($startDir.'/index.php')){
            $webroot = $startDir;
            $startDir = realpath($startDir.'/..');
        }
        return $webroot;
    }

    public static function head(array $options = []/* todo optional stream handler besides stdout*/)
    {
        if(empty(self::$stylesheets)){
            throw new LogicException("Add some stylesheets first");
        }
        if(isset($options['less'])){
            $lessUrl = $options['less'];
            unset($options['less']);
        }else{
            $lessUrl = false;
        }

        $jsonDefaults = ['poll'=> 2500, 'watch'=> true];
        $optionsMerged = array_merge($jsonDefaults, $options);
        $json = json_encode($optionsMerged);

        $optionsMerged['lessnichy'] = [
            'url'=> self::$baseUrl
        ];

//        setcookie('Lessnichy.')

        print "<script type='text/javascript'>var less = $json;</script>\n";

        foreach (self::$stylesheets as $url) {
            print "<link rel='stylesheet/less' type='text/css' href='$url' />\n";
        }
        if($lessUrl){
            print "<script type='text/javascript' src='$lessUrl'></script>\n";
        }

        self::$headPrinted = true;
    }

    /**
     * @return boolean
     */
    public static function getHeadPrinted()
    {
        return self::$headPrinted;
    }
}

if(!class_exists('LESS')){
    class_alias('Lessnichy','LESS');

}
