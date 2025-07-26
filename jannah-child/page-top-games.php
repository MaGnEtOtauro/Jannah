<?php
/**
 * Template Name: Top Games
 * Description: Displays the most viewed games using Block 10 module
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="section-item container">
    <div class="main-content-row">
        <div class="main-content tie-col-md-12">
            
            <?php
            // Page header
            if ( ! tie_get_postdata( 'tie_hide_title' ) ) {
                ?>
                <header class="top-games-header">
                    <h1 class="top-games-title">
                        <span class="title-icon">🏆</span>
                        <?php the_title(); ?>
                    </h1>
                    <p class="header-subtitle">Most popular games based on player views and engagement</p>
                </header>
                <?php
            }
            ?>
            
            <div class="top-games-wrapper">
                <div class="top-games-main">
                    <h2 class="section-title">
                        <span class="section-icon">🏆</span>
                        Most Popular Games
                    </h2>
                    
                    <?php
                    // Get games category if exists
                    $games_category = get_category_by_slug( 'games' );
                    
                    // Get current page number
                    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                    
                    // Most viewed games list using native theme functionality
                    $most_viewed_args = array(
                        'post_type'      => 'post',
                        'posts_per_page' => 15,  // Show 15 games per page
                        'paged'          => $paged,
                        'orderby'        => 'meta_value_num',
                        'meta_key'       => TIELABS_HELPER::get_views_meta_field(),
                        'order'          => 'DESC',
                        'meta_query'     => array(
                            array(
                                'key'     => TIELABS_HELPER::get_views_meta_field(),
                                'compare' => 'EXISTS'
                            )
                        )
                    );

                    // Add category filter if games category exists
                    if ( $games_category ) {
                        $most_viewed_args['cat'] = $games_category->term_id;
                    }

                    $most_viewed_query = new WP_Query( $most_viewed_args );
                    
                    if ( $most_viewed_query->have_posts() ) {
                        echo '<div class="games-grid">';
                        // Calculate starting rank based on current page
                        $rank = ( ( $paged - 1 ) * 15 ) + 1;
                        
                        while ( $most_viewed_query->have_posts() ) {
                            $most_viewed_query->the_post();
                            
                            $view_count = TIELABS_POSTVIEWS::get_views( '', get_the_ID() );
                            $post_date = get_the_date( 'M j, Y' );
                            $comments_count = get_comments_number();
                            
                            ?>
                            <div class="game-card">
                                
                                <div class="game-thumbnail">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail( 'tie-small', array( 'alt' => get_the_title() ) ); ?>
                                        </a>
                                    <?php else : ?>
                                        <div class="no-thumbnail">
                                            <span class="game-icon">🎮</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="game-overlay">
                                        <span class="view-count">
                                            <i class="tieicon-fire" aria-hidden="true"></i>
                                            <?php echo $view_count; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="game-info">
                                    <h3 class="game-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="game-excerpt">
                                        <?php echo wp_trim_words( get_the_excerpt(), 12, '...' ); ?>
                                    </div>
                                    
                                    <div class="game-meta">
                                        <span class="game-date">
                                            <i class="tieicon-calendar" aria-hidden="true"></i>
                                            <?php echo $post_date; ?>
                                        </span>
                                        
                                        <?php if ( $comments_count > 0 ) : ?>
                                            <span class="game-comments">
                                                <i class="tieicon-comment" aria-hidden="true"></i>
                                                <?php echo $comments_count; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $rank++;
                        }
                        
                        echo '</div>';
                        
                        // Display pagination
                        echo '<div class="top-games-pagination">';
                        echo paginate_links( array(
                            'total'        => $most_viewed_query->max_num_pages,
                            'current'      => $paged,
                            'format'       => '?paged=%#%',
                            'show_all'     => false,
                            'type'         => 'plain',
                            'end_size'     => 2,
                            'mid_size'     => 1,
                            'prev_next'    => true,
                            'prev_text'    => sprintf( '<i class="tieicon-arrow-left"></i> %s', esc_html__( 'Previous', 'jannah-child' ) ),
                            'next_text'    => sprintf( '%s <i class="tieicon-arrow-right"></i>', esc_html__( 'Next', 'jannah-child' ) ),
                            'add_args'     => false,
                            'add_fragment' => '',
                        ) );
                        echo '</div>';
                        
                    } else {
                        echo '<div class="no-games">
                                <span class="no-games-icon">🎯</span>
                                <p>' . esc_html__( 'No popular games found. Start viewing games to see them ranked here!', 'jannah-child' ) . '</p>
                              </div>';
                    }
                    
                    wp_reset_postdata();
                    ?>
                </div>
                
                <div class="top-games-stats">
                    <h2 class="section-title">
                        <span class="section-icon">📈</span>
                        Gaming Statistics
                    </h2>
                    
                    <div class="stats-grid">
                        <?php
                        // Get total games count
                        $total_games_args = array(
                            'post_type'      => 'post',
                            'posts_per_page' => -1,
                            'meta_query'     => array(
                                array(
                                    'key'     => TIELABS_HELPER::get_views_meta_field(),
                                    'compare' => 'EXISTS'
                                )
                            )
                        );
                        
                        if ( $games_category ) {
                            $total_games_args['cat'] = $games_category->term_id;
                        }
                        
                        $total_games_query = new WP_Query( $total_games_args );
                        $total_games = $total_games_query->found_posts;
                        wp_reset_postdata();
                        
                        // Get total views
                        $total_views = 0;
                        if ( $total_games_query->have_posts() ) {
                            while ( $total_games_query->have_posts() ) {
                                $total_games_query->the_post();
                                $views = get_post_meta( get_the_ID(), TIELABS_HELPER::get_views_meta_field(), true );
                                $total_views += intval( $views );
                            }
                            wp_reset_postdata();
                        }
                        
                        // Format numbers
                        $formatted_views = $total_views >= 1000000 ? number_format( $total_views / 1000000, 1 ) . 'M' : 
                                          ($total_views >= 1000 ? number_format( $total_views / 1000, 1 ) . 'K' : number_format( $total_views ));
                        ?>
                        
                        <div class="stat-card">
                            <div class="stat-icon">🎮</div>
                            <div class="stat-number"><?php echo number_format( $total_games ); ?></div>
                            <div class="stat-label">Total Games</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">👁️</div>
                            <div class="stat-number"><?php echo $formatted_views; ?></div>
                            <div class="stat-label">Total Views</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">⭐</div>
                            <div class="stat-number"><?php echo $total_games > 0 ? number_format( $total_views / $total_games ) : '0'; ?></div>
                            <div class="stat-label">Avg Views</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">🔥</div>
                            <div class="stat-number">Live</div>
                            <div class="stat-label">Rankings</div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
/* Top Games Page Styling */
.top-games-wrapper {
    background: #1a1a1a;
    border-radius: 20px;
    margin-top: 30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.05);
    overflow: hidden;
}

