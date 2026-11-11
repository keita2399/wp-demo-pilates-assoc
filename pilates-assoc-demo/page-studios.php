<?php
/* 認定スタジオ・インストラクターを探す（導線3） */
get_header();

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$tab     = ( isset( $_GET['tab'] ) && 'instructor' === $_GET['tab'] ) ? 'instructor' : 'studio';
$region  = isset( $_GET['region'] ) ? sanitize_text_field( wp_unslash( $_GET['region'] ) ) : '';
$pref    = isset( $_GET['pref'] ) ? sanitize_text_field( wp_unslash( $_GET['pref'] ) ) : '';
$keyword = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
$equip   = isset( $_GET['equip'] ) ? sanitize_key( wp_unslash( $_GET['equip'] ) ) : '';
$level   = isset( $_GET['level'] ) ? sanitize_key( wp_unslash( $_GET['level'] ) ) : '';
// phpcs:enable

$regions = phi_regions();
if ( ! isset( $regions[ $region ] ) ) {
	$region = '';
}
if ( $pref && ! in_array( $pref, phi_all_prefectures(), true ) ) {
	$pref = '';
}
$equipment = array( 'mat' => 'マット', 'reformer' => 'リフォーマー', 'chair' => 'チェア', 'cadillac' => 'キャデラック', 'barrel' => 'バレル' );

$meta = array( 'relation' => 'AND' );
if ( $pref ) {
	$meta[] = array( 'key' => '_phi_prefecture', 'value' => $pref );
} elseif ( $region ) {
	$meta[] = array( 'key' => '_phi_prefecture', 'value' => $regions[ $region ], 'compare' => 'IN' );
}
if ( 'studio' === $tab && isset( $equipment[ $equip ] ) ) {
	$meta[] = array( 'key' => '_phi_equipment', 'value' => '"' . $equip . '"', 'compare' => 'LIKE' );
}
if ( 'instructor' === $tab ) {
	$meta[] = array( 'key' => '_phi_public', 'value' => 'no', 'compare' => '!=' );
	if ( in_array( $level, array( 'instructor', 'master' ), true ) ) {
		$meta[] = array( 'key' => '_phi_level', 'value' => $level );
	}
}

