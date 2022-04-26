<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_field_Typography') ) :


class acf_field_Typography extends acf_field {
	
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
		
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct( $settings )
	{
		// vars
		$this->name = 'Typography';
		$this->label = __('Typography', 'acf-typography');
		$this->category = __("Content",'acf-typography'); // Basic, Content, Choice, etc
		$this->defaults = array(
			// add default here to merge into your field. 
			// This makes life easy when creating the field options as you don't need to use any if( isset('') ) logic. eg:
			'display_properties'	=> array(),
			'required_properties'	=> array(),
			'font_size'				=> 15,
			'font_weight'			=> '400',
			'font_family'			=> 'Arial, Helvetica, sans-serif',
			'font_style'			=> 'normal',
            'font_variant'			=> 'normal',
            'font_stretch'			=> 'normal',
			'text_align'			=> 'left',
			'letter_spacing'		=> 0,
			'text_decoration'		=> 'none',
			'text_color'			=> '#000',
			'text_transform'		=> 'none'
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

		$google_font_family = acft_get_google_font_family(); // get google fonts from json file

		// merge web-safe-fonts and google fonts arrays
		if( is_array( $google_font_family ) )
			$this->font_family = array_merge( $this->font_family, $google_font_family ); 

		// sort array by array key
		ksort( $this->font_family );
        
        // add 'initial' and 'inherit' property values to top of the array
        $this->font_family = array_merge( array( 'initial' => 'initial', 'inherit' => 'inherit' ), $this->font_family );
		
		$this->font_weight = array(
			'100'		=> '100',
			'200'		=> '200',
			'300'		=> '300',
			'400'		=> '400',
			'500'		=> '500',
			'600'		=> '600',
			'700'		=> '700',
			'800'		=> '800',
			'900'		=> '900'
		);
		$this->font_style = array(
			'normal'	=> 'normal',
			'italic'	=> 'italic',
			'oblique'	=> 'oblique',
		);
        $this->font_variant = array(
			'normal'     => 'normal',
            'small-caps' => 'small-caps',
            'initial'    => 'initial',
            'inherit'    => 'inherit'
		);
		$this->font_stretch = array(
			'ultra-condensed'   => 'ultra-condensed',
			'extra-condensed'   => 'extra-condensed',
			'condensed'         => 'condensed',
			'semi-condensed'    => 'semi-condensed',
			'normal'            => 'normal',
			'semi-expanded'     => 'semi-expanded',
			'expanded'          => 'expanded',
			'extra-expanded'    => 'extra-expanded',
			'ultra-expanded'    => 'ultra-expanded',
			'initial'           => 'initial',
			'inherit'           => 'inherit'
		);
		$this->text_align = array(
			'inherit'	=> 'inherit',
			'left'		=> 'left',
			'right'		=> 'right', 
			'center'	=> 'center', 
			'justify'	=> 'justify', 
			'initial'	=> 'initial'
		);
		$this->text_decoration = array(
			'none' 			=> 'none',
			'underline' 	=> 'underline',
			'overline' 		=> 'overline',
			'line-through' 	=> 'line-through',
			'initial' 		=> 'initial',
			'inherit' 		=> 'inherit'
		);
		$this->text_transform = array(
			'none' 			=> 'none',
			'capitalize' 	=> 'capitalize',
			'uppercase' 	=> 'uppercase',
			'lowercase' 	=> 'lowercase',
			'initial' 		=> 'initial',
			'inherit' 		=> 'inherit'
		);
		
		
		// do not delete!
    	parent::__construct();

	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		// defaults?
		$field = array_merge($this->defaults, $field);
		
		
		// key is needed in the field names to correctly save the data
		$key = $field['name'];
		
		
		// Create Field Options HTML
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Display Properties','acf-typography'); ?></label>
				<p class="description"><?php _e('Select fields to display on edit page', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'checkbox',
					'name'  =>  'fields['.$key.'][display_properties]',
					'value' =>  $field['display_properties'],
					'choices' =>  array(
						'font_size' =>  __("Font Size",'acf-typography'),
						'font_family' =>  __("Font Family",'acf-typography'),
						'font_weight' =>  __("Font Weight",'acf-typography'),
						'font_style' =>  __("Font Style",'acf-typography'),
                        'font_variant' =>  __("Font Variant",'acf-typography'),
                        'font_stretch' =>  __("Font Stretch",'acf-typography'),
						'line_height' =>  __("Line Height",'acf-typography'),
						'letter_spacing' =>  __("Letter Spacing",'acf-typography'),
						'text_align' =>  __("Text Align",'acf-typography'),
						'text_color' =>  __("Text Color",'acf-typography'),
						'text_decoration' =>  __("Text Decoration",'acf-typography'),
						'text_transform' =>  __("Text Transform",'acf-typography'),
					),
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Required Properties",'acf-typography'); ?></label>
				<p class="description"><?php _e('Select fields which are required on edit page', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'checkbox',
					'name'  =>  'fields['.$key.'][required_properties]',
					'value' =>  $field['required_properties'],
					'choices' =>  array(
						'font_size' =>  __("Font Size",'acf-typography'),
						'font_family' =>  __("Font Family",'acf-typography'),
						'font_weight' =>  __("Font Weight",'acf-typography'),
						'font_style' =>  __("Font Style",'acf-typography'),
                        'font_variant' =>  __("Font Variant",'acf-typography'),
                        'font_stretch' =>  __("Font Stretch",'acf-typography'),
						'line_height' =>  __("Line Height",'acf-typography'),
						'letter_spacing' =>  __("Letter Spacing",'acf-typography'),
						'text_align' =>  __("Text Align",'acf-typography'),
						'text_color' =>  __("Text Color",'acf-typography'),
						'text_decoration' =>  __("Text Decoration",'acf-typography'),
						'text_transform' =>  __("Text Transform",'acf-typography'),
					),
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Font Size",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'number',
					'name'  =>  'fields['.$key.'][font_size]',
					'value' =>  $field['font_size'],
					'append' => 'px'
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Font Family",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'select',
					'name'  =>  'fields['.$key.'][font_family]',
					'value' =>  $field['font_family'],
					'choices' =>  $this->font_family,
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Font Weight",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'select',
					'name'  =>  'fields['.$key.'][font_weight]',
					'value' =>  $field['font_weight'],
					'choices' =>  $this->font_weight,
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Font Style",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'select',
					'name'  =>  'fields['.$key.'][font_style]',
					'value' =>  $field['font_style'],
					'ui'	=> 1,
					'choices' =>  $this->font_style,
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Font Variant",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'select',
					'name'  =>  'fields['.$key.'][font_variant]',
					'value' =>  $field['font_variant'],
					'ui'	=> 1,
					'choices' =>  $this->font_variant,
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Font Stretch",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'select',
					'name'  =>  'fields['.$key.'][font_stretch]',
					'value' =>  $field['font_stretch'],
					'ui'	=> 1,
					'choices' =>  $this->font_stretch,
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Line Height",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'number',
					'name'  =>  'fields['.$key.'][line_height]',
					'value' =>  $field['line_height'],
					'append' => 'px'
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Letter Spacing",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'number',
					'name'  =>  'fields['.$key.'][letter_spacing]',
					'value' =>  $field['letter_spacing'],
					'append' => 'px'
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Text Align",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'select',
					'name'  =>  'fields['.$key.'][text_align]',
					'value' =>  $field['text_align'],
					'ui'	=> 1,
					'choices' =>  $this->text_align,
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Text Color",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'text',
					'name'  =>  'fields['.$key.'][text_color]',
					'value' =>  $field['text_color'],
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Text Decoration",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'select',
					'name'  =>  'fields['.$key.'][text_decoration]',
					'value' =>  $field['text_decoration'],
					'ui'	=> 1,
					'choices' => $this->text_decoration,
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Text Transform",'acf-typography'); ?></label>
				<p class="description"><?php _e('(Default)', 'acf-typography'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'  =>  'select',
					'name'  =>  'fields['.$key.'][text_transform]',
					'value' =>  $field['text_transform'],
					'ui'	=> 1,
					'choices' => $this->text_transform,
					'layout'  =>  'horizontal',
				));
				?>
			</td>
		</tr>

		<?php
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		// defaults?
		
		$field = array_merge($this->defaults, $field);
		
		$key = $field['key'];
		
		$field['value'] = acf_force_type_array($field['value']);
		
		// create Field HTML
		
		if( !empty( $field['display_properties'] ) && sizeof($field['display_properties']) > 0){

			foreach( $field['display_properties'] as $f ){
				
				$numbers = $selects = array();
				
				$required = '';
				if( is_array($field['required_properties']) && in_array($f, $field['required_properties']) ){
					$required = 'required';
				}

				if( $f == 'font_size' || $f == 'line_height' || $f == 'letter_spacing' ){
					$numbers[] = $f;
				}else if( $f == 'font_family' || $f == 'font_weight' || $f == 'font_style' || $f == 'font_variant' || $f == 'font_stretch' || $f == 'text_align' || $f == 'text_decoration' || $f == 'text_transform' ){
					$selects[] = $f;
				}
				
				if( in_array($f, $numbers) ){ ?>
					<div id="acf-<?php echo $f; ?>" class="field field_type-number field_key-<?php echo $key; ?> <?php echo $required; ?>" data-field_name="<?php echo $f; ?>" data-field_key="<?php echo $key; ?>" data-field_type="number">
						<p class="label">
							<label for="acf-field-<?php echo $f; ?>">
								<?php echo ucfirst(str_replace('_', ' ', $f)); ?>
								<?php if(!empty($required)){ ?>
									<span class="required">*</span>
								<?php } ?>
							
							</label>
						</p>

						<?php
							do_action('acf/create_field', array(
								'type'  =>  'number',
								'name'  =>  $field['name'].'['.$f.']',
								'value' =>  ( !empty($field['value'][$f]) ? $field['value'][$f] : $field[$f] ),
								'id' => 'acf-field-'.$f,
								'append' => 'px'
							));
						?>
					</div>
				<?php }else if( in_array($f, $selects) ){ ?>
					<div id="acf-<?php echo $f; ?>" class="field field_type-select field_key-<?php echo $key; ?> <?php echo $required; ?>" data-field_name="<?php echo $f; ?>" data-field_key="<?php echo $key; ?>" data-field_type="select">
						<p class="label">
							<label for="acf-field-<?php echo $f; ?>">
								<?php echo ucfirst(str_replace('_', ' ', $f)); ?>
								<?php if(!empty($required)){ ?>
									<span class="required">*</span>
								<?php } ?>
							</label>
						</p>

						<select id="acf-field-<?php echo $f; ?>" class="select" name="<?php echo $field['name'].'['.$f.']'; ?>">
							<?php 
								$options = '';
								foreach($this->$f as $opt){
									if( !empty($field['value'][$f]) )
										$options .= '<option val="'.$opt.'" '. ($field['value'][$f] == $opt? 'selected': '') .'>'.$opt.'</option>';
									else
										$options .= '<option val="'.$opt.'" '. ($field[$f] == $opt? 'selected': '') .'>'.$opt.'</option>';
								}
								echo $options;
							?>

						</select>
					</div>
				<?php }else{ ?>
					<div id="acf-<?php echo $f; ?>" class="field field_type-text field_key-<?php echo $key; ?> <?php echo $required; ?> acf-color_picker" data-field_name="<?php echo $f; ?>" data-field_key="<?php echo $key; ?>" data-field_type="text">
						<p class="label">
							<label for="acf-field-<?php echo $f; ?>">
								<?php echo ucfirst(str_replace('_', ' ', $f)); ?>
								<?php if(!empty($required)){ ?>
									<span class="required">*</span>
								<?php } ?>
							</label>
						</p>

						<?php
							do_action('acf/create_field', array(
								'type'  =>  'text',
								'name'  =>  $field['name'].'['.$f.']',
								'value' =>  ( !empty($field['value'][$f]) ? $field['value'][$f] : $field[$f] ),
								'id' => 'acf-field-'.$f,
							));
						?>
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
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
		
		
		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];
		
		
		// register & include JS
		//wp_register_script( 'acf-input-Typography', "{$url}assets/js/input.js", array('acf-input'), $version );
		//wp_enqueue_script('acf-input-Typography');
		
		
		// register & include CSS
		//wp_register_style( 'acf-input-Typography', "{$url}assets/css/input.css", array('acf-input'), $version );
		//wp_enqueue_style('acf-input-Typography');
		
	}
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your create_field() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_head()
	{
		// Note: This function can be removed if not used
	}
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your create_field_options() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
	}

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your create_field_options() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_head()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  load_value()
	*
		*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in the database
	*/
	
	function load_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		
		// perhaps use $field['preview_size'] to alter the $value?
		
		
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		
		// perhaps use $field['preview_size'] to alter the $value?
		
		
		// Note: This function can be removed if not used
		
		if(!$value || $value == 'null'){
			return false;
		}
		
		// Return the subfields as an array
		if(is_array($value)){
			foreach($value as $k => $v){
				$f = $v;
				$value[$k] = array();
				$value[$k] = $f;
			}
		}
		
		return $value;
	}
	
	
	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	
	function load_field( $field )
	{
		// Note: This function can be removed if not used
		return $field;
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field, $post_id )
	{
		// Note: This function can be removed if not used
		return $field;
	}

}


// initialize
new acf_field_Typography( $this->settings );


// class_exists check
endif;

?>
