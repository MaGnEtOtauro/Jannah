<?php

add_action( 'wp_enqueue_scripts', 'tie_theme_child_styles_scripts', 80 );
function tie_theme_child_styles_scripts() {

	/* Load the RTL.css file of the parent theme */
	if ( is_rtl() ) {
		wp_enqueue_style( 'tie-theme-rtl-css', get_template_directory_uri().'/rtl.css', '' );
	}

	/* THIS WILL ALLOW ADDING CUSTOM CSS TO THE style.css */
	wp_enqueue_style( 'tie-theme-child-css', get_stylesheet_directory_uri().'/style.css', '', '1.0.1' );

}

// Force refresh admin scripts
add_action( 'admin_enqueue_scripts', 'force_admin_refresh' );
function force_admin_refresh() {
	wp_enqueue_script( 'jquery' );
}

// Add smooth scroll functionality for download button
add_action( 'wp_footer', 'add_smooth_scroll_script' );
function add_smooth_scroll_script() {
	?>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Smooth scroll for download button
		const downloadBtn = document.querySelector('.quick-download-btn[data-smooth-scroll="true"]');
		if (downloadBtn) {
			downloadBtn.addEventListener('click', function(e) {
				e.preventDefault();
				const targetId = this.getAttribute('href').substring(1);
				const targetElement = document.getElementById(targetId);
				
				if (targetElement) {
					// Calculate offset for fixed headers
					const offset = 20; // Adjust this value if needed
					const elementPosition = targetElement.getBoundingClientRect().top;
					const offsetPosition = elementPosition + window.pageYOffset - offset;
					
					window.scrollTo({
						top: offsetPosition,
						behavior: 'smooth'
					});
					
					// Optional: Add a subtle flash effect to the target section
					targetElement.style.transition = 'background-color 0.3s ease';
					const originalBg = targetElement.style.backgroundColor;
					targetElement.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
					
					setTimeout(function() {
						targetElement.style.backgroundColor = originalBg;
					}, 500);
				}
			});
		}
	});
	</script>
	<?php
}

// Debug: Add admin notice to confirm child theme is active
add_action( 'admin_notices', 'child_theme_debug_notice' );
function child_theme_debug_notice() {
	if ( get_current_screen()->id === 'post' ) {
		echo '<div class="notice notice-success"><p>Jannah Child Theme is Active - Game Details Meta Box Loaded</p></div>';
	}
}

// =====================================================
// ELEGANT DOWNLOAD SECTION FUNCTIONALITY
// =====================================================

// Add Download Section Meta Box
add_action( 'add_meta_boxes', 'add_download_section_meta_box' );
function add_download_section_meta_box() {
	add_meta_box(
		'download_section_meta_box',
		'🎮 Download Section',
		'download_section_meta_box_callback',
		'post',
		'normal',
		'high'
	);
}

