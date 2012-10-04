<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_reports/config.php';

/**
 * Install / Uninstall and updates the modules
 *
 * @package NsmReports
 * @version 1.0.7
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html#update_file
 */

/**
 * Module Install/Uninstall/Update management object
 *
 * @package NsmReports
 */
class Nsm_reports_upd
{
	/**
	 * Current version of module that is displayed in Modules page in the Control Panel
	 *
	 * @var string
	 * @access public
	 */
	public $version = '1.0.7';
	
	/**
	 * Determines whether module requires a Control Panel to include
	 *
	 * @var array
	 * @access private
	 */
	private $has_cp_backend = true;
	
	/**
	 * Determines whether module adds custom input fields to Publish form
	 *
	 * @var array
	 * @access private
	 */
	private $has_publish_fields = false;
	
	/**
	 * Determines whether module uses Publish form tabs
	 *
	 * @var bool
	 * @access private
	 */
	private $has_tabs = false;
	
	/**
	 * Tabs that the module needs for the Publish form
	 *
	 * @var array
	 * @access private
	 */
	private $tabs = array();
	
	/**
	 * Contains all actions that the database will use
	 *
	 * Actions are inserted during module install and removed during uninstall
	 *
	 * @var array
	 * @access private
	 */
	private $actions = array(
		'Nsm_reports_mcp::download_generated_report',
		'Nsm_reports_mcp::cron_generate'
	);
	
	/**
	 * Contains all database-driven models that the module will use
	 *
	 * Models are iterated over during install to check if database tables need to be created
	 *
	 * @var array
	 * @access private
	 */
	private $models = array(
		'Nsm_saved_report',
	);
	
	/**
	 * PHP5 constructor function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() 
	{ 
		
	}    

	/**
	 * Installs the module
	 * 
	 * Installs the module, adding a record to the exp_modules table, creates and populates
	 *   and necessary database tables, adds any necessary records to the exp_actions table, 
	 *   and if custom tabs are to be used, adds those fields to any saved publish layouts.
	 *
	 * @access public
	 * @author Leevi Graham
	 * @return bool
	 **/
	public function install()
	{
		$EE =& get_instance();
		$data = array(
			'module_name' => substr(__CLASS__, 0, -4),
			'module_version' => $this->version,
			'has_cp_backend' => ($this->has_cp_backend) ? "y" : "n",
			'has_publish_fields' => ($this->has_publish_fields) ? "y" : "n"
		);
		
		$EE->db->insert('modules', $data);

		// Add layout tabs
		if($this->has_publish_fields)
			$EE->cp->add_layout_tabs($this->tabs, $data["module_name"]);

		// Add the actions
		if(isset($this->actions) && is_array($this->actions))
		{
			foreach ($this->actions as $action)
			{
				$parts = explode("::", $action);
				$EE->db->insert('actions', array(
					"class" => $parts[0],
					"method" => $parts[1]
				));
			}
		}

		// Install the model tables
		if($this->models)
		{
			foreach($this->models as $model)
			{
				if(!class_exists($model)){
					include( PATH_THIRD . "nsm_reports/models/" . strtolower($model) .".php");
					if(method_exists($model, "createTable")){
						call_user_func(array("{$model}", 'createTable'));
					}
				}
			}
		}
		return TRUE;
	}

	/**
	 * Updates the module
	 * 
	 * This function is checked on any visit to the module's control panel, and compares the current 
	 *   version number in the file to the recorded version in the database. This allows you to easily
	 *   make database or other changes as new versions of the module come out.
	 *
	 * @access public
	 * @author Leevi Graham
	 * @return bool FALSE if no update is necessary, TRUE if it is.
	 */
	public function update($current = FALSE)
	{
		$EE =& get_instance();
		if($current == $this->version){
			return false;
		}
		if ( ! function_exists('json_decode')){
			$EE->load->library('Services_json');
		}
		if($current < '1.0.6'){
			$EE->db
				->where('class', substr(__CLASS__, 0, -4).'_mcp' )
				->where('method', 'generate' )
				->update('actions', array('method' => 'cron_generate'));
			
		}
		// Update the extension
		$EE->db
			->where('module_name', substr(__CLASS__, 0, -4) )
			->update('modules', array('module_version' => $this->version));
		return true;
	}

	/**
	 * Uninstalls the module
	 *
	 * @access public
	 * @author Leevi Graham
	 * @return bool FALSE if uninstall failed, TRUE if it was successful
	 */
	public function uninstall()
	{

		$EE =& get_instance();
		$module_name = substr(__CLASS__, 0, -4);

		$EE->db->select('module_id');
		$query = $EE->db->get_where('modules', array('module_name' => $module_name));

		$EE->db->where('module_id', $query->row('module_id'));
		$EE->db->delete('module_member_groups');

		$EE->db->where('module_name', $module_name);
		$EE->db->delete('modules');

		$EE->db->where('class', $module_name);
		$EE->db->delete('actions');

		$EE->db->where('class', $module_name . "_mcp");
		$EE->db->delete('actions');
		
		if($this->has_publish_fields){
			$EE->cp->delete_layout_tabs($this->tabs(), $module_name);
		}
		return true;
	}

	/**
	 * Returns the static tab array
	 *
	 * @access public
	 * @author Leevi Graham
	 * @return array the modules tabs
	 */
	public function tabs()
	{
		return $this->tabs;
	}


}