<?php
/**
 * Template Name: Updated Games
 * Description: Displays games updated in the last 5 days based on version changes
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
                <header class="updated-games-header">
                    <h1 class="updated-games-title">
                        <span class="title-icon">🎮</span>
                        <?php the_title(); ?>
                    </h1>
                    <p class="header-subtitle">Track the latest game updates and version changes</p>
                </header>
                <?php
            }
            ?>
            
            <div class="updated-games-wrapper">
                <div class="updated-games-content">
                    <?php
                    // Get current date and time
                    $current_time = current_time( 'timestamp' );
                    $today_start = strtotime( 'today midnight', $current_time );
                    
                    // Display today's updates (visible by default)
                    ?>
                    <div class="day-section today-updates">
                        <div class="day-header">
                            <div class="header-content">
                                <span class="day-icon">📅</span>
                                <h2 class="day-title"><?php esc_html_e( "Today's Updates", 'jannah-child' ); ?></h2>
                                <?php
                                $today_args = array(
                                    'post_type'      => 'post',
                                    'posts_per_page' => -1,
                                    'meta_query'     => array(
                                        array(
                                            'key'     => '_game_version_updated',
                                            'value'   => $today_start,
                                            'compare' => '>=',
                                            'type'    => 'NUMERIC'
                                        )
                                    )
                                );
                                $today_count_query = new WP_Query( $today_args );
                                $today_count = $today_count_query->found_posts;
                                wp_reset_postdata();
                                ?>
                                <span class="update-count"><?php echo $today_count; ?> games</span>
                            </div>
                        </div>
                        <div class="games-list">
                            <?php
                            $today_args['orderby'] = 'meta_value_num';
                            $today_args['meta_key'] = '_game_version_updated';
                            $today_args['order'] = 'DESC';
                            
                            $today_query = new WP_Query( $today_args );
                            
                            if ( $today_query->have_posts() ) {
                                echo '<ul class="updated-games-list">';
                                while ( $today_query->have_posts() ) {
                                    $today_query->the_post();
                                    $game_version = get_post_meta( get_the_ID(), '_game_version', true );
                                    // Add 'v' prefix if it doesn't already start with 'v'
                                    if ( !empty( $game_version ) && !str_starts_with( strtolower( $game_version ), 'v' ) ) {
                                        $game_version = 'v' . $game_version;
                                    }
                                    $update_time = get_post_meta( get_the_ID(), '_game_version_updated', true );
                                    $time_ago = human_time_diff( $update_time, $current_time ) . ' ago';
                                    ?>
                                    <li class="game-item">
                                        <div class="game-bullet"></div>
                                        <div class="game-content">
                                            <div class="game-title-row">
                                                <a href="<?php the_permalink(); ?>" class="game-title"><?php the_title(); ?></a>
                                                <div class="game-meta">
                                                    <span class="version-badge"><?php echo esc_html( $game_version ); ?></span>
                                                    <span class="update-time"><?php echo esc_html( $time_ago ); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                }
                                echo '</ul>';
                            } else {
                                echo '<div class="no-updates"><span class="no-updates-icon">😴</span><p>' . esc_html__( 'No games updated today.', 'jannah-child' ) . '</p></div>';
                            }
                            
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                    
                    <?php
                    // Display previous 4 days in dropdown sections
                    for ( $i = 1; $i <= 4; $i++ ) {
                        $day_start = strtotime( "-{$i} days midnight", $current_time );
                        $day_end = strtotime( "-" . ($i - 1) . " days midnight", $current_time );
                        $day_label = date_i18n( 'l, F j', $day_start );
                        
                        // Get count for this day
                        $day_count_args = array(
                            'post_type'      => 'post',
                            'posts_per_page' => -1,
                            'meta_query'     => array(
                                array(
                                    'key'     => '_game_version_updated',
                                    'value'   => array( $day_start, $day_end ),
                                    'compare' => 'BETWEEN',
                                    'type'    => 'NUMERIC'
                                )
                            )
                        );
                        $day_count_query = new WP_Query( $day_count_args );
                        $day_count = $day_count_query->found_posts;
                        wp_reset_postdata();
                        
                        ?>
                        <div class="day-section previous-day">
                            <div class="day-header dropdown-toggle" data-day="<?php echo $i; ?>">
                                <div class="header-content">
                                    <span class="day-icon">📆</span>
                                    <h2 class="day-title"><?php echo $day_label; ?></h2>
                                    <span class="update-count"><?php echo $day_count; ?> games</span>
                                </div>
                                <span class="dropdown-arrow">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="games-list dropdown-content" style="display: none;">
                                <?php
                                $day_args = array(
                                    'post_type'      => 'post',
                                    'posts_per_page' => -1,
                                    'meta_query'     => array(
                                        array(
                                            'key'     => '_game_version_updated',
                                            'value'   => array( $day_start, $day_end ),
                                            'compare' => 'BETWEEN',
                                            'type'    => 'NUMERIC'
                                        )
                                    ),
                                    'orderby'        => 'meta_value_num',
                                    'meta_key'       => '_game_version_updated',
                                    'order'          => 'DESC'
                                );
                                
                                $day_query = new WP_Query( $day_args );
                                
                                if ( $day_query->have_posts() ) {
                                    echo '<ul class="updated-games-list">';
                                    while ( $day_query->have_posts() ) {
                                        $day_query->the_post();
                                        $game_version = get_post_meta( get_the_ID(), '_game_version', true );
                                        // Add 'v' prefix if it doesn't already start with 'v'
                                        if ( !empty( $game_version ) && !str_starts_with( strtolower( $game_version ), 'v' ) ) {
                                            $game_version = 'v' . $game_version;
                                        }
                                        $update_time = get_post_meta( get_the_ID(), '_game_version_updated', true );
                                        $time_ago = human_time_diff( $update_time, $current_time ) . ' ago';
                                        ?>
                                        <li class="game-item">
                                            <div class="game-bullet"></div>
                                            <div class="game-content">
                                                <div class="game-title-row">
                                                    <a href="<?php the_permalink(); ?>" class="game-title"><?php the_title(); ?></a>
                                                    <div class="game-meta">
                                                        <span class="version-badge"><?php echo esc_html( $game_version ); ?></span>
                                                        <span class="update-time"><?php echo esc_html( $time_ago ); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<div class="no-updates"><span class="no-updates-icon">😴</span><p>' . esc_html__( 'No games updated on this day.', 'jannah-child' ) . '</p></div>';
                                }
                                
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
/* Main wrapper styling */
.updated-games-wrapper {
    background: #1a1a1a;
    border-radius: 20px;
    padding: 30px;
    margin-top: 30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.05);
}

