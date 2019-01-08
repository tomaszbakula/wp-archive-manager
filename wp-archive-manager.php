<?php
/*
Plugin Name:  WP Archive Manager
Description:  Simple WordPress custom post type archive manager.
Version:      1.0.0
Author:       Tomasz Bakula
Author URI:   http://tmk.ninja
*/

require_once 'class-archive-manager.php';
require_once 'class-archive-manager-admin.php';
require_once 'display-functions.php';

$ARCHIVE_MANAGER = new Archive_Manager();