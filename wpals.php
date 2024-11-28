<?php
/**
 * @package WPALS
 * @version 0.0.1
 */
/*
 * Plugin Name: WPALS - WP Auto Link Shortener
 * Plugin URI: https://mad.xion.my.id/
 * Description: This plugins will automatically shorten your permalink into third parties shortener.
 * Author: Madxion Corp
 * Version: 0.0.1
 * Author URI: http://profiles.wordpress.org/madxioncorp/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


include_once("autoload.php");
include "functions.php";

new Wpals();

