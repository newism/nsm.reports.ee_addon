<?php
/**
 * Code Igniter View of report configuration page in Control Panel
 *
 * This file is used when Nsm_reports_mcp::configure() is called and returns the report details and configuration form to the user.
 *
 * @package NsmReports
 * @version 1.0.9
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au> * @copyright Copyright (c) 2007-2011 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html
 */
?>

<?php if(!$report_output_dir['status']) : ?>
	<div class="alert <?= $report_output_dir['class'] ?>"><?= $report_output_dir['message'] ?></div>
<?php endif; ?>
	
<div class="tg">	
	<h2>Report details</h2>
	<table class="data">
		<thead>
			<tr>
				<th scope="col">Title</th>
				<th scope="col">Description</th>
				<th scope="col">Author</th>
				<th scope="col">Version</th>
				<th scope="col">Documentation</th>
			</tr>
		</thead>
		<tbody>
			<tr class="even">
				<th scope="row"><?= $report['title'] ?></th>
				<td><?= $report['notes'] ?></td>
				<td><?= $report['author'] ?></td>
				<td><?= $report['version'] ?></td>
				<td><?php if($report['docs_url']) : ?><a href="<?= $report['docs_url'] ?>">Documentation</a><?php endif; ?>&nbsp;</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="tg" id="nsm-report-config">
	<h2>Customise output</h2>

	<?php if($error) : ?>
		<div class="alert error"><?= $error; ?></div>
	<?php endif; ?>
	<?php if($saved_report_info) : ?>
		<div class="alert info"><?= $saved_report_info; ?></div>
	<?php endif; ?>

	<table class="NSM_Stripeable">
		<tbody>
			<?= $report_config_html; ?>
			<tr>
				<th scope="row"><label for="nsm_reports-generate-output">Output format</label></th>
				<td>
					<select id="nsm_reports-generate-output" name="report[_output]">
					<?php foreach ($report['output_types'] as $type => $type_human) : ?>
						<option value="<?= $type ?>"<?=($type==$config['_output']?' selected="selected"':'') ?>><?= $type_human ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="nsm_reports-generate-email">Email report</label>
					<div class="note">Optional: To send the generated report via email enter an address.</div>
				</th>
				<td>
					<input type="text" name="report[_send_to_email_address]" id="nsm_reports-generate-email" placeholder="user@domain.com" value="<?= $config['_send_to_email_address'] ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">Action</th>
				<td>
					<select id="nsm_reports-generate-report-action" name="action">
						<option id="nsm_reports-generate-report-action-generate" value="generate">Generate report</option>
						<?php if($saved_report_id) : ?>
							<option id="nsm_reports-generate-report-action-save" value="save"<?= ($selected_form_action == 'save' ? ' selected="selected"' : '') ?>>Update saved preset</option>
						<?php endif; ?>
						<option id="nsm_reports-generate-report-action-new" value="new"<?= ($selected_form_action == 'new' ? ' selected="selected"' : '') ?>>Save as new preset</option>
					</select>
				</td>
			</tr>
			</tbody>
		</table>
		<div id="nsm_reports-generate-report-preset-container">
			<div class="alert info">
					<p>Report configuration presets can be loaded from the <a href="<?= $saved_reports_url ?>">Saved Reports</a> page in this module.<br/>
					Report presets generate an external URL that can be shared or used in a cron job.<br/>
					If an email address is specified then the report download link is emailed to the recipient.</p>
			</div>
			<table class="NSM_Stripeable">
				<tbody>
				<tr>
					<th scope="row">
						<label for="nsm_reports-generate-report-save_report_name">Preset name</label>
						<div class="note">Add a title to this report configuration for future reference</div>
					</th>
					<td>
						<input type="text" name="report[_save_report_name]" id="nsm_reports-generate-save_report_name" value="<?= $config['_save_report_name'] ?>"/>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="nsm_reports-generate-report-save_report_description">Preset description</label>
						<div class="note">Add notes to this report configuration to describe the purpose or outcome of the report</div>
					</th>
					<td>
						<textarea name="report[_save_report_description]" id="nsm_reports-generate-save_report_description"><?= $config['_save_report_description'] ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="actions">
		<input type="submit" class="submit" value="Submit"/>
	</div>
</div>

<?= $preview_html ?>
