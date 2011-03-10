<?php

/**
 * NSM Reports Members Example Class
 *
 * @package NsmReports
 * @subpackage Members_report
 * @version 1.0.1
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au> 
 * @copyright Copyright (c) 2007-2011 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://expressionengine-addons.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html
 */

/**
 * Report object
 *
 * @package NsmReports
 */
class Members_report extends Nsm_report_base {
	
	/**
	 * Displays the report name in the control panel
	 *
	 * @var string
	 * @access protected
	 */
	protected $title = 'Members: Complex Demo';
	
	/**
	 * Basic description of the report
	 *
	 * @var string
	 * @access protected
	 */
	protected $notes = 'Demonstrates complex joining of members information and filtering of database results';
	
	/**
	 * Name and/or company of the report's creator
	 *
	 * @var string
	 * @access protected
	 */
	protected $author = 'Iain Saxon - Newism';
	
	/**
	 * A URL to the report's documentation (optional)
	 *
	 * @var string
	 * @access protected
	 */
	protected $docs_url = 'http://www.newism.com.au';
	
	/**
	 * Version number of report as a string to preserve decimal points
	 *
	 * @var string
	 * @access protected
	 */
	protected $version = '1.0.1';
	
	/**
	 * Report type as either 'simple' or 'complex'
	 *
	 * @var string
	 * @access protected
	 */
	protected $type = 'complex';
	
	/**
	 * Valid report output types
	 *
	 * @var array
	 * @access public
	 */
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
	 */
	protected $config = array(
		'_output' => 'browser',
		'member_fields' => array(),
		'additional_fields' => array(),
		'row_limit' => 25,
		'order_by' => ''
	);
	
	/**
	 * Stores the generated SQL statement used by the report
	 *
	 * @var string
	 * @access public
	 */
	public $sql = "";
	
	/**
	 * The file-path where the report is located and is used for including report views
	 *
	 * @var string
	 * @access public
	 */
	public $report_path = '';
	
	/**
	 * The file-path where the report output can be stored on the server
	 *
	 * @var string
	 * @access public
	 */
	public $cache_path = '';
	
	/**
	 * Stores any report errors that are encountered and saved at report run
	 *
	 * @var bool|string By default error is a boolean value and a string if an error is stored
	 * @access public
	 */
	public $error = false;
	
	/**
	 * PHP5 constructor function.
	 *
	 * Prepares instance of ExpressionEngine for object scope and sets report path
	 * Report classes extending this class should always call the parent's constructor
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Returns Control Panel HTML to configure report if report type is complex or returns 'false' if simple
	 *
	 * @access public
	 * @return string|bool Configuration HTML or false 
	 */
	public function configHTML()
	{
		
		$member_fields = $this->getMemberDataFields();
		
		$additional_fields = $this->EE->db->query('
			SELECT
				`exp_member_fields`.`m_field_id`,
				`exp_member_fields`.`m_field_label`
			FROM `exp_member_fields`
			ORDER BY `exp_member_fields`.`m_field_order`'
		);
		
		return $this->EE->load->_ci_load(array(
			'_ci_vars' => array(
				'config' => $this->config,
				'member_fields' => $member_fields,
				'additional_fields' => $additional_fields->result_array()
			),
			'_ci_path' => $this->report_path . "views/configuration.php",
			'_ci_return' => true
		));
	}
	
