<?php
/**
 * This is a lightweight,tiny, simple mvc framework of PHP language.
 * Temporarily Name T
 *　Only for learning & exchanges.
 * Version 1.0
 * Date 2012-10-15
 */

defined('__APPPATH__') or define( '__APPPATH__', dirname( __FILE__ ) . '/app' );	

$base = dirname(__FILE__).'/t/base.php';
$config = __APPPATH__.'/config/config.php';

require_once($base);
T::createApp($config)->run();
