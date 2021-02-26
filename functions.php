<?php

add_action( 'wp_enqueue_scripts', 'mediavine_trellis_child_enqueue_scripts' );
function mediavine_trellis_child_enqueue_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

if ( ! function_exists( 'mv_trellis_the_title' ) ) {
    function mv_trellis_the_title( string $before = '', string $after = '', bool $echo = true ) {
        global $wp_query;

        // Figure out which title to use
        $title = get_the_title();
        if ( is_front_page() || is_home() ) {
            $title = get_bloginfo( 'name' );
        }
        if ( is_home() && ! empty( get_option( 'page_for_posts' ) ) ) {
            $title = get_the_title( get_option( 'page_for_posts' ) );
        }

        if ( is_author() ) {
            $authors = get_multiple_authors(0, true, true);
            $author_page_title = $authors[0]->display_name;
            $title             = ( empty( $author_page_title ) ) ? $wp_query->queried_object->data->display_name : $author_page_title;
        }

        if ( is_category() || is_tag() ) {
            $term_display_title = get_term_meta( $wp_query->queried_object->term_id, 'term_display_title', true );
            $title              = ( empty( $term_display_title ) ) ? ucfirst( $wp_query->queried_object->name ) : $term_display_title;
        }

        if ( is_date() ) {
            $args['year']     = $wp_query->query_vars['year'];
            $args['monthnum'] = $wp_query->query_vars['monthnum'];
            $args['day']      = $wp_query->query_vars['day'];
            $date_format      = ( is_year() ) ? 'Y' : '';
            $date_format      = ( is_month() ) ? 'F Y' : $date_format;
            $the_date         = get_the_date( $date_format );
            $title            = 'Posts from: ' . $the_date;
        }

        // In some query instances, like author or date, $wp_query->queried_object is null
        // this should catch either taxonomy or post-type archives that aren't covered by the conditionals above
        // taxonomy and post-type should have $wp_query->queried_object populated
        if ( is_archive() && ! ( is_category() || is_tag() || is_date() || is_author() ) ) {
            $title = $wp_query->queried_object->name;
        }

        if ( is_search() ) {
            $title = 'Search results for: ' . get_search_query();
        }

        if ( strlen( $title ) === 0 ) {
            return '';
        }

        $title = $before . $title . $after;

        /**
         * Filters the page title
         *
         * @param string $title the current page/post/archive/cpt title, $before and $after already added
         * @param string $before added to the front of the title, usually some form of markup
         * @param string $after added to the end of the title, usually some form of markup
         * @return string filter title
         */
        $title = apply_filters( 'mv_trellis_the_title', $title, $before, $after );

        if ( $echo ) {
            echo wp_kses_post( $title );
        } else {
            return $title;
        }
    }
}
