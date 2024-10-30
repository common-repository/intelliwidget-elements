<?php
if ( !defined('ABSPATH')) exit;

if (!class_exists('IntelliWidgetElements')):

class IntelliWidgetElements {

    function __construct() {

        register_activation_hook( __FILE__, array( $this, '_activate' ) );
        load_plugin_textdomain( 'iwelements', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
        add_action( 'init', array( $this, '_init' ) );
        add_theme_support( 'post-thumbnails', array( 'iwelements' ) );
        add_action( 'admin_menu', array( $this, 'iwel_add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'iwel_settings_init' ) );
        add_action( 'update_option_iwel_settings', array( $this, '_activate' ) );

    }

    function _activate() {
        flush_rewrite_rules();
        $this->_init();
    }
    
    function _init() {
        //add_filter( 'request', array( $this, '_default_sort' ) );
        //add_action( 'parse_request',        array( $this, 'parse_request' ) );
        // elements
        if ( $this->_option( '0' ) ):
            $labels = array(
                'name'                  => __( 'Elements', 'iwelements' ),
                'singular_name'         => __( 'Element', 'iwelements' ),
                'add_new'               => __( 'Add New Element', 'iwelements' ),
                'add_new_item'          => __( 'Add New Element', 'iwelements' ),
                'edit_item'             => __( 'Edit Element', 'iwelements' ),
                'new_item'              => __( 'Add New Element', 'iwelements' ),
                'view_item'             => __( 'View Element', 'iwelements' ),
                'search_items'          => __( 'Search Elements', 'iwelements' ),
                'not_found'             => __( 'No Elements found', 'iwelements' ),
                'not_found_in_trash'    => __( 'No Elements found in trash', 'iwelements' )
            );
            register_post_type( 'element', $this->_get_cpt_args( $labels ) );

            $labels = array(
                'name'                          => __( 'Element Groups', 'iwelements' ),
                'singular_name'                 => __( 'Element Group', 'iwelements' ),
                'search_items'                  => __( 'Search Element Groups', 'iwelements' ),
                'popular_items'                 => __( 'Popular Element Groups', 'iwelements' ),
                'all_items'                     => __( 'All Element Groups', 'iwelements' ),
                'parent_item'                   => __( 'Parent Element Group', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Element Group:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Element Group', 'iwelements' ),
                'update_item'                   => __( 'Update Element Group', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Element Group', 'iwelements' ),
                'new_item_name'                 => __( 'New Element Group Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate groups with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove groups', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used groups', 'iwelements' ),
                'menu_name'                     => __( 'Element Groups', 'iwelements' ),
            );
            register_taxonomy( 'element_group', array( 'element' ), $this->_get_tax_args( 'element_group', $labels ) );
        endif;
        
        // properties
        if ( $this->_option( '1' ) ):
            $labels = array(
                'name'                  => __( 'Properties', 'iwelements' ),
                'singular_name'         => __( 'Property', 'iwelements' ),
                'add_new'               => __( 'Add New Property', 'iwelements' ),
                'add_new_item'          => __( 'Add New Property', 'iwelements' ),
                'edit_item'             => __( 'Edit Property', 'iwelements' ),
                'new_item'              => __( 'Add New Property', 'iwelements' ),
                'view_item'             => __( 'View Property', 'iwelements' ),
                'search_items'          => __( 'Search Properties', 'iwelements' ),
                'not_found'             => __( 'No Properties found', 'iwelements' ),
                'not_found_in_trash'    => __( 'No Properties found in trash', 'iwelements' )
            );
            register_post_type( 'property', $this->_get_cpt_args( $labels, TRUE, FALSE ) );

            $labels = array(
                'name'                          => __( 'Property Categories', 'iwelements' ),
                'singular_name'                 => __( 'Property Category', 'iwelements' ),
                'search_items'                  => __( 'Search Property Categories', 'iwelements' ),
                'popular_items'                 => __( 'Popular Property Categories', 'iwelements' ),
                'all_items'                     => __( 'All Property Categories', 'iwelements' ),
                'parent_item'                   => __( 'Parent Category', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Category:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Property Category', 'iwelements' ),
                'update_item'                   => __( 'Update Property Category', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Property Category', 'iwelements' ),
                'new_item_name'                 => __( 'New Category Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate Categories with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove Property Categories', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used Property Categories', 'iwelements' ),
                'menu_name'                     => __( 'Property Categories', 'iwelements' ),
            );
            register_taxonomy( 'property_category', array( 'property' ), $this->_get_tax_args( 'property_category', $labels ) );
    
            $labels = array(
                'name'                          => __( 'Property Tags', 'iwelements' ),
                'singular_name'                 => __( 'Property Tag', 'iwelements' ),
                'search_items'                  => __( 'Search Property Tags', 'iwelements' ),
                'popular_items'                 => __( 'Popular Property Tags', 'iwelements' ),
                'all_items'                     => __( 'All Property Tags', 'iwelements' ),
                'parent_item'                   => __( 'Parent Tag', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Tag:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Property Tag', 'iwelements' ),
                'update_item'                   => __( 'Update Property Tag', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Property Tag', 'iwelements' ),
                'new_item_name'                 => __( 'New Tag Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate Tags with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove Property Tags', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used Property Tags', 'iwelements' ),
                'menu_name'                     => __( 'Property Tags', 'iwelements' ),
            );
            register_taxonomy( 'property_tag', array( 'property' ), $this->_get_tax_args( 'property_tag', $labels, FALSE ) );
            add_filter( 'manage_edit-property_columns', array( $this, 'addl_columns') );
            add_action( 'manage_property_posts_custom_column', array( $this, '_custom_column' ) );
//            add_filter( 'manage_edit-property_sortable_columns', array( $this, '_sortable') );
        endif;
        
        // slides
        if ( $this->_option( '2' ) ):
            $labels = array(
                'name'                  => __( 'Slides', 'iwelements' ),
                'singular_name'         => __( 'Slide', 'iwelements' ),
                'add_new'               => __( 'Add New Slide', 'iwelements' ),
                'add_new_item'          => __( 'Add New Slide', 'iwelements' ),
                'edit_item'             => __( 'Edit Slide', 'iwelements' ),
                'new_item'              => __( 'Add New Slide', 'iwelements' ),
                'view_item'             => __( 'View Slide', 'iwelements' ),
                'search_items'          => __( 'Search Slides', 'iwelements' ),
                'not_found'             => __( 'No Slides found', 'iwelements' ),
                'not_found_in_trash'    => __( 'No Slides found in trash', 'iwelements' )
            );
            register_post_type( 'slide', $this->_get_cpt_args( $labels ) );
            
            $labels = array(
                'name'                          => __( 'Slide Groups', 'iwelements' ),
                'singular_name'                 => __( 'Slide Group', 'iwelements' ),
                'search_items'                  => __( 'Search Slide Groups', 'iwelements' ),
                'popular_items'                 => __( 'Popular Slide Groups', 'iwelements' ),
                'all_items'                     => __( 'All Slide Groups', 'iwelements' ),
                'parent_item'                   => __( 'Parent Slide Group', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Slide Group:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Slide Group', 'iwelements' ),
                'update_item'                   => __( 'Update Slide Group', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Slide Group', 'iwelements' ),
                'new_item_name'                 => __( 'New Slide Group Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate groups with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove groups', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used groups', 'iwelements' ),
                'menu_name'                     => __( 'Slide Groups', 'iwelements' ),
            );
            register_taxonomy( 'slide_group', array( 'slide' ), $this->_get_tax_args( 'slide_group', $labels ) );
            
            add_filter( 'manage_edit-slide_columns', array( $this, 'addl_columns') );
            add_action( 'manage_slide_posts_custom_column', array( $this, '_custom_column' ) );
//            add_filter( 'manage_edit-slide_sortable_columns', array( $this, '_sortable') );
        endif;
        
        // work
        if ( $this->_option( '3' ) ):
            $labels = array(
                'name'                  => __( 'Work', 'iwelements' ),
                'singular_name'         => __( 'Work Sample', 'iwelements' ),
                'add_new'               => __( 'Add New Work', 'iwelements' ),
                'add_new_item'          => __( 'Add New Work', 'iwelements' ),
                'edit_item'             => __( 'Edit Work', 'iwelements' ),
                'new_item'              => __( 'Add New Work', 'iwelements' ),
                'view_item'             => __( 'View Work Sample', 'iwelements' ),
                'search_items'          => __( 'Search Work', 'iwelements' ),
                'not_found'             => __( 'No Work found', 'iwelements' ),
                'not_found_in_trash'    => __( 'No Work found in trash', 'iwelements' )
            );
            
            register_post_type( 'work', $this->_get_cpt_args( $labels, TRUE, FALSE ) );
    
            $labels = array(
                'name'                          => __( 'Work Categories', 'iwelements' ),
                'singular_name'                 => __( 'Work Category', 'iwelements' ),
                'search_items'                  => __( 'Search Work Categories', 'iwelements' ),
                'popular_items'                 => __( 'Popular Work Categories', 'iwelements' ),
                'all_items'                     => __( 'All Work Categories', 'iwelements' ),
                'parent_item'                   => __( 'Parent Category', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Category:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Work Category', 'iwelements' ),
                'update_item'                   => __( 'Update Work Category', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Work Category', 'iwelements' ),
                'new_item_name'                 => __( 'New Category Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate Categories with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove Work Categories', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used Work Categories', 'iwelements' ),
                'menu_name'                     => __( 'Work Categories', 'iwelements' ),
            );       
            register_taxonomy( 'work_category', array( 'work' ), $this->_get_tax_args( 'work_category', $labels ) );
    
            $labels = array(
                'name'                          => __( 'Work Tags', 'iwelements' ),
                'singular_name'                 => __( 'Work Tag', 'iwelements' ),
                'search_items'                  => __( 'Search Work Tags', 'iwelements' ),
                'popular_items'                 => __( 'Popular Work Tags', 'iwelements' ),
                'all_items'                     => __( 'All Work Tags', 'iwelements' ),
                'parent_item'                   => __( 'Parent Tag', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Tag:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Work Tag', 'iwelements' ),
                'update_item'                   => __( 'Update Work Tag', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Work Tag', 'iwelements' ),
                'new_item_name'                 => __( 'New Tag Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate Tags with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove Work Tags', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used Work Tags', 'iwelements' ),
                'menu_name'                     => __( 'Work Tags', 'iwelements' ),
            );        
            register_taxonomy( 'work_tag', array( 'work' ), $this->_get_tax_args( 'work_tag', $labels, FALSE ) );
    
            $labels = array(
                'name'                          => __( 'Clients', 'iwelements' ),
                'singular_name'                 => __( 'Client', 'iwelements' ),
                'search_items'                  => __( 'Search Clients', 'iwelements' ),
                'popular_items'                 => __( 'Popular Clients', 'iwelements' ),
                'all_items'                     => __( 'All Clients', 'iwelements' ),
                'parent_item'                   => __( 'Parent Client', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Client:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Client', 'iwelements' ),
                'update_item'                   => __( 'Update Client', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Client', 'iwelements' ),
                'new_item_name'                 => __( 'New Client Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate Client with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove Clients', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used Clients', 'iwelements' ),
                'menu_name'                     => __( 'Clients', 'iwelements' ),
            );        
            register_taxonomy( 'client', array( 'work' ), $this->_get_tax_args( 'client', $labels, FALSE ) );
            add_filter( 'manage_edit-work_columns', array( $this, 'addl_columns') );
            add_action( 'manage_work_posts_custom_column', array( $this, '_custom_column' ) );
//            add_filter( 'manage_edit-work_sortable_columns', array( $this, '_sortable') );
        endif;
        
        // testimonials
        if ( $this->_option( '4' ) ):
            $labels = array(
                'name'                  => __( 'Testimonials', 'iwelements' ),
                'singular_name'         => __( 'Testimonial', 'iwelements' ),
                'add_new'               => __( 'Add New Testimonial', 'iwelements' ),
                'add_new_item'          => __( 'Add New Testimonial', 'iwelements' ),
                'edit_item'             => __( 'Edit Testimonial', 'iwelements' ),
                'new_item'              => __( 'Add New Testimonial', 'iwelements' ),
                'view_item'             => __( 'View Testimonial', 'iwelements' ),
                'search_items'          => __( 'Search Testimonials', 'iwelements' ),
                'not_found'             => __( 'No Testimonials found', 'iwelements' ),
                'not_found_in_trash'    => __( 'No Testimonials found in trash', 'iwelements' )
            );        
            register_post_type( 'testimonial', $this->_get_cpt_args( $labels ) );
    
            add_filter( 'manage_edit-testimonial_columns', array( $this, 'addl_columns') );
            add_action( 'manage_testimonial_posts_custom_column', array( $this, '_custom_column' ) );
//            add_filter( 'manage_edit-testimonial_sortable_columns', array( $this, '_sortable') );
        endif;
        
        // faqs
        if ( $this->_option( '5' ) ):
            
            $labels = array(
                'name'                  => __( 'FAQs', 'iwelements' ),
                'singular_name'         => __( 'FAQ', 'iwelements' ),
                'add_new'               => __( 'Add New FAQ', 'iwelements' ),
                'add_new_item'          => __( 'Add New FAQ', 'iwelements' ),
                'edit_item'             => __( 'Edit FAQ', 'iwelements' ),
                'new_item'              => __( 'Add New FAQ', 'iwelements' ),
                'view_item'             => __( 'View FAQ', 'iwelements' ),
                'search_items'          => __( 'Search FAQs', 'iwelements' ),
                'not_found'             => __( 'No FAQs found', 'iwelements' ),
                'not_found_in_trash'    => __( 'No FAQs found in trash', 'iwelements' )
            );
            register_post_type( 'faqs', $this->_get_cpt_args( $labels ) );
            add_filter( 'manage_edit-faq_columns', array( $this, 'addl_columns') );
            add_action( 'manage_faq_posts_custom_column', array( $this, '_custom_column' ) );
//            add_filter( 'manage_edit-faq_sortable_columns', array( $this, '_sortable') );
        endif;
                        
        // programs
        if ( $this->_option( '6' ) ):
            $labels = array(
                'name'                  => __( 'Programs', 'iwelements' ),
                'singular_name'         => __( 'Program', 'iwelements' ),
                'add_new'               => __( 'Add New Program', 'iwelements' ),
                'add_new_item'          => __( 'Add New Program', 'iwelements' ),
                'edit_item'             => __( 'Edit Program', 'iwelements' ),
                'new_item'              => __( 'Add New Program', 'iwelements' ),
                'view_item'             => __( 'View Programs', 'iwelements' ),
                'search_items'          => __( 'Search Programs', 'iwelements' ),
                'not_found'             => __( 'No Programs found', 'iwelements' ),
                'not_found_in_trash'    => __( 'No Programs found in trash', 'iwelements' )
            );
            
            register_post_type( 'program', $this->_get_cpt_args( $labels, TRUE, FALSE ) );
            $labels = array(
                'name'                          => __( 'Fields', 'iwelements' ),
                'singular_name'                 => __( 'Field', 'iwelements' ),
                'search_items'                  => __( 'Search Fields', 'iwelements' ),
                'popular_items'                 => __( 'Popular Fields', 'iwelements' ),
                'all_items'                     => __( 'All Fields', 'iwelements' ),
                'parent_item'                   => __( 'Parent Field', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Field:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Field', 'iwelements' ),
                'update_item'                   => __( 'Update Field', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Field', 'iwelements' ),
                'new_item_name'                 => __( 'New Field Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate Fields with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove Fields', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used Fields', 'iwelements' ),
                'menu_name'                     => __( 'Fields', 'iwelements' ),
            );
            
            register_taxonomy( 'field', array( 'program' ), $this->_get_tax_args( 'field', $labels ) );
    
            add_post_type_support( 'program', 'wpcom-markdown' );
            add_filter( 'manage_edit-program_columns', array( $this, 'addl_columns') );
            add_action( 'manage_program_posts_custom_column', array( $this, '_custom_column' ) );
//            add_filter( 'manage_edit-program_sortable_columns', array( $this, '_sortable') );
        
        endif;
        
        // courses
        if ( $this->_option( '7' ) ):
            $labels = array(
                'name'                  => __( 'Courses', 'iwelements' ),
                'singular_name'         => __( 'Course', 'iwelements' ),
                'add_new'               => __( 'Add New Course', 'iwelements' ),
                'add_new_item'          => __( 'Add New Course', 'iwelements' ),
                'edit_item'             => __( 'Edit Course', 'iwelements' ),
                'new_item'              => __( 'Add New Course', 'iwelements' ),
                'view_item'             => __( 'View Courses', 'iwelements' ),
                'search_items'          => __( 'Search Courses', 'iwelements' ),
                'not_found'             => __( 'No Courses found', 'iwelements' ),
                'not_found_in_trash'    => __( 'No Courses found in trash', 'iwelements' )
            );
            register_post_type( 'course', $this->_get_cpt_args( $labels, TRUE, FALSE ) );
            
            $labels = array(
                'name'                          => __( 'Disciplines', 'iwelements' ),
                'singular_name'                 => __( 'Discipline', 'iwelements' ),
                'search_items'                  => __( 'Search Disciplines', 'iwelements' ),
                'popular_items'                 => __( 'Popular Disciplines', 'iwelements' ),
                'all_items'                     => __( 'All Disciplines', 'iwelements' ),
                'parent_item'                   => __( 'Parent Discipline', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Discipline:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Discipline', 'iwelements' ),
                'update_item'                   => __( 'Update Discipline', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Discipline', 'iwelements' ),
                'new_item_name'                 => __( 'New Discipline Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate Disciplines with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove Disciplines', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used Disciplines', 'iwelements' ),
                'menu_name'                     => __( 'Disciplines', 'iwelements' ),
            );
            register_taxonomy( 'discipline', array( 'course' ), $this->_get_tax_args( 'discipline', $labels ) );
    
            add_post_type_support( 'course', 'wpcom-markdown' );
            add_filter( 'manage_edit-course_columns', array( $this, 'addl_columns') );
            add_action( 'manage_course_posts_custom_column', array( $this, '_custom_column' ) );
//            add_filter( 'manage_edit-course_sortable_columns', array( $this, '_sortable') );
        endif;
        
        // staff
        if ( $this->_option( '8' ) ):
            $labels = array(
                'name'                  => __( 'Staff', 'iwelements' ),
                'singular_name'         => __( 'Staff', 'iwelements' ),
                'add_new'               => __( 'Add New Staff', 'iwelements' ),
                'add_new_item'          => __( 'Add New Staff', 'iwelements' ),
                'edit_item'             => __( 'Edit Staff', 'iwelements' ),
                'new_item'              => __( 'Add New Staff', 'iwelements' ),
                'view_item'             => __( 'View Staff', 'iwelements' ),
                'search_items'          => __( 'Search Staff', 'iwelements' ),
                'not_found'             => __( 'No Staff found', 'iwelements' ),
                'not_found_in_trash'    => __( 'No Staff found in trash', 'iwelements' )
            );
            register_post_type( 'staff', $this->_get_cpt_args( $labels ) );
            
            $labels = array(
                'name'                          => __( 'Departments', 'iwelements' ),
                'singular_name'                 => __( 'Department', 'iwelements' ),
                'search_items'                  => __( 'Search Departments', 'iwelements' ),
                'popular_items'                 => __( 'Popular Departments', 'iwelements' ),
                'all_items'                     => __( 'All Departments', 'iwelements' ),
                'parent_item'                   => __( 'Parent Department', 'iwelements' ),
                'parent_item_colon'             => __( 'Parent Department:', 'iwelements' ),
                'edit_item'                     => __( 'Edit Department', 'iwelements' ),
                'update_item'                   => __( 'Update Department', 'iwelements' ),
                'add_new_item'                  => __( 'Add New Department', 'iwelements' ),
                'new_item_name'                 => __( 'New Department Name', 'iwelements' ),
                'separate_items_with_commas'    => __( 'Separate Departments with commas', 'iwelements' ),
                'add_or_remove_items'           => __( 'Add or remove Departments', 'iwelements' ),
                'choose_from_most_used'         => __( 'Choose from the most used Departments', 'iwelements' ),
                'menu_name'                     => __( 'Departments', 'iwelements' ),
            );
            register_taxonomy( 'department', array( 'staff' ), $this->_get_tax_args( 'department', $labels ) );
    
            add_shortcode( 'stafflist',         array( $this, 'render_staff_list' ) );
            add_shortcode( 'departmentlist',    array( $this, 'render_department_list' ) );
            add_shortcode( 'alphabetlist',      array( $this, 'render_alphabet_list' ) );
    
            add_filter( 'manage_edit-staff_columns', array( $this, 'addl_columns') );
            add_action( 'manage_staff_posts_custom_column', array( $this, '_custom_column' ) );
//            add_filter( 'manage_edit-staff_sortable_columns', array( $this, '_sortable') );
            add_action( 'current_screen', array( $this, '_init_staff' ) );
        endif;        
    }
    
    function _init_staff(){
        //die( print_r( get_current_screen(), TRUE ) );
        if ( current_user_can( 'manage_options' ) && 'edit-staff' == get_current_screen()->id ): //&& 'edit-staff' == get_current_screen()->id
            add_filter( 'views_edit-staff',  array( $this, 'upload_csv_form' ), 50 );
            if (  isset( $_POST[ 'csv_upload' ] ) && !empty( $_FILES ) )
                $this->handle_csv_file();
        endif;
    }
     
    function _get_cpt_args( $labels, $queryable = FALSE, $nosearch = TRUE ){
        return array(
            'labels'                => $labels,
            'public'                => TRUE,
            'hierarchical'          => FALSE,
            'exclude_from_search'   => $nosearch,
            'publicly_queryable'    => $queryable,
            'show_ui'               => TRUE,
            'show_in_nav_menus'     => FALSE,
            'supports'              => array( 
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'page-attributes',
                'custom-fields',
             ),
            'capability_type'       => 'post',
            'has_archive'           => TRUE
        );
            
    }
    
    function _get_tax_args( $slug, $labels, $hierarchical = TRUE ){
        return array(
            'labels'            => $labels,
            'public'            => TRUE,
            'show_in_nav_menus' => TRUE,
            'show_ui'           => TRUE,
            'show_admin_column' => TRUE,
            'show_tagcloud'     => TRUE,
            'hierarchical'      => $hierarchical,
            'rewrite'           => array( 'slug' => $slug ),
            'query_var'         => TRUE
        );
    }
    
    function addl_columns( $columns ) {
        $columns[ 'menu_order' ] = "Order";    
        $column_thumbnail = array( 'thumbnail' => __('Thumbnail','iwelements' ) );
        $columns = array_slice( $columns, 0, 2, true ) + $column_thumbnail + array_slice( $columns, 1, NULL, true );
        return $columns;
    }
    
    function _custom_column( $column ) {
        global $post;
        switch ( $column ):
            case 'thumbnail':
                echo get_the_post_thumbnail( $post->ID, array(60, 60) );
                break;
            case 'menu_order':
                $order = $post->menu_order;
                echo $order;
                break;
        endswitch;
    }

    function _sortable($columns){
        $columns['menu_order'] = 'menu_order';
        return $columns;
    }
    
    function _default_sort( $vars ) {
        if ( !isset( $vars['orderby'] ) ) {
            $vars = array_merge( $vars, array(
                'orderby' => 'menu_order',
                'order'   => 'ASC',
            ) );
        }
        return $vars;
    }
    
    function handle_csv_file(){
        //import handling (test CSV)
        $import_details = false;
        $uploads = wp_upload_dir();
        $temp = $uploads[ 'basedir' ] . "/temp.csv";
        if ( !in_array( $_FILES['importfile']['type'], array('application/vnd.ms-excel','text/plain','text/csv','text/tsv' ) ) ): 
            die( "Only CSV imports are currently supported. This filetype was: {$_FILES['importfile']['type']}." ); 
        else:
            if ( !move_uploaded_file( $_FILES['importfile']['tmp_name'], $temp ) )
                die("Could not upload the file to {$uploads['basedir']}. Check your site's directory permissions." );
            else
                $import_details = $this->handle_csv_import($temp);
        endif;
    }
    
    function handle_csv_import( $file ) {
        $data = file_get_contents( $file );
        // normalize
        $data = preg_replace( "/(\r\n|\r)/", "\n", $data );
        $data_arr = explode( "\n", $data ); //str_getcsv($data, "\n" );
        $count = 0;
        $keys = array();
        foreach ( $data_arr as $row ):
            $colcount   = 0;
            $record     = array();
	        $cols       = str_getcsv( $row, ",", '"' );
            if ( 0 == $count ):
                $keys = $cols;
            else:
        	    foreach( $keys as $key ):
                    $val = array_shift( $cols );
                    $record[ $key ] = $val;
                endforeach;
                $this->update_post( $record, get_current_screen()->post_type, $count );
            endif;
            $count++;
        endforeach;
    }
    
    function update_post( $record, $post_type, $count ){
        $postdata = $this->normalize_record( $record, $post_type );
        // update post
        // retrieve existing post with same slug (Sales_ID)
        $updated = 0;
        if ( $post = get_page_by_path( $postdata[ 'post' ][ 'post_name' ], OBJECT, $post_type ) ):
            $postdata[ 'post' ]['ID'] = $post->ID;
            if ( ( $id = wp_update_post( $postdata[ 'post' ], TRUE ) ) && !is_wp_error( $id ) ):
                $updated = 1;
            endif;
        else:
            // if no match, then this must be a new record so insert instead
            if ( ( $id = wp_insert_post( $postdata[ 'post' ], TRUE ) ) && !is_wp_error( $id ) ):
                $postdata[ 'post' ]['ID'] = $id;
                $updated = 1;
            endif;
        endif;
        // set meta fields
        if ( $updated ):
            // update post meta fields
            foreach ( $postdata[ 'postmeta' ] as $name => $value ):
                //echo $name . "\t" . $value . "\n";
                if ( '' == $value ):
                    delete_post_meta( $postdata[ 'post' ]['ID'], $name );
                else:
                    update_post_meta( $postdata[ 'post' ]['ID'], $name, $value );
                endif;
            endforeach;
            // add categories
            wp_set_object_terms( $postdata[ 'post' ]['ID'], $postdata[ 'terms' ]['ids'], $postdata[ 'terms' ]['taxon'] );

        endif;
    }
    
    function normalize_record( $record, $post_type ){
        $postdata = array(
            'post'      => array(),
            'postmeta'  => array(),
            'terms'     => array(),
        );
        if ( 'staff' == $post_type ):
            $title = $record[ 'lastname' ] . ', ' . $record[ 'firstname' ];
            $postdata[ 'post' ] = array(
                'post_title'    => $title,
                'post_name'     => trim( strtolower( preg_replace( "/[\W\s]+/", '-', $title ) ) ), 
                'post_status'   => 'publish', 
                'post_type'     => 'staff', 
                'post_author'   => 0, 
                'ping_status'   => 'closed',
            );  
                    
            $postdata[ 'postmeta' ][ 'firstname' ]  = $record[ 'firstname' ];
            $postdata[ 'postmeta' ][ 'lastname' ]   = $record[ 'lastname' ];
            $postdata[ 'postmeta' ][ 'title' ]      = $record[ 'title' ]; 
                //empty( $record[ 'title' ] ) ? $record[ 'title2' ] : $record[ 'title' ];
            $postdata[ 'postmeta' ][ 'phone' ]      = $record[ 'phone' ]; 
                //empty( $record[ 'phone' ] ) ? $record[ 'phone2' ] : $record[ 'phone' ];
                //$postdata[ 'postmeta' ][ 'fax' ]        = $record[ 'fax' ];
            $postdata[ 'postmeta' ][ 'email' ]      = $record[ 'email' ];
            $postdata[ 'postmeta' ][ 'room' ]       = $record[ 'room' ];
            $credentials = array();
            $postdata[ 'terms' ][ 'taxon' ] = 'department';
            for( $i = 1; $i < 5; $i++ ):
                if ( !empty( $record[ 'cv' . $i ] ) )
                    $credentials[] = $record[ 'cv' . $i ];
                if ( !empty( $record[ 'department' . $i ] ) ):
                    $termname = $record[ 'department' . $i ];
                    /*
                    if ( !( $term = term_exists( $record[ 'department' . $i ], 'department' ) ) ):
                        $term = wp_insert_term( $record[ 'department' . $i ], 'department' );
                    endif;
                    */
                    $postdata[ 'terms' ][ 'ids' ][] = $termname; //$term[ 'term_taxonomy_id' ];
                endif;
            endfor;
            $postdata[ 'post' ][ 'post_content' ] = implode( "\n", $credentials );
            
        endif;
        return $postdata;
    }
    
    function upload_csv_form( $views ){
        $views[] = '<form method="post" enctype="multipart/form-data"><label>' . __( 'Import from CSV', 'iwelements' ) . ':</label><input type="file" name="importfile" /><input class="button-secondary" type="submit" value="Upload" name="csv_upload" /></form>';
        return $views;
    }
    
    function parse_request( $wp ){
        //die( print_r( $wp, TRUE ) );
    }
    
    function render_staff_list( $atts ) {
        
        $a = shortcode_atts( array(), $atts );
        $out = '';
        
        global $post;
        // get no posts template
        $noposts_template = get_post_meta( $post->ID, 'staff_noposts_template', TRUE );
        
        // get posts template
        $posts_template = get_post_meta( $post->ID, 'staff_posts_template', TRUE );
            
        $query = array(
            'post_status'       => 'publish', 
            'post_type'         => 'staff', 
            'posts_per_page'    => -1,
            'order'             => 'ASC', 
            'orderby'           => 'post_title',
            'suppress_filters'  => FALSE,
        );

        $tax_query = array();
        if ( isset( $_GET[ 'd' ] ) ):
            $term_id = intval( $_GET[ 'd' ] );
            $term = get_term( $term_id );
            $out .= '<h3 class="staff-department-title">' . $term->name . '</h3>';
            $tax_query[] = array(
                'taxonomy' => 'department',
                'field' => 'term_id',
                'terms' => $term_id,
                'operator' => 'AND',
            );
        endif;
        
        
        if ( !empty( $tax_query ) ):
            $tax_query[ 'relation' ] = 'AND';
            $query[ 'tax_query'] = $tax_query;
        endif;
        
        add_filter( 'posts_where', array( $this, 'lastname_where' ) );

        $qobj = new WP_Query( $query );
        //echo $qobj->request;
        remove_filter( 'posts_where', array( $this, 'lastname_where' ) );
        if ( $qobj->have_posts() ):
            $out .= '<ul class="staff-list">';
            while( $qobj->have_posts() ):
                $qobj->the_post();
                $out .= '<li class="staff-list-item">';
                $out .= apply_filters( 'the_content', $posts_template );
                $out .= '</li>';
            endwhile;
            $out .= '</ul>';
        else:
            $out .= apply_filters( 'the_content', $noposts_template );
        endif;
        wp_reset_postdata();
        
        return $out;
    }
    
    function render_department_list( $atts ) {
        $a = shortcode_atts( array(), $atts );
        $terms = get_terms( 'department' );
        $out = '<ul class="staff-department-list">';
        foreach ( $terms as $term ):
            $out .= '<li class="staff-department-item"><a class="staff-department-link" href="?d=' . $term->term_taxonomy_id . '">' . $term->name . '</a></li>';
        endforeach;
        $out .= '</ul>';
        return $out;
    }
    
    function render_alphabet_list( $atts ) {
        $a = shortcode_atts( array(), $atts );
        $out = '<ul class="staff-lastname-list">';
        for ( $i = 0; $i < 26; $i++ ):
            $letter = chr( $i + 65 );
            $out .= '<li class="staff-lastname-item"><a class="staff-lastname-link" href="?l=' . $letter . '">' . $letter . '</a></li>';
        endfor;
        $out .= '</ul>';
        return $out;
    }
    
    function lastname_where( $where ){
        if ( isset( $_GET[ 'l' ] ) ):
            $l = sanitize_text_field( substr( $_GET[ 'l' ], 0, 1 ) );
            $where .= " AND post_title LIKE '" . $l . "%'";
        endif;
        return $where;
    }

    function iwel_add_admin_menu(  ) { 
        add_options_page( 'IntelliWidget Elements', 'IntelliWidget Elements', 'manage_options', 'intelliwidget_elements', array( $this, 'iwel_options_page' ) );
    }
    
    function iwel_settings_init(  ) { 
    
        register_setting( 'iwel_settings_page', 'iwel_settings' );
    
        add_settings_section(
            'iwel_iwel_settings_page_section', 
            __( 'Custom Post Types', 'iwelements' ), 
            array( $this, 'iwel_settings_section_callback' ), 
            'iwel_settings_page'
        );
    
        add_settings_field( 
            'iwel_checkbox_field_0', 
            __( 'Elements', 'iwelements' ), 
            array( $this, 'iwel_checkbox_field_0_render' ), 
            'iwel_settings_page', 
            'iwel_iwel_settings_page_section' 
        );
    
        add_settings_field( 
            'iwel_checkbox_field_1', 
            __( 'Properties', 'iwelements' ), 
            array( $this, 'iwel_checkbox_field_1_render' ), 
            'iwel_settings_page', 
            'iwel_iwel_settings_page_section' 
        );
    
        add_settings_field( 
            'iwel_checkbox_field_2', 
            __( 'Slides', 'iwelements' ), 
            array( $this, 'iwel_checkbox_field_2_render' ), 
            'iwel_settings_page', 
            'iwel_iwel_settings_page_section' 
        );
    
        add_settings_field( 
            'iwel_checkbox_field_3', 
            __( 'Work', 'iwelements' ), 
            array( $this, 'iwel_checkbox_field_3_render' ), 
            'iwel_settings_page', 
            'iwel_iwel_settings_page_section' 
        );
    
        add_settings_field( 
            'iwel_checkbox_field_4', 
            __( 'Testimonials', 'iwelements' ), 
            array( $this, 'iwel_checkbox_field_4_render' ), 
            'iwel_settings_page', 
            'iwel_iwel_settings_page_section' 
        );
    
        add_settings_field( 
            'iwel_checkbox_field_5', 
            __( 'FAQs', 'iwelements' ), 
            array( $this, 'iwel_checkbox_field_5_render' ), 
            'iwel_settings_page', 
            'iwel_iwel_settings_page_section' 
        );
    
        add_settings_field( 
            'iwel_checkbox_field_6', 
            __( 'Programs', 'iwelements' ), 
            array( $this, 'iwel_checkbox_field_6_render' ), 
            'iwel_settings_page', 
            'iwel_iwel_settings_page_section' 
        );
    
        add_settings_field( 
            'iwel_checkbox_field_7', 
            __( 'Courses', 'iwelements' ), 
            array( $this, 'iwel_checkbox_field_7_render' ), 
            'iwel_settings_page', 
            'iwel_iwel_settings_page_section' 
        );
    
        add_settings_field( 
            'iwel_checkbox_field_8', 
            __( 'Staff', 'iwelements' ), 
            array( $this, 'iwel_checkbox_field_8_render' ), 
            'iwel_settings_page', 
            'iwel_iwel_settings_page_section' 
        );
    }
    
    private function _option( $field ){
        $options = get_option( 'iwel_settings' );
        return !empty( $options[ 'iwel_checkbox_field_' . $field ] ) ? 1 : 0;
    }
    
    function iwel_checkbox_field_0_render(  ) { 
        ?>
        <input type='checkbox' name='iwel_settings[iwel_checkbox_field_0]' <?php checked( $this->_option( '0' ), 1 ); ?> value='1'>
        <?php
    }
    
    function iwel_checkbox_field_1_render(  ) { 
        ?>
        <input type='checkbox' name='iwel_settings[iwel_checkbox_field_1]' <?php checked( $this->_option( '1' ), 1 ); ?> value='1'>
        <?php
    }
    
    function iwel_checkbox_field_2_render(  ) { 
        ?>
        <input type='checkbox' name='iwel_settings[iwel_checkbox_field_2]' <?php checked( $this->_option( '2' ), 1 ); ?> value='1'>
        <?php
    }
    
    function iwel_checkbox_field_3_render(  ) { 
        ?>
        <input type='checkbox' name='iwel_settings[iwel_checkbox_field_3]' <?php checked( $this->_option( '3' ), 1 ); ?> value='1'>
        <?php
    }
    
    function iwel_checkbox_field_4_render(  ) { 
        ?>
        <input type='checkbox' name='iwel_settings[iwel_checkbox_field_4]' <?php checked( $this->_option( '4' ), 1 ); ?> value='1'>
        <?php
    }
    
    function iwel_checkbox_field_5_render(  ) { 
        ?>
        <input type='checkbox' name='iwel_settings[iwel_checkbox_field_5]' <?php checked( $this->_option( '5' ), 1 ); ?> value='1'>
        <?php
    }
    
    
    function iwel_checkbox_field_6_render(  ) { 
        ?>
        <input type='checkbox' name='iwel_settings[iwel_checkbox_field_6]' <?php checked( $this->_option( '6' ), 1 ); ?> value='1'>
        <?php
    }
    
    function iwel_checkbox_field_7_render(  ) { 
        ?>
        <input type='checkbox' name='iwel_settings[iwel_checkbox_field_7]' <?php checked( $this->_option( '7' ), 1 ); ?> value='1'>
        <?php
    }
    
    function iwel_checkbox_field_8_render(  ) { 
        ?>
        <input type='checkbox' name='iwel_settings[iwel_checkbox_field_8]' <?php checked( $this->_option( '8' ), 1 ); ?> value='1'>
        <?php
    }
    
    function iwel_settings_section_callback(  ) { 
        echo __( 'Enable or Disable Custom Post Types by toggling the checkboxes below.', 'iwelements' );
    }
    
    function iwel_options_page(  ) { 
    
        ?><div class="wrap">
        <form action='file:///Macintosh HD/Users/jfleming/Documents/LilaeaMedia/plugins-themes/intelliwidget/intelliwidget-elements/tags/1.1.1/options.php' method='post'>
    
            <h1>IntelliWidget Elements</h1>
    
            <?php
            settings_fields( 'iwel_settings_page' );
            do_settings_sections( 'iwel_settings_page' );
            submit_button();
            ?>
    
        </form></div>
        <?php
    }
}

new IntelliWidgetElements();

endif;