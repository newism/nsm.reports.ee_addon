<p><strong>Are you sure you want to permanently delete these reports?</strong></p>
<ul>
<?php foreach($saved_reports as $preset) : ?>
	<li><?= $preset->title ?><input type="hidden" name="delete[]" value="<?= $preset->id ?>"/></li>
<?php endforeach; ?>
</ul>
<p class="notice">THIS ACTION CAN NOT BE UNDONE</p>
<p><input type="submit" class="submit" value="Delete"/></p>