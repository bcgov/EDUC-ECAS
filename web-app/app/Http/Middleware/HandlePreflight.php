<?php

namespace App\Http\Middleware;

use closure;
use Illuminate\HTTP\Request;

class HandlePreflight
{
    
	public function handle(Request $request, Closure $next){
		    $response = $next($request);
					
			$response->header('Access-Control-Allow-Origin', '*');
			$response->header('Access-Control-Allow-Methods', 'GET', 'POST', 'OPTIONS', 'DELETE', 'PUT');
			$response->header('Access-Control-Allow-Headers', 'X-SRF-TOKEN', 'Content_type');
			$response->header('Access-Control-Allow-Credentials', 'true');
				
		return $response;
		
	}
}
