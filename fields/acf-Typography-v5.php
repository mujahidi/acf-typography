<?php
/**
 * ACF Typography Field Type - Version 5.
 *
 * @package ACF_Typography
 * @since 5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if class already exists.
if ( ! class_exists( 'acf_field_Typography' ) ) :

	/**
	 * ACF Typography field class for ACF version 5+.
	 *
	 * @since 5.0.0
	 */
	class acf_field_Typography extends acf_field {
		/**
		 * Font family options.
		 *
		 * @var array
		 */
		public $font_family;

		/**
		 * Font weight options.
		 *
		 * @var array
		 */
		public $font_weight;

		/**
		 * Font style options.
		 *
		 * @var array
		 */
		public $font_style;

		/**
		 * Font variant options.
		 *
		 * @var array
		 */
		public $font_variant;

		/**
		 * Font stretch options.
		 *
		 * @var array
		 */
		public $font_stretch;

		/**
		 * Text align options.
		 *
		 * @var array
		 */
		public $text_align;

		/**
		 * Text decoration options.
		 *
		 * @var array
		 */
		public $text_decoration;

		/**
		 * Text transform options.
		 *
		 * @var array
		 */
		public $text_transform;

		/**
		 * Constructor.
		 *
		 * This function will setup the field type data.
		 *
		 * @since 5.0.0
		 * @param array $settings Field settings.
		 */
		public function __construct( $settings ) {
		
		/*
		 * name (string) Single word, no spaces. Underscores allowed
		 */
		$this->name = 'Typography';
		
		/*
		 * label (string) Multiple words, can include spaces, visible when selecting a field type
		 */
		$this->label = __( 'Typography', 'acf-typography' );
		
		/*
		 * category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		 */
		$this->category = 'content';
		
		/*
		 * defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		 */
		$this->defaults = array(
			'display_properties'  => array(),
			'required_properties' => array(),
			'font_size'           => 15,
			'font_weight'         => '400',
			'font_family'         => 'Arial, Helvetica, sans-serif',
			'font_style'          => 'normal',
			'font_variant'        => 'normal',
			'font_stretch'        => 'normal',
			'text_align'          => 'left',
			'letter_spacing'      => 0,
			'text_decoration'     => 'none',
			'text_color'          => '#000',
			'text_transform'      => 'none',
		);
		
		$this->font_family = array(
			'Arial, Helvetica, sans-serif'                          => 'Arial, Helvetica, sans-serif',
			'"Arial Black", Gadget, sans-serif'                     => '"Arial Black", Gadget, sans-serif',
			'"Bookman Old Style", serif'                            => '"Bookman Old Style", serif',
			'"Comic Sans MS", cursive'                              => '"Comic Sans MS", cursive',
			'Courier, monospace'                                    => 'Courier, monospace',
			'Garamond, serif'                                       => 'Garamond, serif',
			'Georgia, serif'                                        => 'Georgia, serif',
			'Impact, Charcoal, sans-serif'                          => 'Impact, Charcoal, sans-serif',
			'"Lucida Console", Monaco, monospace'                   => '"Lucida Console", Monaco, monospace',
			'"Lucida Sans Unicode", "Lucida Grande", sans-serif'    => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
			'"MS Sans Serif", Geneva, sans-serif'                   => '"MS Sans Serif", Geneva, sans-serif',
			'"MS Serif", "New York", sans-serif'                    => '"MS Serif", "New York", sans-serif',
			'"Palatino Linotype", "Book Antiqua", Palatino, serif'  => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
			'Tahoma,Geneva, sans-serif'                             => 'Tahoma, Geneva, sans-serif',
			'"Times New Roman", Times,serif'                        => '"Times New Roman", Times, serif',
			'"Trebuchet MS", Helvetica, sans-serif'                 => '"Trebuchet MS", Helvetica, sans-serif',
			'Verdana, Geneva, sans-serif'                           => 'Verdana, Geneva, sans-serif',
		);
		
		// Get Google fonts from json file.
		$google_font_family = acft_get_google_font_family();

		// Merge web-safe-fonts and google fonts arrays.
		if ( is_array( $google_font_family ) ) {
			$this->font_family = array_merge( $this->font_family, $google_font_family );
		}

		// Sort array by array key.
		ksort( $this->font_family );

		// Add 'initial' and 'inherit' property values to top of the array.
		$this->font_family = array_merge( array( 'initial' => 'initial', 'inherit' => 'inherit' ), $this->font_family );
		
		$this->font_weight = array(
			'100' => '100',
			'200' => '200',
			'300' => '300',
			'400' => '400',
			'500' => '500',
			'600' => '600',
			'700' => '700',
			'800' => '800',
			'900' => '900',
		);
		$this->font_style = array(
			'normal'  => 'normal',
			'italic'  => 'italic',
			'oblique' => 'oblique',
		);
		$this->font_variant = array(
			'normal'     => 'normal',
			'small-caps' => 'small-caps',
			'initial'    => 'initial',
			'inherit'    => 'inherit',
		);
		$this->font_stretch = array(
			'ultra-condensed' => 'ultra-condensed',
			'extra-condensed' => 'extra-condensed',
			'condensed'       => 'condensed',
			'semi-condensed'  => 'semi-condensed',
			'normal'          => 'normal',
			'semi-expanded'   => 'semi-expanded',
			'expanded'        => 'expanded',
			'extra-expanded'  => 'extra-expanded',
			'ultra-expanded'  => 'ultra-expanded',
			'initial'         => 'initial',
			'inherit'         => 'inherit',
		);
		$this->text_align = array(
			'inherit' => 'inherit',
			'left'    => 'left',
			'right'   => 'right',
			'center'  => 'center',
			'justify' => 'justify',
			'initial' => 'initial',
		);
		$this->text_decoration = array(
			'none'         => 'none',
			'underline'    => 'underline',
			'overline'     => 'overline',
			'line-through' => 'line-through',
			'initial'      => 'initial',
			'inherit'      => 'inherit',
		);
		$this->text_transform = array(
			'none'       => 'none',
			'capitalize' => 'capitalize',
			'uppercase'  => 'uppercase',
			'lowercase'  => 'lowercase',
			'initial'    => 'initial',
			'inherit'    => 'inherit',
		);
		
		/*
		 * l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		 * var message = acf._e('FIELD_NAME', 'error');
		 */
		$this->l10n = array(
			'error' => __( 'Error! Please enter a higher value', 'acf-typography' ),
		);
		
		/*
		 * settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		 */
		$this->settings = $settings;
		
		// Do not delete!
		parent::__construct();
	}
	
	/**
	 * Render field settings.
	 *
	 * Create extra settings for your field. These are visible when editing a field.
	 *
	 * @since 3.6
	 * @param array $field The field being edited.
	 */
	public function render_field_settings( $field ) {
		
		/*
		 * acf_render_field_setting
		 *
		 * This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		 * The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		 *
		 * More than one setting can be added by copy/paste the above code.
		 * Please note that you must also have a matching $defaults value for the field name (font_size)
		 */
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Display Properties', 'acf-typography' ),
				'instructions' => __( 'Select fields to display on edit page', 'acf-typography' ),
				'type'         => 'checkbox',
				'name'         => 'display_properties',
				'choices'      => array(
					'font_size'       => __( 'Font Size', 'acf-typography' ),
					'font_family'     => __( 'Font Family', 'acf-typography' ),
					'font_weight'     => __( 'Font Weight', 'acf-typography' ),
					'font_style'      => __( 'Font Style', 'acf-typography' ),
					'font_variant'    => __( 'Font Variant', 'acf-typography' ),
					'font_stretch'    => __( 'Font Stretch', 'acf-typography' ),
					'line_height'     => __( 'Line Height', 'acf-typography' ),
					'letter_spacing'  => __( 'Letter Spacing', 'acf-typography' ),
					'text_align'      => __( 'Text Align', 'acf-typography' ),
					'text_color'      => __( 'Text Color', 'acf-typography' ),
					'text_decoration' => __( 'Text Decoration', 'acf-typography' ),
					'text_transform'  => __( 'Text Transform', 'acf-typography' ),
				),
				'layout'       => 'horizontal',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Required Properties', 'acf-typography' ),
				'instructions' => __( 'Select fields which are required on edit page', 'acf-typography' ),
				'type'         => 'checkbox',
				'name'         => 'required_properties',
				'choices'      => array(
					'font_size'       => __( 'Font Size', 'acf-typography' ),
					'font_family'     => __( 'Font Family', 'acf-typography' ),
					'font_weight'     => __( 'Font Weight', 'acf-typography' ),
					'font_style'      => __( 'Font Style', 'acf-typography' ),
					'font_variant'    => __( 'Font Variant', 'acf-typography' ),
					'font_stretch'    => __( 'Font Stretch', 'acf-typography' ),
					'line_height'     => __( 'Line Height', 'acf-typography' ),
					'letter_spacing'  => __( 'Letter Spacing', 'acf-typography' ),
					'text_align'      => __( 'Text Align', 'acf-typography' ),
					'text_color'      => __( 'Text Color', 'acf-typography' ),
					'text_decoration' => __( 'Text Decoration', 'acf-typography' ),
					'text_transform'  => __( 'Text Transform', 'acf-typography' ),
				),
				'layout'       => 'horizontal',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Font Size', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'number',
				'name'         => 'font_size',
				'append'       => 'px',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Font Family', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'select',
				'name'         => 'font_family',
				'choices'      => $this->font_family,
				'layout'       => 'horizontal',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Font Weight', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'select',
				'name'         => 'font_weight',
				'choices'      => $this->font_weight,
				'layout'       => 'horizontal',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Font Style', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'select',
				'name'         => 'font_style',
				'choices'      => $this->font_style,
				'layout'       => 'horizontal',
			)
		);

		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Font Variant', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'select',
				'name'         => 'font_variant',
				'choices'      => $this->font_variant,
				'layout'       => 'horizontal',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Font Stretch', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'select',
				'name'         => 'font_stretch',
				'choices'      => $this->font_stretch,
				'layout'       => 'horizontal',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Line Height', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'number',
				'name'         => 'line_height',
				'append'       => 'px',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Letter Spacing', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'number',
				'name'         => 'letter_spacing',
				'append'       => 'px',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Text Align', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'select',
				'name'         => 'text_align',
				'choices'      => $this->text_align,
				'layout'       => 'horizontal',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Text Color', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'text',
				'name'         => 'text_color',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Text Decoration', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'select',
				'name'         => 'text_decoration',
				'choices'      => $this->text_decoration,
				'layout'       => 'horizontal',
			)
		);
		
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Text Transform', 'acf-typography' ),
				'instructions' => __( 'Default', 'acf-typography' ),
				'type'         => 'select',
				'name'         => 'text_transform',
				'choices'      => $this->text_transform,
				'layout'       => 'horizontal',
			)
		);

	}
	
	
	/**
	 * Render field HTML.
	 *
	 * Create the HTML interface for your field.
	 *
	 * @since 3.6
	 * @param array $field The field being rendered.
	 */
	public function render_field( $field ) {
		$field = array_merge( $this->defaults, $field );

		$key = $field['key'];

		// Create Field HTML.
		if ( ! empty( $field['display_properties'] ) && count( $field['display_properties'] ) > 0 ) {
			foreach ( $field['display_properties'] as $f ) {
				$numbers       = array();
				$selects       = array();
				$data_required = '';
				$required      = '';

				if ( is_array( $field['required_properties'] ) && in_array( $f, $field['required_properties'], true ) ) {
					$required      = 'required';
					$data_required = 'data-required="1"';
				}

				if ( 'font_size' === $f || 'line_height' === $f || 'letter_spacing' === $f ) {
					$numbers[] = $f;
				} elseif ( in_array( $f, array( 'font_family', 'font_weight', 'font_style', 'font_variant', 'font_stretch', 'text_align', 'text_decoration', 'text_transform' ), true ) ) {
					$selects[] = $f;
				}

				if ( in_array( $f, $numbers, true ) ) {
					?>
					<div class="acf-field acf-field-number acf-field-<?php echo esc_attr( $f ); ?>" data-name="<?php echo esc_attr( $f ); ?>" data-type="number" data-key="<?php echo esc_attr( $key ); ?>" <?php echo wp_kses_post( $data_required ); ?>>
						<div class="acf-label">
							<label for="acf-field-<?php echo esc_attr( $f ); ?>">
								<?php echo esc_html( ucwords( str_replace( '_', ' ', $f ) ) ); ?>
								<?php if ( ! empty( $required ) ) { ?>
									<span class="acf-required">*</span>
								<?php } ?>
							</label>
						</div>
						<div class="acf-input">
							<div class="acf-input-append">px</div>
							<div class="acf-input-wrap">
								<input type="number" id="<?php echo esc_attr( 'acf-field-' . $f ); ?>" class="acf-is-appended" min="" max="" step="any" name="<?php echo esc_attr( $field['name'] . '[' . $f . ']' ); ?>" value="<?php echo esc_attr( ! empty( $field['value'][ $f ] ) ? $field['value'][ $f ] : $field[ $f ] ); ?>">
							</div>
						</div>
					</div>
					<?php
				} elseif ( in_array( $f, $selects, true ) ) {
					?>
					<div class="acf-field acf-field-select acf-field-<?php echo esc_attr( $f ); ?>" data-name="<?php echo esc_attr( $f ); ?>" data-type="select" data-key="<?php echo esc_attr( $key ); ?>" <?php echo wp_kses_post( $data_required ); ?>>
						<div class="acf-label">
							<label for="acf-field-<?php echo esc_attr( $f ); ?>">
								<?php echo esc_html( ucwords( str_replace( '_', ' ', $f ) ) ); ?>
								<?php if ( ! empty( $required ) ) { ?>
									<span class="acf-required">*</span>
								<?php } ?>
							</label>
						</div>
						<div class="acf-input">
							<select id="acf-<?php echo esc_attr( $f ); ?>" class="" name="<?php echo esc_attr( $field['name'] . '[' . $f . ']' ); ?>" data-ui="0" data-ajax="0" data-multiple="0" data-placeholder="Select" data-allow_null="0">
								<?php
								$current_value = ! empty( $field['value'][ $f ] ) ? $field['value'][ $f ] : $field[ $f ];
								if ( isset( $this->$f ) && is_array( $this->$f ) ) {
									foreach ( $this->$f as $opt ) {
										$selected = selected( $current_value, $opt, false );
										echo '<option value="' . esc_attr( $opt ) . '" ' . $selected . '>' . esc_html( $opt ) . '</option>';
									}
								}
								?>
							</select>
						</div>
					</div>
					<?php
				} else {
					?>
					<div class="acf-field acf-field-color-picker acf-field-<?php echo esc_attr( $f ); ?>" data-name="<?php echo esc_attr( $f ); ?>" data-type="color_picker" data-key="<?php echo esc_attr( $key ); ?>" <?php echo wp_kses_post( $data_required ); ?>>
						<div class="acf-label">
							<label for="acf-field-<?php echo esc_attr( $f ); ?>">
								<?php echo esc_html( ucwords( str_replace( '_', ' ', $f ) ) ); ?>
								<?php if ( ! empty( $required ) ) { ?>
									<span class="acf-required">*</span>
								<?php } ?>
							</label>
						</div>
						<div class="acf-input">
							<div class="acf-color_picker">
								<input type="hidden" name="<?php echo esc_attr( $field['name'] . '[' . $f . ']' ); ?>" value="<?php echo esc_attr( ! empty( $field['value'][ $f ] ) ? $field['value'][ $f ] : $field[ $f ] ); ?>">
								<input type="text" id="<?php echo esc_attr( 'acf-field-' . $f ); ?>" name="<?php echo esc_attr( $field['name'] . '[' . $f . ']' ); ?>" value="<?php echo esc_attr( ! empty( $field['value'][ $f ] ) ? $field['value'][ $f ] : $field[ $f ] ); ?>">
							</div>
						</div>
					</div>
					<?php
				}
			}
		}
	}
	
		
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function input_admin_enqueue_scripts() {
		
		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];
		
		
		// register & include JS
		wp_register_script( 'acf-input-FIELD_NAME', "{$url}assets/js/input.js", array('acf-input'), $version );
		wp_enqueue_script('acf-input-FIELD_NAME');
		
		
		// register & include CSS
		wp_register_style( 'acf-input-FIELD_NAME', "{$url}assets/css/input.css", array('acf-input'), $version );
		wp_enqueue_style('acf-input-FIELD_NAME');
		
	}
	
	*/
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
		
	function input_admin_head() {
	
		
		
	}
	
	*/
	
	
	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and 
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/
   	
   	/*
   	
   	function input_form_data( $args ) {
	   	
		
	
   	}
   	
   	*/
	
	
	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
		
	function input_admin_footer() {
	
		
		
	}
	
	*/
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_enqueue_scripts() {
		
	}
	
	*/

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_head() {
	
	}
	
	*/

	/**
	 * Load value from database.
	 *
	 * This filter is applied to the $value after it is loaded from the db.
	 *
	 * @since 3.6
	 * @param mixed $value The value found in the database.
	 * @param mixed $post_id The post ID from which the value was loaded.
	 * @param array $field The field array holding all the field options.
	 * @return mixed The value.
	 */
	public function load_value( $value, $post_id, $field ) {
		return $value;
	}
	
	/**
	 * Update value before saving to database.
	 *
	 * This filter is applied to the $value before it is saved in the db.
	 *
	 * @since 3.6
	 * @param mixed $value The value to be saved in the database.
	 * @param mixed $post_id The post ID to which the value will be saved.
	 * @param array $field The field array holding all the field options.
	 * @return mixed The modified value.
	 */
	public function update_value( $value, $post_id, $field ) {
		return $value;
	}
	
	/**
	 * Format value for display.
	 *
	 * This filter is applied to the $value after it is loaded from the db and before it is returned to the template.
	 *
	 * @since 3.6
	 * @param mixed $value The value which was loaded from the database.
	 * @param mixed $post_id The post ID from which the value was loaded.
	 * @param array $field The field array holding all the field options.
	 * @return mixed The modified value.
	 */
	public function format_value( $value, $post_id, $field ) {
		// Bail early if no value.
		if ( empty( $value ) ) {
			return $value;
		}
		
		// Return.
		return $value;
	}
	
	/**
	 * Validate field value.
	 *
	 * This filter is used to perform validation on the value prior to saving.
	 * All values are validated regardless of the field's required setting. This allows you to validate and return
	 * messages to the user if the value is not correct.
	 *
	 * @since 5.0.0
	 * @param bool   $valid Validation status based on the value and the field's required setting.
	 * @param mixed  $value The POST value.
	 * @param array  $field The field array holding all the field options.
	 * @param string $input The corresponding input name for POST value.
	 * @return bool Validation status.
	 */
	public function validate_value( $valid, $value, $field, $input ) {
		if ( ! empty( $field['required_properties'] ) ) {
			foreach ( $field['required_properties'] as $rf ) {
				if ( in_array( $rf, $field['display_properties'], true ) && empty( $value[ $rf ] ) ) {
					acf_validate_value( $value[ $rf ], ' ', $field['prefix'] . '[' . $field['key'] . '][' . $rf . ']' );
				}
			}
		}
		
		// Return.
		return $valid;
	}
	
	
	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/
	
	/*
	
	function delete_value( $post_id, $key ) {
		
		
		
	}
	
	*/
	
	/**
	 * Load field settings.
	 *
	 * This filter is applied to the $field after it is loaded from the database.
	 *
	 * @since 3.6
	 * @param array $field The field array holding all the field options.
	 * @return array The field array.
	 */
	public function load_field( $field ) {
		return $field;
	}
	
	/**
	 * Update field settings.
	 *
	 * This filter is applied to the $field before it is saved to the database.
	 *
	 * @since 3.6
	 * @param array $field The field array holding all the field options.
	 * @return array The modified field.
	 */
	public function update_field( $field ) {
		return $field;
	}
}

// Initialize.
new acf_field_Typography( $this->settings );

endif;
