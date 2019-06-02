<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class UnsplashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $client = new Client(); //GuzzleHttp\Client
        $result = $client->request(
            'GET', 
            'https://api.unsplash.com/photos',
            [
                'headers' => [
                    'Accept-Version' => 'v1'
                ],
                'query' => [
                    'client_id' => config('unsplash.key'),

                ]
            ]
        );
        $photos = json_decode((string) $result->getBody());
        return $photos;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($search)
    {
        $client = new Client(); //GuzzleHttp\Client
        $params = array(
            'client_id' => config('unsplash.key'),
            'query' => $search,
            'page' => 1,
            'per_page' => 30
        );
        $result = $client->request(
            'GET', 
            'https://api.unsplash.com/search/photos/?' . http_build_query($params)
        );
        $photos = json_decode((string) $result->getBody(), true);
        return $photos;
    }
}
