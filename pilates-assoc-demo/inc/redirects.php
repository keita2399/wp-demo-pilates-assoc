<?php
/**
 * 旧URL → 新URL の転送表（301）。
 * 転送表は「設定 > 旧URLの転送」で1行1件で管理する。プラグインは使わない。
 * 書式：旧URLのパス  新URLのパス（空白区切り）
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 転送表を配列で返す（旧パス => 新パス） */
function phi_redirect_map() {
	$raw = (string) get_option( 'phi_redirects', '' );
	$map = array();
	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line || 0 === strpos( $line, '#' ) ) {
			continue;
		}
		$parts = preg_split( '/\s+/', $line );
		if ( count( $parts ) < 2 ) {
			continue;
		}
		$map[ phi_normalize_path( $parts[0] ) ] = $parts[1];
	}
	return $map;
}

/** パスをそろえる（日本語URLのエンコード違い、末尾のスラッシュの有無を吸収） */
function phi_normalize_path( $path ) {
	$path = (string) wp_parse_url( $path, PHP_URL_PATH ) . ( wp_parse_url( $path, PHP_URL_QUERY ) ? '?' . wp_parse_url( $path, PHP_URL_QUERY ) : '' );
	$path = rawurldecode( $path );
	$path = '/' . ltrim( $path, '/' );
	if ( false === strpos( $path, '?' ) && false === strpos( basename( $path ), '.' ) ) {
		$path = trailingslashit( $path );
	}
	return $path;
}

add_action( 'template_redirect', function () {
	if ( is_admin() ) {
		return;
	}
	$map = phi_redirect_map();
	if ( ! $map ) {
		return;
	}
	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	// サブディレクトリに置いた場合（Playground など）に備えて、サイトのパスを外す。
	$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( $base && '/' !== $base && 0 === strpos( $request, rtrim( $base, '/' ) ) ) {
		$request = substr( $request, strlen( rtrim( $base, '/' ) ) );
	}
	$candidates = array( phi_normalize_path( $request ) );
	// クエリ付きで一致しなければ、パスだけでも探す
	$path_only = phi_normalize_path( (string) wp_parse_url( $request, PHP_URL_PATH ) );
	$candidates[] = $path_only;
	foreach ( $candidates as $c ) {
		if ( isset( $map[ $c ] ) ) {
			$to = $map[ $c ];
			if ( 0 !== strpos( $to, 'http' ) ) {
				$to = home_url( '/' . ltrim( $to, '/' ) );
			}
			wp_safe_redirect( $to, 301, 'phi-redirect' );
			exit;
		}
	}
}, 1 );
