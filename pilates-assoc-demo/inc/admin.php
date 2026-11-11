<?php
/**
 * 管理画面：一覧の列、「このサイトの仕組み」、旧URLの転送表、ダッシュボード。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------- 一覧の列 ---------- */

add_filter( 'manage_phi_course_posts_columns', function ( $cols ) {
	return array(
		'cb'           => $cols['cb'],
		'title'        => 'コース名',
		'phi_category' => '区分',
		'phi_code'     => '記号',
		'phi_price'    => '料金（税込・試験料込み）',
		'phi_next'     => '次回の開催',
	);
} );

add_action( 'manage_phi_course_posts_custom_column', function ( $col, $id ) {
	switch ( $col ) {
		case 'phi_category':
			echo esc_html( phi_course_category_label( $id ) );
			break;
		case 'phi_code':
			echo esc_html( get_post_meta( $id, '_phi_code', true ) );
			break;
		case 'phi_price':
			$f = phi_course_fees( $id );
			echo '<strong>' . esc_html( phi_yen( $f['total'] ) ) . '</strong><br><span style="color:#666">受講料 ' . esc_html( phi_yen( $f['fee'] ) ) . '／試験料 ' . esc_html( phi_yen( $f['exam'] ) ) . '</span>';
			break;
		case 'phi_next':
			$s = phi_get_sessions( array( 'course' => $id, 'limit' => 1 ) );
			if ( $s ) {
				$d = phi_session_data( $s[0] );
				echo esc_html( $d['date_label'] . ' ' . $d['prefecture'] );
			} else {
				echo '<span style="color:#999">予定なし</span>';
			}
			break;
	}
}, 10, 2 );

add_filter( 'manage_phi_session_posts_columns', function ( $cols ) {
	return array(
		'cb'          => $cols['cb'],
		'phi_date'    => '開催日',
		'phi_course'  => 'コース',
		'phi_place'   => '会場',
		'phi_status'  => '受付状況',
		'phi_price'   => '表示される料金（コースから）',
	);
} );

add_action( 'manage_phi_session_posts_custom_column', function ( $col, $id ) {
	$d = phi_session_data( $id );
	switch ( $col ) {
		case 'phi_date':
			echo '<a class="row-title" href="' . esc_url( get_edit_post_link( $id ) ) . '">' . esc_html( $d['date_label'] ) . '</a>';
			break;
		case 'phi_course':
			echo esc_html( $d['course_title'] );
			break;
		case 'phi_place':
			echo esc_html( trim( $d['prefecture'] . ' ' . $d['venue'] . ' ' . ( 'onsite' !== $d['format'] ? '（' . $d['format_label'] . '）' : '' ) ) );
			break;
		case 'phi_status':
			echo esc_html( $d['status_label'] );
			break;
		case 'phi_price':
			echo esc_html( $d['price_label'] );
			break;
	}
}, 10, 2 );

// 開催日程の一覧は、開催日の近い順に並べる
add_action( 'pre_get_posts', function ( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( 'phi_session' === $q->get( 'post_type' ) && ! $q->get( 'orderby' ) ) {
		$q->set( 'meta_key', '_phi_start' );
		$q->set( 'orderby', 'meta_value' );
		$q->set( 'order', 'ASC' );
	}
	if ( 'phi_course' === $q->get( 'post_type' ) && ! $q->get( 'orderby' ) ) {
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
	}
} );

add_filter( 'manage_phi_studio_posts_columns', function ( $cols ) {
	$new = array();
	foreach ( $cols as $k => $v ) {
		$new[ $k ] = $v;
		if ( 'title' === $k ) {
			$new['phi_pref'] = '所在地';
		}
	}
	unset( $new['date'] );
	return $new;
} );
add_action( 'manage_phi_studio_posts_custom_column', function ( $col, $id ) {
	if ( 'phi_pref' === $col ) {
		echo esc_html( get_post_meta( $id, '_phi_prefecture', true ) . ' ' . get_post_meta( $id, '_phi_city', true ) );
	}
}, 10, 2 );

add_filter( 'manage_phi_instructor_posts_columns', function ( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => '氏名',
		'phi_level' => '資格',
		'phi_pref'  => '活動地域',
		'phi_pub'   => '掲載',
	);
} );
add_action( 'manage_phi_instructor_posts_custom_column', function ( $col, $id ) {
	if ( 'phi_level' === $col ) {
		echo 'master' === get_post_meta( $id, '_phi_level', true ) ? 'マスタートレーナー' : '認定インストラクター';
	} elseif ( 'phi_pref' === $col ) {
		echo esc_html( get_post_meta( $id, '_phi_prefecture', true ) );
	} elseif ( 'phi_pub' === $col ) {
		echo 'no' === get_post_meta( $id, '_phi_public', true ) ? '掲載しない' : '掲載する';
	}
}, 10, 2 );

