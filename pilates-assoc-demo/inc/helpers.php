<?php
/**
 * 共通の関数。料金・日程の「1か所から読む」処理はすべてここを通す。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 地方と都道府県（スタジオ検索の絞り込みに使う） */
function phi_regions() {
	return array(
		'北海道・東北' => array( '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県' ),
		'関東'         => array( '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県' ),
		'中部'         => array( '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県' ),
		'近畿'         => array( '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県' ),
		'中国・四国'   => array( '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県' ),
		'九州・沖縄'   => array( '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県' ),
	);
}

function phi_all_prefectures() {
	$all = array();
	foreach ( phi_regions() as $prefs ) {
		$all = array_merge( $all, $prefs );
	}
	return $all;
}

function phi_region_of( $pref ) {
	foreach ( phi_regions() as $region => $prefs ) {
		if ( in_array( $pref, $prefs, true ) ) {
			return $region;
		}
	}
	return '';
}

/** コースの区分 */
function phi_course_categories() {
	return array(
		'mat'     => 'マット',
		'machine' => 'マシン',
		'special' => '専門コース',
		'ceu'     => '資格更新（CEU）講座',
	);
}

/** 開催の状態 */
function phi_session_statuses() {
	return array(
		'open'     => '受付中',
		'few'      => '残りわずか',
		'full'     => '満席',
		'closed'   => '受付終了',
		'upcoming' => '受付開始前',
	);
}

/** 開催形式 */
function phi_session_formats() {
	return array(
		'onsite' => '対面',
		'online' => 'オンライン',
		'hybrid' => '対面＋オンライン',
	);
}

/** 円表記 */
function phi_yen( $amount ) {
	$amount = (int) $amount;
	return number_format( $amount ) . '円';
}

/**
 * コースの料金（唯一の出どころ）。
 * どの画面もこの関数で料金を読むので、コースの編集画面で直せば全ページが変わる。
 */
function phi_course_fees( $course_id ) {
	$course_id = (int) $course_id;
	$fee       = (int) get_post_meta( $course_id, '_phi_fee', true );
	$exam      = (int) get_post_meta( $course_id, '_phi_exam_fee', true );
	return array(
		'fee'   => $fee,
		'exam'  => $exam,
		'total' => $fee + $exam,
	);
}

/** 料金の短い表記（税込・合計） */
function phi_course_price_label( $course_id ) {
	$f = phi_course_fees( $course_id );
	if ( $f['total'] <= 0 ) {
		return '料金未設定';
	}
	return phi_yen( $f['total'] ) . '（税込）';
}

/** コースの区分ラベル */
function phi_course_category_label( $course_id ) {
	$cats = phi_course_categories();
	$key  = get_post_meta( $course_id, '_phi_category', true );
	return isset( $cats[ $key ] ) ? $cats[ $key ] : '';
}

/** コースをスラッグか ID で引く */
function phi_find_course( $ref ) {
	if ( is_numeric( $ref ) ) {
		$p = get_post( (int) $ref );
		return ( $p && 'phi_course' === $p->post_type ) ? $p : null;
	}
	$p = get_page_by_path( sanitize_title( $ref ), OBJECT, 'phi_course' );
	return $p ? $p : null;
}

/** 公開中のコース一覧（並び順つき） */
function phi_get_courses( $category = '' ) {
	$args = array(
		'post_type'      => 'phi_course',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
	);
	if ( $category ) {
		$args['meta_query'] = array(
			array(
				'key'   => '_phi_category',
				'value' => $category,
			),
		);
	}
	return get_posts( $args );
}

/**
 * 開催日程を取る。
 * $args: course（ID）, from（Y-m-d）, to（Y-m-d）, region, format, limit
 */
function phi_get_sessions( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'course' => 0,
			'from'   => wp_date( 'Y-m-d' ),
			'to'     => '',
			'region' => '',
			'format' => '',
			'limit'  => -1,
		)
	);

	$meta = array( 'relation' => 'AND' );
	if ( $args['from'] ) {
		$meta[] = array(
			'key'     => '_phi_end',
			'value'   => $args['from'],
			'compare' => '>=',
			'type'    => 'DATE',
		);
	}
	if ( $args['to'] ) {
		$meta[] = array(
			'key'     => '_phi_start',
			'value'   => $args['to'],
			'compare' => '<=',
			'type'    => 'DATE',
		);
	}
	if ( $args['course'] ) {
		$meta[] = array(
			'key'   => '_phi_course_id',
			'value' => (int) $args['course'],
		);
	}
	if ( $args['region'] ) {
		$prefs = phi_regions();
		if ( isset( $prefs[ $args['region'] ] ) ) {
			$meta[] = array(
				'key'     => '_phi_prefecture',
				'value'   => $prefs[ $args['region'] ],
				'compare' => 'IN',
			);
		}
	}
	if ( $args['format'] ) {
		$meta[] = array(
			'key'   => '_phi_format',
			'value' => $args['format'],
		);
	}

	$q = new WP_Query(
		array(
			'post_type'      => 'phi_session',
			'post_status'    => 'publish',
			'posts_per_page' => (int) $args['limit'],
			'meta_key'       => '_phi_start',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => $meta,
			'no_found_rows'  => true,
		)
	);
	return $q->posts;
}

