<?php

/**
 * NSM Reports Base Class
 *
 * @package NsmReports
 * @version 0.0.3
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au> 
 * @copyright Copyright (c) 2007-2011 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://expressionengine-addons.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html
 */

/**
 * Base model for NSM Reports
 *
 * @package NsmReports
 */
class Nsm_report_base {
	
	/**
	 * Displays the report name in the control panel
	 *
	 * @var string
	 * @access protected
	 **/
	protected $title = "";
	
	/**
	 * Basic description of the report
	 *
	 * @var string
	 * @access protected
	 **/
	protected $notes = "";
	
	/**
	 * Name and/or company of the report's creator
	 *
	 * @var string
	 * @access protected
	 **/
	protected $author = "";
	
	/**
	 * A URL to the report's documentation (optional)
	 *
	 * @var string
	 * @access protected
	 **/
	protected $docs_url = "";
	
	/**
	 * Version number of report as a string to preserve decimal points
	 *
	 * @var string
	 * @access protected
	 **/
	protected $version = "";
	
	/**
	 * Report type as either 'simple' or 'complex'
	 *
	 * @var string
	 * @access protected
	 **/
	protected $type = "";
	
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
		$this->EE =& get_instance();
		$report = strtolower(get_class($this));
		$this->report_path = PATH_THIRD . "nsm_reports/reports/" . $report . "/";
		$this->cache_path = APPPATH . "cache/nsm_reports/";
	}
	
	/**
	 * PHP5 toString function.
	 *
	 * Returns a string version of class instance
	 *
	 * @access public
	 * @return string This instance's title
	 **/
	public function __toString()
	{
		return $this->title;
	}
	
	/**
	 * Merges incoming configuration options with default options and sets the report config
	 *
	 * @access public
	 * @param array $new_config Report configuration as POST data from form created by 'configHTML' method
	 * @return void
	 **/
	public function setConfig($new_config)
	{
		if(!is_array($new_config)){ $new_config = array(); }
		$this->config = array_merge($this->config, $new_config);
	}
	
	// SQL METHODS
	
	/**
	 * Generates the SQL query string and returns the results as an array
	 * 
	 * @access public
	 * @return array Array of database results
	 */
	public function generateResults()
	{
		return array();
	}


	// OUTPUT METHODS
	
	/**
	 * Returns Control Panel HTML to configure report if report type is complex or returns 'false' if simple
	 *
	 * @access public
	 * @return string|bool Configuration HTML or false 
	 **/
	public function configHTML()
	{
		return false;
	}
	
	/**
	 * Process report and output an array defining the generated report for later file-based processing
	 *
	 * @access public
	 * @param string $output_type Output type to use when formatting report results.
	 * @return array Name, extension and content of generated report
	 **/
	public function generate($output_type="csv")
	{
		$results = false;
		
		$report_name = strtolower(strtolower(get_class($this))).'_'.date('Ymd_Hi');
		
		$results = $this->generateResults();
		
		if(!$results || count($results) < 1){
			$this->error = "No results found";
			return false;
		}
		
		switch($output_type){
			case 'browser':
				$output = $this->outputBrowser($results);
				$extension = "";
			break;
			case 'csv':
				$output = $this->outputCSV($results);
				$extension = "csv";
			break;
			case 'tab':
				$output = $this->outputTSV($results);
				$extension = "txt";
			break;
			case 'html':
				$output = $this->outputHTML($results);
				$output = '<html><head><title>'.$report_name.'</title></head><body>'.$output.'</body></html>';
				$extension = "html";
			break;
			case 'xml':
				$output = $this->outputXML($results);
				$extension = "xml";
			break;
			default:
				$method = 'output_'.$output;
				if(method_exists($this,$method)){
					call_user_func(array($this, $method));
				}else{
					$this->error = "No valid output type specified";
					return false;
				}
			break;
		}
		return array(
			'name' => $report_name,
			'extension' => $extension,
			'content' => $output
		);
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
		$columns = array();
		$rows = $results;
		foreach ($rows[0] as $column => $data){
			$columns[] = $column;
		}
		
		$data = array(
			'columns' => $columns,
			'rows' => $rows,
			'input_prefix' => __CLASS__
		);
		
		return $this->EE->load->_ci_load(array(
			'_ci_vars' => $data,
			'_ci_path' => $this->report_path."../../views/module/report/output_browser.php",
			'_ci_return' => true
		));
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
		
		$thead = "";
		$tfoot = "";
		$tbody = "";
		
		$columns = array();
		$rows = array();
		
		foreach($results as $row_i => $row){
			$col_i = 0;
			$tr = '<tr>';
			foreach($row as $column => $data){
				if($row_i == 0){
					$columns[] = '<th scope="col">' .$column. '</th>';
				}
				$tr .= ($col_i == 0 ? '<th scope="row">' . $data . '</th>' : '<td>' . $data . '</td>');
				$col_i += 1;
			}
			$tr .= '</tr>';
			$rows[] = $tr;
		}
		$thead = '<thead><tr>' . implode('', $columns) . '</tr></thead>';
		$tfoot = '<tfoot><tr><td colspan="' .count($columns). '">' . count($rows) . ' entries found</td></tr></tfoot>';
		$tbody = '<tbody>' . implode('', $rows) . '</tbody>';
		
		$html = '<table class="data">' . $thead . $tfoot . $tbody . '</table>';
		return $html;
		
	}
	
	/**
	 * Builds Comma-Seperated-Value string from report results
	 *
	 * @access public
	 * @param array $results Array of report results.
	 * @return string Result data represented as a CSV
	 **/
	public function outputCSV($results)
	{
		$csv = "";
		$cols = array();
		$rows = array();
		$delimeter = ",";
		$newline = "\n";
		foreach($results as $row_i => $row){
			$row_data = "";
			$col_i = 0;
			foreach($row as $col => $data){
				if($row_i == 0){
					$cols[] = '"' . str_replace('"', '""', ($col) ) . '"';
				}
				$row_data .= ($col_i == 0 ? '' : $delimeter) .
				 				'"' . str_replace('"', '""', ($data) ) . '"';
				$col_i += 1;
			}
			$rows[] = $row_data;
		}
		$csv = implode($delimeter, $cols) . $newline . implode($newline, $rows);
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
		$tsv = "";
		$cols = array();
		$rows = array();
		$delimeter = "\t";
		$newline = "\n";
		foreach($results as $row_i => $row){
			$row_data = "";
			$col_i = 0;
			foreach($row as $col => $data){
				if($row_i == 0){ $cols[] = '"' . str_replace('"', '""', ($col) ) . '"'; }
				$row_data .= ($col_i == 0 ? '' : $delimeter) .
				 				'"' . str_replace('"', '""', ($data) ) . '"';
				$col_i += 1;
			}
			$rows[] = $row_data;
		}
		$tsv = implode($delimeter, $cols) . $newline . implode($newline, $rows);
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
		foreach($results as $row_i => $row){
			$col_i = 0;
			$row_data = '<row>';
			foreach($row as $column => $data){
				if($row_i == 0){
					$columns[] = '<column><id>column_'.$col_i.'</id><name><![CDATA[' .$column. ']]></name></column>';
				}
				$row_data .= '<column_'.$col_i.'>'.(is_int($data) ? $data : '<![CDATA[' . $data . ']]>').'</column_'.$col_i.'>';
				$col_i += 1;
			}
			$row_data .= '</row>';
			$rows[] = $row_data;
		}
		$xml .= '<result_data><columns>'.implode('', $columns).'</columns><rows>'.implode('', $rows).'</rows></result_data>';
		return $xml;
	}
	
	// HELPER METHODS
	
	/**
	 * Forces user to download generated report by sending appropriate HTTP Header details
	 *
	 * @access public
	 * @param string $output_type The report output type to generate.
	 * @return void
	 **/
	public function download($output_type="csv")
	{
		$generated_report = $this->generate($output_type);
		force_download($generated_report['name'].'.'.$generated_report['extension'], $generated_report['content']);
	}
	
	/**
	 * Takes incoming 'file' array and adds information to new zip archive
	 *
	 * @access public
	 * @param array $generated_report An array defining a 'file' that is returned by the 'generate' method.
	 * @return string File-path of zip archive
	 **/
	public function zip_report($generated_report = array())
	{
		$zip_file_path = $this->cache_path . $generated_report['name'] .'.zip';
		$this->EE->load->library('zip');
		$this->EE->zip->add_data($generated_report['name'].'.'.$generated_report['extension'], $generated_report['content']);
		$this->EE->zip->archive($zip_file_path);
		return $zip_file_path;
	}
	
	/**
	 * Sends an email using incoming 'email_config' parameters and 'attachments' collection of reports
	 *
	 * @access public
	 * @param array $email_config An array containing all configuration options related to the email functionality.
	 * @param array $attachments An integer-based collection of arrays with keys 'name' and 'path' used to define email attachments.
	 * @return bool Method success status
	 **/
	public function email_report($email_config = array(), $attachments = array())
	{
		if(!isset($email_config['to'])){ $email_config['to'] = $this->EE->config->item('webmaster_email'); }
		if(!isset($email_config['from_email'])){ $email_config['from_email'] = $this->EE->config->item('webmaster_email'); }
		if(!isset($email_config['from_name'])){ $email_config['from_name'] = $this->EE->config->item('webmaster_name'); }
		if(!isset($email_config['subject'])){ $email_config['subject'] = "Emailing Report: ".$attachments[0]['name']; }
		if(!isset($email_config['message'])){ $email_config['message'] = "This message was sent by an administrator of your website."; }
		
		$this->EE->load->library('email');
		$this->EE->email->clear();
		$this->EE->email->to($email_config['to']);
		$this->EE->email->from($email_config['from_email'], $email_config['from_name']);
		$this->EE->email->subject($email_config['subject']);
		$this->EE->email->message($email_config['message']);
		foreach($attachments as $attachment){
			$this->EE->email->attach($attachment['path']);
		}
		if( $this->EE->email->send() ){
			$status = true;
		}else{
			$status = false;
		}
		return $status;
	}
	
	
	
	/**
	 * Returns an array of the report's static variables
	 *
	 * This method has been added to address limitations in PHP 5.2.x that do not exist in PHP 5.3.x
	 *
	 * @access public
	 * @return array Class static variables
	 **/
	public function getInfo()
	{
		return array(
			'title' => $this->title,
			'notes' => $this->notes,
			'author' => $this->author,
			'docs_url' => $this->docs_url,
			'version' => $this->version,
			'type' => $this->type,
			'output_types' => $this->output_types
		);
	}
	
}
