<?php
/**
 * 試作テーマ：ピラティス指導者養成団体サイト（架空の団体・ダミーデータ）
 *
 * 方針
 * 1. 外部プラグインを使わない。必要な機能はこのテーマ内の inc/ に置き、ファイル単位で用途が分かるようにする。
 * 2. 料金はコースにだけ持たせる。日程・料金一覧・カレンダー・本文中の表記は、すべてコースの値を読みに行く。
 * 3. 事務局が触る画面は、入力欄を決め打ちにして、レイアウトを崩せないようにする。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PHI_DEMO_VERSION', '1.0.0' );
define( 'PHI_DEMO_DIR', get_template_directory() );
define( 'PHI_DEMO_URI', get_template_directory_uri() );

require_once PHI_DEMO_DIR . '/inc/helpers.php';
require_once PHI_DEMO_DIR . '/inc/post-types.php';
require_once PHI_DEMO_DIR . '/inc/meta.php';
require_once PHI_DEMO_DIR . '/inc/shortcodes.php';
require_once PHI_DEMO_DIR . '/inc/redirects.php';
require_once PHI_DEMO_DIR . '/inc/admin.php';
require_once PHI_DEMO_DIR . '/inc/roles.php';
require_once PHI_DEMO_DIR . '/inc/security.php';
require_once PHI_DEMO_DIR . '/inc/seed.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array( 'primary' => 'グローバルナビ' ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'phi-demo', get_stylesheet_uri(), array(), PHI_DEMO_VERSION );
	wp_enqueue_script( 'phi-demo', PHI_DEMO_URI . '/assets/site.js', array(), PHI_DEMO_VERSION, true );
} );

// テーマを有効にしたときにも、ダミーデータが無ければ入れる（Playground 以外で試す場合のため）。
add_action( 'after_switch_theme', function () {
	if ( function_exists( 'phi_demo_seed' ) ) {
		phi_demo_seed();
	}
} );
