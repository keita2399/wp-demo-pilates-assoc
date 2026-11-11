<?php
/* 開催日程（カレンダーと一覧）。料金はコースから読む */
get_header();

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$tz     = wp_timezone();
$now    = new DateTimeImmutable( 'now', $tz );
$ym     = isset( $_GET['ym'] ) ? sanitize_text_field( wp_unslash( $_GET['ym'] ) ) : '';
if ( ! preg_match( '/^\d{4}-(0[1-9]|1[0-2])$/', $ym ) ) {
	$ym = $now->format( 'Y-m' );
}
$course = isset( $_GET['course'] ) ? absint( $_GET['course'] ) : 0;
$region = isset( $_GET['region'] ) ? sanitize_text_field( wp_unslash( $_GET['region'] ) ) : '';
$format = isset( $_GET['format'] ) ? sanitize_key( wp_unslash( $_GET['format'] ) ) : '';
// phpcs:enable

$month_start = new DateTimeImmutable( $ym . '-01', $tz );
$month_end   = $month_start->modify( 'last day of this month' );
$prev        = $month_start->modify( '-1 month' )->format( 'Y-m' );
$next        = $month_start->modify( '+1 month' )->format( 'Y-m' );
$from        = max( $month_start->format( 'Y-m-d' ), $now->format( 'Y-m-d' ) );
$is_past     = $month_end->format( 'Y-m-d' ) < $now->format( 'Y-m-d' );

$filters  = array( 'course' => $course, 'region' => $region, 'format' => $format );
$sessions = $is_past ? array() : phi_get_sessions(
	array_merge(
		$filters,
		array(
			'from' => $from,
			'to'   => $month_end->format( 'Y-m-d' ),
		)
	)
);

// 日付ごとに並べる
$by_day = array();
foreach ( $sessions as $s ) {
	$d   = phi_session_data( $s );
	$cur = new DateTimeImmutable( $d['start'], $tz );
	$end = new DateTimeImmutable( $d['end'], $tz );
	while ( $cur <= $end ) {
		$key = $cur->format( 'Y-m-d' );
		if ( $key >= $month_start->format( 'Y-m-d' ) && $key <= $month_end->format( 'Y-m-d' ) ) {
			$by_day[ $key ][] = $d;
		}
		$cur = $cur->modify( '+1 day' );
	}
}
$base = phi_page_url( 'schedule' );
$keep = array_filter( array( 'course' => $course ? $course : '', 'region' => $region, 'format' => $format ) );
?>
<div class="page-hero">
	<div class="container">
		<?php phi_breadcrumb( array( '開催日程' => '' ) ); ?>
		<h1>開催日程</h1>
		<p>料金は各コースの料金表と同じ値を表示しています。申込は外部の申込フォームで受け付けます。</p>
	</div>
</div>
<div class="container content">
	<form class="search-form" method="get" action="<?php echo esc_url( $base ); ?>">
		<input type="hidden" name="ym" value="<?php echo esc_attr( $ym ); ?>">
		<div class="search-form__row">
			<label>コース
				<select name="course">
					<option value="">すべてのコース</option>
					<?php foreach ( phi_course_categories() as $ck => $cl ) : ?>
						<optgroup label="<?php echo esc_attr( $cl ); ?>">
						<?php foreach ( phi_get_courses( $ck ) as $c ) : ?>
							<option value="<?php echo (int) $c->ID; ?>" <?php selected( $course, $c->ID ); ?>><?php echo esc_html( $c->post_title ); ?></option>
						<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
			</label>
			<label>地方
				<select name="region">
					<option value="">すべて</option>
					<?php foreach ( array_keys( phi_regions() ) as $r ) : ?>
						<option value="<?php echo esc_attr( $r ); ?>" <?php selected( $region, $r ); ?>><?php echo esc_html( $r ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label>形式
				<select name="format">
					<option value="">すべて</option>
					<?php foreach ( phi_session_formats() as $fk => $fl ) : ?>
						<option value="<?php echo esc_attr( $fk ); ?>" <?php selected( $format, $fk ); ?>><?php echo esc_html( $fl ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<button class="btn btn--primary" type="submit">絞り込む</button>
		</div>
	</form>

	<div class="cal-nav">
		<a class="btn btn--ghost" href="<?php echo esc_url( add_query_arg( array_merge( $keep, array( 'ym' => $prev ) ), $base ) ); ?>">← 前の月</a>
		<h2 class="cal-nav__title"><?php echo esc_html( $month_start->format( 'Y年n月' ) ); ?></h2>
		<a class="btn btn--ghost" href="<?php echo esc_url( add_query_arg( array_merge( $keep, array( 'ym' => $next ) ), $base ) ); ?>">次の月 →</a>
	</div>

	<table class="calendar" aria-label="<?php echo esc_attr( $month_start->format( 'Y年n月' ) ); ?>の開催カレンダー">
		<thead><tr><?php foreach ( array( '日', '月', '火', '水', '木', '金', '土' ) as $i => $w ) : ?><th scope="col" class="<?php echo 0 === $i ? 'sun' : ( 6 === $i ? 'sat' : '' ); ?>"><?php echo esc_html( $w ); ?></th><?php endforeach; ?></tr></thead>
		<tbody>
		<?php
		$day   = $month_start->modify( '-' . (int) $month_start->format( 'w' ) . ' days' );
		$today = $now->format( 'Y-m-d' );
		do {
			echo '<tr>';
			for ( $i = 0; $i < 7; $i++ ) {
				$key   = $day->format( 'Y-m-d' );
				$other = $day->format( 'm' ) !== $month_start->format( 'm' );
				$cls   = array();
				if ( $other ) {
					$cls[] = 'other';
				}
				if ( $key === $today ) {
					$cls[] = 'today';
				}
				echo '<td class="' . esc_attr( implode( ' ', $cls ) ) . '"><span class="calendar__d">' . esc_html( $day->format( 'j' ) ) . '</span>';
				if ( ! $other && ! empty( $by_day[ $key ] ) ) {
					foreach ( $by_day[ $key ] as $d ) {
						echo '<a class="calendar__ev calendar__ev--' . esc_attr( $d['status'] ) . '" href="#s-' . (int) $d['id'] . '" title="' . esc_attr( $d['course_title'] . ' ' . $d['prefecture'] . ' ' . $d['status_label'] ) . '">' . esc_html( $d['course_title'] ) . '<span>' . esc_html( $d['prefecture'] ) . '</span></a>';
					}
				}
				echo '</td>';
				$day = $day->modify( '+1 day' );
			}
			echo '</tr>';
		} while ( $day <= $month_end );
		?>
		</tbody>
	</table>

	<h2 class="sub-title"><?php echo esc_html( $month_start->format( 'n月' ) ); ?>の開催一覧（<?php echo count( $sessions ); ?>件）</h2>
	<?php if ( $is_past ) : ?>
		<p class="empty">終了した月です。</p>
	<?php else : ?>
		<?php get_template_part( 'parts/session-list', null, array( 'sessions' => $sessions ) ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
