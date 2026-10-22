CREATE TABLE weather_readings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(100) NOT NULL,
    temperature_c DECIMAL(10,2) NOT NULL,
    humidity_percent INT NOT NULL,
    condition VARCHAR(100),
    observed_at DATETIME NOT NULL,
    is_daylight TINYINT(1)
);
