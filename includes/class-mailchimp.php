<?php
/*
    Plugin Name: Unity3 Mailchimp
    Plugin URI: http://www.unity3software.com/
    Description: Allows customization to a mailchimp template
    Version: 1.0.0
    Author: Richard Blythe
    Author URI: http://unity3software.com/richardblythe
    GitHub Plugin URI: https://github.com/richardblythe/mailchimp
 */

 use \DrewM\MailChimp\MailChimp;
class BF_MailChimp {

    protected $sanitized_id;

    public function __construct( ) {
        // Check function exists.
        // if( !function_exists('acf_add_options_page') )
        //     return;
        $this->sanitized_id = 'bf_mailchimp';

        add_action('init', array($this, 'init'));
        // do acf initialization separately. Todo: might want to disable acf on front end to speed things up
        add_action('acf/init', array($this, 'register_acf'));
    }

    function init() {
        add_action('pre_get_posts', array(&$this, 'blythefamily_blog_rss_query'));
        add_action( 'save_post', array( &$this, 'mailchimp_update_template' ) );
        add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
        
        add_image_size('mailchimp-newsletter', 480, 480, true );
        add_feed('mailchimp-blog', array( &$this, 'blythefamily_blog_rss'));
    }

    function blythefamily_blog_rss(){
        require_once ABSPATH . WPINC . '/feed-rss2.php';
    }
    
    function blythefamily_blog_rss_query($query) {
        if ($query->is_feed && 'blog' == $query->get('feed')) {
            $query->set('category_name', 'news,scriptures,quotes' );
        }
        return $query;
    }

    function mailchimp_update_template( $post_id ) {
    
        // Check if this post has the category: devotions
        $categories = get_field( "{$this->sanitized_id}_post_category", 'options' );
        if ( !isset($_POST['acf']) || !has_category( $categories, $post_id ) )
            return;

        $api_key = get_option( "options_{$this->sanitized_id}_api_key", false );
        $template_id = get_option( "options_{$this->sanitized_id}_template_id", false );
        $html = get_option( "options_{$this->sanitized_id}_template_html", false );
    
        if (!$api_key || !$template_id || !$html ) {
            add_option("{$this->sanitized_id}_error", array(
                'post_id' => null,
                'message' => 'Missing field data. ' . ($api_key ? '' : "api_key: ??"). ($template_id ? '' : "template_id: ??"). ($html ? '' : "html: ??")
            ),false, true);
            return;
        }
            

        //**************************   IMAGES    ***********************************/
        //Get the mailchimp featured images that have been saved this post
        $image_html = '';
        $featured_images_count = get_option( "options_{$this->sanitized_id}_featured_images_count", false );
        global $unity3_mailchimp_featured_images;
        $unity3_mailchimp_featured_images = array();
        
        for ($i = 1; $i <= $featured_images_count; $i++) {
            $group = get_field( "{$this->sanitized_id}_featured_image_group_{$i}", $post_id );
            if ( !empty($group['image']) ) {
                $unity3_mailchimp_featured_images[$group['image']] = $group['link'] ? $group['link'] : '';
            }
        }

        //Check to see if our required images have been set...
        if ($featured_images_count != count($unity3_mailchimp_featured_images)) {
            add_option("{$this->sanitized_id}_error", array(
                    'post_id' => $post_id,
                    'message' => 'Missing Featured Images'
            ),false, true);
            return;
        }

        add_filter( 'attachment_link', array($this, 'attachment_to_gallery_link'), 10, 2 );
        // add_filter( 'unity3/shortcode/gallery/classes', array($this, 'gallery_shortcode_classes'), 10, 2 );

        $image_html = unity3_gallery_shortcode( array(
                'ids' => implode(',', array_keys($unity3_mailchimp_featured_images)),
                'size' => get_option( "options_{$this->sanitized_id}_featured_images_size", 'thumbnail' ),
                'columns' => get_option( "options_{$this->sanitized_id}_featured_images_columns", 3 )
            )
        );

        remove_filter('attachment_link', array($this, 'attachment_to_gallery_link'), 10);
        // remove_filter( 'unity3/shortcode/gallery/classes', array($this, 'gallery_shortcode_classes', 10));


        //******************************   END Images     ***************************************** */
    
        $post = get_post( $post_id );
        $html = str_replace('%%POST_EXCERPT%%', esc_html($post->post_excerpt), $html);
        $html = str_replace('%%FEATURED_IMAGES%%', $image_html, $html);
        $html = str_replace('%%POST_CONTENT%%', get_post_field('post_content', $post_id), $html);

        //push updated template to the mailchimp server...
        require_once( plugin_dir_path( __FILE__ ) . 'vendor/mailchimp-api-master/src/MailChimp.php');
        $MailChimp = new MailChimp($api_key);
        $MailChimp->verify_ssl = true;
        $unity3_mailchimp_save_result = $MailChimp->patch("templates/{$template_id}", [ 'html' => $html ]);
    

        if (!$unity3_mailchimp_save_result) {
            add_option("{$this->sanitized_id}_error", array(
                'post_id' => $post_id,
                'message' => $MailChimp->getLastResponse()
            ), false, true);
        } else {
            global $unity3_mailchimp_updated_post;
            $unity3_mailchimp_updated_post = true;
            delete_option("{$this->sanitized_id}_error");
        }
    }

    // public function gallery_shortcode_classes( $classes, $atts ) {
    //     return $classes . ' unity3-theme two-col-on-xs one-col-on-xxs';
    // }

