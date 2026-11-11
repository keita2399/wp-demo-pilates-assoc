<?php
get_header();
?>
<div class="page-hero">
	<div class="container">
		<?php phi_breadcrumb( array( 'コース一覧' => '' ) ); ?>
		<h1>コース一覧</h1>
	</div>
</div>
<div class="container content">
	<?php foreach ( phi_course_categories() as $key => $label ) : ?>
		<?php
		$list = phi_get_courses( $key );
		if ( ! $list ) {
			continue;
		}
		?>
		<h2 class="sub-title"><?php echo esc_html( $label ); ?></h2>
		<div class="course-grid">
			<?php
			foreach ( $list as $c ) {
				get_template_part( 'parts/course-card', null, array( 'course' => $c ) );
			}
			?>
		</div>
	<?php endforeach; ?>
</div>
<?php
get_footer();
