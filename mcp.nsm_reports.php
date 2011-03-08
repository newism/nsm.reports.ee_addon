<?php

/**
 * NSM Reports CP 
 *
 * @package NsmReports
 * @version 1.0.1
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://expressionengine-addons.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html#control_panel_file
 */
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Load required classes
 */
if(!class_exists('Nsm_reports_ext')){ include(PATH_THIRD."nsm_reports/ext.nsm_reports.php"); }
if(!class_exists('Nsm_report_base')){ include(PATH_THIRD."nsm_reports/models/nsm_report_base.php"); }
if(!class_exists('Nsm_saved_report')){ include(PATH_THIRD."nsm_reports/models/nsm_saved_report.php"); }

/**
 * The Module Control Panel class
 *
 * @package NsmReports 
 */
class Nsm_reports_mcp {
	
	/**
	 * Stores module navigation links as basic array
	 *
	 * @var array
	 * @access private
	 **/
	private $pages = array("index", "saved_reports");
	
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
	 **/
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->addon_id = strtolower(substr(__CLASS__, 0, -4));
		$this->cp_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->addon_id.AMP;
		$this->cache_path = APPPATH.'cache/' . $this->addon_id . "/" ;
		
		$this->EE->load->helper('date');
		
		$NsmReportsExt = new Nsm_reports_ext();
		$this->settings = $NsmReportsExt->settings;
		$this->report_path = $this->settings['report_path'];
		
		$this->EE->load->model('nsm_reports_model');
		
