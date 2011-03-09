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
 * @version 1.0.1
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism
 * @license Commercial - please see LICENSE file included with this distribution
 */
?>
<tr>
	<th scope="row">Additional member data</th>
	<td>
		<ul>
		<?php foreach($additional_fields as $field) : ?>
			<li>
				<label>
					<input
						type="checkbox" 
						name="report[additional_fields][]"
						value="<?=$field['m_field_id'] ?>"
						<?= (in_array($field['m_field_id'], $config['additional_fields']) ?' checked="checked"':''); ?>
					/>
					<?=$field['m_field_label'] ?>
				</label>
			</li>
		<?php endforeach; ?>
		</ul>
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