<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

    $meta_query = array(
        array(
            'key' => '_applicant_contact_id',
            'value' => $post_id
        ),
    );

    if ( isset($selected_status) && !empty($selected_status) )
    {
        $meta_query[] = array(
            'key' => '_status',
            'value' => $selected_status,
        );
    }

    $args = array(
        'post_type'   => 'sale',
        'nopaging'    => true,
        'orderby'     => 'meta_value',
        'order'       => 'DESC',
        'meta_key'    => '_sale_date_time',
        'post_status' => 'publish',
        'meta_query'  => $meta_query,
    );
    $sales_query = new WP_Query( $args );
    $sales_count = $sales_query->found_posts;

    $columns = array(
        'date' => __( 'Sale Date', 'propertyhive' ),
        'property' => __( 'Property', 'propertyhive' ),
        'property_owner' => __( 'Property Owner', 'propertyhive' ),
        'amount' => __( 'Sale Amount', 'propertyhive' ),
        'status' => __( 'Status', 'propertyhive' ),
    );

    $columns = apply_filters( 'propertyhive_contact_sales_columns', $columns );
?>

<div class="tablenav top">
    <div class="alignleft actions">
        <select name="_status" id="_sale_status_filter">
            <option value=""><?php echo esc_html(__( 'All Statuses', 'propertyhive' )); ?></option>
            <?php
                $sale_statuses = ph_get_sale_statuses();

                foreach ( $sale_statuses as $status => $display_status )
                {
                    ?>
                    <option value="<?php echo esc_attr($status); ?>" <?php selected( $status, $selected_status ); ?>><?php echo esc_html($display_status); ?></option>
                    <?php
                }
            ?>
        </select>
        <input type="button" name="filter_action" id="filter-contact-sales-grid" class="button" value="Filter">
        <a href="" name="export_action" id="export-contact-sales-grid" class="button">Export</a>
    </div>
    <div class='tablenav-pages one-page'>
        <span class="displaying-num"><?php echo $sales_count; ?> item<?php echo $sales_count != 1 ? 's' : ''; ?></span>
    </div>
    <br class="clear" />
</div>
<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
        <?php
            $column_i = 0;
            foreach ( $columns as $column_key => $column )
            {
                ?>
                <th scope="col" id='<?php echo esc_attr($column_key); ?>' class='manage-column column-<?php echo esc_attr($column_key); echo ($column_i == 0 ? ' column-primary' : ''); ?>'><?php echo esc_html($column); ?></th>
                <?php
                ++$column_i;
            }
        ?>
        </tr>
    </thead>
    <tbody id="the-list">
    <?php
        if ( $sales_query->have_posts() )
        {
            while ( $sales_query->have_posts() )
            {
                $sales_query->the_post();
                $the_sale = new PH_Sale( get_the_ID() );

                $edit_link = get_edit_post_link( get_the_ID() );

                $column_data = array(
                    'date' => '<a href="' . esc_url($edit_link) . '" target="' . esc_attr(apply_filters('propertyhive_subgrid_link_target', '')) . '" data-sale-id="' . esc_attr(get_the_ID()) . '">' . esc_html(date("jS F Y", strtotime($the_sale->_sale_date_time))) . '</a>',
                    'property' => $the_sale->get_property_address(),
                    'property_owner' => $the_sale->get_property_owners(),
                    'amount' => esc_html($the_sale->get_formatted_amount()),
                    'status' => esc_html(__( ucwords(str_replace("_", " ", $the_sale->_status)), 'propertyhive' )),
                );

                $row_classes = array( 'status-' . $the_sale->_status );
                $row_classes = apply_filters( 'propertyhive_contact_sales_row_classes', $row_classes, get_the_ID(), $the_sale );
                $row_classes = is_array($row_classes) ? array_map( 'sanitize_html_class', array_map( 'strtolower', $row_classes ) ) : array();
                ?>
                    <tr class="<?php echo esc_attr(implode(" ", $row_classes)); ?>" >
                    <?php
                        $column_i = 0;
                        foreach ( $columns as $column_key => $column )
                        {
                            echo '<td class="' . esc_attr($column_key) . ' column-' . esc_attr($column_key) . ($column_i == 0 ? ' column-primary' : '') . '" data-colname="' . esc_attr($column) . '">';

                            if ( isset( $column_data[$column_key] ) )
                            {
                                echo $column_data[$column_key];
                            }

                            do_action( 'propertyhive_contact_sales_custom_column', $column_key );

                            if ( $column_i == 0 ) { echo '<button type="button" class="toggle-row"><span class="screen-reader-text">' . esc_html(__('Show more details', 'propertyhive' )) . '</span></button>'; }

                            echo '</td>';
                            ++$column_i;
                        }
                    ?>
                    </tr>
                <?php
            }
        }
        else
        {
            ?>
            <tr class="no-items">
                <td class="colspanchange" colspan="<?php echo count($columns); ?>"><?php echo esc_html(__( 'No sales found', 'propertyhive' )); ?></td>
            </tr>
            <?php
        }
        wp_reset_postdata();
    ?>
    </tbody>
</table>