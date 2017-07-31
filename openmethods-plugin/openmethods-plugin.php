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
                $args = array('headers' => array('x-auth-token' => $instance['apikey'],
                    'user-agent' => 'Yoann (http://google.com)',
                    'Content-Type' => 'application/json; charset=utf-8'));
                $response = wp_remote_get($teresahApiUrl . "?limit=10&query=" . $_GET['query'], $args);
            }

            if (is_wp_error($response)) {
                return;
            }
            echo "<a href='http://141.5.105.148/' target='_blank' title='Link to TERESAH'>TERESAH (Tools E-Registry for E-Social science, Arts and Humanities)</a> is a cross-community tools knowledge registry aimed at researchers in the Social Sciences and Humanities.";
            $form = '<form role="search" method="get" class="search-form" action="/">
                <label>
                    <span class="screen-reader-text">' . _x('Search for:', 'label') . '</span>
                    <input type="search" class="search-field" placeholder="' . esc_attr_x('Search on TERESAH ...', 'placeholder') . '" value="' . get_search_query() . '" name="query" title="' . esc_attr_x('Search for:', 'label') . '" />
                </label>
                <button type="submit" class="search-submit"><span class="screen-reader-text">Search</span>
                    <svg class="icon icon-search" aria-hidden="true" role="img">
                        <use href="#icon-search" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-search"></use>
                    </svg>        
                </button>
            </form>';
            echo $form;

            $posts = json_decode(wp_remote_retrieve_body($response));
            echo $args['before_widget'];
            //        if (!empty($instance['title'])) {
            //            echo $args['before_title'] . apply_filters('widget_title', $instance['title'], $instance, $this->id_base) . $args['after_title'];
            //        }

            if (!empty($posts)) {
                echo '<ul>';
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