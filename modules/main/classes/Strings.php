<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * class-intelliwidget-strings.php - arrays for UI output
 *
 * @package IntelliWidgetMain
 * @subpackage includes
 * @author Jason C Fleming
 * @copyright 2014-2015 Lilaea Media LLC
 * @access public
 */
class IntelliWidgetMainStrings {

    static function get_label( $key = '' ) {
        $value = FALSE;
        switch ( $key ):    
            case 'metabox_title': 
                $value = __( 'IntelliWidget Profiles', 'intelliwidget' ); 
                break;
            case 'cdf_title': 
                $value = __( 'IntelliWidget Override Fields', 'intelliwidget' ); 
                break;
            case 'hide_if_empty': 
                $value = __( 'Placeholder (Use only Child Profiles)', 'intelliwidget' ); 
                break;
            case 'generalsettings': 
                $value = __( 'Container Settings', 'intelliwidget' ); 
                break;
            case 'content': 
                $value = __( 'IntelliWidget Type', 'intelliwidget' ); 
                break;
            case 'title': 
                $value = __( 'Section Title (Optional)', 'intelliwidget' ); 
                break;
            case 'hide_title': 
                $value = __( 'Do not display', 'intelliwidget' ); 
                break;
            case 'container_id': 
                $value = __( 'Widget ID Attribute', 'intelliwidget' ); 
                break;
            case 'classes': 
                $value = __( 'Widget Classes', 'intelliwidget' ); 
                break;
            case 'addltext': 
                $value = __( 'Text/HTML', 'intelliwidget' ); 
                break;
            case 'text_position': 
                $value = __( 'Display', 'intelliwidget' ); 
                break;
            case 'filter': 
                $value = __( 'Automatically add paragraphs', 'intelliwidget' ); 
                break;
            case 'appearance': 
                $value = __( 'Legacy Template Settings', 'intelliwidget' ); 
                break;
            case 'template': 
                $value = __( 'Template', 'intelliwidget' ); 
                break;
            case 'sortby': 
                $value = __( 'Sort posts by', 'intelliwidget' ); 
                break;
            case 'items': 
                $value = __( 'Max posts', 'intelliwidget' ); 
                break;
            case 'length': 
                $value = __( 'Max words per post', 'intelliwidget' ); 
                break;
            case 'allowed_tags': 
                $value = __( 'Allowed HTML Tags', 'intelliwidget' ) . '<br/>' . __( '(p, br, em, strong, etc.)', 'intelliwidget' ); 
                break;
            case 'link_text': 
                $value = __( '"Read More" Text', 'intelliwidget' ); 
                break;
            case 'imagealign': 
                $value = __( 'Image Align', 'intelliwidget' ); 
                break;
            case 'image_size': 
                $value = __( 'Image Size', 'intelliwidget' ); 
                break;
            case 'selection': 
                $value = __( 'Query Settings', 'intelliwidget' ); 
                break;
            case 'post_types': 
                $value = __( 'Select from these Post Types', 'intelliwidget' ); 
                break;
            case 'terms': 
                $value = __( 'Show posts related to', 'intelliwidget' ); 
                break;
            case 'page': 
                $value = __( 'Show specific posts', 'intelliwidget' ); 
                break;
            case 'gallery': 
                $value = __( 'Specific Media Files', 'intelliwidget' ); 
                break;
            case 'skip_post': 
                $value = __( 'Exclude current post', 'intelliwidget' ); 
                break;
            case 'future_only': 
                $value = __( 'Include only future posts', 'intelliwidget' ); 
                break;
            case 'active_only': 
                $value = __( 'Exclude future posts', 'intelliwidget' ); 
                break;
            case 'skip_expired': 
                $value = __( 'Exclude expired posts', 'intelliwidget' ); 
                break;
            case 'include_private': 
                $value = __( 'Include private posts', 'intelliwidget' ); 
                break;
            case 'nav_menu': 
                $value = __( 'Menu to display', 'intelliwidget' ); 
                break;
            case 'widget_page_id': 
                $value = __( 'Use Profiles from', 'intelliwidget' ); 
                break;
            case 'iw_add': 
                $value = __( '+ Add New Profile', 'intelliwidget' ); 
                break;
            case 'event_date': 
                $value = __( 'Start Date', 'intelliwidget' ); 
                break;
            case 'expire_date': 
                $value = __( 'Expire Date', 'intelliwidget' ); 
                break;
            case 'alt_title': 
                $value = __( 'Alt Title', 'intelliwidget' ); 
                break;
            case 'external_url': 
                $value = __( 'External URL', 'intelliwidget' ); 
                break;
            case 'link_classes': 
                $value = __( 'Link Classes', 'intelliwidget' ); 
                break;
            case 'link_target': 
                $value = __( 'Link Target', 'intelliwidget' ); 
                break;
            case 'replace_widget': 
                $value = __( 'Parent Profile to replace', 'intelliwidget' ); 
                break;
            case 'nocopy': 
                $value = __( 'Override Profile with same nickname', 'intelliwidget' ); 
                break;
            case 'navmenusettings': 
                $value = __( 'Nav Menu Settings', 'intelliwidget' ); 
                break;
            case 'taxmenusettings': 
                $value = __( 'Taxonomy Menu Settings', 'intelliwidget' ); 
                break;
            case 'gallerysettings': 
                $value = __( 'Gallery Settings', 'intelliwidget' ); 
                break;
            case 'taxonomy': 
                $value = __( 'Taxonomy', 'intelliwidget' ); 
                break;
            case 'hide_empty': 
                $value = __( 'Hide terms without posts', 'intelliwidget' ); 
                break;
            case 'current_only_all': 
                $value = __( 'Show all terms and child terms', 'intelliwidget' ); 
                break;
            case 'current_only_cur': 
                $value = __( 'Only show hierarchy for current term', 'intelliwidget' ); 
                break;
            case 'current_only_sub': 
                $value = __( 'Only show children of current term', 'intelliwidget' ); 
                break;
            case 'hierarchical': 
                $value = __( 'Show in hierarchical order', 'intelliwidget' ); 
                break;
            case 'show_count': 
                $value = __( 'Show post count', 'intelliwidget' ); 
                break;
            case 'show_descr': 
                $value = __( 'Show term description', 'intelliwidget' ); 
                break;
            case 'sortby_terms': 
                $value = __( 'Sort terms by', 'intelliwidget' ); 
                break;
            case 'allterms': 
                $value = __( 'of these terms', 'intelliwidget' ); 
                break;
            case 'menu_location':
                $value = __( 'Menu Location', 'intelliwidget' ); 
                break;
            case 'same_term':
                $value = __( 'Show posts for current term', 'intelliwidget' ); 
                break;
            case 'child_pages':
                $value = __( 'Show child pages of current page', 'intelliwidget' ); 
                break;
            case 'hide_no_posts':
                $value = __( 'Content displayed if there are no results', 'intelliwidget' ); 
                break;
            case 'no_img_links':
                $value = __( 'Do not link images', 'intelliwidget' ); 
                break;
            case 'keep_title':
                $value = __( 'Do not use alt title', 'intelliwidget' ); 
                break;
            case 'daily': // IW Pro only
                $value = __( 'Change Daily', 'intelliwidget' ); 
                break;
            case 'all_titles':
                $value = __( 'Apply to archives', 'intelliwidget' ); 
                break;
            case 'all_links':
                $value = __( 'Apply to archives', 'intelliwidget' );
                break;
            case 'menu_order':
                $value = __( 'Order', 'intelliwidget' );
                break;
            case 'tab_label':
                $value = __( 'Tab Label', 'intelliwidget' );
                break;
            case 'gallerybtn':
                $value = __( 'Edit Media', 'intelliwidget' );
                break;
            case 'listdatabtn':
                $value = __( 'Edit Menu', 'intelliwidget' );
                break;
            case 'clearlistdatabtn':
                $value = __( '', 'intelliwidget' );
                break;
            case 'listdata':
                $value = '';
                break;
            case 'link':
                $value = __( 'Link To', 'intelliwidget' );
                break;
            case 'tag':
                $value = __( 'Gallery Elements', 'intelliwidget' );
                break;
            case 'gclass':
                $value = __( 'Gallery Classes', 'intelliwidget' );
                break;
            case 'iclass':
                $value = __( 'Gallery Item Classes', 'intelliwidget' );
                break;
            case 'captions':
                $value = __( 'Show Captions', 'intelliwidget' );
                break;
            case 'metak':
            case 'metak1':
            case 'metak2':
            case 'metak3':
                $value = __( 'Meta Key', 'intelliwidget' ); 
                break;
            case 'metac':
            case 'metac1':
            case 'metac2':
            case 'metac3':
                $value = __( 'Condition', 'intelliwidget' ); 
                break;
            case 'metav':
            case 'metav1':
            case 'metav2':
            case 'metav3':
                $value = __( 'Value', 'intelliwidget' ); 
                break;        
            case 'nickname':
                $value = __( 'Profile Nickname', 'intelliwidget' ); 
                break;        
            case 'related':
                $value = __( 'Related to Current Post', 'intelliwidget' ); 
                break;        
            case 'site_id':
                $value = __( 'Search Network Site', 'intelliwidget' ); 
                break;        
            case 'restrict':
                $value = __( 'Restrict Child Profiles', 'intelliwidget' ); 
                break;        
            case 'nav_menu_classes':
                $value = __( 'Menu Classes', 'intelliwidget' ); 
                break;        
            case 'paged':
                $value = __( 'Use Pagination', 'intelliwidget' ); 
                break;        
        endswitch;
        return apply_filters( 'intelliwidget_labels', $value, $key );
    }
    
