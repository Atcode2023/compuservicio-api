<?php

namespace App\Http\Controllers\Api\Services;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CedulaCNEController extends Controller
{
    public function consultarCedula(Request $request){
        $cedula = $request->input('cedula');

        $existingCustomer = Customer::where('ci', $cedula)->first();
        if ($existingCustomer) {
            return response()->json([
                'message' => 'Customer already exists',
                'data' => $existingCustomer
            ]);
        }

        $cedula = preg_replace('/[^0-9]/', '', $cedula);

        $response = Http::get("http://www.cne.gob.ve/web/registro_electoral/ce.php?nacionalidad=V&cedula={$cedula}");
        $html = $response->body();

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
            // Function to extract the value of a node if it exists, otherwise returns null
        $getNodeValue = function ($xpath, $query) {
            $node = $xpath->query($query)->item(0);
            return $node ? trim($node->nodeValue) : null;
        };

        // Extracting voter data
        $extractedCedula = $getNodeValue($xpath, "//td[contains(., 'dula:')]/following-sibling::td[1]/text()");
        $nombre = $getNodeValue($xpath, "//table//tr/td[contains(., 'Nombre:')]/following-sibling::td[1]");
        $estado = $getNodeValue($xpath, "//table//tr/td[contains(., 'Estado:')]/following-sibling::td[1]");
        $municipio = $getNodeValue($xpath, "//table//tr/td[contains(., 'Municipio:')]/following-sibling::td[1]");
        $parroquia = $getNodeValue($xpath, "//table//tr/td[contains(., 'Parroquia:')]/following-sibling::td[1]");
        $centro = $getNodeValue($xpath, "//table//tr/td[contains(., 'Centro:')]/following-sibling::td[1]");
        $direccion = $getNodeValue($xpath, "//table//tr/td[contains(., 'Direcci')]/following-sibling::td[1]");

        // Extracting electoral service information
        $servicioElectoral = $getNodeValue($xpath, "//p[contains(text(), 'Usted fue seleccionado para prestar el Servicio Electoral')]");
        $cargoMesa = $getNodeValue($xpath, "//font[contains(text(), 'Usted fue seleccionado como')]/ancestor::td/following-sibling::td[1]");


        // Creating an array with the extracted data
        $extractedCedula = str_replace("-", "", $extractedCedula);
        $datos = [
            'cedula' => $extractedCedula,
            'nombre' => $nombre,
            'estado' => $estado,
            'municipio' => $municipio,
            'parroquia' => $parroquia,
            'centro' => $centro,
            'direccion' => $direccion,
            'servicioElectoral' => $servicioElectoral,
            'cargoMesa' => $cargoMesa
        ];
        if (!$datos['cedula'] || !$datos['nombre']) {
            return response()->json(['message' => 'Unable to find complete voter data.'], 404);
        }

        $customerHardcoded = [
            'ci' => $datos['cedula'],
            'name' => $datos['nombre'],
        ];

        // dd('testing');
        return response()->json([
            'message' => 'Cédula consultada y guardada correctamente',
            'data' => ($customerHardcoded)
        ]);

    }

}
