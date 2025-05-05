<?php

namespace App\Http\Controllers\Api\Parking;

use App\Http\Controllers\Controller;
use App\Models\ParkingRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Data\Parking\CalculateParkingFeeRequestData;
use App\Data\Parking\CalculateParkingFeeResponseData;

class CalculateParkingFeeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = CalculateParkingFeeRequestData::validateAndCreate($request->all());

        /** @var ParkingRecord $record */
        $record = ParkingRecord::findOrFail($data->record_id);

        if ($record->exit_time) {
            return response()->json([
                'message' => 'Este veículo já saiu do estacionamento.',
            ], 400);
        }

        $totalAmountData = $record->calculateTotalAmount($data->exit_time);

        $response = CalculateParkingFeeResponseData::make(
            $totalAmountData['amount'],
            number_format($totalAmountData['amount'], 2, ',', '.'),
            $totalAmountData['already_paid']
        );

        return response()->json($response);
    }
}
