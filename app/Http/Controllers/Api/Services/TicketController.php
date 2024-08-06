<?php

namespace App\Http\Controllers\Api\Services;

use App\Http\Controllers\Controller;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;







class TicketController extends Controller
{
    public function ticketInfo(Request $request)
    {

        $rules = [
            'email' => 'email',
            'password' => 'nullable',
            'wifi_name' => 'required',
            'wifi_password' => 'required|min:8',
            'app_password' => 'min:6',
            'hidden_net' => 'nullable',
            'model' => 'nullable'
        ];


        $messages = [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe ser una dirección de correo electrónico válida.',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'wifi_name.required' => 'El nombre de WiFi es requerido.',
            'wifi_password.required' => 'La contraseña de WiFi es requerida.',
            'wifi_password.min' => 'La contraseña de WiFi debe tener al menos :min caracteres.',
            'app_password.required' => 'La contraseña de la aplicación es requerida.',
            'app_password.min' => 'La contraseña de la aplicación debe tener al menos :min caracteres.',
            'hidden_net.required' => 'La red oculta es requerida.',
            'model.required' => 'El modelo es requerido.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        try {
            $email = $request->input('email');
            $password = $request->input('password');
            $wifiName = $request->input('wifi_name');
            $wifiPassword = $request->input('wifi_password');
            $appPassword = $request->input('app_password');
            $hiddenNetwork = $request->input('hidden_net');
            $model = $request->input('model');

            $printer_name = "smb://Guests@alfonso/xprinter";
            $connector = new WindowsPrintConnector($printer_name);
            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("COMPUSERVICIOS C.A.\n");
            $printer->text("Avenida Unda entre calle 5ta y 6ta\n");
            $printer->text("whatsapp: 0412-5558030\n");
            $printer->text("@compuserviciosguanare\n");
            $printer->text("Guanare-Portuguesa\n");
            $printer->text("--------------------------------------------\n");
            $printer->setEmphasis(false);
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(false);
            $printer->text("MODELO DEL ROUTER: $model\n");
            $printer->text("NOMBRE DEL WIFI: $wifiName\n");
            $printer->text("CLAVE DEL WIFI: $wifiPassword\n");
            $printer->text("CORREO: $email\n");
            $printer->text("CLAVE APLICACION: $appPassword\n");
            $printer->text("RED OCULTA: $hiddenNetwork\n");
            $printer->text("CLAVE ACCESO AL ROUTER: $password\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("--------------------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->setEmphasis(true);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(1, 1);
            $printer->text(strtoupper("Gracias por preferirnos!") . "\n");
            $printer->text(strtoupper("Vuelvan Pronto!") . "\n");

            $printer->cut();
            $printer->close();

            return response()->json(['success' => true, 'message' => 'Ticket impreso correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }


}
