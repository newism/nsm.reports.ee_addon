<?php
/**
 * Code Igniter View of Delete Config Presets page in Control Panel
 *
 * This file is used when Nsm_reports_mcp::delete_saved() is called and displays the form content and list of
 *   reports chosen in the pending deletion request.
 *
 * @package NsmReports
 * @version 1.0.2
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au> * @copyright Copyright (c) 2007-2011 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html
 */
?>
<p><strong>Are you sure you want to permanently delete these reports?</strong></p>
<ul>
<?php foreach($saved_reports as $preset) : ?>
	<li><?= $preset->title ?><input type="hidden" name="delete[]" value="<?= $preset->id ?>"/></li>
<?php endforeach; ?>
</ul>
<p class="notice">THIS ACTION CAN NOT BE UNDONE</p>
<p><input type="submit" class="submit" value="Delete"/></p>