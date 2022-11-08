<?php
/*
Plugin Name: Ganohr's Toggle Shortcode
Plugin URI: https://ganohr.net/blog/ganohrs-toggle-shortcode/
Description: You can insert to WordPress that toggle-code for CSS-based Or details tag. 簡単にCSSベース又はdetailsタグによる折りたたみコードをワードプレスへ追加できます。<strong>Usage（使い方）: </strong> &#091;toggle&nbsp;title="title&nbsp;here"&nbsp;(optional)load="open&nbsp;/&nbsp;close"&nbsp;(optional)suffix="(empty)&nbsp;or&nbsp;1&nbsp;to&nbsp;20&nbsp;or&nbsp;black&nbsp;or&nbsp;details..."&#093;contents&nbsp;here&#091;/toggle&#093;
Version: 0.1.0
Author: Ganohr
Author URI: https://ganohr.net/
License: GPL2
*/
?>
<?php
/*	Copyright 2018 Ganohr (email : ganohr@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	 published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
// 直接呼び出しは禁止
if ( ! defined('ABSPATH') ) {
	exit();
}

// 関数がなければ定義する
if ( ! function_exists( 'ganohrs_toggle_shortcode_func' ) ) {
	require_once( 'class-ganohrs-toggle-shortcode-options.php' );

	// ショートコード本体
	function ganohrs_toggle_shortcode_func( $atts, $content ) {
		// CSSを出力する
		if(ganohrs_toggle_shortcode_css_enqueue_or_head() === 'enqueue') {
			ganohrs_toggle_shortcode_load_css();
		}

		// 引数を処理する
		$a = shortcode_atts( array(
			  'title' => '折りたたみ可能コンテンツ'
			, 'load' => 'open'
			, 'suffix' => ''
		), $atts );

		// タイトルを取得
		$title = $a['title'];

		// サフィックスが引数で定義されていたらそれを考慮する
		$suffix = "";
		if(strlen($a['suffix']) > 0) {
			$suffix = $a['suffix'];
		}

		// 折りたたみの開閉状態を記憶しておく
		$is_closed = true;
		if( $a['load'] === 'open' ) {
			$is_closed = false;
		}

		// オプションがdetails-*ならdetailsタグで出力する。
		$style = ganohrs_toggle_shortcode_get_css_style();
		if (strpos($style, 'details') === 0) {
			// 折りたたみの開閉状態に応じてopenを付与する
			$opn = $is_closed ? '' : ' open';
			return <<<EOT
<div class="gnr-tgl-wrap">
<details class="gnr-tgl-details{$suffix}"$opn>
<summary class="gnr-tgl-summary{$suffix}">{$title}</summary>
<div class="gnr-tgl-contents">{$content}</div>
</details>
</div>
EOT;
		}

		// 折りたたみの開閉状態に応じてチェック状態を決定する
		$chk = $is_closed ? "" : ' checked="checked"';

		// 折りたたみコードを返却する
		return <<<EOT
<div class="gnr-tgl-wrap">
<div class="gnr-tgl-w{$suffix}">
<input type="checkbox" id="gnr-tgl-b{$suffix}"{$chk}>
<label class="gnr-tgl-t{$suffix}" for="gnr-tgl-b{$suffix}">{$title}</label>
<div class="gnr-tgl-c{$suffix}">{$content}</div>
</div>
</div>
EOT;
	}

	// CSS追加処理用の関数
	function ganohrs_toggle_shortcode_load_css() {
		// WordPress管理画面ならCSSを追加しない
		if( is_admin() ) {
			return;
		}

		//AMPページなら、CSSロードはしない
		if( (function_exists('is_amp') && is_amp())
			|| (function_exists('is_amp_endpoint') && is_amp_endpoint())
			|| (function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()) ) {
			return;
		}

		// handleを定義
		$handle = "ganohrs-toggle-shortcode";

		// CSSスタイルシートの切り替え
		$toggle_style = ganohrs_toggle_shortcode_get_css_style();

		// CSSスタイルシートの格納先を記憶
		$src = plugins_url($handle . '-' .$toggle_style . '.css', __FILE__);

		// CSSバージョン番号
		$ver = "0.0.2";

		// enqueue/headに応じてCSSを追加する
		if( ganohrs_toggle_shortcode_css_enqueue_or_head() === 'enqueue' ) {
			// enqueue

			// スタイルシートをエンキューする
			if( !wp_style_is( $handle ) ) {
				wp_enqueue_style(
					$handle,
					$src,
					false,
					$ver,
					"all"
				);
			}

		} else {
			// head

			// versionを追加しておく
			$src .= "?ver={$ver}";

			// スタイルシートの呼び出しをヘッダに直接出力する
			echo "<link rel='stylesheet'"
				. " id='{$handle}'"
				. " href='{$src}'"
				. " type='text/css'"
				. " media='all' />"
				. PHP_EOL;
		}

	}

	// 「'enqueue'」か「'head'」かを返す。
	// ※ 'enqueue'なら「wp_enqueue_style」でCSS追加
	// ※ 'head'なら「add_action」で「wp_head」にアクションをフックして追加
	// ※ 基本はenqueueを推奨
	function ganohrs_toggle_shortcode_css_enqueue_or_head() {
		$option = get_option( 'gts_options' );
		if ( $option ) {
			$enqueue_or_head = @$option['enqueue_or_head'];
			if ( ! $enqueue_or_head || strlen( $enqueue_or_head ) == 0 ) {
				$enqueue_or_head = 'enqueue';
			}
			return $enqueue_or_head;
		}
		return 'enqueue';
	}

	// CSSスタイルシートの定義を切り替える
	function ganohrs_toggle_shortcode_get_css_style() {
		$option = get_option( 'gts_options' );
		if ( $option ) {
			$style = @$option['style'];
			if ( ! $style || strlen( $style ) == 0 ) {
				$style = 'details-normal';
			}
			return $style;
		}
		return 'details-normal';
	}

	// CSS追加アクションを定義
	if(ganohrs_toggle_shortcode_css_enqueue_or_head() === 'head') {
		add_action(
			'wp_head',
			'ganohrs_toggle_shortcode_load_css'
		);
	}

	// ショートコードを「[toggle]～[/toggle]」で呼び出せるよう登録する
	add_shortcode( 'toggle', 'ganohrs_toggle_shortcode_func' );
}
?>
