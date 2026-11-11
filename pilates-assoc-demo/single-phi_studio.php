<?php
get_header();
the_post();
$id        = get_the_ID();
$equipment = array( 'mat' => 'マット', 'reformer' => 'リフォーマー', 'chair' => 'チェア', 'cadillac' => 'キャデラック', 'barrel' => 'バレル' );
$eq        = (array) get_post_meta( $id, '_phi_equipment', true );
$people    = get_posts(
	array(
		'post_type'      => 'phi_instructor',
		'posts_per_page' => -1,
		'meta_query'     => array(
			'relation' => 'AND',
			array( 'key' => '_phi_studio_id', 'value' => $id ),
			array( 'key' => '_phi_public', 'value' => 'no', 'compare' => '!=' ),
		),
	)
);
?>
<div class="page-hero page-hero--find">
	<div class="container">
		<?php phi_breadcrumb( array( '認定スタジオを探す' => phi_page_url( 'studios' ), get_the_title() => '' ) ); ?>
		<h1><?php the_title(); ?></h1>
	</div>
</div>
<div class="container content narrow">
	<div class="entry"><?php the_content(); ?></div>
	<div class="spec">
		<dl>
			<dt>所在地</dt><dd><?php echo esc_html( get_post_meta( $id, '_phi_prefecture', true ) . get_post_meta( $id, '_phi_city', true ) . ' ' . get_post_meta( $id, '_phi_address', true ) ); ?></dd>
			<dt>使える器具</dt><dd><?php echo esc_html( implode( '、', array_intersect_key( $equipment, array_flip( $eq ) ) ) ); ?></dd>
			<dt>体験レッスン</dt><dd><?php echo 'yes' === get_post_meta( $id, '_phi_trial', true ) ? 'あり' : 'なし'; ?></dd>
			<dt>在籍インストラクター</dt><dd><?php echo $people ? esc_html( implode( '、', wp_list_pluck( $people, 'post_title' ) ) ) : '―'; ?></dd>
		</dl>
		<?php if ( get_post_meta( $id, '_phi_url', true ) ) : ?>
			<a class="btn btn--primary" href="<?php echo esc_url( get_post_meta( $id, '_phi_url', true ) ); ?>" target="_blank" rel="noopener">スタジオのWebサイトへ<span class="ext" aria-hidden="true">↗</span></a>
		<?php endif; ?>
	</div>
	<p><a class="more" href="<?php echo esc_url( add_query_arg( 'pref', get_post_meta( $id, '_phi_prefecture', true ), phi_page_url( 'studios' ) ) ); ?>">同じ都道府県のスタジオ →</a></p>
</div>
<?php
get_footer();