	/**
	 * Generates the SQL query string and returns the results as an array
	 * 
	 * @access public
	 * @return array Array of database results
	 */
	public function generateResults()
	{
		// prepare the class's configuration
		$config = $this->config;
		
		// select member id from the members table
		$this->EE->db->select('members.`member_id` AS `ID`', false);
		$this->EE->db->from('members');
		
		// prepare a list of member data columns to build SQL with
		$member_fields = $this->getMemberDataFields();
		
		// iterate over the chosen member data columns and add the fields to the CI-AR
		if(count($config['member_fields']) > 0){
			foreach($config['member_fields'] as $field_name){
				$this->EE->db->select('`' . $field_name . '` AS `' . $member_fields[$field_name] . '`', false);
			}
		}
		
		// now prepare the information required to add the custom member fields to the result-set
		if(count($config['additional_fields']) > 0){
			
			// return a CI-DB result containing the chosen custom member column ids and names
			$additional_fields = $this->EE->db->query('
				SELECT 
					`m_field_id` AS `id`, 
					`m_field_label` AS `label`
				FROM `exp_member_fields`
				WHERE `m_field_id` IN 
					(' . implode( ',', $config['additional_fields'] ) . ')
				');
			
			// prepare the database table join in the CI-AR and group by the member id
			$this->EE->db->join('member_data', 'member_data.member_id = members.member_id', 'left');
			$this->EE->db->group_by('members.`member_id`');
			
			// iterate over the returned custom member fields and add them to the CI-AR
			foreach($additional_fields->result_array() as $additional_field){
				$member_data_field = '`m_field_id_'.$additional_field['id'].'` '.
										'AS `'.$additional_field['label'].'`';
				$this->EE->db->select($member_data_field, false);
			}
		}
		
		// add a limit if one is set in the config
		if($config['row_limit'] > 0){
			$this->EE->db->limit($config['row_limit']);
		}
		
		// get the data results
		$query = $this->EE->db->get();
		
		// check for a false result and return false if an error was found
		if ($query == false){
			return false;
		}
		
		// return the results as an array and prepare another array for manipulation
		$original_results = $query->result_array();
		$results = array();
		
		// iterate over the result set and add them to the new results array
		foreach ($original_results as $row_i => $entry) {
			$results[$row_i] = $entry;
			
			// if join date was added to the member columns list then alter value to use EE date-time
			if(in_array('join_date', $config['member_fields'])){
				$value = $results[$row_i][ $member_fields['join_date'] ];
				$results[$row_i][ $member_fields['join_date'] ] = $this->EE->localize->set_human_time($value);
			}
			
			// if last visit date was added to the member columns list then alter value to use EE date-time
			if(in_array('last_visit', $config['member_fields'])){
				$value = $results[$row_i][ $member_fields['last_visit'] ];
				$results[$row_i][ $member_fields['last_visit'] ] = $this->EE->localize->set_human_time($value);
			}
			
		}
		return $results;
	}
	
	/**
	 * Renders a View from the report results to display in the browser
	 *
	 * @access public
	 * @param object $results Array of report results.
	 * @return string Result data represented as HTML
	 */
	public function outputBrowser($results)
	{
		// prepare the class's configuration
		$config = $this->config;
		
		// prepare columns array and row data
		$columns = array();
		$rows = $results;
		
		// iterate over the first row to get column names
		foreach ($rows[0] as $column => $data){
			$columns[] = $column;
		}
		
		// prepare member data fields
		$member_fields = $this->getMemberDataFields();
		
		for( $row_i=0, $row_m=count($rows); $row_i<$row_m; $row_i+=1 ){
			
			// if username was added to the member columns list then alter output to inlcude hyperlink
			if(in_array('username', $config['member_fields'])){
				$value = $rows[$row_i][ $member_fields['username'] ];
				$link = BASE.AMP.'C=myaccount'.AMP.'id='.$rows[$row_i]['ID'];
				$rows[$row_i][ $member_fields['username'] ] = '<a href="'.$link.'">'.$value.'</a>';
			}
			
			// if email address was added to the member columns list then alter output to inlcude hyperlink
			if(in_array('email', $config['member_fields'])){
				$value = $rows[$row_i][ $member_fields['email'] ];
				$link = 'mailto:'.$value;
				$rows[$row_i][ $member_fields['email'] ] = '<a href="'.$link.'">'.$value.'</a>';
			}
			
		}
		
		$data = array(
			'columns' => $columns,
			'rows' => $rows,
			'input_prefix' => __CLASS__
		);
		
		return $this->EE->load->_ci_load(array(
			'_ci_vars' => $data,
			'_ci_path' => $this->report_path."/views/output_browser.php",
			'_ci_return' => true
		));
	}
	
	
	/**
	 * Returns a value-name-pair of all member data fields for use in the report
	 *
	 * @access private
	 * @return array List of member data columns
	 */
	private function getMemberDataFields()
	{
		$fields = array(
			'username' => 'Username',
			'screen_name' => 'Name',
			'email' => 'Email address',
			'join_date' => 'Join date',
			'last_visit' => 'Last visit',
			'total_entries' => 'Entries',
			'total_comments' => 'Comments'
		);
		return $fields;
	}
	
}