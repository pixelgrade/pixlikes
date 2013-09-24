<?php defined('ABSPATH') or die;
	/* @var $field     PixlikesFormField */
	/* @var $form      PixlikesForm  */
	/* @var $default   mixed */
	/* @var $name      string */
	/* @var $idname    string */
	/* @var $label     string */
	/* @var $desc      string */
	/* @var $rendering string  */
?>

<tr valign="top">
	<th scope="row">
		<?php echo $label ?>
	</th>
	<td>
		<fieldset>

			<legend class="screen-reader-text">
				<span><?php echo $label ?></span>
			</legend>

			<?php foreach ($field->getmeta('options', array()) as $fieldname => $conf): ?>
				<?php echo $form->field($fieldname, $conf)->render() ?>
				<br/>
			<?php endforeach; ?>

			<?php if ($field->hasmeta('note')): ?>
				<small>
					<em>(<?php echo $field->getmeta('note') ?>)</em>
				</small>
			<?php endif; ?>

		</fieldset>
	</td>
</tr>
