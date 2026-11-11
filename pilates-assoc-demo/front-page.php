<?php
get_header();
$next_sessions = phi_get_sessions( array( 'limit' => 4 ) );
$news          = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 3 ) );
?>
<section class="hero">
	<div class="container hero__inner">
		<p class="hero__eyebrow">ピラティス指導者の養成と資格認定</p>
		<h1 class="hero__title">正しく動ける身体を、<br>正しく伝えられる指導者へ。</h1>
		<p class="hero__lead">全国の認定スタジオで、マットからマシンまでの養成コースを開催しています。</p>
	</div>
</section>

<section class="paths" aria-labelledby="paths-title">
	<div class="container">
		<h2 id="paths-title" class="section-title">目的からさがす</h2>
		<div class="paths__grid">
			<a class="path-card path-card--start" href="<?php echo esc_url( phi_page_url( 'start' ) ); ?>">
				<span class="path-card__who">これから資格を取りたい方</span>
				<span class="path-card__what">コースの選び方・料金・日程</span>
				<span class="path-card__go">養成コースを見る →</span>
			</a>
			<a class="path-card path-card--members" href="<?php echo esc_url( phi_page_url( 'members' ) ); ?>">
				<span class="path-card__who">資格をお持ちのインストラクターの方</span>
				<span class="path-card__what">資格更新（CEU）・上位コース</span>
				<span class="path-card__go">資格更新の案内へ →</span>
			</a>
			<a class="path-card path-card--find" href="<?php echo esc_url( phi_page_url( 'studios' ) ); ?>">
				<span class="path-card__who">レッスンを受けたい方</span>
				<span class="path-card__what">お近くの認定スタジオ・インストラクター</span>
				<span class="path-card__go">地域から探す →</span>
			</a>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-head">
			<h2 class="section-title">次回の開催</h2>
			<a class="more" href="<?php echo esc_url( phi_page_url( 'schedule' ) ); ?>">開催日程をすべて見る →</a>
		</div>
		<?php get_template_part( 'parts/session-list', null, array( 'sessions' => $next_sessions ) ); ?>
	</div>
</section>

<section class="section section--tint">
	<div class="container">
		<div class="section-head">
			<h2 class="section-title">養成コース</h2>
			<a class="more" href="<?php echo esc_url( phi_page_url( 'fees' ) ); ?>">料金一覧 →</a>
		</div>
		<div class="course-grid">
			<?php
			foreach ( array_slice( array_merge( phi_get_courses( 'mat' ), phi_get_courses( 'machine' ) ), 0, 6 ) as $c ) {
				get_template_part( 'parts/course-card', null, array( 'course' => $c ) );
			}
			?>
		</div>
		<p class="center"><a class="btn btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'phi_course' ) ); ?>">すべてのコースを見る</a></p>
	</div>
</section>

<section class="section">
	<div class="container two-col">
		<div>
			<div class="section-head">
				<h2 class="section-title">お知らせ</h2>
				<a class="more" href="<?php echo esc_url( phi_page_url( 'news' ) ); ?>">一覧 →</a>
			</div>
			<ul class="news-list">
				<?php foreach ( $news as $n ) : ?>
					<li><time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d', $n ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d', $n ) ); ?></time><a href="<?php echo esc_url( get_permalink( $n ) ); ?>"><?php echo esc_html( get_the_title( $n ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="finder">
			<h2 class="section-title">認定スタジオを探す</h2>
			<form action="<?php echo esc_url( phi_page_url( 'studios' ) ); ?>" method="get" class="finder__form">
				<label for="f-region">地方</label>
				<select id="f-region" name="region">
					<option value="">すべての地方</option>
					<?php foreach ( array_keys( phi_regions() ) as $r ) : ?>
						<option value="<?php echo esc_attr( $r ); ?>"><?php echo esc_html( $r ); ?></option>
					<?php endforeach; ?>
				</select>
				<button class="btn btn--primary" type="submit">探す</button>
			</form>
			<p class="finder__note">全国<?php echo (int) wp_count_posts( 'phi_studio' )->publish; ?>か所の認定スタジオを掲載しています。</p>
		</div>
	</div>
</section>
<?php
get_footer();
