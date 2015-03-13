<?php

/**
 * KwmmbAssetic class
 */
class KwmmbAssetic
{
    //--------------------------------------------------------------------------
    //                             Properties
    public static $plugin_dir = KWMMB_DIR;
    public static $plugin_url = KWMMB_URL;
    //                             Properties
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                           Public Methods

    /**
     * Get the plugin asset file
     *
     * @param string $type (script|image|animation|stylesheet|php|sql)
     * @param string $name
     *
     * @return string
     */
    public static function get($type, $name)
    {
        switch ($type) {
            case 'script':
                $path = self::$plugin_url."assets/js/$name.js";
                break;
            case 'image':
                $path = self::$plugin_url."assets/images/$name.png";
                break;
            case 'animation':
                $path = self::$plugin_url."assets/images/$name.gif";
                break;
            case 'stylesheet':
                $path = self::$plugin_url."assets/css/$name.css";
                break;
            case 'php':
                $path = self::$plugin_dir."/$name.php";
                if (!file_exists($path)) {
                    throw new Exception("Assetic: Could not locate $path");
                }
                break;
            case 'sql':
                $path = self::$plugin_dir."/db/$name.sql";
            default:
                $path = '';
                break;
        }

        return $path;
    }

    /**
     * Render a template
     *
     * @param string $template
     * @param array $params
     *
     * @return string
     */
    public static function render($template, $params = array())
    {
        kwmmb_log("Rendering $template");
        ob_start();
            extract($params);
            include self::get('php', $template);
            $rendered_template = ob_get_contents();
        ob_end_clean();

        return $rendered_template;
    }
    //                           Public Methods
    //--------------------------------------------------------------------------
}
