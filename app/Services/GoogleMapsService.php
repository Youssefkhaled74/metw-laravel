<?php

namespace App\Services;

use App\Models\City;
use App\Models\State;
use App\Models\Zone;
use GuzzleHttp\Client;

class GoogleMapsService
{
    protected Client $httpClient;
    protected string $apiKey;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://maps.googleapis.com/maps/api/',
            'timeout' => 8.0,
        ]);
        $this->apiKey = config('googlemap.google_maps_api_key', '');
    }

    public function reverseGeocode(float $latitude, float $longitude): array
    {
        if (empty($this->apiKey)) {
            return [];
        }

        $response = $this->httpClient->get('geocode/json', [
            'query' => [
                'latlng' => $latitude . ',' . $longitude,
                'key' => $this->apiKey,
            ],
        ]);

        $data = json_decode((string) $response->getBody(), true);
        if (!is_array($data) || ($data['status'] ?? '') !== 'OK') {
            return [];
        }

        return $data['results'] ?? [];
    }

    public function distanceInKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadiusKm * $c;
    }

    public function midpoint(float $lat1, float $lng1, float $lat2, float $lng2): array
    {
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $bx = cos($lat2) * cos($lng2 - $lng1);
        $by = cos($lat2) * sin($lng2 - $lng1);
        $lat3 = atan2(sin($lat1) + sin($lat2), sqrt((cos($lat1) + $bx) * (cos($lat1) + $bx) + $by * $by));
        $lng3 = $lng1 + atan2($by, cos($lat1) + $bx);

        return [
            'latitude' => rad2deg($lat3),
            'longitude' => rad2deg($lng3),
        ];
    }



    /**
     * استيراد كل المحافظات
     */
    public function importGovernorates(): void
    {
        $governorates = [
            "Cairo",
            "Giza",
            "Alexandria",
            "Qalyubia",
            "Sharqia",
            "Dakahlia",
            "Gharbia",
            "Beheira",
            "Kafr El Sheikh",
            "Monufia",
            "Ismailia",
            "Port Said",
            "Suez",
            "Damietta",
            "Beni Suef",
            "Faiyum",
            "Minya",
            "Asyut",
            "Sohag",
            "Qena",
            "Luxor",
            "Aswan",
            "Red Sea",
            "New Valley",
            "Matrouh",
            "North Sinai",
            "South Sinai"
        ];

        foreach ($governorates as $gov) {
            $state = State::firstOrCreate([
                'name_en' => $gov,
            ], [
                'name_ar' => $gov,
                'is_active' => 1,
            ]);

            $this->importCities($state);
        }
    }

    /**
     * استيراد المدن داخل محافظة
     */
    public function importCities(State $state): void
    {
        $query = "cities in {$state->name_en}, {$this->country}";
        $url = "https://maps.googleapis.com/maps/api/place/textsearch/json";

        $response = Http::get($url, [
            'query' => $query,
            'key' => $this->apiKey,
        ]);

        $results = $response->json('results', []);

        foreach ($results as $cityData) {
            $city = City::firstOrCreate([
                'name_en' => $cityData['name'],
                'state_id' => $state->id,
            ], [
                'name_ar' => $cityData['name'],
                'is_active' => 1,
            ]);

            $this->importZones($city);
        }

        echo "✅ Imported " . count($results) . " cities for {$state->name_en}\n";
    }

    /**
     * استيراد المناطق داخل المدينة
     */
    public function importZones(City $city): void
    {
        $query = "neighborhoods in {$city->name_en}, {$this->country}";
        $url = "https://maps.googleapis.com/maps/api/place/textsearch/json";

        $response = Http::get($url, [
            'query' => $query,
            'key' => $this->apiKey,
        ]);

        $results = $response->json('results', []);

        foreach ($results as $zoneData) {
            Zone::firstOrCreate([
                'name_en' => $zoneData['name'],
                'city_id' => $city->id,
            ], [
                'name_ar' => $zoneData['name'],
                'is_active' => 1,
            ]);
        }

        echo "   ↳ Imported " . count($results) . " zones for {$city->name_en}\n";
    }
    public function getHandoffPoint(string $locationName): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $response = $this->httpClient->get('geocode/json', [
            'query' => [
                'address' => $locationName . ', Egypt',
                'key' => $this->apiKey,
            ],
        ]);

        $data = json_decode((string)$response->getBody(), true);

        if (($data['status'] ?? '') !== 'OK') {
            return null;
        }

        $location = $data['results'][0]['geometry']['location'] ?? null;

        if (!$location) {
            return null;
        }

        return [
            'latitude' => $location['lat'],
            'longitude' => $location['lng'],
        ];
    }

    public function roadDistanceInKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        if (empty($this->apiKey)) {
            return 0;
        }

        $response = $this->httpClient->get('distancematrix/json', [
            'query' => [
                'origins'      => "{$lat1},{$lng1}",
                'destinations' => "{$lat2},{$lng2}",
                'mode'         => 'driving',
                'units'        => 'metric',
                'key'          => $this->apiKey,
            ],
        ]);

        $data = json_decode((string)$response->getBody(), true);

        if (!isset($data['rows'][0]['elements'][0]['distance']['value'])) {
            return 0;
        }

        // distance in meters -> convert to KM
        $meters = $data['rows'][0]['elements'][0]['distance']['value'];

        return round($meters / 1000, 2);
    }

    public function validateLatLngMatchesState(
        float $latitude,
        float $longitude,
        State $state,
        ?City $city = null
    ): bool {
        $results = $this->reverseGeocode($latitude, $longitude);

        if (empty($results)) {
            return false;
        }

        foreach ($results as $result) {
            $components = $result['address_components'] ?? [];

            $country = null;
            $adminLevel1 = null;
            $locality = null;    // City

            foreach ($components as $component) {
                if (in_array('country', $component['types'])) {
                    $country = $component['long_name'];
                }

                if (in_array('administrative_area_level_1', $component['types'])) {
                    $adminLevel1 = $component['long_name'];
                }

                if (in_array('locality', $component['types'])) {
                    $locality = $component['long_name'];
                }
            }

            // ✅ Country check
            // if (strtolower($country) !== 'egypt') {
            //     return false;
            // }

            // ✅ Flexible State check (ignore "Governorate" word)
            if ($adminLevel1) {
                $normalize = fn($name) => strtolower(trim(str_ireplace(['governorate', 'muhafazah'], '', $name)));

                if ($normalize($adminLevel1) !== $normalize($state->name_en)) {
                    return false;
                }
            }


            // ✅ Optional city check
            // if ($city && $locality && strcasecmp($locality, $city->name_en) !== 0) {
            //     return false;
            // }

            return true; 
        }

        return false;
    }



}