/* Header styling */
.top-games-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 40px 0;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border: 2px solid transparent;
    background-clip: padding-box;
    position: relative;
    border-radius: 20px;
    overflow: hidden;
}

.top-games-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    opacity: 0.15;
    z-index: 0;
}

.top-games-header::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 20px;
    z-index: -1;
}

.top-games-title {
    font-size: 48px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    color: #ffffff;
    position: relative;
    z-index: 1;
}

.title-icon {
    font-size: 56px;
    animation: trophy-glow 3s ease-in-out infinite;
}

.header-subtitle {
    font-size: 18px;
    margin-top: 10px;
    color: #b8b8b8;
    position: relative;
    z-index: 1;
}

/* Main games section */
.top-games-main {
    padding: 30px;
    background: #1f1f1f;
}

.section-title {
    font-size: 28px;
    font-weight: 600;
    color: #e0e0e0;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.section-icon {
    font-size: 32px;
}

/* Games grid */
.games-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.game-card {
    background: #2d2d2d;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.05);
    position: relative;
}

.game-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
    border-color: rgba(102, 126, 234, 0.3);
}


.game-thumbnail {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.game-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.game-card:hover .game-thumbnail img {
    transform: scale(1.1);
}

.no-thumbnail {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #333 0%, #444 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.game-icon {
    font-size: 48px;
    opacity: 0.5;
}

.game-overlay {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0,0,0,0.8);
    padding: 5px 10px;
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

.view-count {
    color: #ff6b6b;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.game-info {
    padding: 20px;
}

.game-title {
    margin: 0 0 10px 0;
    font-size: 18px;
    font-weight: 600;
}

.game-title a {
    color: #e0e0e0;
    text-decoration: none;
    transition: color 0.3s ease;
}

.game-title a:hover {
    color: #9ca3ff;
}

.game-excerpt {
    color: #999;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
}

.game-meta {
    display: flex;
    gap: 15px;
    font-size: 12px;
    color: #777;
}

.game-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Statistics section */
.top-games-stats {
    padding: 30px;
    background: #1a1a1a;
    border-top: 1px solid rgba(255,255,255,0.05);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-card {
    background: #2d2d2d;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    border-color: rgba(102, 126, 234, 0.2);
}

.stat-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #e0e0e0;
    margin-bottom: 5px;
}

.stat-label {
    color: #999;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Pagination */
.top-games-pagination {
    margin-top: 40px;
    text-align: center;
    padding: 30px 0;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.top-games-pagination .page-numbers {
    display: inline-block;
    padding: 12px 18px;
    margin: 0 5px;
    background: #2d2d2d;
    color: #e0e0e0;
    text-decoration: none;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
    font-weight: 500;
}

.top-games-pagination .page-numbers:hover,
.top-games-pagination .page-numbers.current {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.top-games-pagination .page-numbers.prev,
.top-games-pagination .page-numbers.next {
    padding: 12px 25px;
    font-weight: 600;
}

.top-games-pagination .page-numbers.dots {
    background: transparent;
    border: none;
    color: #666;
    cursor: default;
}

.top-games-pagination .page-numbers.dots:hover {
    background: transparent;
    transform: none;
    box-shadow: none;
}

/* No games message */
.no-games {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-games-icon {
    font-size: 48px;
    display: block;
    margin-bottom: 15px;
    opacity: 0.5;
}

.no-games p {
    color: #666;
    font-size: 16px;
    margin: 0;
}

/* Animations */
@keyframes trophy-glow {
    0%, 100% { transform: translateY(0) scale(1); filter: brightness(1); }
    50% { transform: translateY(-5px) scale(1.05); filter: brightness(1.2); }
}

/* Responsive design */
@media (max-width: 768px) {
    .top-games-wrapper {
        margin-top: 20px;
        border-radius: 15px;
    }
    
    .top-games-title {
        font-size: 32px;
    }
    
    .title-icon {
        font-size: 40px;
    }
    
    .top-games-block-10,
    .additional-top-games,
    .top-games-stats {
        padding: 20px;
    }
    
    .games-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .stat-number {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .section-title {
        font-size: 24px;
    }
}
</style>

<?php get_footer(); ?>