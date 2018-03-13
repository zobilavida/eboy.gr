<?php
namespace WilokeListGoFunctionality\Register;

class RegisterNavMenu implements RegisterInterface{
    public $location = 'wiloke_list_go_menu';

    public function __construct()
    {
        add_action('init', array($this, 'register'));
    }

    public function register()
    {
        register_nav_menu(
            $this->location,
            esc_html__('ListGo Menu', 'wiloke')
        );
    }
}