// Meta Box Callback Function
function download_section_meta_box_callback( $post ) {
	// Add nonce for security
	wp_nonce_field( 'download_section_meta_box', 'download_section_meta_box_nonce' );
	
	// Get existing values
	$download_buttons = get_post_meta( $post->ID, '_download_buttons', true );
	if ( ! is_array( $download_buttons ) || empty( $download_buttons ) ) {
		$download_buttons = array( array( 'title' => '', 'url' => '', 'type' => 'download' ) );
	}
	
	?>
	<div id="download-section-container">
		<style>
		#download-section-container {
			padding: 20px;
			background: #f9f9f9;
			border-radius: 8px;
			margin: 10px 0;
		}
		.download-button-group {
			background: white;
			border: 1px solid #ddd;
			border-radius: 6px;
			padding: 15px;
			margin: 10px 0;
			position: relative;
		}
		.download-button-group:hover {
			border-color: #0073aa;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		}
		.download-field-row {
			display: flex;
			gap: 15px;
			align-items: center;
			margin-bottom: 10px;
		}
		.download-field {
			flex: 1;
		}
		.download-field label {
			display: block;
			font-weight: 600;
			margin-bottom: 5px;
			color: #333;
		}
		.download-field input, .download-field select {
			width: 100%;
			padding: 8px 12px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 14px;
		}
		.download-field input:focus, .download-field select:focus {
			border-color: #0073aa;
			outline: none;
			box-shadow: 0 0 3px rgba(0,115,170,0.3);
		}
		.button-type-select {
			min-width: 150px;
		}
		.remove-button {
			position: absolute;
			top: 10px;
			right: 10px;
			background: #dc3232;
			color: white;
			border: none;
			border-radius: 3px;
			padding: 5px 10px;
			cursor: pointer;
			font-size: 12px;
		}
		.remove-button:hover {
			background: #a00;
		}
		.add-download-button {
			background: #0073aa;
			color: white;
			border: none;
			border-radius: 4px;
			padding: 10px 20px;
			cursor: pointer;
			font-size: 14px;
			margin-top: 15px;
		}
		.add-download-button:hover {
			background: #005a87;
		}
		.button-preview {
			margin-top: 10px;
			padding: 8px 16px;
			border-radius: 6px;
			text-decoration: none;
			display: inline-block;
			font-weight: 600;
			text-align: center;
			min-width: 120px;
			transition: all 0.3s ease;
		}
		.button-preview.download { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
		.button-preview.update { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
		.button-preview.dlc { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
		.button-preview.custom { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
		.download-instructions {
			background: #e7f3ff;
			border: 1px solid #b3d9ff;
			border-radius: 4px;
			padding: 15px;
			margin-bottom: 20px;
		}
		.download-instructions h4 {
			margin: 0 0 10px 0;
			color: #0073aa;
		}
		.download-instructions ul {
			margin: 0;
			padding-left: 20px;
		}
		</style>
		
		<div class="download-instructions">
			<h4>📥 Download Section Instructions:</h4>
			<ul>
				<li><strong>Main Download:</strong> Primary game download link</li>
				<li><strong>Update:</strong> Game updates, patches, or new versions</li>
				<li><strong>DLC:</strong> Downloadable content, expansions, or add-ons</li>
				<li><strong>Custom:</strong> Any other type of download (mods, tools, etc.)</li>
			</ul>
		</div>
		
		<div id="download-buttons-container">
			<?php foreach ( $download_buttons as $index => $button ): ?>
				<div class="download-button-group" data-index="<?php echo $index; ?>">
					<?php if ( count( $download_buttons ) > 1 ): ?>
						<button type="button" class="remove-button" onclick="removeDownloadButton(this)">✕ Remove</button>
					<?php endif; ?>
					
					<div class="download-field-row">
						<div class="download-field">
							<label>Button Title</label>
							<input type="text" 
								   name="download_buttons[<?php echo $index; ?>][title]" 
								   value="<?php echo esc_attr( $button['title'] ); ?>" 
								   placeholder="e.g., Download Game, Get Update, Download DLC"
								   onchange="updateButtonPreview(this)" />
						</div>
						
						<div class="download-field button-type-select">
							<label>Button Type</label>
							<select name="download_buttons[<?php echo $index; ?>][type]" onchange="updateButtonPreview(this)">
								<option value="download" <?php selected( $button['type'], 'download' ); ?>>📥 Main Download</option>
								<option value="update" <?php selected( $button['type'], 'update' ); ?>>🔄 Update</option>
								<option value="dlc" <?php selected( $button['type'], 'dlc' ); ?>>🎯 DLC</option>
								<option value="custom" <?php selected( $button['type'], 'custom' ); ?>>⚡ Custom</option>
							</select>
						</div>
					</div>
					
					<div class="download-field">
						<label>Download URL</label>
						<input type="url" 
							   name="download_buttons[<?php echo $index; ?>][url]" 
							   value="<?php echo esc_url( $button['url'] ); ?>" 
							   placeholder="https://example.com/download-link" />
					</div>
					
					<div class="button-preview-container">
						<label>Preview:</label>
						<a href="#" class="button-preview <?php echo esc_attr( $button['type'] ); ?>" onclick="return false;">
							<?php echo $button['title'] ? esc_html( $button['title'] ) : 'Button Preview'; ?>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		
		<button type="button" class="add-download-button" onclick="addDownloadButton()">
			➕ Add Another Download Button
		</button>
	</div>
	
	<script>
	let downloadButtonIndex = <?php echo count( $download_buttons ); ?>;
	
	function addDownloadButton() {
		const container = document.getElementById('download-buttons-container');
		const newButtonHtml = `
			<div class="download-button-group" data-index="${downloadButtonIndex}">
				<button type="button" class="remove-button" onclick="removeDownloadButton(this)">✕ Remove</button>
				
				<div class="download-field-row">
					<div class="download-field">
						<label>Button Title</label>
						<input type="text" 
							   name="download_buttons[${downloadButtonIndex}][title]" 
							   value="" 
							   placeholder="e.g., Download Game, Get Update, Download DLC"
							   onchange="updateButtonPreview(this)" />
					</div>
					
					<div class="download-field button-type-select">
						<label>Button Type</label>
						<select name="download_buttons[${downloadButtonIndex}][type]" onchange="updateButtonPreview(this)">
							<option value="download">📥 Main Download</option>
							<option value="update">🔄 Update</option>
							<option value="dlc">🎯 DLC</option>
							<option value="custom">⚡ Custom</option>
						</select>
					</div>
				</div>
				
				<div class="download-field">
					<label>Download URL</label>
					<input type="url" 
						   name="download_buttons[${downloadButtonIndex}][url]" 
						   value="" 
						   placeholder="https://example.com/download-link" />
				</div>
				
				<div class="button-preview-container">
					<label>Preview:</label>
					<a href="#" class="button-preview download" onclick="return false;">
						Button Preview
					</a>
				</div>
			</div>
		`;
		
		container.insertAdjacentHTML('beforeend', newButtonHtml);
		downloadButtonIndex++;
		updateRemoveButtons();
	}
	
	function removeDownloadButton(button) {
		if (document.querySelectorAll('.download-button-group').length > 1) {
			button.closest('.download-button-group').remove();
			updateRemoveButtons();
		}
	}
	
	function updateRemoveButtons() {
		const buttons = document.querySelectorAll('.remove-button');
		const groups = document.querySelectorAll('.download-button-group');
		
		buttons.forEach(button => {
			if (groups.length <= 1) {
				button.style.display = 'none';
			} else {
				button.style.display = 'block';
			}
		});
	}
	
	function updateButtonPreview(input) {
		const group = input.closest('.download-button-group');
		const preview = group.querySelector('.button-preview');
		const titleInput = group.querySelector('input[name*="[title]"]');
		const typeSelect = group.querySelector('select[name*="[type]"]');
		
		const title = titleInput.value || 'Button Preview';
		const type = typeSelect.value || 'download';
		
		preview.textContent = title;
		preview.className = 'button-preview ' + type;
	}
	
	// Initialize remove button visibility
	document.addEventListener('DOMContentLoaded', function() {
		updateRemoveButtons();
	});
	</script>
	<?php
}

// Save Download Section Meta Box Data
add_action( 'save_post', 'save_download_section_meta_box_data' );
function save_download_section_meta_box_data( $post_id ) {
	// Check if nonce is valid
	if ( ! isset( $_POST['download_section_meta_box_nonce'] ) || 
		 ! wp_verify_nonce( $_POST['download_section_meta_box_nonce'], 'download_section_meta_box' ) ) {
		return;
	}
	
	// Check if user has permission to edit post
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	
	// Check if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	
	// Sanitize and save download buttons data
	if ( isset( $_POST['download_buttons'] ) && is_array( $_POST['download_buttons'] ) ) {
		$download_buttons = array();
		
		foreach ( $_POST['download_buttons'] as $button ) {
			if ( ! empty( $button['title'] ) && ! empty( $button['url'] ) ) {
				$download_buttons[] = array(
					'title' => sanitize_text_field( $button['title'] ),
					'url'   => esc_url_raw( $button['url'] ),
					'type'  => sanitize_text_field( $button['type'] )
				);
			}
		}
		
		update_post_meta( $post_id, '_download_buttons', $download_buttons );
	} else {
		delete_post_meta( $post_id, '_download_buttons' );
	}
}

// Display Download Section at Bottom of Posts
add_filter( 'the_content', 'add_download_section_to_content' );
function add_download_section_to_content( $content ) {
	// Only add to single posts, not pages or other post types
	if ( ! is_single() || ! is_main_query() ) {
		return $content;
	}
	
	// Get download buttons for current post
	$download_buttons = get_post_meta( get_the_ID(), '_download_buttons', true );
	
	// Get requirements data
	$minimum_requirements = get_post_meta( get_the_ID(), '_game_minimum_requirements', true );
	$recommended_requirements = get_post_meta( get_the_ID(), '_game_recommended_requirements', true );
	
	// If no download buttons and no requirements, return original content
	if ( ( empty( $download_buttons ) || ! is_array( $download_buttons ) ) && 
	     empty( $minimum_requirements ) && empty( $recommended_requirements ) ) {
		return $content;
	}
	
	// Generate download section HTML with requirements
	$download_section = generate_download_section_html( $download_buttons, get_the_ID() );
	
	// Append download section to content
	return $content . $download_section;
}

// Generate Download Section HTML
function generate_download_section_html( $download_buttons, $post_id = null ) {
	// Get requirements data
	$minimum_requirements = $post_id ? get_post_meta( $post_id, '_game_minimum_requirements', true ) : '';
	$recommended_requirements = $post_id ? get_post_meta( $post_id, '_game_recommended_requirements', true ) : '';
	
	ob_start();
	?>
	<?php if ( $minimum_requirements || $recommended_requirements ) : ?>
	<div class="container-wrapper game-requirements-section">
		<div class="requirements-display-wrapper">
			<?php if ( $minimum_requirements ) : ?>
			<div class="requirements-display-column">
				<div class="widget-title">
					<h4 class="requirements-title" style="font-size: 15px !important; white-space: nowrap !important; display: flex !important; align-items: center !important; gap: 6px !important; margin: 0 !important; font-weight: 700 !important;">
						<span class="tie-icon-desktop" aria-hidden="true"></span>
						Minimum System Requirements
					</h4>
				</div>
				<div class="requirements-content">
					<?php 
					$lines = explode("\n", trim($minimum_requirements));
					echo '<ul class="requirements-list" style="list-style: none !important; margin: 0 !important; padding: 0 !important;">';
					foreach ($lines as $line) {
						$line = trim($line);
						if (!empty($line)) {
							echo '<li>' . esc_html($line) . '</li>';
						}
					}
					echo '</ul>';
					?>
				</div>
			</div>
			<?php endif; ?>
			
			<?php if ( $recommended_requirements ) : ?>
			<div class="requirements-display-column">
				<div class="widget-title">
					<h4 class="requirements-title" style="font-size: 15px !important; white-space: nowrap !important; display: flex !important; align-items: center !important; gap: 6px !important; margin: 0 !important; font-weight: 700 !important;">
						<span class="tie-icon-cogs" aria-hidden="true"></span>
						Recommended System Requirements
					</h4>
				</div>
				<div class="requirements-content">
					<?php 
					$lines = explode("\n", trim($recommended_requirements));
					echo '<ul class="requirements-list" style="list-style: none !important; margin: 0 !important; padding: 0 !important;">';
					foreach ($lines as $line) {
						$line = trim($line);
						if (!empty($line)) {
							echo '<li>' . esc_html($line) . '</li>';
						}
					}
					echo '</ul>';
					?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	
	<?php if ( ! empty( $download_buttons ) && is_array( $download_buttons ) ) : ?>
	<div class="container-wrapper game-download-section" id="game-download-section">
		<div class="widget-title">
			<h4 class="main-title">
				<span class="tie-icon-download" aria-hidden="true"></span>
				Download Game
			</h4>
			<p class="the-subtitle">Get your copy now and start playing!</p>
		</div>
		
		<div class="download-buttons-list">
			<?php foreach ( $download_buttons as $button ): ?>
				<div class="download-item">
					<a href="<?php echo esc_url( $button['url'] ); ?>" 
					   class="download-link download-<?php echo esc_attr( $button['type'] ); ?>" 
					   target="_blank" 
					   rel="noopener noreferrer">
						<div class="download-icon">
							<?php echo get_download_button_icon( $button['type'] ); ?>
						</div>
						<div class="download-content">
							<span class="download-title"><?php echo esc_html( $button['title'] ); ?></span>
							<span class="download-subtitle"><?php echo get_download_button_subtitle( $button['type'] ); ?></span>
						</div>
						<span class="tie-icon-angle-right download-arrow" aria-hidden="true"></span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
		
		<div class="download-footer-info">
			<div class="download-meta">
				<span><span class="tie-icon-lock" aria-hidden="true"></span> Secure Download</span>
				<span><span class="tie-icon-shield" aria-hidden="true"></span> Virus Free</span>
				<span><span class="tie-icon-support" aria-hidden="true"></span> Full Support</span>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php
	return ob_get_clean();
}

// Get Download Button Icon
function get_download_button_icon( $type ) {
	$icons = array(
		'download' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16L7 11L8.4 9.6L11 12.2V4H13V12.2L15.6 9.6L17 11L12 16Z" fill="currentColor"/><path d="M20 18H4V20H20V18Z" fill="currentColor"/></svg>',
		'update'   => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4V1L8 5L12 9V6C15.31 6 18 8.69 18 12C18 13.01 17.75 13.97 17.3 14.8L18.76 16.26C19.54 15.03 20 13.57 20 12C20 7.58 16.42 4 12 4Z" fill="currentColor"/><path d="M12 18C8.69 18 6 15.31 6 12C6 10.99 6.25 10.03 6.7 9.2L5.24 7.74C4.46 8.97 4 10.43 4 12C4 16.42 7.58 20 12 20V23L16 19L12 15V18Z" fill="currentColor"/></svg>',
		'dlc'      => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z" fill="currentColor"/><path d="M19 15L19.31 16.32L21 16.5L19.31 16.68L19 18L18.69 16.68L17 16.5L18.69 16.32L19 15Z" fill="currentColor"/><path d="M5 15L5.31 16.32L7 16.5L5.31 16.68L5 18L4.69 16.68L3 16.5L4.69 16.32L5 15Z" fill="currentColor"/></svg>',
		'custom'   => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 3C9.23 3 6.19 5.95 6 9.66L4.8 12.6C4.3 13.84 5.18 15.14 6.5 15.14H9V19C9 20.1 9.9 21 11 21H13C14.1 21 15 20.1 15 19V15.14H17.5C18.82 15.14 19.7 13.84 19.2 12.6L18 9.66C17.81 5.95 14.77 3 13 3Z" fill="currentColor"/></svg>'
	);
	
	return isset( $icons[ $type ] ) ? $icons[ $type ] : $icons['download'];
}

// Get Download Button Subtitle
function get_download_button_subtitle( $type ) {
	$subtitles = array(
		'download' => 'Main Game File',
		'update'   => 'Latest Version',
		'dlc'      => 'Additional Content',
		'custom'   => 'Bonus Content'
	);
	
	return isset( $subtitles[ $type ] ) ? $subtitles[ $type ] : 'Download Now';
}

// =====================================================
// CHANGE READ MORE BUTTON TEXT TO DOWNLOAD
// =====================================================

// Filter to change "Read More" button text to "Download"
add_filter( 'TieLabs/more_link_button', 'change_read_more_to_download' );
function change_read_more_to_download( $button ) {
	// Replace "Read More »" with "Download"
	$button = str_replace( 'Read More &raquo;', 'Download', $button );
	return $button;
}

// =====================================================
// GAME DETAILS AND POSTER META BOXES
// =====================================================

// Add Game Details Meta Box
add_action( 'add_meta_boxes', 'add_game_details_meta_box' );
function add_game_details_meta_box() {
	add_meta_box(
		'game_details_meta_box',
		'🎮 Game Details & Poster',
		'game_details_meta_box_callback',
		'post',
		'normal',
		'high'
	);
}

// Add System Requirements and Installation Instructions Meta Box
add_action( 'add_meta_boxes', 'add_game_requirements_meta_box' );
function add_game_requirements_meta_box() {
	add_meta_box(
		'game_requirements_meta_box',
		'📋 System Requirements & Installation Instructions',
		'game_requirements_meta_box_callback',
		'post',
		'normal',
		'high'
	);
}

// Game Details Meta Box Callback
function game_details_meta_box_callback( $post ) {
	// Add nonce for security
	wp_nonce_field( 'game_details_meta_box', 'game_details_meta_box_nonce' );
	
	// Get existing values
	$poster_image_id = get_post_meta( $post->ID, '_game_poster_image_id', true );
	$poster_image_url = $poster_image_id ? wp_get_attachment_url( $poster_image_id ) : '';
	$cracked_by = get_post_meta( $post->ID, '_game_cracked_by', true );
	$sourced_from = get_post_meta( $post->ID, '_game_sourced_from', true );
	$developer = get_post_meta( $post->ID, '_game_developer', true );
	$version = get_post_meta( $post->ID, '_game_version', true );
	$file_size = get_post_meta( $post->ID, '_game_file_size', true );
	
	?>
	<div id="game-details-container">
		<style>
		#game-details-container {
			padding: 20px;
			background: #f9f9f9;
			border-radius: 8px;
			margin: 10px 0;
		}
		.game-details-section {
			background: white;
			border: 1px solid #ddd;
			border-radius: 6px;
			padding: 20px;
			margin-bottom: 20px;
		}
		.game-details-section h3 {
			margin-top: 0;
			color: #23282d;
			border-bottom: 1px solid #eee;
			padding-bottom: 10px;
		}
		.game-detail-field {
			margin-bottom: 15px;
		}
		.game-detail-field label {
			display: block;
			font-weight: 600;
			margin-bottom: 5px;
			color: #333;
		}
		.game-detail-field input[type="text"] {
			width: 100%;
			padding: 8px 12px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 14px;
		}
		.game-detail-field input[type="text"]:focus {
			border-color: #0073aa;
			outline: none;
			box-shadow: 0 0 3px rgba(0,115,170,0.3);
		}
		.poster-upload-container {
			display: flex;
			align-items: flex-start;
			gap: 20px;
		}
		.poster-preview {
			width: 190px;
			height: 239px;
			border: 2px dashed #ddd;
			border-radius: 6px;
			display: flex;
			align-items: center;
			justify-content: center;
			background: #f5f5f5;
			position: relative;
			overflow: hidden;
		}
		.poster-preview img {
			max-width: 100%;
			max-height: 100%;
			object-fit: cover;
		}
		.poster-preview.has-image {
			border-style: solid;
		}
		.poster-upload-buttons {
			display: flex;
			flex-direction: column;
			gap: 10px;
		}
		.upload-poster-button, .remove-poster-button {
			padding: 10px 20px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 14px;
			text-align: center;
		}
		.upload-poster-button {
			background: #0073aa;
			color: white;
		}
		.upload-poster-button:hover {
			background: #005a87;
		}
		.remove-poster-button {
			background: #dc3232;
			color: white;
		}
		.remove-poster-button:hover {
			background: #a00;
		}
		.game-details-row {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 15px;
		}
		@media (max-width: 768px) {
			.game-details-row {
				grid-template-columns: 1fr;
			}
		}
		</style>
		
		<!-- Poster Image Section -->
		<div class="game-details-section">
			<h3>📸 Game Poster Image</h3>
			<p style="margin-bottom: 15px; color: #666;">Upload a poster image (recommended size: 300x380px or similar aspect ratio)</p>
			
			<div class="poster-upload-container">
				<div class="poster-preview <?php echo $poster_image_url ? 'has-image' : ''; ?>" id="poster-preview">
					<?php if ( $poster_image_url ): ?>
						<img src="<?php echo esc_url( $poster_image_url ); ?>" alt="Game Poster">
					<?php else: ?>
						<span style="color: #999;">No poster uploaded</span>
					<?php endif; ?>
				</div>
				
				<div class="poster-upload-buttons">
					<button type="button" class="upload-poster-button" id="upload-poster-button">
						<?php echo $poster_image_url ? '🔄 Change Poster' : '📤 Upload Poster'; ?>
					</button>
					<?php if ( $poster_image_url ): ?>
						<button type="button" class="remove-poster-button" id="remove-poster-button">
							🗑️ Remove Poster
						</button>
					<?php endif; ?>
					<input type="hidden" name="game_poster_image_id" id="game_poster_image_id" value="<?php echo esc_attr( $poster_image_id ); ?>">
				</div>
			</div>
		</div>
		
		<!-- Game Information Section -->
		<div class="game-details-section">
			<h3>📋 Game Information</h3>
			
			<div class="game-details-row">
				<div class="game-detail-field">
					<label for="game_cracked_by">🔓 Cracked By:</label>
					<input type="text" id="game_cracked_by" name="game_cracked_by" value="<?php echo esc_attr( $cracked_by ); ?>" placeholder="e.g., CODEX, CPY, PLAZA">
				</div>
				
				<div class="game-detail-field">
					<label for="game_sourced_from">📦 Sourced From:</label>
					<input type="text" id="game_sourced_from" name="game_sourced_from" value="<?php echo esc_attr( $sourced_from ); ?>" placeholder="e.g., Steam, Epic Games, GOG">
				</div>
			</div>
			
			<div class="game-details-row">
				<div class="game-detail-field">
					<label for="game_developer">👨‍💻 Developer:</label>
					<input type="text" id="game_developer" name="game_developer" value="<?php echo esc_attr( $developer ); ?>" placeholder="e.g., Electronic Arts, Ubisoft">
				</div>
				
				<div class="game-detail-field">
					<label for="game_version">🏷️ Version:</label>
					<input type="text" id="game_version" name="game_version" value="<?php echo esc_attr( $version ); ?>" placeholder="e.g., v1.0.5, Build 12345">
				</div>
			</div>
			
			<div class="game-detail-field">
				<label for="game_file_size">💾 File Size:</label>
				<input type="text" id="game_file_size" name="game_file_size" value="<?php echo esc_attr( $file_size ); ?>" placeholder="e.g., 25 GB, 1.5 GB">
			</div>
		</div>
	</div>
	
	<script>
	jQuery(document).ready(function($) {
		// Upload poster image
		$('#upload-poster-button').click(function(e) {
			e.preventDefault();
			
			var mediaUploader = wp.media({
				title: 'Choose Game Poster',
				button: {
					text: 'Use this poster'
				},
				multiple: false
			});
			
			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				$('#game_poster_image_id').val(attachment.id);
				$('#poster-preview').addClass('has-image').html('<img src="' + attachment.url + '" alt="Game Poster">');
				$('#upload-poster-button').text('🔄 Change Poster');
				
				// Add remove button if not exists
				if (!$('#remove-poster-button').length) {
					$('.poster-upload-buttons').append('<button type="button" class="remove-poster-button" id="remove-poster-button">🗑️ Remove Poster</button>');
				}
			});
			
			mediaUploader.open();
		});
		
		// Remove poster image
		$(document).on('click', '#remove-poster-button', function(e) {
			e.preventDefault();
			$('#game_poster_image_id').val('');
			$('#poster-preview').removeClass('has-image').html('<span style="color: #999;">No poster uploaded</span>');
			$('#upload-poster-button').text('📤 Upload Poster');
			$(this).remove();
		});
	});
	</script>
	<?php
}

// Game Requirements Meta Box Callback
function game_requirements_meta_box_callback( $post ) {
	// Add nonce for security
	wp_nonce_field( 'game_requirements_meta_box', 'game_requirements_meta_box_nonce' );
	
	// Get existing values
	$minimum_requirements = get_post_meta( $post->ID, '_game_minimum_requirements', true );
	$recommended_requirements = get_post_meta( $post->ID, '_game_recommended_requirements', true );
	
	?>
	<div id="game-requirements-container">
		<style>
		#game-requirements-container {
			padding: 20px;
			background: #f9f9f9;
			border-radius: 8px;
			margin: 10px 0;
		}
		.requirements-wrapper {
			display: flex;
			gap: 20px;
			margin-top: 15px;
		}
		.requirements-column {
			flex: 1;
			background: white;
			border: 1px solid #ddd;
			border-radius: 6px;
			padding: 20px;
		}
		.requirements-column h4 {
			margin-top: 0;
			color: #23282d;
			border-bottom: 1px solid #eee;
			padding-bottom: 10px;
			font-size: 16px;
		}
		.requirements-column textarea {
			width: 100%;
			min-height: 300px;
			padding: 10px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-family: monospace;
			font-size: 13px;
			resize: vertical;
		}
		.requirements-column textarea:focus {
			border-color: #0073aa;
			outline: none;
			box-shadow: 0 0 3px rgba(0,115,170,0.3);
		}
		.field-description {
			color: #666;
			font-size: 13px;
			margin-top: 5px;
			font-style: italic;
		}
		@media (max-width: 1200px) {
			.requirements-wrapper {
				flex-direction: column;
			}
		}
		</style>
		
		<div class="requirements-wrapper">
			<div class="requirements-column">
				<h4>💻 Minimum System Requirements</h4>
				<textarea 
					name="game_minimum_requirements" 
					id="game_minimum_requirements"
					placeholder="OS: Windows 10&#10;Processor: Intel Core i5&#10;Memory: 8 GB RAM&#10;Graphics: NVIDIA GTX 1060&#10;Storage: 50 GB available space"><?php echo esc_textarea( $minimum_requirements ); ?></textarea>
				<p class="field-description">Enter the minimum system requirements</p>
			</div>
			
			<div class="requirements-column">
				<h4>⚡ Recommended System Requirements</h4>
				<textarea 
					name="game_recommended_requirements" 
					id="game_recommended_requirements"
					placeholder="OS: Windows 11&#10;Processor: Intel Core i7&#10;Memory: 16 GB RAM&#10;Graphics: NVIDIA RTX 2070&#10;Storage: 50 GB available space"><?php echo esc_textarea( $recommended_requirements ); ?></textarea>
				<p class="field-description">Enter the recommended system requirements</p>
			</div>
		</div>
	</div>
	<?php
}

