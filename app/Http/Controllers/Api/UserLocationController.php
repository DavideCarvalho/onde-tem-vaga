<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Data\UserLocationResponseData;

class UserLocationController extends Controller
{
  public function __invoke(Request $request): JsonResponse
  {
    $ip = $request->ip();
    $apiKey = config('services.ip2location.key');
    $response = Http::get('https://api.ip2location.io/', [
      'key' => $apiKey,
      'ip' => $ip,
      'format' => 'json',
    ]);
    dd($response->json());
    if ($response->successful()) {
      $data = $response->json();
      return response()->json(UserLocationResponseData::make(
        isset($data['latitude']) ? (float)$data['latitude'] : null,
        isset($data['longitude']) ? (float)$data['longitude'] : null,
        $data['city_name'] ?? null,
        $data['region_name'] ?? null,
        $data['country_name'] ?? null,
      ));
    }
    return response()->json(['error' => 'Não foi possível obter localização'], 400);
  }
}
