<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Imports\CustomerImport;
use App\Imports\InvoiceImport;
use App\Imports\PieceServiceImport;
use App\Imports\ReportServiceImport;
use App\Imports\ServiceImport;
use App\Imports\UserImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MasterSeeder::class);

        /* Excel::import(new UserImport, public_path('storage/usuarios.csv'));
        Excel::import(new CustomerImport, public_path('storage/cliente.csv'));
        Excel::import(new ServiceImport, public_path('storage/servicio.csv'));
        Excel::import(new PieceServiceImport, public_path('storage/pieza_servi.csv'));
        Excel::import(new ReportServiceImport, public_path('storage/repo_servi.csv'));
        Excel::import(new InvoiceImport, public_path('storage/pagos.csv')); */
    }
}
