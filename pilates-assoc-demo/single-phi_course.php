<?php
/* コースの紹介ページ */
get_header();
the_post();
$id       = get_the_ID();
$f        = phi_course_fees( $id );
$prereq   = (int) get_post_meta( $id, '_phi_prereq', true );
$is_ceu   = 'ceu' === get_post_meta( $id, '_phi_category', true );
$sessions = phi_get_sessions( array( 'course' => $id ) );
$parent   = $is_ceu ? array( '資格をお持ちの方へ' => phi_page_url( 'members' ) ) : array( '資格を取りたい方へ' => phi_page_url( 'start' ) );
?>
<div class="page-hero">
	<div class="container">
		<?php phi_breadcrumb( $parent + array( get_the_title() => '' ) ); ?>
		<p class="page-hero__cat"><?php echo esc_html( phi_course_category_label( $id ) ); ?>　<?php echo esc_html( get_post_meta( $id, '_phi_code', true ) ); ?></p>
		<h1><?php the_title(); ?></h1>
	</div>
</div>
<div class="container content course-layout">
	<div class="course-layout__main">
		<div class="entry"><?php the_content(); ?></div>

		<h2 class="sub-title" id="sessions">開催日程</h2>
		<?php get_template_part( 'parts/session-list', null, array( 'sessions' => $sessions, 'show_course' => false ) ); ?>
	</div>
	<aside class="course-layout__side">
		<div class="spec">
			<dl>
				<dt>受講料</dt><dd><?php echo esc_html( phi_yen( $f['fee'] ) ); ?></dd>
				<?php if ( $f['exam'] ) : ?><dt>認定試験料</dt><dd><?php echo esc_html( phi_yen( $f['exam'] ) ); ?></dd><?php endif; ?>
				<dt>合計（税込）</dt><dd class="spec__total"><?php echo esc_html( phi_yen( $f['total'] ) ); ?></dd>
				<dt>時間</dt><dd><?php echo esc_html( get_post_meta( $id, '_phi_hours', true ) ); ?></dd>
				<dt>対象</dt><dd><?php echo esc_html( get_post_meta( $id, '_phi_target', true ) ); ?></dd>
				<dt>受講の前提</dt><dd><?php echo $prereq ? '<a href="' . esc_url( get_permalink( $prereq ) ) . '">' . esc_html( get_the_title( $prereq ) ) . '</a>の修了' : 'なし'; ?></dd>
				<?php if ( (int) get_post_meta( $id, '_phi_ceu', true ) ) : ?><dt>取得できるCEU</dt><dd><?php echo (int) get_post_meta( $id, '_phi_ceu', true ); ?></dd><?php endif; ?>
			</dl>
			<a class="btn btn--primary btn--block" href="#sessions">日程を選んで申し込む</a>
			<p class="spec__note">申込とお支払いは外部の申込フォームで行います。</p>
		</div>
	</aside>
</div>
<?php
get_footer();
