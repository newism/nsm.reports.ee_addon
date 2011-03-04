<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NSM Reports Accessory
 *
 * @package			NsmReports
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com> - Technical Director, Newism
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-reports
 * @see				http://expressionengine.com/public_beta/docs/development/accessories.html
 */

class Nsm_reports_acc 
{
	var $id;
	var $version		= '0.0.1';
	var $name			= 'NSM Reports Accessory';
	var $description	= 'Example accessory for NSM Reports.';
	var $sections		= array();

	function set_sections()
	{
		$this->id = strtolower(__CLASS__);
		$this->sections['Title'] = "Content";
	}
}