    static function get_tip( $key = '' ) {
        $value = FALSE;
        switch ( $key ):
            case 'hide_if_empty': 
                $value = __( 'Check this box to restrict this IntelliWidget to pages/posts with custom settings. If the page or post being viewed has not been configured with its own Intelliwidget settings, this profile will be hidden.', 'intelliwidget' ); 
                break;
            case 'generalsettings': 
                $value = __( 'These settings apply to all IntelliWidgets, including the type of IntelliWidget, the Section Title, HTML container id and CSS classes.', 'intelliwidget' ); 
                break;
            case 'content': 
                $value = __( 'This menu controls the type of IntelliWidget to display and the other settings available. If you are using IntelliWidget extensions, they will appear as options here as well.', 'intelliwidget' ); 
                break;
            case 'title': 
                $value = __( 'Enter a title here if you want a heading above this IntelliWidget profile, otherwise, leave it blank.', 'intelliwidget' ); 
                break;
            case 'hide_title': 
                $value = __( 'Check this box to hide the title from widget output. This allows you to use the title to identify the widget in the Admin without affecting the presentation.', 'intelliwidget' ); 
                break;
            case 'container_id': 
                $value = __( 'Enter a unique value if you wish to customize the IntelliWidget container id attribute.', 'intelliwidget' ); 
                break;
            case 'classes': $value = __( "Enter CSS class names for the IntelliWidget container.", 'intelliwidget' );
                break;
            case 'addltext': 
                $value = __( 'These settings allow you to add additional text to display above or below the IntelliWidget output. If your theme supports shortcodes in text widgets, you can use them here. If your user account has HTML editing capabilities, you can enter HTML as well.', 'intelliwidget' ); 
                break;
            case 'text_position': 
                $value = __( 'This menu controls the position of the additional text. You can also choose to display only the text, skipping the post selection entirely.', 'intelliwidget' ); 
                break;
            case 'filter': 
                $value = __( 'Check this box to insert paragraph breaks wherever blank lines appear in the text you enter.', 'intelliwidget' ); 
                break;
            case 'appearance': 
                $value = __( 'Control the number of posts displayed, excerpt length, featured image and other settings.', 'intelliwidget' ); 
                break;
            case 'template': 
                $value = __( 'This menu controls the IntelliWidget template used to display the output. If you are using custom templates, they will appear here as well.', 'intelliwidget' ); 
                break;
            case 'sortby': 
                $value = __( 'This menu controls the post attribute used to sort the posts that are selected. Select ascending or descending order with the second menu (does not apply to random). Start Date is set for each post with IntelliWidget Custom Fields (see).', 'intelliwidget' ); 
                break;
            case 'items': 
                $value = __( 'This setting controls the number of posts that are selected to appear in this IntelliWidget profile.', 'intelliwidget' ); 
                break;
            case 'length': 
                $value = __( 'This setting controls the number of words to display for each post selected to appear in this IntelliWidget profile.', 'intelliwidget' ); 
                break;
            case 'allowed_tags': 
                $value = __( 'By default, HTML is stripped from the post content. Enter any HTML tags that you do not not wish to remove. Do not include &gt; or &lt; characters.', 'intelliwidget' ); 
                break;
            case 'link_text': 
                $value = __( 'Enter a value if you wish to customize the text that appears in the link to each post.', 'intelliwidget' ); 
                break;
            case 'imagealign': 
                $value = __( 'If you are using a Template that includes the featured image, this menu controls how it is aligned relative to the post content.', 'intelliwidget' ); 
                break;
            case 'image_size': 
                $value = __( 'If you are using a Template that includes the featured image, this menu controls the display size of the image.', 'intelliwidget' ); 
                break;
            case 'selection': 
                $value = __( 'These settings control the template used and the posts that are displayed. Select post type, taxonomy terms and date conditions. You can also restrict selection to specific posts.', 'intelliwidget' ); 
                break;
            case 'post_types': 
                $value = __( 'These checkboxes restrict the selection to specific Post Types, post and page by default. At least one must be checked.', 'intelliwidget' ); 
                break;
            case 'terms': 
                $value = __( 'Restrict the output to specific categories, tags or other taxonomies by selecting them from the menu below. Only taxonomies related to the selected post types will appear here as options.', 'intelliwidget' ); 
                break;
            case 'page': 
                $value = __( 'Restrict the output to specific posts by selecting them from the menu below. Only posts of the types selected above will appear as options. The specific posts must also meet any other selection you choose here.', 'intelliwidget' ); 
                break;
            case 'gallery': 
                $value = __( 'Restrict the gallery to specific media files by clicking the button and selecting them from Media Gallery.', 'intelliwidget' ); 
                break;
            case 'skip_post': 
                $value = __( 'Check this box if you wish to exclude the post currently being viewed in the main content from the selection list.', 'intelliwidget' ); 
                break;
            case 'future_only': 
                $value = __( 'Check this box if you wish to restrict the selection list to posts with a future start date. Start dates are set for each individual post using IntelliWidget Custom Fields (see).', 'intelliwidget' ); 
                break;
            case 'active_only': 
                $value = __( 'Check this box if you wish to exclude posts with a future start date from the selection list. Start dates are set for each individual post using IntelliWidget Custom Fields (see).', 'intelliwidget' ); 
                break;
            case 'skip_expired': 
                $value = __( 'Check this box if you wish to exclude posts with a past expire date from the selection list. Expire dates are set for each individual post using IntelliWidget Custom Fields (see).', 'intelliwidget' ); 
                break;
            case 'include_private': 
                $value = __( 'Check this box if you wish to show privately published posts. Links will only be visible to logged in users that can read private content.', 'intelliwidget' ); 
                break;
            case 'nav_menu': $value = __( "This menu controls the Navigation Menu to be displayed in this IntelliWidget profile. To show all pages ( including a home page), use the 'Automatic Page Menu' option. Nav Menus are customized from Appearance > Menus in the WordPress admin.", 'intelliwidget' );
                break;
            case 'widget_page_id': $value = __( "Instead entering new settings below, you can reuse all the settings from another IntelliWidget Profile by selecting it from this menu.", 'intelliwidget' );
                break;
            case 'iw_add': 
                $value = __( 'Click to add a new IntelliWidget profile tab.', 'intelliwidget' ); 
                break;
            case 'event_date': $value = __( "This value represents the post's starting date. It is used in date-based templates, and to include or exclude posts by date in the 'Post Selection Settings.'", 'intelliwidget' );
                break;
            case 'expire_date': $value = __( "This value represents the post's ending date. It is used in date-based templates, and to exclude posts that have expired in the 'Post Selection Settings.'", 'intelliwidget' );
                break;
            case 'alt_title': $value = __( "Enter the value to be used as the title for the post in the IntelliWidget output. If no value is entered, the entire post title will be used.", 'intelliwidget' );
                break;
            case 'external_url': $value = __( "Enter an external URL if you wish for the title to link somewhere other than the post.", 'intelliwidget' );
                break;
            case 'link_classes': $value = __( "Enter additional CSS class names if you wish to customize the title's link styles. This is often used for menu icons.", 'intelliwidget' );
                break;
            case 'link_target': $value = __( "Select a target attribute if you wish for the title link to open in a new window or tab.", 'intelliwidget' );
                break;
            case 'replace_widget': $value = __( "This menu determines the IntelliWidget instance to replace with these settings. Options are labeled by Sidebar Name followed by the nth IntelliWidget in that sidebar. Even if there are other Widgets in the Sidebar, the number represents only the IntelliWidgets in the Sidebar. If you reorder the Widgets in the Sidebar, the number will reflect the change. To use these settings for a shortcode on the post, select 'Shortcode' and use the format [intelliwidget profile=tab#], where 'tab#' corresponds to the number of the tab (above) containing the settings you wish to use.", 'intelliwidget' );
                break;
            case 'nocopy': $value = __( "Check this box to keep these settings even when using another profile from the menu above.", 'intelliwidget' );
                break;
            case 'navmenusettings': 
                $value = __( 'These settings control the behavior of the Nav Menu for this Profile.', 'intelliwidget' ); 
                break;
            case 'gallerysettings': 
                $value = __( 'These settings control the behavior of the Gallery for this Profile.', 'intelliwidget' ); 
                break;
            case 'taxmenusettings': 
                $value = __( 'These settings control the behavior of the Taxonomy Menu for this Profile.', 'intelliwidget' ); 
                break;
            case 'taxonomy': 
                $value = __( 'Select the taxonomy to be displayed in the menu.', 'intelliwidget' ); 
                break;
            case 'hide_empty': 
                $value = __( 'Exclude terms that have no posts assigned to them.', 'intelliwidget' ); 
                break;
            case 'current_only_all': 
                $value = __( 'Show the child terms (sub-categories) of all terms.', 'intelliwidget' ); 
                break;
            case 'current_only_cur': 
                $value = __( 'Show only the parent and child terms (sub-categories) of the current term. Show only the highest level of other terms.', 'intelliwidget' ); 
                break;
            case 'current_only_sub': 
                $value = __( 'Show only the child terms (sub-categories) of the current term.', 'intelliwidget' ); 
                break;
            case 'hierarchical': 
                $value = __( 'Display the menu organized by parent-child relationships.', 'intelliwidget' ); 
                break;
            case 'show_count': 
                $value = __( 'Display the number of posts assigned to each term.', 'intelliwidget' ); 
                break;
            case 'show_descr': 
                $value = __( 'Display the term description if it exists.', 'intelliwidget' ); 
                break;
            case 'sortby_terms': 
                $value = __( 'This menu controls whether sort by Term Label or by the order configured on the Edit Taxonomy admin.', 'intelliwidget' ); 
                break;
            case 'allterms': 
                $value = __( 'Select whether the post must be a member of all the selected terms or just one of them. Select "none" to exclude posts from all selected terms.', 'intelliwidget' ); 
                break;
            case 'menu_location':
                $value = __( 'If replacing a registered Nav Menu Location, select the location to replace.', 'intelliwidget' ); 
                break;
            case 'same_term':
                $value = __( 'Automatically display posts related to current term.', 'intelliwidget' ); 
                break;
            case 'child_pages':
                $value = __( 'Automatically display next tier of sub-pages for the current page.', 'intelliwidget' ); 
                break;
            case 'hide_no_posts':
                $value = __( 'If no posts match the criteria, display this content.', 'intelliwidget' ); 
                break;
            case 'no_img_links':
                $value = __( 'Just show the thumbnail without a link to the post.', 'intelliwidget' ); 
                break;
            case 'keep_title':
                $value = __( 'Use the actual post title even if a alternate title exists.', 'intelliwidget' ); 
                break;
            case 'daily': // IW Pro only
                $value = __( 'Display a different post each day.', 'intelliwidget' ); 
                break;
            case 'all_links':
                $value = __( 'Apply this setting whenever this post link is displayed.', 'intelliwidget' ); 
                break;
            case 'all_titles':
                $value = __( 'Apply this setting whenever this post title is displayed.', 'intelliwidget' ); 
                break;
            case 'menu_order':
                $value = __( 'Value used for sorting posts. For pages and other post types, this value is set in the Page Attributes Meta Box.', 'intelliwidget' );
                break;
            case 'tab_label':
                $value = __( 'Label of child profile tab.', 'intelliwidget' );
                break;
            case 'gallerybtn':
                $value = __( 'Click to open the Media Library Gallery Editor', 'intelliwidget' );
                break;
            case 'listdatabtn':
                $value = __( 'Add custom links, sort items, apply CSS classes and more.', 'intelliwidget' );
                break;
            case 'clearlistdatabtn':
                $value = __( 'Clear manual menu data', 'intelliwidget' );
                break;
            case 'listdata':
                $value = '';
                break;
            case 'link':
                $value = __( 'Select the destination when clicking on an item in the Gallery.', 'intelliwidget' );
                break;
            case 'tag':
                $value = __( 'Select the tags used for this gallery', 'intelliwidget' );
                break;
            case 'gclass':
                $value = __( 'CSS class names for the Gallery outer element.', 'intelliwidget' );
                break;
            case 'iclass':
                $value = __( 'CSS class names for the Gallery inner (item) elements.', 'intelliwidget' );
                break;
            case 'captions':
                $value = __( 'Show caption below Gallery icon.', 'intelliwidget' );
                break;
            case 'nickname':
                $value = __( 'ID for this profile.', 'intelliwidget' );
                break;
            case 'related': 
                $value = __( 'Check this box if you only wish to show posts with a post type relationship to the current post.', 'intelliwidget' ); 
                break;
            case 'site_id': 
                $value = __( "Network Site to search for results. Use 'all' to search all sites.", 'intelliwidget' ); 
                break;
            case 'restrict': 
                $value = __( "Display limited input options and use this profile as defaults for Child Profiles.", 'intelliwidget' ); 
                break;
            case 'nav_menu_classes':
                $value = __( 'Classes to be added to rendered nav menu ul element', 'intelliwidget' ); 
                break;
            case 'paged':
                $value = __( 'Display posts across multiple pages with navigation.', 'intelliwidget' ); 
                break;        
        endswitch;
        return apply_filters( 'intelliwidget_tips', $value, $key );
    }
    
