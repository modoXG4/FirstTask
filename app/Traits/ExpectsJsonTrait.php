<?php
namespace App\Traits;

trait ExpectsJsonTrait
{
    use ApiResponseTrait;
    
    public function respondBasedOnRequest($request, $successResponse, $redirectRoute, $successMessage)
    {
        if ($request->expectsJson()) {
            return $this->success($successResponse, $successMessage);
        }

        return redirect()->route($redirectRoute)->with('success', $successMessage);
    }
}
