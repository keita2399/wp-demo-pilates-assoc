<?php
/**
 * 独自の投稿タイプ。
 * コース（料金を持つ唯一の場所）／開催日程（料金を持たない）／認定スタジオ／認定インストラクター
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {

	register_post_type(
		'phi_course',
		array(
			'labels'        => array(
				'name'          => 'コース',
				'singular_name' => 'コース',
				'add_new'       => 'コースを追加',
				'add_new_item'  => 'コースを追加',
				'edit_item'     => 'コースを編集（料金はここだけで管理）',
				'all_items'     => 'コース一覧',
				'menu_name'     => 'コースと料金',
			),
			'public'        => true,
			'has_archive'   => 'courses',
			'rewrite'       => array(
				'slug'       => 'courses',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-welcome-learn-more',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	register_post_type(
		'phi_session',
		array(
			'labels'              => array(
				'name'          => '開催日程',
				'singular_name' => '開催日程',
				'add_new'       => '日程を追加',
				'add_new_item'  => '日程を追加',
				'edit_item'     => '日程を編集',
				'all_items'     => '開催日程一覧',
				'menu_name'     => '開催日程',
			),
			'public'              => false,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-calendar-alt',
			'menu_position'       => 6,
			'supports'            => array( 'title' ),
			'show_in_rest'        => false,
		)
	);

	register_post_type(
		'phi_studio',
		array(
			'labels'        => array(
				'name'          => '認定スタジオ',
				'singular_name' => '認定スタジオ',
				'add_new'       => 'スタジオを追加',
				'add_new_item'  => 'スタジオを追加',
				'edit_item'     => 'スタジオを編集',
				'all_items'     => '認定スタジオ一覧',
			),
			'public'        => true,
			'has_archive'   => false,
			'rewrite'       => array(
				'slug'       => 'studio',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-location',
			'menu_position' => 7,
			'supports'      => array( 'title', 'editor' ),
			'show_in_rest'  => true,
		)
	);

	register_post_type(
		'phi_instructor',
		array(
			'labels'              => array(
				'name'          => '認定インストラクター',
				'singular_name' => '認定インストラクター',
				'add_new'       => 'インストラクターを追加',
				'add_new_item'  => 'インストラクターを追加',
				'edit_item'     => 'インストラクターを編集',
				'all_items'     => '認定インストラクター一覧',
			),
			'public'              => false,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-groups',
			'menu_position'       => 8,
			'supports'            => array( 'title' ),
			'show_in_rest'        => false,
		)
	);
} );

// 事務局が迷わないよう、独自投稿はブロックエディタではなく入力欄が決まった画面にする。
add_filter( 'use_block_editor_for_post_type', function ( $use, $post_type ) {
	if ( in_array( $post_type, array( 'phi_course', 'phi_session', 'phi_studio', 'phi_instructor' ), true ) ) {
		return false;
	}
	return $use;
}, 10, 2 );

// 開催日程とインストラクターはタイトルを手で入れさせない（保存時に自動で作る）。
add_filter( 'enter_title_here', function ( $text, $post ) {
	if ( 'phi_session' === $post->post_type ) {
		return '（空欄のままで構いません。保存時に「コース名＋日付＋会場」で自動作成します）';
	}
	if ( 'phi_instructor' === $post->post_type ) {
		return '氏名';
	}
	return $text;
}, 10, 2 );