/* Header styling */
.updated-games-header {
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

.updated-games-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    opacity: 0.15;
    z-index: 0;
}

.updated-games-header::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    z-index: -1;
}

.updated-games-title {
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
    animation: bounce 2s infinite;
}

.header-subtitle {
    font-size: 18px;
    margin-top: 10px;
    color: #b8b8b8;
    position: relative;
    z-index: 1;
}

/* Day sections */
.day-section {
    margin-bottom: 20px;
    background: #2d2d2d;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.05);
    transition: all 0.3s ease;
}

.day-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    border-color: rgba(102, 126, 234, 0.2);
}

.day-header {
    padding: 20px 30px;
    background: #262626;
    border: none;
    color: #e0e0e0;
}

.today-updates .day-header {
    background: linear-gradient(135deg, #2d2d2d 0%, #3a3a3a 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.today-updates .day-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    opacity: 0.2;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
    z-index: 1;
}

.day-icon {
    font-size: 24px;
}

.day-title {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    flex: 1;
    color: inherit;
}

.update-count {
    background: rgba(102, 126, 234, 0.2);
    border: 1px solid rgba(102, 126, 234, 0.3);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    color: #b8b8ff;
}

.today-updates .update-count {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: white;
}

.dropdown-toggle {
    cursor: pointer;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background 0.3s ease;
}

.dropdown-toggle:hover {
    background: #333333;
}

.dropdown-arrow {
    transition: transform 0.3s ease;
    color: #999;
}

.dropdown-arrow svg {
    display: block;
}

.dropdown-toggle.active .dropdown-arrow {
    transform: rotate(180deg);
}

/* Games list */
.games-list {
    padding: 30px;
    background: #242424;
}

.updated-games-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.game-item {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding: 20px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    transition: all 0.3s ease;
    position: relative;
}

.game-item:last-child {
    border-bottom: none;
}

.game-item:hover {
    padding-left: 10px;
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
}

/* Custom bullet */
.game-bullet {
    width: 12px;
    height: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 6px;
    position: relative;
    box-shadow: 0 0 15px rgba(102, 126, 234, 0.5);
}

.game-bullet::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    background: rgba(102, 126, 234, 0.2);
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation: pulse 2s infinite;
}

.game-content {
    flex: 1;
}

.game-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.game-title {
    font-size: 18px;
    font-weight: 600;
    color: #e0e0e0;
    text-decoration: none;
    transition: color 0.3s ease;
    flex: 1;
}

.game-title:hover {
    color: #9ca3ff;
    text-shadow: 0 0 20px rgba(156, 163, 255, 0.3);
}

.game-meta {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-shrink: 0;
}

.version-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

.update-time {
    color: #777;
    font-size: 13px;
}

/* No updates message */
.no-updates {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-updates-icon {
    font-size: 48px;
    display: block;
    margin-bottom: 15px;
    opacity: 0.5;
}

.no-updates p {
    color: #666;
    font-size: 16px;
    margin: 0;
}

/* Animations */
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.5);
        opacity: 0;
    }
}

/* Responsive design */
@media (max-width: 768px) {
    .updated-games-wrapper {
        padding: 20px;
    }
    
    .updated-games-title {
        font-size: 32px;
    }
    
    .title-icon {
        font-size: 40px;
    }
    
    .day-header {
        padding: 15px 20px;
    }
    
    .day-title {
        font-size: 18px;
    }
    
    .games-list {
        padding: 20px;
    }
    
    .game-title {
        font-size: 16px;
    }
    
    .game-title-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .game-meta {
        flex-wrap: wrap;
        gap: 8px;
    }
}

/* Scrollbar styling for dark theme */
.updated-games-wrapper ::-webkit-scrollbar {
    width: 8px;
}

.updated-games-wrapper ::-webkit-scrollbar-track {
    background: #1a1a1a;
}

.updated-games-wrapper ::-webkit-scrollbar-thumb {
    background: #444;
    border-radius: 4px;
}

.updated-games-wrapper ::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.dropdown-toggle').on('click', function() {
        var $this = $(this);
        var $content = $this.next('.dropdown-content');
        
        $this.toggleClass('active');
        $content.slideToggle(300);
    });
});
</script>

<?php get_footer(); ?>