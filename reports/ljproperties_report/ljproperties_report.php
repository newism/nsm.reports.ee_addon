<?php

class Ljproperties_report extends Nsm_report {

	public static $title = 'Property List';
	public static $notes = 'This is a test';
	public static $author = 'Iain Saxon - Newism';
	public static $docs_url = 'http://google.com';
	public static $version = '1.0.0';
	public static $type = 'advanced';

	public $error = false;

	protected $config = array(
		'_output' => 'xml',
		'bedrooms' => 1,
		'type' => 'house'
	);

	public function __construct(){
		parent::__construct();
	}

	public function configHTML()
	{
		$property_types = $this->EE->db->query('
			SELECT DISTINCT `field_id_5` 
			AS `type` FROM `exp_channel_data` 
			WHERE `field_id_5` != ""
			ORDER BY `field_id_5`'
		);

		return $this->EE->load->_ci_load(array(
			'_ci_vars' => array(
				'config' => $this->config,
				'property_types' => $property_types->result_array()
			),
			'_ci_path' => $this->report_path . "views/configuration.php",
			'_ci_return' => true
		));
	}

	/**
	 * Generates the SQL statement
	 * 
	 * @param $output string The output of the report
	 * @param $options array The configuration options for the report
	 * @return object DB Result object
	 */
	public function generateResults()
	{
		$config = $this->config;

		$type_cond = ($config['type'])
			? " AND `d`.`field_id_5` = '".$config['type']."'"
			: false;

		$bed_cond = ($config['bedrooms'])
			? " AND `d`.`field_id_11` > ".intval($config['bedrooms'])
			: false;

		$sql = "SELECT
			`p`.`entry_id` AS `ID`,
			`p`.`title` AS `Property Name`,
			`d`.`field_id_11` AS `Bedrooms`,
			`d`.`field_id_5` AS `Type`,".
			($config['_output']=='browser' ? "`p`.`url_title` AS `URL`, `p`.`channel_id` AS `Channel ID`," : "") .
			"
			COUNT(`i`.`row_id`) AS `Number of Images`
		FROM `exp_channel_titles` AS `p`
		LEFT JOIN `exp_channel_data` AS `d`
			ON `d`.`entry_id` = `p`.`entry_id`
		LEFT JOIN `exp_matrix_data` AS `i`
			ON `i`.`entry_id` = `p`.`entry_id`
			AND `i`.`field_id` = 26
		WHERE `p`.`channel_id` = 2"
		. $type_cond 
		. $bed_cond
		. " GROUP BY `p`.`entry_id`";

		$sql = $this->sanitiseSQL($sql);
		$this->sql = $sql;
		$this->EE->db->db_debug = false;
		$query = $this->EE->db->query($this->sql);
		if ($query == false){
			return false;
		}
		return $query;
	}

	public function outCron($query) {
	}

	public function outputBrowser($query)
	{
		$columns = array();
		$rows = array();
		foreach ($query->result_array() as $key => $value) {
			if($key == 0){ 
				foreach($value as $column => $data){ $columns[] = $column; }
			}
			$rows[$key] = $value;
			$rows[$key]['entry_url'] = BASE . AMP . "C=content_publish&M=entry_form&channel_id={$value['Channel ID']}&entry_id={$value['ID']}";
		}
		
		$data = array(
			'columns' => $columns,
			'rows' => $rows,
			'config' => $this->config,
			'input_prefix' => __CLASS__
		);
		
		return $this->EE->load->_ci_load(array(
			'_ci_vars' => $data,
			'_ci_path' => $this->report_path . "views/output_browser.php",
			'_ci_return' => true
		));
		
	}

}