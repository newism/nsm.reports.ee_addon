<?php
/**
 * Code Igniter View of Config Presets List page in Control Panel
 *
 * This file is used when Nsm_reports_mcp::saved_reports() is called and returns a table of all 
 *   saved report configurations to the user with a form to choose presets to delete.
 *
 * @package NsmReports
 * @version 1.0.1
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au> 
 * @copyright Copyright (c) 2007-2011 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://expressionengine-addons.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html
 */
?>
<div class="tg">
	<table class="data">
		<thead>
			<tr>
				<th scope="col">Preset Name</th>
				<th scope="col">Preset Description</th>
				<th scope="col">Report</th>
				<th scope="col">Email Address</th>
				<th scope="col">Format</th>
				<th scope="col">Date Created</th>
				<th scope="col">Last Run</th>
				<th scope="col">Run Count</th>
				<th scope="col">External URL</th>
				<th scope="col">Delete</th>
			</tr>
		</thead>
		<tbody>
		<?php if(count($saved_reports) == 0): ?>
			<tr>
				<td class="error" colspan="8">No entries found</td>
			</tr>
		<?php else: ?>
			<?php foreach($saved_reports as $saved_report): ?>
			<tr>
				<th scope="row">
					<a title="Go to report using config ID <?= $saved_report['id'] ?>" href="<?= $details_url.$saved_report['report_class'].AMP.'save_id='.$saved_report['id'] ?>">
						<?= $saved_report['title'] ?>
					</a>
				</th>
				<td><?= $saved_report['description'] ?></td>
				<td><?= $saved_report['report'] ?></td>
				<td><?= $saved_report['email_address'] ?></td>
				<td><?= $saved_report['output'] ?></td>
				<td><?= $saved_report['created_at'] ?></td>
				<td><?= $saved_report['lastrun_at'] ?></td>
				<td><?= $saved_report['run_count'] ?></td>
				<td>
					<a href="<?= $process_url.$saved_report['id'].AMP.'key='.$saved_report['access_key'] ?>">
						URL
					</a>
				</td>
				<td><input type="checkbox" name="delete[]" value="<?= $saved_report['id'] ?>" />
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
	
	<div class="actions">
		<input type="submit" class="submit" value="Delete"/>
	</div>
	
</div>

