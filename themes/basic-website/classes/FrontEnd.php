<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

class FrontEnd
{

    /**
    * @method
    */

    public static function newMenu($varname = '', $items = array(), $before = '', $after = '')
    {
        $html = '';
        $html .= $before . '<ul>';

        if (empty($items)) {
            return null;
        }

        foreach ($items as $item => $data) {
            if (!isset($data['class'])) {
                $html .= '<li>';
            } else {
                $html .= '<li class="' . $data['class'] . '">';
            }

            if (!isset($data['url'])) {
                if (isset($data['icon-before'])) {
                    $html .= '<i class="before fas ' . $data['icon-before'] . '"></i>';
                }

                if (is_string($item)) {
                    $html .= $item;
                }

                if (isset($data['icon-after'])) {
                    $html .= '<i class="after fas ' . $data['icon-after'] . '"></i>';
                }
            } else {
                $html .= '<a href="' . $data['url'] . '"';

                if (isset($data['blank']) && $data['blank'] == true) {
                    $html .= ' target="_blank">';
                } else {
                    $html .= '>';
                }

                if (isset($data['icon-before'])) {
                    $html .= '<i class="before fas ' . $data['icon-before'] . '"></i>';
                }

                if (is_string($item)) {
                    $html .= $item;
                }

                if (isset($data['icon-after'])) {
                    $html .= '<i class="after fas ' . $data['icon-after'] . '"></i>';
                }

                $html .= '</a>';
            }

            if (isset($data['inner'])) {
                $html .= $data['inner'];
            }

            $html .= '</li>';
        }

        $html .= '</ul>' . $after;

        Webmart::addValue($varname, $html);
    }

}
