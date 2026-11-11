<?php
/* 資格をお持ちの方へ（導線2） */
get_header();
$ceu = phi_get_courses( 'ceu' );
$ceu_ids = wp_list_pluck( $ceu, 'ID' );
$ceu_sessions = array();
foreach ( $ceu_ids as $cid ) {
	$ceu_sessions = array_merge( $ceu_sessions, phi_get_sessions( array( 'course' => $cid ) ) );
}
usort( $ceu_sessions, function ( $a, $b ) {
	return strcmp( get_post_meta( $a->ID, '_phi_start', true ), get_post_meta( $b->ID, '_phi_start', true ) );
} );
?>
<div class="page-hero page-hero--members">
	<div class="container">
		<?php phi_breadcrumb( array( '資格をお持ちの方へ' => '' ) ); ?>
		<h1>資格をお持ちの方へ</h1>
		<p>資格の更新、上位コース、マスタートレーナーへの道のりをご案内します。</p>
	</div>
</div>
<div class="container content">
	<div class="cards-3">
		<a class="info-card" href="<?php echo esc_url( phi_page_url( 'ceu' ) ); ?>"><strong>資格の更新（CEU）</strong><span>2年間で12CEU。更新の流れと必要なポイント</span></a>
		<a class="info-card" href="<?php echo esc_url( get_post_type_archive_link( 'phi_course' ) ); ?>"><strong>上位コース</strong><span>マシン・専門コースの一覧と前提条件</span></a>
		<a class="info-card" href="<?php echo esc_url( add_query_arg( 'tab', 'instructor', phi_page_url( 'studios' ) ) ); ?>"><strong>インストラクター一覧</strong><span>掲載内容の変更は事務局までご連絡ください</span></a>
	</div>

	<section class="section-block">
		<h2>開催予定の資格更新講座</h2>
		<?php get_template_part( 'parts/session-list', null, array( 'sessions' => $ceu_sessions ) ); ?>
	</section>

	<section class="section-block">
		<h2>資格更新講座の一覧</h2>
		<table class="table">
			<thead><tr><th>講座</th><th>取得できるCEU</th><th>時間</th><th>料金（税込）</th></tr></thead>
			<tbody>
			<?php foreach ( $ceu as $c ) : ?>
				<tr>
					<td><a href="<?php echo esc_url( get_permalink( $c ) ); ?>"><?php echo esc_html( $c->post_title ); ?></a></td>
					<td><?php echo (int) get_post_meta( $c->ID, '_phi_ceu', true ); ?></td>
					<td><?php echo esc_html( get_post_meta( $c->ID, '_phi_hours', true ) ); ?></td>
					<td><?php echo esc_html( phi_yen( phi_course_fees( $c->ID )['total'] ) ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</section>
</div>
<?php
get_footer();
