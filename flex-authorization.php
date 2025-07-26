<?php

namespace Ababilithub\FlexAuthorization;

/**
 * FlexAuthorization
 *
 * @package ababilithub/flex-authorization
 *
 * @wordpress-plugin
 * Plugin Name:       FlexAuthorization
 * Plugin URI:        https://ababilithub.com/wp-plugin/flex-authorization
 * Description:       A flexible, Composer-managed, package to Interactive Land Record Management by following modern WordPress and OOP PHP best practices by Ababil IT Hub.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * WC requires at least: 3.0.9
 * WC tested up to:   6.5
 * Requires Plugins:  
 * Author:            Ababil IT Hub
 * Author URI:        https://ababilithub.com/
 * Author Email:      ababilithub@gmail.com
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       flex-authorization
 * Domain Path:       /language
 *
 * Contributors:
 *  - Ababil IT Hub (ababilithub@gmail.com, https://ababilithub.com/)
 *  - Md Shafiul Alam (cse.shafiul@gmail.com, https://ababilithub.com/)
 */

(defined('ABSPATH') && defined('WPINC')) || die();

require_once __DIR__ . '/bootstrap.php';

use Ababilithub\{
   FlexAuthorization\Package\Package,
};
 
$package = Package::getInstance();
     
register_activation_hook(__FILE__, [$package, 'activate']);
register_deactivation_hook(__FILE__, [$package, 'deactivate']);
