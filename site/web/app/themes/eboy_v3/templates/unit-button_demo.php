<?php
$value = get_field( "link" );

if( $value ) {

    echo '<a href="'.$value.'" title="Demo" target="_blank" class="btn red btn1">';
    echo '<img class="ico" src=" ' .get_template_directory_uri() .'/dist/images/button_icon_2.svg">';
    echo 'View Demo';
    echo '</a>';
    //echo $value;

} else {

    echo '';

}
?>
