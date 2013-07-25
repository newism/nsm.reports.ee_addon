<?php if (! defined('BASEPATH')) die('No direct script access allowed');

require PATH_THIRD.'nsm_reports/config.php';

/**
 * NSM Reports CP 
 *
 * @package NsmReports
 * @version 1.0.7
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html#control_panel_file
 */
 



/**
 * Load required classes
 */
if (!class_exists('Nsm_reports_ext')) {
	require PATH_THIRD."nsm_reports/ext.nsm_reports.php";
}
if (!class_exists('Nsm_report_base')) {
	require PATH_THIRD."nsm_reports/models/nsm_report_base.php";
}
if (!class_exists('Nsm_saved_report')) {
	require PATH_THIRD."nsm_reports/models/nsm_saved_report.php";
}

/**
 * The Module Control Panel class
 *
 * @package NsmReports 
 */
class Nsm_reports_mcp
{
	
	/**
	 * Stores module navigation links as basic array
	 *
	 * @var array
	 * @access private
	 */
	private $pages	= array(
		'index', 
		'saved_reports'
	);
	
	public $saved_report;
	
	/**
	 * Stores information about the current report
	 *
	 * @var array
	 * @access public
	 */
	public $report	= array(
		'name'		=> false,
		'action'	=> false,
		'save_id'	=> false,
		'save_key'	=> false,
		'config'	=> array()
	);
	
	/**
	 * Mandatory configuraton details that are merged into report configs
	 *
	 * @var array
	 * @access public
	 */
	public $default_report_config = array(
		'_output'					=> 'browser',
		'_send_to_email_address'	=> '',
		'_save_report_name'			=> '',
		'_save_report_description'	=> ''
	);
	
	/**
	 * PHP5 constructor function.
	 *
	 * Prepares instance of ExpressionEngine for object scope, sets addon_id, prepares extension settings, 
	 *   loads date helper and prepares required file-system paths.
	 *
	 * If there has been no 'report_path' directory set in the extension settings the 'reports' sub-directory
	 *   in this add-on is used as a default location.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->EE->load->helper('date');
		$this->EE->load->model('nsm_reports_model');
		
		$this->addon_id		= NSM_REPORTS_ADDON_ID;
		$this->cp_url		= $this->generateCpUrl();
		$this->report		= $this->prepareReportConfig();
		
		$NsmReportsExt		= new Nsm_reports_ext();
		$this->settings		= $NsmReportsExt->settings;
		$this->report_path	= $this->getNsmReportPath();
		
		$this->EE->nsm_reports_model->set_report_path($this->report_path);
	}
	
	/**
	 * Generates the base control panel URL
	 *
	 * @access public
	 * @return string Returns the CP URL
	 */
	public function generateCpUrl()
	{
		return 'C=addons_modules'.AMP.
				'M=show_module_cp'.AMP.
				'module='.$this->addon_id.AMP;
	}
	
	/**
	 * Returns the file path for reports
	 *
	 * @access public
	 * @return string Returns the reports file path
	 */
	public function getNsmReportPath()
	{
		// if path has been set in config use that else use path set in
		//   extension settings.
		if ($this->EE->config->slash_item('report_path')) {
			$path = $this->EE->config->slash_item('report_path');
		} else {
			$path = $this->settings['report_path'];
		}
		// path still empty? use the built-in path as a fallback.
		if (!$path) {
			$path = $this->EE->nsm_reports_model->get_report_path();
		}
		return $path;
	}
	
	
	
