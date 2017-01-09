<?php
/**
 * Sale Property Details
 *
 * @author 		PropertyHive
 * @category 	Admin
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * PH_Meta_Box_Sale_Property
 */
class PH_Meta_Box_Sale_Property {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
        global $wpdb, $thepostid;
        
        echo '<div class="propertyhive_meta_box">';
        
        echo '<div class="options_group">';
        
        $property_id = get_post_meta( $post->ID, '_property_id', true );

        if ( !empty($property_id) )
        {
            $property = new PH_Property((int)$property_id);

            echo '<p class="form-field">
            
                <label>' . __('Address', 'propertyhive') . '</label>
                
                <a href="' . get_edit_post_link($property_id, '') . '">' . $property->get_formatted_full_address() . '</a> (<a href="' . get_permalink($property_id) . '" target="_blank">View On Website</a>)
                
            </p>';

            echo '<p class="form-field">
            
                <label>' . ( ( $property->department == 'lettings' ) ? __('Landlord', 'propertyhive') : __('Owner', 'propertyhive') ) . '</label>';

            $owner_contact_id = $property->owner_contact_id;

            if ( empty($owner_contact_id) )
            {
                echo 'No ' . ( ( $property->department == 'lettings' ) ? __('landlord', 'propertyhive') : __('owner', 'propertyhive') ) . ' specified';
            }
            else
            {
                $owner = new PH_Contact((int)$owner_contact_id);
                echo '<a href="' . get_edit_post_link($owner_contact_id, '') . '">' . get_the_title($owner_contact_id) . '</a><br>';
                echo 'Telephone: ' . $owner->telephone_number . '<br>';
                echo 'Email: <a href="mailto:' . $owner->email_address . '">' . $owner->email_address . '</a>';
            }
                
            echo '</p>';
        }
        else
        {
            echo 'No property found';
        }

        do_action('propertyhive_sale_property_fields');
	    
        echo '</div>';
        
        echo '</div>';
        
    }

    /**
     * Save meta box data
     */
    public static function save( $post_id, $post ) {
        global $wpdb;

    }

}
