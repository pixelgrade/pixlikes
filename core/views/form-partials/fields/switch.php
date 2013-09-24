<?php defined('ABSPATH') or die;
	/* @var $field     PixlikesFormField */
	/* @var $form      PixlikesForm  */
	/* @var $default   mixed */
	/* @var $name      string */
	/* @var $idname    string */
	/* @var $label     string */
	/* @var $desc      string */
	/* @var $rendering string  */

	// [!!] a switch is a checkbox that is only ever either on or off; not to
	// be confused with a fully functional checkbox which may be many values

	$checked = $form->autovalue($name, $default);

	$attrs = array
		(
			'name' => $name,
			'type' => 'checkbox',
			'id' => $idname,
			'value' => 1,
		);

	// is the checkbox checked?
	if ($checked) {
		$attrs['checked'] = 'checked';
	}

	// Label Fillins
	// -------------

	if ($field->hasmeta('label-fillins')) {
		$fillers = array();
		foreach ($field->getmeta('label-fillins', array()) as $fieldname => $conf) {
			$fillers[":$fieldname"] = $form->field($fieldname, $conf)->render();
		}

		$processed_label = strtr($label, $fillers);
	}
	else { // no fillins available
		$processed_label = $label;
	}

?>

<?php if ($rendering == 'inline'): ?>
	<input <?php echo $field->htmlattributes($attrs) ?> />
<?php else: # rendering != 'inline' ?>
	<label for="<?php echo $idname ?>">
		<input <?php echo $field->htmlattributes($attrs) ?> />
		<?php echo $processed_label ?>
	</label>
<?php endif; ?>
