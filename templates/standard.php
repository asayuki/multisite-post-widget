<?php
/**
 * Standard ultimate posts widget template
 *
 * @version     2.0.0
 */
?>
<?php if ($instance['before_posts']) { ?>
<div class="multisite-before">
  <?php echo wpautop($instance['before_posts']); ?>
</div>
<?php } ?>

<div class="multisite-posts">
  <?php foreach($filteredposts as $current_post) { ?>
  <article <?php echo implode(' ', $current_post->post_class) ?>>
    <h4 class="entry-title">
      <a href="<?php echo $current_post->permalink; ?>" rel="bookmark">
        <?php echo $current_post->post_title ?>
      </a>
      <p>
        <?php echo $current_post->post_excerpt ?>
      </p>
    </h4>
  </article>
  <?php } ?>
</div>

<?php if ($instance['after_posts']) { ?>
<div class="multisite-after">
  <?php echo wpautop($instance['after_posts']); ?>
</div>
<?php } ?>
