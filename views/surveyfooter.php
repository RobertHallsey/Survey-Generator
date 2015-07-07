<?php

/**
 * This file is a view file from the Survey Generator system.
 *
 * @author Robert Hallsey <rhallsey@yahoo.com>
 * @copyright Robert Hallsey, 2015
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */
?>
<?php if (!$disabled): ?>
<p><input type="reset" value="<?php echo __($reset_button) ?>"><input type="submit" name="submit" value="<?php echo __($submit_button) ?>"></p>

</form>
<?php else: ?>

</form>

<p><?php echo __('Timestamp: ') . date('M. d, Y h:i:s A', $timestamp) ?></p>
<?php endif; ?>
<?php if ($js_code): ?>

<script type="text/javascript">
	function formDisable() {
		var form = document.getElementById("form");
		var elements = form.elements;
		for (var i = 0, len = elements.length; i < len; i++) {
			elements[i].disabled = true;
		}
	}
	function formReset() {
		this.form.reset()
	}
	<?php echo $js_code ?>

</script>

<?php endif; ?>

</div><!-- sf survey form -->
