<?php

if( ! class_exists( 'TIE_FLICKR_PHOTOS' ) ) {

	/**
	 * Widget API: TIE_FLICKR_PHOTOS class
	 */
	 class TIE_FLICKR_PHOTOS extends WP_Widget {


		public function __construct(){
			$widget_ops = array( 'classname' => 'flickr-widget' );
			parent::__construct( 'flickr_photos-widget', apply_filters( 'TieLabs/theme_name', 'TieLabs' ) .' - '.esc_html__( 'Flickr', TIELABS_TEXTDOMAIN), $widget_ops );
		}

		/**
		 * Outputs the content for the widget instance.
		 */
		public function widget( $args, $instance ){

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			echo ( $args['before_widget'] );

			if ( ! empty($instance['title']) ){
				echo ( $args['before_title'] . $instance['title'] . $args['after_title'] );
			}

			if( ! empty( $instance['flickr_id'] ) ) {
	
				$transient_key = 'tie_flickr_cache_' . $instance['flickr_id'];
				$flickr_data   = get_transient( $transient_key );

				if ( empty( $flickr_data ) ) {
					$flickr_data = array();
					$rss = 'https://api.flickr.com/services/feeds/photos_public.gne?id='. $instance['flickr_id'] .'&lang=en-us&format=rss_200';
					$rss = fetch_feed( $rss );
			
					if ( ! is_wp_error( $rss ) ) {
						$maxitems  = $rss->get_item_quantity( 20 ); // 20 is the max in the RSS response 
						$rss_items = $rss->get_items( 0, $maxitems );

						foreach ( $rss_items as $item ) {
							$temp = array();
							$temp['url'] = esc_url( $item->get_permalink() );
							$temp['title'] = esc_html( $item->get_title() );
							$content =  $item->get_content();
							preg_match_all( "/<IMG.+?SRC=[\"']([^\"']+)/si", $content, $sub, PREG_SET_ORDER );
							$photo_url = $sub[0][1]; //str_replace( '_m.jpg', '_t.jpg', $sub[0][1] );
							$temp['src'] = esc_url( $photo_url );
							$flickr_data[] = $temp;
						}
			
						set_transient( $transient_key, $flickr_data, 60 * 60 * 24 );
					}
				}
		
				if( ! empty( $flickr_data ) ){

					$no_of_photos   = ! empty( $instance['no_of_photos'] )   ? $instance['no_of_photos']   : 6;
					$flickr_display = ! empty( $instance['flickr_display'] ) ? $instance['flickr_display'] : 'latest';
		
					if( $flickr_display != 'latest' ){
						shuffle( $flickr_data );
					}

					$flickr_data = array_slice( $flickr_data, 0, $no_of_photos );
					?>
					<div class="flickr-images-wrapper">
						<?php foreach( $flickr_data as $single ){ ?>
							<a class="flickr_badge_image" href="<?php echo esc_attr( $single['url'] ) ?>" title="<?php echo esc_attr( $single['title'] ) ?>" target="_blank" rel="nofollow noopener">
								<img src="<?php echo esc_attr( $single['src'] ) ?>" alt="<?php echo esc_attr( $single['title'] ) ?>" width="120" height="120" loading="lazy" />
							</a>
						<?php } ?>
						<div class="clearfix"></div>
					</div><!-- .flickr-images-wrapper -->
					<a target="_blank" rel="nofollow noopener" href="https://www.flickr.com/photos/<?php echo esc_attr( $instance['flickr_id'] )?>/" class="button dark-btn fullwidth"><?php esc_html_e( 'Follow us on Flickr', TIELABS_TEXTDOMAIN ) ?></a>
					<?php
				}
			}

			echo ( $args['after_widget'] );
		}

		/**
		 * Handles updating settings for widget instance.
		 */
		public function update( $new_instance, $old_instance ){
			$instance                   = $old_instance;
			$instance['title']          = sanitize_text_field( $new_instance['title'] );
			$instance['flickr_id']      = $new_instance['flickr_id'];
			$instance['no_of_photos']   = $new_instance['no_of_photos'];
			$instance['flickr_display'] = $new_instance['flickr_display'];
			return $instance;
		}

		/**
		 * Outputs the settings form for the widget.
		 */
		public function form( $instance ){
			$defaults = array( 'title' =>esc_html__( 'Flickr', TIELABS_TEXTDOMAIN), 'no_of_photos' => 6, 'flickr_display' => 'latest' );
			$instance = wp_parse_args( (array) $instance, $defaults );

			$title          = isset( $instance['title'] )          ? $instance['title']          : '';
			$flickr_id      = isset( $instance['flickr_id'] )      ? $instance['flickr_id']      : '';
			$no_of_photos   = isset( $instance['no_of_photos'] )   ? $instance['no_of_photos']   : 6;
			$flickr_display = isset( $instance['flickr_display'] ) ? $instance['flickr_display'] : 'latest';

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', TIELABS_TEXTDOMAIN) ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat" type="text" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'flickr_id' ) ); ?>"><?php esc_html_e( 'Flickr ID:', TIELABS_TEXTDOMAIN) ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'flickr_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickr_id' ) ); ?>" value="<?php echo esc_attr( $flickr_id ) ?>" class="widefat" type="text" />
				<small><a href="<?php echo esc_url( 'http://www.idgettr.com' ); ?>" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'Find your ID at idGettr', TIELABS_TEXTDOMAIN ); ?></a></small>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'flickr_display' ) ); ?>"><?php esc_html_e( 'Photos Order:', TIELABS_TEXTDOMAIN) ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'flickr_display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickr_display' ) ); ?>" class="widefat">
					<option value="latest" <?php selected( $flickr_display, 'latest' ); ?>><?php esc_html_e( 'Most recent', TIELABS_TEXTDOMAIN) ?></option>
					<option value="random" <?php selected( $flickr_display, 'random' ); ?>><?php esc_html_e( 'Random', TIELABS_TEXTDOMAIN) ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'no_of_photos' ) ); ?>"><?php esc_html_e( 'Number of photos to show:', TIELABS_TEXTDOMAIN) ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'no_of_photos' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'no_of_photos' ) ); ?>" value="<?php echo esc_attr( $no_of_photos ) ?>" type="number" step="1" min="1" size="3" class="tiny-text" />
				<small><?php esc_html_e( 'Max: 20', TIELABS_TEXTDOMAIN ); ?></small>
			</p>

		<?php
		}
	}



	/**
	 * Register the widget.
	 */
	add_action( 'widgets_init', 'tie_flickr_photos_register' );
	function tie_flickr_photos_register(){
		register_widget( 'TIE_FLICKR_PHOTOS' );
	}

}