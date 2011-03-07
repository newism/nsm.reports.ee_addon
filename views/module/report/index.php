<div class="tg">
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
			<?php if(!$reports) : ?>
			<tr>
				<td class="alert error" colspan="5">No reports found in directory</td>
			</tr>
			<?php else : ?>
			<?php 
				$i = 0;
				foreach ($reports as $report_class => $report) : 
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