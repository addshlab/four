<?php // HEADER // ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset=<?php bloginfo('charset'); ?>>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?ver=<?php echo date( 'U' ); ?>" type="text/css" media="all" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php // GLOBAL HEADER // ?>

<header class="global header">

    <nav>
    <?php wp_nav_menu( array( 'theme_location' => 'header' ) ); ?>
    </nav>

    <div class="color-mode">
        <input id="color-mode" name="dark" <?php dark_mode_checked(); ?> type="checkbox">
        <label for="color-mode"></label>
    </div>

    <?php if ( is_home() || is_front_page() ) : ?>
    <h1>
    <?php else : ?>
    <p>
    <?php endif; ?>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php if ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
        <?php else : ?>
        <?php bloginfo( 'name' ); ?>
        <?php endif; ?>
        </a>
    <?php if ( is_home() || is_front_page() ) : ?>
    </h1>
    <?php else : ?>
    </p>
    <?php endif; ?>

    <?php if ( is_archive() ) : ?>
        <h1 class="subtitle"><?php the_archive_title(); ?>の記事一覧</h1>
    <?php endif; ?>
    
    <?php if ( ( is_single() || is_page() ) && !is_front_page() ) : ?>
        <h1 class="subtitle"><?php if ( mb_strlen( get_the_title() ) === 0 ) : echo '(no title)' ; else : the_title(); endif; ?></h1>
    <?php endif; ?>

    <?php if ( is_search() ) : ?>
        <h1 class="subtitle">"<em><?php the_search_query(); ?></em>"の検索結果</h1>
    <?php endif; ?>
</header>

<?php // CONTENTS // ?>

<main class="main">

<?php if ( ( get_option( 'show_on_front' ) !== 'page' || ! is_front_page() ) && is_active_sidebar( 'before-post-contents' ) ) : ?>
        <article>
        <?php dynamic_sidebar( 'before-post-contents' ); ?>
        </article>
<?php endif; ?>

<?php if ( is_404() ) : ?>
        <article>
            <div class="content">
                <h1 class="title">お探しのページが見つかりません。</h1>
                <p>そのページは存在しないか、移動した可能性があります。</p>
                </div>
        </article>
<?php elseif ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php
	// その日最初の投稿チェック
	if( $post_day_check !== get_post_time( 'Y-m-d', false ) ) {
		$sameDayFlag = true;
	} else {
		$sameDayFlag = false;
	}
?>

        <article id="post-<?php the_ID(); ?>" <?php post_class( $sameDayFlag ); ?>>
        <?php if( $sameDayFlag ) : ?>
<?php
/**
 * 投稿タイトルの表示
 * 投稿ページと固定ページではH1, それ以外ではH2でマークアップ
 */
?>
            <?php if ( is_home() || is_single() || is_archive() || is_search() ) : ?>
            <header>
                <ul class="meta">
                    <li class="post-date">
                        <time datetime="<?php echo get_post_time( 'Y-m-d\TH:i:s\Z', false ); ?>">
                        <?php echo get_post_time( 'Y-m-d', false ); ?>
                        </time>
                    </li>
                </ul>
            </header>
            <?php endif; ?>

        <?php endif; ?>
<?php
/**
 * 投稿内容の表示
 * 投稿ページと固定ページで表示
 */
?>
            <?php if ( is_single() || is_page() ) : ?>
            <div class="content">
            <?php the_content(); ?>
            </div> <!-- .content -->
            <?php wp_link_pages(); ?>
            <?php elseif ( is_archive() || is_home() || is_search() ) : ?>
            <div class="content">

            <h2 class="title">
                <a href="<?php the_permalink(); ?>">
                    <?php echo mb_substr( get_the_title(), 0, 100 ); ?><?php if ( mb_strlen( get_the_title() ) === 0 ) : echo '(no title)' ; elseif ( mb_strlen( get_the_title() ) >= 100 ) : echo ' ...'; endif; ?>
                </a>
            </h2>
            </div> <!-- .content -->
            <?php else : ?>
            <div class="excerpt">
                    <?php $str_count = mb_strlen( strip_tags( get_the_content() ) ); ?>
                    <?php echo mb_substr( strip_tags( get_the_content() ), 0, 10 ); ?><?php if ( $str_count === 0 ) : echo '(no content)' ; elseif ( $str_count >= 10 ) : echo ' ...'; endif; ?>
                    <span class="content-word-count"><?php echo $str_count . 'words.' ?></span>
                </div>
            <?php endif; ?>