	/**
	 * Reads incoming post / get variables and stores them in an array
	 *
	 * @access public
	 * @return array Returns the new array
	 */
	public function prepareReportConfig()
	{
		// prep config variables
		$init_config	= array();
		$report_config	= array(
			'report' => array()
		);
		$get_post		= array_merge(
			array_keys($_GET),
			array_keys($_POST)
		);
		
		// if this is a saved report use it and stop
		$save_id		= $this->EE->input->get('report__save_id');
		$save_key		= $this->EE->input->get('key');
		if ($save_id && $save_key) {
			$saved_report = Nsm_saved_report::findByIdKey($save_id, $save_key);
			if (!$saved_report) {
				return array(
					'status' => false,
					'message' => $EE->lang->line('nsm_reports_generate_no_preset')
				);
			}
			$report_config['report']['name']    = $saved_report->report;
			$report_config['report']['config']  = $saved_report->config;
			$report_config = array_merge(
  			$this->report,
  			$report_config['report']
  		);
  		$this->saved_report = $saved_report;
  		return $report_config;
		}
		
		// find any post or get variables prefixed with report__ and prep them
		foreach ($get_post as $get_key) {
			if (strpos($get_key, 'report__') !== false) {
				$init_config[$get_key] = $this->EE->input->get_post($get_key);
			}
		}
		// if the initial config isn't empty process the data as a config
		if (count($init_config) > 0) {
			$report_config = $this->processIncomingConfig($init_config);
		}
		// merge default report variables with the initial config
		$report_config = array_merge(
			$this->report,
			$report_config['report']
		);
		// any get/post vars in the report node would be posted from the
		//  report configuration form so these take priority
		if (is_array($this->EE->input->get_post('report'))) {
			$report_config['config'] = array_merge(
				$report_config['config'],
				$this->EE->input->get_post('report')
			);
		}
		return $report_config;
	}
	
	public function processIncomingConfig($array) {
	    $output = array();
	    foreach ($array as $key => $value) {
	        $key_parts	= explode('__', $key);
	        $ref		= &$output;
	        foreach ($key_parts as $part) {
	            if (!is_array($ref)) {
	                $ref = array();
				}
	            if (!array_key_exists($part, $ref)) {
	                $ref[$part] = array();
				}
	            $ref = &$ref[$part];
	        }
	        $ref = $value;
	    }
	    return $output;
	}
	
	/**
	 * Checks the generated reports path for problems and returns status
	 *
	 * @access public
	 * @return string Returns the Expression Engine processed page View
	 */
	public function checkGeneratedReportsDirectory()
	{
		$generated_report_path	= $this->settings['generated_reports_path'];
		$extension_settings_url	= (
			BASE.AMP.
			'C=addons_extensions'.AMP.
			'M=extension_settings'.AMP.
			'file=nsm_reports'
		);
		// does directory exist?
		if (!is_dir($generated_report_path)) {
			return array(
				'status'	=> false,
				'class'		=> 'error',
				'message'	=> sprintf(
					$this->EE->lang->line(
						'nsm_reports_messages_generated_report_'.
						'path_not_exists'
					),
					$generated_report_path,
					$extension_settings_url
				)
			);
		}
		// does directory have write access?
		if (!is_writable($generated_report_path)) {
			return array(
				'status'	=> false,
				'class'		=> 'error',
				'message'	=> sprintf(
					$this->EE->lang->line(
						'nsm_reports_messages_generated_report_'.
						'path_not_writeable'
					),
					$generated_report_path,
					$extension_settings_url
				)
			);
		}
		
		return array(
			'status'	=> true,
			'class'		=> 'ok',
			'message'	=> sprintf(
				$this->EE->lang->line(
					'nsm_reports_messages_generated_report_'.
					'path_ok'
				),
				$generated_report_path
			)
		);
	}
	
	/**
	 * Processes the module's DashBoard page by finding all reports and listing them.
	 *
	 * @access public
	 * @return string Returns the Expression Engine processed page View
	 */
	public function index()
	{
		$reports = $this->EE->nsm_reports_model->find_all();
		
		if ($reports) {
			foreach ($reports as $key => &$report) {
				$report['config_url'] = (
					BASE.AMP.
					$this->cp_url.
					'method=mcp_configure'.AMP.
					'report__name='.$key);
			}
		}

		$data	= array(
			'cp_url'			=> BASE.AMP.$this->cp_url,
			'reports'			=> $reports,
			'report_output_dir'	=> $this->checkGeneratedReportsDirectory()
		);

		$out	= $this->EE->load->view("module/report/index", $data, TRUE);
		return $this->_renderLayout("index", $out);
	}
	
	
	/**
	 * MCP friendly wrapper for the Configure Report method.
	 *
	 * @access public
	 * @param bool|string $preview_html Pre-generated report results to be displayed on page
	 * @param bool|string $error If an error was encountered it will be displayed on the page
	 * @return string Returns the Expression Engine processed page View
	 */
	public function mcp_configure($preview_html = false, $error = false)
	{
		// get outut of configuration
		$configuration = $this->configure($preview_html, $error);
		// if config is an array there's been an error
		if (is_array($configuration)) {
			$this->EE->session->set_flashdata(
				'message_'.($configuration['status'] ? 'success' : 'failure'),
				$configuration['message']
			);
			$this->EE->functions->redirect( $_SERVER['REQUEST_URI'] );
		} else {
			return $configuration;
		}
	}
	
