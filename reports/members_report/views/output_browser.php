<?php
/**
 * Code Igniter View of report preview page in Control Panel
 *
 * This file is used as the default View for a report preview and is used during the Members_report::outputBrowser() method.
 *
 * @package NsmReports
 * @subpackage Members_report
 * @version 1.0.2
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au> 
 * @copyright Copyright (c) 2007-2011 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://expressionengine-addons.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html
 */
?>
<div class="tg">
	<h2>Preview</h2>
	<table class="data col_sortable NSM_Stripeable">
		<thead>
			<tr>
				<?php foreach($columns as $column) : ?>
					<th scope="col"><?= $column ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
		<?php foreach($rows as $row_i => $row) : $col_i = 0; ?>
			<tr>
				<?php foreach($row as $column => $data) : $col_i++; ?>
					<?php if($col_i==0) : ?>
						<th scope="row"><?= $data; ?></th>
					<?php else: ?>
						<td><?= $data; ?></td>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>