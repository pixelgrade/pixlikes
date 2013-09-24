<?php defined('ABSPATH') or die;
	/* @var PixtypesFormField $field */
	/* @var PixtypesForm $form */
	/* @var mixed $default */
	/* @var string $name */
	/* @var string $idname */
	/* @var string $label */
	/* @var string $desc */
	/* @var string $rendering */
	/* @var string $show_on */

$show_on = $field->getmeta('show_on'); ?>
<div class="group" <?php if ( !empty($show_on) ) echo 'show_on="'. $show_on .'"'; ?>>

	<h4><?php echo $label ?></h4>
	<table class="form-table">
		<?php foreach ($field->getmeta('options', array()) as $fieldname => $fieldconfig): ?>

			<?php
				$field = $form->field($fieldname, $fieldconfig);
				// we set the fields to default to inline
				$field->ensuremeta('rendering', 'inline');
				// export field meta for processing
				$fielddesc = $field->getmeta('desc', null);
				$fieldexample = $field->getmeta('pixtype-group-example', null);
				$fieldnote = $field->getmeta('pixtype-group-note', null);
			?>

			<tbody>

				<tr valign="top">
					<th scope="row">
						<strong><?php echo $field->getmeta('label', '') ?></strong>

						<?php if ( ! empty($fielddesc)): ?>
							<p><?php echo $fielddesc ?></p>
						<?php endif; ?>
					</th>
					<td>
						<p><?php echo $field->render() ?></p>

						<?php if ( ! empty($fieldnote)): ?>
							<p><?php echo $fieldnote ?></p>
						<?php endif; ?>
					</td>
				</tr>

				<?php if ( ! empty($fieldexample)): ?>
					<tr>
						<td colspan="2">
							<p>Example: <?php echo $fieldexample ?></p>
						</td>
					</tr>
				<?php endif; ?>

			</tbody>

		<?php endforeach; ?>
	</table>
</div>