<?php
/**
 * コースのカード。$args['course'] … コースの投稿
 */
$course = $args['course'];
$fees   = phi_course_fees( $course->ID );
$next   = phi_get_sessions( array( 'course' => $course->ID, 'limit' => 1 ) );
$nd     = $next ? phi_session_data( $next[0] ) : null;
?>
<article class="course-card">
	<p class="course-card__cat"><?php echo esc_html( phi_course_category_label( $course->ID ) ); ?></p>
	<h3 class="course-card__title"><a href="<?php echo esc_url( get_permalink( $course ) ); ?>"><?php echo esc_html( get_the_title( $course ) ); ?></a></h3>
	<p class="course-card__hours"><?php echo esc_html( get_post_meta( $course->ID, '_phi_hours', true ) ); ?></p>
	<p class="course-card__price"><?php echo esc_html( phi_yen( $fees['total'] ) ); ?><small>税込<?php echo $fees['exam'] ? '・試験料込み' : ''; ?></small></p>
	<p class="course-card__next">
		<?php if ( $nd ) : ?>
			次回 <?php echo esc_html( $nd['date_label'] ); ?>　<?php echo esc_html( $nd['prefecture'] ); ?>
		<?php else : ?>
			次回の日程は調整中です
		<?php endif; ?>
	</p>
</article>
