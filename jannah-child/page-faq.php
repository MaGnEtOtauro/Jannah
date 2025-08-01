<?php
/**
 * Template Name: FAQ Page
 * The template for displaying FAQ page
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

get_header(); ?>

<div class="container">
	<div class="content">
		<div class="tie-row main-content">
			<div class="tie-col-md-8">
				
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>
						
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
							
							<div class="entry-header">
								<h1 class="entry-title"><?php the_title(); ?></h1>
							</div>

							<div class="entry-content">
								<?php the_content(); ?>
								
								<div class="faq-container">
									<div class="faq-intro">
										<p>Find answers to frequently asked questions about SteamOra games and downloads. Click on any question to expand the answer.</p>
									</div>

									<div class="faq-items">
										
										<div class="faq-item active">
											<div class="faq-question">
												<h3>What is SteamOra.net?</h3>
												<span class="faq-toggle">−</span>
											</div>
											<div class="faq-answer">
												<p>SteamOra is a website where you can download free pre-installed Steam games. The name doesn't refer to "RIP" versions — all games are clean and ready to play.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>Is SteamOra safe to download from?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>Yes! All files are tested and verified before being uploaded. There are no viruses or malware — every game is pre-installed and safe to run.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>How do I download a game?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>Just click the Download button on the game page. You'll be taken to a dedicated download page where you can start downloading the game directly.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>How do I update a game?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<ol>
													<li>Download the latest version from SteamOra.</li>
													<li>Rename your old game folder so it won't be overwritten.</li>
													<li>Extract the new version and launch it.</li>
													<li>If your save data loads properly, you can safely delete the old version.</li>
												</ol>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>How can I request a game or an update?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>Go to our Request Games page and leave a comment in the correct format. We'll try our best to fulfill it.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>How do I change the language or nickname in a game?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<ol>
													<li>First, check the in-game settings.</li>
													<li>If it's not there, look for .ini files in the game folder like steam_emu.ini or steamconfig.ini, and edit the language or nickname fields using Notepad.</li>
													<li>For GOG games, look for .info files instead.</li>
												</ol>
												<p><strong>Note:</strong> We mainly support English files, so other languages may not always be included.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>My antivirus says the game is a virus. What should I do?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>This is a false positive. Some cracked files are flagged by antivirus software, but they're safe. Disable your antivirus, re-extract the game, and it should run fine. We test every game ourselves before uploading.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>Where is my save file located?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>Save files are different for every game. Most are stored in Documents or AppData. Try searching on Google like this:</p>
												<p><strong>"GameName save file location"</strong></p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>I'm getting a 404 error while downloading.</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>Your IP might be blocked. Use a VPN to start the download — once it begins, you can turn the VPN off.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>Do you support mods, cheats, or trainers?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>No, SteamOra doesn't provide mods, cheats, or trainers. However, mods usually work if you install them correctly. Please search online for help on this.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>Do these cracked games support multiplayer?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>Some games include multiplayer support, but you won't be able to connect through official platforms like Steam. You and your friends must use the same cracked version. Join our Discord to see which games have multiplayer.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>How do I add Online Multiplayer (Online-Fix)?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>On supported games, look for the Online-Fix button. Download the files and copy them into your game folder, replacing existing ones.</p>
												<p>Watch the tutorial: <a href="#" target="_blank" rel="noopener">YouTube Link</a></p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>How to follow setup steps and handle antivirus exclusions?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>Visit our Steps for Games page for setup help and antivirus exclusion.</p>
												<p>Watch the video tutorial: <a href="#" target="_blank" rel="noopener">YouTube Link</a></p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>What is a Torrent and how do I use it?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>Torrent is an alternative way to download large games faster.</p>
												<ol>
													<li>Download the .torrent file.</li>
													<li>Open it with a torrent client like qBittorrent or uTorrent.</li>
													<li>It will start downloading the game files directly.</li>
												</ol>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>How can I increase download speed?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>Use Internet Download Manager (IDM). It speeds up downloads and works well with browsers.</p>
												<p>Install IDM and its browser extension to auto-capture links, or manually add download URLs.</p>
											</div>
										</div>

										<div class="faq-item">
											<div class="faq-question">
												<h3>Does SteamOra crack games? Where do the games come from?</h3>
												<span class="faq-toggle">+</span>
											</div>
											<div class="faq-answer">
												<p>SteamOra doesn't crack games. We collect them from trusted sources like CS.RIN.RU, FitGirl, Xatab, and Masquerade. We install, test, and prepare them to be pre-installed and easy to use. Credit goes to the original scene groups.</p>
											</div>
										</div>

									</div>
								</div>
							</div>
						</article>
						
					<?php endwhile; ?>
				<?php endif; ?>
				
			</div>
			
			<?php get_sidebar(); ?>
			
		</div>
	</div>
</div>

<style>
/* FAQ Styles - Jannah Theme Colors */
.faq-container {
	margin: 30px 0;
	max-width: 100%;
}

