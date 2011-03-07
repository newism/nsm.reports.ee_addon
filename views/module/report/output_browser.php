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