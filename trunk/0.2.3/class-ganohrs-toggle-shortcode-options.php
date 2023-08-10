<?php
/**
 * Ganohrs Toggle Shortcode
 *
 * PHP Version >= 5.0 (Tested 8.2.4 & 8.1.6 & 8.0.7 & 7.4.28)
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
	 * Ganohrs Toggle Shortcode Options
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
			array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . __( 'Settings' ) . '</a>' );
			return $links;
		}

		/**
		 * 設定ページへプラグインを追加する
		 *
		 * @return	   void
		 */
		public function add_plugin_page() {
			$load_hook = add_options_page(
				"Ganohrs Toggle Shortcode",
				"Ganohrs Toggle Shortcode",
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
			$toggle_style  = @$this->options['style'];
			$minified	   = '';
			if ( is_string( $toggle_style )
				&& $toggle_style !== ''
				&& strpos( $toggle_style, '.' ) === false
			) {
				$src = __DIR__ . '/ganohrs-toggle-shortcode-' . $toggle_style . '.css';
				if ( file_exists( $src ) ) {
					$minified	= file_get_contents( $src );
					$before_css = '';
					while ( $before_css !== $minified ) {
						$before_css = $minified;
						$minified	= preg_replace( '#/\*.*?\*/|\r\n|\r|\n#s', '' , $minified );
						$minified	= preg_replace( '#[\t]+#s'		, ' ', $minified );
						$minified	= preg_replace( '# {2,}#s'		, ' ', $minified );
						$minified	= preg_replace( '#;\}#s'		, '}', $minified );
						$minified	= preg_replace( '# :|: #s'		, ':', $minified );
						$minified	= preg_replace( '# ,|, #s'		, ',', $minified );
						$minified	= preg_replace( '# ;|; #s'		, ';', $minified );
						$minified	= preg_replace( '# \{|\{ #s'	, '{', $minified );
						$minified	= preg_replace( '# \}|\} #s'	, '}', $minified );
					}
					$minified = "<textarea readonly id='minified-css'>$minified</textarea>";
				}
			}
			if ( $minified === '' ) {
				$minified = 'CSS is Nothing';
			}

			?>
			<div class="wrap">
				<h1>Ganohrs Toggle Shortcode</h1>

				<form method="post" action="options.php">
					<?php
						settings_fields( 'gts_options_group' );
						do_settings_sections( 'gts_options' );

						submit_button();
					?>
				</form>
			</div>
			<style>
			#wpbody-content {
				font-size: 110%;
			}
			h2 {
				background: white;
				line-height: 2;
				font-weight: bold;
				font-size: 1.4rem;
				border-left: 0.3rem solid black;
				padding: 0.3rem;
				margin: 0.3rem 0;
			}
			code {
				background: none;
			}
			textarea {
				width: 100%;
				height: 30vh;
			}
			dl {
				padding-left: 2em;
			}
			dt {
				font-size: 120%;
			}
			dt:first-letter {
				font-size: 140%;
				border-bottom: 2px solid black;
			}
			strong {
				font-weight: 600;
				border-bottom: 1px dotted black;
			}
			</style>
			<h2>Basic Usage / 基本的な使い方</h2>
			<p><pre>[toggle title="title here" load="(option)close"]contents here[/toggle]</pre></p>
			<h3>Examples / 例</h3>
			<h4>Examples 1: Toggle opend when first access / 折りたたみ領域が最初から開いている例</h3>
			<p><pre><code>&#091;toggle&nbsp;title=&quot;Eight&nbsp;Choices&nbsp;of&nbsp;Tongue&nbsp;Twisters!&quot;&#093;
&lt;ol&gt;
&lt;li&gt;Red&nbsp;lorry,&nbsp;yellow&nbsp;lorry&#46;
&lt;li&gt;How&nbsp;much&nbsp;wood&nbsp;would&nbsp;a&nbsp;woodchuck&nbsp;chuck,&nbsp;if&nbsp;a&nbsp;woodchuck&nbsp;could&nbsp;chuck&nbsp;wood?&nbsp;He&nbsp;would&nbsp;chuck&nbsp;as&nbsp;a&nbsp;wood&nbsp;chuck&nbsp;would&nbsp;if&nbsp;a&nbsp;woodchuck&nbsp;could&nbsp;chuck&nbsp;wood&#46;
&lt;li&gt;A&nbsp;big&nbsp;black&nbsp;bug&nbsp;bit&nbsp;a&nbsp;big&nbsp;black&nbsp;bear&nbsp;and&nbsp;made&nbsp;the&nbsp;big&nbsp;black&nbsp;bear&nbsp;bleed&nbsp;blood&#46;
&lt;li&gt;How&nbsp;many&nbsp;cans&nbsp;can&nbsp;a&nbsp;canner&nbsp;can,&nbsp;if&nbsp;a&nbsp;canner&nbsp;can&nbsp;can&nbsp;cans?&nbsp;A&nbsp;canner&nbsp;can&nbsp;can&nbsp;many&nbsp;cans&nbsp;as&nbsp;a&nbsp;canner&nbsp;can,&nbsp;if&nbsp;a&nbsp;canner&nbsp;can&nbsp;can&nbsp;cans&#46;
&lt;li&gt;She&nbsp;sells&nbsp;seashells&nbsp;on&nbsp;the&nbsp;seashore&#46;
&lt;li&gt;Gloomy&nbsp;grooms&nbsp;roamed&nbsp;around&nbsp;Rome&#46;
&lt;li&gt;Lily&nbsp;really&nbsp;relied&nbsp;on&nbsp;Larry&apos;s&nbsp;reply&#46;
&lt;li&gt;He&nbsp;threw&nbsp;three&nbsp;free&nbsp;throws&#46;
&lt;/ol&gt;
&#091;/toggle&#093;</code></pre></p>
			<?php
			echo do_shortcode(
				<<<EOF
[toggle title="Eight Choices of Tongue Twisters!"]
<ol>
<li>Red lorry, yellow lorry.
<li>How much wood would a woodchuck chuck, if a woodchuck could chuck wood? He would chuck as a wood chuck would if a woodchuck could chuck wood.
<li>A big black bug bit a big black bear and made the big black bear bleed blood.
<li>How many cans can a canner can, if a canner can can cans? A canner can can many cans as a canner can, if a canner can can cans.
<li>She sells seashells on the seashore.
<li>Gloomy grooms roamed around Rome.
<li>Lily really relied on Larry's reply.
<li>He threw three free throws.
</ol>
[/toggle]
EOF
			);
			?>
			<h4>Examples 2: Toggle closed when first access / 折りたたみ領域が最初は閉じている例</h3>
			<p><pre><code>&#091;toggle&nbsp;title=&quot;パンはパンでも食べられないパンは?&quot;&nbsp;load=&quot;close&quot;&#093;
フライパン
&#091;/toggle&#093;</code></pre></p>
			<?php
			echo do_shortcode(
				<<<EOF
[toggle title="パンはパンでも食べられないパンは?" load="close"]
フライパン
[/toggle]
EOF
			);
			?>
			<h4>Examples 3: Multi style suport when you chose "Details Default" / スタイルの"Details Default"を選んでいる場合複数のスタイルがサポートされる</h3>
			<p><pre><code>&#091;toggle&nbsp;title=&quot;normal style / 通常のスタイル&quot;&#093;
Normal Style / 通常のスタイル
&#091;/toggle&#093;
&#091;toggle&nbsp;title=&quot;red style / 赤いスタイル&quot;&nbsp;suffix=&quot;red&quot;&#093;
Red Style / 赤いスタイル
&#091;/toggle&#093;
&#091;toggle&nbsp;title=&quot;white style / 白いスタイル&quot;&nbsp;suffix=&quot;white&quot;&#093;
White Style / 白いスタイル
&#091;/toggle&#093;</code></pre></p>
			<?php
			echo do_shortcode(
				<<<EOF
[toggle title="normal style / 通常のスタイル"]
Normal Style / 通常のスタイル
[/toggle]
[toggle title="red style / 赤いスタイル" suffix="red"]
Red Style / 赤いスタイル
[/toggle]
[toggle title="white style / 白いスタイル" suffix="white"]
White Style / 白いスタイル
[/toggle]
EOF
			);
			?>
			<p>&nbsp;</p>
			<h2>What's Output CSS When AMP? / Output CSS When AMPってなに?</h2>
			<p>Please specify if the theme or plugin you are using for AMP conversion can automatically process <code>style</code> and <code>link</code> tags. If you get an AMP error(or AMP Style isn't activate), copy the CSS from the area below and add the corresponding AMP CSS to the given area.</p>
			<p>あなたがAMP化のために使用しているテーマやプラグインが、<code>style</code>タグや<code>link</code>タグを自動で処理できる場合に指定してください。AMPエラーが出る場合やAMPでスタイルが有効化されない場合、以下のエリアからCSSをコピーし、対応するAMP用CSSを所定の領域へ追加してください。</p>
			<h3>Minified CSS</h3>
			<p><?php echo $minified; ?></p>
			<h3>Examples for setting for using AMP / AMPを利用する際の設定例</h3>
			<dl>
				<dt>When use AMP Supported <strong>Theme</strong>(Like a Cocoon, THE Thor, Lionmedia, etc...)</dt>
				<dd>It need <em>Output CSS When AMP</em> to set "<strong>No</strong>", And copy upper <em>Minified CSS</em>, And Append this CSS to "AMP CSS Area".</dd>
				<dt>When use AMP Supported <strong>Plugins</strong>(Like a AMP, AMP for WP, etc...)</dt>
				<dd>It need <em>Output CSS When AMP</em> to set "<strong>Yes</strong>", And set <em>Enqueue Type</em> to <strong>Head</strong>.</dd>
			</dl>
			<dl>
				<dt>もしもAMPをサポートしている<strong>テーマ</strong>(Cocoon, THE Thor, Lionmedia, etc...)を使用する場合</dt>
				<dd><em>Output CSS When AMP</em>を"<strong>No</strong>"に設定し、<em>Minified CSS</em>をコピーし、このCSSを"AMP用のCSS領域"へ貼り付けて下さい。</dd>
				<dt>もしもAMPをサポートしている<strong>プラグイン</strong>(Like a AMP, AMP for WP, etc...)を使用する場合</dt>
				<dd><em>Output CSS When AMP</em>を"<strong>Yes</strong>"に設定し、<em>Enqueue Type</em>は<strong>Head</strong>にしてください。</dd>
			</dl>
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
			<h2>How to down size CSS? / CSSを小さくするにはどうすればいいですか?</h2>
			<p>First, select the "details" style. Compared to styles that aren't "details", that alone cuts the size of the CSS in half.</p>
			<p>If you want to downsize it further, set "Output CSS When AMP" and "Output CSS When None AMP" to "No" and copy the CSS from "Minified CSS". After that, remove the unused "suffix" definition and add the CSS to the style.css of the child theme or the CSS area for AMP.</p>
			<p>&nbsp;</p>
			<p>まず、「details」スタイルを選択してください。「details」じゃないスタイルと比べて、それだけでCSSのサイズが約半分になります。</p>
			<p>それ以上のダウンサイズを実現したいのであれば「Output CSS When AMP」及び「Output CSS When None AMP」を"No"にし、「Minified CSS」からCSSをコピーしてください。その後、使用しない"suffix"の定義を除去したCSSを、小テーマのstyle.cssやAMP用のCSS領域に追加してください。</p>
			<p>&nbsp;</p>
			<h2>What's Replace Suffix? / Replace Suffixって何?</h2>
			<p>"Replace Number Suffix" removes the numerically specified suffix and replaces it with a style without a suffix specified. "Replace None Number Suffix" removes non-numeric suffixes and replaces them with styles without suffixes.</p>
			<p>Mainly, "Replace Number Suffix" should be turned ON when using the "Details Default" style if you were using the old style. Conversely, "Replace None Number Suffix" should be turned ON when using a style other than "Details Default" style. By using this function, you can easily use various styles.</p>
			<p>&nbsp;</p>
			<p>「Replace Number Suffix」は、数値で指定されたSuffixを除去してSuffix指定のないスタイルに読み替えます。「Replace None Number Suffix」は数値以外で指定されたSuffixを除去して、Suffix指定のないスタイルに読み替えます。</p>
			<p>主に「Replace Number Suffix」は古いスタイルを利用していた方が、「Details Default」スタイルを利用する際にONにします。逆に「Replace None Number Suffix」は、「Details Default」スタイルを利用していた方が、それ以外のスタイルを利用する際にONにします。この機能を利用することで、様々なスタイルを簡単に利用できるようになります。</p>
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
			<p>&nbsp;</p>
			<p>In some cases, it's a good idea to no output CSS and make adjustments from there.</p>
			<p>場合によっては、CSSを出力しないようにして、そこから調整をすると良いでしょう。</p>
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
			add_settings_field(
				'output_css_when_amp',
				'Output CSS When AMP',
				array( $this, 'output_css_when_amp_callback' ),
				'gts_options',
				'gts_setting_section'
			);
			add_settings_field(
				'output_css_when_none_amp',
				'Output CSS When None AMP',
				array( $this, 'output_css_when_none_amp_callback' ),
				'gts_options',
				'gts_setting_section'
			);
			add_settings_field(
				'replace_number_suffix',
				'Replace Number Suffix',
				array( $this, 'replace_number_suffix_callback' ),
				'gts_options',
				'gts_setting_section'
			);
			add_settings_field(
				'replace_none_number_suffix',
				'Replace None Number Suffix',
				array( $this, 'replace_none_number_suffix_callback' ),
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

			$new_input['style'] 						= isset( $input['style'] )						? @$input['style']						: 'details-default';
			$new_input['enqueue_or_head']				= isset( $input['enqueue_or_head'] )			? @$input['enqueue_or_head']			: 'enqueue';
			$new_input['fix_autoformat']				= isset( $input['fix_autoformat'] )				? @$input['fix_autoformat'] 			: 'fix';
			$new_input['output_css_when_amp']			= isset( $input['output_css_when_amp'] )		? @$input['output_css_when_amp']		: 'no';
			$new_input['output_css_when_none_amp']		= isset( $input['output_css_when_none_amp'] )	? @$input['output_css_when_none_amp']	: 'yes';
			$new_input['replace_number_suffix']			= isset( $input['replace_number_suffix'] )		? @$input['replace_number_suffix']		: 'yes';
			$new_input['replace_none_number_suffix']	= isset( $input['replace_none_number_suffix'] )	? @$input['replace_none_number_suffix']	: 'no';
			return $new_input;
		}

		/**
		 * 数値以外のSuffixを除去するかどうかのコールバック
		 *
		 * @return	   void
		 */
		public function replace_none_number_suffix_callback() {
			$replace_none_number_suffix = is_array( $this->options ) ? @$this->options['replace_none_number_suffix'] : '';
			if ( ! is_string( $replace_none_number_suffix ) || strlen( $replace_none_number_suffix ) === 0 ) {
				$replace_none_number_suffix = 'no';
			}
			?>
				<label for="replace_none_number_suffix_yes" ><input id="replace_none_number_suffix_yes" type="radio" name="gts_options[replace_none_number_suffix]" <?php echo ( $replace_none_number_suffix === 'yes' ? 'checked' : '' ); ?> value="yes" />Yes</label>
				<label for="replace_none_number_suffix_no"  ><input id="replace_none_number_suffix_no"  type="radio" name="gts_options[replace_none_number_suffix]" <?php echo ( $replace_none_number_suffix !== 'yes' ? 'checked' : '' ); ?> value="no"  />No </label>
			<?php
		}

		/**
		 * 数値のSuffixを除去するかどうかのコールバック
		 *
		 * @return	   void
		 */
		public function replace_number_suffix_callback() {
			$replace_number_suffix = is_array( $this->options ) ? @$this->options['replace_number_suffix'] : '';
			if ( ! is_string( $replace_number_suffix ) || strlen( $replace_number_suffix ) === 0 ) {
				$replace_number_suffix = 'yes';
			}
			?>
				<label for="replace_number_suffix_yes" ><input id="replace_number_suffix_yes" type="radio" name="gts_options[replace_number_suffix]" <?php echo ( $replace_number_suffix === 'yes' ? 'checked' : '' ); ?> value="yes" />Yes</label>
				<label for="replace_number_suffix_no"  ><input id="replace_number_suffix_no"  type="radio" name="gts_options[replace_number_suffix]" <?php echo ( $replace_number_suffix !== 'yes' ? 'checked' : '' ); ?> value="no"  />No </label>
			<?php
		}

		/**
		 * 非AMPモード時にCSSを出力するか否か変更用のコールバック
		 *
		 * @return	   void
		 */
		public function output_css_when_none_amp_callback() {
			$output_css_when_none_amp = is_array( $this->options ) ? @$this->options['output_css_when_none_amp'] : '';
			if ( ! is_string( $output_css_when_none_amp ) || strlen( $output_css_when_none_amp ) === 0 ) {
				$output_css_when_none_amp = 'yes';
			}
			?>
				<label for="output_css_when_none_amp_yes" ><input id="output_css_when_none_amp_yes" type="radio" name="gts_options[output_css_when_none_amp]" <?php echo ( $output_css_when_none_amp === 'yes' ? 'checked' : '' ); ?> value="yes" />Yes</label>
				<label for="output_css_when_none_amp_no"  ><input id="output_css_when_none_amp_no"  type="radio" name="gts_options[output_css_when_none_amp]" <?php echo ( $output_css_when_none_amp !== 'yes' ? 'checked' : '' ); ?> value="no"  />No </label>
			<?php
		}

		/**
		 * AMPモード時にCSSを出力するか否か変更用のコールバック
		 *
		 * @return	   void
		 */
		public function output_css_when_amp_callback() {
			$output_css_when_amp = is_array( $this->options ) ? @$this->options['output_css_when_amp'] : '';
			if ( ! is_string( $output_css_when_amp ) || strlen( $output_css_when_amp ) === 0 ) {
				$output_css_when_amp = 'no';
			}
			?>
				<label for="output_css_when_amp_yes" ><input id="output_css_when_amp_yes" type="radio" name="gts_options[output_css_when_amp]" <?php echo ( $output_css_when_amp === 'yes' ? 'checked' : '' ); ?> value="yes" />Yes</label>
				<label for="output_css_when_amp_no"  ><input id="output_css_when_amp_no"  type="radio" name="gts_options[output_css_when_amp]" <?php echo ( $output_css_when_amp !== 'yes' ? 'checked' : '' ); ?> value="no"  />No </label>
			<?php
		}

		/**
		 * Fix Autoformat変更用コールバック
		 *
		 * @return	   void
		 */
		public function fix_autoformat_callback() {
			$fix_autoformat = is_array( $this->options ) ? @$this->options['fix_autoformat'] : '';
			if ( ! is_string( $fix_autoformat ) || strlen( $fix_autoformat ) === 0 ) {
				$fix_autoformat = 'fix';
			}
			?>
				<label for="fix_autoformat_fix" ><input id="fix_autoformat_fix"  type="radio" name="gts_options[fix_autoformat]" <?php echo ( $fix_autoformat === 'fix' ? 'checked' : '' ); ?> value="fix"	   />Fix	</label>
				<label for="fix_autoformat_none"><input id="fix_autoformat_none" type="radio" name="gts_options[fix_autoformat]" <?php echo ( $fix_autoformat !== 'fix' ? 'checked' : '' ); ?> value="nothing" />Nothing</label>
			<?php
		}

		/**
		 * Enqueue Type変更用コールバック
		 *
		 * @return	   void
		 */
		public function enqueue_type_callback() {
			$enqueue_type = is_array( $this->options ) ? @$this->options['enqueue_or_head'] : '';
			if ( ! is_string( $enqueue_type ) || strlen( $enqueue_type ) === 0 ) {
				$enqueue_type = 'enqueue';
			}
			?>
				<label for="enqueue_type_enqueue"><input id="enqueue_type_enqueue" type="radio" name="gts_options[enqueue_or_head]" <?php echo ( $enqueue_type === 'enqueue' ? 'checked' : '' ); ?> value="enqueue" />Enqueue</label>
				<label for="enqueue_type_head"	 ><input id="enqueue_type_head"    type="radio" name="gts_options[enqueue_or_head]" <?php echo ( $enqueue_type === 'head'	 ? 'checked' : '' ); ?> value="head"	/>Head	 </label>
			<?php
		}

		/**
		 * スタイル変更用コールバック
		 *
		 * @return	   void
		 */
		public function style_callback() {

			$style = is_array( $this->options ) ? @$this->options['style'] : '';
			if ( ! is_string( $style ) || strlen( $style ) === 0 ) {
				$style = 'details-default';
			}
			?>
				<select name="gts_options[style]">
					<option <?php echo ( $style === 'details-default'	? 'selected' : '' ); ?> value="details-default"	>Details Default 		</option>
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
