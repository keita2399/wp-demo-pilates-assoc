<?php
/**
 * プラグインを使わずにできる基本の対策。
 * 本番では、これに加えてサーバー側のWAF、ログイン試行の制限、日次バックアップ（サーバー機能）を組み合わせる。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// WordPressのバージョンを外に出さない
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// 使わない機能を止める
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

// 投稿者のURL（/?author=1）からユーザー名が分かるのを防ぐ
add_action( 'template_redirect', function () {
	if ( is_author() || isset( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}, 0 );

// REST API からユーザー一覧を取れないようにする（ログインしていない人）
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
} );

// 管理画面でのファイル編集を止める
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

// コメントは使わない
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
