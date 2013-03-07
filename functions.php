<?php
/////////////
// $Id: functions.php 2 2012-10-06 04:40:39Z lja284 $
// $URL: https://utforge.its.utexas.edu/repos/wp-ut-responsive/trunk/utresponsive/functions.php $
/////////////

// Custom Header -- make ours slightly different than the Responsive parent theme:
// Note: the parent theme has code that users custom-header and get_custom_header .. which is WP 3.4+
// We'll need to update our override code here when we upgrade our server.
define('HEADER_IMAGE_WIDTH', 960); 
define('HEADER_IMAGE_HEIGHT', 320);
define('HEADER_IMAGE', '%s/../responsive-child-theme/images/default-logo.png'); // %s is the template dir uri

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
                        <a href="http://www.utexas.edu/policies/privacy/">Web Privacy Policy</a> &nbsp;|&nbsp; 
                        <a href="http://www.utexas.edu/brand-guidelines/web-guidelines/accessibility">Web Accessibility</a>
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
            return $translations->translate('http://blogs.utexas.edu/utresponsive/');
        }
        return $translation;
    }
}

add_filter('gettext', array('UT_Text_Wrangler', 'powered_by_text'), 10, 4);

?>
