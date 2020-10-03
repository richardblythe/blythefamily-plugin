<?php

class BF_Episode extends Unity3_Post_Group {

	const POST_TYPE     = 'blythe_episode';
	private $header_img;

	function __construct() {

        parent::__construct( self::POST_TYPE, __('Episode'), __('Episodes'), 'Adds episodes from the Blythe Family YouTube channel' );

        $this->mergeSettings( array(
            'drag_sort_posts' => false,
            'group_rewrite' => array( 'base' => 'episodes' )
        ));

        add_action('acf/save_post', array( $this, 'acf_save_post'), 20 );
	}

	function Init()
    {
        parent::Init();
        add_post_type_support( self::POST_TYPE, 'custom-fields' );
        add_post_type_support( self::POST_TYPE, 'genesis-singular-images' );
        add_post_type_support( self::POST_TYPE, 'genesis-entry-meta-before-content' );
        //get_post_type(), 'genesis-entry-meta-before-content'
    }

    function acf_save_post( $post_id ) {

	    if ( self::POST_TYPE ==  get_post_type( $post_id ) ) {
            $acf_date = get_field( 'publish_date', $post_id);
            $my_post = array();
            $my_post['ID'] = $post_id;
            $my_post['post_date'] = $acf_date;
            $post = wp_update_post( $my_post );
        }

    }

}

////*************************
////       Register
////*************************
unity3_modules()->Register(new BF_Episode());
