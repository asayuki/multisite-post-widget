<?php


    // Set default arguments
    $instance = wp_parse_args( (array) $instance, array(
      'title' => __('Multisite Posts', 'upw'),
      'class' => '',
      'title_link' => '' ,
      'number' => '5',
      'types' => 'post',
      'blogs' => '',
      'cats' => '',
      'tags' => '',
      'atcat' => false,
      'thumb_size' => 'thumbnail',
      'attag' => false,
      'excerpt_length' => 10,
      'excerpt_readmore' => __('Read more &rarr;', 'upw'),
      'order' => 'DESC',
      'orderby' => 'date',
      'meta_key' => '',
      'sticky' => 'show',
      'show_cats' => false,
      'show_tags' => false,
      'show_title' => true,
      'show_date' => true,
      'date_format' => get_option('date_format') . ' ' . get_option('time_format'),
      'show_author' => true,
      'show_comments' => false,
      'show_excerpt' => true,
      'show_content' => false,
      'show_readmore' => true,
      'show_thumbnail' => true,
      'custom_fields' => '',
      // Set template to 'legacy' if field from UPW < 2.0 is set.
      'template' => empty($instance['morebutton_text']) ? 'standard' : 'legacy',
      'template_custom' => '',
      'before_posts' => '',
      'after_posts' => ''
    ) );

    // Or use the instance
    $title  = strip_tags($instance['title']);
    $class  = strip_tags($instance['class']);
    $title_link  = strip_tags($instance['title_link']);
    $number = strip_tags($instance['number']);
    $types  = $instance['types'];
    $blogs = $instance['blogs'];
    $cats = $instance['cats'];
    $tags = $instance['tags'];
    $atcat = $instance['atcat'];
    $thumb_size = $instance['thumb_size'];
    $attag = $instance['attag'];
    $excerpt_length = strip_tags($instance['excerpt_length']);
    $excerpt_readmore = strip_tags($instance['excerpt_readmore']);
    $order = $instance['order'];
    $orderby = $instance['orderby'];
    $meta_key = $instance['meta_key'];
    $sticky = $instance['sticky'];
    $show_cats = $instance['show_cats'];
    $show_tags = $instance['show_tags'];
    $show_title = $instance['show_title'];
    $show_date = $instance['show_date'];
    $show_author = $instance['show_author'];
    $show_comments = $instance['show_comments'];
    $show_excerpt = $instance['show_excerpt'];
    $show_content = $instance['show_content'];
    $show_readmore = $instance['show_readmore'];
    $show_thumbnail = $instance['show_thumbnail'];
    $custom_fields = strip_tags($instance['custom_fields']);
    $template = $instance['template'];
    $template_custom = strip_tags($instance['template_custom']);
    $before_posts = format_to_edit($instance['before_posts']);
    $after_posts = format_to_edit($instance['after_posts']);

    // Let's turn $types, $cats, and $tags into an array if they are set
    if (!empty($types)) 
    {
        if(!is_array($types))
            $types = explode(',', $types);
    }
        
    if (!empty($cats)) 
    {
        if(!is_array($cats))
            $cats = explode(',', $cats);
    }
        
    if (!empty($tags)) 
    {
        if(!is_array($tags))
        {
            $tags = explode(',', $tags);
        }
    }
    if (!empty($blogs)) 
    {
        if(!is_array($blogs))
        {
            $blogs = explode(',', $blogs);
        }
    }
    
    // Count number of post types for select box sizing
    $cpt_types = get_post_types( array( 'public' => true ), 'names' );
    if ($cpt_types) {
        foreach ($cpt_types as $cpt ) {
            $cpt_ar[] = $cpt;
        }
        $n = count($cpt_ar);
        if($n > 6) { $n = 6; }
    } else {
        $n = 3;
    }

    
    // Count number of categories for select box sizing
    $categories = array();
    $tag_list = array();
    $post_types = array(); 
    $all_blogs = wp_get_sites();
    foreach ( $all_blogs as $blog ) {

        
        // switch to the blog
        switch_to_blog( $blog['blog_id'] );
        $blogcategory = get_categories( 'hide_empty=0' );
        foreach ( $blogcategory as $category ) {
            $categories[] = $category;
        }

        $blogtags = get_tags( 'hide_empty=0' );
        foreach($blogtags as $tag)
        {
            $tag_list[] = $tag;
        }

        $blogposttypes = get_post_types( array( 'public' => true ), 'names' );
        foreach($blogposttypes as $post_type )
        {
            if(!in_array($post_type, $post_types))
            {
                $post_types[] = $post_type;
            }   
        }
    }
    switch_to_blog( $this->currentblogid );
    if ($categories) {
        foreach ($categories as $cat) {
            $cat_ar[] = $cat;
        }
        $c = count($cat_ar);
        if($c > 6) { $c = 6; }
    } else {
        $c = 3;
    }

    // Count number of tags for select box sizing
    
    if ($tag_list) 
    {
        foreach ($tag_list as $tag) 
        {
            $tag_ar[] = $tag;
        }
        $t = count($tag_ar);
        if($t > 6) 
        { 
            $t = 6; 
        }
    } 
    else 
    {
        $t = 3;
    }

