<?php
if ( is_page_template('template-front.php')) {
// Default homepage

get_template_part('templates/unit-head_front');

} elseif ( !is_front_page()){
get_template_part('templates/unit-head_in');
}
