<?php

class Microdot_Walker_Nav_Menu extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= '';
    }

    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= '';
    }

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $classes = array();
        if( !empty( $item->classes ) ) {
            $classes = (array) $item->classes;
        }

        $active_class = '';
        if( in_array('current-menu-item', $classes) ) {
            $active_class = ' active';
        } else if( in_array('current-menu-parent', $classes) ) {
            $active_class = ' class="active-parent"';
        } else if( in_array('current-menu-ancestor', $classes) ) {
            $active_class = ' class="active-ancestor"';
        }

        $url = '';
        if( !empty( $item->url ) ) {
            $url = $item->url;
        }
        $icon = get_template_directory_uri();

      //  $output .= '<div'. $active_class . '><a href="' . $url . '">' . $item->title . '</a>';
				$output .= '<a href="' . $url . '" class="btn btn-primary btn2 mx-3'. $active_class . '" role="button" >  ' ;
      //  $output .= '<img class="ico" src="';
      //  $output .=  $icon;
//$output .= '/dist/images/ico_';
      ///  $output .= $item->title;
      //  $output .= '.svg';
      //  $output .= '">';
        $output .=  $item->title;
        $output .= '</a>';

    }

    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= '';
    }
}
