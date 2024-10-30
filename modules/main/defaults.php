<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * First time run - create default objects
 */
$tmp = 'O:24:"IntelliWidgetMainTemplate":5:{s:2:"id";s:4:"tmp1";s:5:"items";a:2:{s:4:"ctr3";O:25:"IntelliWidgetMainContainer":5:{s:2:"id";s:4:"ctr3";s:5:"items";a:0:{}s:7:"options";a:18:{s:4:"name";s:9:"Container";s:4:"part";s:9:"container";s:3:"src";s:0:"";s:6:"render";s:0:"";s:6:"usetag";s:1:"1";s:3:"tag";s:3:"div";s:8:"tagclass";s:0:"";s:8:"useinner";s:1:"1";s:5:"inner";s:3:"div";s:5:"ratio";s:2:"sq";s:10:"innerclass";s:0:"";s:6:"useimg";s:1:"1";s:6:"source";s:14:"featured_image";s:9:"fieldname";s:0:"";s:7:"uselink";s:1:"1";s:9:"linkclass";s:0:"";s:6:"depimg";s:1:"1";s:5:"usebg";s:0:"";}s:3:"css";O:19:"IntelliWidgetMainCss":5:{s:2:"id";s:8:"css_ctr3";s:5:"items";a:0:{}s:7:"options";a:10:{s:1:"l";s:1:"1";s:1:"r";s:0:"";s:1:"c";s:0:"";s:1:"f";s:1:"1";s:1:"e";s:0:"";s:1:"u";s:0:"";s:1:"b";s:1:"1";s:1:"t";s:1:"5";s:1:"w";s:3:"1-3";s:1:"s";s:0:"";}s:3:"css";N;s:8:"required";b:1;}s:8:"required";b:0;}s:4:"ctr1";O:25:"IntelliWidgetMainContainer":5:{s:2:"id";s:4:"ctr1";s:5:"items";a:2:{s:4:"prt1";O:20:"IntelliWidgetPart":5:{s:2:"id";s:4:"prt1";s:5:"items";a:0:{}s:7:"options";a:9:{s:4:"name";s:5:"Title";s:4:"part";s:9:"posttitle";s:3:"src";s:12:"render_title";s:6:"render";s:8:"function";s:7:"uselink";s:1:"1";s:9:"linkclass";s:0:"";s:6:"usetag";s:1:"1";s:3:"tag";s:2:"h3";s:8:"tagclass";s:19:"intelliwidget-title";}s:3:"css";N;s:8:"required";b:0;}s:4:"prt2";O:20:"IntelliWidgetPart":5:{s:2:"id";s:4:"prt2";s:5:"items";a:0:{}s:7:"options";a:15:{s:4:"name";s:7:"Excerpt";s:4:"part";s:7:"excerpt";s:3:"src";s:14:"render_excerpt";s:6:"render";s:8:"function";s:6:"length";s:2:"15";s:12:"allowed_tags";s:0:"";s:6:"usetag";s:1:"1";s:3:"tag";s:4:"span";s:8:"tagclass";s:21:"intelliwidget-excerpt";s:7:"uselink";s:1:"1";s:9:"link_text";s:9:"Read More";s:9:"linkclass";s:23:"intelliwidget-more-link";s:10:"usemoretag";s:0:"";s:7:"moretag";s:0:"";s:12:"moretagclass";s:0:"";}s:3:"css";N;s:8:"required";b:0;}}s:7:"options";a:18:{s:4:"name";s:9:"Container";s:4:"part";s:9:"container";s:3:"src";s:0:"";s:6:"render";s:0:"";s:6:"usetag";s:1:"1";s:3:"tag";s:3:"div";s:8:"tagclass";s:0:"";s:8:"useinner";s:0:"";s:5:"inner";s:3:"div";s:5:"ratio";s:2:"st";s:10:"innerclass";s:0:"";s:6:"useimg";s:0:"";s:6:"source";s:14:"featured_image";s:9:"fieldname";s:0:"";s:7:"uselink";s:1:"1";s:9:"linkclass";s:0:"";s:6:"depimg";s:1:"1";s:5:"usebg";s:0:"";}s:3:"css";O:19:"IntelliWidgetMainCss":5:{s:2:"id";s:8:"css_ctr1";s:5:"items";a:0:{}s:7:"options";a:10:{s:1:"l";s:0:"";s:1:"r";s:0:"";s:1:"c";s:0:"";s:1:"f";s:0:"";s:1:"e";s:0:"";s:1:"u";s:0:"";s:1:"b";s:0:"";s:1:"t";s:0:"";s:1:"w";s:0:"";s:1:"s";s:0:"";}s:3:"css";N;s:8:"required";b:1;}s:8:"required";b:0;}}s:7:"options";a:20:{s:4:"name";s:15:"Custom Template";s:4:"part";s:8:"template";s:3:"src";s:0:"";s:6:"render";s:0:"";s:7:"columns";s:1:"1";s:6:"usetag";s:1:"1";s:3:"tag";s:3:"div";s:8:"tagclass";s:8:"bottom15";s:9:"classmeta";s:0:"";s:7:"usewrap";s:0:"";s:4:"wrap";s:3:"div";s:9:"wrapclass";s:0:"";s:8:"useinner";s:0:"";s:5:"inner";s:3:"div";s:5:"ratio";s:2:"tv";s:10:"innerclass";s:0:"";s:5:"usebg";s:0:"";s:6:"useimg";s:1:"1";s:6:"source";s:14:"featured_image";s:9:"fieldname";s:0:"";}s:3:"css";O:19:"IntelliWidgetMainCss":5:{s:2:"id";s:8:"css_tmp1";s:5:"items";a:0:{}s:7:"options";a:2:{s:1:"u";i:0;s:1:"b";s:1:"0";}s:3:"css";N;s:8:"required";b:1;}s:8:"required";b:0;}';
$opt = 'a:2:{s:8:"condsets";a:1:{i:1;s:12:"Untitled Set";}s:4:"tree";O:20:"IntelliWidgetMainTree":5:{s:2:"id";s:4:"tree";s:5:"items";a:8:{s:8:"css_tmp1";s:4:"tmp1";s:4:"tmp1";s:4:"tree";s:4:"prt1";s:4:"ctr1";s:8:"css_ctr1";s:4:"ctr1";s:4:"ctr1";s:4:"tmp1";s:8:"css_ctr3";s:4:"ctr3";s:4:"ctr3";s:4:"tmp1";s:4:"prt2";s:4:"ctr1";}s:7:"options";a:0:{}s:3:"css";N;s:8:"required";i:1;}}';
// double check it does not exist
if ( is_multisite() )
    switch_to_blog( get_main_site_id() );	
if ( ! get_option( INTELLIWIDGET_OPTIONS ) ):
    self::$options = unserialize( $opt );
    /**
     * save as serialized objects.
     * DO NOT AUTOLOAD 
     * or bad things may happen...
     */
    update_option( 
        INTELLIWIDGET_OPTIONS, 
        self::$options, 
        FALSE 
    );
    if ( ! get_option( INTELLIWIDGET_TEMPLATES . 'tmp1' ) )
        update_option( 
            INTELLIWIDGET_TEMPLATES . 'tmp1', unserialize( $tmp ), 
            FALSE 
        );
endif;
if ( is_multisite() && ms_is_switched() )
    restore_current_blog();    

