<?php

define('HEADER_IMAGE_WIDTH', 960); 
define('HEADER_IMAGE_HEIGHT', 320);
define('HEADER_IMAGE', get_stylesheet_directory_uri().'/images/default-logo.png'); 

/* Overwriting breadcrumb trail, TODO this will need to be removed once the Responsive child theme is updated */
function responsive_breadcrumb_lists()
{

   $responsive_options = get_option( 'responsive_theme_options' );

   if ( 1 == $responsive_options['breadcrumb'] )
   {
      return;
   }

   /* === OPTIONS === */
   $text['home']     = __( 'Home', 'responsive' ); // text for the 'Home' link
   $text['category'] = __( 'Archive for %s', 'responsive' ); // text for a category page
   $text['search']   = __( 'Search results for: %s', 'responsive' ); // text for a search results page
   $text['tag']      = __( 'Posts tagged %s', 'responsive' ); // text for a tag page
   $text['author']   = __( 'View all posts by %s', 'responsive' ); // text for an author page
   $text['404']      = __( 'Error 404', 'responsive' ); // text for the 404 page

   $show['current'] = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
   $show['home']    = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
   $show['search']  = 0; // 1 - show breadcrumbs on the search page, 0 - don't show

   $delimiter    = ' <span class="chevron">&#8250;</span> '; // delimiter between crumbs
        $before       = '<span class="breadcrumb-current">'; // tag before the current crumb
        $after        = '</span>'; // tag after the current crumb
        /* === END OF OPTIONS === */

        $home_link   = home_url( '/' );
        $before_link = '<span class="breadcrumb" typeof="v:Breadcrumb">';
        $after_link  = '</span>';
        $link_att    = ' rel="v:url" property="v:title"';
        $link        = $before_link . '<a' . $link_att . ' href="%1$s">%2$s</a>' . $after_link;

        $post        = get_queried_object();
        $parent_id   = isset( $post->post_parent ) ? $post->post_parent : '';

        $html_output = '';

        if( is_front_page() ) {
            if( 1 == $show['home'] ) {
                $html_output .= '<div class="breadcrumb-list"><a href="' . $home_link . '">' . $text['home'] . '</a></div>';
            }

        } else {
            $html_output .= '<div class="breadcrumb-list" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf( $link, $home_link, $text['home'] ) . $delimiter;

            if( is_home() ) {
                if( 1 == $show['current'] ) {
                    $html_output .= $before . get_the_title( get_option( 'page_for_posts', true ) ) . $after;
                }

            } elseif( is_category() ) {
                $this_cat = get_category( get_query_var( 'cat' ), false );
                if( 0 != $this_cat->parent ) {
                    $cats = get_category_parents( $this_cat->parent, true, $delimiter );
                    $cats = str_replace( '<a', $before_link . '<a' . $link_att, $cats );
                    $cats = str_replace( '</a>', '</a>' . $after_link, $cats );
                    $html_output .= $cats;
                }
                $html_output .= $before . sprintf( $text['category'], single_cat_title( '', false ) ) . $after;

            } elseif( is_search() ) {
                if( 1 == $show['search'] ) {
                    $html_output .= $before . sprintf( $text['search'], get_search_query() ) . $after;
                }

            } elseif( is_day() ) {
                $html_output .= sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
                $html_output .= sprintf( $link, get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ) ) . $delimiter;
                $html_output .= $before . get_the_time( 'd' ) . $after;

            } elseif( is_month() ) {
                $html_output .= sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
                $html_output .= $before . get_the_time( 'F' ) . $after;

            } elseif( is_year() ) {
                $html_output .= $before . get_the_time( 'Y' ) . $after;

            } elseif( is_single() && !is_attachment() ) {
                if( 'post' != get_post_type() ) {
                    $post_type    = get_post_type_object( get_post_type() );
                    $archive_link = get_post_type_archive_link( $post_type->name );
                    $html_output .= sprintf( $link, $archive_link, $post_type->labels->singular_name );
                    if( 1 == $show['current'] ) {
                        $html_output .= $delimiter . $before . get_the_title() . $after;
                    }
                } else {
                    $cat  = get_the_category();
                    $cat  = $cat[0];
                    $cats = get_category_parents( $cat, true, $delimiter );
                    if( 0 == $show['current'] ) {
                        $cats = preg_replace( "#^(.+)$delimiter$#", "$1", $cats );
                    }
                    $cats = str_replace( '<a', $before_link . '<a' . $link_att, $cats );
                    $cats = str_replace( '</a>', '</a>' . $after_link, $cats );
                    $html_output .= $cats;
                    if( 1 == $show['current'] ) {
                        $html_output .= $before . get_the_title() . $after;
                    }
                }

            } elseif( !is_single() && !is_page() && !is_404() && 'post' != get_post_type() ) {
                $post_type = get_post_type_object( get_post_type() );
                $html_output .= $before . $post_type->labels->singular_name . $after;

            } elseif( is_attachment() ) {
                $parent = get_post( $parent_id );
                $cat    = get_the_category( $parent->ID );

                if( isset( $cat[0] ) ) {
                    $cat = $cat[0];
                }

                if( $cat ) {
                    $cats = get_category_parents( $cat, true, $delimiter );
                    $cats = str_replace( '<a', $before_link . '<a' . $link_att, $cats );
                    $cats = str_replace( '</a>', '</a>' . $after_link, $cats );
                    $html_output .= $cats;
                }

                $html_output .= sprintf( $link, get_permalink( $parent ), $parent->post_title );
                if( 1 == $show['current'] ) {
                    $html_output .= $delimiter . $before . get_the_title() . $after;
                }

            } elseif( is_page() && !$parent_id ) {
                if( 1 == $show['current'] ) {
                    $html_output .=  $before . get_the_title() . $after;
                }

            } elseif( is_page() && $parent_id ) {
                $breadcrumbs = array();
                while( $parent_id ) {
                    $page_child    = get_page( $parent_id );
                    $breadcrumbs[] = sprintf( $link, get_permalink( $page_child->ID ), get_the_title( $page_child->ID ) );
                    $parent_id     = $page_child->post_parent;
                }
                $breadcrumbs = array_reverse( $breadcrumbs );
                for( $i = 0; $i < count( $breadcrumbs ); $i++ ) {
                    $html_output .= $breadcrumbs[$i];
                    if( $i != count( $breadcrumbs ) - 1 ) {
                        $html_output .= $delimiter;
                    }
                }
                if( 1 == $show['current'] ) {
                    $html_output .= $delimiter . $before . get_the_title() . $after;
                }

            } elseif( is_tag() ) {
                $html_output .= $before . sprintf( $text['tag'], single_tag_title( '', false ) ) . $after;

            } elseif( is_author() ) {
                $user_id = get_query_var( 'author' );
                $userdata = get_the_author_meta( 'display_name', $user_id );
                $html_output .= $before . sprintf( $text['author'], $userdata ) . $after;

            } elseif( is_404() ) {
                $html_output .= $before . $text['404'] . $after;

            }

            if( get_query_var( 'paged' ) || get_query_var( 'page' ) ) {
                $page_num = get_query_var( 'page' ) ? get_query_var( 'page' ) : get_query_var( 'paged' );
                $html_output .= $delimiter . sprintf( __( 'Page %s', 'responsive' ), $page_num );

            }

            $html_output .= '</div>';

        }

        echo $html_output;

} // end responsive_breadcrumb_lists

