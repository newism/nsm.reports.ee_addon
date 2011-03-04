<?php

class Basic_report extends Nsm_report {

	public static $title = 'Basic channel entries report';
	public static $notes = 'Outputs channel entries';
	public static $author = 'Iain Saxon - Newism';
	public static $docs_url = 'http://google.com';
	public static $version = '1.0.0';
	public static $type = 'simple';

	public $error = false;

	public function __construct(){
		parent::__construct();
	}

	/**
	 * Generates the SQL statement
	 * 
	 * @param $output string The output of the report
	 * @param $options array The configuration options for the report
	 * @return object DB Result object
	 */
	public function generateResults($options = array())
	{
		$sql = "SELECT
			`p`.`entry_id` AS `ID`,
			`p`.`title` AS `Title`,
			`p`.`url_title` AS `URL Title`,
			`p`.`channel_id` AS `Channel ID`
		FROM `exp_channel_titles` AS `p`
		ORDER BY `p`.`channel_id`";
		
		$sql = $this->sanitiseSQL($sql);
		$this->sql = $sql;
		$this->EE->db->db_debug = false;
		$query = $this->EE->db->query($this->sql);
		if ($query == false){
			return false;
		}
		return $query;
	}
}