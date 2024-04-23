<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Country;
use App\Models\Emission;
use App\Models\Pollutant;
use App\Models\Temperature;
use App\Models\User;
use App\Models\Year;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Undefined;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    public function run(): void
    {
        
        User::create([
            'name' => 'Admin',
            'email' => 'a@a.com',
            'password' => 'trudnehaslo',
            'role' => 'admin'
        ]);
        $this->emisje();
        $this->temps();
       
    }

    private function temps()
    {
        $countryNameIndex = 3;
        $tempValueIndex = 11;
        $yearIndex = 9;

        $file = Storage::get('FAOSTAT.csv');
        $rows = explode("\n", $file);
        $header = array_shift($rows);


        foreach ($rows as $row) {
            $data = str_getcsv($row);

            $countryName = $data[$countryNameIndex];
            $year = $data[$yearIndex];
            $value = $data[$tempValueIndex];

            $countryOb = Country::where('name', $countryName)->first();
            $yearOb = Year::where('year', $year)->first();

            if(!$countryOb || !$yearOb){
                continue;
            }

            $value = ($value === "") ? null : $value;

            Emission::where('country_id', $countryOb->id)
            ->where('year_id', $yearOb->id)
            ->update(['temperatureChange' => $value]);
            
        }
    }

    private function emisje()
    {
        $countryName = 1;
        $emissionValueIndex = 14;
        $pollutantIndex = 3;
        $variableIndex = 5;
        $yearIndex = 6;
        $file = Storage::get('AIR_EMISSIONS.csv');

        $rows = explode("\n", $file);
        
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = str_getcsv($row);
            if ($data[$emissionValueIndex] == "") continue;
            $country = Country::firstOrCreate(['name' => $data[$countryName]]);

            $pollutant = Pollutant::firstOrCreate(['name' => $data[$pollutantIndex]]);

            $year = Year::firstOrCreate(['year' => $data[$yearIndex]]);

            $emissionValue = new Emission();
            $emissionValue->emissionValue = $data[$emissionValueIndex];
            $emissionValue->variable = $data[$variableIndex];
            $emissionValue->country()->associate($country);
            $emissionValue->pollutant()->associate($pollutant);
            $emissionValue->year()->associate($year);

            $emissionValue->save();
        }
    }
}

