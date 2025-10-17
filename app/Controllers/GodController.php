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
use App\Models\CancelModel;
use App\Models\LiburModel;
use App\Models\AksesModel;
use App\Models\AreaModel;
use App\Models\BsModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\UserModel;
use App\Models\BsMesinModel;
use App\Models\MonthlyMcModel;
use App\Models\MachinesModel;

use DateTime;

class GodController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $cancelModel;
    protected $liburModel;
    protected $aksesModel;
    protected $userModel;
    protected $areaModel;
    protected $BsModel;
    protected $BsMesinModel;
    protected $MonthlyMcModel;
    protected $machinesModel;


    public function __construct()
    {

        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->cancelModel = new cancelModel();
        $this->aksesModel = new AksesModel();
        $this->userModel = new UserModel();
        $this->areaModel = new AreaModel();
        $this->BsModel = new BsModel();
        $this->BsMesinModel = new BsMesinModel();
        $this->MonthlyMcModel = new MonthlyMcModel();
        $this->machinesModel = new MachinesModel();

        if ($this->filters   = ['role' => ['capacity', 'planning', 'god', session()->get('role') . '']] != session()->get('role')) {
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
        $orderJalan = $this->bookingModel->getOrderJalan();
        $terimaBooking = $this->bookingModel->getBookingMasuk();
        $mcJalan = $this->jarumModel->mcJalan();
        $totalMc = $this->jarumModel->totalMc();
        $area = $this->jarumModel->getArea();
        $bulan = date('m');
        $buyer = $this->orderModel->getBuyer();
        $yesterday = date('Y-m-d', strtotime('14 days ago'));
        $month = date('F');
        $year = date('Y');

        $data = [
            'role' => session()->get('role'),
            'title' => 'Capacity System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jalan' => $orderJalan,
            'TerimaBooking' => $terimaBooking,
            'mcJalan' => $mcJalan,
            'area' => $area,
            'buyer' => $buyer



        ];
        return view(session()->get('role') . '/index', $data);
    }

    public function booking()
    {
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Booking/booking', $data);
    }
    public function bookingPerJarum($jarum)
    {
        $product = $this->productModel->getJarum($jarum);
        $booking = $this->bookingModel->getDataPerjarum($jarum);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'product' => $product,
            'booking' => $booking

        ];
        return view(session()->get('role') . '/Booking/jarum', $data);
    }

    public function bookingPerBulanJarum($jarum)
    {
        $bulan = $this->bookingModel->getbulan($jarum);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'bulan' => $bulan,
            'jarum' => $jarum,
        ];
        return view(session()->get('role') . '/Booking/bookingbulan', $data);
    }

    public function bookingPerBulanJarumTampil($bulan, $tahun, $jarum)
    {
        $booking = $this->bookingModel->getDataPerjarumbulan($bulan, $tahun, $jarum);
        $product = $this->productModel->getJarum($jarum);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'booking' => $booking,
            'product' => $product,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jarum' => $jarum,
        ];
        return view(session()->get('role') . '/Booking/jarumbulan', $data);
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
        $idProduct = $this->productModel->getId(['prodtype' => $product, 'jarum' => $jarum]);
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
                return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('success', 'Data Berhasil Di Input');
            } else {
                return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('error', 'Data Gagal Di Input');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('error', 'Data Sudah Ada, Silahkan Cek Ulang Kembali Inputanya');
        }
    }
    public function detailbooking($idBooking)
    {
        $needle = $this->bookingModel->getNeedle($idBooking);
        $product = $this->productModel->findAll();
        $booking = $this->bookingModel->getDataById($idBooking);
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $childOrder = $this->orderModel->getChild($idBooking);
        $childBooking = $this->bookingModel->getChild($idBooking);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'booking' => $booking,
            'jarum' => $needle,
            'product' => $product,
            'jenisJarum' => $totalMesin,
            'childOrder' => $childOrder,
            'childBooking' => $childBooking

        ];
        return view(session()->get('role') . '/Booking/detail', $data);
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
            $this->bookingModel->update($id_booking, ['sisa_booking' => $sisa_booking]);
            return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
        } else {

            $inputModel = [
                'tgl_terima_order' => $tgl_turun,
                'no_model' => $no_model,
                'deskripsi' => $deskripsi,
                'id_booking' => $id_booking,
            ];
            $input = $this->orderModel->insert($inputModel);
            if (!$input) {
                return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $id_booking))->withInput()->with('error', 'Gagal Ambil Order');
            } else {
                $id = $id_booking;
                $status = "";
                if ($sisa_booking == "0") {
                    $status = "Habis";
                } else {
                    $status = "Aktif";
                }
                $data = [
                    'role' => session()->get('role'),
                    'sisa_booking' => $sisa_booking,
                    'status' => $status
                ];
                $this->bookingModel->update($id, $data);
                return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
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
        $getid = [
            'prodtype' => $product,
            'jarum' => $jarum
        ];
        $idProduct = $this->productModel->getId($getid);
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
                return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('success', 'Data Berhasil Di Input');
            } else {
                return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('error', 'Data Gagal Di Input');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('error', 'Data Sudah Ada, Silahkan Cek Ulang Kembali Inputanya');
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
                $data = ['role' => session()->get('role'),];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }
                if (!empty($data)) {

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
                    $seam = $data[15];
                    $no_order = $data[18];
                    $tgl_booking = date('Y-m-d');
                    $product_type = $data[2];
                    $sisa = $data[8];
                    $getIdProd = ['prodtype' => $product_type, 'jarum' => $jarum];
                    $idprod = $this->productModel->getId($getIdProd);


                    if ($data[5] == null) {
                        break;
                    } else {
                        $insert = [
                            'no_booking' => $no_booking,
                            'id_product_type' => $idprod,
                            'kd_buyer_booking' => $buyer,
                            'desc' => $desc,
                            'delivery' => $delivery,
                            'opd' => $opd1,
                            'sisa_booking' => $qty,
                            'qty_booking' => $qty,
                            'needle' => $jarum,
                            'seam' => $seam,
                            'no_order' => $no_order,
                            'lead_time' => $lead_time,
                            'tgl_terima_booking' => $tgl_booking
                        ];

                        // $existOrder = $this->bookingModel->existingOrder($no_order);
                        // if (!$existOrder) {
                        $this->bookingModel->insert($insert);
                        // }
                    }
                }
            }
            return redirect()->to(base_url(session()->get('role') . '/databooking'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/databooking'))->with('error', 'No data found in the Excel file');
        }
    }
    public function importpecahbooking()
    {
        $file = $this->request->getFile('excel_file');
        $refId = $this->request->getPost('refid');
        $sisa = $this->request->getPost('sisa');

        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet();
            $startRow = 12; // Ganti dengan nomor baris mulai
            foreach ($spreadsheet->getActiveSheet()->getRowIterator($startRow) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = ['role' => session()->get('role'),];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }
                if (!empty($data)) {

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
                    $seam = $data[15];
                    $no_order = $data[18];
                    $tgl_booking = date('Y-m-d');
                    $product_type = $data[2];

                    $getIdProd = ['prodtype' => $product_type, 'jarum' => $jarum];
                    $idprod = $this->productModel->getId($getIdProd);

                    if ($data[5] == null) {
                        break;
                    } else {
                        $insert = [
                            'no_booking' => $no_booking,
                            'id_product_type' => $idprod,
                            'kd_buyer_booking' => $buyer,
                            'desc' => $desc,
                            'delivery' => $delivery,
                            'opd' => $opd1,
                            'sisa_booking' => $qty,
                            'qty_booking' => $qty,
                            'needle' => $jarum,
                            'seam' => $seam,
                            'no_order' => $no_order,
                            'lead_time' => $lead_time,
                            'tgl_terima_booking' => $tgl_booking,
                            'status' => 'Pecahan',
                            'ref_id' => $refId,
                        ];
                        // $existOrder = $this->bookingModel->existingOrder($no_order);
                        // if (!$existOrder) {
                        $this->bookingModel->insert($insert);
                        $this->bookingModel->update($refId, ['sisa_booking' => $sisa]);
                        // }
                    }
                }
            }
            return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $refId))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $refId))->with('error', 'No data found in the Excel file');
        }
    }
    public function updatebooking($idBooking)
    {

        $data = [
            'role' => session()->get('role'),
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
            return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $idBooking))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $idBooking))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function deletebooking($idBooking)
    {

        $jarum = $this->request->getPost("jarum");
        $id = $idBooking;
        $delete = $this->bookingModel->delete($id);
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }
    public function cancelbooking($idBooking)
    {
        $jarum = $this->request->getPost("jarum");
        $sisa = $this->request->getPost("sisa_booking_remaining");
        $qty_cancel = intval($this->request->getPost("qty_cancel"));
        $alasan = $this->request->getPost("alasan");

        // Prepare data for insertion
        $insert = [
            "id_booking" => $idBooking,
            "qty_cancel" => $qty_cancel,
            "alasan" => $alasan,
        ];

        // Update booking status based on sisa quantity
        if ($sisa == 0) {
            // If sisa is 0, update status to 'Cancel Booking' and set sisa_booking to 0
            $updateData = ['status' => 'Cancel Booking', 'sisa_booking' => 0];
        } else {
            // If sisa is not 0, update status to 'Active' and set sisa_booking to $sisa
            $updateData = ['status' => 'Active', 'sisa_booking' => $sisa];
        }

        // Update booking status and insert cancellation data
        $this->bookingModel->update($idBooking, $updateData);
        $input = $this->cancelModel->insert($insert);

        // Redirect with success or error message
        if ($input) {
            return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('success', 'Bookingan Berhasil Di Cancel');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/databooking/' . $jarum))->withInput()->with('error', 'Gagal Cancel Booking');
        }
    }

    public function getCancelBooking()
    {
        $resultCancelBooking = $this->bookingModel->getCancelBooking();
        $charts = $this->bookingModel->chartCancel();
        $bulan = array_keys($charts['details']);
        $jumlahPembatalan = array_values($charts['totals']);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Summary Cancel Booking',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'details' => $resultCancelBooking,
            'bulan' => $bulan,
            'jumlahPembatalan' => $jumlahPembatalan,
            'totalChart' => $charts['totals']
        ];
        return view(session()->get('role') . '/Booking/cancelbooking', $data);
    }

    public function detailcancelbooking($week, $buyer)
    {

        $resultCancelBooking = $this->bookingModel->getDetailCancelBooking($week, $buyer);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Detail Cancel Booking',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'data' => $resultCancelBooking,
        ];
        return view(session()->get('role') . '/Booking/detailcancelbooking', $data);
    }

    public function getTurunOrder()
    {
        $turunOrder = $this->ApsPerstyleModel->getTurunOrderPerbulan();
    }

    // planning

    public function bookingPlan()
    {
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
        ];
        return view('Planning/Booking/booking', $data);
    }

    public function bookingPerBulanJarumPLan($jarum)
    {
        $bulan = $this->bookingModel->getbulan($jarum);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'bulan' => $bulan,
            'jarum' => $jarum,
        ];
        return view('Planning/Booking/bookingbulan', $data);
    }
    public function bookingPerBulanJarumTampilPlan($bulan, $tahun, $jarum)
    {
        $booking = $this->bookingModel->getDataPerjarumbulan($bulan, $tahun, $jarum);
        $product = $this->productModel->getJarum($jarum);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'booking' => $booking,
            'product' => $product,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jarum' => $jarum,
        ];
        return view('Planning/Booking/jarumbulan', $data);
    }
    public function bookingPerJarumPLan($jarum)
    {
        $product = $this->productModel->getJarum($jarum);
        $booking = $this->bookingModel->getDataPerjarum($jarum);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'product' => $product,
            'booking' => $booking

        ];
        return view('Planning/Booking/jarum', $data);
    }
    public function detailbookingPlan($idBooking)
    {
        $needle = $this->bookingModel->getNeedle($idBooking);
        $product = $this->productModel->findAll();
        $booking = $this->bookingModel->getDataById($idBooking);
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $childOrder = $this->orderModel->getChild($idBooking);
        $childBooking = $this->bookingModel->getChild($idBooking);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'booking' => $booking,
            'jarum' => $needle,
            'product' => $product,
            'jenisJarum' => $totalMesin,
            'childOrder' => $childOrder,
            'childBooking' => $childBooking

        ];
        return view('Planning/Booking/detail', $data);
    }
    public function target()
    {
        $Jarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Target',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'Jarum' => $Jarum,
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Target/index', $data);
    }
    public function targetjarum($jarum)
    {
        $product = $this->productModel
            ->where('jarum', $jarum)
            ->orderBy('id_product_type', 'asc')
            ->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Target by Needle',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'product' => $product,
            'jarum' => $jarum
        ];
        return view(session()->get('role') . '/Target/target', $data);
    }
    public function edittarget()
    {
        $id = $this->request->getPost("id");
        $keterangan = $this->request->getPost("keterangan");
        $jarum = $this->request->getPost("jarum");
        $target = $this->request->getPost("target");

        if ($id) {
            $this->productModel->set('konversi', $target)
                ->set('keterangan', $keterangan)
                ->where('id_product_type', $id)
                ->update();
            return redirect()->to(base_url(session()->get('role') . '/datatargetjarum/' . $jarum))->withInput()->with('success', 'Data Berhasil Diinput');
        }
    }
    public function getTypebyJarum()
    {
        if ($this->request->isAJAX()) {
            $jarum = $this->request->getPost('jarum');

            $productTypes = $this->productModel->getProductTypesByJarum($jarum);
            return $this->response->setJSON($productTypes);
        } else {
            // Jika bukan permintaan AJAX, kembalikan 404
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
    }
    public function uncancelbooking($id)
    {
        $qty_cancel = $this->request->getPost('qty_cancel');
        if ($id) {
            $this->bookingModel->set('sisa_booking', 'sisa_booking + ' . $qty_cancel, false)
                ->set('status', 'Aktif')
                ->where('id_booking', $id)
                ->update();
            $this->cancelModel->where('id_booking', $id)
                ->delete();
            return redirect()->to(base_url(session()->get('role') . '/cancelBooking/'))->withInput()->with('success', 'Data Uncancel Success');
        }
    }

    public function account()
    {
        $userdata = $this->userModel->getData();
        $areadata = $this->areaModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Account management',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'userdata' => $userdata,
            'area' => $areadata

        ];
        return view(session()->get('role') . '/Account/index', $data);
    }
    public function addaccount()
    {
        $insert = [
            'username' => $this->request->getPost("username"),
            'password' => $this->request->getPost("password"),
            'role' => $this->request->getPost("role"),
        ];
        $query = $this->userModel->insert($insert);
        if ($query) {
            return redirect()->to(base_url(session()->get('role') . '/account'))->with('success', 'User Berhasil di input');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/account'))->with('error', 'User Gagal di input');
        }
    }
    public function assignarea()
    {
        $userId = $this->request->getPost("iduser");
        $areaList = $this->request->getPost("areaList");
        if (!empty($areaList)) {
            $db = \Config\Database::connect();
            foreach ($areaList as $areaId) {
                $exists = $this->aksesModel->where(['user_id' => $userId, 'area_id' => $areaId])->first();
                if (!$exists) {
                    $data = [
                        'role' => session()->get('role'),
                        'user_id' => $userId,
                        'area_id' => $areaId,
                    ];
                    $query = "INSERT INTO user_areas (user_id, area_id) VALUES (?, ?)";
                    $db->query($query, [$userId, $areaId]);
                    if (!$db) {
                        return redirect()->to(base_url(session()->get('role') . '/account'))->with('error', 'User Gagal di input');
                    }
                }
            }
            return redirect()->to(base_url(session()->get('role') . '/account'))->with('success', 'User Berhasil di input');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/account'))->with('error', 'Tidak ada data');
        }
    }
    public function deleteaccount($id)
    {
        $delete = $this->userModel->delete($id);
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/account'))->with('success', 'User Berhasil di hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/account'))->with('error', 'User Gagal di hapus');
        }
    }
    public function updateaccount($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Delete akses terkait user_id
        $this->aksesModel->where('user_id', $id)->delete();

        // Data update user
        $field = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role'),
        ];

        // Update data user
        $update = $this->userModel->update($id, $field);
        if ($update) {
            $areaList = $this->request->getPost("areaList");

            if (!empty($areaList)) {
                foreach ($areaList as $areaId) {

                    $data = [
                        'role' => session()->get('role'),
                        'user_id' => $id,
                        'area_id' => $areaId,
                    ];
                    $query = "INSERT INTO user_areas (user_id, area_id) VALUES (?, ?)";
                    $db->query($query, [$id, $areaId]);
                    if (!$db) {
                        return redirect()->to(base_url(session()->get('role') . '/account'))->with('error', 'User Gagal di input');
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return redirect()->to(base_url(session()->get('role') . '/account'))->with('error', 'User Gagal di Update');
            } else {
                return redirect()->to(base_url(session()->get('role') . '/account'))->with('success', 'User Berhasil di Update');
            }
        } else {
            $db->transRollback();
            return redirect()->to(base_url(session()->get('role') . '/account'))->with('error', 'User Gagal di Update');
        }
    }
    public function updateSisa()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(500);

        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            $startRow = 2; // Ganti dengan nomor baris mulai
            $batchSize = 100; // Ukuran batch
            $batchData = [];
            $failedRows = []; // Array untuk menyimpan informasi baris yang gagal
            $db = \Config\Database::connect();
            foreach ($worksheet->getRowIterator($startRow) as $row) {
                $rowIndex = $row->getRowIndex();

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = ['role' => session()->get('role'),];

                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }

                if (!empty($data)) {
                    $batchData[] = ['rowIndex' => $rowIndex, 'data' => $data];
                    // Process batch
                    if (count($batchData) >= $batchSize) {
                        $this->processBatchnew($batchData, $db, $failedRows);
                        $batchData = []; // Reset batch data
                    }
                }
            }

            // Process any remaining data
            if (!empty($batchData)) {
                $this->processBatchnew($batchData, $db, $failedRows);
            }

            // Prepare notification message for failed rows
            if (!empty($failedRows)) {
                $failedRowsStr = implode(', ', $failedRows);
                $errorMessage = "Baris berikut gagal diimpor: $failedRowsStr";
                return redirect()->to(base_url(session()->get('role') . '/produksi'))->with('error', $errorMessage);
            }

            return redirect()->to(base_url(session()->get('role') . '/produksi'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/produksi'))->with('error', 'No data found in the Excel file');
        }
    }
    private function processBatchnew($batchData, $db, &$failedRows)
    {
        $db->transStart();
        foreach ($batchData as $batchItem) {
            $rowIndex = $batchItem['rowIndex'];
            $data = $batchItem['data'];
            try {
                $area = $data[0];
                $no_model = $data[1];
                $style = $data[3];
                $jarum = $data[2];
                $tgl = $data[4]; // Misal ini timestamp (jumlah detik sejak 1970-01-01)
                $delivery = date('Y-m-d', strtotime($tgl));
                $sisa = intval($data[5]);
                $inisial = $data[6];
                $update = [
                    'factory' => $area,
                    'mastermodel' => $no_model,
                    'size' => $style,
                    'delivery' => $delivery,
                ];

                // Fetch data based on model, style, and delivery
                $resetSisa = $this->ApsPerstyleModel->resetSisaDlv($update);

                $getId = $this->ApsPerstyleModel->getIdPerDeliv($update);

                $update = $this->ApsPerstyleModel->update($getId['idapsperstyle'], ['sisa' => $sisa, 'inisial' => $inisial]);
                if (!$update) {
                    $failedRows[] = 'Error on row ' . $rowIndex . ': Gagal Update ';
                }
            } catch (\Exception $e) {
                log_message('error', 'Error in row ' . $rowIndex . ': ' . $e->getMessage());
                $failedRows[] = 'Error on row ' . $rowIndex . ': ' . $e->getMessage();
            }
        }
        $db->transComplete();
    }
    public function dashboardData()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $area = $this->request->getGet('area');
        $detailMc = $this->machinesModel->checkExist($area);
        if (!$bulan || !$tahun) {
            return $this->response->setJSON(['error' => 'Bulan dan Tahun wajib diisi']);
        }

        try {
            // Oper filter ke model kalau tersedia
            $filters = [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'area'  => $area
            ];
            $month = date('F', mktime(0, 0, 0, $bulan, 10)); // 10 = tanggal dummy, bisa berapa aja yg valid
            $judul = $month . '-' . $tahun;
            $targetMonth = 0;
            if (!empty($filters['area'])) {
                $targetMonth = $this->MonthlyMcModel->getTargetArea($judul, $area); // area spesifik
            } else {
                $targetMonth = $this->MonthlyMcModel->getTarget($judul); // target keseluruhan
            }

            $prodYesterday = $this->produksiModel->monthlyProd($filters);
            // $totalProd = $this->produksiModel->totalProdBulan($filters);
            // return $this->response->setJSON($totalProd);

            $bs = $this->BsModel->bsMonthly($filters);
            $bsMesin = $this->BsMesinModel->getTotalKgMonth($filters) ?? 0;
            $direct = $this->produksiModel->directMonthly($filters);
            $target = $this->ApsPerstyleModel->monthlyTarget($filters);
            $hari = $this->produksiModel->hariProduksi($filters);
            $jumhari = $hari['hari'];
            $prodTotal = 0;
            $prodGr = 0;
            $bsGr = 0;
            $bsPcs = $bs['bs'];

            // Siapin data model + size untuk dikirim ke API bulk
            $bulkRequest = [];
            foreach ($prodYesterday as $prd) {
                $key = $prd['mastermodel'] . '_' . $prd['size'];
                $bulkRequest[$key] = [
                    'model' => $prd['mastermodel'],
                    'size'  => $prd['size']
                ];
            }

            // Kirim bulk ke API
            $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getGwBulk';
            $options = [
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\n",
                    'content' => json_encode(array_values($bulkRequest))
                ]
            ];
            $context = stream_context_create($options);
            $response = file_get_contents($apiUrl, false, $context);
            $gwData = json_decode($response, true);

            // Index ulang hasil bulk berdasarkan 'model_size' key

            $gwMap = [];
            foreach ($gwData as $item) {
                $key = $item['model'] . '_' . $item['size'];
                $gwMap[$key] = isset($item['gw']['gw']) ? (float)$item['gw']['gw'] : 0;
            }

            // Hitung produksi & gramasi
            foreach ($prodYesterday as $prd) {
                $key = $prd['mastermodel'] . '_' . $prd['size'];
                $gw = isset($gwMap[$key]) ? $gwMap[$key] : 0;

                $prodQty = (int)$prd['prod'];
                $prodGw = $gw * $prodQty;

                $prodTotal += $prodQty;
                $prodGr += $prodGw;
            }


            if (empty($prodYesterday)) {
                $deffectRate = 0;
                $pph = 0;
                $quality = 0;
                $percentage = 0;
                $productivity = 0;
            } else {
                $deffectRate = (($bsMesin['qty_gram'] / ($bsMesin['qty_gram'] + $prodGr)) + ($bsPcs / $prodTotal)) * 100;
                $pph = round(($prodTotal / 2) / ($direct / 24));
                $good = 100 - $deffectRate;
                $quality = ($good / $prodTotal) * 100;
                $prod = $target['qty'] - $target['sisa'];
                $percentage =  ($prod / $target['qty']) * 100;
                $productivity =  (($prodTotal / 24) / ($targetMonth['total_output'] * (int)$jumhari)) * 100;
            }

            $data = [
                'deffect' => $deffectRate,
                'bs' => $bsPcs ?? 0,
                'output' => $prodTotal ?? 0,
                'pph' => $pph,
                'qty' => $target['qty'] ?? 0,
                'sisa' => $target['sisa'] ?? 0,
                'quality' => $quality,
                'percentage' => $percentage,
                'productivity' =>   round($productivity),
                'prodtotal' => ($prodTotal / 24),
                'targetday' => $targetMonth['total_output'],
                'targetOutput' => $targetMonth['total_output'] * (int)$jumhari,
                'hari' => $jumhari,
                'planmc' => $targetMonth['mesin'],
                'mesinDetail' => $detailMc
            ];

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
    public function getDailyProd()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $area = $this->request->getGet('area');

        if (!$bulan || !$tahun) {
            return $this->response->setJSON(['error' => 'Bulan dan Tahun wajib diisi']);
        }
        try {
            // Oper filter ke model kalau tersedia
            $filters = [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'area'  => $area
            ];
            $month = date('F', mktime(0, 0, 0, $bulan, 10)); // 10 = tanggal dummy, bisa berapa aja yg valid
            $judul = $month . '-' . $tahun;
            $targetMonth = 0;
            if (!empty($filters['area'])) {
                $targetMonth = $this->MonthlyMcModel->getTargetArea($judul, $area); // area spesifik
            } else {
                $targetMonth = $this->MonthlyMcModel->getTarget($judul); // target keseluruhan
            }

            $dailyProd = $this->produksiModel->dailyProductivity($filters);
            $bulkRequest = [];
            foreach ($dailyProd as $prd) {
                $key = $prd['mastermodel'] . '_' . $prd['size'];
                $bulkRequest[$key] = [
                    'model' => $prd['mastermodel'],
                    'size'  => $prd['size']
                ];
            }
            // Kirim bulk ke API
            $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getGwBulk';
            $options = [
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\n",
                    'content' => json_encode(array_values($bulkRequest))
                ]
            ];
            $context = stream_context_create($options);
            $response = file_get_contents($apiUrl, false, $context);
            $gwData = json_decode($response, true);

            // Index ulang hasil bulk berdasarkan 'model_size' key
            $gwMap = [];
            foreach ($gwData as $item) {
                $key = $item['model'] . '_' . $item['size'];
                $gwMap[$key] = isset($item['gw']['gw']) ? (float)$item['gw']['gw'] : 0;
            }
            $summaryByTanggal = [];
            $groupedProd = [];
            $tanggalList = [];
            foreach ($dailyProd as $prd) {
                $tanggal = $prd['tgl_produksi'];
                $model = $prd['mastermodel'];
                $size = $prd['size'];
                $qty = (int)$prd['prod'];

                $key = $tanggal . '_' . $model . '_' . $size;
                $tanggalList[$tanggal] = true;

                if (!isset($groupedProd[$key])) {
                    $groupedProd[$key] = [
                        'tanggal' => $tanggal,
                        'model' => $model,
                        'size' => $size,
                        'qty' => 0
                    ];
                }
                $groupedProd[$key]['qty'] += $qty;
            }
            foreach ($groupedProd as $item) {
                $tanggal = $item['tanggal'];
                $model = $item['model'];
                $size = $item['size'];
                $qty = $item['qty'];

                $keyGw = $model . '_' . $size;
                $gw = isset($gwMap[$keyGw]) ? $gwMap[$keyGw] : 0;

                // Siapkan filter BS
                $fill = [
                    'no_model' =>  $prd['mastermodel'],
                    'style' =>  $prd['size'],
                    'area'  => $area,
                    'tanggal' => $tanggal
                ];

                if (!isset($summaryByTanggal[$tanggal])) {
                    $bsMesinDaily = $this->BsMesinModel->bsTanggal($fill);
                    $bsSetting = $this->BsModel->getBsPertanggal($fill);
                    $summaryByTanggal[$tanggal] = [
                        'prodTotal' => 0,
                        'prodGr' => 0,
                        'bsmesin' => $bsMesinDaily['qty_gram'] ?? 0,
                        'bsSetting' => $bsSetting,
                    ];
                }
                // Hitung produksi
                $prodGw = $gw * $qty;

                $summaryByTanggal[$tanggal]['prodTotal'] += $qty;
                $summaryByTanggal[$tanggal]['prodGr'] += $prodGw;
                $summaryByTanggal[$tanggal]['gw'] = $gw;
            }

            // Post-process: convert satuan dan hitung persentase
            foreach ($summaryByTanggal as $tanggal => &$data) {
                $data['tanggal'] = $tanggal;
                $data['prodTotal'] = $data['prodTotal'] / 24; // dz
                $data['prodGr'] = $data['prodGr'] / 1000; // kg
                $data['bsmesin'] = $data['bsmesin'] / 1000; // kg
                $data['bsSetting'] = $data['bsSetting'] / 24; //dz
                $data['target'] = $targetMonth['total_output'] ?? 0;
                $data['productivity'] = ($data['target'] > 0) ? ($data['prodTotal'] / $data['target']) * 100 : 0;

                $totalProdGr = $data['prodGr'];
                $totalBsMesin = $data['bsmesin'];
                $totalBsSetting = $data['bsSetting'];
                $totalProd = $data['prodTotal'];

                $data['deffectRate'] = ($totalProdGr > 0)
                    ? (($totalBsMesin / ($totalBsMesin + $totalProdGr)) + ($totalBsSetting / $totalProd)) * 100
                    : 0;
            }
            unset($data);

            // Sort berdasarkan tanggal
            ksort($summaryByTanggal);

            // Return JSON response
            return $this->response->setJSON(array_values($summaryByTanggal));
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function importMesin()
    {
        $file = $this->request->getFile('file');
        // dd($file);
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xls', 'xlsx', 'csv'])) {
            return redirect()->back()->with('error', 'Format harus .xls/.xlsx/.csv');
        }

        try {
            // Baca spreadsheet
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getSheet(0); // sheet pertama: "mesin"
            $rows  = $sheet->toArray(null, true, true, true);

            // Validasi header
            $header = array_map('trim', $rows[1] ?? []);
            $expected = ['no_mc', 'jarum', 'brand', 'dram', 'kode', 'tahun', 'status', 'area'];
            $mapIndex = []; // peta kolom -> index
            foreach ($header as $idx => $name) {
                $name = strtolower($name);
                if (in_array($name, $expected)) {
                    $mapIndex[$name] = $idx; // ex: 'A','B',...
                }
            }
            foreach ($expected as $col) {
                if (!isset($mapIndex[$col])) {
                    return redirect()->back()->with('error', "Header kolom '$col' tidak ditemukan.");
                }
            }

            $model = new MachinesModel();
            $db = \Config\Database::connect();

            // Kumpulkan data
            $data = [];
            $now  = date('Y-m-d H:i:s');
            $rowCount = count($rows);

            for ($i = 2; $i <= $rowCount; $i++) {
                $r = $rows[$i];

                // Ambil per kolom pakai map index
                $no_mc  = (int) trim((string)($r[$mapIndex['no_mc']] ?? ''));
                $jarum  = trim((string)($r[$mapIndex['jarum']] ?? ''));
                $brand  = trim((string)($r[$mapIndex['brand']] ?? ''));
                $dram   = trim((string)($r[$mapIndex['dram']] ?? ''));
                $kode   = trim((string)($r[$mapIndex['kode']] ?? ''));
                $tahun  = (int) trim((string)($r[$mapIndex['tahun']] ?? '0'));
                $status = trim((string)($r[$mapIndex['status']] ?? 'idle'));
                $area = trim((string)($r[$mapIndex['area']] ?? ''));

                // Skip baris kosong
                if ($no_mc === 0 && $jarum === '' && $brand === '' && $kode === '') continue;

                // Validasi minimal
                if ($no_mc <= 0 || $jarum === '' || $brand === '' || $kode === '' || $tahun <= 0) {
                    return redirect()->back()->with('error', "Baris #$i tidak valid. Cek no_mc/jarum/brand/kode/tahun.");
                }

                $data[] = [
                    'no_mc'      => $no_mc,
                    'jarum'      => $jarum,
                    'brand'      => $brand,
                    'dram'       => ($dram === '' ? null : $dram),
                    'kode'       => $kode,
                    'tahun'      => $tahun,
                    'status'     => ($status === '' ? 'idle' : $status),
                    'area'       => $area,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            // dd ($data);
            if (empty($data)) {
                return redirect()->back()->with('error', 'Tidak ada data yang bisa diimport.');
            }

            // === STRATEGI IMPORT ===
            // Opsi A (cepat & aman): upsert pakai ON DUPLICATE KEY (butuh UNIQUE no_mc)
            // Jika MySQL, kita pakai query mentah sekali dorong per-chunk.

            $builder = $db->table('machines');
            $chunks = array_chunk($data, 500);

            foreach ($chunks as $chunk) {
                // Build "INSERT ... ON DUPLICATE KEY UPDATE ..."
                $cols = ['no_mc', 'jarum', 'brand', 'dram', 'kode', 'tahun', 'status', 'area', 'created_at', 'updated_at'];
                $placeholders = '(' . rtrim(str_repeat('?,', count($cols)), ',') . ')';
                $valuesSql = implode(',', array_fill(0, count($chunk), $placeholders));

                $sql = "INSERT INTO machines (" . implode(',', $cols) . ") VALUES $valuesSql
                        ON DUPLICATE KEY UPDATE
                            jarum=VALUES(jarum),
                            brand=VALUES(brand),
                            dram=VALUES(dram),
                            kode=VALUES(kode),
                            tahun=VALUES(tahun),
                            status=VALUES(status),
                            area=VALUES(area),
                            updated_at=VALUES(updated_at)";

                $binds = [];
                foreach ($chunk as $row) {
                    foreach ($cols as $c) $binds[] = $row[$c] ?? null;
                }

                $db->query($sql, $binds);
            }

            return redirect()->back()->with('success', 'Import selesai: ' . count($data) . ' baris diproses.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
