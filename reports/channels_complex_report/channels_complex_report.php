<?php

/**
 * NSM Reports Complex Example Class
 *
 * @package NsmReports
 * @subpackage Channels_complex_report
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
class Channels_complex_report extends Nsm_report_base {
	
	 /**
	 * Displays the report name in the control panel
	 *
	 * @var string
	 * @access protected
	 **/
	protected $title = 'Channels: Complex Demo';
	
	/**
	 * Basic description of the report
	 *
	 * @var string
	 * @access protected
	 **/
	protected $notes = 'Demonstrates a complex report example by retrieving all channel titles and providing configuration options to users';
	
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
		
		$entry_date_modes = array(
			'after' => ">",
			'before' => "<",
			'equal' => "=",
			'not' => "<>"
		);
		
		$channel_cond = ($config['channel_filter'])
			? " AND `t`.`channel_id` = '".intval($config['channel_filter'])."'"
			: false;
		$status_cond = ($config['status_filter'])
			? " AND `t`.`status` = '".$config['status_filter']."'"
			: false;
		
		
		$this->EE->load->helper('date');
		$date_cond = ($config['date_filter_mode'])
			? " AND `t`.`entry_date` ".$entry_date_modes[$config['date_filter_mode']].
				" '".local_to_gmt(human_to_unix($config['date_filter']))."'"
			: false;

		$sql = "SELECT
			`t`.`entry_id` AS `id`,
			`t`.`title` AS `name`,
			`t`.`entry_date` AS `created_at`,
			`t`.`url_title` AS `url_title`,
			`t`.`status` AS `status`,
			`t`.`channel_id` AS `channel_id`,
			`c`.`channel_title` AS `channel_name`
		FROM `exp_channel_titles` AS `t`
		LEFT JOIN `exp_channels` AS `c`
			ON `c`.`channel_id` = `t`.`channel_id`
		WHERE `t`.`channel_id` > 0 " .
			$channel_cond . 
			$status_cond . 
			$date_cond .
		"
		ORDER BY `t`.`channel_id`,
			`t`.`title`";
		
		$query = $this->EE->db->query($sql);
		if ($query == false){
			return false;
		}
		return $query->result_array();
	}
	
	/**
	 * Renders a View from the report results to display in the browser
	 *
	 * @access public
	 * @param object $results Array of report results.
	 * @return string Result data represented as HTML
	 **/
	public function outputBrowser($results)
	{
		$rows = array();
		
		foreach($results as $entry_i => $entry){
			$rows[$entry_i] = $entry;
			$rows[$entry_i]['created_at'] = $this->EE->localize->set_human_time($entry['created_at']);
			$rows[$entry_i]['entry_url'] = BASE.AMP.'C=content_publish&M=entry_form&channel_id='.$entry['channel_id'].'&entry_id='.$entry['id'];
		}
		
		$data = array(
			'rows' => $rows,
			'input_prefix' => __CLASS__
		);
		
		if(APP_VER < '2.1.5') {
			// EE < .2.2.0
			return $this->EE->load->_ci_load(array(
				'_ci_vars' => $data,
				'_ci_path' => $this->report_path . 'views/output_browser.php',
				'_ci_return' => true
			));
		}else{
			$this->EE->load->add_package_path($this->report_path);
			return $this->EE->load->view('output_browser', $data, TRUE);
		}
	}
	
	/**
	 * Builds HTML string from report results
	 *
	 * @access public
	 * @param object $results Array of report results.
	 * @return string Result data represented as HTML
	 **/
	public function outputHTML($results)
	{
		$html = "";
		
		$columns = "";
		$rows = "";
		
		$num_rows = count($results);
		
		$columns .= '<th scope="col">ID</th>';
		$columns .= '<th scope="col">Title</th>';
		$columns .= '<th scope="col">Date Created</th>';
		$columns .= '<th scope="col">Status</th>';
		$columns .= '<th scope="col">Channel</th>';
		
		foreach($results as $row_i => $row){
			$rows .= '<tr>';
			$rows .= 	'<th scope="row">' . $row['id'] . '</th>';
			$rows .= 	'<td>' . $row['name'] . '</td>';
			$rows .= 	'<td>' . $this->EE->localize->set_human_time($row['created_at']) . '</td>';
			$rows .= 	'<td>' . ucwords($row['status']) . '</td>';
			$rows .= 	'<td>' . $row['channel_name'] . '</td>';
			$rows .= '</tr>';
		}
		$thead = '<thead><tr>' . $columns . '</tr></thead>';
		$tfoot = '<tfoot><tr><td colspan="5">' . $num_rows . ' entries found</td></tr></tfoot>';
		$tbody = '<tbody>' . $rows . '</tbody>';
		
		$html .= '<table class="data">' .
		 			'<thead>' .
						'<tr>' . $columns . '</tr>' .
					'</thead>' .
					'<tfoot>' . 
						'<tr>' . 
							'<td colspan="5">' . $num_rows . ' entries found</td>' . 
						'</tr>' . 
					'</tfoot>' .
					'<tbody>' . 
						$rows . 
					'</tbody>' .
				'</table>';
		return $html;
		
	}
	
	/**
	 * Builds Comma-Seperated-Value string from report results
	 *
	 * @access public
	 * @param object $results Array of report results.
	 * @return string Result data represented as a CSV
	 **/
	public function outputCSV($results)
	{
		$csv = '"ID","Title","Date Created","Status","Channel"';
		foreach($results as $row_i => $row){
			$csv .= "\n" . '"'. $row['id'] . '","' . $row['name'] . '","' . $this->EE->localize->set_human_time($row['created_at']) . '","' . ucwords($row['status']) . '","' . $row['channel_name'] . '"';
		}
		return $csv;
	}
	
	/**
	 * Builds Tab-Seperated-Value string from report results
	 *
	 * @access public
	 * @param object $results Array of report results.
	 * @return string Generated TSV string
	 **/
	public function outputTSV($results)
	{
		$tsv = '"ID"'."\t".'"Title"'."\t".'"Date Created"'."\t".'"Status"'."\t".'"Channel"';
		foreach($results as $row_i => $row){
			$tsv .= "\n" . 
					'"' . $row['id'] . '"' . "\t" .
					'"' . $row['name'] . '"' . "\t" .
					'"' . $this->EE->localize->set_human_time($row['created_at']) . '"' . "\t" .
					'"' . ucwords($row['status']) . '"' . "\t" .
					'"' . $row['channel_name'] . '"';
		}
		return $tsv;
	}
	
	/**
	 * Builds an XML string from report results
	 *
	 * @access public
	 * @param object $results Array of report results.
	 * @return string Result data represented as an XML string
	 **/
	public function outputXML($results)
	{
		$xml = '<?xml version="1.0"?>';
		
		$columns[] = '<column><id>column_0</id><name><![CDATA[' .'ID'. ']]></name></column>';
		$columns[] = '<column><id>column_1</id><name><![CDATA[' .'Title'. ']]></name></column>';
		$columns[] = '<column><id>column_2</id><name><![CDATA[' .'Date Created'. ']]></name></column>';
		$columns[] = '<column><id>column_3</id><name><![CDATA[' .'Status'. ']]></name></column>';
		$columns[] = '<column><id>column_4</id><name><![CDATA[' .'Channel'. ']]></name></column>';
		
		foreach($results as $row_i => $row){
			$row_data = '<row>';
			$row_data .= 	'<column_0>'.$row['id'].'</column_0>';
			$row_data .= 	'<column_1>'.'<![CDATA[' . $row['name'] . ']]>'.'</column_1>';
			$row_data .= 	'<column_2>'.'<![CDATA[' . $this->EE->localize->set_human_time($row['created_at']) . ']]>'.'</column_2>';
			$row_data .= 	'<column_3>'.'<![CDATA[' . ucwords($row['status']) . ']]>'.'</column_3>';
			$row_data .= 	'<column_4>'.'<![CDATA[' . $row['channel_name'] . ']]>'.'</column_4>';
			$row_data .= '</row>';
			$rows[] = $row_data;
		}
		$xml .= '<result_data><columns>'.implode('', $columns).'</columns><rows>'.implode('', $rows).'</rows></result_data>';
		return $xml;
	}
	
}