	/**
	 * Generates the report summary and configuration form for the selected report.
	 *
	 * Method also allows pre-generated report results and/or a report error to be
	 *   passed as parameters.
	 * If a save_id has been added to the page request then the report will be configured to
	 *   use the preset data and inform the user that a a preset is being used.
	 *
	 * @access public
	 * @param bool|string $preview_html Pre-generated report results to be displayed on page
	 * @param bool|string $error If an error was encountered it will be displayed on the page
	 * @return string Returns the Expression Engine processed page View
	 */
	public function configure($preview_html = false, $error = false)
	{
		$report_class	= $this->report['name'];
		$report			= $this->EE->nsm_reports_model->find($report_class);
		if (!$report) {
			return array(
				'status'	=> false,
				'message'	=> $this->EE->lang->line('nsm_reports_configure_no_report')
			);
		}
		
		$config = array_merge(
			$this->default_report_config,
			$this->report['config']
		);

		$saved_report_info	= false;
		$saved_report_id	= $this->report['save_id'];

		if ($saved_report_id > 0) {
			$saved_report = Nsm_saved_report::findById($saved_report_id);
			if (!$saved_report) {
				return array(
					'status'	=> false,
					'message'	=> $this->EE->lang->line('nsm_reports_configure_no_preset')
				);
			}
			$config				= array_merge($config, $saved_report->config);
			$saved_report_info	= sprintf(
				$this->EE->lang->line('nsm_reports_configure_preset_info'), 
				$saved_report->title,
				$saved_report->id
			);
		}
		
		$report->setConfig($config);
		
		$selected_form_action			= $this->report['action'];
		$report_config_html				= $report->configHTML();
		$report_info					= $report->getInfo();
		$report_info['output_types']	= $report->output_types;
		
		$data = array(
			'report'				=> $report_info,
			'error'					=> $error,
			'saved_report_info'		=> $saved_report_info,
			'config'				=> $config,
			'saved_report_id'		=> $saved_report_id,
			'report_config_html'	=> $report_config_html,
			'preview_html'			=> $preview_html,
			'selected_form_action'	=> $selected_form_action,
			'saved_reports_url'		=> BASE.AMP.$this->cp_url.'method=saved_reports',
			'report_output_dir'		=> $this->checkGeneratedReportsDirectory()
		);

		$view	= $this->EE->load->view('module/report/config', $data, TRUE);
		$out	= form_open(
						$this->cp_url.'method=configure_submit'.AMP.'report__name='.$report_class,
						array(),
						array(
							'report__name'=>$report_class,
							'report__save_id'=>$saved_report_id
						)
					);
		$out	.= $view;
		$out	.= form_close();
		
		return $this->_renderLayout(
			"report_config",
			$out,
			array('report_title' => $report_info['title'])
		);
	}
	
	/**
	 * Manages program flow for submitted data sent from method Nsm_reports_mcp::configure.
	 *
	 * Form input 'action' determines what action should be taken next.
	 *
	 * @access public
	 * @return mixed The return data is the return value of the method called in the switch statement
	 */
	public function configure_submit()
	{
		switch ($this->report['action']) {
			case 'generate':
				return $this->mcp_generate();
				break;
			case 'save':
				return $this->save();
				break;
			case 'new':
				return $this->save(true);
				break;
		}
	}
	
