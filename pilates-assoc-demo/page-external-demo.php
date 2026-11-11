<?php
/**
 * 試作用の案内ページ。
 * 架空のURL（example.com）へのリンクを押すと、ここに来る。
 * 本番では、ここに来ずに外部サービス（申込フォーム、お問い合わせフォーム、ショップ、スタジオのサイト）へ直接移動する。
 */
get_header();

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$service = isset( $_GET['service'] ) ? sanitize_key( wp_unslash( $_GET['service'] ) ) : '';
$session = isset( $_GET['session'] ) ? absint( $_GET['session'] ) : 0;
$studio  = isset( $_GET['studio'] ) ? absint( $_GET['studio'] ) : 0;
// phpcs:enable

$d = null;
if ( 'apply' === $service && $session && 'phi_session' === get_post_type( $session ) ) {
	$d = phi_session_data( $session );
}
$titles = array(
	'apply'   => '申込フォームへ移動します',
	'contact' => 'お問い合わせフォームへ移動します',
	'shop'    => 'オンラインショップへ移動します',
	'studio'  => 'スタジオのWebサイトへ移動します',
);
$title = isset( $titles[ $service ] ) ? $titles[ $service ] : '外部サイトへ移動します';
$back  = wp_get_referer();
?>
<div class="page-hero">
	<div class="container">
		<h1><?php echo esc_html( $title ); ?></h1>
		<p>試作のため、ここで止めています。本番では、このページを出さずに外部サービスへ直接移動します。</p>
	</div>
</div>
<div class="container content narrow">
	<?php if ( $d ) : ?>
		<p>本番では、次の内容を付けて現行の申込フォーム（外部サービス）を開きます。受講される方が、フォームでコースや日程を選び直す必要はありません。</p>
		<div class="spec">
			<dl>
				<dt>コース</dt><dd><?php echo esc_html( $d['course_title'] ); ?></dd>
				<dt>日程</dt><dd><?php echo esc_html( $d['date_label'] ); ?><?php echo $d['days_label'] ? '　' . esc_html( $d['days_label'] ) : ''; ?></dd>
				<dt>会場</dt><dd><?php echo esc_html( trim( $d['prefecture'] . '　' . $d['venue'] ) ); ?></dd>
				<dt>料金</dt><dd class="spec__total"><?php echo esc_html( $d['price_label'] ); ?></dd>
			</dl>
		</div>
		<p class="demo-note">料金はコースに登録した値です。申込フォームの側にも同じ料金を渡すので、サイトとフォームで金額が食い違うことがありません。外部サービスがURLで値を受け取れない場合は、コースと日程だけを渡す形にします（調査の段階で確認します）。</p>
	<?php elseif ( 'contact' === $service ) : ?>
		<p>お問い合わせは、現在お使いの外部フォームサービスをそのまま使います。今回のリニューアルでは作り替えません。</p>
	<?php elseif ( 'shop' === $service ) : ?>
		<p>オンラインショップは別のプロジェクトのため、リンクを置くだけにします。</p>
	<?php elseif ( 'studio' === $service ) : ?>
		<p><?php echo $studio ? esc_html( get_the_title( $studio ) ) . 'の' : ''; ?>Webサイトを新しいタブで開きます（試作のスタジオは架空です）。</p>
	<?php else : ?>
		<p>外部のサイトを開きます。</p>
	<?php endif; ?>
	<p>
		<?php if ( $back ) : ?>
			<a class="btn btn--ghost" href="<?php echo esc_url( $back ); ?>">← 前のページに戻る</a>
		<?php endif; ?>
		<a class="btn btn--ghost" href="<?php echo esc_url( phi_page_url( 'schedule' ) ); ?>">開催日程へ</a>
	</p>
</div>
<?php
get_footer();
