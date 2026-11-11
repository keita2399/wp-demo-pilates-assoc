<?php
/**
 * 本文中で料金や日程を書くときの部品。
 * 例：マット1の受講料は [course_price course="mat-1" type="fee"] です。
 * 料金を本文に直接書かないので、コースの料金を直せば本文の表記も変わる。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'course_price', function ( $atts ) {
	$atts   = shortcode_atts( array( 'course' => '', 'type' => 'total' ), $atts, 'course_price' );
	$course = phi_find_course( $atts['course'] );
	if ( ! $course ) {
		return current_user_can( 'edit_posts' ) ? '<mark>［コース「' . esc_html( $atts['course'] ) . '」が見つかりません］</mark>' : '';
	}
	$f = phi_course_fees( $course->ID );
	switch ( $atts['type'] ) {
		case 'fee':
			$v = $f['fee'];
			break;
		case 'exam':
			$v = $f['exam'];
			break;
		default:
			$v = $f['total'];
	}
	return '<span class="price-inline" title="コース「' . esc_attr( $course->post_title ) . '」の料金から自動表示">' . esc_html( phi_yen( $v ) ) . '</span>';
} );

add_shortcode( 'next_sessions', function ( $atts ) {
	$atts     = shortcode_atts( array( 'course' => '', 'limit' => 3 ), $atts, 'next_sessions' );
	$course   = $atts['course'] ? phi_find_course( $atts['course'] ) : null;
	$sessions = phi_get_sessions(
		array(
			'course' => $course ? $course->ID : 0,
			'limit'  => (int) $atts['limit'],
		)
	);
	ob_start();
	get_template_part( 'parts/session-list', null, array( 'sessions' => $sessions ) );
	return ob_get_clean();
} );