// Save Game Details Meta Box Data
add_action( 'save_post', 'save_game_details_meta_box_data' );
function save_game_details_meta_box_data( $post_id ) {
	// Check if nonce is valid
	if ( ! isset( $_POST['game_details_meta_box_nonce'] ) || 
		 ! wp_verify_nonce( $_POST['game_details_meta_box_nonce'], 'game_details_meta_box' ) ) {
		return;
	}
	
	// Check if user has permission to edit post
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	
	// Check if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	
	// Save poster image ID
	if ( isset( $_POST['game_poster_image_id'] ) ) {
		update_post_meta( $post_id, '_game_poster_image_id', absint( $_POST['game_poster_image_id'] ) );
	}
	
	// Save game details
	$fields = array(
		'game_cracked_by' => '_game_cracked_by',
		'game_sourced_from' => '_game_sourced_from',
		'game_developer' => '_game_developer',
		'game_version' => '_game_version',
		'game_file_size' => '_game_file_size'
	);
	
	foreach ( $fields as $field_name => $meta_key ) {
		if ( isset( $_POST[ $field_name ] ) ) {
			$new_value = sanitize_text_field( $_POST[ $field_name ] );
			
			// Special handling for game version to track changes
			if ( $field_name === 'game_version' ) {
				$old_value = get_post_meta( $post_id, $meta_key, true );
				$post_status = get_post_status( $post_id );
				
				// Only track version updates for published posts that already had a version
				// This prevents newly published posts from appearing in Recent Updates
				if ( $old_value !== $new_value && !empty( $new_value ) && !empty( $old_value ) && $post_status === 'publish' ) {
					update_post_meta( $post_id, '_game_version_updated', current_time( 'timestamp' ) );
				}
			}
			
			update_post_meta( $post_id, $meta_key, $new_value );
		}
	}
}

// Save Game Requirements Meta Box Data
add_action( 'save_post', 'save_game_requirements_meta_box_data' );
function save_game_requirements_meta_box_data( $post_id ) {
	// Check if nonce is valid
	if ( ! isset( $_POST['game_requirements_meta_box_nonce'] ) || 
		 ! wp_verify_nonce( $_POST['game_requirements_meta_box_nonce'], 'game_requirements_meta_box' ) ) {
		return;
	}
	
	// Check if user has permission to edit post
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	
	// Check if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	
	// Save minimum requirements
	if ( isset( $_POST['game_minimum_requirements'] ) ) {
		update_post_meta( $post_id, '_game_minimum_requirements', wp_kses_post( $_POST['game_minimum_requirements'] ) );
	}
	
	// Save recommended requirements
	if ( isset( $_POST['game_recommended_requirements'] ) ) {
		update_post_meta( $post_id, '_game_recommended_requirements', wp_kses_post( $_POST['game_recommended_requirements'] ) );
	}
}

// =====================================================
// CUSTOM SLIDER IMAGE FUNCTION
// =====================================================

// Custom function to get slider image (poster for slider 7, featured image for others)
function get_custom_slider_image( $post_id, $image_size, $slider_number ) {
	// Only use poster image for slider 7
	if ( $slider_number == 7 ) {
		// Get the game poster image ID
		$poster_image_id = get_post_meta( $post_id, '_game_poster_image_id', true );
		
		// If poster image exists, use it with optimized sizing
		if ( $poster_image_id ) {
			// Try to get a properly sized version of the poster image
			// Use custom horizontal slider sizes first, prioritize our custom sizes
			$poster_sizes = array( 'slider-poster-large', 'slider-poster', 'slider-horizontal', 'large', 'full' );
			
			foreach ( $poster_sizes as $size ) {
				$poster_image = wp_get_attachment_image_src( $poster_image_id, $size );
				if ( ! empty( $poster_image[0] ) ) {
					return $poster_image[0];
				}
			}
		}
	}
	
	// Fall back to featured image for all other cases
	$original_post = $GLOBALS['post'];
	$GLOBALS['post'] = get_post( $post_id );
	setup_postdata( $GLOBALS['post'] );
	
	$featured_image = tie_thumb_src( $image_size );
	
	$GLOBALS['post'] = $original_post;
	setup_postdata( $original_post );
	
	return $featured_image;
}

// Register custom image size for slider posters
add_action( 'after_setup_theme', 'register_slider_poster_sizes' );
function register_slider_poster_sizes() {
	// Add custom image size for slider 7 - smart cropping for complete container fill
	// Using array for center cropping to minimize loss of important content
	add_image_size( 'slider-poster', 600, 400, array( 'center', 'center' ) ); // Smart center crop
	add_image_size( 'slider-poster-large', 800, 533, array( 'center', 'center' ) ); // Smart center crop
	add_image_size( 'slider-horizontal', 480, 320, array( 'center', 'center' ) ); // Smart center crop
}


// Function to regenerate thumbnails for existing poster images
function regenerate_poster_thumbnails() {
	// Make sure image functions are available
	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	}
	
	// Get all posts with poster images
	$posts_with_posters = get_posts( array(
		'post_type' => 'post',
		'meta_query' => array(
			array(
				'key' => '_game_poster_image_id',
				'compare' => 'EXISTS'
			)
		),
		'posts_per_page' => -1
	) );
	
	foreach ( $posts_with_posters as $post ) {
		$poster_image_id = get_post_meta( $post->ID, '_game_poster_image_id', true );
		if ( $poster_image_id ) {
			// Regenerate thumbnails for this image
			wp_update_attachment_metadata( $poster_image_id, wp_generate_attachment_metadata( $poster_image_id, get_attached_file( $poster_image_id ) ) );
		}
	}
}

// Hook to regenerate thumbnails when the theme is activated or updated
add_action( 'after_switch_theme', 'regenerate_poster_thumbnails' );

// =====================================================
// RECENT UPDATES FUNCTIONALITY
// =====================================================

// Add Recent Updates option to Sort By dropdown
add_filter( 'TieLabs/Builder/Block/post_order_args', 'add_recent_updates_sort_option' );
function add_recent_updates_sort_option( $args ) {
	$args['recent_updates'] = esc_html__( 'Recent Updates', 'jannah-child' );
	return $args;
}

// Filter blocks to show only recently updated games when "Recent Updates" is selected
add_filter( 'TieLabs/Query/args', 'filter_recent_updates_query', 10, 2 );
function filter_recent_updates_query( $args, $block ) {
	// Check if Sort By is set to "recent_updates"
	if ( isset( $block['order'] ) && $block['order'] === 'recent_updates' ) {
		
		// Only show posts that have recent version updates
		$args['meta_query'] = array(
			array(
				'key' => '_game_version_updated',
				'compare' => 'EXISTS'
			)
		);
		
		// Order by version update timestamp, most recent first
		$args['meta_key'] = '_game_version_updated';
		$args['orderby'] = 'meta_value_num';
		$args['order'] = 'DESC';
	}
	
	return $args;
}

