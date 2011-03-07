<tr>
	<th scope="row">Minumum bedrooms</th>
	<td>
		<select name="report[bedrooms]">
			<option value="0"<?= ($config['bedrooms']==0?' selected="selected"':''); ?>>1</option>
			<option value="1"<?= ($config['bedrooms']==1?' selected="selected"':''); ?>>2</option>
			<option value="2"<?= ($config['bedrooms']==2?' selected="selected"':''); ?>>3</option>
			<option value="3"<?= ($config['bedrooms']==3?' selected="selected"':''); ?>>4 or more</option>
		</select>
	</td>
</tr>
<tr>
	<th scope="row">Type</th>
	<td>
		<select name="report[type]">
			<option value=""<?= ($config['type']==''?' selected="selected"':''); ?>>Any</option>
			<?php foreach($property_types as $row) : ?>
				<option value="<?=$row['type'] ?>"<?= ($config['type']==$row['type']?' selected="selected"':''); ?>><?=$row['type'] ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>