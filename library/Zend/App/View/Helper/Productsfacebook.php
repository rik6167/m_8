<?php

/**
 * App_View_Helper_Products
 *
 * @author jfalvarez
 */
class App_View_Helper_ProductsFacebook extends Zend_View_Helper_Abstract
{
    public function productsfacebook( Array $options = array() )
    {
        $products = new Default_Model_Products();
        $data = $products->getProducts();

        $translate = new Zend_View_Helper_Translate();

        $xhtml = null;
        $productarr = array();
        $index = 1;
        foreach ( $data AS $product )
        {
            // TODO: Crear un router para tener 'pretty urls' ^o^. (jfas)
            $xhtml = "<div class='content_a'><div class='subtitles'><a href='/products/index/view/id/{$product["id_producto"]}/type/fb' >{$product["nombre_producto"]}</a></div>
                <div class='content_info'>
                <div class='article'>
                <div class='thumb'><img src='/images/products/{$product["imagen"]}' alt='thumb' width='79' height='94' border='0' /></div>";

            if ( false === empty( $product["fecha_inicio"] ) )
            {
                $xhtml .= "<div class='time_left'>" . $translate->translate( "Remaining Time" ) . "</div>
                <div class='time' id='timer-{$product["id_producto"]}'></div>";
            }

            $xhtml .= "</div>
                <div class='prices_content'>
                <div class='price_title_blue'><span class='padding_l_r'>" . $translate->translate( "Start Price" ) . "</span></div>
                <div class='initial_price_blue'><p class='padding_t_b3'>" . ( ( false === empty( $product["precio_inicial"] ) ) ? $product["precio_inicial"] : $product["precio_msrp"] ) . "</p></div>
                <div class='price_title_orange'><span class='padding_l_r'>" . $translate->translate( "Current Price" ) . "</span></div>
                <div class='actual_price_orange'><p class='padding_t_b3'>" . ( ( false === empty( $product["precio_actual"] ) ) ? $product["precio_actual"] : $product["precio_msrp"] ) . "</p></div>
                <div class='link_title_grey'><span class='padding_l_r'>" . $translate->translate( "Users" ) . "</span></div>
                <div class='link_grey'><p class='padding_t_b3'>" . ( ( false === empty( $product["usuarios"] ) ) ? $product["usuarios"] : 0 ) . " " . $translate->translate( "Users" ) . "</p></div>
                </div>
                </div>
                <div class='join'><p class='padding_t_b2'><a href='/products/index/view/id/{$product["id_producto"]}/type/fb' class='join'>" . $translate->translate( "Join!" ) . "</a></p></div>
                </div>";

            if ( false === empty( $product["fecha_inicio"] ) )
            {
                $xhtml .= "
                <script type='text/javascript'>
                    var timerProd{$product["id_producto"]} = new Date( '" . date( "F d, Y H:m:s", strtotime( "{$product["fecha_inicio"]} +{$product["horas_estimadas"]} hours" ) ) . "' );
                   $( '#timer-{$product["id_producto"]}' ).countdown( { until: timerProd{$product["id_producto"]}, format: 'HMS', compact: true, description: '' } );
                </script>";
            }

            $productarr[$index] = $xhtml;
            $index++;
        }
        return $productarr;
    }
}
