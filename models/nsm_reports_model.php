<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * NSM Reports Model 
 *
 * @package NsmReports
 * @version 1.0.7
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 * @see http://codeigniter.com/user_guide/general/models.html
 **/

/**
 * Reports model class
 *
 * This class is added to the Code Igniter library and is used to retrieve reports
 *
 * @package NsmReports
 */
class Nsm_reports_model extends CI_Model 
{
	
	/**
	 * File-path of module reports directory
	 *
	 * @var string
	 * @access protected
	 **/
	protected $report_path = '';
	
	/**
	 * PHP5 constructor function.
	 *
	 * Sets the report_path of the model during object construction to a default location
	 *
	 * @access public
	 * @return void
	 **/
	public function __construct()
	{
		$this->report_path = realpath(dirname(__FILE__).'/../reports');
	}
	
	/**
	 * Sets the folder path that the object is using
	 *
	 * @access public
	 * @param string $report_path The file-path where reports are located
	 * @return void
	 */
	public function set_report_path($report_path)
	{
		$this->report_path = $report_path;
	}
	
	/**
	 * Returns the report path that the object is using
	 *
	 * @access public
	 * @return string File-path of reports directory
	 */
	public function get_report_path()
	{
		return $this->report_path;
	}
	
	/**
	 * Retrieves all reports that appear in the reports directory as an array
	 *
	 * @access public
	 * @return array Array of reports found in the method
	 */
	public function find_all()
	{
		$report_folders = directory_map($this->report_path);
		$reports = false;
		foreach ($report_folders as $folder => $files)
		{
			if(substr($folder, 0, 1) == "-"){ continue; }
			
			$folder = strtolower($folder);
			if(file_exists($this->report_path."/".$folder."/".$folder.EXT)){
				require($this->report_path."/".$folder."/".$folder.EXT);
				$class_name = ucfirst($folder);
				$class_obj = new $class_name;
				$class_info = $class_obj->getInfo();
				$reports[$class_name] = array(
					'title' => $class_info['title'],
					'notes' => $class_info['notes'],
					'author' => $class_info['author'],
					'docs_url' => $class_info['docs_url'],
					'version' => $class_info['version'],
					'type' => $class_info['type'],
					'output_types' => $class_info['output_types']
				);
			}
		}
		return $reports;
	}
	
	/**
	 * Retrieves a report from the reports directory.
	 * 
	 * Includes the report using PHP native file-system command and returns a new instance of the report
	 *
	 * @access public
	 * @return array Array of reports found in the method
	 */
	public function find($report)
	{
		$report = strtolower($report);
		$file_path = $this->report_path."/".$report."/".$report.EXT;
		if(!$report){ return false; }
		if(!file_exists($file_path)){ return false; }
		require_once $file_path;
		$class_name = ucfirst($report);
		return new $class_name;
	}
}