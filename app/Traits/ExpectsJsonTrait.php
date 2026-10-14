<?php
namespace App\Traits;

trait ExpectsJsonTrait
{
    use ApiResponseTrait;

    public function respondBasedOnRequest($request, $successResponse, $redirectRoute, $successMessage, $statusCode = 200, array $redirectParams = [])
    {
        if ($request->expectsJson()) {
            return $this->success($successResponse, $successMessage, $statusCode);
        }

        if ($redirectRoute === null) {
            abort($statusCode, $successMessage);
        }

        return redirect()->route($redirectRoute, $redirectParams)->with('success', $successMessage);
    }
}
