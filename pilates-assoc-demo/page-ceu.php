<?php
/* 資格更新（CEU）のご案内。本文は事務局が編集、講座と料金はコースから自動 */
get_header();
the_post();
$ceu = phi_get_courses( 'ceu' );
?>
<div class="page-hero page-hero--members">
	<div class="container">
		<?php phi_breadcrumb( array( '資格をお持ちの方へ' => phi_page_url( 'members' ), get_the_title() => '' ) ); ?>
		<h1><?php the_title(); ?></h1>
	</div>
</div>
<div class="container content narrow">
	<div class="entry"><?php the_content(); ?></div>
	<h2 class="sub-title">資格更新講座</h2>
	<div class="course-grid">
		<?php
		foreach ( $ceu as $c ) {
			get_template_part( 'parts/course-card', null, array( 'course' => $c ) );
		}
		?>
	</div>
</div>
<?php
get_footer();
