</main>
<footer class="site-footer">
	<div class="container site-footer__grid">
		<div>
			<p class="site-footer__name"><?php bloginfo( 'name' ); ?></p>
			<p class="site-footer__note">提案用の試作サイトです。掲載内容はすべて架空です。</p>
		</div>
		<div>
			<p class="site-footer__head">目的からさがす</p>
			<ul>
				<li><a href="<?php echo esc_url( phi_page_url( 'start' ) ); ?>">資格を取りたい方へ</a></li>
				<li><a href="<?php echo esc_url( phi_page_url( 'members' ) ); ?>">資格をお持ちの方へ</a></li>
				<li><a href="<?php echo esc_url( phi_page_url( 'studios' ) ); ?>">認定スタジオ・インストラクターを探す</a></li>
			</ul>
		</div>
		<div>
			<p class="site-footer__head">ご案内</p>
			<ul>
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'phi_course' ) ); ?>">コース一覧</a></li>
				<li><a href="<?php echo esc_url( phi_page_url( 'schedule' ) ); ?>">開催日程</a></li>
				<li><a href="<?php echo esc_url( phi_page_url( 'fees' ) ); ?>">料金一覧</a></li>
				<li><a href="<?php echo esc_url( phi_page_url( 'ceu' ) ); ?>">資格更新（CEU）</a></li>
				<li><a href="<?php echo esc_url( phi_page_url( 'faq' ) ); ?>">よくある質問</a></li>
			</ul>
		</div>
		<div>
			<p class="site-footer__head">協会</p>
			<ul>
				<li><a href="<?php echo esc_url( phi_page_url( 'about' ) ); ?>">協会について</a></li>
				<li><a href="<?php echo esc_url( phi_page_url( 'news' ) ); ?>">お知らせ</a></li>
				<li><a href="<?php echo esc_url( phi_page_url( 'contact' ) ); ?>">お問い合わせ</a></li>
				<li><a href="<?php echo esc_url( phi_out_url( 'https://example.com/shop/', array( 'service' => 'shop' ) ) ); ?>"<?php if ( ! phi_is_dummy_url( 'https://example.com/shop/' ) ) : ?> target="_blank" rel="noopener"<?php endif; ?>>オンラインショップ（外部サイト）</a></li>
			</ul>
		</div>
	</div>
	<div class="container site-footer__bottom">
		<small>&copy; ピラティス指導者協会（架空）　WordPress試作</small>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
