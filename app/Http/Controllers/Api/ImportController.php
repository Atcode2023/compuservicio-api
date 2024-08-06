<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use App\Imports\InvoiceImport;
use App\Imports\PieceServiceImport;
use App\Imports\ReportServiceImport;
use App\Imports\ServiceImport;
use App\Imports\UserImport;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function import($name)
    {
        try {
            switch ($name) {
                case 'service':
                    Excel::import(new ServiceImport, public_path('storage/servicio.csv'));
                    break;

                case 'cliente':
                    Excel::import(new CustomerImport, public_path('storage/cliente.csv'));
                    break;

                case 'pieces':
                    Excel::import(new PieceServiceImport, public_path('storage/pieza_servi.csv'));
                    break;

                case 'reports':
                    Excel::import(new ReportServiceImport, public_path('storage/repo_servi.csv'));
                    break;

                case 'invoices':
                    Excel::import(new InvoiceImport, public_path('storage/pagos.csv'));
                    break;

                case 'users':
                    Excel::import(new UserImport, public_path('storage/usuarios.csv'));
                    break;
            }

            return 'success';
        } catch (Exception $e) {
            return [$e, $e->getMessage()];
        }
    }
}