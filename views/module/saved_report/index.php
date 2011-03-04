
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
					<a title="Go to report using config ID <?= $saved_report->id ?>" href="<?= $details_url.$saved_report->report.AMP.'save_id='.$saved_report->id ?>">
						<?= $saved_report->title ?>
					</a>
				</th>
				<td><?= $saved_report->description ?></td>
				<td><?= $reports[$saved_report->report]['title'] ?></td>
				<td><?= $saved_report->email_address ?></td>
				<td><?= $saved_report->output ?></td>
				<td><?= date('d/m/Y H:i', $saved_report->created_at) ?></td>
				<td><?= ( $saved_report->lastrun_at > 0 ? date('d/m/Y H:i', $saved_report->lastrun_at) : 'Never' ) ?></td>
				<td><?= $saved_report->run_count ?></td>
				<td>
					<a href="<?= $process_url.$saved_report->id.AMP.'key='.$saved_report->access_key ?>">
						URL
					</a>
				</td>
				<td><input type="checkbox" name="delete[]" value="<?= $saved_report->id ?>" />
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
	
	<div class="actions">
		<input type="submit" class="submit" value="Delete"/>
	</div>
	
</div>

