<?php

/**
* Plugin Name: Teresah Search Widget
* Author: Yoann
*/

class TeresahSearchWidget extends WP_Widget
{
    /**
     * Sets up the widgets name etc
     */
    public function __construct()
    {
        $widget_ops = array(
            'classname' => 'teresah-search-widget',
            'description' => 'A search widget that searches data on TERESAH website');

        parent::__construct('teresah_search_widget', 'Teresah Search Widget', $widget_ops);
    }


    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        if(!empty($instance['apikey']) && !empty($instance['teresahurl'])) {
            $teresahApiUrl = $instance['teresahurl'] . "api/v1/tools/search.json";
            $teresahToolUrl = $instance['teresahurl'] . "tools/";

            if (isset($_GET['query']) && !empty($_GET['query'])) {
                $teresahArgs = array('headers' => array('x-auth-token' => $instance['apikey'],
                    'user-agent' => 'Yoann (http://google.com)',
                    'Content-Type' => 'application/json; charset=utf-8'));
                $response = wp_remote_get($teresahApiUrl . "?limit=10&query=" . $_GET['query'], $teresahArgs);
            }

            if (is_wp_error($response)) {
                return;
            }
            echo $args['before_widget'];
            echo "<a href='http://141.5.105.148/' target='_blank' title='Link to TERESAH' id='teresah_link'>TERESAH (Tools E-Registry for E-Social science, Arts and Humanities)</a> is a cross-community tools knowledge registry aimed at researchers in the Social Sciences and Humanities.";
            $form = '<form role="search" method="get" class="search-form" action="/#teresah_link">
                <label>
                    <span class="screen-reader-text">' . _x('Search for:', 'label') . '</span>
                    <input type="search" class="search-field" placeholder="' . esc_attr_x('Search on TERESAH ...', 'placeholder') . '" value="' . get_search_query() . '" name="query" title="' . esc_attr_x('Search for:', 'label') . '" />
                </label>
                <button type="submit" class="search-submit"><span class="screen-reader-text">Search</span>        
                </button>
            </form>';
            echo $form;

            $posts = json_decode(wp_remote_retrieve_body($response));
            if (!empty($posts)) {
                echo '<ul style="list-style-type: circle;">';
                foreach ($posts->tools->data as $post) {
                    echo '<li><a href="' . $teresahToolUrl . $post->slug . '" target="_blank">' . $post->name . '</a></li>';
                }
                if (sizeof($posts->tools->data) < 1) {
                    echo '<li>No results...</li>';
                }
                echo '</ul>';
            }
            echo $args['after_widget'];
        }
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form($instance)
    {
        $apikey = (!empty($instance['apikey'])) ? $instance['apikey'] : '';
        $admin = '<label for="' . $this->get_field_id('apikey') . '">API key: </label>
                <input class="widefat" id="' . $this->get_field_id('apikey') . '" title="input" name="' . $this->get_field_name('apikey') . '" type="text"
               value="' . esc_attr($apikey) . '"/>';
        echo $admin;

        $teresahurl = (!empty($instance['teresahurl'])) ? $instance['teresahurl'] : '';
        $admin = '<label for="' . $this->get_field_id('teresahurl') . '">TERESAH URL: </label>
                <input class="widefat" id="' . $this->get_field_id('teresahurl') . '" title="input" name="' . $this->get_field_name('teresahurl') . '" type="text"
               value="' . esc_attr($teresahurl) . '"/>';
        echo $admin;
    }
}

add_action('widgets_init', function() {
    register_widget('TeresahSearchWidget');
});