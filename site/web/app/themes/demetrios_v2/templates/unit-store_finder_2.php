<?php $fields = get_fields();

if( $fields ): ?>
	<ul>
		<?php foreach( $fields as $name => $value ): ?>
			<li><b><?php echo $name; ?></b> <?php echo $value; ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>




<div class="container">
           <?php echo eboywp_display( 'facet', 'location' ); ?>
           <div id="map" style="width:100%; height:400px"></div>
           <?php echo eboywp_display( 'template', 'stores_2' ); ?>
       </div>
