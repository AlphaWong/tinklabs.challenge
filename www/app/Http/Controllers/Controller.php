<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    public function index(Request $request, $controller = null, $action = null)
    {
        if (!empty($controller) && !empty($action)) {
            // TODO: use `use`
            $classObj = 'App\\Http\\Models\\Api\\'.ucfirst($controller);
            $methodName = 'model'.ucfirst($action);

            // Make sure models & method exist
            if (class_exists($classObj) && method_exists($classObj, $methodName)) {
                $modelReturn = (new $classObj)->{$methodName}($request);
                return response()->json($modelReturn, 201);
            }
        }

        abort(404);
    }
}