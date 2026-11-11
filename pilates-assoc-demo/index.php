<?php
/* お知らせの一覧（ほかに当てはまるテンプレートが無いときもこれを使う） */
get_header();
?>
<div class="page-hero">
	<div class="container">
		<?php phi_breadcrumb( array( 'お知らせ' => '' ) ); ?>
		<h1>お知らせ</h1>
	</div>
</div>
<div class="container content narrow">
	<?php if ( have_posts() ) : ?>
		<ul class="news-list news-list--full">
			<?php
			while ( have_posts() ) :
				the_post();
				$cats = get_the_category();
				?>
				<li>
					<time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
					<?php if ( $cats ) : ?><span class="tag"><?php echo esc_html( $cats[0]->name ); ?></span><?php endif; ?>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p class="empty">お知らせはまだありません。</p>
	<?php endif; ?>
</div>
<?php
get_footer();
