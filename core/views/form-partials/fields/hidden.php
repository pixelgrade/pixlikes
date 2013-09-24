<?php defined('ABSPATH') or die;
	/* @var $field     PixlikesFormField */
	/* @var $form      PixlikesForm  */
	/* @var $default   mixed */
	/* @var $name      string */
	/* @var $idname    string */
	/* @var $label     string */
	/* @var $desc      string */
	/* @var $rendering string  */

	isset($type) or $type = 'text';

	$attrs = array
		(
			'name' => $name,
			'id' => $idname,
			'type' => 'text',
			'value' => $form->autovalue($name)
		);
?>

<input <?php echo $field->htmlattributes($attrs) ?>/>
