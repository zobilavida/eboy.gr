<script id="wiloke-list-feature-item" type="text/html">
	<div class="row">
		<div class="col-sm-8">
			<div class="addlisting-popup__field">
				<input type="text" name="name" value="<%= name %>">
			</div>
		</div>
		<div class="col-sm-12">
			<div class="addlisting-popup__field">
				<label>
					<input id="feature-unavailable" <% if (unavailable=='yes'){ %>checked <% } %> data-test="<%= unavailable %>" type="checkbox" name="unavailable" value="1">
					<?php esc_html_e('Unavailable', 'wiloke'); ?>
				</label>
			</div>
		</div>
	</div>
</script>