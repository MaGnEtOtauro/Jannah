<?php
/**
 * Featured Area - Custom Game Details Layout
 *
 * @author   TieLabs (Modified)
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

$post_format = tie_get_postdata( 'tie_post_head', 'standard' );

// Only customize standard post format
if( $post_format == 'standard' ) {
	
	// Get game details
	$poster_image_id = get_post_meta( get_the_ID(), '_game_poster_image_id', true );
	$poster_image_url = $poster_image_id ? wp_get_attachment_url( $poster_image_id ) : '';
	$cracked_by = get_post_meta( get_the_ID(), '_game_cracked_by', true );
	$sourced_from = get_post_meta( get_the_ID(), '_game_sourced_from', true );
	$developer = get_post_meta( get_the_ID(), '_game_developer', true );
	$version = get_post_meta( get_the_ID(), '_game_version', true );
	$file_size = get_post_meta( get_the_ID(), '_game_file_size', true );
	
	// Check if we have game details to display
	if( $poster_image_url || $cracked_by || $sourced_from || $developer || $version || $file_size ) {
		?>
		<div class="featured-area game-details-featured">
			<div class="featured-area-inner">
				<div class="game-details-wrapper container-wrapper">
					<div class="game-poster-column">
						<?php if( $poster_image_url ): ?>
							<div class="game-poster portrait-poster">
								<img src="<?php echo esc_url( $poster_image_url ); ?>" alt="<?php the_title_attribute(); ?> Poster">
							</div>
						<?php elseif( has_post_thumbnail() ): ?>
							<div class="game-poster portrait-poster">
								<?php the_post_thumbnail( 'full' ); ?>
							</div>
						<?php endif; ?>
					</div>
					
					<div class="game-info-column">
						<div class="game-details-list">
							<?php if( $cracked_by ): ?>
								<div class="game-detail-item">
									<span class="detail-label"><i class="fa fa-unlock" aria-hidden="true"></i> Cracked By:</span>
									<span class="detail-value"><?php echo esc_html( $cracked_by ); ?></span>
								</div>
							<?php endif; ?>
							
							<?php if( $sourced_from ): ?>
								<div class="game-detail-item">
									<span class="detail-label"><i class="fa fa-cube" aria-hidden="true"></i> Sourced From:</span>
									<span class="detail-value"><?php echo esc_html( $sourced_from ); ?></span>
								</div>
							<?php endif; ?>
							
							<?php if( $developer ): ?>
								<div class="game-detail-item">
									<span class="detail-label"><i class="fa fa-code" aria-hidden="true"></i> Developer:</span>
									<span class="detail-value"><?php echo esc_html( $developer ); ?></span>
								</div>
							<?php endif; ?>
							
							<?php if( $version ): ?>
								<div class="game-detail-item">
									<span class="detail-label"><i class="fa fa-tag" aria-hidden="true"></i> Version:</span>
									<span class="detail-value"><?php echo esc_html( $version ); ?></span>
								</div>
							<?php endif; ?>
							
							<?php if( $file_size ): ?>
								<div class="game-detail-item">
									<span class="detail-label"><i class="fa fa-hdd-o" aria-hidden="true"></i> File Size:</span>
									<span class="detail-value"><?php echo esc_html( $file_size ); ?></span>
								</div>
							<?php endif; ?>
						</div>
						
						<div class="game-details-badges">
							<span class="badge secure"><i class="fa fa-shield" aria-hidden="true"></i> Virus Free</span>
							<span class="badge tested"><i class="fa fa-check-circle" aria-hidden="true"></i> Tested & Working</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		return; // Don't show the default featured image
	}
}

// If not a standard post or no game details, use the parent theme's featured.php
include get_template_directory() . '/templates/single-post/featured.php';