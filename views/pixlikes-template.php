<?php
/**
 * This is the default view for the like box
 * It should be overridden by the theme but let's create an example
 */

/*
 * There should be an element with the class="pixlikes-box"
 * Also $data_id variable contains the post id required by the js file
 */

echo '<a class="pixlikes-box" href="#" '.$data_id.' style="background-color: #345; display:block; width:30px; height:30px">'. $likes_number .'</a>';
