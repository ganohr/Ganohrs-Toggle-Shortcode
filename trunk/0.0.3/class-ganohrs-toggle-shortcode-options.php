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
			return $new_input;
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
				<label for="enqueue_type_head"	 ><input id="enqueue_type_head"    type="radio" name="gts_options[enqueue_or_head]" <?php echo ($enqueue_type === 'head'	? "checked" : "")?> value="head"	/>Head</label>
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
