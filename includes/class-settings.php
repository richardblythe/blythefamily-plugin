<?php

class BF_Settings {

    public const MENU_SLUG = 'blythe-family-settings';
    protected $acf_key = 'bf_settings';

    function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('acf/init', array($this, 'register_acf'));
        add_filter( 'upload_mimes', array($this, 'upload_mimes'), 1000, 1 );
    }

    function upload_mimes( $mime_types ) {
        $mime_types['json'] = 'application/json'; // Adding .json extension
        return $mime_types;
    }

    function init() {
        if( function_exists('acf_add_options_page') ) {           
           // add sub page
           acf_add_options_sub_page(array(
               'page_title' 	=> 'Blythe Family Settings',
               'menu_title' 	=> 'Blythe Family',
               'menu_slug'      => BF_Settings::MENU_SLUG,
               'parent_slug' 	=> 'options-general.php',
           ));
           
       }
    }

    function register_acf() {

        acf_add_local_field_group( array(
            'title' => 'Google API',
            'key' => "{$this->acf_key}_google_api",
            'fields' => array(
                array(
                    'label' => 'Google API Key File',
                    'key'  => "{$this->acf_key}_google_api_key_file",
                    'name' => "{$this->acf_key}_google_api_key_file",
                    'type' => 'file',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => BF_Settings::MENU_SLUG
                    ),
                ),
            ),
        ));
    }
}

new BF_Settings();