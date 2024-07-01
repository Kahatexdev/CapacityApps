<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\ProduksiModel;
use App\Models\LiburModel;
use LengthException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};

class SummaryController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
        if ($this->filters   = ['role' => ['capacity']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();
    }
    protected function isLogedin()
    {
        if (!session()->get('id_user')) {
            return redirect()->to(base_url('/login'));
        }
    }
    public function index()
    {
        //
    }
    public function summaryPerTanggal()
    {
        helper('excel');

        // Ambil data dari formulir
        $noModel = $this->request->getPost('no_model');
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');

        // Tangani unggahan berkas
        $file = $this->request->getFile('file');
        if ($file->isValid() && !$file->hasMoved()) {
            $filePath = $file->store('uploads'); // Menyimpan berkas di writable/uploads
            $filePath = WRITEPATH . 'uploads/' . $file->getName();

            // Muat berkas Excel yang diunggah
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Buat spreadsheet baru
            $newSpreadsheet = new Spreadsheet();
            $newSheet = $newSpreadsheet->getActiveSheet();

            // Filter data berdasarkan input formulir (logika contoh, sesuaikan sesuai kebutuhan)
            $rowIndex = 1;
            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $noModelCell = $cellIterator->current(); // Asumsikan No Model ada di kolom pertama
                $cellIterator->next();
                $dateCell = $cellIterator->current(); // Sesuaikan dengan kolom tanggal yang benar

                $rowNoModel = $noModelCell->getValue();
                $rowDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateCell->getValue())->format('Y-m-d');

                if (($noModel === '' || $rowNoModel == $noModel) && 
                    ($awal === '' || $rowDate >= $awal) && 
                    ($akhir === '' || $rowDate <= $akhir)) {

                    $colIndex = 'A';
                    foreach ($cellIterator as $cell) {
                        $newSheet->setCellValue($colIndex . $rowIndex, $cell->getValue());
                        $colIndex++;
                    }
                    $rowIndex++;
                }
            }

            // Simpan spreadsheet baru
            $filename = 'summary_report.xlsx';
            $writer = new Xlsx($newSpreadsheet);
            $newFilePath = WRITEPATH . 'uploads/' . $filename;
            $writer->save($newFilePath);

            // Kembalikan berkas sebagai unduhan
            return $this->response->download($newFilePath, null)->setFileName($filename);
        } else {
            return redirect()->back()->with('error', 'Unggahan berkas gagal.');
        }
    }
}
