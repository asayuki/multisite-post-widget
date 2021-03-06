<?php


    global $post;
    if($post == null)
        return;
    $current_post_id =  $post->ID;

    $cache = wp_cache_get( 'widget_multisite_posts', 'widget' );

    if ( !is_array( $cache ) )
        $cache = array();

    if ( isset( $cache[$args['widget_id']] ) ) {
        echo $cache[$args['widget_id']];
        return;
    }

    //ob_start();
    extract( $args );

    $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
    $title_link = $instance['title_link'];
    $number = empty($instance['number']) ? -1 : $instance['number'];
    $types = 'any';

    $hidemeta = $instance['hidemeta'];

    if(!empty($instance['types']))
    {
        $types = $instance['types'];
        if(!is_array($types))
            $types =  explode(',', $types);
    }

    $cats = '';
    if(!empty($instance['cats']))
    {
        $cats =  $instance['cats'];
        if(!is_array($cats))
        {
            $cats = explode(',', $cats);
        }
    }
    $tags = '';
    if(!empty($instance['tags']))
    {
        $tags = $instance['tags'];
        if(!is_array($tags))
            $tags = explode(',', $tags);
    }
    if (is_multisite()):
    $blogs = array();
    if(!empty($instance['blogs']))
    {
        $blogs = $instance['blogs'];
        if(!is_array($blogs))
            $blogs = explode(',', $blogs);
    }
  endif;
    $atcat = $instance['atcat'] ? true : false;
    $attag = $instance['attag'] ? true : false;
    $cssClass = $instance['class'];
    $before_posts = $instance['before_posts'];
    $after_posts = $instance['after_posts'];

    // If $atcat true and in category
    if ($atcat && is_category()) {
        $cats = get_query_var('cat');
    }

    // If $atcat true and is single post
    if ($atcat && is_single()) {
        $cats = '';
        foreach (get_the_category() as $catt) {
            $cats .= $catt->term_id.' ';
        }
        $cats = str_replace(' ', ',', trim($cats));
    }

    // If $attag true and in tag
    if ($attag && is_tag()) {
        $tags = get_query_var('tag_id');
    }

    // If $attag true and is single post
    if ($attag && is_single()) {
        $tags = '';
        $thetags = get_the_tags();
        if ($thetags) {
            foreach ($thetags as $tagg) {
                $tags .= $tagg->term_id . ' ';
            }
        }
        $tags = str_replace(' ', ',', trim($tags));
    }

    // Excerpt more filter
    $new_excerpt_more = create_function('$more', 'return "...";');
    add_filter('excerpt_more', $new_excerpt_more);

    // Excerpt length filter
    $orderby = $instance['orderby'];
    $args = array(
      'posts_per_page' => $number,
      'category_slug__in' => $cats,
      'tag_slug__in' => $tags,
      'post_type' => $types
    );

    if ($orderby === 'meta_value') {
      $args['orderby'] = $orderby;
      $args['order'] = $instance['order'];
      $args['meta_key'] = $instance['meta_key'];
    }

    if (!empty($sticky_query)) {
        $args[key($sticky_query)] = reset($sticky_query);
    }

    $args = apply_filters('upw_wp_query_args', $args, $instance, $this->id_base);

    $filteredposts = array();

    if (is_multisite()):
      $currentblog = get_current_site();
      foreach ($blogs as $blog) {
          switch_to_blog( $blog );
          /* Get all categories */
          $categoriesArr = array();
          if (is_array($args['category_slug__in'])) {
            foreach ($args['category_slug__in'] as $slugg) {
              $sluggy = get_category_by_slug($slugg);
              array_push($categoriesArr, $sluggy->cat_ID);
            }
          }
          $args['category__in'] = $categoriesArr;
          /* End Get all categories*/

          /* Get all tags */
          $tagsArr = array();
          if (is_array($args['tag_slug__in'])) {
            foreach ($args['tag_slug__in'] as $slugg) {
              $sluggy = get_term_by('slug', $slugg, 'post_tag');
              array_push($tagsArr, $sluggy->term_id);
            }
          }
          $args['tag__in'] = $tagsArr;
          /* End Get all tags */

          $query = new WP_Query($args);

          while ( $query->have_posts() ) {
              $query->the_post();
              $post->blog = $blog;
              $post->post_class = get_post_class();
              $post->permalink = get_permalink();
              $post->meta_sort_value = '';
              $thumb_id = get_post_thumbnail_id();
              $post->featuredimage = null;

              /* Excerpts */
              $excerpt = get_the_excerpt();
              $excerptText = '';
              if (mb_strlen($excerpt) > 300) {
                $subex = mb_substr($excerpt, 0, 300);
                $exwords = explode(' ', $subex);
                $excut = -(mb_strlen($exwords[count($exwords)-1]));
                if ($excut < 0) {
                  $excerptText = mb_substr($subex, 0, $excut);
                } else {
                  $excerptText = $subex;
                }
                $excerptText .= '...';
              } else {
                $excerptText = $excerpt;
              }
              $post->excerpt = $excerptText;
              /* End of Exerpts*/

              if(!empty($thumb_id))
              {
                  $post->featuredimage = wp_get_attachment_metadata($thumb_id);
                  $post->featuredimage['uploadsbase'] = wp_upload_dir();

              }

              if($orderby == 'meta_value')
              {
                  $post->meta_sort_value = get_post_meta($post->ID, $instance['meta_key'], true);
              }
              $filteredposts[] = $post;
          }
          wp_reset_postdata();
          wp_reset_query();
      }
      switch_to_blog( $this->currentblogid );
    else:
      $categoriesArr = array();
      if (is_array($args['category_slug__in'])) {
        foreach ($args['category_slug__in'] as $slugg) {
          $sluggy = get_category_by_slug($slugg);
          array_push($categoriesArr, $sluggy->cat_ID);
        }
      }
      $args['category__in'] = $categoriesArr;
      /* End Get all categories*/

      /* Get all tags */
      $tagsArr = array();
      if (is_array($args['tag_slug__in'])) {
        foreach ($args['tag_slug__in'] as $slugg) {
          $sluggy = get_term_by('slug', $slugg, 'post_tag');
          array_push($tagsArr, $sluggy->term_id);
        }
      }
      $args['tag__in'] = $tagsArr;
      /* End Get all tags */

      $query = new WP_Query($args);
      while ( $query->have_posts() ) {
          $query->the_post();
          //$post->blog = $blog;
          $post->post_class = get_post_class();
          $post->permalink = get_permalink();
          $post->meta_sort_value = '';
          $thumb_id = get_post_thumbnail_id();
          $post->featuredimage = null;

          /* Excerpts */
          $excerpt = get_the_excerpt();
          $excerptText = '';
          if (mb_strlen($excerpt) > 300) {
            $subex = mb_substr($excerpt, 0, 300);
            $exwords = explode(' ', $subex);
            $excut = -(mb_strlen($exwords[count($exwords)-1]));
            if ($excut < 0) {
              $excerptText = mb_substr($subex, 0, $excut);
            } else {
              $excerptText = $subex;
            }
            $excerptText .= '...';
          } else {
            $excerptText = $excerpt;
          }
          $post->excerpt = $excerptText;
          /* End of Exerpts*/

          if(!empty($thumb_id))
          {
              $post->featuredimage = wp_get_attachment_metadata($thumb_id);
              $post->featuredimage['uploadsbase'] = wp_upload_dir();

          }

          if($orderby == 'meta_value')
          {
              $post->meta_sort_value = get_post_meta($post->ID, $instance['meta_key'], true);
          }
          $filteredposts[] = $post;
      }
      wp_reset_postdata();
      wp_reset_query();
    endif;

    $filteredposts = $this->sort($filteredposts);
    $filteredposts = array_slice($filteredposts, 0, $number);

    if ($this->endsWith($instance['template'], '.php'))
    {
        $templatePath = $this->get_template_directory().DIRECTORY_SEPARATOR.$instance['template'];

        if(file_exists($templatePath))
            include $templatePath;

    }
    else
    {
        include plugin_dir_path( dirname( __FILE__ ) ) . 'templates/standard.php';

    }

    // Reset the global $the_post as this query will have stomped on it
    wp_reset_postdata();


    if ($cache) {
        $cache[$args['widget_id']] = ob_get_flush();
    }
    wp_cache_set( 'widget_multisite_posts', $cache, 'widget' );

?>
