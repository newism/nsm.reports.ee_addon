<?php

/**
 * NSM Reports Simple Example Class
 *
 * @package NsmReports
 * @subpackage Channels_simple_report
 * @version 1.0.4
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au> 
 * @copyright Copyright (c) 2007-2011 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html
 */

/**
 * Report object
 *
 * @package NsmReports
 */
class Channels_simple_report extends Nsm_report_base {
	
	/**
	 * Displays the report name in the control panel
	 *
	 * @var string
	 * @access protected
	 **/
	protected $title = 'Channels: Simple Demo';
	
	/**
	 * Basic description of the report
	 *
	 * @var string
	 * @access protected
	 **/
	protected $notes = 'Demonstrates a simple report example by retrieving all channel titles';
	
	/**
	 * Name and/or company of the report's creator
	 *
	 * @var string
	 * @access protected
	 **/
	protected $author = 'Iain Saxon - Newism';
	
	/**
	 * A URL to the report's documentation (optional)
	 *
	 * @var string
	 * @access protected
	 **/
	protected $docs_url = 'http://www.newism.com.au';
	
	/**
	 * Version number of report as a string to preserve decimal points
	 *
	 * @var string
	 * @access protected
	 **/
	protected $version = '1.0.3';
	
	/**
	 * Report type as either 'simple' or 'complex'
	 *
	 * @var string
	 * @access protected
	 **/
	protected $type = 'simple';
	
	/**
	 * Valid report output types
	 *
	 * @var array
	 * @access public
	 **/
	public $output_types = array(
									'browser' => 'View in browser',
									'csv' => 'Comma-Seperated Values (CSV)',
									'tab' => 'Tab-Seperated Values (TSV)',
									'html' => 'HyperText Markup Language (HTML)',
									'xml' => 'eXtensible Markup Language (XML)'
								);
	
	/**
	 * Default report configuration options with '_output' as a minumum entry
	 *
	 * @var array
	 * @access protected
	 **/
	protected $config = array(
		'_output' => 'browser'
	);
	
	/**
	 * Stores the generated SQL statement used by the report
	 *
	 * @var string
	 * @access public
	 **/
	public $sql = "";
	
	/**
	 * The file-path where the report is located and is used for including report views
	 *
	 * @var string
	 * @access public
	 **/
	public $report_path = '';
	
	/**
	 * The file-path where the report output can be stored on the server
	 *
	 * @var string
	 * @access public
	 **/
	public $cache_path = '';
	
	/**
	 * Stores any report errors that are encountered and saved at report run
	 *
	 * @var bool|string By default error is a boolean value and a string if an error is stored
	 * @access public
	 **/
	public $error = false;
	
	/**
	 * PHP5 constructor function.
	 *
	 * Prepares instance of ExpressionEngine for object scope and sets report path
	 * Report classes extending this class should always call the parent's constructor
	 * 
	 * @access public
	 * @return void
	 **/
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Generates the SQL query string and returns the results as an array
	 * 
	 * @access public
	 * @return array Array of database results
	 */
	public function generateResults()
	{
		$sql = "SELECT
			`p`.`entry_id` AS `ID`,
			`p`.`title` AS `Title`,
			`p`.`url_title` AS `URL Title`,
			`c`.`channel_title` AS `Channel Name`
		FROM `exp_channel_titles` AS `p`
		LEFT JOIN `exp_channels` AS `c`
			ON `c`.`channel_id` = `p`.`channel_id`
		ORDER BY `p`.`channel_id`";
		
		$query = $this->EE->db->query($sql);
		if ($query == false){
			return false;
		}
		return $query->result_array();
	}
	
}