<?php
/**
 * CONFIGURATION FORM EXAMPLE
 * 
 * This file is used as the Code Igniter View in Members_report::configHTML()
 * 
 * This form demonstrates adding a new input field to filter channels by channel_id and status.
 *
 * @package NsmReports
 * @subpackage Members_report
 * @version 1.0.3
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism
 * @license Commercial - please see LICENSE file included with this distribution
 */
?>
<tr>
	<th scope="row">Member data</th>
	<td>
		<?php foreach($member_fields as $field_name => $field_label) : ?>
				<label>
					<input
						type="checkbox" 
						name="report[member_fields][]"
						value="<?=$field_name ?>"
						<?= (in_array($field_name, $config['member_fields']) ?' checked="checked"':''); ?>
					/>
					<?=$field_label ?>
				</label>
		<?php endforeach; ?>
	</td>
</tr>
<tr>
	<th scope="row">Custom member data</th>
	<td>
		<?php foreach($additional_fields as $field) : ?>
				<label>
					<input
						type="checkbox" 
						name="report[additional_fields][]"
						value="<?=$field['m_field_id'] ?>"
						<?= (in_array($field['m_field_id'], $config['additional_fields']) ?' checked="checked"':''); ?>
					/>
					<?=$field['m_field_label'] ?>
				</label>
		<?php endforeach; ?>
	</td>
</tr>
<tr>
	<th scope="row">Limit</th>
	<td>
		<select name="report[row_limit]">
			<option value="25"<?= ($config['row_limit'] == 25 ? ' selected="selected"' : ''); ?>>25</option>
			<option value="50"<?= ($config['row_limit'] == 50 ? ' selected="selected"' : ''); ?>>50</option>
			<option value="100"<?= ($config['row_limit'] == 100 ? ' selected="selected"' : ''); ?>>100</option>
			<option value="200"<?= ($config['row_limit'] == 200 ? ' selected="selected"' : ''); ?>>200</option>
			<option value="400"<?= ($config['row_limit'] == 400 ? ' selected="selected"' : ''); ?>>400</option>
			<option value=""<?= ($config['row_limit'] == '' ? ' selected="selected"' : ''); ?>>None</option>
		</select>
	</td>
</tr>