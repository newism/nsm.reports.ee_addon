<div class="tg">
	<h2>Preview</h2>
	<table class="data row_sortable NSM_Stripeable">
		<thead>
			<tr>
				<th scope="col">ID</th>
				<th scope="col">Title</th>
				<th scope="col">Bedrooms</th>
				<th scope="col">Type</th>
				<th scope="col">No. Images</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($rows as $key => $item) : ?>
			<tr>
				<td><?= $item['ID'] ?></td>
				<th scope="row"><a href="<?= $item['entry_url'] ?>"><?= $item['Property Name']; ?></a></th>
				<td><?= $item['Bedrooms']; ?></td>
				<td><?= $item['Type']; ?></td>
				<td><?= $item['Number of Images']; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>