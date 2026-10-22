# From model to code

1. Which file is the model, and why is it platform independent?

The model is `app/PIM/weather_reading.json`. It is platform independent because it
only says `text`, `decimal`, `wholenumber`, `datetime`, and `yesno`; it never names
a database or a framework, so the same file works for any target.

2. Which files were generated from it?

Running `php spark model:generate app/PIM/weather_reading.json` wrote
`app/Database/Migrations/2026-10-22-123522_CreateWeatherReadingTable.php` and
`app/Models/WeatherReadingModel.php`. Running the same command with the `sql`
target wrote `writable/generated/weather_readings.sql`.

3. Which mapping rule turned a text field into a column, and what did it become?

The `city` field has type `text`, and the `$ci4Types` rule
`'text' => ['VARCHAR', 255]` turned it into the column `city` with
`'type' => 'VARCHAR', 'constraint' => 255, 'null' => false` in the migration. For
the sql target the `$sqlTypes` rule `'text' => 'VARCHAR(100)'` produced
`city VARCHAR(100) NOT NULL`.

4. What would you change to produce code for a different platform?

I would add a new mapping list and a new `make...` method for that platform in
`app/Commands/ModelGenerate.php`, then pass its name as the target instead of
`ci4` or `sql`. The model file in `app/PIM` would not change at all.
