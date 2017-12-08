<?php
/**
 * Template Name: About eboy_v3 Template
 */
?>

<div class="container">
  <div class="row">
    <div class="col-3">
<?= get_post_field('post_title', $post->ID) ?>
</div>
    <div class="col-9">
<?= get_post_field('post_content', $post->ID) ?>
</div>
</div>
<hr class="style1">
<div class="row">


    <?php
    $experience_title = get_post_meta($post->ID, 'Experience', false);
    if( count( $experience_title ) != 0 ) { ?>
      <div class="col-3">
    Experience
    </div>
    <div class="col-9 experience-content">

    <?php foreach($experience_title as $experience_title) {
            echo '<div class="row">'.$experience_title.'</div>';
            }
            ?>

    </div>
    <?php
    } else {
    // do nothing;
    }
    ?>

</div>


</div>
