<?php
/**
 * VIEW IN BROWSER OUTPUT EXAMPLE
 * 
 * This file is used as the Code Igniter View in Channels_complex_report::outputBrowser()
 * 
 * This form demonstrates the use of a custom View to create specialised report outputs for use previewing in the Control Panel.
 * 
 * @package NsmReports
 * @subpackage Channels_complex_report
 * @version 1.0.0
 * @author Leevi Graham <http://leevigraham.com.au>
 * @copyright Copyright (c) 2007-2010 Newism
 * @license Commercial - please see LICENSE file included with this distribution
 */
?>
<div class="tg">
	<h2>Preview</h2>
	<table class="data row_sortable NSM_Stripeable">
		<thead>
			<tr>
				<th scope="col">ID</th>
				<th scope="col">Title</th>
				<th scope="col">Date Created</th>
				<th scope="col">Status</th>
				<th scope="col">Channel</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($rows as $key => $item) : ?>
			<tr>
				<td><?= $item['id'] ?></td>
				<th scope="row"><a href="<?= $item['entry_url'] ?>"><?= $item['name']; ?></a></th>
				<td><?= date('d/m/Y', $item['created_at']); ?></td>
				<td><?= ucwords($item['status']); ?></td>
				<td><?= $item['channel_name']; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>