<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NSM Reports Plugin
 * 
 * Generally a module is better to use than a plugin if if it has not CP backend
 *
 * @package			NsmReports
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-reports
 * @see 			http://expressionengine.com/public_beta/docs/development/plugins.html
 */

/**
 * Plugin Info
 *
 * @var array
 */
$plugin_info = array(
	'pi_name' => 'NSM Reports Plugin',
	'pi_version' => '0.0.1',
	'pi_author' => 'Leevi Graham',
	'pi_author_url' => 'http://leevigraham.com/',
	'pi_description' => 'Plugin description',
	'pi_usage' => "Refer to the included README"
);

class Nsm_reports{

	/**
	 * The return string
	 *
	 * @var string
	 */
	var $return_data = "";

	function Nsm_reports()
	{
		$EE =& get_instance();
		$this->return_data = "NSM Reports Output";
	}

}