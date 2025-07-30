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
					// Calculate offset to center the element in viewport
					const viewportHeight = window.innerHeight;
					const elementHeight = targetElement.offsetHeight;
					const headerOffset = 80; // Account for any fixed headers/navigation
					
					// Calculate position to center the element
					const centerOffset = (viewportHeight - elementHeight) / 2 - headerOffset;
					const elementPosition = targetElement.getBoundingClientRect().top;
					const offsetPosition = elementPosition + window.pageYOffset - Math.max(centerOffset, 20);
					
					window.scrollTo({
						top: offsetPosition,
						behavior: 'smooth'
					});
					
					// Optional: Add a subtle flash effect to the target section
					targetElement.style.transition = 'background-color 0.3s ease';
					const originalBg = targetElement.style.backgroundColor;
					targetElement.style.backgroundColor = 'rgba(6, 105, 255, 0.08)';
					
					setTimeout(function() {
						targetElement.style.backgroundColor = originalBg;
					}, 800);
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
	
	// Generate download section HTML with requirements
	$download_section = generate_download_section_html( $download_buttons, get_the_ID() );
	
	// Append download section to content (no separator after main content)
	return $content . $download_section;
}

// Generate Download Section HTML
function generate_download_section_html( $download_buttons, $post_id = null ) {
	// Get requirements data
	$minimum_requirements = $post_id ? get_post_meta( $post_id, '_game_minimum_requirements', true ) : '';
	$recommended_requirements = $post_id ? get_post_meta( $post_id, '_game_recommended_requirements', true ) : '';
	
	// Get DLC data
	$dlc_list = $post_id ? get_post_meta( $post_id, '_game_dlc_list', true ) : '';
	$dlcs = array();
	if ( ! empty( $dlc_list ) ) {
		$dlcs = array_filter( array_map( 'trim', explode( "\n", $dlc_list ) ) );
	}
	
	// Get accordion settings
	$accordion_state = $post_id ? get_post_meta( $post_id, '_game_dlc_accordion_state', true ) : 'auto';
	$auto_threshold = $post_id ? get_post_meta( $post_id, '_game_dlc_auto_threshold', true ) : 5;
	
	// Determine if accordion should be open
	$is_open = false;
	if ( $accordion_state === 'open' ) {
		$is_open = true;
	} elseif ( $accordion_state === 'auto' && count( $dlcs ) <= $auto_threshold ) {
		$is_open = true;
	}
	
	ob_start();
	?>
	<?php if ( ! empty( $dlcs ) ) : ?>
		<?php echo generate_dlc_accordion_html( $dlcs, $is_open ); ?>
	<?php endif; ?>
	<!-- DLC SEPARATOR START -->
	<hr class="section-separator dlc-separator">
	<!-- DLC SEPARATOR END -->
	
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
	<!-- REQUIREMENTS SEPARATOR START -->
	<hr class="section-separator requirements-separator">
	<!-- REQUIREMENTS SEPARATOR END -->
	
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
	
	<style>
	/* Section Separators - Always load these styles */
	.section-separator {
		border: none;
		height: 1px;
		background: #e8eaed;
		margin: 40px 0;
		display: block;
		visibility: visible;
	}
	
	/* Dark mode separators */
	body.tie-dark-mode .section-separator,
	html.tie-dark-mode .section-separator,
	.tie-dark-mode .section-separator,
	body[data-theme="dark"] .section-separator,
	html[data-theme="dark"] .section-separator,
	[data-theme="dark"] .section-separator,
	body.dark-mode .section-separator,
	html.dark-mode .section-separator,
	.dark-mode .section-separator,
	body[class*="dark"] .section-separator,
	html[class*="dark"] .section-separator {
		background: rgba(255,255,255,0.15);
	}
	
	/* Responsive separators */
	@media (max-width: 768px) {
		.section-separator {
			margin: 30px 0;
		}
	}
	
	/* IMPROVED DOWNLOAD SECTION STYLES */
	.game-download-section {
		margin: 30px 0 !important;
		text-align: center !important;
	}
	
	.game-download-section .widget-title {
		margin-bottom: 24px !important;
	}
	
	.game-download-section .main-title {
		font-size: 18px !important;
		font-weight: 700 !important;
		margin: 0 0 6px 0 !important;
		display: inline-flex !important;
		align-items: center !important;
		gap: 8px !important;
		color: #2c3e50 !important;
	}
	
	.game-download-section .the-subtitle {
		color: #7f8c8d !important;
		font-size: 14px !important;
		margin: 0 !important;
		font-weight: 400 !important;
	}
	
	.download-buttons-list {
		margin-bottom: 20px !important;
		display: flex !important;
		flex-direction: column !important;
		align-items: center !important;
		gap: 8px !important;
	}
	
	.download-item {
		width: 100% !important;
		max-width: 260px !important;
		margin: 0 !important;
	}
	
	.download-link {
		display: flex !important;
		align-items: center !important;
		padding: 10px 14px !important;
		border: 1px solid var(--brand-color) !important;
		border-radius: 6px !important;
		background: linear-gradient(135deg, var(--brand-color) 0%, #0056d3 100%) !important;
		color: white !important;
		text-decoration: none !important;
		transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
		position: relative !important;
		box-shadow: 0 6px 20px rgba(6, 105, 255, 0.35), 0 2px 6px rgba(0, 0, 0, 0.1) !important;
		width: 100% !important;
		min-height: 48px !important;
		transform: translateY(-3px) !important;
	}
	
	.download-link:hover {
		background: rgba(255, 255, 255, 0.25) !important;
		backdrop-filter: blur(10px) !important;
		-webkit-backdrop-filter: blur(10px) !important;
		border: 1px solid rgba(255, 255, 255, 0.3) !important;
		color: white !important;
		transform: translateY(-4px) scale(1.02) !important;
		box-shadow: 0 8px 32px rgba(6, 105, 255, 0.4), 0 4px 16px rgba(255, 255, 255, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
	}
	
	.download-icon {
		display: flex !important;
		align-items: center !important;
		justify-content: center !important;
		width: 28px !important;
		height: 28px !important;
		margin-right: 12px !important;
		background: rgba(255, 255, 255, 0.2) !important;
		border-radius: 5px !important;
		color: white !important;
		flex-shrink: 0 !important;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15) !important;
	}
	
	.download-link:hover .download-icon {
		background: rgba(255, 255, 255, 0.3) !important;
		color: white !important;
		box-shadow: 0 2px 8px rgba(255, 255, 255, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
	}
	
	.download-content {
		flex: 1 !important;
		display: flex !important;
		flex-direction: column !important;
		min-width: 0 !important;
		justify-content: center !important;
		gap: 1px !important;
	}
	
	.download-title {
		font-size: 14px !important;
		font-weight: 600 !important;
		margin: 0 !important;
		line-height: 1.2 !important;
		white-space: nowrap !important;
		overflow: hidden !important;
		text-overflow: ellipsis !important;
	}
	
	.download-subtitle {
		font-size: 11px !important;
		color: rgba(255, 255, 255, 0.8) !important;
		line-height: 1.1 !important;
		margin: 0 !important;
		white-space: nowrap !important;
		overflow: hidden !important;
		text-overflow: ellipsis !important;
	}
	
	.download-link:hover .download-subtitle {
		color: rgba(255, 255, 255, 0.9) !important;
	}
	
	.download-arrow {
		color: rgba(255, 255, 255, 0.9) !important;
		transition: all 0.25s ease !important;
		margin-left: auto !important;
		flex-shrink: 0 !important;
		font-size: 11px !important;
		align-self: center !important;
	}
	
	.download-link:hover .download-arrow {
		color: white !important;
		transform: translateX(3px) !important;
	}
	
	.download-footer-info {
		border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
		padding-top: 16px !important;
		margin-top: 20px !important;
	}
	
	.download-meta {
		display: flex !important;
		justify-content: center !important;
		gap: 20px !important;
		flex-wrap: wrap !important;
	}
	
	.download-meta span {
		font-size: 11px !important;
		color: #95a5a6 !important;
		display: flex !important;
		align-items: center !important;
		gap: 5px !important;
		font-weight: 500 !important;
	}
	
	/* Dark theme support */
	.dark-skin .game-download-section .main-title {
		color: #ecf0f1 !important;
	}
	
	.dark-skin .game-download-section .the-subtitle {
		color: #bdc3c7 !important;
	}
	
	.dark-skin .download-link {
		background: linear-gradient(135deg, var(--brand-color) 0%, #0056d3 100%) !important;
		border-color: var(--brand-color) !important;
		color: white !important;
		box-shadow: 0 6px 20px rgba(6, 105, 255, 0.35), 0 2px 6px rgba(0, 0, 0, 0.25) !important;
		transform: translateY(-3px) !important;
	}
	
	.dark-skin .download-link:hover {
		background: rgba(255, 255, 255, 0.15) !important;
		backdrop-filter: blur(15px) !important;
		-webkit-backdrop-filter: blur(15px) !important;
		border: 1px solid rgba(255, 255, 255, 0.25) !important;
		color: white !important;
		transform: translateY(-4px) scale(1.02) !important;
		box-shadow: 0 8px 32px rgba(6, 105, 255, 0.5), 0 4px 16px rgba(255, 255, 255, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
	}
	
	.dark-skin .download-subtitle {
		color: #aaa !important;
	}
	
	.dark-skin .download-meta span {
		color: #95a5a6 !important;
	}
	
	.dark-skin .download-footer-info {
		border-color: rgba(255, 255, 255, 0.08) !important;
	}
	
	/* Responsive */
	@media (max-width: 768px) {
		.download-buttons-list {
			gap: 6px !important;
		}
		
		.download-item {
			max-width: 100% !important;
		}
		
		.download-link {
			padding: 8px 12px !important;
			min-height: 42px !important;
		}
		
		.download-icon {
			width: 24px !important;
			height: 24px !important;
			margin-right: 10px !important;
		}
		
		.download-title {
			font-size: 13px !important;
		}
		
		.download-subtitle {
			font-size: 10px !important;
		}
		
		.download-arrow {
			font-size: 10px !important;
		}
	}
	</style>
	
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

// =====================================================
// DLC ACCORDION FUNCTIONALITY
// =====================================================

// Add DLC Meta Box above Download Section
add_action( 'add_meta_boxes', 'add_dlc_meta_box', 5 ); // Priority 5 to appear before Download section
function add_dlc_meta_box() {
	add_meta_box(
		'dlc_accordion_meta_box',
		'🎯 DLC Accordion Settings',
		'dlc_accordion_meta_box_callback',
		'post',
		'normal',
		'high'
	);
}

// DLC Meta Box Callback
function dlc_accordion_meta_box_callback( $post ) {
	// Add nonce for security
	wp_nonce_field( 'dlc_accordion_meta_box', 'dlc_accordion_meta_box_nonce' );
	
	// Get existing values
	$dlc_list = get_post_meta( $post->ID, '_game_dlc_list', true );
	$accordion_state = get_post_meta( $post->ID, '_game_dlc_accordion_state', true );
	$auto_threshold = get_post_meta( $post->ID, '_game_dlc_auto_threshold', true );
	
	// Set defaults
	if ( empty( $accordion_state ) ) {
		$accordion_state = 'auto';
	}
	if ( empty( $auto_threshold ) ) {
		$auto_threshold = 5;
	}
	
	?>
	<div id="dlc-accordion-container">
		<style>
		#dlc-accordion-container {
			padding: 20px;
			background: #f9f9f9;
			border-radius: 8px;
			margin: 10px 0;
		}
		.dlc-field-group {
			background: white;
			border: 1px solid #ddd;
			border-radius: 6px;
			padding: 20px;
			margin-bottom: 15px;
		}
		.dlc-field-group h4 {
			margin-top: 0;
			color: #23282d;
			border-bottom: 1px solid #eee;
			padding-bottom: 10px;
			font-size: 16px;
		}
		.dlc-field {
			margin-bottom: 15px;
		}
		.dlc-field label {
			display: block;
			font-weight: 600;
			margin-bottom: 5px;
			color: #333;
		}
		.dlc-field textarea {
			width: 100%;
			min-height: 200px;
			padding: 10px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
			font-size: 14px;
			resize: vertical;
		}
		.dlc-field textarea:focus {
			border-color: #0073aa;
			outline: none;
			box-shadow: 0 0 3px rgba(0,115,170,0.3);
		}
		.dlc-field select, .dlc-field input[type="number"] {
			padding: 8px 12px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 14px;
		}
		.dlc-field select:focus, .dlc-field input[type="number"]:focus {
			border-color: #0073aa;
			outline: none;
			box-shadow: 0 0 3px rgba(0,115,170,0.3);
		}
		.dlc-field-hint {
			color: #666;
			font-size: 13px;
			margin-top: 5px;
			font-style: italic;
		}
		.dlc-settings-row {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 20px;
		}
		@media (max-width: 768px) {
			.dlc-settings-row {
				grid-template-columns: 1fr;
			}
		}
		</style>
		
		<div class="dlc-field-group">
			<h4>📋 DLC List</h4>
			<div class="dlc-field">
				<label for="game_dlc_list">Enter DLCs (one per line):</label>
				<textarea 
					name="game_dlc_list" 
					id="game_dlc_list"
					placeholder="Example:&#10;Season Pass&#10;Weapon Pack: Elite Arsenal&#10;Map Pack: Desert Storm&#10;Character Skin: Ghost Operator&#10;Bonus Mission: Night Raid"><?php echo esc_textarea( $dlc_list ); ?></textarea>
				<p class="dlc-field-hint">Each line will be displayed as a bullet point in the accordion</p>
			</div>
		</div>
		
		<div class="dlc-field-group">
			<h4>⚙️ Display Settings</h4>
			<div class="dlc-settings-row">
				<div class="dlc-field">
					<label for="game_dlc_accordion_state">Accordion Default State:</label>
					<select name="game_dlc_accordion_state" id="game_dlc_accordion_state">
						<option value="auto" <?php selected( $accordion_state, 'auto' ); ?>>Auto (based on DLC count)</option>
						<option value="closed" <?php selected( $accordion_state, 'closed' ); ?>>Always Closed</option>
						<option value="open" <?php selected( $accordion_state, 'open' ); ?>>Always Open</option>
					</select>
					<p class="dlc-field-hint">Control whether the accordion is open or closed by default</p>
				</div>
				
				<div class="dlc-field">
					<label for="game_dlc_auto_threshold">Auto-close Threshold:</label>
					<input 
						type="number" 
						name="game_dlc_auto_threshold" 
						id="game_dlc_auto_threshold"
						value="<?php echo esc_attr( $auto_threshold ); ?>"
						min="1"
						max="50"
						style="width: 100px;">
					<p class="dlc-field-hint">When using Auto mode, accordion closes if DLCs exceed this number</p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

// Save DLC Meta Box Data
add_action( 'save_post', 'save_dlc_meta_box_data' );
function save_dlc_meta_box_data( $post_id ) {
	// Check if nonce is valid
	if ( ! isset( $_POST['dlc_accordion_meta_box_nonce'] ) || 
		 ! wp_verify_nonce( $_POST['dlc_accordion_meta_box_nonce'], 'dlc_accordion_meta_box' ) ) {
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
	
	// Save DLC list
	if ( isset( $_POST['game_dlc_list'] ) ) {
		update_post_meta( $post_id, '_game_dlc_list', sanitize_textarea_field( $_POST['game_dlc_list'] ) );
	}
	
	// Save accordion state
	if ( isset( $_POST['game_dlc_accordion_state'] ) ) {
		update_post_meta( $post_id, '_game_dlc_accordion_state', sanitize_text_field( $_POST['game_dlc_accordion_state'] ) );
	}
	
	// Save auto threshold
	if ( isset( $_POST['game_dlc_auto_threshold'] ) ) {
		update_post_meta( $post_id, '_game_dlc_auto_threshold', absint( $_POST['game_dlc_auto_threshold'] ) );
	}
}

// Remove the old DLC content filter since we'll display it differently
// DLC accordion is now displayed through the generate_download_section_html function

// Generate DLC Accordion HTML
function generate_dlc_accordion_html( $dlcs, $is_open = false ) {
	$dlc_count = count( $dlcs );
	$accordion_class = $is_open ? 'dlc-accordion open' : 'dlc-accordion';
	$content_style = $is_open ? '' : 'style="display: none;"';
	
	ob_start();
	?>
	<div class="container-wrapper dlc-accordion-section">
		<div class="<?php echo esc_attr( $accordion_class ); ?>" id="dlc-accordion">
		<div class="dlc-accordion-header" onclick="toggleDLCAccordion()">
			<div class="dlc-header-content">
				<h3 class="dlc-title">Included DLCs</h3>
				<span class="dlc-count">(<?php echo $dlc_count; ?> DLC<?php echo $dlc_count > 1 ? 's' : ''; ?>)</span>
			</div>
			<span class="dlc-toggle-icon">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</div>
		<div class="dlc-accordion-content" <?php echo $content_style; ?>>
			<ul class="dlc-list">
				<?php foreach ( $dlcs as $dlc ): ?>
					<li><?php echo esc_html( $dlc ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	</div>
	
	<style>
	.dlc-accordion-section {
		margin: 30px 0;
	}
	
	/* Section Separators */
	.section-separator {
		border: none;
		height: 1px;
		background: #e8eaed;
		margin: 40px 0;
		display: block;
		visibility: visible;
	}
	
	/* Dark mode separators */
	body.tie-dark-mode .section-separator,
	html.tie-dark-mode .section-separator,
	.tie-dark-mode .section-separator,
	body[data-theme="dark"] .section-separator,
	html[data-theme="dark"] .section-separator,
	[data-theme="dark"] .section-separator,
	body.dark-mode .section-separator,
	html.dark-mode .section-separator,
	.dark-mode .section-separator,
	body[class*="dark"] .section-separator,
	html[class*="dark"] .section-separator {
		background: rgba(255,255,255,0.15);
	}
	
	/* Responsive separators */
	@media (max-width: 768px) {
		.section-separator {
			margin: 30px 0;
		}
	}
	
	.dlc-accordion {
		background: #f8f9fa;
		border: 1px solid #e9ecef;
		border-radius: 8px;
		margin: 0;
		overflow: hidden;
		transition: all 0.3s ease;
	}
	
	.dlc-accordion:hover {
		border-color: #d1d5db;
		box-shadow: 0 2px 4px rgba(0,0,0,0.05);
	}
	
	.dlc-accordion-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 16px 20px;
		cursor: pointer;
		user-select: none;
		background: white;
		border-bottom: 1px solid #e9ecef;
		transition: background-color 0.2s ease;
	}
	
	.dlc-accordion-header:hover {
		background-color: #f8f9fa;
	}
	
	.dlc-header-content {
		display: flex;
		align-items: center;
		gap: 12px;
	}
	
	.dlc-icon {
		font-size: 24px;
		line-height: 1;
	}
	
	.dlc-title {
		margin: 0;
		font-size: 18px;
		font-weight: 600;
		color: #2c3e50;
	}
	
	.dlc-count {
		color: #6c757d;
		font-size: 14px;
		font-weight: normal;
	}
	
	.dlc-toggle-icon {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 32px;
		height: 32px;
		background: #f1f3f5;
		border-radius: 50%;
		transition: all 0.3s ease;
		color: #6c757d;
	}
	
	.dlc-accordion.open .dlc-toggle-icon {
		transform: rotate(180deg);
		background: #e9ecef;
	}
	
	.dlc-accordion-content {
		padding: 0;
		background: white;
		overflow: hidden;
		transition: all 0.3s ease;
	}
	
	/* Force dark mode styles with highest specificity */
	body.tie-dark-mode .dlc-accordion-header,
	.tie-dark-mode .dlc-accordion-header,
	[data-dark-mode="true"] .dlc-accordion-header {
		background: rgba(255,255,255,0.1) !important;
		border-bottom-color: #333 !important;
	}
	
	body.tie-dark-mode .dlc-accordion-header:hover,
	.tie-dark-mode .dlc-accordion-header:hover,
	[data-dark-mode="true"] .dlc-accordion-header:hover {
		background-color: #2a2a2a !important;
	}
	
	body.tie-dark-mode .dlc-title,
	.tie-dark-mode .dlc-title,
	[data-dark-mode="true"] .dlc-title {
		color: #ffffff !important;
	}
	
	body.tie-dark-mode .dlc-count,
	.tie-dark-mode .dlc-count,
	[data-dark-mode="true"] .dlc-count {
		color: #cccccc !important;
	}
	
	body.tie-dark-mode .dlc-accordion-content,
	.tie-dark-mode .dlc-accordion-content,
	[data-dark-mode="true"] .dlc-accordion-content {
		background: rgba(255,255,255,0.1) !important;
	}
	
	.dlc-list {
		list-style: none !important;
		margin: 0;
		padding: 20px;
		columns: 2;
		column-gap: 30px;
	}
	
	.dlc-list li {
		position: relative;
		padding-left: 24px;
		margin-bottom: 10px;
		color: #495057;
		break-inside: avoid;
		page-break-inside: avoid;
		list-style: none !important;
	}
	
	.dlc-list li:before {
		content: "•";
		position: absolute;
		left: 0;
		color: #007bff;
		font-weight: bold;
		font-size: 20px;
		line-height: 1;
	}
	
	/* Responsive design */
	@media (max-width: 768px) {
		.dlc-list {
			columns: 1;
		}
		
		.dlc-header-content {
			gap: 8px;
		}
		
		.dlc-icon {
			font-size: 20px;
		}
		
		.dlc-title {
			font-size: 16px;
		}
		
		.dlc-count {
			font-size: 12px;
		}
	}
	
	/* ULTRA AGGRESSIVE DARK MODE TARGETING */
	
	/* Main accordion container - ALL possible selectors */
	body.tie-dark-mode .container-wrapper .dlc-accordion,
	html.tie-dark-mode .container-wrapper .dlc-accordion,
	.tie-dark-mode .container-wrapper .dlc-accordion,
	body.tie-dark-mode .dlc-accordion-section .dlc-accordion,
	html.tie-dark-mode .dlc-accordion-section .dlc-accordion,
	.tie-dark-mode .dlc-accordion-section .dlc-accordion,
	body.tie-dark-mode .dlc-accordion,
	html.tie-dark-mode .dlc-accordion,
	.tie-dark-mode .dlc-accordion,
	body[data-theme="dark"] .dlc-accordion,
	html[data-theme="dark"] .dlc-accordion,
	[data-theme="dark"] .dlc-accordion,
	body.dark-mode .dlc-accordion,
	html.dark-mode .dlc-accordion,
	.dark-mode .dlc-accordion,
	body[class*="dark"] .dlc-accordion,
	html[class*="dark"] .dlc-accordion {
		background: rgba(255,255,255,0.1) !important;
		border-color: rgba(255,255,255,0.2) !important;
	}
	
	/* Accordion header - ALL possible selectors */
	body.tie-dark-mode .container-wrapper .dlc-accordion-header,
	html.tie-dark-mode .container-wrapper .dlc-accordion-header,
	.tie-dark-mode .container-wrapper .dlc-accordion-header,
	body.tie-dark-mode .dlc-accordion-section .dlc-accordion-header,
	html.tie-dark-mode .dlc-accordion-section .dlc-accordion-header,
	.tie-dark-mode .dlc-accordion-section .dlc-accordion-header,
	body.tie-dark-mode .dlc-accordion-header,
	html.tie-dark-mode .dlc-accordion-header,
	.tie-dark-mode .dlc-accordion-header,
	body[data-theme="dark"] .dlc-accordion-header,
	html[data-theme="dark"] .dlc-accordion-header,
	[data-theme="dark"] .dlc-accordion-header,
	body.dark-mode .dlc-accordion-header,
	html.dark-mode .dlc-accordion-header,
	.dark-mode .dlc-accordion-header,
	body[class*="dark"] .dlc-accordion-header,
	html[class*="dark"] .dlc-accordion-header {
		background: rgba(255,255,255,0.1) !important;
		border-bottom-color: rgba(255,255,255,0.2) !important;
	}
	
	/* Title text - ALL possible selectors */
	body.tie-dark-mode .container-wrapper .dlc-title,
	html.tie-dark-mode .container-wrapper .dlc-title,
	.tie-dark-mode .container-wrapper .dlc-title,
	body.tie-dark-mode .dlc-accordion-section .dlc-title,
	html.tie-dark-mode .dlc-accordion-section .dlc-title,
	.tie-dark-mode .dlc-accordion-section .dlc-title,
	body.tie-dark-mode .dlc-title,
	html.tie-dark-mode .dlc-title,
	.tie-dark-mode .dlc-title,
	body[data-theme="dark"] .dlc-title,
	html[data-theme="dark"] .dlc-title,
	[data-theme="dark"] .dlc-title,
	body.dark-mode .dlc-title,
	html.dark-mode .dlc-title,
	.dark-mode .dlc-title,
	body[class*="dark"] .dlc-title,
	html[class*="dark"] .dlc-title {
		color: #ffffff !important;
	}
	
	/* Count text - ALL possible selectors */
	body.tie-dark-mode .container-wrapper .dlc-count,
	html.tie-dark-mode .container-wrapper .dlc-count,
	.tie-dark-mode .container-wrapper .dlc-count,
	body.tie-dark-mode .dlc-accordion-section .dlc-count,
	html.tie-dark-mode .dlc-accordion-section .dlc-count,
	.tie-dark-mode .dlc-accordion-section .dlc-count,
	body.tie-dark-mode .dlc-count,
	html.tie-dark-mode .dlc-count,
	.tie-dark-mode .dlc-count,
	body[data-theme="dark"] .dlc-count,
	html[data-theme="dark"] .dlc-count,
	[data-theme="dark"] .dlc-count,
	body.dark-mode .dlc-count,
	html.dark-mode .dlc-count,
	.dark-mode .dlc-count,
	body[class*="dark"] .dlc-count,
	html[class*="dark"] .dlc-count {
		color: #cccccc !important;
	}
	
	/* Content area - ALL possible selectors */
	body.tie-dark-mode .container-wrapper .dlc-accordion-content,
	html.tie-dark-mode .container-wrapper .dlc-accordion-content,
	.tie-dark-mode .container-wrapper .dlc-accordion-content,
	body.tie-dark-mode .dlc-accordion-section .dlc-accordion-content,
	html.tie-dark-mode .dlc-accordion-section .dlc-accordion-content,
	.tie-dark-mode .dlc-accordion-section .dlc-accordion-content,
	body.tie-dark-mode .dlc-accordion-content,
	html.tie-dark-mode .dlc-accordion-content,
	.tie-dark-mode .dlc-accordion-content,
	body[data-theme="dark"] .dlc-accordion-content,
	html[data-theme="dark"] .dlc-accordion-content,
	[data-theme="dark"] .dlc-accordion-content,
	body.dark-mode .dlc-accordion-content,
	html.dark-mode .dlc-accordion-content,
	.dark-mode .dlc-accordion-content,
	body[class*="dark"] .dlc-accordion-content,
	html[class*="dark"] .dlc-accordion-content {
		background: rgba(255,255,255,0.1) !important;
	}
	
	/* List items - ALL possible selectors */
	body.tie-dark-mode .container-wrapper .dlc-list li,
	html.tie-dark-mode .container-wrapper .dlc-list li,
	.tie-dark-mode .container-wrapper .dlc-list li,
	body.tie-dark-mode .dlc-accordion-section .dlc-list li,
	html.tie-dark-mode .dlc-accordion-section .dlc-list li,
	.tie-dark-mode .dlc-accordion-section .dlc-list li,
	body.tie-dark-mode .dlc-list li,
	html.tie-dark-mode .dlc-list li,
	.tie-dark-mode .dlc-list li,
	body[data-theme="dark"] .dlc-list li,
	html[data-theme="dark"] .dlc-list li,
	[data-theme="dark"] .dlc-list li,
	body.dark-mode .dlc-list li,
	html.dark-mode .dlc-list li,
	.dark-mode .dlc-list li,
	body[class*="dark"] .dlc-list li,
	html[class*="dark"] .dlc-list li {
		color: #dddddd !important;
	}
	</style>
	
	<script>
	function toggleDLCAccordion() {
		var accordion = document.getElementById('dlc-accordion');
		var content = accordion.querySelector('.dlc-accordion-content');
		
		if (accordion.classList.contains('open')) {
			accordion.classList.remove('open');
			content.style.display = 'none';
		} else {
			accordion.classList.add('open');
			content.style.display = 'block';
		}
	}
	
	// ULTRA AGGRESSIVE dark mode styling enforcement
	document.addEventListener('DOMContentLoaded', function() {
		function applyDarkModeStyles() {
			// Check EVERY possible dark mode indicator
			var isDarkMode = document.body.classList.contains('tie-dark-mode') || 
							document.documentElement.classList.contains('tie-dark-mode') ||
							document.body.getAttribute('data-dark-mode') === 'true' ||
							document.documentElement.getAttribute('data-dark-mode') === 'true' ||
							document.body.getAttribute('data-theme') === 'dark' ||
							document.documentElement.getAttribute('data-theme') === 'dark' ||
							document.body.classList.contains('dark-mode') ||
							document.documentElement.classList.contains('dark-mode') ||
							window.getComputedStyle(document.body).backgroundColor.includes('rgb(30, 30, 30)') ||
							window.getComputedStyle(document.body).backgroundColor.includes('rgb(26, 26, 26)');
			
			console.log('DLC Accordion: Dark mode detected:', isDarkMode);
			
			if (isDarkMode) {
				var accordion = document.getElementById('dlc-accordion');
				if (accordion) {
					var header = accordion.querySelector('.dlc-accordion-header');
					var content = accordion.querySelector('.dlc-accordion-content');
					var title = accordion.querySelector('.dlc-title');
					var count = accordion.querySelector('.dlc-count');
					
					console.log('DLC Accordion: Applying dark mode styles');
					
					// Force accordion container styles
					accordion.style.setProperty('background', 'rgba(255,255,255,0.1)', 'important');
					accordion.style.setProperty('border-color', 'rgba(255,255,255,0.2)', 'important');
					
					if (header) {
						header.style.setProperty('background-color', 'rgba(255,255,255,0.1)', 'important');
						header.style.setProperty('border-bottom-color', 'rgba(255,255,255,0.2)', 'important');
					}
					if (content) {
						content.style.setProperty('background-color', 'rgba(255,255,255,0.1)', 'important');
					}
					if (title) {
						title.style.setProperty('color', '#ffffff', 'important');
						console.log('DLC Accordion: Title color set to white');
					}
					if (count) {
						count.style.setProperty('color', '#cccccc', 'important');
					}
				}
			}
		}
		
		// Apply styles immediately
		setTimeout(applyDarkModeStyles, 100);
		
		// Apply styles multiple times to catch dynamic loading
		setTimeout(applyDarkModeStyles, 500);
		setTimeout(applyDarkModeStyles, 1000);
		setTimeout(applyDarkModeStyles, 2000);
		
		// Watch for theme changes with multiple observers
		var observer = new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				if (mutation.type === 'attributes') {
					setTimeout(applyDarkModeStyles, 50);
				}
			});
		});
		
		observer.observe(document.body, {
			attributes: true,
			subtree: true,
			childList: true
		});
		
		observer.observe(document.documentElement, {
			attributes: true,
			subtree: true,
			childList: true
		});
		
		// Also watch for window resize and scroll events
		window.addEventListener('resize', applyDarkModeStyles);
		window.addEventListener('scroll', applyDarkModeStyles);
		window.addEventListener('load', applyDarkModeStyles);
		
		// Force check every 3 seconds
		setInterval(applyDarkModeStyles, 3000);
	});
	</script>
	<?php
	return ob_get_clean();
}

