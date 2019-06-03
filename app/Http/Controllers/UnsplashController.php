<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use JWTAuth;
use JWTAuthException;

class UnsplashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page = 1)
    {
        $photos = Cache::remember('_popular_photos_page_' . $page, 86400, function () use ($page) {
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
                        'page' => $page,

                    ]
                ]
            );
            $photos = json_decode((string) $result->getBody());
            return $photos;
        });
        $user = JWTAuth::user();
        $data = array(
            'success' => true,
            'user' => array(
                'name' => $user->name ?? '',
                'email' => $user->email ?? ''
            ),
            'photos' => $photos
        );
        return response()->json($data, 201);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($search)
    {
        $photos = Cache::remember('_popular_photos_search_' . str_slug($search, '_'), 86400, function () use ($search) {
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
        });
        $user = JWTAuth::user();
        $data = array(
            'success' => true,
            'user' => array(
                'name' => $user->name ?? '',
                'email' => $user->email ?? ''
            ),
            'photos' => $photos
        );
        return response()->json($data, 201);
    }
}
