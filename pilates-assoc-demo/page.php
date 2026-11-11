<?php
get_header();
the_post();
?>
<div class="page-hero">
	<div class="container">
		<?php phi_breadcrumb( array( get_the_title() => '' ) ); ?>
		<h1><?php the_title(); ?></h1>
	</div>
</div>
<div class="container content narrow">
	<div class="entry"><?php the_content(); ?></div>
</div>
<?php
get_footer();