.faq-intro {
	background: linear-gradient(135deg, #222222 0%, #333333 100%);
	padding: 20px;
	border-radius: 8px;
	margin-bottom: 30px;
	border-left: 4px solid #007cba;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.faq-intro p {
	margin: 0;
	color: #ffffff;
	font-size: 16px;
}

.faq-items {
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.faq-item {
	background: #222222;
	border: 1px solid #333333;
	border-radius: 8px;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
	transition: all 0.3s ease;
	overflow: hidden;
}

.faq-item:hover {
	box-shadow: 0 4px 12px rgba(0, 124, 186, 0.2);
	transform: translateY(-2px);
}

.faq-question {
	padding: 18px 20px;
	cursor: pointer;
	display: flex;
	justify-content: space-between;
	align-items: center;
	background: #222222;
	transition: all 0.3s ease;
	border-bottom: 1px solid transparent;
}

.faq-question:hover {
	background: #333333;
}

.faq-question h3 {
	margin: 0;
	font-size: 17px;
	font-weight: 600;
	color: #ffffff;
	line-height: 1.4;
}

.faq-toggle {
	font-size: 20px;
	font-weight: bold;
	color: #007cba;
	min-width: 30px;
	text-align: center;
	transition: all 0.3s ease;
}

.faq-item.active .faq-toggle {
	transform: rotate(45deg);
	color: #ffffff;
}

.faq-answer {
	padding: 0 20px;
	max-height: 0;
	overflow: hidden;
	transition: all 0.4s ease;
	background: #333333;
	border-top: 1px solid #444444;
}

.faq-item.active .faq-answer {
	padding: 20px;
	max-height: 1000px;
}

.faq-answer p {
	margin: 0 0 15px 0;
	color: #cccccc;
	line-height: 1.6;
	font-size: 15px;
}

.faq-answer p:last-child {
	margin-bottom: 0;
}

.faq-answer ol {
	margin: 0 0 15px 0;
	padding-left: 20px;
	color: #cccccc;
	line-height: 1.6;
}

.faq-answer ol li {
	margin-bottom: 8px;
	color: #cccccc;
}

.faq-answer strong {
	color: #ffffff;
	font-weight: 600;
}

.faq-answer a {
	color: #007cba;
	text-decoration: none;
	transition: color 0.3s ease;
}

.faq-answer a:hover {
	color: #0099e6;
	text-decoration: underline;
}

/* Active state styling */
.faq-item.active {
	border-color: #007cba;
	box-shadow: 0 4px 15px rgba(0, 124, 186, 0.3);
}

.faq-item.active .faq-question {
	background: linear-gradient(135deg, #007cba 0%, #005580 100%);
	border-bottom-color: #005580;
}

.faq-item.active .faq-question h3 {
	color: #ffffff;
}

.faq-item.active .faq-answer {
	background: #333333;
}

/* Responsive Design */
@media (max-width: 768px) {
	.faq-question {
		padding: 15px;
	}
	
	.faq-question h3 {
		font-size: 16px;
	}
	
	.faq-item.active .faq-answer {
		padding: 15px;
	}
	
	.faq-intro {
		padding: 15px;
	}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const faqItems = document.querySelectorAll('.faq-item');
	
	faqItems.forEach(item => {
		const question = item.querySelector('.faq-question');
		const answer = item.querySelector('.faq-answer');
		const toggle = item.querySelector('.faq-toggle');
		
		question.addEventListener('click', function() {
			const isActive = item.classList.contains('active');
			
			// Close all other FAQ items
			faqItems.forEach(otherItem => {
				if (otherItem !== item) {
					otherItem.classList.remove('active');
					otherItem.querySelector('.faq-toggle').textContent = '+';
				}
			});
			
			// Toggle current item
			if (isActive) {
				item.classList.remove('active');
				toggle.textContent = '+';
			} else {
				item.classList.add('active');
				toggle.textContent = '−';
			}
		});
	});
});
</script>

<?php get_footer(); ?>