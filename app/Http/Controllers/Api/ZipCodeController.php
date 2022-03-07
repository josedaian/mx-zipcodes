<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Exceptions\ZipCodeNotFound;
use App\Http\Resources\ZipCodeResource;
use App\Models\ZipCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ZipCodeController extends ApiController
{
    function get(Request $request, string $zipCode){
        return $this->dispatchApiRequest($request, function () use($zipCode){
            $zipCode = Cache::rememberForever($zipCode, function() use($zipCode){
                return ZipCode::with(['federalEntity', 'municipality', 'settlements.settlementType'])
                    ->where('zip_code', $zipCode)
                    ->first();
            });

            if(!$zipCode){
                throw new ZipCodeNotFound($zipCode);
            }

            return $this->successResponse(new ZipCodeResource($zipCode));
        });
    }
}
