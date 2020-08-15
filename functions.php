<?php
if ( ! isset( $content_width ) ) {
    $content_width = 600;
}

/**
 * 除外フィルタ
 */
remove_filter( 'the_content', 'wptexturize' );

/**
 * テーマにコンポーネントされていないファイルの検知
 */
function no_component_files() {
    global $pagenow;
    if ( $pagenow !== 'index.php' ) return;

    $message = '<p>fourテーマのリリースビルドに不要なファイルが含まれています。</p>';
    $flag = 0;
    $theme_dir_path = get_stylesheet_directory();
    $allow_files = array(
        $theme_dir_path . '/functions.php',
        $theme_dir_path . '/index.php',
        $theme_dir_path . '/screenshot.png',
        $theme_dir_path . '/style.css',
    );

    foreach ( glob( $theme_dir_path . '/*', GLOB_BRACE ) as $file_path ) {
        if( is_dir( $file_path ) ){
            if ( false === in_array( $file_path, $allow_files ) ) {
                $dir_alert .= '<p>テーマディレクトリ内にコンポーネント外のディレクトリ ' . $file_path . ' が存在します。</p>';
                $flag = 1;
            }
        }
        if( is_file( $file_path ) ){
            if ( false === in_array( $file_path, $allow_files ) ) {
                $file_alert .= '<p>テーマディレクトリ内にコンポーネント外のファイル ' . $file_path . ' が存在します。</p>';
                $flag = 1;
            }
        }
    }

    if ( $flag === 1 ) {
        echo '<div class="message error">';
        echo $message;
        echo $dir_alert;
        echo $file_alert;
        echo '</div>';
    }
}
add_action ( 'admin_notices', 'no_component_files' );

/**
 * 管理画面以外でもDashiconsを使用する
 * DashiconsはWordPress3.8以上で使用可能なアイコンフォントセットです
 * @see https://developer.wordpress.org/resource/dashicons/
 */
function enqueue_scripts() {
    wp_enqueue_style( 'dashicons', site_url( '/' ) . '/wp-includes/css/dashicons.min.css' );
    wp_enqueue_script( 'js-cookie', get_template_directory_uri() . '/lib/js.cookie.min.js', array(), '2.2.1', false );
}
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

/**
 * ダークモード切替
 */
function darkmode_js() {
    $output = <<<EOM
<script>
var toggle_color = ''
var os_color = ''
const saved_value = Cookies.get('color_theme_value');

if (!saved_value) {
    // Cookieが存在しなければOSのダークモード判定
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-mode', 'dark')
        var os_color = 'dark'
        // Cookieが存在しなければトグル用のチェックボックスをcheckedにする
	jQuery('#color-mode').prop('checked', true);
    } else {
        document.documentElement.setAttribute('data-mode', 'light')
        var os_color = 'light'
    }
} else {
    if (saved_value=='dark') {
        document.documentElement.setAttribute('data-mode', 'dark')
        //var os_color = 'dark'
    } else {
        document.documentElement.setAttribute('data-mode', 'light')
        //var os_color = 'light'
    }
}

window.matchMedia('(prefers-color-scheme: dark)').addListener(e => {
  if (e.matches) {
    document.documentElement.setAttribute('data-mode', 'dark')
  } else {
    document.documentElement.setAttribute('data-mode', 'light')
    var color = 'light'
  }
});


// トグルボタンでダークモード切り替え
const btn = document.querySelector("#color-mode");
 
btn.addEventListener("change", () => {
  if (btn.checked == true) {
    document.documentElement.setAttribute('data-mode', 'dark')
    toggle_color = 'dark'
  } else {
    document.documentElement.setAttribute('data-mode', 'light')
    toggle_color = 'light'
  }
});

// Cookie保存

jQuery("#color-mode").change(function(e){
    if (!toggle_color) {
        save_value = os_color
    } else {
        save_value = toggle_color
    }
    Cookies.set('color_theme_value', save_value , { expires: 7 });
});
</script>
EOM;
    echo $output;
}
add_action( 'wp_footer', 'darkmode_js' );

