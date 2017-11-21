<?php
if ( is_front_page()) {
// Default homepage

get_template_part('templates/unit-head_list');

} elseif ( !is_front_page()){
get_template_part('templates/unit-head_list');
}
