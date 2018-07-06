<?php
  $content = get_sub_field('box_content_left');
  $color = get_sub_field('box_color_left');
  ?>
<div class="box <?php echo $color; ?> p-5 wow bounceInUp">
  <?php echo $content; ?>
</div>
