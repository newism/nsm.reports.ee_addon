<?php

/**
 * NSM Reports Language File
 *
 * @package NsmReports
 * @version 1.0.10
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html#lang_file
 */

/**
 * The module's language file
 *
 * @var array
 */
$lang = array(

	/* Module */
	'nsm_reports' => 'NSM Reports',
	'nsm_reports_module_name' => 'NSM Reports',
	'nsm_reports_module_description' => 'Extensible reports module',

	'nsm_reports_index_page_title' => 'Reports',
	'nsm_reports_index_nav_title' => 'Reports',
	'nsm_reports_index_nav_title' => 'Reports',
	
	'nsm_reports_create_report_nav_title' => 'Create report',
	'nsm_reports_create_report_page_title' => 'Create report',
	
	'nsm_reports_report_config_page_title' => '{report_title}',
	'nsm_reports_report_config_nav_title' => 'Report',
	
	'nsm_reports_details_simple_page_title' => 'Report Details: {report_title}',
	'nsm_reports_details_simple_nav_title' => 'Details',

	'nsm_reports_delete_simple_page_title' => 'Delete Report: {report_title}',
	'nsm_reports_delete_simple_nav_title' => 'Delete',
	
	'nsm_reports_upload_simple_page_title' => 'Upload Report',
	'nsm_reports_upload_simple_nav_title' => 'Upload Report',
	
	'nsm_reports_saved_reports_page_title' => 'Saved reports',
	'nsm_reports_saved_reports_nav_title' => 'Saved reports',
	
	'nsm_reports_details_auto_report_page_title' => 'Auto Report Details',
	'nsm_reports_details_auto_report_nav_title' => 'Auto Report Details',
	
	'nsm_reports_config_auto_report_page_title' => 'Configure Auto Report',
	'nsm_reports_config_auto_report_nav_title' => 'Configure Auto Report',
	
	'nsm_reports_delete_saved_report_page_title' => 'Delete Auto Report',
	'nsm_reports_delete_auto_report_nav_title' => 'Delete Auto Report',
	
	'nsm_reports_output_page_title' => 'Report output',
	
	'nsm_reports_messages_generated_report_path_ok' => 'The generated reports path <strong>%1$s</strong> is ready for use.',
	'nsm_reports_messages_generated_report_path_not_exists' => 'The generated reports path <strong>%1$s</strong> does not exist. '.
																'Please make sure that this directory exists and has sufficient file permissions to write files.'.
																'This directory can be changed in the <a href="%2$s">extension settings</a> for this module.',
	'nsm_reports_messages_generated_report_path_not_writeable' => 'The generated reports path <strong>%1$s</strong> needs sufficient file permissions to save reports. '.
																	'Please check the file permissions for this directory. '.
																	'This directory can be changed in the <a href="%2$s">extension settings</a> for this module.',
	
	/* Extension */
	'save_extension_settings' => 'Save extension settings',

	/* Messages / Alerts */
	'alert.success.extension_settings_saved' => 'Extension settings have been saved.',
	
	/* Output */
	'nsm_reports_configure_no_report' => 'No report found',
	'nsm_reports_configure_no_preset' => 'No report preset found',
	'nsm_reports_configure_preset_info' => 'Now using saved report configuration \'%1$s\' (ID %2$s). '.
											'Any changes made to the configuration will not be saved until you change the Action to \'Save\' and submit the form.',
	
	'nsm_reports_generate_no_preset' => 'No report preset found',
	'nsm_reports_generate_no_report' => 'No report found',
	'nsm_reports_generate_illegal_output' => 'You cannot remote access a report preset that outputs to the browser.',
	'nsm_reports_generate_output_fail' => 'There was a problem generating the report.',
	'nsm_reports_generate_send_email_subject' => 'Sending Report: %1$s using preset %2$s (%3$s)',
	'nsm_reports_generate_send_email_message' => 'This email was sent from your website using a process URL. '.
												'Your report %1$s using preset %2$s (%3$s) '.
												'has been generated and ready for download at this location: '.
												'%4$s',
	'nsm_reports_generate_send_email_ok' => 'The report was successfully sent to the specified email address.',
	'nsm_reports_generate_send_email_error' => 'There was an error sending the report.',
	'nsm_reports_generate_ok' => 'The report was successfully generated.',
	
	'nsm_reports_save_no_preset' => 'No saved report found',
	'nsm_reports_save_ok' => 'The report preset \'%1$s\' was successfully saved.',
	'nsm_reports_save_model_error' => 'There was an error saving the new report preset.',
	
	'nsm_reports_delete_saved_no_presets' => 'No saved reports found',
	
	'nsm_reports_delete_saved_submit_no_ids' => 'No report presets were chosen for deletion.',
	'nsm_reports_delete_saved_submit_ok' => 'The saved reports were successfully deleted.',
	'nsm_reports_delete_saved_submit_error' => 'There was an error deleting the saved reports.',
	
	'nsm_reports_download_generated_report_invalid_group_id' => 'You are not logged in to a member group that permits reporting.',
	'nsm_reports_download_generated_report_no_file' => 'No file found.',
	'nsm_reports_download_generated_report_path_not_exists' => 'The generated reports path is not set or does not exist.',
	
);