    static function get_menu( $key = '' ) {
        $value = FALSE;
        switch ( $key ):
            case 'content': 
                $value = array(
                    'post_list' => __( 'Posts (default)', 'intelliwidget' ),
                    'nav_menu'  => __( 'Nav Menu', 'intelliwidget' ),
                    'tax_menu'  => __( 'Taxonomy Menu', 'intelliwidget' ),
                    'gallery'   => __( 'Gallery', 'intelliwidget' ),
                );
                break;
            case 'replaces': 
                $value = array(
                    'none'      => __( 'Unassigned', 'intelliwidget' ),
                );
                break;
            case 'text_position': 
                $value = array(
                    ''          => __( 'None', 'intelliwidget' ),
                    'above'     => __( 'Above Posts', 'intelliwidget' ),
                    'below'     => __( 'Below Posts', 'intelliwidget' ),
                    'only'      => __( 'This text only (no posts)', 'intelliwidget' ),
                );
                break;
            case 'sortby': 
                $value = array(
                    'selection' => __( 'Gallery Order', 'intelliwidget' ),
                    'menu_order'=> __( 'Menu Order', 'intelliwidget' ),
                    'title'     => __( 'Title', 'intelliwidget' ),
                    'date'      => __( 'Post Date', 'intelliwidget' ),
                    'event_date'=> __( 'Event Date', 'intelliwidget' ),
                    'rand'      => __( 'Random', 'intelliwidget' ),
                );
                break;
            case 'image_size':
                global $_wp_additional_image_sizes;
                $value = array(
                    'none'      => __( 'No Image', 'intelliwidget' ),
                    'thumbnail' => __( 'Thumbnail', 'intelliwidget' ),
                    'medium'    => __( 'Medium', 'intelliwidget' ),
                    'large'     => __( 'Large', 'intelliwidget' ),
                    'full'      => __( 'Full', 'intelliwidget' ),
                );
                if ( is_array( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) )
                    foreach ( $_wp_additional_image_sizes as $name => $size )
                        $value[ $name ] = ucwords( str_replace( '-', ' ', $name ) ) . ' ( ' . $size[ 'width' ] . 'px )';
                break;
            case 'imagealign': 
                $value = array(
                    'none'      => __( 'Auto', 'intelliwidget' ),
                    'left'      => __( 'Left', 'intelliwidget' ),
                    'center'    => __( 'Center', 'intelliwidget' ),
                    'right'     => __( 'Right', 'intelliwidget' ),
                );
                break;
            case 'link_target': 
                $value = array(
                    ''          => __( 'None', 'intelliwidget' ),
                    '_new'      => '_new',
                    '_blank'    => '_blank',
                    '_self'     => '_self',
                    '_top'      => '_top',
                );
                break;
            case 'tax_sortby': 
                $value = array(
                    'menu_order'=> __( 'Menu Order', 'intelliwidget' ),
                    'title'     => __( 'Title',      'intelliwidget' ),
                );
                break;
                
            case 'default_nav': 
                $value = array(
                    ''          => __( 'None', 'intelliwidget' ),
                    '-1'        => __( 'Automatic Page Menu', 'intelliwidget' ),
                ); 
                break;
            case 'link':
                $value = array(
                    'file'  => 'Media File',
                    'tb'    => 'Thickbox Modal',
                    'pp'    => 'PrettyPhoto Modal',
                    'post'  => 'Attachment Page',
                    'none'  => 'None',
                );
                break;
            case 'tag':
                $value = array(
                    'list'    => 'List',
                    'data'    => 'Data',
                    'html5'   => 'HTML5',
                );
                break;
            case 'meta_cond':
                $value = array(
                    'eq' => __( 'Equals', 'intelliwidget' ),
                    'ne' => __( 'Not Equal To', 'intelliwidget' ),
                    'gt' => __( 'Greater Than (string)', 'intelliwidget' ),
                    'lt' => __( 'Less Than (string)', 'intelliwidget' ),
                    'gn' => __( 'Greater Than (number)', 'intelliwidget' ),
                    'ln' => __( 'Less Than (number)', 'intelliwidget' ),
                    'nl' => __( 'Does Not Exist', 'intelliwidget' ),
                    'nn' => __( 'Exists', 'intelliwidget' ),
                    'in' => __( 'In', 'intelliwidget' ),
                    'ni' => __( 'Not In', 'intelliwidget' ),
                );
                break;
            case 'modules':
                $value = array(
                    'types'     => 'Post Types',
                    'taxes'     => 'Taxonomies',
                    'relmedia'  => 'Related Media',
                    'iwtemplates' => 'Theme Templates',
                    'layout'    => 'Widget Templates',
                    'condset'   => 'Conditional Profiles',
                );
                break;
        endswitch;
        return apply_filters( 'intelliwidget_menus_' . $key, $value );
    }
    