/* ---------- このサイトの仕組み ---------- */

add_action( 'admin_menu', function () {
	add_menu_page( 'このサイトの仕組み', 'このサイトの仕組み', 'edit_posts', 'phi-about', 'phi_render_about_page', 'dashicons-info-outline', 2 );
	add_submenu_page( 'phi-about', '旧URLの転送', '旧URLの転送', 'manage_options', 'phi-redirects', 'phi_render_redirects_page' );
} );

function phi_render_about_page() {
	$active  = (array) get_option( 'active_plugins', array() );
	$mu      = function_exists( 'get_mu_plugins' ) ? get_mu_plugins() : array();
	$theme   = wp_get_theme();
	?>
	<div class="wrap phi-about">
		<h1>このサイトの仕組み</h1>
		<p class="phi-lead">提案用の試作です。団体名・料金・日程・スタジオ・インストラクターはすべて架空のダミーデータです。</p>

		<h2>1．使っているプラグイン</h2>
		<table class="widefat striped" style="max-width:900px">
			<thead><tr><th>名前</th><th>用途</th><th>区分</th></tr></thead>
			<tbody>
			<?php if ( ! $active ) : ?>
				<tr><td colspan="3"><strong>有効なプラグインはありません。</strong>料金の一元管理、日程、スタジオ検索、旧URLの転送は、すべてテーマ「<?php echo esc_html( $theme->get( 'Name' ) ); ?>」の中のコードで動いています。</td></tr>
			<?php else : ?>
				<?php
				foreach ( $active as $file ) :
					$data = get_plugin_data( WP_PLUGIN_DIR . '/' . $file, false, false );
					?>
					<tr><td><?php echo esc_html( $data['Name'] ); ?></td><td><?php echo esc_html( wp_trim_words( $data['Description'], 30 ) ); ?></td><td>通常のプラグイン</td></tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php foreach ( $mu as $file => $data ) : ?>
				<tr><td><?php echo esc_html( $data['Name'] ? $data['Name'] : $file ); ?></td><td>試作を動かしている環境（WordPress Playground）が自動で入れるもの。本番サイトには入りません。</td><td>実行環境</td></tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<p>本番でプラグインが必要になる場合も、「何のために入れるか」「止めたら何が動かなくなるか」をこの表に書き足して、納品物として引き渡します。</p>

		<h2>2．料金と日程の持ち方</h2>
		<ol class="phi-steps">
			<li>料金は<strong>コースの編集画面にだけ</strong>入力します（受講料・試験料）。</li>
			<li>開催日程には料金の欄がありません。どのコースの日程かを選ぶだけで、料金はコースから読み込まれます。</li>
			<li>コース紹介、料金一覧、開催日程、トップページ、本文中の料金表記（<code>[course_price course="mat-1"]</code>）は、すべて同じ値を表示します。</li>
		</ol>
		<p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=phi_course' ) ); ?>">コースと料金を開く</a>
		<a class="button" href="<?php echo esc_url( phi_page_url( 'fees' ) ); ?>" target="_blank">料金一覧（公開ページ）を見る</a></p>
		<p class="description">試し方：コース「マット1」の受講料を書き換えて更新し、料金一覧・開催日程・よくある質問のページを開き直してください。</p>

		<h2>3．事務局が更新できる範囲</h2>
		<table class="widefat striped" style="max-width:900px">
			<thead><tr><th>内容</th><th>事務局</th><th>管理者</th><th>操作する画面</th></tr></thead>
			<tbody>
				<tr><td>お知らせ</td><td>できる</td><td>できる</td><td>投稿</td></tr>
				<tr><td>開催日程の追加・受付状況の変更</td><td>できる</td><td>できる</td><td>開催日程</td></tr>
				<tr><td>コースの料金・説明文</td><td>できる</td><td>できる</td><td>コースと料金</td></tr>
				<tr><td>認定スタジオ・インストラクターの登録と掲載可否</td><td>できる</td><td>できる</td><td>認定スタジオ／認定インストラクター</td></tr>
				<tr><td>固定ページの文章（協会について等）</td><td>できる</td><td>できる</td><td>固定ページ</td></tr>
				<tr><td>旧URLの転送表</td><td>できない</td><td>できる</td><td>このサイトの仕組み ＞ 旧URLの転送</td></tr>
				<tr><td>デザイン・メニュー構成・プラグイン・ユーザー</td><td>できない</td><td>できる</td><td>（事務局の画面には表示しません）</td></tr>
			</tbody>
		</table>
		<p>試作には事務局用のユーザー「jimukyoku」を用意しています（権限グループ「事務局」）。</p>

		<h2>4．旧URLからの転送</h2>
		<p>現行サイトのURLを新しいURLへ301で転送します。表は1行1件で、管理者が編集します。現在の登録件数：<strong><?php echo count( phi_redirect_map() ); ?>件</strong></p>
		<p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=phi-redirects' ) ); ?>">転送表を開く</a></p>
	</div>
	<style>.phi-about h2{margin-top:2em}.phi-lead{font-size:14px;background:#fff8e5;border-left:4px solid #dba617;padding:8px 12px;max-width:880px}.phi-steps li{margin-bottom:.4em}</style>
	<?php
}

function phi_render_redirects_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['phi_redirects_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['phi_redirects_nonce'] ) ), 'phi_redirects' ) ) {
		$raw = isset( $_POST['phi_redirects'] ) ? sanitize_textarea_field( wp_unslash( $_POST['phi_redirects'] ) ) : '';
		update_option( 'phi_redirects', $raw, false );
		echo '<div class="notice notice-success"><p>転送表を保存しました。</p></div>';
	}
	$map = phi_redirect_map();
	?>
	<div class="wrap">
		<h1>旧URLの転送（301）</h1>
		<p>1行に「旧URLのパス」と「新URLのパス」を空白で区切って書きます。「#」で始まる行はメモです。</p>
		<form method="post">
			<?php wp_nonce_field( 'phi_redirects', 'phi_redirects_nonce' ); ?>
			<textarea name="phi_redirects" rows="16" class="large-text code"><?php echo esc_textarea( get_option( 'phi_redirects', '' ) ); ?></textarea>
			<p><button class="button button-primary">保存</button></p>
		</form>
		<h2>確認</h2>
		<table class="widefat striped" style="max-width:900px">
			<thead><tr><th>旧URL（押すと転送を確認できます）</th><th>転送先</th></tr></thead>
			<tbody>
			<?php foreach ( $map as $from => $to ) : ?>
				<tr><td><a href="<?php echo esc_url( home_url( $from ) ); ?>" target="_blank"><?php echo esc_html( $from ); ?></a></td><td><?php echo esc_html( $to ); ?></td></tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/* ---------- ダッシュボード ---------- */

add_action( 'wp_dashboard_setup', function () {
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	wp_add_dashboard_widget( 'phi_dashboard', '事務局のよく使う操作', function () {
		$next = phi_get_sessions( array( 'limit' => 5 ) );
		echo '<p><a class="button button-primary" href="' . esc_url( admin_url( 'post-new.php?post_type=phi_session' ) ) . '">開催日程を追加</a> ';
		echo '<a class="button" href="' . esc_url( admin_url( 'post-new.php' ) ) . '">お知らせを書く</a> ';
		echo '<a class="button" href="' . esc_url( admin_url( 'edit.php?post_type=phi_course' ) ) . '">料金を確認・変更</a></p>';
		echo '<h3 style="margin-top:1em">直近の開催</h3><ul>';
		foreach ( $next as $s ) {
			$d = phi_session_data( $s );
			echo '<li><a href="' . esc_url( get_edit_post_link( $s->ID ) ) . '">' . esc_html( $d['date_label'] . ' ' . $d['course_title'] ) . '</a>（' . esc_html( $d['prefecture'] . '・' . $d['status_label'] ) . '）</li>';
		}
		echo '</ul><p><a href="' . esc_url( admin_url( 'admin.php?page=phi-about' ) ) . '">このサイトの仕組みを見る</a></p>';
	} );
} );

// 管理画面の上部に、試作であることを常に出す
add_action( 'admin_notices', function () {
	$screen = get_current_screen();
	if ( $screen && in_array( $screen->id, array( 'dashboard', 'edit-phi_course', 'phi_course' ), true ) ) {
		echo '<div class="notice notice-info"><p>提案用の試作です。料金を書き換えると、公開ページの料金がすべて変わることを確認できます。試作の中で何を変えても構いません（試作のURLを開き直すと、最初の状態に戻ります）。</p></div>';
	}
} );