	/**
	 * MCP friendly wrapper for the Generate Report method.
	 *
	 * @access public
	 * @param bool|string $preview_html Pre-generated report results to be displayed on page
	 * @param bool|string $error If an error was encountered it will be displayed on the page
	 * @return string Returns the Expression Engine processed page View
	 */
	public function mcp_generate()
	{
		// get outut of configuration
		$generation = $this->generate();
		// if config is an array there's been an error
		if (is_array($generation)) {
			$this->EE->session->set_flashdata('message_'.($generation['status'] ? 'success' : 'failure'), $generation['message']);
			$this->EE->functions->redirect( str_replace('&method=configure_submit&', '&method=mcp_configure&', $_SERVER['REQUEST_URI']) );
		} else {
			return $generation;
		}
	}
	
	
	/**
	 * CRON friendly wrapper for the Generate Report method.
	 *
	 * @access public
	 * @param bool|string $preview_html Pre-generated report results to be displayed on page
	 * @param bool|string $error If an error was encountered it will be displayed on the page
	 * @return string Returns the Expression Engine processed page View
	 */
	public function cron_generate()
	{
		// get outut of configuration
		$generation = $this->generate();
		// if config is an array there's been an error
		if (is_array($generation)) {
			die($generation['message']);
		} else {
			return $generation;
		}
	}
	
	/**
	 * Generates a report result-set and performs an action depending on conditions
	 *
	 * Actions that can happen include:
	 *   -  renders results to browser by passing generated report as a parameter to Nsm_reports_mcp::configure
	 *   -  saves report data as a zip-file and emails download link to selected email address 
	 *   -  forces a download of the report in the browser using the output format that has been chosen
	 * This method will configure a report to use a preset if the id and key are set or configures report using post data.
	 *
	 * @access public
	 * @return mixed Returns either the output of Nsm_reports_mcp::configure or a php die
	 */
	public function generate()
	{
		// Use a 'local' copy of the EE instance as this method can be called via an action
		$EE =& get_instance();
		$EE->lang->loadfile('nsm_reports');
		// prepare process_url variables
		$save_id		= $this->report['save_id'];
		$save_key		= $this->report['save_key'];
		$report_class	= $this->report['name'];
		
		// This user check has been commented out as a result of a logical choice.
		// Anyone can generate a report. Only permitted groups can download the report.
		/*$can_download_groups = $this->settings['member_groups'];
		$group_id = $this->EE->session->userdata['group_id'];
		
		if(!isset($can_download_groups[$group_id]) || $can_download_groups[$group_id]['can_download'] == false){
			$this->EE->output->fatal_error("You are not logged in to a member group that permits reporting.");
			exit;
		}
		*/
		
		$report			= $EE->nsm_reports_model->find($report_class);
		if (!$report) {
			return array(
				'status'	=> false,
				'message'	=> $EE->lang->line('nsm_reports_generate_no_report')
			);
		}
		
		$saved_report	= $this->saved_report;
		// if $saved_report_id and saved_report_key aren't false then generate report as a 'cron' process
		/*if ($save_id && $save_key) {
			$saved_report = Nsm_saved_report::findByIdKey($save_id, $save_key);
			if (!$saved_report) {
				return array(
					'status' => false,
					'message' => $EE->lang->line('nsm_reports_generate_no_preset')
				);
			}
			$report_class			= $saved_report->report;
			$config					= $saved_report->config;
			$output_type			= $saved_report->output;
			$send_to_email_address	= $saved_report->email_address;
		} else {*/
			$config					= array_merge(
				$this->default_report_config,
				$this->report['config']
			);
			$output_type			= $config['_output'];
			$send_to_email_address	= $config['_send_to_email_address'];
		//}
		
		$report->cache_path = (
			$EE->config->slash_item('generated_reports_path') ? 
			$EE->config->slash_item('generated_reports_path') : 
			$this->settings['generated_reports_path']
		);

		$report->setConfig($config);
		$report_info = $report->getInfo();
		$output_data = $report->generate($output_type);
		
		/*
			If sending a report to the browser then
				- output report to browser
				- do not generate a report zip
				- do not send emails
				- do not force downloads
			Else
				- if sending an email then
				 	- generate a report zip
					- send an email
				- if method called via ACT then
					- check for existing report zip, if not already created then create
				- else force download
		*/
		
		// is report to be sent to control panel?
		if ($output_type == 'browser') {
			// send to browser
			if ($EE->input->get('ACT')) {
				return array(
					'status'	=> false,
					'message'	=> $EE->lang->line('nsm_reports_generate_illegal_output')
				);
			}
			return $this->mcp_configure($output_data['content'], $report->error);
		} else {
			// report data to be emailed or downloaded
			
			// prepare placeholder variable
			$generated_report_path = null;
			
			if ($send_to_email_address) {
				// email address supplied so send report as email
				$generated_report_path = $report->zip_report($output_data);
				if (!$generated_report_path) {
					return array(
						'status' => false,
						'message' => $EE->lang->line('nsm_reports_generate_output_fail')
					);
				}
				$process_url = (defined('NSM_SITE_URL')
					? NSM_SITE_URL
					: 'http://'.$_SERVER['SERVER_NAME']) . '/?ACT='. 
						$EE->functions->fetch_action_id('Nsm_reports_mcp', 'download_generated_report') . 
						'&file='. md5(basename($generated_report_path)
				);
			
				$email_config = array(
					'to'		=> $send_to_email_address,
					'subject'	=> sprintf(
										$EE->lang->line('nsm_reports_generate_send_email_subject'), 
										$report_info['title'], 
										( !$saved_report ? 'None' : $saved_report->title ),
										( !$saved_report ? 'NA' : $saved_report->id )
									),
					'message'	=> sprintf(
										$EE->lang->line('nsm_reports_generate_send_email_message'), 
										$report_info['title'], 
										( !$saved_report ? 'None' : $saved_report->title ),
										( !$saved_report ? 'NA' : $saved_report->id ),
										$process_url
									)
				);
				
				$email_sent = $report->email_report($email_config, array());
				if ($email_sent == true) {
					if ($saved_report) {
						$saved_report->lastrun_at	= now();
						$saved_report->run_count	= ($saved_report->run_count + 1);
						$saved_report->update();
						return array(
							'status'	=> false,
							'message'	=> $EE->lang->line('nsm_reports_generate_send_email_ok')
						);
					}
				} else {
					return array(
						'status'	=> false,
						'message'	=> $EE->lang->line('nsm_reports_generate_send_email_error')
					);
				}
			}
			
			if ($EE->input->get('ACT')) {
				
				if ($generated_report_path == null) {
					$generated_report_path = $report->zip_report($output_data);
				}
				if (!$generated_report_path) {
					return array(
						'status'	=> false,
						'message'	=> $EE->lang->line('nsm_reports_generate_output_fail')
					);
				}
				
				return array(
					'status'	=> false,
					'message'	=> $EE->lang->line('nsm_reports_generate_ok')
				);
				//$this->EE->session->set_flashdata('message_success', "The report was successfully generated.");

			} else {
				// force download of report
				$EE->load->helper('download');
				force_download($output_data['name'].'.'.$output_data['extension'], $output_data['content']);
			}
		}
		
	}
	
