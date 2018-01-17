<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 
 Template Name: Contact Template 

 */

get_header();

	while ( have_posts() ) : the_post(); ?>

		<div id="page-header" class="inner">
			<div class="page-header-wrap">
				<div class="page-header-left">
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</div>

				<div class="page-header-right">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
					<span>/</span>
					<?php the_title(); ?>
				</div>
			</div>
		</div>

		<div id="primary" class="content-area page-content">
			<div class="inner">

				<div class="contact-wrap">
					<div class="contact-left">
						<form action="#">
							<fieldset>
								<label>Name <span>*</span></label>
								<input type="text" name="name" id="name" />
							</fieldset>

							<fieldset>
								<label>Email Address <span>*</span></label>
								<input type="email" name="email" id="email" />
							</fieldset>

							<fieldset>
								<label>Subject</label>
								<input type="text" name="subject" id="subject" />
							</fieldset>

							<fieldset>
								<label>Message</label>
								<textarea name="message" id="message"></textarea>
							</fieldset>

							<fieldset>
								<input type="submit" name="submit" value="Submit" />
							</fieldset>
						</form>
					</div>

					<div class="contact-right">
						<div class="cwrap phone">
							<span class="icon"><i class="fa fa-phone" aria-hidden="true"></i></span>
							<span class="small">Phone</span>
							<span class="detail"><?php echo get_theme_mod('crispshop_phone_number', ''); ?></span>
						</div>

						<div class="cwrap fax">
							<span class="icon"><i class="fa fa-fax" aria-hidden="true"></i></span>
							<span class="small">Fax</span>
							<span class="detail"><?php echo get_theme_mod('crispshop_contact_fax', ''); ?></span>
						</div>

						<div class="cwrap email">
							<span class="icon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
							<span class="small">Email</span>
							<span class="detail"><a href="<?php echo get_theme_mod('crispshop_contact_email', ''); ?>"><?php echo get_theme_mod('crispshop_contact_email', ''); ?></a></span>
						</div>

						<div class="cwrap address">
							<span class="icon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
							<span class="small">Address</span>
							<span class="detail"><?php echo get_theme_mod('crispshop_contact_address', ''); ?></span>
						</div>
					</div>

					<div class="clear"></div>
				</div>

				<?php $crispshop_contact_map = get_theme_mod('crispshop_contact_map', '');
				if (!$crispshop_contact_map) { ?>
				<div class="contact-map">
					<div id="map"></div>
					<?php $address = get_theme_mod('crispshop_contact_address', '');
					$geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');
					$geo = json_decode($geo, true);

					if ($geo['status'] == 'OK') {
						$latitude = $geo['results'][0]['geometry']['location']['lat'];
						$longitude = $geo['results'][0]['geometry']['location']['lng'];
					}

					$crispshop_base_color = get_theme_mod('crispshop_base_color', '#ea3a3c'); ?>

					<script type="text/javascript">
					var map;
					function initMap() {
						var setLatLng = {lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?>};

						var map = new google.maps.Map(document.getElementById('map'), {
							zoom: 16,
							center: setLatLng,
							scrollwheel: false,
						});

						var icon = {
							path: "M16.734,0C9.375,0,3.408,5.966,3.408,13.325c0,11.076,13.326,20.143,13.326,20.143S30.06,23.734,30.06,13.324   C30.06,5.965,24.093,0,16.734,0z M16.734,19.676c-3.51,0-6.354-2.844-6.354-6.352c0-3.508,2.844-6.352,6.354-6.352   c3.508-0.001,6.352,2.845,6.352,6.353C23.085,16.833,20.242,19.676,16.734,19.676z",
						    fillColor: '<?php echo $crispshop_base_color; ?>',
						    fillOpacity: 1,
						    anchor: new google.maps.Point(0,0),
						    strokeWeight: 0,
						    scale: 1.2
						}

						var marker = new google.maps.Marker({
							position: setLatLng,
							map: map,
							icon: icon
						});
					}
					</script>
					<script src="//maps.googleapis.com/maps/api/js?v=3&key=AIzaSyDKDs2YOZC3MIALMMAttxaplWiu1IQlbbs&callback=initMap"></script>
				</div>
				<?php } ?>

			</div><!-- #main -->
		</div><!-- #primary -->

	<?php endwhile; ?>

<?php get_footer();
