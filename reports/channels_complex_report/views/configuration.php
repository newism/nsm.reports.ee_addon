<?php
/**
 * CONFIGURATION FORM EXAMPLE
 * 
 * This file is used as the Code Igniter View in Channels_complex_report::configHTML()
 * 
 * This form demonstrates adding a new input field to filter channels by channel_id and status.
 *
 * @package NsmReports
 * @subpackage Channels_complex_report
 * @version 1.0.10
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism
 * @license Commercial - please see LICENSE file included with this distribution
 */
?>
<tr>
	<th scope="row">Channel</th>
	<td>
		<select name="report[channel_filter]">
			<option value=""<?= ($config['channel_filter']==''?' selected="selected"':''); ?>>Any</option>
			<?php foreach($channels as $row) : ?>
				<option value="<?=$row['channel_id'] ?>"<?= ($config['channel_filter']==$row['channel_id']?' selected="selected"':''); ?>><?=$row['title'] ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>
<tr>
	<th scope="row">Date</th>
	<td>
		<select name="report[date_filter_mode]">
			<option value=""<?= ($config['date_filter_mode']==''?' selected="selected"':''); ?>>Any</option>
			<option value="after"<?= ($config['date_filter_mode']=='after'?' selected="selected"':''); ?>>After</option>
			<option value="before"<?= ($config['date_filter_mode']=='before'?' selected="selected"':''); ?>>Before</option>
			<option value="equal"<?= ($config['date_filter_mode']=='equal'?' selected="selected"':''); ?>>Is</option>
			<option value="not"<?= ($config['date_filter_mode']=='not'?' selected="selected"':''); ?>>Not</option>
		</select>
		<input type="text" id="report_config_new_review_date" name="report[date_filter]" style="width:33%" value="<?= $config['date_filter']; ?>" />
	</td>
</tr>
<tr>
	<th scope="row">Status</th>
	<td>
		<select name="report[status_filter]">
			<option value=""<?= ($config['status_filter']==''?' selected="selected"':''); ?>>Any</option>
			<?php foreach($status_options as $row) : ?>
				<option value="<?=$row['status'] ?>"<?= ($config['status_filter']==$row['status']?' selected="selected"':''); ?>><?=ucwords($row['status']) ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>
