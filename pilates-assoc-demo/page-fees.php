<?php
/* 料金一覧。コースの料金をそのまま並べる（このページに料金は書かれていない） */
get_header();
?>
<div class="page-hero">
	<div class="container">
		<?php phi_breadcrumb( array( '料金一覧' => '' ) ); ?>
		<h1>料金一覧</h1>
		<p>すべて税込です。開催日程・コース紹介に表示している料金と同じ値です。</p>
	</div>
</div>
<div class="container content">
	<p class="demo-note">このページの金額は、各コースの編集画面に登録した料金から自動で作っています。料金を変えるときに、このページを直す必要はありません。</p>
	<?php foreach ( phi_course_categories() as $key => $label ) : ?>
		<?php
		$list = phi_get_courses( $key );
		if ( ! $list ) {
			continue;
		}
		?>
		<h2 class="sub-title"><?php echo esc_html( $label ); ?></h2>
		<div class="table-wrap">
		<table class="table table--fees">
			<thead><tr><th>コース</th><th>時間</th><th class="num">受講料</th><th class="num">認定試験料</th><th class="num">合計</th><th>受講の前提</th></tr></thead>
			<tbody>
			<?php
			foreach ( $list as $c ) :
				$f      = phi_course_fees( $c->ID );
				$prereq = (int) get_post_meta( $c->ID, '_phi_prereq', true );
				?>
				<tr>
					<th scope="row"><a href="<?php echo esc_url( get_permalink( $c ) ); ?>"><?php echo esc_html( $c->post_title ); ?></a></th>
					<td><?php echo esc_html( get_post_meta( $c->ID, '_phi_hours', true ) ); ?></td>
					<td class="num"><?php echo esc_html( phi_yen( $f['fee'] ) ); ?></td>
					<td class="num"><?php echo $f['exam'] ? esc_html( phi_yen( $f['exam'] ) ) : '―'; ?></td>
					<td class="num"><strong><?php echo esc_html( phi_yen( $f['total'] ) ); ?></strong></td>
					<td><?php echo $prereq ? esc_html( get_the_title( $prereq ) . 'の修了' ) : 'なし'; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		</div>
	<?php endforeach; ?>
</div>
<?php
get_footer();