/** 開催の表示用データ（料金はコースから読む） */
function phi_session_data( $session ) {
	$session   = get_post( $session );
	$course_id = (int) get_post_meta( $session->ID, '_phi_course_id', true );
	$start     = get_post_meta( $session->ID, '_phi_start', true );
	$end       = get_post_meta( $session->ID, '_phi_end', true );
	$status    = get_post_meta( $session->ID, '_phi_status', true );
	$format    = get_post_meta( $session->ID, '_phi_format', true );
	$statuses  = phi_session_statuses();
	$formats   = phi_session_formats();
	$apply     = get_post_meta( $session->ID, '_phi_apply_url', true );
	if ( ! $apply && $course_id ) {
		$apply = get_post_meta( $course_id, '_phi_apply_url', true );
	}
	return array(
		'id'           => $session->ID,
		'course_id'    => $course_id,
		'course_title' => $course_id ? get_the_title( $course_id ) : '（コース未設定）',
		'course_url'   => $course_id ? get_permalink( $course_id ) : '',
		'start'        => $start,
		'end'          => $end ? $end : $start,
		'date_label'   => phi_date_range_label( $start, $end ),
		'days_label'   => get_post_meta( $session->ID, '_phi_days_label', true ),
		'venue'        => get_post_meta( $session->ID, '_phi_venue', true ),
		'prefecture'   => get_post_meta( $session->ID, '_phi_prefecture', true ),
		'format'       => $format,
		'format_label' => isset( $formats[ $format ] ) ? $formats[ $format ] : '',
		'status'       => $status,
		'status_label' => isset( $statuses[ $status ] ) ? $statuses[ $status ] : '',
		'trainer'      => get_post_meta( $session->ID, '_phi_trainer', true ),
		'apply_url'    => $apply,
		'price_label'  => $course_id ? phi_course_price_label( $course_id ) : '',
	);
}

/** 曜日つきの日付 */
function phi_date_label( $ymd ) {
	if ( ! $ymd ) {
		return '';
	}
	$ts = strtotime( $ymd . ' 00:00:00' );
	if ( ! $ts ) {
		return esc_html( $ymd );
	}
	$w = array( '日', '月', '火', '水', '木', '金', '土' );
	return gmdate( 'Y年n月j日', $ts ) . '（' . $w[ (int) gmdate( 'w', $ts ) ] . '）';
}

function phi_date_range_label( $start, $end ) {
	if ( ! $end || $end === $start ) {
		return phi_date_label( $start );
	}
	$s  = strtotime( $start . ' 00:00:00' );
	$e  = strtotime( $end . ' 00:00:00' );
	$w  = array( '日', '月', '火', '水', '木', '金', '土' );
	$el = ( gmdate( 'Y', $s ) === gmdate( 'Y', $e ) ) ? gmdate( 'n月j日', $e ) : gmdate( 'Y年n月j日', $e );
	return phi_date_label( $start ) . '〜' . $el . '（' . $w[ (int) gmdate( 'w', $e ) ] . '）';
}

/** 状態バッジ */
function phi_status_badge( $status, $label ) {
	if ( ! $label ) {
		return '';
	}
	return '<span class="badge badge--' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
}

/** 申込ボタン（外部の申込サービスへ。満席・終了なら押せない表示） */
function phi_apply_button( $data, $class = '' ) {
	// 満席・終了・受付前は、状態のバッジだけを出す（押せないボタンは置かない）
	if ( in_array( $data['status'], array( 'full', 'closed', 'upcoming' ), true ) ) {
		return '';
	}
	if ( ! $data['apply_url'] ) {
		return '';
	}
	$url = phi_out_url( $data['apply_url'], array( 'service' => 'apply', 'session' => $data['id'] ) );
	return '<a class="btn btn--primary ' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '"' . phi_out_target( $data['apply_url'] ) . '>申込フォームへ<span class="ext" aria-hidden="true">↗</span></a>';
}

/**
 * 試作用：架空のURL（example.com）へのリンクは、試作の中の案内ページに差し替える。
 * 本番では外部サービスのURLがそのまま使われる。
 */
function phi_is_dummy_url( $url ) {
	$host = (string) wp_parse_url( $url, PHP_URL_HOST );
	return 'example.com' === $host || '.example.com' === substr( $host, -12 );
}

function phi_out_url( $url, $args = array() ) {
	if ( ! phi_is_dummy_url( $url ) ) {
		return $url;
	}
	return add_query_arg( $args, phi_page_url( 'external-demo' ) );
}

/** 外部リンクの target 属性（試作の案内ページへ行くときは同じタブ） */
function phi_out_target( $url ) {
	return phi_is_dummy_url( $url ) ? '' : ' target="_blank" rel="noopener"';
}

/** 固定ページの URL をスラッグで */
function phi_page_url( $slug ) {
	$p = get_page_by_path( $slug );
	return $p ? get_permalink( $p ) : home_url( '/' . $slug . '/' );
}

/** パンくず */
function phi_breadcrumb( $items ) {
	echo '<nav class="breadcrumb" aria-label="パンくずリスト"><ol>';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">ホーム</a></li>';
	foreach ( $items as $label => $url ) {
		if ( $url ) {
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		} else {
			echo '<li aria-current="page">' . esc_html( $label ) . '</li>';
		}
	}
	echo '</ol></nav>';
}
