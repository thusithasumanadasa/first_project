<!DOCTYPE html>
<html <?php language_attributes(); ?>>              
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />                   
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php wp_title( '|', true, 'right' ); ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11">                
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />       
        <link rel="shortcut icon" type="image/x-icon"  href="<?php echo get_option('kopa_theme_options_favicon_url'); ?>">     
        <link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_option('kopa_theme_options_apple_iphone_icon_url'); ?>">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_option('kopa_theme_options_apple_ipad_icon_url'); ?>">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_option('kopa_theme_options_apple_iphone_retina_icon_url'); ?>">
        <link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_option('kopa_theme_options_apple_ipad_retina_icon_url'); ?>">        
        <?php wp_head(); ?>
    </head>    
    <?php $layout = get_option('kopa_theme_options_layout', 'wide'); 
    $layout = ( $layout === 'box' ? 'kopa-boxed' : '' );
    $layout .= ' heavy-dark-footer';
    ?>
    <body <?php body_class($layout); ?>>
    
        <div class="kopa-wrapper">
        
            <div class="kopa-background"></div>
            
            <header id="page-header">
                <div id="header-top">
                    <div class="wrapper">
                        <div class="row-fluid">
                            <div class="span12 clearfix">
                                <p class="hot-line"><?php echo get_option('kopa_theme_options_left_header_description'); ?></p>
                                <div class="top-box clearfix">
                                    <ul class="top-link clearfix">

                                        <li><?php 
                                            wp_register('', '');

                                            if ( is_user_logged_in() || get_option( 'users_can_register' ) ) {
                                                echo '&nbsp;/&nbsp;';
                                            }
                                            
                                            wp_loginout();
                                        ?></li>

                                    </ul>
                                    <div class="search clearfix">
                                        <a href="#" class="search-icon" title="Search">Search</a>
                                        <?php get_search_form(); ?>
                                    </div><!--search-->
                                </div><!--top=box-->
                            </div><!--span12-->
                        </div><!--row-fluid-->
                    </div><!--wrapper-->
                </div><!--header-top-->
                
                <div id="header-bottom" class="cbp-af-header">
                    <div class="wrapper">
                        <div class="row-fluid">
                            <div class="span12 clearfix">
                                <div id="logo-image" class="<?php echo ( display_header_text() && get_header_image() ) ? 'kp-text-logo' : ''; ?>">
                                    <?php if ( get_header_image() ) { ?>
                                    <a href="<?php echo esc_url( home_url() ); ?>">
                                        <img src="<?php header_image(); ?>" alt="<?php bloginfo('name'); ?> <?php _e( 'Logo', kopa_get_domain() ); ?>">
                                    </a>
                                    <?php } ?>
                                    <h1 class="site-title"><a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo('name'); ?></a></h1>
                                </div><!--logo-image-->
                                <nav id="main-nav">
                                    <?php 

                                    if ( has_nav_menu('main-nav') ) {
                                        $main_menu_walker = new kopa_main_menu();
                                        wp_nav_menu( array(
                                            'theme_location' => 'main-nav',
                                            'container'      => '',
                                            'items_wrap'     => '<ul id="main-menu" class="clearfix">%3$s</ul>',
                                            'walker'         => $main_menu_walker
                                        ) );

                                        $mobile_menu_walker = new kopa_mobile_menu();
                                        wp_nav_menu( array(
                                            'theme_location' => 'main-nav',
                                            'container'      => 'div',
                                            'container_id'   => 'mobile-menu',
                                            'items_wrap'     => '<span>'.__( 'Menu', kopa_get_domain() ).'</span><ul id="toggle-view-menu">%3$s</ul>',
                                            'walker'         => $mobile_menu_walker
                                        ) );
                                    }
                                    ?>
                                </nav><!--main-nav-->
                            </div><!--span12-->
                        </div><!--row-fluid-->
                    </div><!--wrapper-->
                </div><!--header-bottom-->
            </header><!--page-header-->