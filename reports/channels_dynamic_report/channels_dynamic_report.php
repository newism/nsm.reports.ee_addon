<?php

/**
 * NSM Reports Dynamic Example Class
 *
 * @package NsmReports
 * @subpackage Channels_dynamic_report
 * @version 1.0.7
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
class Channels_dynamic_report extends Nsm_report_base {
	
	 /**
	 * Displays the report name in the control panel
	 *
	 * @var string
	 * @access protected
	 **/
	protected $title = 'Channels: Dynamic Demo';
	
	/**
	 * Basic description of the report
	 *
	 * @var string
	 * @access protected
	 **/
	protected $notes = 'Demonstrates a complex report example by retrieving all channel titles and providing configuration options to users while returning all custom fields for the channels';
	
	/**
	 * Name and/or company of the report's creator
	 *
	 * @var string
	 * @access protected
	 **/
	protected $author = 'Iain Saxon @ Newism';
	
	/**
	 * A URL to the report's documentation (optional)
	 *
	 * @var string
	 * @access protected
	 **/
	protected $docs_url = 'http://ee-garage.com/nsm-reports/user-guide';
	
	/**
	 * Version number of report as a string to preserve decimal points
	 *
	 * @var string
	 * @access protected
	 **/
	protected $version = '1.0.7';
	
	/**
	 * Report type as either 'simple' or 'complex'
	 *
	 * @var string
	 * @access protected
	 **/
	protected $type = 'complex';
	
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
		'_output' => 'browser',
		'channel_filter' => false,
		'status_filter' => false,
		'date_filter' => false,
		'date_filter_mode' => false
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
	 * Returns Control Panel HTML to configure report if report type is complex or returns 'false' if simple
	 *
	 * @access public
	 * @return string|bool Configuration HTML or false 
	 **/
	public function configHTML()
	{
		$channels = $this->EE->db->query('
			SELECT 
				`exp_channels`.`channel_id`, 
				`exp_channels`.`channel_title` AS `title`
			FROM `exp_channels`
			ORDER BY `channel_title`'
		);
		$status_options = $this->EE->db->query('
			SELECT DISTINCT
				`exp_channel_titles`.`status`
			FROM `exp_channel_titles`
			ORDER BY `exp_channel_titles`.`status`'
		);
		
		$data = array(
					'config' => $this->config,
					'channels' => $channels->result_array(),
					'status_options' => $status_options->result_array()
				);
		
		$this->EE->cp->add_js_script(array('ui' => 'datepicker'));
		$this->EE->cp->add_to_foot('<link rel="stylesheet" href="'.BASE.AMP.'C=css'.AMP.'M=datepicker" type="text/css" media="screen" />');
		
		$default_date = $this->EE->localize->set_localized_time() * 1000;
		$current_time = date("' H:i'", gmt_to_local(now()));
		
		$behaviours = <<<BEHAVIOURS
<script type="text/javascript">
/* <![CDATA[ */
$(function(){
	$("#report_config_new_review_date").datepicker({ 
		dateFormat: $.datepicker.W3C + {$current_time},
		defaultDate: new Date({$default_date})
	});
});
/* ]]> */
</script>
BEHAVIOURS;
		
		$this->EE->cp->add_to_foot($behaviours);
		
		if(APP_VER < '2.1.5') {
			// EE < .2.2.0
			return $this->EE->load->_ci_load(array(
				'_ci_vars' => $data,
				'_ci_path' => $this->report_path . 'views/configuration.php',
				'_ci_return' => true
			));
		}else{
			$this->EE->load->add_package_path($this->report_path);
			return $this->EE->load->view('configuration', $data, TRUE);
		}
	}
	
	/**
	 * Generates the SQL query string and returns the results as an array
	 * 
	 * @access public
	 * @return array Array of database results
	 */
	public function generateResults()
	{
		$config = $this->config;
		
		$db =& $this->EE->db;
		
		$entry_date_modes = array(
			'after' => ">",
			'before' => "<",
			'equal' => "=",
			'not' => "<>"
		);
		
		// GET FIELDS
		$db->select('field_id, field_label');
		$db->from('channel_fields');
		$db->join('channels', 'channels.field_group = channel_fields.group_id', 'left');
		if (!empty($config['channel_filter'])) {
			$db->where('channel_id', intval($config['channel_filter']));
		}
		$get_channel_fields = $db->get();
		
		// GET ENTRIES
		$db->from('channel_titles');
		$db->join('channel_data', 'channel_data.entry_id = channel_titles.entry_id', 'left');
		$db->join('channels', 'channels.channel_id = channel_titles.channel_id', 'left');
		$db->order_by('channel_title', 'asc');
		
		$db->select('channel_titles.entry_id AS `ID`');
		$db->select('channel_titles.title AS `Title`');
		$db->select('channel_titles.entry_date AS `Date`');
		$db->select('channels.channel_title AS `Channel`');
		
		foreach ($get_channel_fields->result() as $row) {
			$db->select("channel_data.field_id_{$row->field_id} AS `{$row->field_label}`");
		}
		
		if (!empty($config['channel_filter'])) {
			$db->where('channel_titles.channel_id ', $config['channel_filter']);
		}
		
		if (!empty($config['status_filter'])) {
			$db->where('channel_titles.status ', $config['status_filter']);
		}
		
		if (!empty($config['date_filter_mode'])) {
			$db->where(
				'channel_titles.entry_date '.$entry_date_modes[$config['date_filter_mode']],
				local_to_gmt(human_to_unix($config['date_filter']))
			);
		}
		
		$get_entries = $db->get();
		if (!$get_entries->num_rows()) {
			return false;
		}
		
		return $get_entries->result_array();
	}
	
	
}