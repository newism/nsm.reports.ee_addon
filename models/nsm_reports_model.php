<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @ignore
 *
 * NSM Reports Model 
 *
 * @package			NsmReports
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-reports
 * @see				http://codeigniter.com/user_guide/general/models.html
 **/
class Nsm_reports_model extends CI_Model {

	protected $folder_path = '';

	function __construct(){
		$this->folder_path = PATH_THIRD.'nsm_reports/reports';
	}

	public function get_folder_path()
	{
		return $this->folder_path;
	}

	public function find_all()
	{
		$report_folders = directory_map($this->folder_path);
		$reports = false;
		foreach ($report_folders as $folder => $files)
		{
			if(substr($folder, 0, 1) == "-") continue;
			
			if(file_exists($this->folder_path."/".$folder."/".$folder.EXT)){
				require($this->folder_path."/".$folder."/".$folder.EXT);
				$class = ucfirst($folder);
				$reports[$class] = array(
					'title' => $class::$title,
					'notes' => $class::$notes,
					'author' => $class::$author,
					'docs_url' => $class::$docs_url,
					'version' => $class::$version,
					'type' => $class::$type
				);
			}
		}
		return $reports;
	}

	function find($report){
		$file_path = $this->folder_path."/".$report."/".$report.EXT;
		if(!$report || !file_exists($file_path)) return false;
		require_once $file_path;
		return new $report;
	}
}