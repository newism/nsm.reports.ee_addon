<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NSM Reports Fieldtype
 *
 * @package			NsmReports
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://expressionengine-addons.com/nsm-reports
 * @see				http://expressionengine.com/public_beta/docs/development/fieldtypes.html
 * @ignore
 */

class Nsm_reports_ft extends EE_Fieldtype
{
	/**
	 * Field info - Required
	 * 
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name'		=> 'NSM Reports',
		'version'	=> '1.0.0'
	);

	/**
	 * The fieldtype global settings array
	 * 
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * The field type - used for form field prefixes. Must be unique and match the class name. Set in the constructor
	 * 
	 * @access private
	 * @var string
	 */
	public $field_type = '';

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	public function __construct()
	{
		$this->field_type = strtolower(substr(__CLASS__, 0, -3));
		parent::EE_Fieldtype();
	}	

	/**
	 * Replaces the custom field tag
	 * 
	 * @access public
	 * @param $data string Contains the field data (or prepped data, if using pre_process)
	 * @param $params array Contains field parameters (if any)
	 * @param $tagdata mixed Contains data between tag (for tag pairs) FALSE for single tags
	 * @return string The HTML replacing the tag
	 * 
	 */
	public function replace_tag($data, $params = FALSE, $tagdata = FALSE)
	{
		return "Tag content";
	}

	/**
	 * Install the fieldtype
	 *
	 * @return array The default settings for the fieldtype
	 */
	public function install()
	{
		return array("setting_1" => TRUE);
	}

	/**
	 * Display the field in the publish form
	 * 
	 * @access public
	 * @param $data String Contains the current field data. Blank for new entries.
	 * @return String The custom field HTML
	 */
	public function display_field($data)
	{
		return "Field content";
	}

	/**
	 * Displays the cell - MATRIX COMPATIBILITY
	 * 
	 * @access public
	 * @param $data The cell data
	 * @return string The cell HTML
	 */
	public function display_cell($data)
	{
		return "Cell content";
	}

	/**
	 * Publish form validation
	 * 
	 * @access public
	 * @param $data array Contains the submitted field data.
	 * @return mixed TRUE or an error message
	 */
	public function validate($data)
	{
		return TRUE;
	}


	/**
	 * Display a global settings page. The current available global settings are in $this->settings.
	 *
	 * @access public
	 * @return string The global settings form HTML
	 */
	public function display_global_settings()
	{
		return "Global settings";
	}
	
	/**
	 * Display the settings form for each custom field
	 * 
	 * @access public
	 * @param $data mixed Not sure what this data is yet :S
	 * @return string Override the field custom settings with custom html
	 */
	public function display_settings($field_settings)
	{
		$rows = $this->_fieldSettings($field_settings);

		// add the rows
		foreach ($rows as $row)
			$this->EE->table->add_row($row[0], $row[1]);
	}

	/**
	 * Display Cell Settings - MATRIX
	 * 
	 * @access public
	 * @param $cell_settings array The cell settings
	 * @return array Label and form inputs
	 */
	public function display_cell_settings($cell_settings)
	{
		$rows = $this->_fieldSettings($cell_settings);
		return $rows;
	}

	/**
	 * Prepares settings array for fields and matrix cells
	 * 
	 * @access public
	 * @param $settings array The field / cell settings
	 * @return array Labels and form inputs
	 */
	private function _fieldSettings($settings)
	{
		$r = array();
		$r[] = array("Key", "Value");
		return $r;
	}

	/**
	 * Save the global settngs
	 *
	 * @access public
	 * @return array The new global settings
	 */
	 public function save_global_settings()
	 {
	 	$new_settings = array_merge($this->settings, $this->EE->input->post(__CLASS__));
	 	return $new_settings;
	 }

	/**
	 * Save the custom field settings
	 * 
	 * @param $data array The submitted post data.
	 * @return array Field settings
	 */
	public function save_settings($data)
	{
		$field_settings = $this->EE->input->post(__CLASS__);

		// Force formatting
		// $field_settings['field_fmt'] = 'none';
		// $field_settings['field_show_fmt'] = 'n';
		// $field_settings['field_type'] = $this->field_type;

		// Cleanup
		unset($_POST[__CLASS__]);
		foreach (array_keys($field_settings) as $setting)
		{
			if (isset($_POST[__CLASS__."_".$setting]))
				unset($_POST[__CLASS__."_".$setting]);
		}

		return $field_settings;
	}

	/**
	 * Process the cell settings before saving
	 * 
	 * @access public
	 * @param $col_settings array The settings for the column
	 * @return array The new settings
	 */
	public function save_cell_settings($col_settings)
	{
		$col_settings = $col_settings[$this->field_type];
		return $col_settings;
	}

}
//END CLASS