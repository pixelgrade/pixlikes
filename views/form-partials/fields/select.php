<?php defined('ABSPATH') or die;
	/* @var $field     PixlikesFormField */
	/* @var $form      PixlikesForm  */
	/* @var $default   mixed */
	/* @var $name      string */
	/* @var $idname    string */
	/* @var $label     string */
	/* @var $desc      string */
	/* @var $rendering string  */

	// [!!] the counter field needs to be able to work inside other fields; if
	// the field is in another field it will have a null label

	$selected = $form->autovalue($name, $default);

	$attrs = array
		(
			'name' => $name,
			'id' => $idname,
		);
?>

<select <?php echo $field->htmlattributes($attrs) ?>>
	<?php foreach ($this->getmeta('options', array()) as $key => $label): ?>
		<option <?php if ($key == $selected): ?>selected<?php endif; ?>
				value="<?php echo $key ?>">
			<?php echo $label ?>
		</option>
	<?php endforeach; ?>
</select>
