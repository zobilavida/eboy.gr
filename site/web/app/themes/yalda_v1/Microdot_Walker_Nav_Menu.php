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

      //  $output .= '<div'. $active_class . '><a href="' . $url . '">' . $item->title . '</a>';
      $output .= '<span class="col '. $active_class . '"><i class="icon ion-' . $item->title . '"></i>' . $item->title . '</span>';
      //  $output .= '<a href="' . $url . '" class="col '. $active_class . '"  >' . $item->title . '</a>';

    }

    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= '';
    }
}
