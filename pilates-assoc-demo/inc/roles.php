<?php
/**
 * 権限グループ「事務局」。
 * お知らせ・日程・料金・スタジオ・固定ページの文章は更新できる。
 * デザイン・プラグイン・ユーザー・設定・転送表には触れない。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function phi_add_roles() {
	remove_role( 'jimukyoku' );
	add_role(
		'jimukyoku',
		'事務局',
		array(
			'read'                   => true,
			'upload_files'           => true,
			'edit_posts'             => true,
			'edit_others_posts'      => true,
			'edit_published_posts'   => true,
			'publish_posts'          => true,
			'delete_posts'           => true,
			'delete_published_posts' => true,
			'delete_others_posts'    => true,
			'edit_pages'             => true,
			'edit_others_pages'      => true,
			'edit_published_pages'   => true,
			'publish_pages'          => false,
			'delete_pages'           => false,
			'manage_categories'      => true,
		)
	);
}

// 事務局の管理画面から、触らせない項目を消す
add_action( 'admin_menu', function () {
	$user = wp_get_current_user();
	if ( ! in_array( 'jimukyoku', (array) $user->roles, true ) ) {
		return;
	}
	foreach ( array( 'edit-comments.php', 'tools.php', 'themes.php', 'plugins.php', 'users.php', 'options-general.php' ) as $slug ) {
		remove_menu_page( $slug );
	}
}, 999 );
