<?php
/**
 * 開催日程の一覧。料金は各コースから読む（phi_session_data の price_label）。
 * $args['sessions'] … 開催日程の投稿の配列
 * $args['show_course'] … コース名を出すか（既定：出す）
 */
$sessions    = isset( $args['sessions'] ) ? $args['sessions'] : array();
$show_course = isset( $args['show_course'] ) ? (bool) $args['show_course'] : true;
if ( ! $sessions ) {
	echo '<p class="empty">条件に合う開催予定はありません。</p>';
	return;
}
?>
<ul class="session-list">
<?php
foreach ( $sessions as $s ) :
	$d = phi_session_data( $s );
	?>
	<li class="session" id="s-<?php echo (int) $d['id']; ?>">
		<div class="session__date">
			<span class="session__day"><?php echo esc_html( $d['date_label'] ); ?></span>
			<?php if ( $d['days_label'] ) : ?><span class="session__days"><?php echo esc_html( $d['days_label'] ); ?></span><?php endif; ?>
		</div>
		<div class="session__body">
			<?php if ( $show_course ) : ?>
				<a class="session__course" href="<?php echo esc_url( $d['course_url'] ); ?>"><?php echo esc_html( $d['course_title'] ); ?></a>
			<?php endif; ?>
			<p class="session__meta">
				<span><?php echo esc_html( $d['prefecture'] ); ?>　<?php echo esc_html( $d['venue'] ); ?></span>
				<?php if ( 'onsite' !== $d['format'] && $d['format_label'] ) : ?><span class="tag"><?php echo esc_html( $d['format_label'] ); ?></span><?php endif; ?>
				<?php if ( $d['trainer'] ) : ?><span>担当：<?php echo esc_html( $d['trainer'] ); ?></span><?php endif; ?>
			</p>
		</div>
		<div class="session__price">
			<span class="session__price-label">料金</span>
			<span class="session__price-value"><?php echo esc_html( $d['price_label'] ); ?></span>
		</div>
		<div class="session__action">
			<?php echo wp_kses_post( phi_status_badge( $d['status'], $d['status_label'] ) ); ?>
			<?php echo wp_kses_post( phi_apply_button( $d ) ); ?>
		</div>
	</li>
<?php endforeach; ?>
</ul>