	/**
	 * Saves a report configuration as a preset and stores options in the database.
	 *
	 * If parameter 'save_as_new' is set to true the method will ignore update actions.
	 * Some basic form validation takes place in this method and if an error occurs then
	 *   return the output of the Nsm_reports::configure method and pass the form validation
	 *   error message as the $error parameter.
	 *
	 * @access public
	 * @param bool $save_as_new If TRUE then data will always be inserted and never updated
	 * @return string|void Return Code Igniter View if form validation is false else redirect to new page
	 */
	public function save($save_as_new = false)
	{
		
		$report		= $this->report['name'];
		$config		= $this->report['config'];
		$save_id	= $this->report['save_id'];
		
		/* 
			Perform form validation here.
			We can assume that the user is saving the report so checking for the
			  form action is unneeded.
		*/
		$this->EE->load->library('form_validation');
		$this->EE->form_validation->set_rules('report[_save_report_name]', 'Preset Name', 'required');
		
		if ($this->EE->form_validation->run() == false) {
			$validation_errors = validation_errors();
			return $this->mcp_configure('', $validation_errors);
		}
		
		$data = array(
			'title'			=> $config['_save_report_name'],
			'description'	=> $config['_save_report_description'],
			'report'		=> $report,
			'email_address'	=> $config['_send_to_email_address'],
			'output'		=> $config['_output'],
			'config'		=> $config,
			'active'		=> 1
		);
		
		if ($save_id > 0 && $save_as_new == false) {
			$save_report = Nsm_saved_report::findById($save_id);
			if (!$save_report) {
				return array(
					'status'	=> false,
					'message'	=> $this->EE->lang->line('nsm_reports_save_no_preset')
				);
			}
			$save_report->setData($data);
			$action_status = $save_report->update();
		} else {
			$data				= array_merge($data, array(
				'created_at' => now()
			));
			$data['access_key']	= md5($data['created_at'].$data['report']);
			$save_report		= new Nsm_saved_report($data);
			$action_status		= $save_report->add();
		}
		
		if ($action_status == true) {
			$this->EE->session->set_flashdata(
												'message_success',
												sprintf(
														$this->EE->lang->line('nsm_reports_save_ok'),
														$config['_save_report_name']
												)
											);
			$this->EE->functions->redirect(BASE.AMP.$this->cp_url.'method=saved_reports');
		} else {
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('nsm_reports_save_model_error'));
			$this->EE->functions->redirect(BASE.AMP.$this->cp_url.'method=index'.AMP.'report='.$report);
		}
	}
	
	/**
	 * Displays all saved report presets and displays them as a table.
	 *
	 * Method checks that report exists before showing the associated presets.
	 *
	 * Saved preset objects are converted into view-friendly arrays.
	 *
	 * Form showed on page manages presets to be included in delete command.
	 *
	 * @access public
	 * @return string Returns the Expression Engine processed page View
	 */
	public function saved_reports()
	{
		$error				= false;
		$reports			= $this->EE->nsm_reports_model->find_all();
		$reports_classes	= array_keys($reports);
		$saved_reports		= false;
		$get_saved_reports	= Nsm_saved_report::findAll();
		
		if ($get_saved_reports) {
			$saved_reports = array();
			foreach ($get_saved_reports as $saved_report_key => $saved_report) {
				$report_class = $saved_report->report;
				if (in_array($report_class, $reports_classes)) {
					$saved_reports[] = array(
						'id'			=> $saved_report->id,
						'title'			=> $saved_report->title,
						'description'	=> $saved_report->description,
						'access_key'	=> $saved_report->access_key,
						'created_at'	=> $this->EE->localize->set_human_time($saved_report->created_at),
						'updated_at'	=> $this->EE->localize->set_human_time($saved_report->updated_at),
						'lastrun_at'	=> ($saved_report->lastrun_at > 0 ? $this->EE->localize->set_human_time($saved_report->lastrun_at) : 'Never'),
						'report_class'	=> $saved_report->report,
						'report'		=> $reports[ $saved_report->report ]['title'],
						'email_address'	=> $saved_report->email_address,
						'output'		=> $reports[ $saved_report->report ]['output_types'][ $saved_report->output ], 
						'config'		=> $saved_report->config,
						'active'		=> $saved_report->active,
						'run_count'		=> $saved_report->run_count
					);
				}
			}
		}
		
		if (defined('NSM_SITE_URL')) {
			$site_url = NSM_SITE_URL;
		} elseif ($this->EE->config->slash_item('site_url')) {
			$site_url = $this->EE->config->slash_item('site_url');
		} else {
			$site_url = 'http://'.$_SERVER['SERVER_NAME'];
		}
		
		$data	= array(
			'saved_reports'	=> $saved_reports,
			'reports'		=> $reports,
			'details_url'	=> BASE.AMP.$this->cp_url.'method=mcp_configure&report__name=',
			'process_url'	=> $site_url . '/?ACT='. 
								$this->EE->cp->fetch_action_id('Nsm_reports_mcp', 'cron_generate') . 
								AMP . 'report__save_id=',
			'error'			=> $error
		);
		$out	= form_open($this->cp_url.'method=delete_saved') .
					$this->EE->load->view('module/saved_report/index', $data, TRUE) .
					form_close();
					
		return $this->_renderLayout("saved_reports", $out);
	}
	
	/**
	 * Displays a confirmation form for the delete command and lists items to be deleted.
	 *
	 * @access public
	 * @return string Returns the Expression Engine processed page View
	 */
	public function delete_saved()
	{
		$saved_report_ids	= $this->EE->input->post('delete');
		$saved_reports		= Nsm_saved_report::findByIds($saved_report_ids);
		$error				= "";
		if (!$saved_reports) {
			return array(
				'status'	=> false,
				'message'	=> $this->EE->lang->line('nsm_reports_delete_saved_no_presets')
			);
		}
		
		
		$data	= array(
			'saved_reports'	=> $saved_reports,
			'error'			=> $error,
			'form_action'	=> AMP.$this->cp_url.'method=delete_saved_submit'
		);

		$out	= form_open($this->cp_url.'method=delete_saved_submit') .
					$this->EE->load->view('module/saved_report/delete', $data, TRUE) .
					form_close();
		return $this->_renderLayout("delete_saved_report", $out);
	}
	
	
	/**
	 * Deletes all presets where ID is sent in Nsm_report_mcp::delete_saved form.
	 *
	 * @access public
	 * @return void Method will always redirect to new page
	 */
	public function delete_saved_submit()
	{
		$saved_report_ids = $this->EE->input->post('delete');
		if (!$saved_report_ids) {
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('nsm_reports_delete_saved_submit_no_ids'));
			$this->EE->functions->redirect(BASE.AMP.$this->cp_url.'method=saved_reports');
		}
		if (Nsm_saved_report::deleteByIds($saved_report_ids)){
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('nsm_reports_delete_saved_submit_ok'));
		} else {
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('nsm_reports_delete_saved_submit_error'));
		}
		$this->EE->functions->redirect(BASE.AMP.$this->cp_url.'method=saved_reports');
	}

	/**
	 * Security checkpoint for downloading generated reports.
	 *
	 * First checks that user is logged in and is a member of can_download group (set in extension settings).
	 * Then gets all files in generated reports directory and iterates over files.
	 * On each file compare the requested file hash against the md5 hash of the file.
	 * If file hash is equal to the filename hash force file download.
	 *
	 * @access public
	 * @return mixed Either a forced file download or an error message
	 */
	public function download_generated_report()
	{
		$EE =& get_instance();
		$EE->lang->loadfile('nsm_reports');
		
		$can_download_groups	= $this->settings['member_groups'];
		$group_id				= $EE->session->userdata['group_id'];
		
		if ($can_download_groups[$group_id]['can_download'] == false) {
			$EE->output->fatal_error($this->EE->lang->line('nsm_reports_download_generated_report_invalid_group_id'));
			exit;
		}
		
		$real_file = false;
		$file_hash = $EE->input->get('file');
		$file_path = $this->settings['generated_reports_path'];
		
		if (!is_dir($file_path)) {
			$EE->output->fatal_error($this->EE->lang->line('nsm_reports_download_generated_report_path_not_exists'));
			exit;
		}
		
		$EE->load->helper('file');
		$files = get_filenames($file_path);
		if (count($files) > 0) {
			foreach ($files as $file) {
				if (md5($file) == $file_hash) {
					$EE->load->helper('download');
					$real_file	= $file;
					$name		= basename($real_file);
					$data		= file_get_contents($file_path.$real_file);
					force_download($name, $data);
					exit;
				}
			}
		}
		
		$EE->output->fatal_error($this->EE->lang->line('nsm_reports_download_generated_report_no_file'));
		exit;
	}
	
	/**
	 * Manages View display for Expression Engine control panel.
	 *
	 * Uses parameters to build the control panel page and associated navigation items.
	 *
	 * @access public
	 * @param string $page The base array key to use when retrieving data from the Language object
	 * @param string $out String version of processed Expression Engine View
	 * @param array $page_replacements Collection of string replacements to commit to page title
	 * @return mixed Either a forced file download or an error message
	 */
	public function _renderLayout($page, $out = FALSE, $page_replacements = array())
	{
		$page_title = $this->EE->lang->line("nsm_reports_{$page}_page_title");
		foreach ($page_replacements as $key => $value){
			$page_title = str_replace("{".$key."}", $value, $page_title);
		}
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line($page_title));
		$this->EE->cp->set_breadcrumb(BASE.AMP.$this->cp_url, $this->EE->lang->line('nsm_reports_module_name'));

		$nav = array();
		foreach ($this->pages as $page) {
			$nav[lang("nsm_reports_{$page}_nav_title")] =  BASE.AMP.$this->cp_url . "method=" . $page;
		}
		$this->EE->cp->set_right_nav($nav);

		$this->EE->load->library($this->addon_id."_helper");
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js" charset="utf-8"></script>');
		$this->EE->nsm_reports_helper->addCpJs('cp.js');
		
		return "<div class='mor'>{$out}</div>";
	}
	
}
