<?php

namespace App\Services;

use App\Entity\Anuncio;

class MapBox
{
    private const API_TOKEN = '';

    public function getRoute(string $lat1, string $lon1, string $lat2, string $lon2)
    {
        $url = "https://api.mapbox.com/directions/v5/mapbox/driving/$lon1,$lat1;$lon2,$lat2?geometries=geojson&access_token=".self::API_TOKEN;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        $data = json_decode($response);

        return $data;
    }
}