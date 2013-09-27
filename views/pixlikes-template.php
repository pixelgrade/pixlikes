<?php
/**
 * This is the default view for the like box
 * It should be overridden by the theme but let's create an example
 */

/*
 * There should be an element with the class="pixlikes-box"
 * Also $data_id variable contains the post id required by the js file
 */

//echo '<a class="pixlikes-box '.$display_only.'" href="#" '.$data_id.' title="'. $title .'" style="background-color: #345; display:block; width:30px; height:30px">'. $likes_number .'</a>';?>
<div class="pixlikes-box <?php echo $display_only . ' ' . $class ?> likes-box" <?php echo $data_id ?>>
	<span class="like-link"><i class="icon-e-heart"></i></span>
	<div class="likes-text">
		<span class="likes-count"><?php echo $likes_number ?></span> likes
	</div>
</div>