$results = get_posts(
	array(
		'post_type'      => 'studio' === $tab ? 'phi_studio' : 'phi_instructor',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		's'              => $keyword,
		'meta_query'     => $meta,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);
// 都道府県の並び（北から）にそろえる
$order = array_flip( phi_all_prefectures() );
usort( $results, function ( $a, $b ) use ( $order ) {
	$pa = get_post_meta( $a->ID, '_phi_prefecture', true );
	$pb = get_post_meta( $b->ID, '_phi_prefecture', true );
	$oa = isset( $order[ $pa ] ) ? $order[ $pa ] : 99;
	$ob = isset( $order[ $pb ] ) ? $order[ $pb ] : 99;
	return $oa === $ob ? strcmp( $a->post_title, $b->post_title ) : $oa - $ob;
} );
$base = phi_page_url( 'studios' );
?>
<div class="page-hero page-hero--find">
	<div class="container">
		<?php phi_breadcrumb( array( '認定スタジオ・インストラクターを探す' => '' ) ); ?>
		<h1>認定スタジオ・インストラクターを探す</h1>
		<p>協会の認定を受けたスタジオとインストラクターを、地域から探せます。</p>
	</div>
</div>
<div class="container content">
	<div class="tabs" role="tablist">
		<a role="tab" class="tabs__tab" aria-selected="<?php echo 'studio' === $tab ? 'true' : 'false'; ?>" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'studio', 'region' => $region, 'pref' => $pref ), $base ) ); ?>">スタジオ</a>
		<a role="tab" class="tabs__tab" aria-selected="<?php echo 'instructor' === $tab ? 'true' : 'false'; ?>" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'instructor', 'region' => $region, 'pref' => $pref ), $base ) ); ?>">インストラクター</a>
	</div>

	<form class="search-form" method="get" action="<?php echo esc_url( $base ); ?>">
		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>">
		<div class="search-form__row">
			<label>地方
				<select name="region" data-region-select>
					<option value="">すべて</option>
					<?php foreach ( array_keys( $regions ) as $r ) : ?>
						<option value="<?php echo esc_attr( $r ); ?>" <?php selected( $region, $r ); ?>><?php echo esc_html( $r ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label>都道府県
				<select name="pref" data-pref-select>
					<option value="">すべて</option>
					<?php foreach ( $regions as $r => $prefs ) : ?>
						<optgroup label="<?php echo esc_attr( $r ); ?>" data-region="<?php echo esc_attr( $r ); ?>">
							<?php foreach ( $prefs as $p ) : ?>
								<option value="<?php echo esc_attr( $p ); ?>" <?php selected( $pref, $p ); ?>><?php echo esc_html( $p ); ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
			</label>
			<?php if ( 'studio' === $tab ) : ?>
				<label>器具
					<select name="equip">
						<option value="">指定なし</option>
						<?php foreach ( $equipment as $k => $l ) : ?>
							<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $equip, $k ); ?>><?php echo esc_html( $l ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			<?php else : ?>
				<label>資格
					<select name="level">
						<option value="">指定なし</option>
						<option value="master" <?php selected( $level, 'master' ); ?>>マスタートレーナー</option>
						<option value="instructor" <?php selected( $level, 'instructor' ); ?>>認定インストラクター</option>
					</select>
				</label>
			<?php endif; ?>
			<label class="search-form__kw">名前で探す
				<input type="search" name="q" value="<?php echo esc_attr( $keyword ); ?>" placeholder="<?php echo 'studio' === $tab ? 'スタジオ名' : '氏名'; ?>">
			</label>
			<button class="btn btn--primary" type="submit">この条件で探す</button>
		</div>
	</form>

	<p class="result-count"><?php echo count( $results ); ?>件<?php echo ( $region || $pref || $keyword || $equip || $level ) ? '（条件で絞り込み中　<a href="' . esc_url( add_query_arg( 'tab', $tab, $base ) ) . '">条件を外す</a>）' : ''; ?></p>

	<?php if ( ! $results ) : ?>
		<p class="empty">条件に合う<?php echo 'studio' === $tab ? 'スタジオ' : 'インストラクター'; ?>が見つかりませんでした。地方や都道府県を広げてお探しください。</p>
	<?php elseif ( 'studio' === $tab ) : ?>
		<ul class="result-grid">
			<?php
			foreach ( $results as $s ) :
				$eq = (array) get_post_meta( $s->ID, '_phi_equipment', true );
				?>
				<li class="result-card">
					<p class="result-card__area"><?php echo esc_html( get_post_meta( $s->ID, '_phi_prefecture', true ) . ' ' . get_post_meta( $s->ID, '_phi_city', true ) ); ?></p>
					<h2 class="result-card__name"><a href="<?php echo esc_url( get_permalink( $s ) ); ?>"><?php echo esc_html( $s->post_title ); ?></a></h2>
					<p class="result-card__tags">
						<?php foreach ( $eq as $e ) : ?>
							<?php if ( isset( $equipment[ $e ] ) ) : ?><span class="tag"><?php echo esc_html( $equipment[ $e ] ); ?></span><?php endif; ?>
						<?php endforeach; ?>
						<?php if ( 'yes' === get_post_meta( $s->ID, '_phi_trial', true ) ) : ?><span class="tag tag--accent">体験あり</span><?php endif; ?>
					</p>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<ul class="result-grid">
			<?php
			foreach ( $results as $p ) :
				$is_master = 'master' === get_post_meta( $p->ID, '_phi_level', true );
				$sid       = (int) get_post_meta( $p->ID, '_phi_studio_id', true );
				$certs     = (array) get_post_meta( $p->ID, '_phi_certs', true );
				$cert_names = array();
				foreach ( $certs as $cid ) {
					$t = get_the_title( (int) $cid );
					if ( $t ) {
						$cert_names[] = $t;
					}
				}
				?>
				<li class="result-card">
					<p class="result-card__area"><?php echo esc_html( get_post_meta( $p->ID, '_phi_prefecture', true ) ); ?></p>
					<h2 class="result-card__name"><?php echo esc_html( $p->post_title ); ?>
						<?php if ( $is_master ) : ?><span class="tag tag--accent">マスタートレーナー</span><?php endif; ?>
					</h2>
					<p class="result-card__sub">所属：<?php echo $sid ? '<a href="' . esc_url( get_permalink( $sid ) ) . '">' . esc_html( get_the_title( $sid ) ) . '</a>' : 'フリー'; ?></p>
					<p class="result-card__sub">修了：<?php echo esc_html( implode( '、', $cert_names ) ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
<?php
get_footer();
