<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Emission;
use App\Models\Pollutant;
use App\Models\Year;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class EmissionController extends Controller
{
    public function importJson(Request $request)
    {
        if ($request->hasFile('importFile')) {
            $jsonFile = $request->file('importFile');
            if ($jsonFile->getClientOriginalExtension() !== 'json') {
                return new JsonResponse(['message' => 'Invalid file format. Only JSON files are allowed.']);
            }
            if ($jsonFile) {
                $jsonContent = file_get_contents($jsonFile->getRealPath());
                $jsonData = json_decode($jsonContent, true);

                if ($jsonData === null && json_last_error() !== JSON_ERROR_NONE) {
                    return new JsonResponse(['message' => 'Error parsing JSON. Invalid JSON content.']);
                }
                
                DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");

                DB::beginTransaction();

                try {
                    foreach ($jsonData as $item) {
                        $countryName = $item['country'];
                        $yearValue = $item['year'];
                        $pollutantName = $item['pollutant'];
                        $variable = $item['variable'];
                        $emissionValue = $item['emissionValue'];
                        $temperatureChange = $item['temperatureChange'];

                        $country = Country::firstOrCreate(['name' => $countryName]);
                        $year = Year::firstOrCreate(['year' => $yearValue]);
                        $pollutant = Pollutant::firstOrCreate(['name' => $pollutantName]);

                        $emission = Emission::where('country_id', $country->id)
                            ->where('year_id', $year->id)
                            ->where('pollutant_id', $pollutant->id)
                            ->where('variable', $variable)
                            ->first();
                        
                        if ($emission) {
                            // Entry exists, update it
                            $emission->emissionValue = $emissionValue;
                            $emission->temperatureChange = $temperatureChange;
                            $emission->save();
                        } else {
                            // Entry does not exist, insert new
                            $newEmission = new Emission();
                            $newEmission->country_id = $country->id;
                            $newEmission->year_id = $year->id;
                            $newEmission->pollutant_id = $pollutant->id;
                            $newEmission->variable = $variable;
                            $newEmission->emissionValue = $emissionValue;
                            $newEmission->temperatureChange = $temperatureChange;
                            $newEmission->save();
                        }
                        
                    }

                    DB::commit();
                    return new JsonResponse(['message' => 'json imported correctly']);
                } catch (Exception $e) {
                    return response($e->getMessage());
                    DB::rollBack();
                }
            }
        }

        return new JsonResponse(['message' => 'no data']);;
    }

    

    public function importXml(Request $request)
    {
        if ($request->hasFile('importFile')) {

            $xmlFile = $request->file('importFile');
            if ($xmlFile->getClientOriginalExtension() !== 'xml') {
                return new JsonResponse(['message' => 'Invalid file format. Only XML files are allowed.']);
            }
            if ($xmlFile) {

                $xmlContent = file_get_contents($xmlFile->getRealPath());

                try {
                    $xml = simplexml_load_string($xmlContent);
                } catch (\ErrorException $e) {
                    return new JsonResponse(['message' => 'Error parsing XML: ' . $e->getMessage()]);
                } catch (\Throwable $e) {
                    return new JsonResponse(['message' => 'Error parsing XML: ' . $e->getMessage()]);
                }

                DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");

                DB::beginTransaction();

                try{
                    foreach ($xml->emissionInfo as $item) {
                        $countryName = $item->country;
                        $yearValue = $item->year;
                        $pollutantName = $item->pollutant;
                        $variable = $item->variable;
                        $emissionValue = $item->emissionValue;
                        $temperatureChange = $item->temperatureChange;
    
                        $country = Country::firstOrCreate(['name' => $countryName]);
                        $year = Year::firstOrCreate(['year' => $yearValue]);
                        $pollutant = Pollutant::firstOrCreate(['name' => $pollutantName]);
                        
                        $emission = Emission::where('country_id', $country->id)
                            ->where('year_id', $year->id)
                            ->where('pollutant_id', $pollutant->id)
                            ->where('variable', $variable)
                            ->first();
    
                        if ($emission) {
                            $emission->emissionValue = $emissionValue;
                            if($temperatureChange == ""){
                                $emission->temperatureChange = null;
                            } else {
                                $emission->temperatureChange = $temperatureChange;
                            }
                            
                            
                            
                            $emission->save();
                            
                        } else {
                            $newEmission = new Emission();
                            $newEmission->country_id = $country->id;
                            $newEmission->year_id = $year->id;
                            $newEmission->pollutant_id = $pollutant->id;
                            $newEmission->variable = $variable;
                            $newEmission->emissionValue = $emissionValue;
                            $newEmission->temperatureChange = $temperatureChange;
                            $newEmission->save();
                            
                        }
    
                    }
                    DB::commit();
                    return new JsonResponse(['message' => 'xml imported correctly']);
                }catch(Exception $e){
                    return response($e->getMessage());
                    DB::rollBack();
                }
                
            }

        }

        return new JsonResponse(['message' => 'no data']);
    }

    public function exportXml(Request $request)
    {
        $country = $request->input('country') ?? 'Australia';
        $pollutant = $request->input('pollutant') ?? 'Sulphur Oxides';
        $variable = $request->input('variable') ?? 'Total man-made emissions';
        DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
        DB::beginTransaction();
        try {
            $emissions = Emission::with(['country', 'year', 'pollutant'])
                ->when($country, function ($query) use ($country) {
                    $query->whereHas('country', function ($query) use ($country) {
                        $query->where('name', $country);
                    });
                })
                ->when($pollutant, function ($query) use ($pollutant) {
                    $query->whereHas('pollutant', function ($query) use ($pollutant) {
                        $query->where('name', $pollutant);
                    });
                })
                ->when($variable, function ($query) use ($variable) {
                    $query->where('variable', $variable);
                })
                ->take(50)->get(['emissionValue', 'temperatureChange', 'country_id', 'year_id', 'pollutant_id']);

            $xml = new SimpleXMLElement('<data></data>');
            foreach ($emissions as $emission) {
                $item = $xml->addChild('emissionInfo');
                $item->addChild('country', $emission->country->name ?? null);
                $item->addChild('year', $emission->year->year ?? null);
                $item->addChild('pollutant', $emission->pollutant->name ?? null);
                $item->addChild('variable', $variable);
                $item->addChild('emissionValue', $emission->emissionValue);
                $item->addChild('temperatureChange', $emission->temperatureChange);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response($e->getMessage());
        }



        return response($xml->asXML(), 200)
            ->header('Content-Type', 'application/xml');
    }

    public function exportJson(Request $request)
    {
        $country = $request->input('country') ?? 'Australia';
        $pollutant = $request->input('pollutant') ?? 'Sulphur Oxides';
        $variable = $request->input('variable') ?? 'Total man-made emissions';

        DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
        DB::beginTransaction();

        try {
            $emissions = Emission::with(['country', 'year', 'pollutant'])
            ->when($country, function ($query) use ($country) {
                $query->whereHas('country', function ($query) use ($country) {
                    $query->where('name', $country);
                });
            })
            ->when($pollutant, function ($query) use ($pollutant) {
                $query->whereHas('pollutant', function ($query) use ($pollutant) {
                    $query->where('name', $pollutant);
                });
            })
            ->when($variable, function ($query) use ($variable) {
                $query->where('variable', $variable);
            })
            ->take(50)->get(['emissionValue', 'temperatureChange', 'country_id', 'year_id', 'pollutant_id']);
        

        $emissions = $emissions->map(function ($emission) use ($variable) {
            return [
                'country' => $emission->country->name ?? null,
                'year' => $emission->year->year ?? null,
                'pollutant' => $emission->pollutant->name ?? null,
                'variable' => $variable,
                'emissionValue' => $emission->emissionValue,
                'temperatureChange' => $emission->temperatureChange,
            ];
        });
        DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return response($e->getMessage());
        }
        
        return new JsonResponse($emissions);
    }
}
