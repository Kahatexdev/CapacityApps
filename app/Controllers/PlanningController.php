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
use App\Models\KebutuhanMesinModel;
use App\Models\MesinPlanningModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\RequestInterface;



class PlanningController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $KebutuhanMesinModel;
    protected $MesinPlanningModel;

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
        $this->KebutuhanMesinModel = new KebutuhanMesinModel();
        $this->MesinPlanningModel = new MesinPlanningModel();
        if ($this->filters   = ['role' => ['planning']] != session()->get('role')) {
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
        $mcJalan = $this->jarumModel->getJalanMesinPerArea();
        $totalMc = $this->jarumModel->totalMc();
        $bulan = date('m');
        $totalMesin = $this->jarumModel->getJalanMesinPerArea();
        $jarum = $this->jarumModel->jarumPerArea();
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
            'totalMc' => $totalMc,
            'Area' => $totalMesin,
            'order' => $this->ApsPerstyleModel->getTurunOrder($bulan),
            'jarum'=> $jarum
         ];
        return view(session()->get('role') . '/index', $data);
    }
    public function order()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'acive7' => '',

            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/ordermaster', $data);
    }

    public function assignareal()
    {
        $data = [
            'role' => session()->get('role'),
            'mastermodel' => $this->request->getPost("no_model"),
            'jarum' => $this->request->getPost("jarum"),
            'area' => $this->request->getPost("area"),
        ];
        $assign = $this->ApsPerstyleModel->asignAreal($data);
        if ($assign) {
            return redirect()->to(base_url(session()->get('role') . '/dataorder/'))->withInput()->with('success', 'Berhasil Assign Area');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/dataorder/'))->withInput()->with('error', 'Gagal Assign Area');
        }
    }
    public function assignarealall()
    {
        $data = [
            'role' => session()->get('role'),
            'mastermodel' => $this->request->getPost("no_model"),
            'area' => $this->request->getPost("area"),
        ];
        $assign = $this->ApsPerstyleModel->asignArealall($data);
        if ($assign) {
            return redirect()->to(base_url(session()->get('role') . '/dataorder/'))->withInput()->with('success', 'Berhasil Assign Area');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/dataorder/'))->withInput()->with('error', 'Gagal Assign Area');
        }
    }
    public function listplanning()
    {
        $dataBooking = $this->KebutuhanMesinModel->listPlan();
        $data = [
            'role' => session()->get('role'),
            'title' => 'List Planning From Capacity',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'data' => $dataBooking
        ];
        return view(session()->get('role') . '/Planning/listPlanning', $data);
    }
    public function listplanningAps()
    {
        $dataBooking = $this->KebutuhanMesinModel->listPlan();
        $data = [
            'role' => session()->get('role'),
            'title' => 'List Planning From Capacity',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'data' => $dataBooking
        ];
        return view(session()->get('role') . '/Planning/listPlanning', $data);
    }
    public function splitarea()
    {
        $idaps = $this->request->getPost('idaps');
        $pdk = $this->request->getPost('noModel');
        $deliv = $this->request->getPost('delivery');

        $update = [
            'factory' => $this->request->getPost('area1'),
            'qty' => $this->request->getPost('qty1'),
            'sisa' => $this->request->getPost('qty1'),
        ];
        $insert = [
            'machinetypeid' => $this->request->getPost('jarum'),
            'no_order' => $this->request->getPost('order'),
            'size' => $this->request->getPost('style'),
            'country' => $this->request->getPost('country'),
            'mastermodel' => $pdk,
            'delivery' => $deliv,
            'qty' => $this->request->getPost('qty2'),
            'sisa' => $this->request->getPost('qty2'),
            'factory' => $this->request->getPost('area2'),
            'production_unit' => "PU Belum Di Pilih",
            'smv' => $this->request->getPost('smv'),
            'seam' => $this->request->getPost('seam')

        ];
        $u = $this->ApsPerstyleModel->update($idaps, $update);
        if ($u) {
            $this->ApsPerstyleModel->insert($insert);
            return redirect()->to(base_url(session()->get('role') . '/detailModelPlanning/' . $pdk . '/' . $deliv))->withInput()->with('success', 'Berhasil Split Style Area');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailModelPlanning/' . $pdk . '/' . $deliv))->withInput()->with('error', 'Gagal Membagi Style');
        }
    }
    public function detaillistplanning($judul)
    {
        $dataplan = $this->KebutuhanMesinModel->jarumPlan($judul);
        $data = [
            'role' => session()->get('role'),
            'title' => 'List Planning From Capacity',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'data' => $dataplan,
            'judul' => $judul,
        ];
        return view(session()->get('role') . '/Planning/detailPlanning', $data);
    }
    public function detaillistplanningAps($judul)
    {
        $dataplan = $this->KebutuhanMesinModel->jarumPlan($judul);
        $data = [
            'role' => session()->get('role'),
            'title' => 'List Planning From Capacity',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'data' => $dataplan,
            'judul' => $judul,
        ];
        return view(session()->get('role') . '/Planning/detailPlanning', $data);
    }

    public function pickmachine($jarum)
    {
        $datamc = $this->jarumModel->listmachine($jarum);
        $mesin = $this->request->getPost('mesin');
        $status = $this->request->getPost('deskripsi');
        $id = $this->request->getPost('id');
        $data = [
            'role' => session()->get('role'),
            'title' => 'Pick Machine',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'datamc' => $datamc,
            'mesin' => $mesin,
            'status' => $status,
            'id' => $id,
            'jarum' => $jarum,
        ];
        return view(session()->get('role') . '/Planning/pilihMesin', $data);
    }

    public function savemachine($id)
    {
        $data = $this->MesinPlanningModel->savemachine($id);
        $judulData = $this->KebutuhanMesinModel->find($id);
        $judul = $judulData ? $judulData['judul'] : null;
        if ($data) {
            return redirect()->to(base_url(session()->get('role') . '/detaillistplanning/' . $judul . '/' . $id))->withInput()->with('success', 'Success Pick Machine Area');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detaillistplanning/' . $judul . '/' . $id))->withInput()->with('error', 'Error Pick Machine Area');
        }
    }

    public function viewdetail($id)
    {

        $datamc = $this->MesinPlanningModel->getDataPlanning($id);
        $judulData = $this->KebutuhanMesinModel->find($id);
        $judul = $judulData ? $judulData['judul'] : null;
        $mesin = $judulData ? $judulData['mesin'] : null;
        $jarum = $judulData ? $judulData['jarum'] : null;
        $data = [
            'role' => session()->get('role'),
            'title' => 'View Detail Machine Choosen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'datamc' => $datamc,
            'judul' => $judul,
            'mesin' => $mesin,
            'jarum' => $jarum,

        ];
        return view(session()->get('role') . '/Planning/mesinSelected', $data);
    }
    public function viewdetailAps($id)
    {

        $datamc = $this->MesinPlanningModel->getDataPlanning($id);
        $judulData = $this->KebutuhanMesinModel->find($id);
        $judul = $judulData ? $judulData['judul'] : null;
        $mesin = $judulData ? $judulData['mesin'] : null;
        $jarum = $judulData ? $judulData['jarum'] : null;
        $data = [
            'role' => session()->get('role'),
            'title' => 'View Detail Machine Choosen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'datamc' => $datamc,
            'judul' => $judul,
            'mesin' => $mesin,
            'jarum' => $jarum,

        ];
        return view(session()->get('role') . '/Planning/mesinSelected', $data);
    }
    public function editarea()
    {
        $id = $this->request->getPost('id');
        $area = $this->request->getPost('area');
        $pdk = $this->request->getPost('pdk');
        $deliv = $this->request->getPost('deliv');
        $update = $this->ApsPerstyleModel->update($id, ['factory' => $area]);
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/detailModelPlanning/' . $pdk . '/' . $deliv))->withInput()->with('success', 'Berhasil Mengubah Area');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailModelPlanning/' . $pdk . '/' . $deliv))->withInput()->with('error', 'Gagal Mengubah Area');
        }
    }
}
