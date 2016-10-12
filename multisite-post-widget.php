<?php
/*
Plugin Name: Multisite Posts Widget
Description: This is a modified version of Ultimate Post Widget that includes posts from othersites in same network (http://pomelodesign.com)
Version: 1.0.0
Author URI: http://pomelodesign.com
Text Domain: upw
Domain Path: /languages/
License: MIT
*/

if ( !class_exists( 'WP_Widget_Multisite_Posts' ) ) {
  class WP_Widget_Multisite_Posts extends WP_Widget {

      public $currentblogid;
      public $instance;
    function __construct() {

        $this->currentblogid = get_current_blog_id();


      $widget_options = array(
        'classname' => 'widget_multisite_posts',
        'description' => __( 'Displays list of posts with an array of options', 'upw' )
      );

      $control_options = array(
        'width' => 450
      );

      parent::__construct(
        'sticky-posts',
        __( 'Multisite Posts', 'upw' ),
        $widget_options,
        $control_options
      );

      $this->alt_option_name = 'widget_multisite_posts';

      add_action('save_post', array(&$this, 'flush_widget_cache'));
      add_action('deleted_post', array(&$this, 'flush_widget_cache'));
      add_action('switch_theme', array(&$this, 'flush_widget_cache'));
      add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_scripts'));

      if (apply_filters('upw_enqueue_styles', true) && !is_admin()) {
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_theme_scripts'));
      }

      load_plugin_textdomain('upw', false, basename( dirname( __FILE__ ) ) . '/languages' );

    }


    function enqueue_admin_scripts() {
      wp_register_style('upw_admin_styles', plugins_url('css/upw-admin.min.css', __FILE__));
      wp_enqueue_style('upw_admin_styles');

      //wp_register_script('upw_admin_scripts', plugins_url('js/upw-admin.min.js', __FILE__), array('jquery'), null, true);
      wp_register_script('upw_admin_scripts', plugins_url('js/upw-admin.js', __FILE__), array('jquery'), null, true);
      wp_enqueue_script('upw_admin_scripts');
    }

    function enqueue_theme_scripts() {
      wp_register_style('upw_theme_standard', plugins_url('css/upw-theme-standard.min.css', __FILE__));
      wp_enqueue_style('upw_theme_standard');
    }

    function widget( $args, $instance ) {
        $this->instance = $instance;
        include 'includes/public-widget.php';
    }

    function update( $new_instance, $old_instance ) {
      error_log('hej');
      $this->instance = $old_instance;

      $this->instance['title'] = strip_tags( $new_instance['title'] );
      $this->instance['class'] = strip_tags( $new_instance['class']);
      $this->instance['title_link'] = strip_tags( $new_instance['title_link'] );
      $this->instance['number'] = strip_tags( $new_instance['number'] );

      $this->instance['types'] = (isset( $new_instance['types'] )) ? implode(',', (array) $new_instance['types']) : '';

      if (is_multisite()):
        $blogs = (array) $new_instance['blogs'];
        $this->instance['blogs'] = (isset( $new_instance['blogs'] )) ? implode(',', (array) $new_instance['blogs']) : '';
      endif;
      $this->instance['cats'] = (isset( $new_instance['cats'] )) ? implode(',', (array) $new_instance['cats']) : '';
      $this->instance['tags'] = (isset( $new_instance['tags'] )) ? implode(',', (array) $new_instance['tags']) : '';
      $this->instance['atcat'] = isset( $new_instance['atcat'] );
      $this->instance['attag'] = isset( $new_instance['attag'] );
      $this->instance['hidemeta'] = isset($new_instance['hidemeta']);

      $this->instance['order'] = $new_instance['order'];
      $this->instance['orderby'] = $new_instance['orderby'];
      $this->instance['meta_key'] = $new_instance['meta_key'];

      $this->instance['template'] = strip_tags( $new_instance['template'] );

      if (current_user_can('unfiltered_html')) {
          $this->instance['before_posts'] =  $new_instance['before_posts'];
          $this->instance['after_posts'] =  $new_instance['after_posts'];
      } else {
          $this->instance['before_posts'] = wp_filter_post_kses($new_instance['before_posts']);
          $this->instance['after_posts'] = wp_filter_post_kses($new_instance['after_posts']);
      }

      $this->flush_widget_cache();

      $alloptions = wp_cache_get( 'alloptions', 'options' );
      if ( isset( $alloptions['widget_multisite_posts'] ) )
        delete_option( 'widget_multisite_posts' );

      return $this->instance;

    }

    function flush_widget_cache() {

      wp_cache_delete( 'widget_multisite_posts', 'widget' );

    }

    function form( $instance ) {
        $this->instance = $instance;
      include 'includes/admin-form.php';
    }

    function sort($filteredposts)
    {


        usort($filteredposts, array($this, 'itemcompare'));

        return $filteredposts;

    }
    private function itemcompare($a, $b)
    {
        $order = $this->instance['order'];
        $orderby = $this->instance['orderby'];
        $aval;
        $bval;
        if($orderby == 'date')
        {
            $aval = $a->post_date;
            $bval = $b->post_date;
        }
        else if($orderby == 'title')
        {
            $aval = $a->post_title;
            $bval = $b->post_title;
        }
        else if($orderby == 'meta_value')
        {
            $aval = $a->meta_sort_value;
            $bval = $b->meta_sort_value;

        }
        else if($orderby == 'menu_order')
        {
            $aval = $a->menu_order;
            $bval = $b->menu_order;
        }

        //$custom_fields = $this->instance['custom_fields'];
        if($order == 'ASC')
            return $aval > $bval;
        return $aval < $bval;
    }
    public function get_theme_templates()
    {
        $templateDirectory = $this->get_template_directory();
        if(!file_exists($templateDirectory))
        {
            mkdir($templateDirectory);
        }
        $templates = scandir($templateDirectory);
        $templateItems = array();
        foreach($templates as $template)
        {
            if(!$this->endsWith($template, '.PHP'))
                continue;
            $item = new stdClass();
            $item->title = 'Custom: ' . $template;
            $item->filename = $template;
            $templateItems[] = $item;
        }
        return $templateItems;
    }
    public function get_template_directory()
    {
        return STYLESHEETPATH.DIRECTORY_SEPARATOR.'mpw-templates';
    }
    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr(strtolower($haystack), -$length) === strtolower($needle));
    }

  }


  function init_wp_widget_multisite_posts() {
    register_widget( 'WP_Widget_Multisite_Posts' );
  }

  add_action( 'widgets_init', 'init_wp_widget_multisite_posts' );
}
