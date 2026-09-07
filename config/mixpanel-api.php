<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Project Token
    |--------------------------------------------------------------------------
    |
    | Used to authenticate ingestion requests (event tracking, profile
    | updates). Find it under Project Settings > Access Keys.
    |
    */
    'token' => env('MIXPANEL_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | API Key & Secret
    |--------------------------------------------------------------------------
    |
    | Used for Basic Auth against the query and export APIs (insights,
    | funnels, retention, raw event exports). Find them under Project
    | Settings > Access Keys.
    |
    */
    'api_key' => env('MIXPANEL_API_KEY', ''),
    'secret' => env('MIXPANEL_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Project ID
    |--------------------------------------------------------------------------
    |
    | Required by the Query API's /insights (events) endpoint.
    |
    */
    'project_id' => env('MIXPANEL_PROJECT_ID', ''),

];
