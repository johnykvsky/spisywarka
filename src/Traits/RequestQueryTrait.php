<?php
namespace App\Traits;

use Symfony\Component\HttpFoundation\Request;

trait RequestQueryTrait
{

   /**
    * @param  Request $request
    * @param  string $parameter
    * @param  bool $urlDecode
    * @return string|null
    */
   public function getFromRequest(Request $request, string $parameter, bool $urlDecode = false): ?string
   {
        $param = $request->query->get($parameter);
        
        if ($urlDecode) {
            $param = urldecode($param);
        }

        $searchQuery = filter_var($param, FILTER_SANITIZE_STRING);

        return !empty($searchQuery) ? $searchQuery : null;
   }
}
