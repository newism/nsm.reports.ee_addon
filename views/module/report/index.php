<?php
/**
 * Code Igniter View of dashboard page in Control Panel
 *
 * This file is used when Nsm_reports_mcp::index() is called and returns a table of all reports found in reports directory to the user.
 *
 * @package NsmReports
 * @version 1.0.5
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au> * @copyright Copyright (c) 2007-2011 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html
 */
?>
<div class="tg">
	<table class="data col-sortable NSM_Stripeable">
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
			<?php if(!$reports) : ?>
			<tr>
				<td class="alert error" colspan="5">No reports found in directory</td>
			</tr>
			<?php else : ?>
			<?php 
				$i = 0;
				foreach ($reports as $report_class => $report) :
				$i += 1;
				$class = ($i%2) ? "even" : "odd";
			?>
			<tr class="<?=$class;?>">
				<th scope="row"><a href='<?= $report['config_url']; ?>'><?= $report['title']; ?></a></th>
				<td><?= $report['notes']; ?></td>
				<td><?= $report['author']; ?></td>
				<td><?= $report['version']; ?></td>
				<td><?php if($report['docs_url']) : ?><a href='<?= $report['docs_url'] ?>'>Documentation</a><?php endif; ?>&nbsp;</td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>