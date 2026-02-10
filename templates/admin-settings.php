<div class="wrap">
    <h1><?php echo esc_html(__('Location Finder V2 Settings', 'advantago-location-finder')); ?></h1>

    <form method="post" action="options.php">
        <?php
        settings_fields('alf_settings_group');
        $options = get_option('alf_settings', array());
        if (!isset($pages) || !is_array($pages)) {
            $pages = get_pages();
        }
        ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="yext_api_key"><?php _e('Yext API Key', 'advantago-location-finder'); ?></label></th>
                <td>
                    <input type="text" id="yext_api_key" name="alf_settings[yext_api_key]" value="<?php echo esc_attr(isset($options['yext_api_key']) ? $options['yext_api_key'] : ''); ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="google_maps_api_key"><?php _e('Google Maps API Key', 'advantago-location-finder'); ?></label></th>
                <td>
                    <input type="text" id="google_maps_api_key" name="alf_settings[google_maps_api_key]" value="<?php echo esc_attr(isset($options['google_maps_api_key']) ? $options['google_maps_api_key'] : ''); ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="map_service_id"><?php _e('Map Service ID', 'advantago-location-finder'); ?></label></th>
                <td>
                    <input type="text" id="map_service_id" name="alf_settings[map_service_id]" value="<?php echo esc_attr(isset($options['map_service_id']) ? $options['map_service_id'] : ''); ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="locations_per_page"><?php _e('Location Entries per page', 'advantago-location-finder'); ?></label></th>
                <td>
                    <input type="number" id="locations_per_page" name="alf_settings[locations_per_page]" value="<?php echo esc_attr(isset($options['locations_per_page']) ? $options['locations_per_page'] : 5); ?>" min="5" max="100" class="small-text">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="cache_lifespan"><?php _e('Cache Lifespan', 'advantago-location-finder'); ?></label></th>
                <td>
                    <select id="cache_lifespan" name="alf_settings[cache_lifespan]">
                        <?php $current_cache = isset($options['cache_lifespan']) ? $options['cache_lifespan'] : '900'; ?>
                        <option value="900" <?php selected($current_cache, '900'); ?>><?php _e('15 minutes', 'advantago-location-finder'); ?></option>
                        <option value="1800" <?php selected($current_cache, '1800'); ?>><?php _e('30 minutes', 'advantago-location-finder'); ?></option>
                        <option value="3600" <?php selected($current_cache, '3600'); ?>><?php _e('60 minutes', 'advantago-location-finder'); ?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Clear Cache', 'advantago-location-finder'); ?></th>
                <td>
                    <button type="button" id="alf-clear-cache-btn" class="button"><?php _e('Clear Cache', 'advantago-location-finder'); ?></button>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="main_color"><?php _e('Main color', 'advantago-location-finder'); ?></label></th>
                <td>
                    <input type="text" id="main_color" name="alf_settings[main_color]" value="<?php echo esc_attr(isset($options['main_color']) ? $options['main_color'] : '#000000'); ?>" class="alf-color-picker">
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Show logo on detail page', 'advantago-location-finder'); ?></th>
                <td>
                    <input type="checkbox" id="show_logo_detail" name="alf_settings[show_logo_detail]" value="1" <?php checked(isset($options['show_logo_detail']) ? $options['show_logo_detail'] : 0, 1); ?>>
                    <label for="show_logo_detail"><?php _e('Show logo on detail page', 'advantago-location-finder'); ?></label>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Show location name on detail page', 'advantago-location-finder'); ?></th>
                <td>
                    <input type="checkbox" id="show_location_name_detail" name="alf_settings[show_location_name_detail]" value="1" <?php checked(isset($options['show_location_name_detail']) ? $options['show_location_name_detail'] : 0, 1); ?>>
                    <label for="show_location_name_detail"><?php _e('Show location name on detail page', 'advantago-location-finder'); ?></label>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Activate Search autocomplete', 'advantago-location-finder'); ?></th>
                <td>
                    <input type="checkbox" id="activate_autocomplete" name="alf_settings[activate_autocomplete]" value="1" <?php checked(isset($options['activate_autocomplete']) ? $options['activate_autocomplete'] : 0, 1); ?>>
                    <label for="activate_autocomplete"><?php _e('Activate Search autocomplete', 'advantago-location-finder'); ?></label>
                    <p class="description"><?php _e('For searches with suggestions to work, the Google Places API must be active for the Google Key used.', 'advantago-location-finder'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="country_codes"><?php _e('Country Codes included in suggestions', 'advantago-location-finder'); ?></label></th>
                <td>
                    <input type="text" id="country_codes" name="alf_settings[country_codes]" value="de" disabled>
                    <p class="description"><?php _e('Maximum 5 countries. Country code format: ISO 3166-1 Alpha-2, separated by commas (e.g., de, ch, at). Default: de', 'advantago-location-finder'); ?></p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
