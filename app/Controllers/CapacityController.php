<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;


use PhpOffice\PhpSpreadsheet\IOFactory;

class CapacityController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $bookingModel;
    protected $orderModel;

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->orderModel = new OrderModel();
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

        $data = [
            'title' => 'Capacity System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',

        ];
        return view('Capacity/index', $data);
    }


    public function booking()
    {
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
        ];
        return view('Capacity/Booking/booking', $data);
    }
    public function bookingPerJarum($jarum)
    {
        $product = $this->productModel->findAll();
        $booking = $this->bookingModel->getDataPerjarum($jarum);

        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'jarum' => $jarum,
            'product' => $product,
            'booking' => $booking

        ];
        return view('Capacity/Booking/jarum', $data);
    }
    public function OrderPerJarum($jarum)
    {
        $tampilperjarum = $this->orderModel->findAll();
        $tampilperdelivery = $this->orderModel->tampilPerdelivery();
        $product = $this->productModel->findAll();
        $booking = $data = [
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'jarum' => $jarum,
            'tampildata' => $tampilperdelivery,
            'product' => $product,

        ];
        return view('Capacity/Order/jarum', $data);
    }
    public function inputbooking()
    {
        $jarum = $this->request->getPost("jarum");
        $tglbk = $this->request->getPost("tgl_booking");
        $no_order = $this->request->getPost("no_order");
        $no_pdk = $this->request->getPost("no_pdk");
        $desc = $this->request->getPost("desc");
        $seam = $this->request->getPost("seam");
        $opd = $this->request->getPost("opd");
        $shipment = $this->request->getPost("shipment");
        $qty = $this->request->getPost("qty");
        $product = $this->request->getPost("productType");
        $idProduct = $this->productModel->getId($product);
        $buyer = $this->request->getPost("buyer");
        $leadTime = $this->request->getPost("lead");


        $validate = [
            'no_order' => $no_order,
            'no_pdk' => $no_pdk
        ];
        $check = $this->bookingModel->checkExist($validate);
        if (!$check) {
            $input = [
                'tgl_terima_booking' => $tglbk,
                'kd_buyer_booking' => $buyer,
                'id_product_type' => $idProduct,
                'no_order' => $no_order,
                'no_booking' => $no_pdk,
                'desc' => $desc,
                'opd' => $opd,
                'delivery' => $shipment,
                'qty_booking' => $qty,
                'sisa_booking' => $qty,
                'needle' => $jarum,
                'seam' => $seam,
                'lead_time' => $leadTime
            ];
            $insert =   $this->bookingModel->insert($input);
            if ($insert) {
                return redirect()->to(base_url('/capacity/databooking/' . $jarum))->withInput()->with('success', 'Data Berhasil Di Input');
            } else {
                return redirect()->to(base_url('/capacity/databooking/' . $jarum))->withInput()->with('error', 'Data Gagal Di Input');
            }
        } else {
            return redirect()->to(base_url('/capacity/databooking/' . $jarum))->withInput()->with('error', 'Data Sudah Ada, Silahkan Cek Ulang Kembali Inputanya');
        }
    }
    public function detailbooking($idBooking)
    {
        $needle = $this->bookingModel->getNeedle($idBooking);
        $product = $this->productModel->findAll();
        $booking = $this->bookingModel->getDataById($idBooking);
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'booking' => $booking,
            'jarum' => $needle,
            'product' => $product,
            'jenisJarum' => $totalMesin

        ];
        return view('Capacity/Booking/detail', $data);
    }
    public function inputOrder()
    {
        $tgl_turun = $this->request->getPost("tgl_turun");
        $no_model = $this->request->getPost("no_model");
        $no_booking = $this->request->getPost("no_booking");
        $deskripsi = $this->request->getPost("deskripsi");
        $sisa_booking = $this->request->getPost("sisa_booking_akhir");
        $id_booking = $this->request->getPost("id_booking");
        $jarum = $this->request->getPost("jarum");

        $check = $this->orderModel->checkExist($no_model);
        if ($check) {
            return redirect()->to(base_url('/capacity/detailbooking/' . $id_booking))->withInput()->with('error', 'No Model Sudah ada');
        } else {

            $inputModel = [
                'tgl_terima_order' => $tgl_turun,
                'no_model' => $no_model,
                'deskripsi' => $deskripsi,
                'id_booking' => $id_booking,
            ];
            $input = $this->orderModel->insert($inputModel);
            if (!$input) {
                return redirect()->to(base_url('capacity/detailbooking/' . $id_booking))->withInput()->with('error', 'Gagal Ambil Order');
            } else {
                $id = $id_booking;
                $data = [
                    'sisa_booking' => $sisa_booking,
                    'status' => 'Aktif'
                ];
                $this->bookingModel->update($id, $data);
                return redirect()->to(base_url('capacity/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
            }
        }
    }
    public function pecahbooking($id_booking)
    {

        $jarum = $this->request->getPost("jarum");
        $tglbk = $this->request->getPost("tgl_booking");
        $no_order = $this->request->getPost("no_order");
        $no_pdk = $this->request->getPost("no_booking");
        $desc = $this->request->getPost("desc");
        $seam = $this->request->getPost("seam");
        $opd = $this->request->getPost("opd");
        $shipment = $this->request->getPost("delivery");
        $qty = $this->request->getPost("qty");
        $product = $this->request->getPost("productType");
        $idProduct = $this->productModel->getId($product);
        $buyer = $this->request->getPost("buyer");
        $leadTime = $this->request->getPost("lead");
        $refId = $id_booking;



        $validate = [
            'no_order' => $no_order,
            'no_pdk' => $no_pdk
        ];
        $check = $this->bookingModel->checkExist($validate);
        if (!$check) {
            $input = [
                'tgl_terima_booking' => $tglbk,
                'kd_buyer_booking' => $buyer,
                'id_product_type' => $idProduct,
                'no_order' => $no_order,
                'no_booking' => $no_pdk,
                'desc' => $desc,
                'opd' => $opd,
                'delivery' => $shipment,
                'qty_booking' => $qty,
                'sisa_booking' => $qty,
                'needle' => $jarum,
                'seam' => $seam,
                'lead_time' => $leadTime,
                'status' => 'Pecahan',
                'ref_id' => $refId
            ];
            $insert =   $this->bookingModel->insert($input);

            if ($insert) {
                $this->bookingModel->update($id_booking, ['sisa_booking' => $this->request->getPost("sisa")]);
                return redirect()->to(base_url('/capacity/databooking/' . $jarum))->withInput()->with('success', 'Data Berhasil Di Input');
            } else {
                return redirect()->to(base_url('/capacity/databooking/' . $jarum))->withInput()->with('error', 'Data Gagal Di Input');
            }
        } else {
            return redirect()->to(base_url('/capacity/databooking/' . $jarum))->withInput()->with('error', 'Data Sudah Ada, Silahkan Cek Ulang Kembali Inputanya');
        }
    }
    public function importbooking()
    {
        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet();
            $startRow = 12; // Ganti dengan nomor baris mulai
            foreach ($spreadsheet->getActiveSheet()->getRowIterator($startRow) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }
                if (!empty($data)) {
                    $product_type = $data[2];
                    $idprod = $this->productModel->getId($product_type);
                    $no_booking = $data[0];
                    $buyer = $data[3];
                    $desc = $data[5];
                    $shipment = $data[6];
                    $unixTime = ($shipment - 25569) * 86400;
                    $delivery = date('Y-m-d', $unixTime);
                    $qty = $data[7];
                    $opd = $data[9];
                    $unixOpd = ($opd - 25569) * 86400;
                    $opd1 = date('Y-m-d', $unixOpd);
                    $jarum = $data[14];
                    $lead_time = $data[16];
                    $seam = $data[17];
                    $no_order = $data[18];
                    $tgl_booking = date('Y-m-d');

                    if ($data[0] == null) {
                        break;
                    } else {
                        $insert = [
                            'no_booking' => $no_booking,
                            'id_product_type' => $idprod,
                            'kd_buyer_booking' => $buyer,
                            'desc' => $desc,
                            'delivery' => $delivery,
                            'opd' => $opd1,
                            'qty_booking' => $qty,
                            'sisa_booking' => $qty,
                            'needle' => $jarum,
                            'seam' => $seam,
                            'no_order' => $no_order,
                            'lead_time' => $lead_time,
                            'tgl_terima_booking' => $tgl_booking
                        ];
                        $existOrder = $this->bookingModel->existingOrder($no_order);
                        if (!$existOrder) {
                            $this->bookingModel->insert($insert);
                        }
                    }
                }
            }
            return redirect()->to(base_url('/capacity/databooking'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url('/capacity/databooking'))->with('error', 'No data found in the Excel file');
        }
    }

    public function importModel()
    {
        $request = \Config\Services::request();
        helper(['form', 'url']);
        $nomodel = $this->request->getVar('no_model');
        $file_excel = $this->request->getFile('fileexcel');
        $ext = $file_excel->getClientExtension();
        if ($ext == 'xls') {
            $render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        } else {
            $render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
        $spreadsheet = $render->load($file_excel);

        $data = $spreadsheet->getActiveSheet()->toArray();
        $db = \Config\Database::connect();

        foreach ($data as $x => $row) {
            if ($x == 0) {
                continue;
            }

            $recordID = $row[0];
            $delFlag = $row[1];
            $articleNo = $row[2];
            $merchandiser = $row[3];
            $priority = $row[4];
            $producttype = $row[5];
            $idProduct = $this->productModel->getId($producttype);
            $factorycd = $row[6];
            $custCode = $row[7];
            $customerStyle = $row[8];
            $style = $row[9];
            $description = $row[10];
            $delivery = $row[11];
            $rdelivery = str_replace('/', '-', (substr($delivery, -10)));
            $delivery2 = date('Y-m-d', strtotime($rdelivery));
            $qty = $row[12];
            $qtyset = $row[13];
            $isfirmorder = $row[14];
            $remarks = $row[15];
            $shipMode = $row[16];
            $country = $row[17];
            $color = $row[18];
            $size = $row[19];
            $sam = $row[20];
            $unitprice = $row[21];
            $machinetypeid = $row[22];
            $seam = $row[23];
            $leadtime = $row[24];
            $processRoute = $row[25];
            $lcoDate = $row[26];
            $rlcoDate = str_replace('/', '-', (substr($lcoDate, -10)));
            $lcoDate2 = date('Y-m-d', strtotime($rlcoDate));
            $no_model = $row[27];
            $area = $row[28];
            $orderNo = $row[29];
            $custOrder = $row[30];


            $simpandata = [
                'recordID' => $recordID,
                'articleNo' => $articleNo,
                'delivery' => $delivery2,
                'qty' => $qty,
                'country' => $country,
                'color' => $color,
                'size' => $size,
                'smv' => $sam,
                'machinetypeid' => $machinetypeid,
                'processRoute' => $processRoute,
                'lcoDate' => $lcoDate2,
                'no_model' => $nomodel,
            ];
            $db->table('aps_order_report')->insert($simpandata);
        }
        $updateData = [
            'seam' => $processRoute,
            'id_product_type' => $idProduct,
            'kd_buyer_order' => $custCode,
            'leadtime' => $leadtime,
            'description' => $description
        ];

        $db->table('data_model')
            ->where('no_model', $nomodel)
            ->update($updateData);

        $query = $db->query("INSERT INTO apsperstyle
		SELECT DISTINCT '',aps_order_report.machinetypeid,aps_order_report.no_model,aps_order_report.size,aps_order_report.delivery,SUM(aps_order_report.qty) AS qty,SUM(aps_order_report.qty) AS sisa,data_model.seam,'BELUM ADA AREAL'
		from aps_order_report JOIN data_model
		ON aps_order_report.no_model=data_model.no_model
		WHERE aps_order_report.no_model = '$nomodel'
		GROUP BY delivery,size,no_model");

        $db->table('aps_order_report')
        ->where('no_model', $nomodel)
        ->delete();

        if ($query) {
            return redirect()->to(base_url('capacity'))->withInput()->with('success', 'Data Berhasil Diinput');
        } else {
        }
    }
    public function order()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'TotalMesin' => $totalMesin,
        ];
        return view('Capacity/Order/order', $data);
    }
    public function produksi()
    {
        $data = [
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
        ];
        return view('Capacity/Produksi/produksi', $data);
    }
    public function updatebooking($idBooking)
    {

        $data = [
            'no_booking' =>  $this->request->getPost("no_booking"),
            'desc' => $this->request->getPost("desc"),
            'opd' =>  $this->request->getPost("opd"),
            'delivery' => $this->request->getPost("delivery"),
            'lead_time' => $this->request->getPost("lead"),
            'qty_booking' => $this->request->getPost("qty"),
            'sisa_booking' => $this->request->getPost("sisa")
        ];
        $id = $idBooking;
        $update = $this->bookingModel->update($id, $data);
        if ($update) {
            return redirect()->to(base_url('capacity/detailbooking/' . $idBooking))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url('capacity/detailbooking/' . $idBooking))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function deletebooking($idBooking)
    {

        $jarum = $this->request->getPost("jarum");
        $id = $idBooking;
        $delete = $this->bookingModel->delete($id);
        if ($delete) {
            return redirect()->to(base_url('capacity/databooking/' . $jarum))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url('capacity/databooking/' . $jarum))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }
    public function cancelbooking($idBooking)
    {

        $jarum = $this->request->getPost("jarum");
        $id = $idBooking;
        $cancel = $this->bookingModel->update($id, ['status' => 'Cancel Booking']);
        if ($cancel) {
            return redirect()->to(base_url('capacity/databooking/' . $jarum))->withInput()->with('success', 'Bookingan Berhasil Di Cancel');
        } else {
            return redirect()->to(base_url('capacity/databooking/' . $jarum))->withInput()->with('error', 'Gagal Cancek Booking');
        }
    }
}
