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