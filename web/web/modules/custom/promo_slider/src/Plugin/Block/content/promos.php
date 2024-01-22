<?php

namespace Drupal\promo_slider\Plugin\Block\content;

class Promos
{

    public static function getTopPromo()
    {
        $retrive = [
            "Body" => "hello",
            "Env" => $_ENV["APP_ENV"]
        ];

        return $retrive;
    }
}
