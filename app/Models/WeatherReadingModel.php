<?php

namespace App\Models;

use CodeIgniter\Model;

class WeatherReadingModel extends Model
{
    protected $table         = 'weather_readings';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['city', 'temperature_c', 'humidity_percent', 'condition', 'observed_at', 'is_daylight'];
}
