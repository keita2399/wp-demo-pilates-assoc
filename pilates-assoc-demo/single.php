<?php
get_header();
the_post();
?>
<div class="page-hero">
	<div class="container">
		<?php phi_breadcrumb( array( 'お知らせ' => phi_page_url( 'news' ), get_the_title() => '' ) ); ?>
		<p class="page-hero__cat"><time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		<h1><?php the_title(); ?></h1>
	</div>
</div>
<div class="container content narrow">
	<div class="entry"><?php the_content(); ?></div>
	<p><a class="more" href="<?php echo esc_url( phi_page_url( 'news' ) ); ?>">← お知らせ一覧へ</a></p>
</div>
<?php
get_footer();
