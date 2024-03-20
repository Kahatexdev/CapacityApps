<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;

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
        $product = $this->productModel->findAll();
        $booking = $$data = [
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'jarum' => $jarum,
            'tampildata' => $tampilperjarum,
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
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'booking' => $booking,
            'jarum' => $needle,
            'product' => $product

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
