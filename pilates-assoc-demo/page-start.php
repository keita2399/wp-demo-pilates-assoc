<?php
/* 資格を取りたい方へ（導線1） */
get_header();
$mat1 = phi_find_course( 'mat-1' );
?>
<div class="page-hero page-hero--start">
	<div class="container">
		<?php phi_breadcrumb( array( '資格を取りたい方へ' => '' ) ); ?>
		<h1>資格を取りたい方へ</h1>
		<p>運動指導の経験がない方も、マット1から始められます。</p>
	</div>
</div>
<div class="container content">
	<section class="section-block">
		<h2>資格取得までの流れ</h2>
		<ol class="flow">
			<li><span class="flow__no">1</span><strong>マット1を受講</strong><span>基本原則と基礎エクササイズ<?php if ( $mat1 ) : ?>　<?php echo esc_html( phi_yen( phi_course_fees( $mat1->ID )['total'] ) ); ?>（税込）<?php endif; ?></span></li>
			<li><span class="flow__no">2</span><strong>マット2を受講</strong><span>中級・上級と段階づけ</span></li>
			<li><span class="flow__no">3</span><strong>認定試験</strong><span>筆記と実技。合格で認定インストラクターに</span></li>
			<li><span class="flow__no">4</span><strong>マシンへ進む</strong><span>リフォーマー、チェア、キャデラック</span></li>
		</ol>
	</section>

	<section class="section-block">
		<h2>コースと料金</h2>
		<?php foreach ( phi_course_categories() as $key => $label ) : ?>
			<?php
			if ( 'ceu' === $key ) {
				continue;
			}
			$list = phi_get_courses( $key );
			if ( ! $list ) {
				continue;
			}
			?>
			<h3 class="sub-title"><?php echo esc_html( $label ); ?></h3>
			<div class="course-grid">
				<?php
				foreach ( $list as $c ) {
					get_template_part( 'parts/course-card', null, array( 'course' => $c ) );
				}
				?>
			</div>
		<?php endforeach; ?>
	</section>

	<section class="section-block">
		<h2>直近のマット1</h2>
		<?php get_template_part( 'parts/session-list', null, array( 'sessions' => phi_get_sessions( array( 'course' => $mat1 ? $mat1->ID : -1, 'limit' => 3 ) ) ) ); ?>
		<p><a class="more" href="<?php echo esc_url( phi_page_url( 'schedule' ) ); ?>">すべての開催日程 →</a>　<a class="more" href="<?php echo esc_url( phi_page_url( 'faq' ) ); ?>">よくある質問 →</a></p>
	</section>
</div>
<?php
get_footer();
