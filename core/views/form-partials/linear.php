<?php defined('ABSPATH') or die;
	/* @var $form PixlikesForm */
	/* @var $conf PixlikesMeta */

	/* @var $f PixlikesForm */
	$f = &$form;
?>

<?php foreach ($conf->get('fields', array()) as $fieldname): ?>

	<?php echo $f->field($fieldname)
		->addmeta('special_sekrit_property', '!!')
		->render() ?>

<?php endforeach; ?>
