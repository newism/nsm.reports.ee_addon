<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * EasySigns Commerce Model 
 *
 * @package			EasysignsCommerce
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/easysigns-commerce
 * @see				http://codeigniter.com/user_guide/general/models.html
 **/
class Nsm_saved_report {
	
	public $id = "";
	public $title = "";
	public $description = "";
	public $access_key = "";
	public $created_at = "";
	public $updated_at = "";
	public $lastrun_at = "";
	public $report = "";
	public $email_address = "";
	public $chart_data = 0;
	public $active = 0;
	public $output = "";
	public $config = array();
	public $run_count = 0;

	public function __construct($data = array()) {
		if(count($data) > 0){
			$this->setData($data);
		}
	}
	
	/**
	 * PHP5 toString function.
	 * Returns a string version of class instance
	 *
	 * @access public
	 * @return string This instance's title
	 **/
	public function __toString()
	{
		return $this->title;
	}
	
	// adds auto report to database
	public function add()
	{
		$EE =& get_instance();
		$EE->db->insert(self::$table_name, $this->_prepareData());
		return $EE->db->insert_id();
	}

	// updates the database entry
	public function update()
	{
		$EE =& get_instance();
		$EE->db->update(self::$table_name, $this->_prepareData(), array('id' => $this->id));
		return $EE->db->affected_rows();
	}
	
	// deletes the database entry
	public function delete()
	{
		$EE =& get_instance();
		$EE->db->delete(self::$table_name, array('id' => $this->id));
		return $EE->db->affected_rows();
	}
	
	/**
	 * Prepares object data for update insert
	 *
	 * @return array The modified data
	 */
	private function _prepareData(){

		$EE =& get_instance();
		$EE->load->helper('date');

		$data = array();
		foreach (self::$table_fields as $key => $definition) {
			$new_value = "";
			switch ($key) {
				case 'created_at' :
					if($this->created_at < 1){
						$new_value = now();
						$this->created_at = $new_value;
					}else{
						$new_value = $this->{$key};
					}
				break;
				case 'config' :
					$new_value = ( is_array($this->{$key}) ? json_encode($this->{$key}) : $this->{$key});
				break;
				default:
					if(property_exists($this, $key)){
						$new_value = $this->{$key};
					}
				break;
			}
			if($key !== 'id'){
				$data[$key] = $new_value;
			}
		}
		return $data;
	}

	/**
	 * Sets the object data
	 */
	public function setData($data = array()) {
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				if(property_exists($this, $key)){
					switch($key){
						case 'config':
							$new_value = ( !is_array($value) ? json_decode($value, true) : $value);
						break;
						default:
							$new_value = $value;
						break;
					}
					$this->{$key} = $new_value;
				}
			}
		}
	}
	
	
	// returns all auto report objects
	public static function findAll()
	{
		$EE =& get_instance();
		$get_auto_reports = $EE->db->get(self::$table_name);
		$results = $get_auto_reports->result_array();
		$auto_reports = array();
		if(count($results) > 0){
			foreach($results as $result){
				$report = new Nsm_saved_report($result);
				$auto_reports[] = $report;
			}
		}
		return $auto_reports;
	}
	
	// returns auto report objects where matching id and access key
	public static function findByIdKey($id, $key)
	{
		$EE =& get_instance();
		$get_auto_report = $EE->db->get_where(self::$table_name, array('id' => $id, 'access_key' => $key));
		$results = $get_auto_report->result_array();
		$auto_report = array();
		if(count($results) > 0){
			$result = $results[0];
			$report = new Nsm_saved_report($result);
			$auto_report = $report;
		}else{
			return false;
		}
		return $auto_report;
	}
	
	// returns auto report objects where matching id
	public static function findById($id)
	{
		$EE =& get_instance();
		$get_auto_report = $EE->db->get_where(self::$table_name, array('id' => $id));
		$results = $get_auto_report->result_array();
		$auto_report = array();
		if(count($results) > 0){
			$result = $results[0];
			$report = new Nsm_saved_report($result);
			$auto_report = $report;
		}else{
			return false;
		}
		return $auto_report;
	}
	
	// returns auto report objects where matching id
	public static function findByIds($ids)
	{
		$EE =& get_instance();
		$EE->db->from(self::$table_name);
		$EE->db->where_in('id', $ids);
		$get_saved_reports = $EE->db->get();
		$results = $get_saved_reports->result_array();
		$saved_reports = array();
		if(count($results) > 0){
			foreach($results as $result){
				$saved_reports[] = new Nsm_saved_report($result);
			}
		}else{
			return false;
		}
		return $saved_reports;
	}
	
	// returns auto report objects where matching id
	public static function deleteByIds($ids)
	{
		$EE =& get_instance();
		$EE->db->where_in('id', $ids);
		if($EE->db->delete(self::$table_name)){
			return true;
		}
		return false;
	}
	
	/**
	 * The model table
	 * 
	 * @var string
	 */
	private static $table_name = "nsm_reports_saved_reports";

	/**
	 * The model table fields
	 * 
	 * @var array
	 */
	private static $table_fields = array(
		"id" 				=> array('type' => 'INT', 'constraint' => '10', 'auto_increment' => TRUE, 'unsigned' => TRUE),
		"title"				=> array('type' => 'VARCHAR', 'constraint' => '255'),
		"description"		=> array('type' => 'TEXT'),
		"access_key"		=> array('type' => 'VARCHAR', 'constraint' => '255'),
		"created_at" 		=> array('type' => 'INT', 'constraint' => '10'),
		"updated_at" 		=> array('type' => 'INT', 'constraint' => '10'),
		"lastrun_at" 		=> array('type' => 'INT', 'constraint' => '10'),
		"report" 			=> array('type' => 'VARCHAR', 'constraint' => '255'),
		"email_address" 	=> array('type' => 'VARCHAR', 'constraint' => '255'),
		"output" 			=> array('type' => 'VARCHAR', 'constraint' => '5'),
		"config" 			=> array('type' => 'TEXT'),
		"chart_data"	 	=> array('type' => 'TINYINT', 'constraint' => '1'),
		"active"	 		=> array('type' => 'TINYINT', 'constraint' => '1'),
		"run_count"	 		=> array('type' => 'INT', 'constraint' => '10')
	);

	/**
	 * Create the model table
	 */
	public static function createTable()
	{
		$EE =& get_instance();
		$EE->load->dbforge();
		$EE->dbforge->add_field(self::$table_fields);
		$EE->dbforge->add_key('id', TRUE);

		if (!$EE->dbforge->create_table(self::$table_name, TRUE))
		{
			show_error("Unable to create table in ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$table_name);
			log_message('error', "Unable to create settings table for ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$table_name);
		}
	}
}