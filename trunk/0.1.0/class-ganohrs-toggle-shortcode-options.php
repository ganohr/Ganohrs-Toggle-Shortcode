<?php
/**
 * Ganohr's Toggle Shortcode
 *
 * PHP Version >= 5.0 (Tested 8.1.6 & 7.4.28)
 *
 * @since	   0.0.1
 * @package    Ganohrs Toggle Shortcode
 * @author	   Ganohr<ganohr@gmail.com>
 */

// 直接呼び出しは禁止
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'ganohrs_toggle_shortcode_Options' ) ) :


	/**
	 * Ganohr's Toggle Shortcode Options
	 *
	 * @author	   Ganohr<ganohr@gmail.com>
	 * @return	   void
	 */
	class ganohrs_toggle_shortcode_Options {

		/**
		 * 設定ページ用の識別ID
		 */
		const PAGE_ID = 'ganohrs-toggle-shortcode-options';

		/**
		 * オプション記憶用
		 */
		private $options = array();

		/**
		 * コンストラクタ
		 *
		 * @return	   void
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );
			add_filter( 'plugin_action_links_ganohrs-toggle-shortcode/ganohrs-toggle-shortcode.php', array( $this, 'add_plugin_action_links' ) );
		}

		/**
		 * プラグイン一覧に設定リンクを付加する
		 *
		 * @param array $links プラグイン一覧のリンクリスト
		 * @return array 更新後のプラグイン一覧のリンクリスト
		 */
		function add_plugin_action_links( $links ) {
			$url = admin_url( 'options-general.php?page=' . self::PAGE_ID );
			array_unshift( $links, '<a href="' . esc_url($url) . '">' . __('Settings') . '</a>' );
			return $links;
		}

		/**
		 * 設定ページへプラグインを追加する
		 *
		 * @return	   void
		 */
		public function add_plugin_page() {
			$load_hook = add_options_page(
				"Ganohr's Toggle Shortcode",
				"Ganohr's Toggle Shortcode",
				'manage_options',
				self::PAGE_ID,
				array( $this, 'admin_manage_page' )
			);
		}

		/**
		 * オプションページ
		 *
		 * @return	   void
		 */
		public function admin_manage_page() {
			$this->options = get_option( 'gts_options' );

			?>
			<div class="wrap">
			<h1>Ganohr's Toggle Shortcode</h1>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'gts_options_group' );
					do_settings_sections( 'gts_options' );

					submit_button();
				?>
			</form>
			</div>
			<style>h2 {
				background: white;
				line-height: 2;
				font-weight: bold;
				font-size: 1.4rem;
				border-left: 0.3rem solid black;
				padding: 0.3rem;
				margin: 0.3rem 0;
			}</style>
			<h2>Basic Usage / 基本的な使い方</h2>
			<p><pre>[toggle title="title here" load="open"]contents here[/toggle]</pre></p>
			<p>&nbsp;</p>
			<h2>Details styles informations / Detailsスタイルに関して</h2>
			<p>Please choose styles named "Details xxx" unless you have a specific reason is nothing.</p>
			<p>特別な理由がない限り、"Details xxx"と名前が付いているスタイルを選択して下さい。</p>
			<p>&nbsp;</p>
			<p>Styles named "Details xxx" have an unlimited number of folds and work fine without a suffix.</p>
			<p>"Details xxx"と名前が付いているスタイルは、折り畳める件数に上限がなく、またsuffixを指定しなくても正しく動作します。</p>
			<p>&nbsp;</p>
			<p>You can choose style named "Details xxx", it can provide toggled area with "&lt;details&gt; tag".</p>
			<p>スタイルで"Details xxx"を選択すれば、"&lt;details&gt;タグ"を利用して折りたたみ領域を提供できます。</p>
			<p>&nbsp;</p>
			<p>You can choose suffix named "Details xxx", it can provide toggled area with "&lt;details&gt; tag".</p>
			<p>suffixで"Details xxx"を選択すれば、"&lt;details&gt;タグ"を利用して折りたたみ領域を提供できます。</p>
			<p>&nbsp;</p>
			<h2>Usability - Contrast / ユーザービリティ・コントラスト</h2>
			<p>Named "Details xxx" and it not named "SNS" or "Designed", it styles color scheme is usability-friendly and has a good contrast ratio.</p>
			<p>"Details xxx"と名付けられており"SNS"や"Designed"を含まないものは、ユーザービリティに配慮されており、良好なコントラスト比が確保されています。</p>
			<p>&nbsp;</p>
			<h2>Enqueue Type / エンキュー方法</h2>
			<p>You can chose CSS Enqueue Type (Type "Enqueue" will append CSS file to footer, or Type "Head" will append WP_Head).</p>
			<p>CSSのエンキュー方法を指定できます（"Enqueue"ならフッターへ、"Head"ならWP_Headを利用して付加します。）</p>
			<p>&nbsp;</p>
			<p>For Enqueue Type, "Enqueue" is recommended for SEO, but "Head" is recommended if you want to apply your own style.</p>
			<p>Enqueue TypeはSEOの観点から"Enqueue"を推奨しますが、独自のスタイルを適用したい場合は"Head"を推奨します。</p>
			<p>&nbsp;</p>
			<h2>What's Fix Autoformat? / Fix Autoformatってなに?</h2>
			<p>Do you have weird empty spaces above and below inside your toggled content? This is caused by WordPress autoformatting. The latest version provides the intended layout by removing line break tags destroyed by auto formatting and line break tags added unnecessarily by the "Fix Autoformat" function.</p>
			<p>折りたたんだコンテンツの内部で、上下に変なスペースが空いていませんか? これはWordPressのオートフォーマットが原因です。最新版は"Fix Autoformat"機能により、オートフォーマットにより破壊された改行タグや無駄に追加された改行タグを除去することで、意図したレイアウトを提供します</p>
			<p>&nbsp;</p>
			<h2>How to adjust your own style? / どうやってスタイルを調整すればいいの?</h2>
			<p>If you want to adjust margins, etc., set Enqueue Type to "Head" and define CSS in Customizer's "Additional Styles". You can specify the following clazz names/ID names.</p>
			<p>マージンなどを調整したい場合、Enqueue Typeを"Head"にし、カスタマイザーの「追加スタイル」にCSSを定義して下さい。以下のクラス名/ID名を指定できます。</p>
<h3>Details Style</h3>
<pre>
.gnr-tgl-wrap : Wrapper Div Tag
	.gnr-tgl-details{suffix} : Details Tag
		.gnr-tgl-summary{suffix} : Summary Tag
		.gnr-tgl-contents{suffix}: Conents Div Tag
</pre>
<h3>Not Details Style</h3>
<pre>
.gnr-tgl-wrap : Wrapper Div Tag
	.gnr-tgl-w{suffix} : Inner wrapper Div Tag
		#gnr-tgl-b{suffix} : Checkbox Tag
		.gnr-tgl-t{suffix} : Title Label Tag
		.gnr-tgl-c{suffix} : Contents Div Tag
</pre>
			<?php
		}

		/**
		 * ページ初期化
		 *
		 * @return	   void
		 */
		public function page_init() {
			register_setting(
				'gts_options_group',
				'gts_options',
				array( $this, 'sanitize_and_check' )
			);

			add_settings_section(
				'gts_setting_section',
				'Settings',
				null,
				'gts_options'
			);
			add_settings_field(
				'style',
				'Choose Style',
				array( $this, 'style_callback' ),
				'gts_options',
				'gts_setting_section'
			);
			add_settings_field(
				'enqueue_or_head',
				'Enqueue Type',
				array( $this, 'enqueue_type_callback' ),
				'gts_options',
				'gts_setting_section'
			);
			add_settings_field(
				'fix_autoformat',
				'Fix Autoformat',
				array( $this, 'fix_autoformat_callback' ),
				'gts_options',
				'gts_setting_section'
			);
		}

		/**
		 * 入力値をサニタイズし、適切な値に設定する
		 *
		 * @param array $input POSTされた入力値の配列
		 * @return サニタイズされた入力値の配列
		 */
		public function sanitize_and_check( $input ) {
			$new_input = array();

			$new_input['style'] = isset( $input['style'] ) ? $input['style'] : 'details-nornaml';
			$new_input['enqueue_or_head'] = isset( $input['enqueue_or_head'] ) ? $input['enqueue_or_head'] : 'enqueue';
			$new_input['fix_autoformat'] = isset( $input['fix_autoformat'] ) ? $input['fix_autoformat'] : 'fix';
			return $new_input;
		}

		/**
		 * Fix Autoformat変更用コールバック
		 *
		 * @return	   void
		 */
		public function fix_autoformat_callback() {
			$fix_autoformat = is_array($this->options) ? $this->options['fix_autoformat'] : '';
			if ( strlen( $fix_autoformat ) === 0 ) {
				$fix_autoformat = 'fix';
			}
			?>
				<label for="fix_autoformat_fix" ><input id="fix_autoformat_fix"  type="radio" name="gts_options[fix_autoformat]" <?php echo ($fix_autoformat === 'fix' ? "checked" : "")?> value="fix"	   />Fix	</label>
				<label for="fix_autoformat_none"><input id="fix_autoformat_none" type="radio" name="gts_options[fix_autoformat]" <?php echo ($fix_autoformat !== 'fix' ? "checked" : "")?> value="nothing" />Nothing</label>
			<?php
		}

		/**
		 * Enqueue Type変更用コールバック
		 *
		 * @return	   void
		 */
		public function enqueue_type_callback() {
			$enqueue_type = is_array($this->options) ? $this->options['enqueue_or_head'] : '';
			if ( strlen( $enqueue_type ) === 0 ) {
				$enqueue_type = 'enqueue';
			}
			?>
				<label for="enqueue_type_enqueue"><input id="enqueue_type_enqueue" type="radio" name="gts_options[enqueue_or_head]" <?php echo ($enqueue_type === 'enqueue' ? "checked" : "")?> value="enqueue" />Enqueue</label>
				<label for="enqueue_type_head"	 ><input id="enqueue_type_head"    type="radio" name="gts_options[enqueue_or_head]" <?php echo ($enqueue_type === 'head'	? "checked" : "")?> value="head"	/>Head	 </label>
			<?php
		}

		/**
		 * スタイル変更用コールバック
		 *
		 * @return	   void
		 */
		public function style_callback() {

			$style = is_array($this->options) ? $this->options['style'] : '';
			if ( strlen( $style ) === 0 ) {
				$style = 'details-normal';
			}
			?>
				<select name="gts_options[style]">
					<option <?php echo ( $style === 'details-normal'	? 'selected' : '' ); ?> value="details-normal"	>Details Normal 		</option>
					<option <?php echo ( $style === 'details-black'		? 'selected' : '' ); ?> value="details-black"	>Details Black			</option>
					<option <?php echo ( $style === 'details-blue'		? 'selected' : '' ); ?> value="details-blue"	>Details Blue			</option>
					<option <?php echo ( $style === 'details-brown'		? 'selected' : '' ); ?> value="details-brown"	>Details Brown			</option>
					<option <?php echo ( $style === 'details-gray'		? 'selected' : '' ); ?> value="details-gray"	>Details Gray			</option>
					<option <?php echo ( $style === 'details-green'		? 'selected' : '' ); ?> value="details-green"	>Details Green			</option>
					<option <?php echo ( $style === 'details-ice'		? 'selected' : '' ); ?> value="details-ice" 	>Details Ice			</option>
					<option <?php echo ( $style === 'details-navy'		? 'selected' : '' ); ?> value="details-navy"	>Details Navy			</option>
					<option <?php echo ( $style === 'details-pink'		? 'selected' : '' ); ?> value="details-pink"	>Details Pink			</option>
					<option <?php echo ( $style === 'details-purple'	? 'selected' : '' ); ?> value="details-purple"	>Details Purple 		</option>
					<option <?php echo ( $style === 'details-red'		? 'selected' : '' ); ?> value="details-red" 	>Details Red			</option>
					<option <?php echo ( $style === 'details-white'		? 'selected' : '' ); ?> value="details-white"	>Details White			</option>
					<option <?php echo ( $style === 'details-yellow'	? 'selected' : '' ); ?> value="details-yellow"	>Details Yellow 		</option>
					<option <?php echo ( $style === 'details-gold'		? 'selected' : '' ); ?> value="details-gold"	>Details Designed Gold	</option>
					<option <?php echo ( $style === 'details-shock'		? 'selected' : '' ); ?> value="details-shock"	>Details Designed Shock	</option>
					<option <?php echo ( $style === 'details-vivid'		? 'selected' : '' ); ?> value="details-vivid"	>Details Designed Vivid	</option>
					<option <?php echo ( $style === 'details-facebook'	? 'selected' : '' ); ?> value="details-facebook">Details SNS Facebook	</option>
					<option <?php echo ( $style === 'details-twitter'	? 'selected' : '' ); ?> value="details-twitter" >Details SNS Twitter	</option>
					<option <?php echo ( $style === 'details-line'		? 'selected' : '' ); ?> value="details-line"	>Details SNS Line		</option>
					<option <?php echo ( $style === 'details-pocket'	? 'selected' : '' ); ?> value="details-pocket"	>Details SNS Pocket		</option>
					<option <?php echo ( $style === 'details-hatena'	? 'selected' : '' ); ?> value="details-hatena"	>Details SNS Hatena		</option>
					<option <?php echo ( $style === 'normal'			? 'selected' : '' ); ?> value="normal"			>Normal 				</option>
					<option <?php echo ( $style === 'black'				? 'selected' : '' ); ?> value="black"			>Black					</option>
					<option <?php echo ( $style === 'blue'				? 'selected' : '' ); ?> value="blue"			>Blue					</option>
					<option <?php echo ( $style === 'brown'				? 'selected' : '' ); ?> value="brown"			>Brown					</option>
					<option <?php echo ( $style === 'gray'				? 'selected' : '' ); ?> value="gray"			>Gray					</option>
					<option <?php echo ( $style === 'green'				? 'selected' : '' ); ?> value="green"			>Green					</option>
					<option <?php echo ( $style === 'ice'				? 'selected' : '' ); ?> value="ice" 			>Ice					</option>
					<option <?php echo ( $style === 'navy'				? 'selected' : '' ); ?> value="navy"			>Navy					</option>
					<option <?php echo ( $style === 'pink'				? 'selected' : '' ); ?> value="pink"			>Pink					</option>
					<option <?php echo ( $style === 'purple'			? 'selected' : '' ); ?> value="purple"			>Purple 				</option>
					<option <?php echo ( $style === 'red'				? 'selected' : '' ); ?> value="red" 			>Red					</option>
					<option <?php echo ( $style === 'white'				? 'selected' : '' ); ?> value="white"			>White					</option>
					<option <?php echo ( $style === 'yellow'			? 'selected' : '' ); ?> value="yellow"			>Yellow 				</option>
					<option <?php echo ( $style === 'gold'				? 'selected' : '' ); ?> value="gold"			>Designed Gold			</option>
					<option <?php echo ( $style === 'shock'				? 'selected' : '' ); ?> value="shock"			>Designed Shock			</option>
					<option <?php echo ( $style === 'vivid'				? 'selected' : '' ); ?> value="vivid"			>Designed Vivid			</option>
					<option <?php echo ( $style === 'facebook'			? 'selected' : '' ); ?> value="facebook"		>SNS Facebook			</option>
					<option <?php echo ( $style === 'twitter'			? 'selected' : '' ); ?> value="twitter" 		>SNS Twitter			</option>
					<option <?php echo ( $style === 'line'				? 'selected' : '' ); ?> value="line"			>SNS Line				</option>
					<option <?php echo ( $style === 'pocket'			? 'selected' : '' ); ?> value="pocket"			>SNS Pocket				</option>
					<option <?php echo ( $style === 'hatena'			? 'selected' : '' ); ?> value="hatena"			>SNS Hatena				</option>
				</select>
			<?php
		}
	}

	if ( is_admin() ) {
		new ganohrs_toggle_shortcode_Options();
	}

endif;