?>

      <div class="upw-tabs">
        <a class="upw-tab-item active" data-toggle="upw-tab-general"><?php _e('General', 'upw'); ?></a>
        <a class="upw-tab-item" data-toggle="upw-tab-display"><?php _e('Display', 'upw'); ?></a>
        <a class="upw-tab-item" data-toggle="upw-tab-filter"><?php _e('Filter', 'upw'); ?></a>
        <a class="upw-tab-item" data-toggle="upw-tab-order"><?php _e('Order', 'upw'); ?></a>
      </div>

      <div class="upw-tab upw-tab-general">

        <p>
          <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'upw' ); ?>:</label>
          <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'title_link' ); ?>"><?php _e( 'Title URL', 'upw' ); ?>:</label>
          <input class="widefat" id="<?php echo $this->get_field_id( 'title_link' ); ?>" name="<?php echo $this->get_field_name( 'title_link' ); ?>" type="text" value="<?php echo $title_link; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'CSS class', 'upw' ); ?>:</label>
          <input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" type="text" value="<?php echo $class; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('before_posts'); ?>"><?php _e('Before posts', 'upw'); ?>:</label>
          <textarea class="widefat" id="<?php echo $this->get_field_id('before_posts'); ?>" name="<?php echo $this->get_field_name('before_posts'); ?>" rows="5"><?php echo $before_posts; ?></textarea>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('after_posts'); ?>"><?php _e('After posts', 'upw'); ?>:</label>
          <textarea class="widefat" id="<?php echo $this->get_field_id('after_posts'); ?>" name="<?php echo $this->get_field_name('after_posts'); ?>" rows="5"><?php echo $after_posts; ?></textarea>
        </p>

      </div>

      <div class="upw-tab upw-hide upw-tab-display">

        <p>

          <label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template', 'upw'); ?>:</label>
          <select name="<?php echo $this->get_field_name('template'); ?>" id="<?php echo $this->get_field_id('template'); ?>" class="widefat template">
            <option value="standard"<?php if( $template == 'standard') echo ' selected'; ?>><?php _e('Standard', 'upw'); ?></option>
              <?php
              $customTemplates = $this->get_theme_templates();
              foreach($customTemplates as $template)
              {
                  echo '<option value="'.$template->filename.'">'.$template->title.'</option>';
              }
                  ?>
          </select>
            <?php 
            
              
                  ?>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts', 'upw' ); ?>:</label>
          <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" value="<?php echo $number; ?>" min="-1" />
        </p>



      </div>

      <div class="upw-tab upw-hide upw-tab-filter">

        <p>
          <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('atcat'); ?>" name="<?php echo $this->get_field_name('atcat'); ?>" <?php checked( (bool) $atcat, true ); ?> />
          <label for="<?php echo $this->get_field_id('atcat'); ?>"> <?php _e('Show posts only from current category', 'upw');?></label>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('cats'); ?>"><?php _e( 'Categories', 'upw' ); ?>:</label>
          <select name="<?php echo $this->get_field_name('cats'); ?>" id="<?php echo $this->get_field_id('cats'); ?>" class="widefat" style="height: auto;" size="<?php echo $c ?>" multiple>
            <option value="" <?php if (empty($cats)) echo 'selected="selected"'; ?>><?php _e('&ndash; Show All &ndash;') ?></option>
            <?php
    
    foreach ($categories as $category ) { ?>
              <option value="<?php echo $category->slug; ?>" <?php if(is_array($cats) && in_array($category->slug, $cats)) echo 'selected="selected"'; ?>><?php echo $category->cat_name;?></option>
            <?php } ?>
          </select>
        </p>

        <?php if ($tag_list) { ?>
          <p>
            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('attag'); ?>" name="<?php echo $this->get_field_name('attag'); ?>" <?php checked( (bool) $attag, true ); ?> />
            <label for="<?php echo $this->get_field_id('attag'); ?>"> <?php _e('Show posts only from current tag', 'upw');?></label>
          </p>

          <p>
            <label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e( 'Tags', 'upw' ); ?>:</label>
            <select name="<?php echo $this->get_field_name('tags'); ?>" id="<?php echo $this->get_field_id('tags'); ?>" class="widefat" style="height: auto;" size="<?php echo $t ?>" multiple>
              <option value="" <?php if (empty($tags)) echo 'selected="selected"'; ?>><?php _e('&ndash; Show All &ndash;') ?></option>
              <?php
                  foreach ($tag_list as $tag) { ?>
                <option value="<?php echo $tag->slug; ?>" <?php if (is_array($tags) && in_array($tag->slug, $tags)) echo 'selected="selected"'; ?>><?php echo $tag->name;?></option>
              <?php } ?>
            </select>
          </p>
        <?php } ?>

        <p>
          <label for="<?php echo $this->get_field_id('types'); ?>"><?php _e( 'Post types', 'upw' ); ?>:</label>
          <select name="<?php echo $this->get_field_name('types'); ?>" id="<?php echo $this->get_field_id('types'); ?>" class="widefat" style="height: auto;" size="<?php echo $n ?>" multiple>
            <option value="" <?php if (empty($types)) echo 'selected="selected"'; ?>><?php _e('&ndash; Show All &ndash;') ?></option>
            <?php
    
              foreach ($post_types as $post_type ) { ?>
              <option value="<?php echo $post_type; ?>" <?php if(is_array($types) && in_array($post_type, $types)) { echo 'selected="selected"'; } ?>><?php echo $post_type;?></option>
            <?php } ?>
          </select>
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('blogs'); ?>"><?php _e( 'Websites', 'upw' ); ?>:</label>
          <select name="<?php echo $this->get_field_name('blogs'); ?>" id="<?php echo $this->get_field_id('blogs'); ?>" class="widefat" style="height: auto;" size="<?php echo $n ?>" multiple>
            <option value="" <?php if (empty($blogs)) echo 'selected="selected"'; ?>><?php _e('&ndash; Show All &ndash;') ?></option>
            <?php
            
            foreach ($all_blogs as $blog ) { 
                $details = get_blog_details($blog['blog_id']);
                ?>
              <option value="<?php echo $blog['blog_id']; ?>" <?php if(is_array($blogs) && in_array($blog['blog_id'], $blogs)) { echo 'selected="selected"'; } ?>><?php echo $details->blogname;?></option>
            <?php } ?>
          </select>
        </p>

      </div>

      <div class="upw-tab upw-hide upw-tab-order">

        <p>
            
          <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order by', 'upw'); ?>:</label>
            <select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>" class="widefat orderby">
                <option value="date" <?php if( $orderby == 'date') echo ' selected'; ?>><?php _e('Published Date', 'upw'); ?></option>
                <option value="title" <?php if( $orderby == 'title') echo ' selected'; ?>><?php _e('Title', 'upw'); ?></option>
                <option value="meta_value" <?php if( $orderby == 'meta_value') echo ' selected'; ?>><?php _e('Custom Field', 'upw'); ?></option>
                <option value="menu_order" <?php if( $orderby == 'menu_order') echo ' selected'; ?>><?php _e('Menu Order', 'upw'); ?></option>
            </select>
        </p>

        <p<?php if ($orderby !== 'meta_value') echo ' style="display:none;"'; ?>>
          <label for="<?php echo $this->get_field_id( 'meta_key' ); ?>"><?php _e('Custom field', 'upw'); ?>:</label>
          <input class="widefat meta_key" id="<?php echo $this->get_field_id('meta_key'); ?>" name="<?php echo $this->get_field_name('meta_key'); ?>" type="text" value="<?php echo $meta_key; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'upw'); ?>:</label>
          <select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" class="widefat">
            <option value="DESC"<?php if( $order == 'DESC') echo ' selected'; ?>><?php _e('Descending', 'upw'); ?></option>
            <option value="ASC"<?php if( $order == 'ASC') echo ' selected'; ?>><?php _e('Ascending', 'upw'); ?></option>
          </select>
        </p>

      </div>

      <?php if ( $instance ) { 
          
      ?>

        <script>

            jQuery(document).ready(function ($) {
              if (typeof (window.upwAdmin) !== undefined)
                  window.upwAdmin();

            var show_excerpt = $("#<?php echo $this->get_field_id( 'show_excerpt' ); ?>");
            var show_content = $("#<?php echo $this->get_field_id( 'show_content' ); ?>");
            var show_readmore = $("#<?php echo $this->get_field_id( 'show_readmore' ); ?>");
            var show_readmore_wrap = $("#<?php echo $this->get_field_id( 'show_readmore' ); ?>").parents('p');
            var show_thumbnail = $("#<?php echo $this->get_field_id( 'show_thumbnail' ); ?>");
            var show_date = $("#<?php echo $this->get_field_id( 'show_date' ); ?>");
            
            var excerpt_length = $("#<?php echo $this->get_field_id( 'excerpt_length' ); ?>").parents('p');
            var excerpt_readmore_wrap = $("#<?php echo $this->get_field_id( 'excerpt_readmore' ); ?>").parents('p');
                var thumb_size_wrap = $("#<?php echo $this->get_field_id( 'thumb_size' ); ?>").parents('p');

            var order = $(".orderby");
            var meta_key_wrap = $(".meta_key").parents('p');

            var toggleReadmore = function() {
              if (show_excerpt.is(':checked') || show_content.is(':checked')) {
                show_readmore_wrap.show('fast');
              } else {
                show_readmore_wrap.hide('fast');
              }
              toggleExcerptReadmore();
            }

            var toggleExcerptReadmore = function() {
              if ((show_excerpt.is(':checked') || show_content.is(':checked')) && show_readmore.is(':checked')) {
                excerpt_readmore_wrap.show('fast');
              } else {
                excerpt_readmore_wrap.hide('fast');
              }
            }

            // Toggle read more option
            show_excerpt.click(function() {
              toggleReadmore();
            });

            // Toggle read more option
            show_content.click(function() {
              toggleReadmore();
            });

            // Toggle excerpt length on click
            show_excerpt.click(function(){
              excerpt_length.toggle('fast');
            });

            // Toggle excerpt length on click
            show_readmore.click(function(){
              toggleExcerptReadmore();
            });


            // Toggle excerpt length on click
            show_thumbnail.click(function(){
              thumb_size_wrap.toggle('fast');
            });

                // Show or hide custom field meta_key value on order change
            order.change(function () {

              if ($(this).val() === 'meta_value') {
                meta_key_wrap.show('fast');
              } else {
                meta_key_wrap.hide('fast');
              }
            });


          });

        </script>

      <?php

            }


?>