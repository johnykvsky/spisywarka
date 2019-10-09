<?php
namespace App\Traits;

use Symfony\Component\HttpFoundation\Request;

trait RequestQueryTrait
{
   public function getFromRequest(Request $request, string $parameter, $urlDecode = false)
   {
        $param = $request->query->get($parameter);
        
        if ($urlDecode) {
            $param = urldecode($param);
        }

        $searchQuery = filter_var($param, FILTER_SANITIZE_STRING);

        return !empty($searchQuery) ? $searchQuery : null;
   }
}
