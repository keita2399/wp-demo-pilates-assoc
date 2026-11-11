<?php
get_header();
?>
<div class="page-hero">
	<div class="container">
		<h1>ページが見つかりません</h1>
	</div>
</div>
<div class="container content narrow">
	<p>お探しのページは移動したか、削除された可能性があります。旧サイトのURLは新しいURLへ自動で転送していますが、転送先が見つからなかったページです。</p>
	<ul class="link-list">
		<li><a href="<?php echo esc_url( phi_page_url( 'start' ) ); ?>">資格を取りたい方へ</a></li>
		<li><a href="<?php echo esc_url( phi_page_url( 'members' ) ); ?>">資格をお持ちの方へ</a></li>
		<li><a href="<?php echo esc_url( phi_page_url( 'studios' ) ); ?>">認定スタジオを探す</a></li>
		<li><a href="<?php echo esc_url( phi_page_url( 'schedule' ) ); ?>">開催日程</a></li>
	</ul>
</div>
<?php
get_footer();
