<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Seeds\ProductType;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\ProduksiModel;
use App\Models\CancelModel;
use App\Models\TransferModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BookingController extends BaseController
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
    public function __construct()
    {

        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->cancelModel = new cancelModel();
        if ($this->filters   = ['role' => ['capacity', session()->get('role') . '', 'god', 'sudo']] != session()->get('role')) {
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

    public function bookingPerBulanJarum($needle)
    {
        $pos = strpos($needle, '-');
        if ($pos) {
            $jarum = substr($needle, 0, $pos);
        } else {
            $jarum = $needle;
        }
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

    public function allbooking()
    {
        $booking = $this->bookingModel->getAllData();
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
        ];
        return view(session()->get('role') . '/Booking/allbooking', $data);
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
            'no_pdk' => $no_pdk,
            'tgl_terima_booking' => $tglbk,
            'delivery' => $shipment
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
        $product = $this->productModel->getProdType();
        $booking = $this->bookingModel->getDataById($idBooking);
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $childOrder = $this->orderModel->getChild($idBooking);
        $childBooking = $this->bookingModel->getChild($idBooking);
        $transferModel = new TransferModel();
        $transferData = $transferModel->getTransferData($idBooking);
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
            'childBooking' => $childBooking,
            'transferData' => $transferData

        ];
        // dd($data);
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
            'no_pdk' => $no_pdk,
            'tgl_terima_booking' => $tglbk,
            'delivery' => $shipment
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
                $cekKeterangan = $this->bookingModel->select('keterangan')->where('id_booking', $id_booking)->first();
                $oldKeterangan = $cekKeterangan['keterangan'] ?? ''; // kalau null, fallback ke string kosong
                $keterangan = $oldKeterangan . ' | ' . $this->request->getPost('keterangan');
                $this->bookingModel->update($id_booking, ['sisa_booking' => $this->request->getPost("sisa"), 'keterangan' => $keterangan,]);
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
                $baris = $row->getRowIndex();
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
                    if (!$idprod) {
                        return redirect()->to(base_url(session()->get('role') . '/databooking'))->withInput()->with('error', 'Data gagal di import, Target tidak di temukan silahkan' . $jarum . '-' . $product_type . ' pada baris ' . $baris);
                    }


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
        $keterangan = $this->request->getPost('keterangan');
        // dd($keterangan);
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
                        $cekKeterangan = $this->bookingModel->select('keterangan')->where('id_booking', $refId)->first();
                        $oldKeterangan = $cekKeterangan['keterangan'] ?? '';
                        $ket = $oldKeterangan . ' | ' . $keterangan;
                        $this->bookingModel->update($refId, ['sisa_booking' => $sisa, 'keterangan' => $ket]);
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

        $needle = $this->request->getPost("jarum");
        $productType = $this->request->getPost('productType');

        // dd($this->request->getPost("jarum"));

        $getId = [
            'jarum' => $needle,
            'prodtype' => $productType
        ];
        $idProdtype = $this->productModel->getId($getId);
        $data = [
            'role' => session()->get('role'),
            'no_order' => $this->request->getPost("no_order"),
            'no_booking' =>  $this->request->getPost("no_booking"),
            'desc' => $this->request->getPost("desc"),
            'needle' => $needle,
            'id_product_type' => $idProdtype,
            'opd' =>  $this->request->getPost("opd"),
            'delivery' => $this->request->getPost("delivery"),
            'lead_time' => $this->request->getPost("lead"),
            'qty_booking' => $this->request->getPost("qty"),
            'sisa_booking' => $this->request->getPost("sisa"),
            'keterangan' => $this->request->getPost("keterangan")
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
        return view(session()->get('role') . '/Booking/booking', $data);
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
        return view(session()->get('role') . '/Booking/bookingbulan', $data);
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
        return view(session()->get('role') . '/Booking/jarumbulan', $data);
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
        return view(session()->get('role') . '/Booking/jarum', $data);
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
        return view(session()->get('role') . '/Booking/detail', $data);
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
        $product = $this->request->getPost("producttype");
        $keterangan = $this->request->getPost("keterangan");
        $jarum = $this->request->getPost("jarum");
        $target = $this->request->getPost("target");

        if ($id) {
            $this->productModel->set('konversi', $target)
                ->set('keterangan', $keterangan)
                ->set('product_type', $product)
                ->where('id_product_type', $id)
                ->update();
            return redirect()->to(base_url(session()->get('role') . '/datatargetjarum/' . $jarum))->withInput()->with('success', 'Data Has Been Updated');
        }
    }
    public function addtarget()
    {
        $keterangan = $this->request->getPost("keterangan");
        $jarum = $this->request->getPost("jarum");
        $target = $this->request->getPost("target");
        $product = $this->request->getPost("producttype");

        $input = [
            'konversi' => $target,
            'product_type' => $product,
            'keterangan' => $keterangan,
            'jarum' => $jarum
        ];
        $insert =   $this->productModel->insert($input);
        if ($insert) {
            return redirect()->to(base_url(session()->get('role') . '/datatargetjarum/' . $jarum))->withInput()->with('success', 'Data Berhasil Diinput');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datatargetjarum/' . $jarum))->withInput()->with('error', 'Data Gagal Diinput');
        }
    }
    public function deletetarget()
    {
        $id = $this->request->getPost("id");
        $jarum = $this->request->getPost("jarum");
        $del = $this->productModel->delete($id);
        if ($del) {
            return redirect()->to(base_url(session()->get('role') . '/datatargetjarum/' . $jarum))->withInput()->with('success', 'Data Berhasil Dihapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datatargetjarum/' . $jarum))->withInput()->with('error', 'Data Gagal Dihapus');
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
    public function transferQTy()
    {
        $asal = $this->request->getPost('id_booking');

        $delivery = $this->request->getPost('delivery');
        $getIdBooking = [
            'no_booking' => $this->request->getPost('no_booking'),
            'no_order' => $this->request->getPost('no_order'),
            'kd_buyer' => $this->request->getPost('kd_buyer'),
            'delivery' => $delivery
        ];
        $tujuan = $this->bookingModel->getIdForTransfer($getIdBooking);
        if (!$tujuan) {
            return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $asal))->with('error', 'Data booking tujuan transfer tidak ditemukan');
        } else {
            $sisa = $this->request->getPost('sisaQty');
            $keterangan = $this->request->getPost('keterangan');
            $qtyTransfer = $this->request->getPost('transferQty');
            $totalqty = $tujuan['qty_booking'] + $qtyTransfer;
            $sisaTotalQty = $tujuan['sisa_booking'] + $qtyTransfer;
            $data = [
                'from_id' => $asal,
                'qty_transfer' => $qtyTransfer,
                'to_id' => $tujuan['id_booking']
            ];
            $tfModel = new \App\Models\TransferModel();
            $insert = $tfModel->insert($data);

            if ($insert) {
                $cekKeterangan = $this->bookingModel->select('keterangan')->where('id_booking', $id_booking)->first();
                $oldKeterangan = $cekKeterangan['keterangan'] ?? ''; // kalau null, fallback ke string kosong
                $ket = $oldKeterangan . ' | ' .  $keterangan;
                $this->bookingModel->update($asal, ['sisa_booking' => $sisa, 'keterangan' => $ket]);
                $this->bookingModel->update($tujuan['id_booking'], ['qty_booking' => $totalqty, 'sisa_booking' => $sisaTotalQty]);
                return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $asal))->with('success', 'Data transfer berhasil disimpan dan data booking berhasil diperbarui.');
            } else {
                return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $asal))->with('error', 'Data transfer gagal disimpan dan data booking berhasil diperbarui.');
            }
        }
    }
}