function UTbranding() 
{
    echo'<div id="UTbranding">
    <a href="http://www.utexas.edu">
        <img src="http://www.utexas.edu/opa/graphics/2dtower.jpg" alt="The University of Texas at Austin"   width="320" height="44">
    </a>
    </div>
    ';
}

add_filter('responsive_in_header','UTbranding');

function UTfooter_links()
{
    echo '<div class="grid col-940 clearfix fit centered">
                  <a href="http://www.utexas.edu/its/">Information Technology Services</a>
                  <br/>
                        <a href="http://www.utexas.edu/web-privacy-policy">Web Privacy Policy</a> &nbsp;|&nbsp; 
                        <a href="http://www.utexas.edu/web-accessibility-policy">Web Accessibility</a>
 <br/>
 <br/>
  </div>';
}

add_filter('responsive_footer','UTfooter_links');

// Using WP's i18n to stamp our name and link into the footer
// Idea came from here: http://blog.ftwr.co.uk/archives/2010/01/02/mangling-strings-for-fun-and-profit/
class UT_Text_Wrangler {
    function powered_by_text($translation, $text, $domain) {
        $translations = &get_translations_for_domain( $domain );
        if ( $text == 'Responsive Theme') {
            return $translations->translate( 'UT Austin Responsive Theme');
        } elseif ($text == 'http://themeid.com/responsive-theme/') {
            return $translations->translate('http://sites.utexas.edu/utresponsive/');
        }
        return $translation;
    }
}

add_filter('gettext', array('UT_Text_Wrangler', 'powered_by_text'), 10, 4);

// the functions responsive_template_data() and responsive_theme_data() add
// some <meta> tags with unregistered metadata keywords ("template" and
// "theme"), which fails HTML validation. So, let's remove them. Can't just
// remove_action(), because the parent theme calls add_action() after this
// file runs.  So we insert this function as an action in get_header
// (before wp_head) to remove those actions from wp_head.
function remove_meta_tags() {
    remove_action('wp_head', 'responsive_template_data');
    remove_action('wp_head', 'responsive_theme_data');
}

add_action("get_header","remove_meta_tags");

// Added an extra hook inside the #footer div
function responsive_footer() 
{
    do_action('responsive_footer');
}


?>
