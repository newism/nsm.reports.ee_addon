<?php

/**
 * Config file for NSM Reports
 *
 * @package NsmReports
 * @version 1.0.7
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 */


if(!defined('NSM_REPORTS_VERSION')) {
	define('NSM_REPORTS_VERSION', '1.0.7');
	define('NSM_REPORTS_NAME', 'NSM Reports');
	define('NSM_REPORTS_ADDON_ID', 'nsm_reports');
}

$config['name'] 	= NSM_REPORTS_NAME;
$config["version"] 	= NSM_REPORTS_VERSION;

$config['nsm_addon_updater']['versions_xml'] = 'http://ee-garage.com/nsm-reports/release-notes/feed';
