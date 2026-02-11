<?php
/** @var array $config */
/** @var array $result */
?>
<div class="alf-locations-list">
    <?php if (empty($result) || empty($result['success'])): ?>
        <div class="alf-locations-list__error">
            <strong class="alf-locations-list__error-label"><?php _e('Error:', 'advantago-location-finder'); ?></strong>
            <span class="alf-locations-list__error-message"><?php echo isset($result['error']) ? esc_html($result['error']) : __('Unknown error.', 'advantago-location-finder'); ?></span>
        </div>
        <?php if (!empty($config['debug']) && !empty($result['status'])): ?>
            <div class="alf-locations-list__debug">
                <?php _e('HTTP Status:', 'advantago-location-finder'); ?><?php echo (int)$result['status']; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php
        $data = isset($result['data']) ? $result['data'] : array();
        $locations = isset($data['entities']) ? $data['entities'] : array();
        ?>
        <div class="alf-locations-list__summary">
            <?php printf(__('Retrieved %d locations. (pls watch synchronized and not synchronized/hidden locations on yext)', 'advantago-location-finder'), isset($data['count']) ? (int)$data['count'] : count($locations)); ?>
        </div>
        <div class="alf-locations-list__source">
            <?php
            $source = isset($result['source']) ? $result['source'] : 'unknown';
            printf(__('Data source: %s', 'advantago-location-finder'), esc_html($source));
            ?>
        </div>
        <?php if (!empty($locations)): ?>
            <ul class="alf-locations-list__items">
                <?php foreach ($locations as $location): ?>
                    <li class="alf-locations-list__item">

                        <div class="alf-locations-list__item-row">

                            <div class="alf-locations-list__item-col alf-locations-list__item-col-base-infos">
                                <div class="alf-locations-list__item-name">
                                    <?php
                                    $name = isset($location['name']) ? $location['name'] : (isset($location['id']) ? ('ID ' . $location['id']) : __('Unnamed', 'advantago-location-finder'));
                                    echo esc_html($name);
                                    ?>
                                </div>
                                <div class="alf-locations-list__item-distance">
                                    2,6 km entfernt
                                </div>

                                <div class="opened">
                                    <?php
                                    // Opening hours status
                                    $is_open = false;
                                    $hours = isset($location['hours']) ? $location['hours'] : null;

                                    if ($hours) {

                                        $days_map = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
                                        $current_day = strtolower($days_map[date('w')]);
                                        $current_time = date('H:i');
                                        if (isset($hours[$current_day]['openIntervals'])) {
                                            foreach ($hours[$current_day]['openIntervals'] as $interval) {
                                                $start = isset($interval['start']) ? $interval['start'] : '';
                                                $end = isset($interval['end']) ? $interval['end'] : '';
                                                if ($current_time >= $start && $current_time <= $end) {
                                                    $is_open = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <span class="alf-locations-list__item-status">
                                     <?php echo $is_open ? esc_html__('Opened', 'advantago-location-finder') : esc_html__('Closed', 'advantago-location-finder'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="alf-locations-list__item-row">

                            <div class="alf-locations-list__item-col alf-locations-list__item-col-additional-infos">

                                <?php if ($hours): ?>
                                    <div class="alf-locations-list__item-hours">
                                        <?php
                                        $all_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
                                        foreach ($all_days as $day):
                                            $day_label = ucfirst($day);
                                            ?>
                                            <div class="alf-locations-list__item-hours-day">
                                                <span class="alf-locations-list__item-hours-day-name"><?php echo esc_html($day_label); ?>:</span>
                                                <span class="alf-locations-list__item-hours-day-times">
                                        <?php
                                        if (isset($hours[$day]['openIntervals']) && !empty($hours[$day]['openIntervals'])) {
                                            $intervals = array();
                                            foreach ($hours[$day]['openIntervals'] as $interval) {
                                                $start = isset($interval['start']) ? $interval['start'] : '';
                                                $end = isset($interval['end']) ? $interval['end'] : '';
                                                if ($start && $end) {
                                                    $intervals[] = esc_html($start) . ' - ' . esc_html($end);
                                                }
                                            }
                                            echo implode(', ', $intervals);
                                        } else {
                                            echo esc_html__('Closed', 'advantago-location-finder');
                                        }
                                        ?>
                                        </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if (isset($location['c_bewertungswidget'])): ?>
                                        <span class="alf-locations-list__item-widget"><?php echo $location['c_bewertungswidget']; ?></span>
                                    <?php endif; ?>

                                <?php endif; ?>

                            </div>

                            <div class="alf-locations-list__item-col">
                                <div class="alf-locations-list__item-rewiev">
                                    <?php if (isset($location['c_bewertungswidget'])): ?>
                                        <span class="alf-locations-list__item-rewview-widget"><?php echo $location['c_bewertungswidget']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>


                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <pre class="alf-locations-list__json"><?php echo esc_html(wp_json_encode($data, JSON_PRETTY_PRINT)); ?></pre>
        <?php endif; ?>
    <?php endif; ?>
</div>
