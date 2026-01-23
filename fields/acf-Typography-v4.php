<?php
/**
 * ACF Typography Field Type - Version 4.
 *
 * @package ACF_Typography
 * @since 3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if class already exists.
if ( ! class_exists( 'acf_field_Typography' ) ) :

	/**
	 * ACF Typography field class for ACF version 4.
	 *
	 * @since 3.6.0
	 */
	class acf_field_Typography extends acf_field {

		/**
		 * Plugin settings (url, path, version).
		 *
		 * @var array
		 */
		public $settings;

		/**
		 * Default field options.
		 *
		 * @var array
		 */
		public $defaults;

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
		 * Set name / label needed for actions / filters.
		 *
		 * @since 3.6
		 * @param array $settings Plugin settings.
		 */
		public function __construct( $settings ) {
		$this->name     = 'Typography';
		$this->label    = __( 'Typography', 'acf-typography' );
		$this->category = __( 'Content', 'acf-typography' );
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

		// Store plugin settings.
		$this->settings = $settings;

		// Do not delete!
		parent::__construct();
	}
	
	
	/**
	 * Create field options.
	 *
	 * This function will create extra options for your field.
	 * These are visible when editing a field.
	 *
	 * @since 3.6
	 * @param array $field The field being edited.
	 */
	public function create_options( $field ) {
		// Merge with defaults.
		$field = array_merge( $this->defaults, $field );

		// Key is needed in the field names to correctly save the data.
		$key = $field['name'];

		// Create Field Options HTML.
		?>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Display Properties', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( 'Select fields to display on edit page', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'checkbox',
						'name'    => 'fields[' . $key . '][display_properties]',
						'value'   => $field['display_properties'],
						'choices' => array(
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
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Required Properties', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( 'Select fields which are required on edit page', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'checkbox',
						'name'    => 'fields[' . $key . '][required_properties]',
						'value'   => $field['required_properties'],
						'choices' => array(
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
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Font Size', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'   => 'number',
						'name'   => 'fields[' . $key . '][font_size]',
						'value'  => $field['font_size'],
						'append' => 'px',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Font Family', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'select',
						'name'    => 'fields[' . $key . '][font_family]',
						'value'   => $field['font_family'],
						'choices' => $this->font_family,
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Font Weight', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'select',
						'name'    => 'fields[' . $key . '][font_weight]',
						'value'   => $field['font_weight'],
						'choices' => $this->font_weight,
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Font Style', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'select',
						'name'    => 'fields[' . $key . '][font_style]',
						'value'   => $field['font_style'],
						'ui'      => 1,
						'choices' => $this->font_style,
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Font Variant', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'select',
						'name'    => 'fields[' . $key . '][font_variant]',
						'value'   => $field['font_variant'],
						'ui'      => 1,
						'choices' => $this->font_variant,
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Font Stretch', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'select',
						'name'    => 'fields[' . $key . '][font_stretch]',
						'value'   => $field['font_stretch'],
						'ui'      => 1,
						'choices' => $this->font_stretch,
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Line Height', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'   => 'number',
						'name'   => 'fields[' . $key . '][line_height]',
						'value'  => $field['line_height'],
						'append' => 'px',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Letter Spacing', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'   => 'number',
						'name'   => 'fields[' . $key . '][letter_spacing]',
						'value'  => $field['letter_spacing'],
						'append' => 'px',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Text Align', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'select',
						'name'    => 'fields[' . $key . '][text_align]',
						'value'   => $field['text_align'],
						'ui'      => 1,
						'choices' => $this->text_align,
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Text Color', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'  => 'text',
						'name'  => 'fields[' . $key . '][text_color]',
						'value' => $field['text_color'],
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Text Decoration', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'select',
						'name'    => 'fields[' . $key . '][text_decoration]',
						'value'   => $field['text_decoration'],
						'ui'      => 1,
						'choices' => $this->text_decoration,
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo esc_attr( $this->name ); ?>">
			<td class="label">
				<label><?php esc_html_e( 'Text Transform', 'acf-typography' ); ?></label>
				<p class="description"><?php esc_html_e( '(Default)', 'acf-typography' ); ?></p>
			</td>
			<td>
				<?php
				do_action(
					'acf/create_field',
					array(
						'type'    => 'select',
						'name'    => 'fields[' . $key . '][text_transform]',
						'value'   => $field['text_transform'],
						'ui'      => 1,
						'choices' => $this->text_transform,
						'layout'  => 'horizontal',
					)
				);
				?>
			</td>
		</tr>

		<?php
	}
	
	
	/**
	 * Create field HTML.
	 *
	 * Create the HTML interface for your field.
	 *
	 * @since 3.6
	 * @param array $field The field being rendered.
	 */
	public function create_field( $field ) {
		// Merge with defaults.
		$field = array_merge( $this->defaults, $field );

		$key = $field['key'];

		$field['value'] = acf_force_type_array( $field['value'] );

		// Create Field HTML.
		if ( ! empty( $field['display_properties'] ) && count( $field['display_properties'] ) > 0 ) {

			foreach ( $field['display_properties'] as $f ) {

				$numbers = array();
				$selects = array();

				$required = '';
				if ( is_array( $field['required_properties'] ) && in_array( $f, $field['required_properties'], true ) ) {
					$required = 'required';
				}

				if ( 'font_size' === $f || 'line_height' === $f || 'letter_spacing' === $f ) {
					$numbers[] = $f;
				} elseif ( in_array( $f, array( 'font_family', 'font_weight', 'font_style', 'font_variant', 'font_stretch', 'text_align', 'text_decoration', 'text_transform' ), true ) ) {
					$selects[] = $f;
				}

				if ( in_array( $f, $numbers, true ) ) {
					?>
					<div id="acf-<?php echo esc_attr( $f ); ?>" class="field field_type-number field_key-<?php echo esc_attr( $key ); ?> <?php echo esc_attr( $required ); ?>" data-field_name="<?php echo esc_attr( $f ); ?>" data-field_key="<?php echo esc_attr( $key ); ?>" data-field_type="number">
						<p class="label">
							<label for="acf-field-<?php echo esc_attr( $f ); ?>">
								<?php echo esc_html( ucfirst( str_replace( '_', ' ', $f ) ) ); ?>
								<?php if ( ! empty( $required ) ) { ?>
									<span class="required">*</span>
								<?php } ?>
							</label>
						</p>

						<?php
						do_action(
							'acf/create_field',
							array(
								'type'   => 'number',
								'name'   => $field['name'] . '[' . $f . ']',
								'value'  => ( ! empty( $field['value'][ $f ] ) ? $field['value'][ $f ] : $field[ $f ] ),
								'id'     => 'acf-field-' . $f,
								'append' => 'px',
							)
						);
						?>
					</div>
					<?php
				} elseif ( in_array( $f, $selects, true ) ) {
					?>
					<div id="acf-<?php echo esc_attr( $f ); ?>" class="field field_type-select field_key-<?php echo esc_attr( $key ); ?> <?php echo esc_attr( $required ); ?>" data-field_name="<?php echo esc_attr( $f ); ?>" data-field_key="<?php echo esc_attr( $key ); ?>" data-field_type="select">
						<p class="label">
							<label for="acf-field-<?php echo esc_attr( $f ); ?>">
								<?php echo esc_html( ucfirst( str_replace( '_', ' ', $f ) ) ); ?>
								<?php if ( ! empty( $required ) ) { ?>
									<span class="required">*</span>
								<?php } ?>
							</label>
						</p>

						<select id="acf-field-<?php echo esc_attr( $f ); ?>" class="select" name="<?php echo esc_attr( $field['name'] . '[' . $f . ']' ); ?>">
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
					<?php
				} else {
					?>
					<div id="acf-<?php echo esc_attr( $f ); ?>" class="field field_type-text field_key-<?php echo esc_attr( $key ); ?> <?php echo esc_attr( $required ); ?> acf-color_picker" data-field_name="<?php echo esc_attr( $f ); ?>" data-field_key="<?php echo esc_attr( $key ); ?>" data-field_type="text">
						<p class="label">
							<label for="acf-field-<?php echo esc_attr( $f ); ?>">
								<?php echo esc_html( ucfirst( str_replace( '_', ' ', $f ) ) ); ?>
								<?php if ( ! empty( $required ) ) { ?>
									<span class="required">*</span>
								<?php } ?>
							</label>
						</p>

						<?php
						do_action(
							'acf/create_field',
							array(
								'type'  => 'text',
								'name'  => $field['name'] . '[' . $f . ']',
								'value' => ( ! empty( $field['value'][ $f ] ) ? $field['value'][ $f ] : $field[ $f ] ),
								'id'    => 'acf-field-' . $f,
							)
						);
						?>
					</div>
					<?php
				}
			}
		}
	}
	
	
	/**
	 * Enqueue scripts and styles for field input.
	 *
	 * This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	 * Use this action to add CSS + JavaScript to assist your create_field() action.
	 *
	 * @since 3.6
	 */
	public function input_admin_enqueue_scripts() {
		// Note: This function can be removed if not used.

		// Vars.
		$url     = $this->settings['url'];
		$version = $this->settings['version'];

		// Register & include JS.
		// wp_register_script( 'acf-input-Typography', "{$url}assets/js/input.js", array('acf-input'), $version );
		// wp_enqueue_script('acf-input-Typography');

		// Register & include CSS.
		// wp_register_style( 'acf-input-Typography', "{$url}assets/css/input.css", array('acf-input'), $version );
		// wp_enqueue_style('acf-input-Typography');
	}

	/**
	 * Add CSS and JavaScript in admin head.
	 *
	 * This action is called in the admin_head action on the edit screen where your field is created.
	 * Use this action to add CSS and JavaScript to assist your create_field() action.
	 *
	 * @since 3.6
	 */
	public function input_admin_head() {
		// Note: This function can be removed if not used.
	}

	/**
	 * Enqueue scripts for field group admin.
	 *
	 * This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	 * Use this action to add CSS + JavaScript to assist your create_field_options() action.
	 *
	 * @since 3.6
	 */
	public function field_group_admin_enqueue_scripts() {
		// Note: This function can be removed if not used.
	}

	/**
	 * Add CSS and JavaScript in field group admin head.
	 *
	 * This action is called in the admin_head action on the edit screen where your field is edited.
	 * Use this action to add CSS and JavaScript to assist your create_field_options() action.
	 *
	 * @since 3.6
	 */
	public function field_group_admin_head() {
		// Note: This function can be removed if not used.
	}

	/**
	 * Load value from database.
	 *
	 * This filter is applied to the $value after it is loaded from the db.
	 *
	 * @since 3.6
	 * @param mixed $value   The value found in the database.
	 * @param mixed $post_id The post ID from which the value was loaded.
	 * @param array $field   The field array holding all the field options.
	 * @return mixed The value.
	 */
	public function load_value( $value, $post_id, $field ) {
		// Note: This function can be removed if not used.
		return $value;
	}

	/**
	 * Update value before saving to database.
	 *
	 * This filter is applied to the $value before it is updated in the db.
	 *
	 * @since 3.6
	 * @param mixed $value   The value which will be saved in the database.
	 * @param mixed $post_id The post ID of which the value will be saved.
	 * @param array $field   The field array holding all the field options.
	 * @return mixed The modified value.
	 */
	public function update_value( $value, $post_id, $field ) {
		// Note: This function can be removed if not used.
		return $value;
	}

	/**
	 * Format value for display.
	 *
	 * This filter is applied to the $value after it is loaded from the db and before it is passed to the create_field action.
	 *
	 * @since 3.6
	 * @param mixed $value   The value which was loaded from the database.
	 * @param mixed $post_id The post ID from which the value was loaded.
	 * @param array $field   The field array holding all the field options.
	 * @return mixed The modified value.
	 */
	public function format_value( $value, $post_id, $field ) {
		// Defaults?
		/*
		$field = array_merge( $this->defaults, $field );
		*/

		// Perhaps use $field['preview_size'] to alter the $value?

		// Note: This function can be removed if not used.
		return $value;
	}

	/**
	 * Format value for API.
	 *
	 * This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field.
	 *
	 * @since 3.6
	 * @param mixed $value   The value which was loaded from the database.
	 * @param mixed $post_id The post ID from which the value was loaded.
	 * @param array $field   The field array holding all the field options.
	 * @return mixed The modified value.
	 */
	public function format_value_for_api( $value, $post_id, $field ) {
		// Defaults?
		/*
		$field = array_merge( $this->defaults, $field );
		*/

		// Perhaps use $field['preview_size'] to alter the $value?

		// Note: This function can be removed if not used.

		if ( ! $value || 'null' === $value ) {
			return false;
		}

		// Return the subfields as an array.
		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				$f          = $v;
				$value[ $k ] = array();
				$value[ $k ] = $f;
			}
		}

		return $value;
	}

	/**
	 * Load field settings.
	 *
	 * This filter is applied to the $field after it is loaded from the database.
	 *
	 * @since 3.6
	 * @param array $field The field array holding all the field options.
	 * @return array The field array holding all the field options.
	 */
	public function load_field( $field ) {
		// Note: This function can be removed if not used.
		return $field;
	}

	/**
	 * Update field settings.
	 *
	 * This filter is applied to the $field before it is saved to the database.
	 *
	 * @since 3.6
	 * @param array $field   The field array holding all the field options.
	 * @param mixed $post_id The field group ID (post_type = acf).
	 * @return array The modified field.
	 */
	public function update_field( $field, $post_id ) {
		// Note: This function can be removed if not used.
		return $field;
	}
}


// Initialize.
new acf_field_Typography( $this->settings );

// Class_exists check.
endif;