    static function get_fields( $key = '' ) {
        $value = FALSE;
        switch ( $key ):
            case 'checkbox': 
                $value = array(
                    //'skip_expired', 
                    'skip_post', 
                    'hide_if_empty', 
                    'filter', 
                    //'future_only', 
                    //'active_only', 
                    'include_private',
                    'nocopy',
                    'show_count',
                    'hide_empty',
                    'hierarchical',
                    'show_descr',
                    'hide_title',
                    'same_term',
                    'child_pages',
                    'no_img_links',
                    'keep_title',
                    'daily',
                    'all_links',
                    'all_titles',
                    'captions',
                    'related',
                    'restrict',
                    'paged',
                ); 
                break;
            case 'text': 
                $value = array(
                    'custom_text', 
                    'title', 
                    'link_text',
                    'hide_no_posts',
                ); 
                break;
            case 'custom': 
                $value = array(
                    //'event_date',
                    //'expire_date',
                    'alt_title',
                    'external_url',
                    'link_classes',
                    'link_target',
                    'all_titles',
                    'all_links',
                    // menu order is a post field, not a postmeta field, so it is not included here.
                );
                break;
        endswitch;
        return apply_filters( 'intelliwidget_fields', $value, $key );
    }

    static function get_default_styles() {
        return apply_filters( 'iwtemplates_default_styles', array(
            'l' => array(
                'label'     => __( 'Float Left', 'iwtemplates' ),
                'name'      => 'float-left',
                'default'   => 0,
            ),
            'r' => array(
                'label'     => __( 'Float Right', 'intelliwidget' ),
                'name'      => 'float-right',
                'default'   => 0,
            ),
            'row' => array(
                'label'     => __( 'Row', 'intelliwidget' ),
                'name'      => 'row',
                'show'      => 1,
                'default'   => '0',
            ),
            'c3' => array(
                'label'     => __( '1/4', 'intelliwidget' ),
                'name'      => 'col-md-3',
                'show'      => 1,
                'default'   => '0',
            ),
            'c4' => array(
                'label'     => __( '1/3', 'intelliwidget' ),
                'name'      => 'col-md-4',
                'show'      => 1,
                'default'   => '0',
            ),
            'c6' => array(
                'label'     => __( '1/2', 'intelliwidget' ),
                'name'      => 'col-md-6',
                'show'      => 1,
                'default'   => '0',
            ),
            'c8' => array(
                'label'     => __( '2/3', 'intelliwidget' ),
                'name'      => 'col-md-8',
                'show'      => 1,
                'default'   => '0',
            ),
            'c9' => array(
                'label'     => __( '3/4', 'intelliwidget' ),
                'name'      => 'col-md-9',
                'show'      => 1,
                'default'   => '0',
            ),
            'fr' => array(
                'label'     => __( 'Flex Row', 'intelliwidget' ),
                'name'      => 'd-flex',
                'default'   => '0',
            ),
            'fc' => array(
                'label'     => __( 'Flex Column', 'intelliwidget' ),
                'name'      => 'flex-column d-flex',
                'default'   => '0',
            ),
            'c' => array(
                'label'     => __( 'Container', 'intelliwidget' ),
                'name'      => 'container',
                'default'   => 0,
            ),
            'cf' => array(
                'label'     => __( 'Container-Fluid', 'intelliwidget' ),
                'name'      => 'container-fluid',
                'default'   => 0,
            ),
            'w' => array(
                'label'     => __( 'Wrap', 'intelliwidget' ),
                'name'      => 'flex-wrap',
                'default'   => 0,
            ),
            'fw' => array(
                'label'     => __( 'Wrap', 'intelliwidget' ),
                'name'      => 'flex-wrap',
                'default'   => 0,
            ),
            'f' => array(
                'label'     => __( 'Flex', 'intelliwidget' ),
                'name'      => 'flex-fill',
                'default'   => 0,
            ),
            'h' => array(
                'label'     => __( 'Center Wide', 'intelliwidget' ),
                'name'      => 'center-page',
                'show'      => 1,
                'default'   => 1,
            ),
            'hn' => array(
                'label'     => __( 'Center Narrow', 'intelliwidget' ),
                'name'      => 'center-page-narrow',
                'default'   => 0,
            ),
            'p' => array(
                'label'     => __( 'Pad L/R', 'intelliwidget' ),
                'name'      => 'px-3 px-md-4',
                'show'      => 1,
                'default'   => 1,
            ),
            'x' => array(
                'label'     => __( 'Clearfix', 'intelliwidget' ),
                'name'      => 'clearfix',
                'show'      => 1,
                'default'   => 1,
            ),
            'n' => array(
                'label'     => __( 'No Gutters', 'intelliwidget' ),
                'name'      => 'no-gutters',
                'show'      => 1,
                'default'   => 0,
            ),
            'a' => array(
                'label'     => __( 'Classes', 'intelliwidget' ),
                'name'      => 'additional',
                'default'   => '',
            ),
            'o' => array(
                'label'     => __( 'Outer Classes', 'intelliwidget' ),
                'name'      => 'outer',
                'default'   => '',
            ),
            'id' => array( 
                'label'     => __( 'ID', 'intelliwidget' ),
                'name'      => 'domid',
                'default'   => '',
            ),
            'img' => array(
                'label'     => __( 'Image', 'intelliwidget' ),
                'name'      => 'image',
                'default'   => '',
            ),
            'i' => array(
                'label'     => __( 'Image', 'intelliwidget' ),
                'name'      => 'image',
                'default'   => '',
            ),
            't' => array(
                'label'     => __( 'Tag', 'intelliwidget' ),
                'name'      => 'tag',
                'default'   => 'div',
            ),
            'tag' => array(
                'label'     => __( 'Tag', 'intelliwidget' ),
                'name'      => 'tag',
                'default'   => 'div',
            ),
            'link' => array(
                'label'     => __( 'Link', 'intelliwidget' ),
                'name'      => 'link',
                'default'   => '',
            ),
            's' => array(
                'label'     => __( 'Style', 'intelliwidget' ),
                'name'      => 'style',
                'default'   => '',
            ),
            'st' => array(
                'label'     => __( 'Style', 'intelliwidget' ),
                'name'      => 'style',
                'default'   => '',
            ),
            'tc' => array(
                'label'     => __( 'Center Text', 'intelliwidget' ),
                'name'      => 'text-center',
                'show'      => 1,
                'default'   => '0',
            ),
            'tr' => array(
                'label'     => __( 'Right Text', 'intelliwidget' ),
                'name'      => 'text-right',
                'default'   => '0',
            ),
            'v' => array(
                'label'     => __( 'Center Vertical', 'intelliwidget' ),
                'name'      => 'center-vertical',
                'show'      => 1,
                'default'   => '0',
            ),

        ) ); 
    }

    static function get_style( $id ) {
        $styles = self::get_default_styles();
        return array_key_exists( $id, $styles ) ? $styles[ $id ][ 'name' ] : FALSE;
    }
    
    static function get_row_classes(){
        $array = array();
        foreach ( self::get_default_styles() as $id => $stylearr )
            if ( !empty( $stylearr[ 'show' ] ) )
                $array[ $id ] = $stylearr[ 'label' ];
        return $array;
    }

}
