<?php
/**
 * View for Control Panel Settings Form
 * 
 * This file is responsible for displaying the user-configurable settings for the NSM Multi Language extension in the ExpressionEngine control panel.
 *
 * @package NsmReports
 * @version 1.0.9
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism
 * @license Commercial - please see LICENSE file included with this distribution
 */

$EE =& get_instance();

?>

<div class="mor">
	<?= form_open(
			'C=addons_extensions&M=extension_settings&file=' . $addon_id,
			array('id' => $addon_id . '_prefs'),
			array($input_prefix."[enabled]" => TRUE)
		)
	?>

	<!-- 
	===============================
	Alert Messages
	===============================
	-->

	<?php if($error) : ?>
		<div class="alert error"><?php print($error); ?></div>
	<?php endif; ?>

	<?php if($message) : ?>
		<div class="alert success"><?php print($message); ?></div>
	<?php endif; ?>
	
	<?php if(!$report_output_dir['status']) : ?>
		<div class="alert <?= $report_output_dir['class'] ?>"><?= $report_output_dir['message'] ?></div>
	<?php endif; ?>
	
	<div class="tg">
		<h2>General settings</h2>
		<table>
			<tbody>
				<tr class="even">
					<th scope="row">Report path 
						<div class="note">
							This is the server path that will be used to store the reports.
							If this is not set it will default to the 'reports' sub-directory of this add-on.
						</div>
					</th>
					<td>
						<input type="text" name="<?= $input_prefix ?>[report_path]" value="<?= $data['report_path'] ?>"/>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="tg">
		<h2>Download configuration</h2>
		<table>
			<tbody>
				<tr class="even">
					<th scope="row">Generated reports path 
						<div class="note">
							This is the server path that will be used to store generated reports.
							We suggest that this directory should be outside of the public website directory for security.
						</div>
					</th>
					<td>
						<input type="text" name="<?= $input_prefix ?>[generated_reports_path]" value="<?= $data['generated_reports_path'] ?>"/>
					</td>
				</tr>
				<tr class="odd">
					<th scope="row">Download permissions
						<div class="note">
							Which member-groups are allowed to download reports?
						</div>
					</th>
					<td>
						<?php foreach($member_groups as $count => $member_group) : 
							$member_group_settings = $data["member_groups"][$member_group->group_id];
							$checked = ($member_group_settings['can_download']) ? " checked='checked'" : "";
						?>
							<label class="checkbox" for="mg-<?= $member_group->group_id ?>">
								<input type="hidden" name="<?= "{$input_prefix}[member_groups][{$member_group->group_id}][can_download]" ?>" value="0" />
								<input 
									<?= $checked ?>
									type="checkbox"
									value="1"
									name="<?= "{$input_prefix}[member_groups][{$member_group->group_id}][can_download]" ?>"
									id="mg-<?= $member_group->group_id ?>"
								 />
								<?= $member_group->group_title; ?>
							</label>
						<?php endforeach; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- 
	===============================
	Channel Settings
	===============================
	-->

	<!-- 
	===============================
	Submit Button
	===============================
	-->

	<div class="actions">
		<input type="submit" class="submit" value="<?php print lang('save_extension_settings') ?>" />
	</div>

	<?= form_close(); ?>
</div>
