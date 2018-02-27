<?php $fields = get_field_objects();?>
<div class="row">

  <?php if( $fields ): ?>

  	<?php foreach( $fields as $field ): ?>

  		<?php if( $field['value'] ): ?>

        <div class="col-4 py-2 px-0">
        <h6><?php echo $field['label']; ?>:</h6>
        </div>

        <div class="col-8 p-1">
        <div class="progress">

          <div class="progress-bar bg-info" role="progressbar" aria-valuenow="<?php echo $field['value']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $field['value']; ?></div>
      </div>
        </div>
      <?php endif; ?>

  	<?php endforeach; ?>

  <?php endif; ?>
</div>
