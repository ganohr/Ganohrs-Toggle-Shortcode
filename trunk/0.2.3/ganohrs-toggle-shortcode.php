<?php
/*
Plugin Name: Ganohr's Toggle Shortcode
Plugin URI: https://ganohr.net/blog/ganohrs-toggle-shortcode/
Description: You can insert to WordPress that toggle-code for CSS-based Or details tag. 簡単にCSSベース又はdetailsタグによる折りたたみコードをワードプレスへ追加できます。<strong>Usage（使い方）: </strong> &#091;toggle&nbsp;title="title&nbsp;here"&nbsp;(optional)load="open&nbsp;/&nbsp;close"&nbsp;(optional)suffix="(empty)&nbsp;or&nbsp;1&nbsp;to&nbsp;20&nbsp;or&nbsp;black&nbsp;or&nbsp;details..."&#093;contents&nbsp;here&#091;/toggle&#093;
Version: 0.2.3
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
if (!defined('ABSPATH')) {
	exit();
}

// 関数がなければ定義する
if (!function_exists('ganohrs_toggle_shortcode_func')) :
	require_once('class-ganohrs-toggle-shortcode-options.php');

	function ganohrs_toggle_shortcode_fixautofm_head($content)
	{
		$head_p_pos = strpos($content, '<p>');
		$head_p_pos = $head_p_pos ? $head_p_pos : 0;
		$tail_p_pos = strpos($content, '</p>');
		$tail_p_pos = $tail_p_pos ? $tail_p_pos : 0;
		if ($head_p_pos > $tail_p_pos) {
			$content = substr($content, 0, $tail_p_pos) . substr($content, $tail_p_pos + 4);
		}
		$remover = function ($c, $tag) {
			$pos = strpos($c, $tag);
			$len = strlen($tag);
			while ($pos === 0) {
				$c = substr($c, $len);
				$pos = strpos($c, $tag);
			}
			return trim($c);
		};
		$content = $remover($content, '<br>');
		$content = $remover($content, '<br/>');
		$content = $remover($content, '<br />');

		return $content;
	}
	function ganohrs_toggle_shortcode_fixautofm_tail($content)
	{
		$head_p_pos = strrpos($content, '<p>');
		$head_p_pos = $head_p_pos ? $head_p_pos : 0;
		$tail_p_pos = strrpos($content, '</p>');
		$tail_p_pos = $tail_p_pos ? $tail_p_pos : 0;
		if ($head_p_pos > $tail_p_pos) {
			$content = substr($content, 0, $head_p_pos);
		}
		$remover = function ($c, $tag) {
			$pos = strrpos($c, $tag);
			$len = strlen($tag);
			while ($pos !== false) {
				if (strlen($c) !== ($pos + $len)) {
					break;
				}
				$c = substr($c, 0, -$len);
				// PHP8未満のバグ対処
				if ($c === false) {
					$c = '';
					break;
				}
				$pos = strrpos($c, $tag);
			}
			return trim($c);
		};

		$content = $remover($content, '<br>');
		$content = $remover($content, '<br/>');
		$content = $remover($content, '<br />');

		return $content;
	}

	// ショートコード本体
	function ganohrs_toggle_shortcode_func($atts, $content)
	{
		// 必要ならCSSを出力する
		if (ganohrs_toggle_shortcode_css_enqueue_or_head() === 'enqueue') {
			ganohrs_toggle_shortcode_load_css();
		}

		// 無駄なスペース等除去しておく
		$content = trim($content);

		// 引数を処理する
		$a = shortcode_atts(array(
			'title' => '折りたたみ可能コンテンツ', 'load' => 'open', 'suffix' => ''
		), $atts);

		// タイトルを取得
		$title = $a['title'];

		// サフィックスを取得
		$suffix = "";
		if (strlen($a['suffix']) > 0) {
			$suffix = $a['suffix'];
		}

		// suffixを除去するかどうか
		if (isset($suffix) && strlen($suffix) > 0) {
			if (false
				||   is_numeric($suffix) && ganohrs_toggle_shortcode_get_replace_number_suffix()
				|| ! is_numeric($suffix) && ganohrs_toggle_shortcode_get_replace_none_number_suffix()
			) {
				$suffix = "";
			}
		}

		// 折りたたみの開閉状態を記憶しておく
		$is_closed = true;
		if ($a['load'] === 'open') {
			$is_closed = false;
		}

		// Fix Autoformatにfixが指定されていたら、先頭や最後の<p></p>や<br>のみを除去する
		if (ganohrs_toggle_shortcode_get_fixautofm() === 'fix') {
			// 中途半端なWPのオートフォーマットにより追加された先頭の</p>や<br>を除去する
			$content = ganohrs_toggle_shortcode_fixautofm_head($content);

			// 中途半端なWPのオートフォーマットにより追加された文末の<p>や<br>を除去する
			$content = ganohrs_toggle_shortcode_fixautofm_tail($content);
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
<div class="gnr-tgl-contents{$suffix}">{$content}</div>
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
	function ganohrs_toggle_shortcode_load_css()
	{
		// そもそもショートコードが含まれていない投稿にはCSSを付加しない
		if (@strpos(get_the_content(null, false), "[toggle") === false && !is_admin()) {
			return;
		}

		// AMPページの場合、設定に応じてCSSを読み込む
		if (ganohrs_is_amp()) {
			if (!ganohrs_toggle_shortcode_get_output_css_when_amp()) {
				return;
			}
		} elseif (!ganohrs_toggle_shortcode_get_output_css_when_none_amp()) {
			return;
		}

		// CSSスタイルシートの切り替え
		$toggle_style = ganohrs_toggle_shortcode_get_css_style();

		// handleを定義
		$handle = "ganohrs-toggle-shortcode";

		// CSSスタイルシートの格納先を記憶
		$src = plugins_url($handle . '-' . $toggle_style . '.css', __FILE__);

		// CSSバージョン番号
		$ver = "0.2.2";

		// enqueue/headに応じてCSSを追加する
		if (ganohrs_toggle_shortcode_css_enqueue_or_head() === 'enqueue') {
			// enqueue

			// スタイルシートをエンキューする
			if (!wp_style_is($handle)) {
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

	function ganohrs_toggle_shortcode_get_replace_none_number_suffix()
	{
		$option = get_option('gts_options');
		if ($option) {
			$replace_none_number_suffix = @$option['replace_none_number_suffix'];
			if (!$replace_none_number_suffix || strlen($replace_none_number_suffix) == 0) {
				return false;
			}
			return $replace_none_number_suffix === 'yes';
		}
		return false;
	}

	function ganohrs_toggle_shortcode_get_replace_number_suffix()
	{
		$option = get_option('gts_options');
		if ($option) {
			$replace_number_suffix = @$option['replace_number_suffix'];
			if (!$replace_number_suffix || strlen($replace_number_suffix) == 0) {
				return true;
			}
			return $replace_number_suffix === 'yes';
		}
		return false;
	}

	function ganohrs_toggle_shortcode_get_output_css_when_amp()
	{
		$option = get_option('gts_options');
		if ($option) {
			$output_css_when_amp = @$option['output_css_when_amp'];
			if (!$output_css_when_amp || strlen($output_css_when_amp) == 0) {
				return true;
			}
			return $output_css_when_amp === 'yes';
		}
		return false;
	}

	function ganohrs_toggle_shortcode_get_output_css_when_none_amp()
	{
		$option = get_option('gts_options');
		if ($option) {
			$output_css_when_none_amp = @$option['output_css_when_none_amp'];
			if (!$output_css_when_none_amp || strlen($output_css_when_none_amp) == 0) {
				return false;
			}
			return $output_css_when_none_amp === 'yes';
		}
		return false;
	}

	function ganohrs_toggle_shortcode_get_fixautofm()
	{
		$option = get_option('gts_options');
		if ($option) {
			$fix_autoformat = @$option['fix_autoformat'];
			if (!$fix_autoformat || strlen($fix_autoformat) == 0) {
				$fix_autoformat = 'fix';
			}
			return $fix_autoformat;
		}
		return 'fix';
	}

	// 「'enqueue'」か「'head'」かを返す。
	// ※ 'enqueue'なら「wp_enqueue_style」でCSS追加
	// ※ 'head'なら「add_action」で「wp_head」にアクションをフックして追加
	// ※ 基本はenqueueを推奨
	function ganohrs_toggle_shortcode_css_enqueue_or_head()
	{
		$option = get_option('gts_options');
		if ($option) {
			$enqueue_or_head = @$option['enqueue_or_head'];
			if (!$enqueue_or_head || strlen($enqueue_or_head) == 0) {
				$enqueue_or_head = 'enqueue';
			}
			return $enqueue_or_head;
		}
		return 'enqueue';
	}

	// CSSスタイルシートの定義を切り替える
	function ganohrs_toggle_shortcode_get_css_style()
	{
		$option = get_option('gts_options');
		if ($option) {
			$style = @$option['style'];
			if (!$style || strlen($style) == 0) {
				$style = 'details-normal';
			}
			return $style;
		}
		return 'details-normal';
	}

	// CSS追加アクションを定義
	if (ganohrs_toggle_shortcode_css_enqueue_or_head() === 'head') {
		if (!has_action('wp_head', 'ganohrs_toggle_shortcode_load_css')) {
			add_action(
				'wp_head',
				'ganohrs_toggle_shortcode_load_css'
			);
		}
	} elseif (has_action('wp_head', 'ganohrs_toggle_shortcode_load_css')) {
		remove_action(
			'wp_head',
			'ganohrs_toggle_shortcode_load_css'
		);
	}
	// 管理画面へCSS追加アクションを登録
	add_action('admin_print_styles', 'ganohrs_toggle_shortcode_load_css');

	// ショートコードを「[toggle]～[/toggle]」で呼び出せるよう登録する
	add_shortcode('toggle', 'ganohrs_toggle_shortcode_func');

endif;

////AMPページか否か判定する
if (!function_exists('ganohrs_is_amp')) :

	function ganohrs_is_amp()
	{
		if (function_exists('is_amp') && is_amp()) {
			return true;
		} elseif (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
			return true;
		} elseif (function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()) {
			return true;
		} elseif (@$_GET['amp'] === '1') {
			return true;
		} elseif (@$_GET['type'] === 'AMP') {
			return true;
		}
		$uri = ganohrs_get_uri_full();
		return ganohrs_is_amp_pattern($uri);
	}
	function ganohrs_get_uri_full()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http')
			. '://'
			. $_SERVER['SERVER_NAME']
			. $_SERVER['REQUEST_URI'];
	}
	function ganohrs_is_amp_pattern($uri)
	{
		if (ganohrs_tail_pattern_matched($uri, '/amp')) {
			return true;
		}
		if (ganohrs_tail_pattern_matched($uri, '/amp/')) {
			return true;
		}
		if (ganohrs_tail_pattern_matched($uri, '?amp=1')) {
			return true;
		}
		if (ganohrs_tail_pattern_matched($uri, 'type=AMP')) {
			return true;
		}
		return false;
	}
	function ganohrs_tail_pattern_matched($target, $pattern)
	{
		if (empty($target) && empty($pattern)) {
			return true;
		} elseif (empty($target)) {
			return false;
		} elseif (empty($pattern)) {
			return false;
		}
		$s_end = strlen($target);
		$s_len = strlen($pattern);
		$offset = $s_end - $s_len;
		if ($offset < 0) {
			return false;
		}
		$pos = strpos($target, $pattern, $offset);
		return $pos === $offset;
	}
	function ganohrs_remove_amp_uri_part($uri, $pattern)
	{
		$s_end = strlen($uri);
		$s_len = strlen($pattern);
		$offset = $s_end - $s_len;
		if ($offset < 0) {
			return $uri;
		}
		$pos = strpos($uri, $pattern, $offset);
		if ($pos === $offset) {
			return substr($uri, 0, $pos);
		}
		return $uri;
	}
endif;

?>