		if($this->report_path !== ''){
			$this->EE->nsm_reports_model->set_report_path($this->report_path);
		}else{
			$this->report_path = $this->EE->nsm_reports_model->get_report_path();
		}
	}
	
	/**
	 * Processes the module's DashBoard page by finding all reports and listing them.
	 *
	 * @access public
	 * @return string Returns the Expression Engine processed page View
	 **/
	public function index()
	{
		$reports = $this->EE->nsm_reports_model->find_all();
		
		if($reports){
			foreach ($reports as $key => &$report){
				$report['config_url'] = BASE.AMP.$this->cp_url.'method=configure'.AMP.'report='.$key;
			}
		}

		$data = array(
			'cp_url' => BASE.AMP.$this->cp_url,
			'reports' => $reports
		);

		$out = $this->EE->load->view("module/report/index", $data, TRUE);
		return $this->_renderLayout("index", $out);
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
	 **/
	public function configure($preview_html = false, $error = false)
	{
		$report_class = $this->EE->input->get('report');
		if(!$report = $this->EE->nsm_reports_model->find($report_class)){
			die("No report found");
		}

		$config = array(
			'_output' => 'browser',
			'_send_to_email_address' => '',
			'_save_report_name' => '',
			'_save_report_description' => ''
		);

		$saved_report_info = false;

		$saved_report_id = $this->EE->input->get('save_id');
		if($this->EE->input->post('saved_report_id')){
			$saved_report_id = $this->EE->input->post('saved_report_id');
		}

		if($saved_report_id > 0){
			if(!$saved_report = Nsm_saved_report::findById($saved_report_id)){
				die("No auto report found");
			}
			$config = array_merge($config, $saved_report->config);
			$saved_report_info = "Now using saved report configuration '".$saved_report->title."' (ID ".$saved_report->id."). ".
									"Any changes made to the configuration will not be saved until you change the Action to 'Save' and submit the form.";
		}

		if($this->EE->input->post('report')){
			$config = $this->EE->input->post('report');
		}

		$selected_form_action = $this->EE->input->post('action');

		$report->setConfig($config);
		$report_config_html = $report->configHTML();

		$data = array(
			'report' => $report,
			'error' => $error,
			'saved_report_info' => $saved_report_info,
			'config' => $config,
			'saved_report_id' => $saved_report_id,
			'report_config_html' => $report_config_html,
			'preview_html' => $preview_html,
			'selected_form_action' => $selected_form_action,
			'saved_reports_url' => BASE . AMP . $this->cp_url . AMP .'method=saved_reports'
		);

		$out = $this->EE->load->view('module/report/config', $data, TRUE);
		$out = form_open($this->cp_url.'method=configure_submit'.AMP.'report='.$report_class,
					array(),
					array(
						'report_name'=>$report_class,
						'saved_report_id'=>$saved_report_id
					)
				)
				. $out 
				. form_close();
		return $this->_renderLayout("report_config", $out, array('report_title' => $report::$title));
	}
	
	/**
	 * Manages program flow for submitted data sent from method Nsm_reports_mcp::configure.
	 *
	 * Form input 'action' determines what action should be taken next.
	 *
	 * @access public
	 * @return mixed The return data is the return value of the method called in the switch statement
	 **/
	public function configure_submit()
	{
		$action = $this->EE->input->post('action');
		switch($action){
			case 'generate':
				return $this->generate();
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
	 **/
	public function generate()
	{
		// prepare process_url variables
		$saved_report_id = $this->EE->input->get('save_id');
		$saved_report_key = $this->EE->input->get('key');
		
		$report_class = $this->EE->input->get('report');
		
		// if $saved_report_id and saved_report_key aren't false then generate report as a 'cron' process
		if($saved_report_id && $saved_report_key){
			$saved_report = Nsm_saved_report::findByIdKey($saved_report_id, $saved_report_key);
			if(!$saved_report){
				die("No report found");
			}
			$report_class = $saved_report->report;
			$config = $saved_report->config;
			$output_type = $saved_report->output;
			$send_to_email_address = $saved_report->email_address;
		}else{
			$config = $this->EE->input->post('report');
			$output_type = $config['_output'];
			$send_to_email_address = $config['_send_to_email_address'];
		}
		
		if(!$report = $this->EE->nsm_reports_model->find($report_class)) {
			die("No report found");
		}
		
		$report->cache_path = $this->settings['generated_reports_path'];
		$report->setConfig($config);
		
		$output_data = $report->generate($output_type);
		
		// is report to be sent to control panel?
		if($output_type == 'browser'){
			// send to browser
			return $this->configure($output_data['content'], $report->error);
		} else {
			// report data to be emailed or downloaded
			if($send_to_email_address){
				// email address supplied so send report as email
				$generated_report_path = $report->zip_report($output_data);

				$process_url = (defined('NSM_SITE_URL') ? NSM_SITE_URL : 'http://'.$_SERVER['SERVER_NAME']) . '/?ACT='. 
								$this->EE->functions->fetch_action_id('Nsm_reports_mcp', 'download_generated_report') . 
								'&file='. md5(basename($generated_report_path));

				$email_config = array(
					'to' => $saved_report->email_address,
					'subject' => 'Sending Report: '.$report::$title.' using preset '.$saved_report->title.' ('.$saved_report->id.')',
					'message' => 'This email was sent from your website using a process URL. '.
									'Your report '.$report::$title.' using preset '.$saved_report->title.' ('.$saved_report->id.') '.
									'has been generated and ready for download at this location: '.
									''.$process_url
				);

				$email_sent = $report->email_report($email_config, array());
				if( $email_sent == true ){
					if($saved_report){
						$saved_report->lastrun_at = now();
						$saved_report->run_count = $saved_report->run_count + 1;
						$saved_report->update();
						die("The report was successfully sent to the specified email address.");
					}
					$this->EE->session->set_flashdata('message_success', "The report was successfully emailed to '".$send_to_email_address."'.");
				}else{
					if($saved_report){
						die("There was an error sending the report.");
					}
					$this->EE->session->set_flashdata('message_error', "There was an error sending the report.");
				}
				$this->EE->functions->redirect(BASE.AMP.$this->cp_url.'method=configure'.AMP.'report='.$report_class);

			} else {
				// force download of report
				$this->EE->load->helper('download');
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
	 **/
	public function save($save_as_new = false)
	{
		
		$report = $this->EE->input->post('report_name');
		$config = $this->EE->input->post('report');
		$saved_report_id = $this->EE->input->post('saved_report_id');
		
		/* 
			Perform form validation here.
			We can assume that the user is saving the report so checking for the
			  form action is unneeded.
		*/
		$this->EE->load->library('form_validation');
		$this->EE->form_validation->set_rules('report[_save_report_name]', 'Preset Name', 'required');
		
		if($this->EE->form_validation->run() == false){
			$validation_errors = validation_errors();
			return $this->configure('', $validation_errors);
		}
		
		$data = array(
			'title' => $config['_save_report_name'],
			'description' => $config['_save_report_description'],
			'report' => $report,
			'email_address' => $config['_send_to_email_address'],
			'output' => $config['_output'],
			'config' => $config,
			'active' => 1
		);
		
		if($saved_report_id > 0 && $save_as_new == false){
			if(!$save_report = Nsm_saved_report::findById($saved_report_id)){
				die("No saved report found");
			}
			$save_report->setData($data);
			$action_status = $save_report->update();
		}else{
			$data = array_merge($data, array(
				'created_at' => now()
			));
			$data['access_key'] = md5($data['created_at'].$data['report']);
			$save_report = new Nsm_saved_report($data);
			$action_status = $save_report->add();
		}
		
		if($action_status == true){
			$this->EE->session->set_flashdata('message_success', "The report preset '" .$config['_save_report_name']. "' was successfully saved.");
			$this->EE->functions->redirect(BASE.AMP.$this->cp_url.'method=saved_reports');
		}else{
			$this->EE->session->set_flashdata('message_error', "There was an error saving the new report preset.");
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
	 **/
	public function saved_reports()
	{
		$error = false;
		
		$reports = $this->EE->nsm_reports_model->find_all();
		$reports_classes = array_keys($reports);
		$saved_reports = array();

		$get_saved_reports = Nsm_saved_report::findAll();
		if($get_saved_reports){
			foreach($get_saved_reports as $saved_report_key => $saved_report){
				$report_class = $saved_report->report;
				if(in_array($report_class, $reports_classes)){
					$saved_reports[] = array(
						'id' => $saved_report->id,
						'title' => $saved_report->title,
						'description' => $saved_report->description,
						'access_key' => $saved_report->access_key,
						'created_at' => $this->EE->localize->set_human_time($saved_report->created_at),
						'updated_at' => $this->EE->localize->set_human_time($saved_report->updated_at),
						'lastrun_at' => ($saved_report->lastrun_at > 0 ? $this->EE->localize->set_human_time($saved_report->lastrun_at) : 'Never'),
						'report_class' => $saved_report->report,
						'report' => $reports[ $saved_report->report ]['title'],
						'email_address' => $saved_report->email_address,
						'output' => $saved_report->output,
						'config' => $saved_report->config,
						'active' => $saved_report->active,
						'run_count' => $saved_report->run_count
					);
				}
			}
		}
		
		$data = array(
			'saved_reports' => $saved_reports,
			'reports' => $reports,
			'details_url' => BASE.AMP.$this->cp_url.'method=configure&report=',
			'process_url' => (defined('NSM_SITE_URL') ? NSM_SITE_URL : 'http://'.$_SERVER['SERVER_NAME']) . '/?ACT='. 
								$this->EE->cp->fetch_action_id('Nsm_reports_mcp', 'generate') . 
								AMP . 'save_id=',
			'error' => $error
		);

		$out = form_open($this->cp_url.'method=delete_saved') .
					$this->EE->load->view('module/saved_report/index', $data, TRUE) .
				form_close();
		return $this->_renderLayout("saved_reports", $out);
	}
	
	/**
	 * Displays a confirmation form for the delete command and lists items to be deleted.
	 *
	 * @access public
	 * @return string Returns the Expression Engine processed page View
	 **/
	public function delete_saved()
	{
		$saved_report_ids = $this->EE->input->post('delete');
		if(!$saved_reports = Nsm_saved_report::findByIds($saved_report_ids)){
			die("No saved reports found");
		}
		
		$error = "";
		
		$data = array(
			'saved_reports' => $saved_reports,
			'error' => $error,
			'form_action' => AMP.$this->cp_url.'method=delete_saved_submit'
		);

		$out = form_open($this->cp_url.'method=delete_saved_submit') .
					$this->EE->load->view('module/saved_report/delete', $data, TRUE) .
				form_close();
		return $this->_renderLayout("delete_saved_report", $out);
	}
	
	
	/**
	 * Deletes all presets where ID is sent in Nsm_report_mcp::delete_saved form.
	 *
	 * @access public
	 * @return void Method will always redirect to new page
	 **/
	public function delete_saved_submit()
	{
		$saved_report_ids = $this->EE->input->post('delete');
		$error = "";
		if( Nsm_saved_report::deleteByIds($saved_report_ids) ){
			$this->EE->session->set_flashdata('message_success', "The saved reports were successfully deleted.");
		}else{
			$this->EE->session->set_flashdata('message_error', "There was an error deleting the saved reports.");
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
	 **/
	public function download_generated_report()
	{
		$EE =& get_instance();
		$can_download_groups = $this->settings['member_groups'];
		$group_id = $EE->session->userdata['group_id'];
		
		if($can_download_groups[$group_id]['can_download'] == false){
			$EE->output->fatal_error("You are not logged in to a member group that permits report.");
			exit;
		}
		
		$real_file = false;
		$file_hash = $EE->input->get('file');
		$file_path = $this->settings['generated_reports_path'];
		
		$EE->load->helper('file');
		$files = get_filenames($file_path);
		if(count($files) > 0){
			foreach($files as $file){
				if( md5($file) == $file_hash){
					$real_file = $file;
					$EE->load->helper('download');
					$name = basename($real_file);
					$data = file_get_contents($file_path.$real_file);
					force_download($name, $data);
					exit;
				}
			}
		}
		
		$EE->output->fatal_error("No File Found.");
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
	 **/
	public function _renderLayout($page, $out = FALSE, $page_replacements = array())
	{
		$page_title = $this->EE->lang->line("{$page}_page_title");
		foreach ($page_replacements as $key => $value){
			$page_title = str_replace("{".$key."}", $value, $page_title);
		}
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line($page_title));
		$this->EE->cp->set_breadcrumb(BASE.AMP.$this->cp_url, $this->EE->lang->line('nsm_reports_module_name'));

		$nav = array();
		foreach ($this->pages as $page) {
			$nav[lang("{$page}_nav_title")] =  BASE.AMP.$this->cp_url . "method=" . $page;
		}
		$this->EE->cp->set_right_nav($nav);

		$this->EE->load->library($this->addon_id."_helper");
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js" charset="utf-8"></script>');
		$this->EE->nsm_reports_helper->addCpJs('cp.js');

		return "<div class='mor'>{$out}</div>";
	}
	
	
	
}