function dark_mode_checked() {
    if ( $color_theme_value = $_COOKIE['color_theme_value'] ) {
	if ( 'dark' === $color_theme_value ) {
            echo 'checked';
	} else {
	    echo '';
	}
    } else {
        echo '';
    }
}

/**
 * style.cssにファイルタイムスタンプ由来のフィンガープリントを付与
 * @see https://worklog.be/archives/2983
*/
function default_style_fingerprint( $styles ) {
    $mtime = filemtime( get_stylesheet_directory() . '/style.css' );
    $styles->default_version = $mtime;
}
add_action( 'wp_default_styles', 'default_style_fingerprint' );

/**
 * テーマのサポートする機能の定義
 */
function custom_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'editor-style.css' );
}
add_action( 'after_setup_theme', 'custom_theme_setup' );

/**
 * タイトルタグのカスタマイズ
 */
function wpdocs_hack_wp_title_for_home( $title ) {
    if ( empty( $title ) && ( is_home() || is_front_page() ) ) {
        $title = get_bloginfo( 'name' );
    } elseif ( is_single() ) {
        $title = get_the_title() . ' | ' . get_bloginfo( 'name' );
    } else {
        $title = get_the_title();
    }
    return $title;
}
add_filter( 'pre_get_document_title', 'wpdocs_hack_wp_title_for_home' );

/**
 * Meta Description のカスタマイズ
 */
 function add_meta_description() {
     global $post;

     if ( is_home() ) {
        $content = get_bloginfo( 'description' );
    } elseif ( is_singular() ) {
        if ( $post->post_except ) {
            $content = esc_attr( $post->post_excerpt );
        } else {
            $content = esc_attr( strip_tags( $post->post_content ) );
            $content = preg_replace( "/[\r\n]/", " ", $content );
             $content = mb_strimwidth( $content, 0, 100, "..." );    
        }
    } else {
        return;
    }

     echo '<meta name="description" content="' . $content . '">';
 }
add_filter( 'wp_head', 'add_meta_description' );

/**
 * アーカイブタイトルの整形
 */
add_filter( 'get_the_archive_title', function ( $archive_title ) {    
global $wp_locale;
    if ( is_category() ) {    
        $archive_title = single_cat_title( '', false );    
        } elseif ( is_tag() ) {    
            $archive_title = single_tag_title( '', false );    
        } elseif ( is_month() ) {
            $archive_title = get_query_var( 'year' ) . '年' .get_query_var( 'monthnum' ) . '月';    
        } elseif ( is_year() ) {
            $archive_title = get_query_var( 'year' ) . '年';    
        } elseif ( is_author() ) {    
            $archive_title = '<span class="vcard">' . get_the_author() . '</span>' ;    
        } elseif ( is_tax() ) { //for custom post types
            $archive_title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
        } elseif (is_post_type_archive()) {
            $archive_title = post_type_archive_title( '', false );
        }
    return $archive_title;    
} );

/**
 * メニューの定義
 */
function menu_init() {
    $menus = array(
        'header' => 'Header Menu',
        'footer' => 'Footer Menu',
    );
    register_nav_menus( $menus );
}
add_action( 'after_setup_theme', 'menu_init' );

/**
 * ウィジェットエリアの定義
 */
function theme_slug_widgets_init() {
    register_sidebar(
        array(
            'name' => __( 'Sidebar menu', 'four' ),
            'id' => 'sidebar-menu',
            'description' => __( 'Display menu to sidebar.', 'four' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h2 class="widgettitle">',
            'after_title'   => '</h2>',
        )

    );
    register_sidebar(
        array(
            'name' => __( 'Before post contents', 'four' ),
            'id' => 'before-post-contents',
            'description' => __( 'Display before post contents.', 'four' ),
            'before_widget' => '<div class="content">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
        )
    );
    register_sidebar(
        array(
            'name' => __( 'After post contents', 'four' ),
            'id' => 'after-post-contents',
            'description' => __( 'Display after post contents.', 'four' ),
            'before_widget' => '<div class="content">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'theme_slug_widgets_init' );

