<?php

namespace Phytoceutics\ElementorWidgets;

use Phytoceutics\ElementorWidgets\Widgets\Product_Grid;

/**
 * Plugin Name: Phytoceutics Elementor Widgets
 * Description: Elementor Widgets for Phytoceutics
 * Version:     0.1.0
 * Author:      Gibson Silali
 * Author URI:  https://github.com/gissilali
 * Text Domain: phytoceutics-elementor-widgets
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * 
 *
 * @package     PhytoceuticsElementorWidgets
 * @author      Gibson Silali
 * @copyright   2024
 * @license     GPL-2.0+
 *
 * @phytoceutics-elementor-widgets
 */


if (!defined("ABSPATH")) {
    exit;
}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');


final class PhytoceuticsElementorWidgets
{

    const VERSION = '0.1.0';

    const ELEMENTOR_MINIMUM_VERSION = '3.0.0';

    const PHP_MINIMUM_VERSION = '8.0';

    private static $instance = null;
    private function __construct()
    {
        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'init_plugin']);
        add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);

    }

    public function i18n()
    {
        load_plugin_textdomain('phytoceutics-elementor-widgets');
    }

    public function init_plugin()
    {
        // Check php version
        if (version_compare(phpversion(), self::PHP_MINIMUM_VERSION, '<')) {

        }

        // check if elementor is installed
        if (!is_plugin_active('elementor/elementor.php')) {

        }


        // bring in the controls
    }

    public function init_widgets()
    {
        require_once __DIR__ . '/widgets/product-grid.php';

        \Elementor\Plugin::instance()->widgets_manager->register(new Product_Grid());
    }

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new PhytoceuticsElementorWidgets();
        }

        return self::$instance;
    }
}


PhytoceuticsElementorWidgets::get_instance();
