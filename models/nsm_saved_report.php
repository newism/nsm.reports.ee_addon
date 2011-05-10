<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * NSM Reports - Saved Configuration Preset
 *
 * @package NsmReports
 * @version 1.0.2
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/easysigns-commerce
 * @see http://codeigniter.com/user_guide/general/models.html
 **/

/**
 * Model for Saved Report Configurations
 *
 * Used to represent an instance of a saved configuration preset.
 * Model refers to its properties using $this and has methods to CRUD the instance.
 * Also includes static 'helper' methods to retrieve and delete preset collections.
 *
 * @package NsmReports
 */
class Nsm_saved_report {
	
	/**
	 * Preset configuration Database Identifier
	 *
	 * @var string
	 * @access public
	 **/
	public $id = "";
	
	/**
	 * Preset configuration name
	 *
	 * @var string
	 * @access public
	 **/
	public $title = "";
	
	/**
	 * Description of preset configuration
	 *
	 * @var string
	 * @access public
	 **/
	public $description = "";
	
	/**
	 * The security key to be used to access the preset
	 *
	 * @var string
	 * @access public
	 **/
	public $access_key = "";
	
	/**
	 * Unix-timestamp of the creation date
	 *
	 * @var int
	 * @access public
	 **/
	public $created_at = 0;
	
	/**
	 * Unix-timestamp of the last update
	 *
	 * @var int
	 * @access public
	 **/
	public $updated_at = 0;
	
	/**
	 * Unix-timestamp of the last time that the preset was run
	 *
	 * @var int
	 * @access public
	 **/
	public $lastrun_at = 0;
	
	/**
	 * The class name of the module that the preset is assigned to
	 *
	 * @var string
	 * @access public
	 **/
	public $report = "";
	
	/**
	 * Email Recipient of generated reports
	 *
	 * @var string
	 * @access public
	 **/
	public $email_address = "";
	
	/**
	 * Active status of preset
	 *
	 * @var bool
	 * @access public
	 **/
	public $active = 0;
	
	/**
	 * Output type of generated reports
	 *
	 * @var string
	 * @access public
	 **/
	public $output = "";
	
	/**
	 * Preset configuration options as json-decoded array
	 *
	 * @var array
	 * @access public
	 **/
	public $config = array();
	
	/**
	 * Number of times that the report preset has been run
	 *
	 * @var int
	 * @access public
	 **/
	public $run_count = 0;
	
	
	/**
	 * PHP5 constructor function.
	 *
	 * Creates an instance of the object and assigns any values that have been passed to the constructor to the object
	 *
	 * @access public
	 * @param array $data Information to be assigned to the object instance
	 * @return void
	 **/
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
	
	/**
	 * Inserts the instance into the database.
	 *
	 * @access public
	 * @return int Inserted ID of preset
	 **/
	public function add()
	{
		$EE =& get_instance();
		$EE->db->insert(self::$table_name, $this->_prepareData());
		return $EE->db->insert_id();
	}

	/**
	 * Updates the saved preset details in the database with the new values.
	 *
	 * @access public
	 * @return int Number of affected rows (should be '1' if successful)
	 **/
	public function update()
	{
		$EE =& get_instance();
		$EE->db->update(self::$table_name, $this->_prepareData(), array('id' => $this->id));
		return $EE->db->affected_rows();
	}
	
	/**
	 * Deletes the preset from the database
	 *
	 * @access public
	 * @return int Number of affected rows (should be '1' if successful)
	 **/
	public function delete()
	{
		$EE =& get_instance();
		$EE->db->delete(self::$table_name, array('id' => $this->id));
		return $EE->db->affected_rows();
	}
	
	/**
	 * Prepares object data for update/insert commands
	 *
	 * Only array keys that are present in the model's database table are included
	 *
	 * @access private
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
					$new_value = (is_array($this->{$key}))
									? $EE->javascript->generate_json($this->{$key}, true)
									: $this->{$key};
				break;
				default:
					if(property_exists($this, $key)) {
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
	 * Assigns applicable data from array to the object
	 *
	 * Only array keys that correspond to properties of the model are processed 
	 *
	 * @access public
	 * @param array $data Data to be assigned to the object
	 * @return void
	 */
	public function setData($data = array()) {
		if(count($data) > 0){

			if ( ! function_exists('json_decode')) {
				$EE->load->library('Services_json');
			}

			foreach ($data as $key => $value) {
				if(property_exists($this, $key)){
					switch($key){
						case 'config':
							$new_value = (!is_array($value))
											? json_decode($value, true)
											: $value;
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
	
	/**
	 * Finds all saved report presets and returns them as an array of object instances
	 *
	 * @access public
	 * @static
	 * @return array Collection of configuration presets
	 **/
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
	
	/**
	 * Returns report preset objects matching the id and access key
	 *
	 * @access public
	 * @static
	 * @param int $id The ID to find from the database
	 * @param string $key Access key of preset
	 * @return array Collection of configuration presets
	 **/
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
	
	/**
	 * Returns a single instance of a configuration preset from the database matching the ID
	 *
	 * @access public
	 * @static
	 * @param int $id The ID to find from the database
	 * @return object Populated instance of Nsm_saved_report
	 **/
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
	
	/**
	 * Returns array of report preset objects that appear in the array parameter
	 *
	 * @access public
	 * @static
	 * @param array $ids Integer-based array of IDs to filter by
	 * @param string $key Access key of preset
	 * @return array Collection of configuration presets
	 **/
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
	
	/**
	 * Deletes preset configurations from database where the IDs match the parameter array
	 *
	 * @access public
	 * @static
	 * @param array $ids The IDs of presets to be deleted from the database
	 * @return bool Status of database operation
	 **/
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
	 * The name of the database table this model uses
	 * 
	 * @static
	 * @var string
	 */
	private static $table_name = "nsm_reports_saved_reports";

	/**
	 * The fields that appear in database table
	 * 
	 * @static
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
		"output" 			=> array('type' => 'VARCHAR', 'constraint' => '255'),
		"config" 			=> array('type' => 'TEXT'),
		"chart_data"	 	=> array('type' => 'TINYINT', 'constraint' => '1'),
		"active"	 		=> array('type' => 'TINYINT', 'constraint' => '1'),
		"run_count"	 		=> array('type' => 'INT', 'constraint' => '10')
	);

	/**
	 * Create the model's table in the database
	 * 
	 * This method is called during the installation process of the module
	 * 
	 * @access public
	 * @static
	 * @return void
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