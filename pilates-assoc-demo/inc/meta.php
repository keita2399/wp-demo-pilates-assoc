<?php
/**
 * 入力欄（メタボックス）と保存処理。
 * 料金はコースの入力欄にだけ置く。開催日程には料金の欄を作らない。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 入力欄の定義（保存処理と画面の両方がこの定義を使う） */
function phi_meta_fields( $post_type ) {
	$courses = array( '' => '選択してください' );
	if ( in_array( $post_type, array( 'phi_course', 'phi_session', 'phi_instructor' ), true ) ) {
		foreach ( phi_get_courses() as $c ) {
			$courses[ $c->ID ] = $c->post_title;
		}
	}
	$prefs = array( '' => '選択してください' );
	foreach ( phi_all_prefectures() as $p ) {
		$prefs[ $p ] = $p;
	}

	switch ( $post_type ) {
		case 'phi_course':
			return array(
				'_phi_category'  => array( 'label' => '区分', 'type' => 'select', 'options' => phi_course_categories() ),
				'_phi_code'      => array( 'label' => 'コース記号', 'type' => 'text', 'help' => '例：MAT-1。旧サイトのURLとの対応にも使います。' ),
				'_phi_fee'       => array( 'label' => '受講料（税込・円）', 'type' => 'number', 'help' => 'ここを直すと、コース紹介・料金一覧・開催日程・カレンダー・本文中の料金表記がすべて変わります。' ),
				'_phi_exam_fee'  => array( 'label' => '認定試験料（税込・円）', 'type' => 'number', 'help' => '試験が無いコースは 0' ),
				'_phi_hours'     => array( 'label' => '受講時間', 'type' => 'text', 'help' => '例：全4日（計24時間）' ),
				'_phi_ceu'       => array( 'label' => '取得できるCEU', 'type' => 'number', 'help' => '資格更新に使えるポイント数' ),
				'_phi_prereq'    => array( 'label' => '受講の前提となるコース', 'type' => 'select', 'options' => array( '' => 'なし' ) + array_diff_key( $courses, array( '' => '' ) ) ),
				'_phi_target'    => array( 'label' => '対象となる方', 'type' => 'text' ),
				'_phi_apply_url' => array( 'label' => '申込フォームのURL（外部サービス）', 'type' => 'url', 'help' => '日程ごとに別のフォームがある場合は、日程の側で上書きできます。' ),
			);
		case 'phi_session':
			return array(
				'_phi_course_id'  => array( 'label' => 'コース', 'type' => 'select', 'options' => $courses, 'help' => '料金はコースから自動で表示されます。ここには入力しません。' ),
				'_phi_start'      => array( 'label' => '開始日', 'type' => 'date' ),
				'_phi_end'        => array( 'label' => '終了日', 'type' => 'date', 'help' => '1日だけの場合は空欄' ),
				'_phi_days_label' => array( 'label' => '日程の補足', 'type' => 'text', 'help' => '例：土日2回×2週、各10:00〜17:00' ),
				'_phi_venue'      => array( 'label' => '会場', 'type' => 'text' ),
				'_phi_prefecture' => array( 'label' => '都道府県', 'type' => 'select', 'options' => $prefs ),
				'_phi_format'     => array( 'label' => '開催形式', 'type' => 'select', 'options' => phi_session_formats() ),
				'_phi_status'     => array( 'label' => '受付状況', 'type' => 'select', 'options' => phi_session_statuses() ),
				'_phi_trainer'    => array( 'label' => '担当マスタートレーナー', 'type' => 'text' ),
				'_phi_apply_url'  => array( 'label' => '申込フォームのURL（この日程だけ別の場合）', 'type' => 'url', 'help' => '空欄ならコースのフォームを使います。' ),
			);
		case 'phi_studio':
			return array(
				'_phi_prefecture' => array( 'label' => '都道府県', 'type' => 'select', 'options' => $prefs ),
				'_phi_city'       => array( 'label' => '市区町村', 'type' => 'text' ),
				'_phi_address'    => array( 'label' => '住所（番地以降）', 'type' => 'text' ),
				'_phi_url'        => array( 'label' => 'スタジオのWebサイト', 'type' => 'url' ),
				'_phi_equipment'  => array( 'label' => '使える器具', 'type' => 'checkboxes', 'options' => array( 'mat' => 'マット', 'reformer' => 'リフォーマー', 'chair' => 'チェア', 'cadillac' => 'キャデラック', 'barrel' => 'バレル' ) ),
				'_phi_trial'      => array( 'label' => '体験レッスン', 'type' => 'select', 'options' => array( 'yes' => 'あり', 'no' => 'なし' ) ),
			);
		case 'phi_instructor':
			$studios = array( '' => '所属なし（フリー）' );
			foreach ( get_posts( array( 'post_type' => 'phi_studio', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) as $s ) {
				$studios[ $s->ID ] = $s->post_title;
			}
			return array(
				'_phi_level'      => array( 'label' => '資格', 'type' => 'select', 'options' => array( 'instructor' => '認定インストラクター', 'master' => 'マスタートレーナー' ) ),
				'_phi_prefecture' => array( 'label' => '活動地域（都道府県）', 'type' => 'select', 'options' => $prefs ),
				'_phi_studio_id'  => array( 'label' => '所属スタジオ', 'type' => 'select', 'options' => $studios ),
				'_phi_certs'      => array( 'label' => '修了コース', 'type' => 'checkboxes', 'options' => array_diff_key( $courses, array( '' => '' ) ) ),
				'_phi_public'     => array( 'label' => '一覧への掲載', 'type' => 'select', 'options' => array( 'yes' => '掲載する', 'no' => '掲載しない（本人の希望）' ) ),
			);
	}
	return array();
}

add_action( 'add_meta_boxes', function () {
	$titles = array(
		'phi_course'     => 'コースの基本情報と料金',
		'phi_session'    => '開催の内容',
		'phi_studio'     => 'スタジオの情報',
		'phi_instructor' => 'インストラクターの情報',
	);
	foreach ( $titles as $pt => $title ) {
		add_meta_box( 'phi_meta_' . $pt, $title, 'phi_render_meta_box', $pt, 'normal', 'high' );
	}
	add_meta_box( 'phi_price_usage', 'この料金が表示されている場所', 'phi_render_price_usage', 'phi_course', 'side', 'default' );
} );

function phi_render_meta_box( $post ) {
	wp_nonce_field( 'phi_save_meta', 'phi_meta_nonce' );
	$fields = phi_meta_fields( $post->post_type );
	echo '<table class="form-table phi-meta"><tbody>';
	foreach ( $fields as $key => $f ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $f['label'] ) . '</label></th><td>';
		switch ( $f['type'] ) {
			case 'select':
				echo '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '">';
				foreach ( $f['options'] as $ov => $ol ) {
					echo '<option value="' . esc_attr( $ov ) . '"' . selected( (string) $val, (string) $ov, false ) . '>' . esc_html( $ol ) . '</option>';
				}
				echo '</select>';
				break;
			case 'checkboxes':
				$vals = is_array( $val ) ? array_map( 'strval', $val ) : array();
				echo '<fieldset>';
				foreach ( $f['options'] as $ov => $ol ) {
					echo '<label style="display:inline-block;margin:0 1em .3em 0"><input type="checkbox" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $ov ) . '"' . checked( in_array( (string) $ov, $vals, true ), true, false ) . '> ' . esc_html( $ol ) . '</label>';
				}
				echo '</fieldset>';
				break;
			case 'number':
				echo '<input type="number" min="0" step="1" class="regular-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
				break;
			case 'date':
				echo '<input type="date" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
				break;
			case 'url':
				echo '<input type="url" class="large-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" placeholder="https://">';
				break;
			default:
				echo '<input type="text" class="large-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
		}
		if ( ! empty( $f['help'] ) ) {
			echo '<p class="description">' . esc_html( $f['help'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

/** コース編集画面の右側：この料金がどこに出ているか */
function phi_render_price_usage( $post ) {
	$fees = phi_course_fees( $post->ID );
	echo '<p style="font-size:14px"><strong>' . esc_html( $fees['total'] ? phi_yen( $fees['total'] ) . '（税込・試験料込み）' : '未設定' ) . '</strong></p>';
	echo '<p>この値は次の場所に自動で表示されます。ほかのページで料金を書き直す必要はありません。</p><ul style="list-style:disc;padding-left:1.4em">';
	$places = array(
		'このコースの紹介ページ'   => get_permalink( $post ),
		'料金一覧'                 => phi_page_url( 'fees' ),
		'開催日程（カレンダー）'   => add_query_arg( 'course', $post->ID, phi_page_url( 'schedule' ) ),
		'トップページの次回開催'   => home_url( '/' ),
	);
	if ( 'ceu' === get_post_meta( $post->ID, '_phi_category', true ) ) {
		$places['資格更新（CEU）のご案内'] = phi_page_url( 'ceu' );
	}
	foreach ( $places as $label => $url ) {
		echo '<li><a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $label ) . '</a></li>';
	}
	// 本文中にショートコードで料金を書いているページ
	global $wpdb;
	$slug = $post->post_name;
	if ( $slug ) {
		$like = '%' . $wpdb->esc_like( '[course_price course="' . $slug . '"' ) . '%';
		$ids  = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_status='publish' AND post_content LIKE %s", $like ) );
		foreach ( $ids as $id ) {
			echo '<li><a href="' . esc_url( get_permalink( $id ) ) . '" target="_blank">' . esc_html( get_the_title( $id ) ) . '</a>（本文中の料金表記）</li>';
		}
	}
	$count = count( phi_get_sessions( array( 'course' => $post->ID ) ) );
	echo '</ul><p>今後の開催日程：' . (int) $count . '件（すべてこの料金で表示）</p>';
}

add_action( 'save_post', function ( $post_id, $post ) {
	if ( ! isset( $_POST['phi_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['phi_meta_nonce'] ) ), 'phi_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( phi_meta_fields( $post->post_type ) as $key => $f ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			$raw = null;
		} elseif ( 'url' === $f['type'] ) {
			$raw = esc_url_raw( wp_unslash( $_POST[ $key ] ) );
		} else {
			$raw = map_deep( wp_unslash( $_POST[ $key ] ), 'sanitize_text_field' );
		}
		switch ( $f['type'] ) {
			case 'checkboxes':
				$val = is_array( $raw ) ? array_values( array_intersect( array_map( 'strval', $raw ), array_map( 'strval', array_keys( $f['options'] ) ) ) ) : array();
				break;
			case 'select':
				$val = ( null !== $raw && array_key_exists( (string) $raw, array_map( 'strval', $f['options'] ) ) ) ? sanitize_text_field( $raw ) : '';
				break;
			case 'number':
				$val = ( null === $raw || '' === $raw ) ? '' : max( 0, (int) $raw );
				break;
			case 'date':
				$val = ( is_string( $raw ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ) ? $raw : '';
				break;
			case 'url':
				$val = esc_url_raw( (string) $raw );
				break;
			default:
				$val = sanitize_text_field( (string) $raw );
		}
		update_post_meta( $post_id, $key, $val );
	}
	if ( 'phi_session' === $post->post_type ) {
		$end = get_post_meta( $post_id, '_phi_end', true );
		if ( ! $end ) {
			update_post_meta( $post_id, '_phi_end', get_post_meta( $post_id, '_phi_start', true ) );
		}
		phi_session_autotitle( $post_id );
	}
}, 10, 2 );

/** 開催日程のタイトルを自動で作る */
function phi_session_autotitle( $post_id ) {
	$course = (int) get_post_meta( $post_id, '_phi_course_id', true );
	$start  = get_post_meta( $post_id, '_phi_start', true );
	$venue  = get_post_meta( $post_id, '_phi_venue', true );
	$title  = trim( ( $course ? get_the_title( $course ) : 'コース未設定' ) . ' ' . $start . ' ' . $venue );
	// 保存処理から呼ばれるので、二重に走らないよう印を外してから更新する。
	unset( $_POST['phi_meta_nonce'] );
	wp_update_post(
		array(
			'ID'         => $post_id,
			'post_title' => $title,
			'post_name'  => sanitize_title( 'session-' . $post_id ),
		)
	);
}