    // public function attachment_to_gallery_title( $caption, $attachment_id ){
    //     global $unity3_gallery_request_data;
    //     $_post = get_post($unity3_gallery_request_data[$attachment_id]);
    //     return get_the_title($_post);
    // }

    public function attachment_to_gallery_link( $link, $attachment_id ){
        global $unity3_mailchimp_featured_images;
        return $unity3_mailchimp_featured_images[$attachment_id];
    }

    public function admin_notices() {

        global $unity3_mailchimp_updated_post;

        if ( $unity3_mailchimp_updated_post ) : ?>
            <div class="notice notice-success">
                <p><?php esc_html_e('Mailchimp template has been updated to remote servers') ?></p>
            </div>
            <?php
            return;
        endif;

        //****************   Error Check  ***************************************
        //Next, we will check for an unresolved error
        $unity3_mailchimp_error = get_option("{$this->sanitized_id}_error", false);
        if (isset($unity3_mailchimp_error['message'])) {
            $error_message = current_user_can('manage_options') ?
                $unity3_mailchimp_error['message'] : 'Please contact website administrator';

            $link = '';
            if ( isset($unity3_mailchimp_error['post_id']) ) {

                if ( $_post = get_post( $unity3_mailchimp_error['post_id'] ) ) {
                    $link = get_edit_post_link( $_post );
                } else {
                    //settings link...
                    $link = menu_page_url( $this->acf_settings_menu_slug, false);
                }
            }

            $resolve_error_link = empty($link) ? '' : '<a href="' . esc_url( $link ) . '">' . __('Resolve Issue', 'unity3-mailchimp') . '</a>';

            if ( $unity3_mailchimp_error ) : ?>
                <div style="padding: 20px;" class="notice notice-error">
                    <strong>Problem with Mailchimp</strong>
                    <?php esc_html_e($unity3_mailchimp_error['message']); ?>
                    <?php echo '&nbsp' . $resolve_error_link; ?>
                </div>
                <?php
            endif;
        }
    }


    function register_acf() {

        acf_add_local_field_group( array(
            'title' => 'MailChimp Settings',
            'key' => "{$this->sanitized_id}_settings_field",
            'fields' => array(
                array(
                    'label' => 'API Key',
                    'key'  => "{$this->sanitized_id}_api_key",
                    'name' => "{$this->sanitized_id}_api_key",
                    'type' => 'text',
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
                array(
                    'label' => 'Post Category',
                    'key'  => "{$this->sanitized_id}_post_category",
                    'name' => "{$this->sanitized_id}_post_category",
                    'type' => 'taxonomy',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'taxonomy' => 'category',
                    'field_type' => 'select',
                    'allow_null' => 0,
                    'add_term' => 0,
                    'save_terms' => 1,
                    'load_terms' => 0,
                    'return_format' => 'id',
                    'multiple' => 0,
                ),
                array(
                    'label' => 'Featured Images Count',
                    'key'  => "{$this->sanitized_id}_featured_images_count",
                    'name' => "{$this->sanitized_id}_featured_images_count",
                    'type' => 'number',
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
                    'min' => '',
                    'max' => '',
                    'step' => '',
                ), //
                array(
                    'label' => 'Featured Images Size',
                    'key' => "{$this->sanitized_id}_featured_images_size",
                    'name' => "{$this->sanitized_id}_featured_images_size",
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => unity3_get_image_sizes(),
                    'default_value' => array(
                    ),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 1,
                    'ajax' => 0,
                    'return_format' => 'value',
                    'placeholder' => '',
                ),
                array(
                    'label' => 'Template ID',
                    'key'  => "{$this->sanitized_id}_template_id",
                    'name' => "{$this->sanitized_id}_template_id",
                    'type' => 'text',
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
                array(
                    'label' => 'Template Html',
                    'key'  => "{$this->sanitized_id}_template_html",
                    'name' => "{$this->sanitized_id}_template_html",
                    'type' => 'textarea',
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
                    'maxlength' => '',
                    'rows' => '',
                    'new_lines' => '',
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

        //***********************************************************/
        //Now register the post fields
        $category_id = get_option( "options_{$this->sanitized_id}_post_category", false );
        $locations = array();
        $term = get_term($category_id);
        if (!is_wp_error( $term ) ) {
            $locations[] = array(
                array(
                    'param' => 'post_category',
                    'operator' => '==',
                    'value' => 'category:' . $term->slug,
                )
            );
        }

        $image_field_count = get_option( "options_{$this->sanitized_id}_featured_images_count", false );
        $featured_images = array();
        
        for ($i = 1; $i <= $image_field_count; $i++) {
            $featured_images[] = array(
                'label' => "Featured Image {$i}",
                'key'  => "{$this->sanitized_id}_featured_image_group_{$i}",
                'name' => "{$this->sanitized_id}_featured_image_group_{$i}",
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'label' => 'Image',
                        'key' => "{$this->sanitized_id}_featured_image_group_{$i}_image",
                        'name' => "image",
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => '',
                        'preview_size' => 'medium',
                        'library' => '',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                    array(
                        'label' => 'Link To',
                        'key' => "{$this->sanitized_id}_featured_image_group_{$i}_link",
                        'name' => "link",
                        'type' => 'link',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'url',
                    ),
                ),
            );
        }

        acf_add_local_field_group( array(
            'title' => 'Mailchimp',
            'key'   => "{$this->sanitized_id}_featured_images",
            'fields' => $featured_images,
            'location' => $locations,
            'menu_order' => 10,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        )); 

    }

}

new BF_MailChimp();