<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">本文へ移動</a>
<div class="demo-bar" role="note">提案用のWordPress試作サイトです。団体名・料金・日程・スタジオ・人物はすべて架空のダミーデータです。</div>
<header class="site-header">
	<div class="container site-header__inner">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="site-logo__mark" aria-hidden="true">P</span>
			<span class="site-logo__text"><?php bloginfo( 'name' ); ?><small>架空の団体・試作</small></span>
		</a>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="global-nav">
			<span class="nav-toggle__bar"></span><span class="screen-reader-text">メニュー</span>
		</button>
		<nav id="global-nav" class="global-nav" aria-label="グローバルナビ">
			<ul>
				<?php
				$nav = array(
					'start'    => '資格を取りたい方',
					'members'  => '資格をお持ちの方',
					'studios'  => 'スタジオを探す',
					'schedule' => '開催日程',
					'fees'     => '料金',
					'news'     => 'お知らせ',
				);
				foreach ( $nav as $slug => $label ) {
					$current = is_page( $slug ) || ( 'news' === $slug && ( is_home() || is_singular( 'post' ) ) ) || ( 'start' === $slug && ( is_post_type_archive( 'phi_course' ) || is_singular( 'phi_course' ) ) ) || ( 'studios' === $slug && is_singular( 'phi_studio' ) );
					echo '<li><a href="' . esc_url( phi_page_url( $slug ) ) . '"' . ( $current ? ' aria-current="page"' : '' ) . '>' . esc_html( $label ) . '</a></li>';
				}
				?>
				<li class="global-nav__contact"><a href="<?php echo esc_url( phi_page_url( 'contact' ) ); ?>">お問い合わせ</a></li>
			</ul>
		</nav>
	</div>
</header>
<main id="main" class="site-main">
