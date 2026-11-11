<?php
/**
 * 試作用のダミーデータ投入。団体・人物・スタジオ・料金はすべて架空。
 * 日程は「投入した日」から先の日付で作るので、いつ開いてもカレンダーが空にならない。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function phi_demo_seed( $force = false ) {
	if ( get_option( 'phi_demo_seeded' ) && ! $force ) {
		return;
	}
	update_option( 'phi_demo_seeded', time() );

	phi_add_roles();

	// ---------- サイトの設定 ----------
	update_option( 'blogname', 'ピラティス指導者協会' );
	update_option( 'blogdescription', 'WordPress試作・架空の団体' );
	update_option( 'timezone_string', 'Asia/Tokyo' );
	update_option( 'date_format', 'Y年n月j日' );
	update_option( 'time_format', 'H:i' );
	update_option( 'start_of_week', 0 );
	update_option( 'posts_per_page', 10 );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'permalink_structure', '/%postname%/' );

	// 初期の投稿・固定ページ・コメントを片付ける
	foreach ( get_posts( array( 'post_type' => array( 'post', 'page', 'phi_course', 'phi_session', 'phi_studio', 'phi_instructor' ), 'post_status' => 'any', 'posts_per_page' => -1 ) ) as $p ) {
		wp_delete_post( $p->ID, true );
	}

	// ---------- コース（料金を持つのはここだけ） ----------
	$courses = array(
		array( 'mat-1', 'マット1', 'mat', 'MAT-1', 77000, 11000, '全3日（計18時間）', 0, '', '運動指導の経験がない方も受講できます', 'ピラティスの基本原則と、マットで行う基礎エクササイズを学びます。正しい姿勢の見方、呼吸、動きの順序を身につけ、グループレッスンを指導できる状態を目指します。' ),
		array( 'mat-2', 'マット2', 'mat', 'MAT-2', 77000, 11000, '全3日（計18時間）', 0, 'mat-1', 'マット1を修了した方', '中級・上級のマットエクササイズと、クライアントの状態に合わせた段階づけを学びます。マット1とあわせて修了すると、マットの認定試験を受けられます。' ),
		array( 'reformer-1', 'リフォーマー1', 'machine', 'RF-1', 132000, 16500, '全4日（計24時間）', 0, 'mat-1', 'マット1を修了した方', 'リフォーマーの安全な使い方、スプリングの設定、基礎エクササイズを学びます。' ),
		array( 'reformer-2', 'リフォーマー2', 'machine', 'RF-2', 132000, 16500, '全4日（計24時間）', 0, 'reformer-1', 'リフォーマー1を修了した方', '中級・上級のリフォーマーエクササイズと、目的別のプログラムの組み立てを学びます。' ),
		array( 'chair', 'チェア', 'machine', 'CH', 88000, 11000, '全2日（計12時間）', 0, 'reformer-1', 'リフォーマー1を修了した方', 'チェアを使ったバランスと筋力のエクササイズを学びます。' ),
		array( 'cadillac', 'キャデラック', 'machine', 'CD', 110000, 11000, '全3日（計18時間）', 0, 'reformer-1', 'リフォーマー1を修了した方', 'キャデラック（トラピーズテーブル）でのアシストと、リハビリテーションへの応用を学びます。' ),
		array( 'barrel', 'バレル', 'machine', 'BR', 66000, 11000, '全2日（計12時間）', 0, 'mat-2', 'マット2を修了した方', 'ラダーバレル、スパインコレクターを使った柔軟性と体幹のエクササイズを学びます。' ),
		array( 'props', 'プロップス', 'special', 'PR', 44000, 0, '全1日（計6時間）', 3, 'mat-1', 'マット1を修了した方', 'ボール、リング、バンドなどの小道具を使い、マットレッスンの幅を広げます。' ),
		array( 'pre-post-natal', '産前産後ピラティス', 'special', 'PN', 66000, 11000, '全2日（計12時間）', 0, 'mat-2', 'マット2を修了した方', '妊娠期と産後の身体の変化を理解し、安全なプログラムを組み立てます。' ),
		array( 'rehab-basic', 'リハビリテーション基礎', 'special', 'RH', 99000, 11000, '全3日（計18時間）', 0, 'mat-1', '医療・リハビリ職の方、またはマット1を修了した方', '整形外科疾患の基礎知識と、リハビリテーションの現場でピラティスを使う際の考え方を学びます。' ),
		array( 'ceu-scoliosis', '側弯症へのアプローチ', 'ceu', 'CEU-SC', 22000, 0, '1日（6時間）', 6, '', '認定インストラクターの方', '側弯症のクライアントへのアセスメントとエクササイズの選び方を学ぶ資格更新講座です。' ),
		array( 'ceu-golf', 'ゴルファーのためのピラティス', 'ceu', 'CEU-GF', 16500, 0, '半日（4時間）', 4, '', '認定インストラクターの方', '回旋動作のパフォーマンス向上と、腰痛予防のプログラムを学ぶ資格更新講座です。' ),
		array( 'ceu-anatomy', 'オンライン機能解剖', 'ceu', 'CEU-AN', 11000, 0, 'オンライン（3時間）', 3, '', '認定インストラクターの方', '自宅から受講できる機能解剖の講座です。録画の視聴期間は2週間です。' ),
	);

	$ids   = array();
	$order = 0;
	foreach ( $courses as $c ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'phi_course',
				'post_status'  => 'publish',
				'post_title'   => $c[1],
				'post_name'    => $c[0],
				'post_content' => '<p>' . $c[10] . '</p>',
				'post_excerpt' => $c[10],
				'menu_order'   => $order++,
			)
		);
		$ids[ $c[0] ] = $id;
		update_post_meta( $id, '_phi_category', $c[2] );
		update_post_meta( $id, '_phi_code', $c[3] );
		update_post_meta( $id, '_phi_fee', $c[4] );
		update_post_meta( $id, '_phi_exam_fee', $c[5] );
		update_post_meta( $id, '_phi_hours', $c[6] );
		update_post_meta( $id, '_phi_ceu', $c[7] );
		update_post_meta( $id, '_phi_target', $c[9] );
		update_post_meta( $id, '_phi_apply_url', 'https://example.com/apply/?course=' . $c[0] );
	}
	foreach ( $courses as $c ) {
		update_post_meta( $ids[ $c[0] ], '_phi_prereq', $c[8] ? $ids[ $c[8] ] : '' );
	}

	// ---------- 開催日程（料金の欄は無い） ----------
	// コース, 何日後に始まるか, 日数, 都道府県, 会場, 形式, 受付状況, 担当
	$sessions = array(
		array( 'mat-1', 9, 3, '東京都', '協会スタジオ 渋谷', 'onsite', 'few', '森 真由美' ),
		array( 'mat-1', 16, 3, '大阪府', 'スタジオ リュクス 梅田', 'onsite', 'open', '川村 恵' ),
		array( 'reformer-1', 12, 4, '東京都', '協会スタジオ 渋谷', 'onsite', 'full', '森 真由美' ),
		array( 'ceu-anatomy', 6, 1, '東京都', 'オンライン（Zoom）', 'online', 'open', '石田 浩二' ),
		array( 'mat-2', 23, 3, '福岡県', 'ピラティスルーム みなと', 'onsite', 'open', '佐伯 直子' ),
		array( 'props', 20, 1, '愛知県', 'スタジオ ノード 名古屋', 'onsite', 'open', '川村 恵' ),
		array( 'ceu-scoliosis', 27, 1, '東京都', '協会スタジオ 渋谷', 'hybrid', 'open', '石田 浩二' ),
		array( 'mat-1', 30, 3, '北海道', 'ピラティス ことり 札幌', 'onsite', 'open', '佐伯 直子' ),
		array( 'chair', 34, 2, '大阪府', 'スタジオ リュクス 梅田', 'onsite', 'open', '川村 恵' ),
		array( 'reformer-2', 37, 4, '東京都', '協会スタジオ 渋谷', 'onsite', 'open', '森 真由美' ),
		array( 'pre-post-natal', 41, 2, '神奈川県', 'スタジオ ブレス 横浜', 'onsite', 'open', '佐伯 直子' ),
		array( 'mat-1', 44, 3, '宮城県', 'ムーブ仙台', 'onsite', 'open', '川村 恵' ),
		array( 'rehab-basic', 48, 3, '東京都', '協会スタジオ 渋谷', 'onsite', 'open', '石田 浩二' ),
		array( 'ceu-golf', 52, 1, '千葉県', 'スタジオ ベイ 船橋', 'onsite', 'open', '森 真由美' ),
		array( 'mat-2', 55, 3, '東京都', '協会スタジオ 渋谷', 'onsite', 'open', '森 真由美' ),
		array( 'reformer-1', 58, 4, '大阪府', 'スタジオ リュクス 梅田', 'onsite', 'open', '川村 恵' ),
		array( 'cadillac', 63, 3, '東京都', '協会スタジオ 渋谷', 'onsite', 'upcoming', '石田 浩二' ),
		array( 'mat-1', 66, 3, '広島県', 'コアラボ 広島', 'onsite', 'upcoming', '佐伯 直子' ),
		array( 'barrel', 70, 2, '福岡県', 'ピラティスルーム みなと', 'onsite', 'upcoming', '佐伯 直子' ),
		array( 'ceu-anatomy', 73, 1, '東京都', 'オンライン（Zoom）', 'online', 'upcoming', '石田 浩二' ),
		array( 'mat-1', 79, 3, '東京都', '協会スタジオ 渋谷', 'onsite', 'upcoming', '森 真由美' ),
		array( 'reformer-1', 86, 4, '愛知県', 'スタジオ ノード 名古屋', 'onsite', 'upcoming', '川村 恵' ),
		array( 'mat-2', 93, 3, '大阪府', 'スタジオ リュクス 梅田', 'onsite', 'upcoming', '川村 恵' ),
		array( 'ceu-scoliosis', 100, 1, '大阪府', 'スタジオ リュクス 梅田', 'onsite', 'upcoming', '石田 浩二' ),
		array( 'mat-1', -20, 3, '東京都', '協会スタジオ 渋谷', 'onsite', 'closed', '森 真由美' ),
	);
	$today = new DateTimeImmutable( 'now', wp_timezone() );
	foreach ( $sessions as $s ) {
		$start = $today->modify( ( $s[1] >= 0 ? '+' : '' ) . $s[1] . ' days' );
		// 始まりは土曜にそろえる（実際の講座に近い見え方にするため）
		$dow = (int) $start->format( 'w' );
		if ( 6 !== $dow ) {
			$start = $start->modify( '+' . ( ( 6 - $dow + 7 ) % 7 ) . ' days' );
		}
		$end = $start->modify( '+' . ( $s[2] - 1 ) . ' days' );
		$cid = $ids[ $s[0] ];
		$id  = wp_insert_post(
			array(
				'post_type'   => 'phi_session',
				'post_status' => 'publish',
				'post_title'  => get_the_title( $cid ) . ' ' . $start->format( 'Y-m-d' ) . ' ' . $s[4],
			)
		);
		update_post_meta( $id, '_phi_course_id', $cid );
		update_post_meta( $id, '_phi_start', $start->format( 'Y-m-d' ) );
		update_post_meta( $id, '_phi_end', $end->format( 'Y-m-d' ) );
		update_post_meta( $id, '_phi_days_label', $s[2] > 1 ? '全' . $s[2] . '日間 各日10:00〜17:00' : ( 'online' === $s[5] ? '19:00〜22:00' : '10:00〜17:00' ) );
		update_post_meta( $id, '_phi_venue', $s[4] );
		update_post_meta( $id, '_phi_prefecture', $s[3] );
		update_post_meta( $id, '_phi_format', $s[5] );
		update_post_meta( $id, '_phi_status', $s[6] );
		update_post_meta( $id, '_phi_trainer', $s[7] );
	}

	// ---------- 認定スタジオ ----------
	$studios = array(
		array( 'ピラティス ことり 札幌', '北海道', '札幌市中央区', array( 'mat', 'reformer' ), 'yes' ),
		array( 'ムーブ仙台', '宮城県', '仙台市青葉区', array( 'mat', 'reformer', 'chair' ), 'yes' ),
		array( '協会スタジオ 渋谷', '東京都', '渋谷区', array( 'mat', 'reformer', 'chair', 'cadillac', 'barrel' ), 'no' ),
		array( 'スタジオ アーク 吉祥寺', '東京都', '武蔵野市', array( 'mat', 'reformer' ), 'yes' ),
		array( 'ピラティス ひだまり 世田谷', '東京都', '世田谷区', array( 'mat' ), 'yes' ),
		array( 'スタジオ ブレス 横浜', '神奈川県', '横浜市西区', array( 'mat', 'reformer', 'barrel' ), 'yes' ),
		array( 'スタジオ ベイ 船橋', '千葉県', '船橋市', array( 'mat', 'reformer' ), 'yes' ),
		array( 'コアバランス 大宮', '埼玉県', 'さいたま市大宮区', array( 'mat', 'chair' ), 'yes' ),
		array( 'スタジオ ノード 名古屋', '愛知県', '名古屋市中区', array( 'mat', 'reformer', 'chair', 'cadillac' ), 'yes' ),
		array( 'ピラティス しずく 静岡', '静岡県', '静岡市葵区', array( 'mat' ), 'yes' ),
		array( 'スタジオ リュクス 梅田', '大阪府', '大阪市北区', array( 'mat', 'reformer', 'chair', 'cadillac' ), 'yes' ),
		array( 'ピラティス なぎ 京都', '京都府', '京都市中京区', array( 'mat', 'reformer' ), 'yes' ),
		array( 'スタジオ ウェーブ 神戸', '兵庫県', '神戸市中央区', array( 'mat', 'barrel' ), 'no' ),
		array( 'コアラボ 広島', '広島県', '広島市中区', array( 'mat', 'reformer' ), 'yes' ),
		array( 'ピラティスルーム みなと', '福岡県', '福岡市中央区', array( 'mat', 'reformer', 'chair', 'barrel' ), 'yes' ),
		array( 'スタジオ てぃーだ 那覇', '沖縄県', '那覇市', array( 'mat' ), 'yes' ),
	);
	$studio_ids = array();
	foreach ( $studios as $i => $st ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'phi_studio',
				'post_status'  => 'publish',
				'post_title'   => $st[0],
				'post_name'    => 'studio-' . ( $i + 1 ),
				'post_content' => '<p>' . $st[2] . 'にある認定スタジオです（架空のスタジオです）。' . ( 'yes' === $st[4] ? '体験レッスンを受け付けています。' : '' ) . '</p>',
			)
		);
		$studio_ids[ $st[0] ] = $id;
		update_post_meta( $id, '_phi_prefecture', $st[1] );
		update_post_meta( $id, '_phi_city', $st[2] );
		update_post_meta( $id, '_phi_address', '○○1-2-3（ダミー）' );
		update_post_meta( $id, '_phi_url', 'https://example.com/studio-' . ( $i + 1 ) . '/' );
		update_post_meta( $id, '_phi_equipment', $st[3] );
		update_post_meta( $id, '_phi_trial', $st[4] );
	}

	// ---------- 認定インストラクター ----------
	$instructors = array(
		array( '森 真由美', 'master', '東京都', '協会スタジオ 渋谷', array( 'mat-1', 'mat-2', 'reformer-1', 'reformer-2', 'chair', 'cadillac', 'barrel' ) ),
		array( '川村 恵', 'master', '大阪府', 'スタジオ リュクス 梅田', array( 'mat-1', 'mat-2', 'reformer-1', 'reformer-2', 'chair' ) ),
		array( '佐伯 直子', 'master', '福岡県', 'ピラティスルーム みなと', array( 'mat-1', 'mat-2', 'reformer-1', 'barrel', 'pre-post-natal' ) ),
		array( '石田 浩二', 'master', '東京都', '', array( 'mat-1', 'mat-2', 'reformer-1', 'rehab-basic', 'cadillac' ) ),
		array( '高橋 由紀', 'instructor', '北海道', 'ピラティス ことり 札幌', array( 'mat-1', 'mat-2', 'reformer-1' ) ),
		array( '小野寺 舞', 'instructor', '宮城県', 'ムーブ仙台', array( 'mat-1', 'mat-2', 'chair' ) ),
		array( '中村 彩', 'instructor', '東京都', 'スタジオ アーク 吉祥寺', array( 'mat-1', 'mat-2', 'reformer-1', 'reformer-2' ) ),
		array( '山本 早苗', 'instructor', '東京都', 'ピラティス ひだまり 世田谷', array( 'mat-1', 'mat-2', 'pre-post-natal' ) ),
		array( '井上 健太', 'instructor', '東京都', '', array( 'mat-1', 'mat-2', 'rehab-basic' ) ),
		array( '木村 瞳', 'instructor', '神奈川県', 'スタジオ ブレス 横浜', array( 'mat-1', 'mat-2', 'barrel' ) ),
		array( '林 美咲', 'instructor', '千葉県', 'スタジオ ベイ 船橋', array( 'mat-1', 'mat-2', 'reformer-1' ) ),
		array( '清水 香織', 'instructor', '埼玉県', 'コアバランス 大宮', array( 'mat-1', 'mat-2', 'chair' ) ),
		array( '松本 千尋', 'instructor', '愛知県', 'スタジオ ノード 名古屋', array( 'mat-1', 'mat-2', 'reformer-1', 'cadillac' ) ),
		array( '杉山 遥', 'instructor', '静岡県', 'ピラティス しずく 静岡', array( 'mat-1', 'mat-2' ) ),
		array( '岡田 涼子', 'instructor', '大阪府', 'スタジオ リュクス 梅田', array( 'mat-1', 'mat-2', 'reformer-1', 'reformer-2' ) ),
		array( '藤原 葵', 'instructor', '京都府', 'ピラティス なぎ 京都', array( 'mat-1', 'mat-2', 'reformer-1' ) ),
		array( '西田 亮', 'instructor', '兵庫県', 'スタジオ ウェーブ 神戸', array( 'mat-1', 'mat-2', 'rehab-basic' ), 'no' ),
		array( '村上 沙織', 'instructor', '広島県', 'コアラボ 広島', array( 'mat-1', 'mat-2', 'reformer-1' ) ),
		array( '原田 朋美', 'instructor', '福岡県', 'ピラティスルーム みなと', array( 'mat-1', 'mat-2', 'chair', 'barrel' ) ),
		array( '金城 さくら', 'instructor', '沖縄県', 'スタジオ てぃーだ 那覇', array( 'mat-1', 'mat-2' ) ),
	);
	foreach ( $instructors as $in ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'phi_instructor',
				'post_status' => 'publish',
				'post_title'  => $in[0],
			)
		);
		update_post_meta( $id, '_phi_level', $in[1] );
		update_post_meta( $id, '_phi_prefecture', $in[2] );
		update_post_meta( $id, '_phi_studio_id', $in[3] && isset( $studio_ids[ $in[3] ] ) ? $studio_ids[ $in[3] ] : '' );
		update_post_meta( $id, '_phi_certs', array_map( 'strval', array_map( function ( $slug ) use ( $ids ) { return $ids[ $slug ]; }, $in[4] ) ) );
		update_post_meta( $id, '_phi_public', isset( $in[5] ) ? $in[5] : 'yes' );
	}

	// ---------- 固定ページ ----------
	$pages = array(
		'home'      => array( 'ホーム', '' ),
		'start'     => array( '資格を取りたい方へ', '' ),
		'members'   => array( '資格をお持ちの方へ', '' ),
		'studios'   => array( '認定スタジオ・インストラクターを探す', '' ),
		'schedule'  => array( '開催日程', '' ),
		'fees'      => array( '料金一覧', '' ),
		'ceu'       => array( '資格更新（CEU）のご案内', '<p>認定資格は2年ごとの更新制です。更新には、2年間で合計12CEU以上の取得が必要です。CEUは協会が開催する資格更新講座のほか、養成コースの再受講でも取得できます。</p><h2>更新の流れ</h2><ol><li>2年間で12CEU以上を取得する</li><li>有効期限の2か月前に届く案内メールから、更新手続きのフォームを送信する</li><li>更新料をお支払いいただき、新しい認定証を受け取る</li></ol>' ),
		'faq'       => array( 'よくある質問', '<h2>マット1の受講料はいくらですか？</h2><p>マット1の受講料は [course_price course="mat-1" type="fee"]、認定試験料は [course_price course="mat-1" type="exam"] です（いずれも税込）。</p><p class="demo-note">この料金は本文に直接書かれていません。コース「マット1」の料金から自動で表示しています。コースの料金を変えると、この文章の金額も変わります。</p><h2>リフォーマーを受講するには、何が必要ですか？</h2><p>マット1の修了が必要です。リフォーマー1の受講料は [course_price course="reformer-1" type="fee"] です。</p><h2>運動指導の経験がなくても受講できますか？</h2><p>マット1は、運動指導の経験がない方も受講できます。</p><h2>申込後のキャンセルはできますか？</h2><p>開催日の14日前までは全額を返金します。それ以降の扱いは、申込フォームの規約をご確認ください（試作のため架空の条件です）。</p>' ),
		'about'     => array( '協会について', '<p>ピラティス指導者協会は、ピラティスの指導者を養成し、資格を認定する架空の団体です。このサイトは、WordPressによるサイト構築の提案のために作った試作で、団体名・人物・スタジオ・料金・日程はすべてダミーデータです。</p><h2>このサイトで試していること</h2><ul><li>料金をコースに1か所だけ持たせ、全ページに反映する</li><li>訪問者の目的ごとに入口を分ける（資格を取りたい方／資格をお持ちの方／スタジオを探す方）</li><li>認定スタジオとインストラクターを地域で探せる</li><li>旧サイトのURLから新しいURLへ自動で転送する</li><li>外部プラグインを使わない</li></ul>' ),
		'contact'   => array( 'お問い合わせ', '<p>お問い合わせは、現在お使いの外部フォームサービスをそのまま使う想定です（今回の範囲では作り替えません）。</p><p><a class="btn btn--primary" href="https://example.com/contact/" target="_blank" rel="noopener">お問い合わせフォームへ（外部サービス）</a></p><p>講座の申込とお支払いも、各コースの「申込フォームへ」から現行の外部サービスに移ります。</p>' ),
		'news'      => array( 'お知らせ', '' ),
	);
	$page_ids = array();
	$order    = 0;
	foreach ( $pages as $slug => $pg ) {
		$page_ids[ $slug ] = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $pg[0],
				'post_name'    => $slug,
				'post_content' => $pg[1],
				'menu_order'   => $order++,
			)
		);
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_ids['home'] );
	update_option( 'page_for_posts', $page_ids['news'] );

	// ---------- お知らせ ----------
	$c1 = phi_seed_term( '講座', 'course-info' );
	$c2 = phi_seed_term( '協会から', 'association' );
	$news = array(
		array( 2, 'マット1（東京）は残りわずかです', '<p>直近のマット1（協会スタジオ 渋谷）は残席わずかです。日程は<a href="' . esc_url( home_url( '/schedule/' ) ) . '">開催日程</a>をご覧ください。料金は [course_price course="mat-1"]（試験料込み・税込）です。</p>', $c1 ),
		array( 9, '資格更新講座「側弯症へのアプローチ」を新設しました', '<p>認定インストラクター向けの資格更新講座を新設しました。取得できるCEUは6です。</p>', $c1 ),
		array( 16, '年末年始の事務局の休業について', '<p>12月27日から1月5日まで、事務局を休業します（試作のためのダミーのお知らせです）。</p>', $c2 ),
		array( 30, '認定スタジオ一覧を更新しました', '<p>新たに2つのスタジオが認定スタジオになりました。<a href="' . esc_url( home_url( '/studios/' ) ) . '">認定スタジオを探す</a>からご覧ください。</p>', $c2 ),
		array( 45, '公式サイトをリニューアルしました', '<p>目的別の入口と、地域で探せるスタジオ検索を用意しました（試作のためのダミーのお知らせです）。</p>', $c2 ),
	);
	foreach ( $news as $n ) {
		$date = $today->modify( '-' . $n[0] . ' days' )->format( 'Y-m-d 10:00:00' );
		wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_title'    => $n[1],
				'post_content'  => $n[2],
				'post_date'     => $date,
				'post_category' => array( $n[3] ),
			)
		);
	}
	$uncat = get_category_by_slug( 'uncategorized' );
	if ( $uncat && $c2 ) {
		update_option( 'default_category', $c2 );
		wp_delete_category( $uncat->term_id );
	}

	// ---------- 旧URLの転送表 ----------
	update_option(
		'phi_redirects',
		implode(
			"\n",
			array(
				'# 旧URL  新URL（現行サイトの調査で全ページ分を作ります。ここは例です）',
				'/course/mat1/  /courses/mat-1/',
				'/course/mat2/  /courses/mat-2/',
				'/course/reformer1/  /courses/reformer-1/',
				'/schedule.html  /schedule/',
				'/calendar/  /schedule/',
				'/price/  /fees/',
				'/studio-list/  /studios/',
				'/instructor-list/  /studios/?tab=instructor',
				'/category/info/  /news/',
				'/?page_id=12  /about/',
			)
		),
		false
	);

	// ---------- 事務局ユーザー ----------
	if ( ! username_exists( 'jimukyoku' ) ) {
		wp_insert_user(
			array(
				'user_login'   => 'jimukyoku',
				'user_pass'    => 'jimukyoku-demo',
				'display_name' => '事務局（試作）',
				'user_email'   => 'jimukyoku@example.com',
				'role'         => 'jimukyoku',
			)
		);
	}

	flush_rewrite_rules( false );
}

/** カテゴリーを取得、無ければ作る */
function phi_seed_term( $name, $slug ) {
	$t = get_term_by( 'slug', $slug, 'category' );
	if ( $t ) {
		return (int) $t->term_id;
	}
	$r = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
	return is_wp_error( $r ) ? 0 : (int) $r['term_id'];
}
