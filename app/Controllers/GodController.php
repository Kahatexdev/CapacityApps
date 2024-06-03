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
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\UserModel;


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
        if ($this->filters   = ['role' => ['capacity', 'planning', 'god', 'sudo']] != session()->get('role')) {
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
        $bulan = date('m');

        $startDate = new \DateTime('first day of this month');
        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();
        $currentMonth = $startDate->format('F');
        $weekCount = 1; // Initialize week count for the first week of the month
        $monthlyData = [];

        for ($i = 0; $i < 52; $i++) {
            $startOfWeek = clone $startDate;
            $startOfWeek->modify("+$i week");
            $startOfWeek->modify('Monday this week');

            $endOfWeek = clone $startOfWeek;
            $endOfWeek->modify('Sunday this week');
            $numberOfDays = $startOfWeek->diff($endOfWeek)->days + 1;

            $weekHolidays = [];
            foreach ($holidays as $holiday) {
                $holidayDate = new \DateTime($holiday['tanggal']);
                if ($holidayDate >= $startOfWeek && $holidayDate <= $endOfWeek) {
                    $weekHolidays[] = [
                        'nama' => $holiday['nama'],
                        'tanggal' => $holidayDate->format('d-F'),
                    ];
                    $numberOfDays--;
                }
            }
            $currentMonthOfYear = $startOfWeek->format('F');
            if ($currentMonth !== $currentMonthOfYear) {
                $currentMonth = $currentMonthOfYear;
                $weekCount = 1; // Reset week count
                $monthlyData[$currentMonth] = [];
            }

            $startOfWeekFormatted = $startOfWeek->format('d/m');
            $endOfWeekFormatted = $endOfWeek->format('d/m');

            $monthlyData[$currentMonth][] = [
                'week' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $numberOfDays,
                'holidays' => $weekHolidays,
            ];

            $weekCount++;
        }
        $data = [
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
            'totalMc' => $totalMc,
            'order' => $this->ApsPerstyleModel->getTurunOrder($bulan),
            'weeklyRanges' => $monthlyData,
            'DaftarLibur' => $holidays


        ];
        return view('Sudo/index', $data);
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
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
        ];
        return view('Sudo/Booking/booking', $data);
    }
    public function bookingPerJarum($jarum)
    {
        $product = $this->productModel->getJarum($jarum);
        $booking = $this->bookingModel->getDataPerjarum($jarum);

        $data = [
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
        return view('Sudo/Booking/jarum', $data);
    }

    public function bookingPerBulanJarum($jarum)
    {
        $bulan = $this->bookingModel->getbulan($jarum);
        $data = [
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
        return view('Sudo/Booking/bookingbulan', $data);
    }

    public function bookingPerBulanJarumTampil($bulan, $tahun, $jarum)
    {
        $booking = $this->bookingModel->getDataPerjarumbulan($bulan, $tahun, $jarum);
        $product = $this->productModel->getJarum($jarum);
        $data = [
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
        return view('Sudo/Booking/jarumbulan', $data);
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
        return view('Sudo/Booking/detail', $data);
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
            return redirect()->to(base_url('Sudo/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
        } else {

            $inputModel = [
                'tgl_terima_order' => $tgl_turun,
                'no_model' => $no_model,
                'deskripsi' => $deskripsi,
                'id_booking' => $id_booking,
            ];
            $input = $this->orderModel->insert($inputModel);
            if (!$input) {
                return redirect()->to(base_url('Sudo/detailbooking/' . $id_booking))->withInput()->with('error', 'Gagal Ambil Order');
            } else {
                $id = $id_booking;
                $status = "";
                if ($sisa_booking == "0") {
                    $status = "Habis";
                } else {
                    $status = "Aktif";
                }
                $data = [
                    'sisa_booking' => $sisa_booking,
                    'status' => $status
                ];
                $this->bookingModel->update($id, $data);
                return redirect()->to(base_url('Sudo/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
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
                $data = [];
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
                $data = [];
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
            return redirect()->to(base_url('Sudo/detailbooking/' . $idBooking))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url('Sudo/detailbooking/' . $idBooking))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function deletebooking($idBooking)
    {

        $jarum = $this->request->getPost("jarum");
        $id = $idBooking;
        $delete = $this->bookingModel->delete($id);
        if ($delete) {
            return redirect()->to(base_url('Sudo/databooking/' . $jarum))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url('Sudo/databooking/' . $jarum))->withInput()->with('error', 'Gagal Hapus Data');
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
            return redirect()->to(base_url('Sudo/databooking/' . $jarum))->withInput()->with('success', 'Bookingan Berhasil Di Cancel');
        } else {
            return redirect()->to(base_url('Sudo/databooking/' . $jarum))->withInput()->with('error', 'Gagal Cancel Booking');
        }
    }

    public function getCancelBooking()
    {
        $resultCancelBooking = $this->bookingModel->getCancelBooking();
        $charts = $this->bookingModel->chartCancel();
        $bulan = array_keys($charts['details']);
        $jumlahPembatalan = array_values($charts['totals']);
        $data = [
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
        return view('Sudo/Booking/cancelbooking', $data);
    }

    public function detailcancelbooking($week, $buyer)
    {

        $resultCancelBooking = $this->bookingModel->getDetailCancelBooking($week, $buyer);
        $data = [
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
        return view('Sudo/Booking/detailcancelbooking', $data);
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
        return view('Sudo/Target/index', $data);
    }
    public function targetjarum($jarum)
    {
        $product = $this->productModel
            ->where('jarum', $jarum)
            ->orderBy('id_product_type', 'asc')
            ->findAll();
        $data = [
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
        return view('Sudo/Target/target', $data);
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
            return redirect()->to(base_url('Sudo/datatargetjarum/' . $jarum))->withInput()->with('success', 'Data Berhasil Diinput');
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
            return redirect()->to(base_url('Sudo/cancelBooking/'))->withInput()->with('success', 'Data Uncancel Success');
        }
    }

    public function account()
    {
        $userdata = $this->userModel->getData();
        $areadata = $this->areaModel->findAll();
        $data = [
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
        return view('Sudo/Account/index', $data);
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
            return redirect()->to(base_url('sudo/account'))->with('success', 'User Berhasil di input');
        } else {
            return redirect()->to(base_url('sudo/account'))->with('error', 'User Gagal di input');
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
                        'user_id' => $userId,
                        'area_id' => $areaId,
                    ];
                    $query = "INSERT INTO user_areas (user_id, area_id) VALUES (?, ?)";
                    $db->query($query, [$userId, $areaId]);
                    if (!$db) {
                        return redirect()->to(base_url('sudo/account'))->with('error', 'User Gagal di input');
                    }
                }
            }
            return redirect()->to(base_url('sudo/account'))->with('success', 'User Berhasil di input');
        } else {
            return redirect()->to(base_url('sudo/account'))->with('error', 'Tidak ada data');
        }
    }
    public function deleteaccount($id)
    {
        $delete = $this->userModel->delete($id);
        if ($delete) {
            return redirect()->to(base_url('sudo/account'))->with('success', 'User Berhasil di hapus');
        } else {
            return redirect()->to(base_url('sudo/account'))->with('error', 'User Gagal di hapus');
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
                        'user_id' => $id,
                        'area_id' => $areaId,
                    ];
                    $query = "INSERT INTO user_areas (user_id, area_id) VALUES (?, ?)";
                    $db->query($query, [$id, $areaId]);
                    if (!$db) {
                        return redirect()->to(base_url('sudo/account'))->with('error', 'User Gagal di input');
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return redirect()->to(base_url('sudo/account'))->with('error', 'User Gagal di Update');
            } else {
                return redirect()->to(base_url('sudo/account'))->with('success', 'User Berhasil di Update');
            }
        } else {
            $db->transRollback();
            return redirect()->to(base_url('sudo/account'))->with('error', 'User Gagal di Update');
        }
    }
}
