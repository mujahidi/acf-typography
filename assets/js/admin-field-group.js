(function( $ ) {
    'use strict';
    
    $(function() {

        $('.acf-field-setting-display_properties').each(function(){

            var $this = $(this),
                properties_field = Array(),
                field_object = $this.closest('.acf-field-object-typography'),
                field_id = field_object.data('id');

            $this.find('input[type="checkbox"]').each(function(){
                properties_field.push( $(this).val() );
            });

            if( properties_field.length ){

                properties_field.forEach(function( property ){

                    var checkbox_field = field_object.find('#acf_fields-' + field_id + '-required_properties-' + property);

                    checkbox_field.closest('li').hide();
                    field_object.find('.acf-field-setting-' + property).hide();

                    if( field_object.find('#acf_fields-' + field_id + '-display_properties-' + property).prop('checked') ){
                        checkbox_field.closest('li').show();
                        field_object.find('.acf-field-setting-' + property).show();
                    }
                    
                });

            }

        });

        $('.acf-field-list').on('click', '.acf-field-setting-display_properties input', function(){

            var display_properties = $(this).closest('.acf-field-setting-display_properties'),
                properties_field = Array(),
                field_object = display_properties.closest('.acf-field-object-typography'),
                field_id = field_object.data('id');

            display_properties.find('input[type="checkbox"]').each(function(){
                properties_field.push( $(this).val() );
            });

            if( properties_field.length ){

                properties_field.forEach(function( property ){

                    var checkbox_field = field_object.find('#acf_fields-' + field_id + '-required_properties-' + property);

                    checkbox_field.closest('li').hide();
                    field_object.find('.acf-field-setting-' + property).hide();

                    if( field_object.find('#acf_fields-' + field_id + '-display_properties-' + property).prop('checked') ){
                        checkbox_field.closest('li').show();
                        field_object.find('.acf-field-setting-' + property).show();
                    }
                    
                });

            }

        });
        
    });

})( jQuery );
