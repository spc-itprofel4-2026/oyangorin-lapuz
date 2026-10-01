<?php

namespace App\Controllers;

use App\Models\WeatherLogModel;
use Config\Services;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function hello($name = null)
    {
        $data = [
            'name' => $name ?? 'World',
        ];

        return view('hello', $data);
    }

    public function weather()
    {
        $client = Services::curlrequest();
        $response = $client->get('https://api.open-meteo.com/v1/forecast?latitude=8.2280&longitude=124.2452&current_weather=true');
        $result = json_decode($response->getBody(), true);
        $temperature = $result['current_weather']['temperature'] ?? null;

        $weatherLogModel = new WeatherLogModel();
        $weatherLogModel->insert([
            'city'        => 'Iligan City',
            'temperature' => $temperature,
            'fetched_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'city'        => 'Iligan City',
            'temperature' => $temperature,
            'source'      => 'Open-Meteo',
        ]);
    }

    public function weatherLogs()
    {
        $weatherLogModel = new WeatherLogModel();

        return $this->response->setJSON($weatherLogModel->findAll());
    }
}
