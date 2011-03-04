<?php if (! defined('BASEPATH')) die('No direct script access allowed');

if(!class_exists('Nsm_reports_ext')){ include(PATH_THIRD."nsm_reports/ext.nsm_reports.php"); }
if(!class_exists('Nsm_report')){ include(PATH_THIRD."nsm_reports/models/nsm_report.php"); }
if(!class_exists('Nsm_saved_report')){ include(PATH_THIRD."nsm_reports/models/nsm_saved_report.php"); }

/**
 * NSM Reports Tag methods
 *
 * @package			NsmReports
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-reports
 * @see				http://expressionengine.com/public_beta/docs/development/modules.html#control_panel_file
 */

class Nsm_reports {

	/**
	 * PHP5 constructor function.
	 *
	 * @access public
	 * @return void
	 **/
	function __construct()
	{
		// set the addon id
		$this->addon_id = strtolower(substr(__CLASS__,0));
	
		// Create a singleton reference
		$EE =& get_instance();

		// define a constant for the current site_id rather than calling $PREFS->ini() all the time
		if (defined('SITE_ID') == FALSE)
			define('SITE_ID', $EE->config->item('site_id'));

		// Init the cache
		// If the cache doesn't exist create it
		if (! isset($EE->session->cache[$this->addon_id]))
			$EE->session->cache[$this->addon_id] = array();

		// Assig the cache to a local class variable
		$this->cache =& $EE->session->cache[$this->addon_id];
		$this->cache_path = APPPATH.'cache/' . $this->addon_id . "/" ;
		
		$EE->load->model('nsm_reports_model');
		$NsmReportsExt = new Nsm_reports_ext();
		$this->settings = $NsmReportsExt->settings;
		
	}
	
	
}