<?php
/**
 * コメントの投稿とコメントのリストを表示
 * 投稿ページと固定ページで表示
 */
?>
            <?php if ( ( is_single() || is_page() ) && 1 === 0 ) : ?>
            <ol class="commentlist">
            <?php wp_list_comments(); ?>
            </ol>
            <?php comments_template(); ?>
            <?php paginate_comments_links(); ?>
            <?php endif; ?>

            <?php if ( ! is_front_page() && ( is_single() || is_page() ) ) : ?>
            <footer>
                <ul class="meta">
                    <li class="post-date">
                        <time datetime="<?php echo get_post_time( 'Y-m-d\TH:i:s\Z', false ); ?>">
                        <?php echo get_post_time( 'Y-m-d H:i:sP', false ); ?>
                        </time>(GMT: <time datetime="<?php echo get_post_time( 'Y-m-d\TH:i:s\Z', true ); ?>"><?php echo get_post_time( 'Y-m-d H:i:sP', true ); ?></time>)
                    </li>
                    <?php if ( get_post_time( 'U' ) !== get_post_modified_time( 'U' ) ) : ?>
                    <li class="last-modified">
                        <time datetime="<?php echo get_post_modified_time( 'Y-m-d\TH:i:s\Z', false ); ?>">
                        <?php echo get_post_modified_time( 'Y-m-d H:i:sP', false ); ?>
                        </time>(GMT: <time datetime="<?php echo get_post_modified_time( 'Y-m-d\TH:i:s\Z', true ); ?>"><?php echo get_post_modified_time( 'Y-m-d H:i:sP', true ); ?></time>)
                    </li>
                    <?php endif; ?>
                    <?php if ( has_category() ) : ?>
                    <li class="post-category"><?php the_category( ', ' ); ?></li>
                    <?php endif; ?>
                    <?php if ( has_tag() ) : ?>
                    <li class="post-tag"><?php the_tags( '', ', ', '' ); ?></li>
                    <?php endif; ?>
                </ul>
            </footer>


            <?php endif; ?>
        </article>
    
<?php
/**
 * アーカイブページで同じ日の投稿を検知するための変数
 */
$post_day_check = get_post_time( 'Y-m-d', false );
?>

<?php endwhile; ?>
<?php else: ?>
<article>
<div class="content">
<p>記事がありません。</p>
</div>
</article>
<?php endif; ?>

<?php if ( ( get_option( 'show_on_front' ) !== 'page' || ! is_front_page() ) && is_active_sidebar( 'after-post-contents' ) ) : ?>
        <article>
        <?php dynamic_sidebar( 'after-post-contents' ); ?>
        </article>
<?php endif; ?>

    <nav class="paginate_links">
        <?php echo paginate_links( array(
            'show_all' => true,
        ) ); ?>
    </nav>

</main>

<?php
/**
 * サイドバー
 * 一般的にsidebar.phpに含められる
 */
?>
<?php if ( ( get_option( 'show_on_front' ) !== 'page' || ! is_front_page() ) && is_active_sidebar( 'sidebar-menu' ) ) : ?>
<aside class="sidebar">
        <?php dynamic_sidebar( 'sidebar-menu' ); ?>
</aside>
<?php endif; ?>


<?php
/**
 * グローバルフッタ
 * 一般的にfooter.phpに含められる
 */
?>
<footer class="global footer">
    <nav>
    <?php wp_nav_menu( array( 'theme_location' => 'footer' ) ); ?>
    </nav>
</footer>
    
<?php wp_footer(); ?>
</body>
</html>
