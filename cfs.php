<?php
/*
Plugin Name: Zeemgo Expansion Pack
Plugin URI: http://zeemgo.com/plugins/eprsbiz/

Description: Add features from the RS Biz WordPress theme options page to your WordPress edit pages. To finish configuring the plugin, activate the plugin and then click on 'ZEP Features' in the left sidebar menu' or click on the 'Configure' link under the plugin name. Features you can now change -- with the free version of this plugin -- per individual page:

RS Biz Top Bar Options: Top Bar Text, Phone Number, and Text Before Phone Number on Top Bar features.

RS Biz Video Options: YouTube Video Code, Headline Below Video, and Embed Video/Image Code features.

RS Biz Footer Options: Footer Headline feature.

In the paid version (http://zeemgo.com/plugins/eprsbiz/) you will get the features in the free version AND the two following features below:

RS Biz Background Options: Color Scheme, Page Background Color, and Image Behind Video features.

RS Biz Bullet List Below Video Options: List Item #1-10, and List Item #1-10, Optional Description features.

Version: 1.0
Author: JBV-USA / Zeemgo
Author URI: http://zeemgo.com/
License: GPL2
*/

class Custom_Field_Suite
{
    public $version = '0.1';


    /**
     * Constructor
     * @since 1.0.0
     */
    function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }


    /**
     * Fire up CFS
     * @since 1.0.0
     */
    function init() {
        $this->dir = dirname( __FILE__ );
        $this->url = plugins_url( 'zep-for-rsbiz' );

        // i18n
        $this->load_textdomain();

        add_action( 'admin_head',               array( $this, 'admin_head' ) );
        add_action( 'admin_footer',             array( $this, 'admin_footer' ) );
        add_action( 'admin_menu',               array( $this, 'admin_menu' ) );
        add_action( 'save_post',                array( $this, 'save_post' ) );
        add_action( 'delete_post',              array( $this, 'delete_post' ) );
        add_action( 'add_meta_boxes',           array( $this, 'add_meta_boxes' ) );
        add_action( 'wp_ajax_cfs_ajax_handler', array( $this, 'ajax_handler' ) );
	    add_filter( 'admin_body_class',         array( $this, 'add_body_class' ) );

        // Force the $cfs variable
        if ( !is_admin() ) {
            add_action( 'parse_query', array( $this, 'parse_query' ) );
        }

        foreach ( array( 'api', 'upgrade', 'field', 'field_group', 'session', 'form', 'third_party' ) as $f ) {
            include( $this->dir . "/includes/$f.php" );
        }

        $upgrade = new cfs_upgrade( $this->version );

        // load classes
        $this->api = new cfs_api($this);
        $this->form = new cfs_form($this);
        $this->field_group = new cfs_field_group($this);
        $this->third_party = new cfs_third_party($this);
        $this->fields = $this->get_field_types();

        register_post_type( 'cfs', array(
            'public'            => false,
            'show_ui'           => true,
            'show_in_menu'      => false,
            'capability_type'   => 'page',
            'hierarchical'      => false,
            'supports'          => array( 'title' ),
            'query_var'         => false,
            'labels'            => array(
                'name'                  => __( 'Features', 'cfs' ),
                'singular_name'         => __( 'Features', 'cfs' ),
                'add_new'               => __( 'Add New', 'cfs' ),
                'add_new_item'          => __( 'Add New Features', 'cfs' ),
                'edit_item'             => __( 'Edit Features', 'cfs' ),
                'new_item'              => __( 'New Features', 'cfs' ),
                'view_item'             => __( 'View Features', 'cfs' ),
                'search_items'          => __( 'Search Features', 'cfs' ),
                'not_found'             => __( 'No Features Found', 'cfs' ),
                'not_found_in_trash'    => __( 'No Features Found in Trash', 'cfs' ),
            ),
        ));

        // customize the table header
        add_filter( 'manage_cfs_posts_columns', array( $this, 'cfs_columns' ) );
        add_action( 'manage_cfs_posts_custom_column', array( $this, 'cfs_column_content' ), 10, 2 );

        do_action( 'cfs_init' );
    }


    function load_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'cfs' );
        $mofile = WP_LANG_DIR . '/zep-for-rsbiz/cfs-' . $locale . '.mo';

        if ( file_exists( $mofile ) ) {
            load_textdomain( 'cfs', $mofile );
        }
        else {
            load_plugin_textdomain( 'cfs', false, 'zep-for-rsbiz/languages' );
        }
    }


    /**
     * Customize table columns on the Features listing page
     * @since 1.0.0
     */
    function cfs_columns() {
        return array(
            'cb'            => '<input type="checkbox" />',
            'title'         => __( 'Title', 'cfs' ),
            'placement'     => __( 'Placement', 'cfs' ),
        );
    }


    /**
     * Populate the "Placement" column on the Features listing page
     * @param string $column_name 
     * @param int $post_id 
     * @since 1.9.5
     */
    function cfs_column_content( $column_name, $post_id ) {
        if ( 'placement' == $column_name ) {
            global $wpdb;

            $labels = array(
                'post_types'        => __( 'Post Types', 'cfs' ),
                'user_roles'        => __( 'User Roles', 'cfs' ),
                'post_ids'          => __( 'Post IDs', 'cfs' ),
                'term_ids'          => __( 'Term IDs', 'cfs' ),
                'page_templates'    => __( 'Page Templates', 'cfs' )
            );

            $results = $wpdb->get_var( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '$post_id' AND meta_key = 'cfs_rules' LIMIT 1" );
            $results = unserialize( $results );

            foreach ( $results as $criteria => $values ) {
                $label = $labels[$criteria];
                $operator = ( '==' == $values['operator'] ) ? '=' : '!=';
                echo "<div>$label " . $operator . ' [' . implode(' or ', $values['values']) . ']</div>';
            }
        }
    }


    /**
     * Register field types
     * @since 1.0.0
     */
    function get_field_types() {

        $field_types = array(
            'text'              => $this->dir . '/includes/fields/text.php',
            'textarea'          => $this->dir . '/includes/fields/textarea.php',
            'wysiwyg'           => $this->dir . '/includes/fields/wysiwyg.php',
            'date'              => $this->dir . '/includes/fields/date/date.php',
            'color'             => $this->dir . '/includes/fields/color/color.php',
            'true_false'        => $this->dir . '/includes/fields/true_false.php',
            'select'            => $this->dir . '/includes/fields/select.php',
            'relationship'      => $this->dir . '/includes/fields/relationship.php',
            'user'              => $this->dir . '/includes/fields/user.php',
            'file'              => $this->dir . '/includes/fields/file.php',
            'loop'              => $this->dir . '/includes/fields/loop.php',
            'tab'               => $this->dir . '/includes/fields/tab.php',
        );

        // support custom field types
        $field_types = apply_filters( 'cfs_field_types', $field_types );

        foreach ( $field_types as $type => $path ) {
            $class_name = 'cfs_' . $type;

            // allow for multiple classes per file
            if ( !class_exists( $class_name ) ) {
                include_once( $path );
            }

            $field_types[$type] = new $class_name( $this );
        }

        return $field_types;
    }


    /**
     * Generate input field HTML
     * @param object $field 
     * @since 1.0.0
     */
    function create_field( $field ) {
        $defaults = array(
            'type' => 'text',
            'input_name' => '',
            'input_class' => '',
            'options' => array(),
            'value' => '',
        );

        $field = (object) array_merge( $defaults, (array) $field );
        $this->fields[$field->type]->html( $field );
    }


    /**
     * Retrieve custom field values
     * @param mixed $field_name 
     * @param mixed $post_id 
     * @param array $options 
     * @return mixed
     * @since 1.0.0
     */
    function get( $field_name = false, $post_id = false, $options = array() ) {
        if ( false !== $field_name ) {
            return $this->api->get_field( $field_name, $post_id, $options );
        }

        return $this->api->get_fields( $post_id, $options );
    }


    /**
     * Get custom field properties (label, name, settings, etc.)
     * @param mixed $field_name 
     * @param mixed $post_id 
     * @return array
     * @since 1.8.3
     */
    function get_field_info( $field_name = false, $post_id = false ) {
        return $this->api->get_field_info( $field_name, $post_id );
    }


    /**
     * Get custom field labels
     * @param mixed $field_name 
     * @param mixed $post_id 
     * @return mixed
     * @since 1.3.3
     * @deprecated 1.8.0
     */
    function get_labels( $field_name = false, $post_id = false ) {
        $field_info = $this->api->get_field_info( $field_name, $post_id );

        if ( !empty( $field_name ) ) {
            return $field_info['label'];
        }
        else {
            $output = array();

            foreach ( $field_info as $name => $field_data ) {
                $output[$name] = $field_data['label'];
            }

            return $output;
        }
    }


    /**
     * Retrieve reverse-related values (using the relationship field type)
     * @param int $post_id 
     * @param array $options 
     * @return array
     * @since 1.4.4
     */
    function get_reverse_related( $post_id, $options = array() ) {
        return $this->api->get_reverse_related( $post_id, $options );
    }


    /**
     * Save field values (and post data)
     * @param array $field_data 
     * @param array $post_data 
     * @param array $options 
     * @return int The post ID
     * @since 1.1.4
     */
    function save( $field_data = array(), $post_data = array(), $options = array() ) {
        return $this->api->save_fields( $field_data, $post_data, $options );
    }


    /**
     * Display a front-end form
     * @param array $params 
     * @return string The form HTML
     * @since 1.8.5
     */
    function form( $params = array() ) {
        ob_start();

        $this->form->render( $params );

        return ob_get_clean();
    }


    /**
     * admin_head
     * @since 1.0.0
     */
    function admin_head() {
        $screen = get_current_screen();

        if ( 'post' == $screen->base ) {
            include( $this->dir . '/templates/admin_head.php' );
        }
    }


    /**
     * admin_footer
     * @since 1.0.0
     */
    function admin_footer() {
        $screen = get_current_screen();

        if ( 'edit' == $screen->base && 'cfs' == $screen->post_type ) {
            include($this->dir . '/templates/admin_footer.php');
        }
    }


    /**
     * add_meta_boxes
     * @since 1.0.0
     */
    function add_meta_boxes() {
        add_meta_box( 'cfs_fields', __('Fields', 'cfs'), array( $this, 'meta_box' ), 'cfs', 'normal', 'high', array( 'box' => 'fields' ) );
        add_meta_box( 'cfs_rules', __('Placement Rules', 'cfs'), array( $this, 'meta_box' ), 'cfs', 'normal', 'high', array( 'box' => 'rules' ) );
        add_meta_box( 'cfs_extras', __('Extras', 'cfs'), array( $this, 'meta_box' ), 'cfs', 'normal', 'high', array( 'box' => 'extras' ) );
    }


    /**
     * admin_menu
     * @since 1.0.0
     */
    function admin_menu() {
	add_object_page( __( 'ZEP Features', 'cfs' ), __( 'ZEP Features', 'cfs' ), 'manage_options', 'cfs-tools', array( $this, 'page_tools' ) );
    }


    /**
     * save_post
     * @param int $post_id 
     * @since 1.0.0
     */
    function save_post( $post_id )
    {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        if ( !isset( $_POST['cfs']['save'] ) ) {
            return;
        }

        if ( false !== wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( wp_verify_nonce( $_POST['cfs']['save'], 'cfs_save_fields' ) ) {
            $fields = isset( $_POST['cfs']['fields'] ) ? $_POST['cfs']['fields'] : array();
            $rules = isset( $_POST['cfs']['rules'] ) ? $_POST['cfs']['rules'] : array();
            $extras = isset( $_POST['cfs']['extras'] ) ? $_POST['cfs']['extras'] : array();

            $this->field_group->save( array(
                'post_id'   => $post_id,
                'fields'    => $fields,
                'rules'     => $rules,
                'extras'    => $extras,
            ) );
        }
    }


    /**
     * delete_post
     * @param int $post_id 
     * @return boolean
     * @since 1.0.0
     */
    function delete_post( $post_id ) {
        global $wpdb;

        if ( 'cfs' != get_post_type( $post_id ) ) {
            $post_id = (int) $post_id;
            $wpdb->query( "DELETE FROM {$wpdb->prefix}cfs_values WHERE post_id = $post_id" );
        }

        return true;
    }


    /**
     * meta_box
     * @param object $post 
     * @param array $metabox 
     * @since 1.0.0
     */
    function meta_box( $post, $metabox ) {
        $box = $metabox['args']['box'];
        include( $this->dir . "/templates/meta_box_$box.php" );
    }


    /**
     * field_html
     * @param object $field 
     * @since 1.0.3
     */
    function field_html( $field ) {
        include( $this->dir . '/templates/field_html.php' );
    }


    /**
     * page_tools
     * @since 1.6.3
     */
    function page_tools() {
        include( $this->dir . '/templates/page_tools.php' );
    }


    /**
     * page_addons
     * @since 1.8.0
     */
    function page_addons() {
        include( $this->dir . '/templates/page_addons.php' );
    }


    /**
     * ajax_handler
     * @since 1.7.5
     */
    function ajax_handler() {
        global $wpdb;

        $ajax_method = isset( $_POST['action_type'] ) ? $_POST['action_type'] : false;

        if ( $ajax_method && is_admin() ) {
            include( $this->dir . '/includes/ajax.php' );
            $ajax = new cfs_ajax();

            if ( 'import' == $ajax_method ) {
                $options = array(
                    'import_code' => json_decode( stripslashes( $_POST['import_code'] ), true ),
                );
                echo $this->field_group->import( $options );
            }
            elseif ('export' == $ajax_method) {
                echo json_encode( $this->field_group->export( $_POST ) );
            }
            elseif ('reset' == $ajax_method) {
                if ( current_user_can( 'manage_options' ) ) {
                    $ajax->reset();
                    deactivate_plugins( plugin_basename( __FILE__ ) );
                    echo admin_url( 'plugins.php' );
                }
            }
            elseif ( method_exists( $ajax, $ajax_method ) ) {
                echo $ajax->$ajax_method( $_POST );
            }
            exit;
        }
    }


    /**
     * Make sure that $cfs exists for template parts
     * @since 1.8.8
     */
    function parse_query( $wp_query ) {
        $wp_query->query_vars['cfs'] = $this;
    }


	/**
	 * Add a class of 'mp6' if WordPress 3.8-alpha or higher, allowing us to help the UI better match the WordPress admin
	 * Reference: http://make.wordpress.org/ui/2013/11/19/targeting-the-new-dashboard-design-in-a-post-mp6-world/
	 *
	 * @param $classes
	 *
	 * @return array|string
	 */
	function add_body_class( $classes ) {
		if ( version_compare( $GLOBALS['wp_version'], '3.8-alpha', '>' ) ) {
			$classes = explode( " ", $classes );
			if ( ! in_array( 'mp6', $classes ) ) {
				$classes[] = 'mp6';
			}
			$classes = implode( " ", $classes );
		}
		return $classes;
	}
}


$cfs = new Custom_Field_Suite();

function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=cfs-tools">Configure</a>';
  	array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );