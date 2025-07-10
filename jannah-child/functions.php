<?php

add_action( 'wp_enqueue_scripts', 'tie_theme_child_styles_scripts', 80 );
function tie_theme_child_styles_scripts() {

	/* Load the RTL.css file of the parent theme */
	if ( is_rtl() ) {
		wp_enqueue_style( 'tie-theme-rtl-css', get_template_directory_uri().'/rtl.css', '' );
	}

	/* THIS WILL ALLOW ADDING CUSTOM CSS TO THE style.css */
	wp_enqueue_style( 'tie-theme-child-css', get_stylesheet_directory_uri().'/style.css', '' );

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
	
	// If no download buttons, return original content
	if ( empty( $download_buttons ) || ! is_array( $download_buttons ) ) {
		return $content;
	}
	
	// Generate download section HTML
	$download_section = generate_download_section_html( $download_buttons );
	
	// Append download section to content
	return $content . $download_section;
}

// Generate Download Section HTML
function generate_download_section_html( $download_buttons ) {
	ob_start();
	?>
	<div class="container-wrapper game-download-section">
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
