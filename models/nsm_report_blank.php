<?php

class NSM_REPORTS_REPLACE_CLASS_NAME extends Nsm_report {
	
	public static $title = "NSM_REPORTS_REPLACE_TITLE";
	public static $notes = "NSM_REPORTS_REPLACE_NOTES";
	public static $author = "NSM_REPORTS_REPLACE_AUTHOR";
	public static $docs_url = "NSM_REPORTS_REPLACE_DOCS_URL";
	public static $version = "NSM_REPORTS_REPLACE_VERSION";
	public static $type = "NSM_REPORTS_REPLACE_TYPE";
	
	public static $output_types = array('Browser', 'PDF', 'HTML', 'XML', 'CSV');
	
	public $error = false;
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Generates the report results
	 * 
	 * @param $output string The output of the report
	 * @param $options array The configuration options for the report
	 * @return object DB Result object
	 */
	public function generateSQL($output, $options) {
		$sql = "NSM_REPORTS_REPLACE_SQL";
		$sql = $this->sanatiseSql($sql);
		// 
	}

}