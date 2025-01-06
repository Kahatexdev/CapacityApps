<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\AreaModel;
use App\Models\ProduksiModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DateTime;
use App\Models\HistorySmvModel;

class OrderController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $roleSession;
    protected $historysmv;
    protected $areaModel;


    public function __construct()
    {
        session();
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->historysmv = new HistorySmvModel();
        $this->areaModel = new AreaModel();
        if ($this->filters   = ['role' => ['capacity',  'planning', 'aps', 'god']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();
        $this->roleSession =  session()->get('role');
    }
    protected function isLogedin()
    {
        if (!session()->get('id_user')) {
            return redirect()->to(base_url('/login'));
        }
    }
    public function order()
    {
        $role = session()->get('role');
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
            'active7' => '',
            'TotalMesin' => $totalMesin,
            'role' => $role
        ];
        return view($role . '/Order/ordermaster', $data);
    }


    public function detailModelCapacity($noModel, $delivery)
    {
        $pdkProgress = $this->ApsPerstyleModel->getProgress($noModel);
        $dataApsPerstyle = $this->ApsPerstyleModel->detailModel($noModel, $delivery); // Call the model method
        $role = session()->get('role');

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'dataAps' => $dataApsPerstyle,
            'noModel' => $noModel,
            'delivery' => $delivery,
            'progress' => $pdkProgress,
            'role' => $role

        ];
        return view(session()->get('role') . '/Order/detailOrder', $data);
    }

    public function semuaOrder()
    {
        $role = session()->get('role');

        $product = $this->productModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'product' => $product,
            'role' => $role

        ];
        return view($role . '/Order/semuaorder', $data);
    }

    public function tampilPerdelivery()
    {
        $request = service('request');
        $requestData = $request->getPost();
        log_message('debug', 'POST data: ' . print_r($requestData, true));

        $start = $requestData['start'] ?? 0;
        $length = $requestData['length'] ?? 10;
        $orderIndex = $requestData['order'][0]['column'] ?? 0;
        $orderDir = $requestData['order'][0]['dir'] ?? 'asc';
        $orderColumn = $requestData['columns'][$orderIndex]['data'] ?? '';

        // Extract search value from request
        $searchValue = $requestData['search']['value'] ?? '';

        // Fetch data from the model with search filter
        $tampilperdelivery = $this->orderModel->tampilPerdelivery($searchValue);

        $data = array_slice($tampilperdelivery, $start, $length);

        $recordsTotal = count($tampilperdelivery);
        $recordsFiltered = $recordsTotal;

        $response = [
            'draw' => $requestData['draw'] ?? 0,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }


    public function belumImport()
    {
        $role = session()->get('role');

        $tampilperdelivery = $this->orderModel->tampilbelumImport();
        $product = $this->productModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperdelivery,
            'product' => $product,
            'role' => $role


        ];
        return view($role . '/Order/semuaorder2', $data);
    }

    public function orderPerJarum()
    {
        $role = session()->get('role');

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
            'active7' => '',
            'TotalMesin' => $totalMesin,
            'role' => $role

        ];
        return view(session()->get('role') . '/Order/orderjarum', $data);
    }
    public function orderPerJarumBln()
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
            'active7' => '',
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/orderjarumbln', $data);
    }

    public function detailmodeljarum($noModel, $delivery, $jarum)
    {
        $apsPerstyleModel = new ApsPerstyleModel(); // Create an instance of the model
        $dataApsPerstyle = $apsPerstyleModel->detailModelJarum($noModel, $delivery, $jarum); // Call the model method
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'dataAps' => $dataApsPerstyle,
            'noModel' => $noModel,
            'delivery' => $delivery,
        ];

        return view(session()->get('role') . '/Order/detailModelJarum', $data);
    }
    public function detailmodeljarumPlan($noModel, $delivery, $jarum)
    {
        $apsPerstyleModel = new ApsPerstyleModel(); // Create an instance of the model
        $dataApsPerstyle = $apsPerstyleModel->detailModelJarum($noModel, $delivery, $jarum); // Call the model method
        $area = new AreaModel();
        $dataArea = $area->findALl();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'dataAps' => $dataApsPerstyle,
            'noModel' => $noModel,
            'delivery' => $delivery,
            'area' => $dataArea
        ];

        return view(session()->get('role') . '/Order/detailModelJarum', $data);
    }



    public function DetailOrderPerJarum($jarum)
    {
        $tampilperdelivery = $this->orderModel->tampilPerjarum($jarum);
        $product = $this->productModel->findAll();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $booking = $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'tampildata' => $tampilperdelivery,
            'product' => $product,
            'jenisJarum' => $totalMesin,

        ];
        return view(session()->get('role') . '/Order/semuaorderjarum', $data);
    }

    public function DetailOrderPerJarumBlnDetail($bulan, $tahun, $jarum)
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $tampilperdelivery = $this->orderModel->tampilPerjarumBulan($bulan, $tahun, $jarum);
        $product = $this->productModel->findAll();
        $booking = $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'tampildata' => $tampilperdelivery,
            'product' => $product,
            'jenisJarum' => $totalMesin,

        ];
        return view(session()->get('role') . '/Order/semuaorderjarum', $data);
    }

    public function DetailOrderPerJarumBln($jarum)
    {
        $bulan = $this->ApsPerstyleModel->getBulan($jarum);
        $product = $this->productModel->findAll();
        $booking = $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'bulan' => $bulan,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Order/orderjarumblngroup', $data);
    }
    public function DetailOrderPerJarumPlan($jarum)
    {
        $tampilperdelivery = $this->orderModel->tampilPerjarum($jarum);
        $product = $this->productModel->findAll();
        $booking = $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'tampildata' => $tampilperdelivery,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Order/semuaorderjarum', $data);
    }
    public function DetailOrderPerAreaPlan($area)
    {
        $tampilperdelivery = $this->orderModel->tampilPerarea($area);
        $product = $this->productModel->findAll();
        $booking = $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'tampildata' => $tampilperdelivery,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Order/semuaorderarea', $data);
    }
    public function updateorder($idOrder)
    {

        $data = [
            'role' => session()->get('role'),
            'mastermodel' => $this->request->getPost("no_model"),
            'size' =>  $this->request->getPost("style"),
            'delivery' => $this->request->getPost("delivery"),
            'qty' => $this->request->getPost("qty"),
            'sisa' => $this->request->getPost("sisa"),
            'seam' => $this->request->getPost("seam"),
            'smv' => $this->request->getPost("smv"),
            'factory' => $this->request->getPost("factory"),
            'inisial' => $this->request->getPost("inisial"),
        ];
        $jrm = $this->request->getPost("jarum");
        $id = $idOrder;
        $update = $this->ApsPerstyleModel->update($id, $data);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $modl . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $modl . '/' . $jrm))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function updateorderjarum($idOrder)
    {

        $data = [
            'role' => session()->get('role'),
            'mastermodel' => $this->request->getPost("no_model"),
            'size' =>  $this->request->getPost("style"),
            'delivery' => $this->request->getPost("delivery"),
            'qty' => $this->request->getPost("qty"),
            'sisa' => $this->request->getPost("sisa"),
            'seam' => $this->request->getPost("seam"),
            'factory' => $this->request->getPost("factory"),
        ];
        $id = $idOrder;
        $update = $this->ApsPerstyleModel->update($id, $data);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        $jrm = $this->request->getPost("jarum");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $modl . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $modl . '/' . $jrm))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function updateorderjarumplan($idOrder)
    {

        $data = [
            'role' => session()->get('role'),
            'mastermodel' => $this->request->getPost("no_model"),
            'size' =>  $this->request->getPost("style"),
            'delivery' => $this->request->getPost("delivery"),
            'qty' => $this->request->getPost("qty"),
            'sisa' => $this->request->getPost("sisa"),
            'seam' => $this->request->getPost("seam"),
            'factory' => $this->request->getPost("factory"),
        ];
        $id = $idOrder;
        $update = $this->ApsPerstyleModel->update($id, $data);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        $jrm = $this->request->getPost("jarum");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('error', 'Gagal Update Data');
        }
    }

    public function updatemesinperjarum($idDataMesin)
    {

        $data = [
            'role' => session()->get('role'),
            'total_mesin' => $this->request->getPost("total_mc"),
            'brand' => $this->request->getPost("brand"),
            'mesin_jalan' => $this->request->getPost("mesin_jalan"),
        ];
        $id = $idDataMesin;
        $update = $this->jarumModel->update($id, $data);
        $area = $this->request->getPost("area");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperjarum/' . $area))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperjarum/' . $area))->withInput()->with('error', 'Gagal Update Data');
        }
    }

    public function deletedetailstyle($idOrder)
    {

        $idOrder = $this->request->getPost("idapsperstyle");
        $id = $idOrder;
        $delete = $this->ApsPerstyleModel->delete($id);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/detailmodel/' . $modl . '/' . $del))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodel/' . $modl . '/' . $del))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }

    public function deletedetailmodeljarum($idOrder)
    {

        $idOrder = $this->request->getPost("idapsperstyle");
        $id = $idOrder;
        $delete = $this->ApsPerstyleModel->delete($id);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        $jrm = $this->request->getPost("jarum");
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }
    public function deletedetailmodeljarumplan($idOrder)
    {

        $idOrder = $this->request->getPost("idapsperstyle");
        $id = $idOrder;
        $delete = $this->ApsPerstyleModel->delete($id);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        $jrm = $this->request->getPost("jarum");
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('error', 'Gagal Hapus Data');
        }
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
    public function inputOrderManual()
    {
        $tgl = $this->request->getPost('tgl_turun');
        $model = $this->request->getPost('no_model');
        $jarum = $this->request->getPost('jarum');
        $prod = $this->request->getPost('productType');
        $id = $this->request->getPost('id');

        $getId = [
            'jarum' => $jarum,
            'prodtype' => $prod
        ];
        $idProdtype = $this->productModel->getId($getId);


        if ($model) {
            $check = $this->orderModel->checkExist($model);
            if ($check) {
                return redirect()->to(base_url(session()->get('role') . '/dataorderperjarum/' . $id))->withInput()->with('error', 'Data Model Exist');
            } else {
                $insert = $this->orderModel->insert([
                    'no_model' => $model,
                    'id_product_type' => $idProdtype,
                    'created_at' => $tgl
                ]);
            }
            if ($insert) {
                return redirect()->to(base_url(session()->get('role') . '/dataorderperjarum/' . $id))->withInput()->with('success', 'Data Model Inserted');
            } else {
                return redirect()->to(base_url(session()->get('role') . '/dataorderperjarum/' . $id))->withInput()->with('error', 'Data Model Not Insert');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/dataorderperjarum/' . $id))->withInput()->with('error', 'Please Check Model number');
        }
    }

    public function importModel()
    {
        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $row = $spreadsheet->getActiveSheet();
            $nomodel = $this->request->getVar('no_model');
            $idModel = $this->orderModel->getId($nomodel);
            $startRow = 4; // Ganti dengan nomor baris mulai
            foreach ($spreadsheet->getActiveSheet()->getRowIterator($startRow) as $row) {
                $rowIndex = $row->getRowIndex();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $row = [];
                foreach ($cellIterator as $cell) {
                    $row[] = $cell->getValue();
                }
                if (!empty($row)) {
                    $no_models = $row[29];
                    $firstSpacePosition = strpos($no_models, ' '); // Cari posisi spasi pertama
                    $no_model = substr($no_models, 0, $firstSpacePosition);
                    if ($no_model != $nomodel) {
                        return redirect()->to(base_url(session()->get('role') . '/semuaOrder'))->with('error', 'Nomor Model Tidak Sama. Silahkan periksa kembali' . $rowIndex);
                    } else {
                        if ($row[5] == null) {
                            return redirect()->to(base_url(session()->get('role') . '/semuaOrder'))->with('error', 'GAGAL');
                        } else {
                            $recordID = $row[0];
                            $articleNo = $row[30];
                            $producttype = $row[5];
                            $custCode = $row[7];
                            $description = $row[10];
                            $delivery = $row[11];
                            $rdelivery = str_replace('/', '-', (substr($delivery, -10)));
                            $delivery2 = date('Y-m-d', strtotime($rdelivery));
                            $qty = $row[12];
                            $country = $row[17];
                            $color = $row[18];
                            $size = $row[19];
                            $sam = $row[20];
                            if ($sam == null) {
                                $sam = 185;
                            }
                            $machinetypeid = $row[22];
                            $prodtype = [
                                'jarum' => $machinetypeid,
                                'prodtype' => $producttype
                            ];
                            $idProduct = $this->productModel->getId($prodtype);

                            $leadtime = $row[24];
                            $processRoute = $row[25];
                            $lcoDate = $row[26];
                            $rlcoDate = str_replace('/', '-', (substr($lcoDate, -10)));
                            $lcoDate2 = date('Y-m-d', strtotime($rlcoDate));


                            $simpandata = [
                                'machinetypeid' => $machinetypeid,
                                'size' => $size,
                                'mastermodel' => $nomodel,
                                'no_order' => $articleNo,
                                'delivery' => $delivery2,
                                'qty' => $qty,
                                'sisa' => $qty,
                                'country' => $country,
                                'color' => $color,
                                'seam' => $processRoute,
                                'smv' => $sam,
                                'production_unit' => 'PU Belum Dipilih',
                                'factory' => 'Belum Ada Area'
                            ];

                            $updateData = [
                                'kd_buyer_order' => $custCode,
                                'id_product_type' => $idProduct,
                                'seam' => $processRoute,
                                'leadtime' => $leadtime,
                                'description' => $description
                            ];
                            $validate = [
                                'size' => $size,
                                'delivery' => $delivery2,
                                'mastermodel' => $nomodel,
                                'country' => $country,
                                'qty' => $qty
                            ];

                            $existingAps = $this->ApsPerstyleModel->checkAps($validate);
                            if (!$existingAps) {
                                $this->ApsPerstyleModel->insert($simpandata);
                            } else {
                                $id = $existingAps['idapsperstyle'];
                                $qtyLama = $existingAps['qty'];
                                $qtyBaru = $qty + $qtyLama;
                                $this->ApsPerstyleModel->update($id, ['qty' => $qtyBaru, 'sisa' => $qtyBaru]);
                            }
                            $this->orderModel->update($idModel, $updateData);

                            // }
                        }
                    }
                }
            }
            return redirect()->to(base_url(session()->get('role') . '/belumImport'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/belumImport'))->with('error', 'No data found in the Excel file');
        }
    }
    public function deletedetailorder($idModel)
    {

        $idModel = $this->request->getPost("no_model");
        $id = $idModel;
        $delete = $this->ApsPerstyleModel->where('Mastermodel', $id)->delete();
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/semuaOrder/'))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/semuaOrder/'))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }

    // Planning
    public function detailModelPlanning($noModel, $delivery)
    {
        $dataApsPerstyle = $this->ApsPerstyleModel->detailModel($noModel, $delivery);
        $dataMc = $this->jarumModel->getAreaModel($noModel);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'dataAps' => $dataApsPerstyle,
            'noModel' => $noModel,
            'delivery' => $delivery,
            'dataMc' => $dataMc,
        ];
        return view(session()->get('role') . '/Order/detailOrder', $data);
    }
    public function detailPdk($noModel, $jarum)
    {
        $pdk = $this->ApsPerstyleModel->getSisaPerDeliv($noModel, $jarum);
        $sisaPerDeliv = [];
        foreach ($pdk as $perdeliv) {
            $deliv = $perdeliv['delivery'];
            $sisaPerDeliv[$deliv] = $this->ApsPerstyleModel->getSisaPerDlv($noModel, $jarum, $deliv);
        }
        foreach ($sisaPerDeliv as $deliv => $list) {
            $totalqty = 0;
            $qty = 0;
            if (is_array($list)) {
                foreach ($list as $val) {
                    if (isset($val['sisa'])) {
                        $qty += $val['qty'];
                        $totalqty = $qty;
                    }
                }
            }
            $sisaPerDeliv[$deliv]['totalQty'] = $totalqty;
        }
        $totalPo = $this->ApsPerstyleModel->totalPo($noModel);
        // ini ngambil jumlah qty
        $sisaArray = array_column($pdk, 'sisa');
        $maxValue = max($sisaArray);
        $indexMax = array_search($maxValue, $sisaArray);
        $totalQty = 0;
        for ($i = 0; $i <= $indexMax; $i++) {
            $totalQty += $sisaArray[$i];
        }

        // ini ngambil jumlah hari
        usort($pdk, function ($a, $b) {
            return strtotime($a['delivery']) - strtotime($b['delivery']);
        });
        $totalQty = round($totalQty / 24);
        $deliveryTerjauh = end($pdk)['delivery'];
        $today = new DateTime(date('Y-m-d'));
        $deliveryDate = new DateTime($deliveryTerjauh); // Tanggal delivery terjauh
        $diff = $today->diff($deliveryDate);
        $hari = $diff->days - 7;

        // ini ngambil delivery bottleneck

        $deliveryMax = $pdk[$indexMax]['delivery'];
        $tglDeliv = new DateTime($deliveryMax); // Tanggal delivery terjauh
        $beda = $today->diff($tglDeliv);
        $hariTarget = $beda->days - 7;
        $hariTarget = ($hariTarget <= 0) ? 1 : $hariTarget;

        // disini ngambil rata rata target.
        $smvArray = array_column($pdk, 'smv');
        $smvArray = array_map('intval', $smvArray);
        $averageSmv = array_sum($smvArray) / count($smvArray);
        $target = round((86400 / (intval($averageSmv))) * 0.85 / 24);

        // ini baru kalkulasi
        $mesin = round($totalQty / $target / $hariTarget);
        $targetPerhari = round($mesin * $target);


        // ini bagian rekomendasi (hard bgt bjir)
        $start = date('Y-m-d', strtotime('+7 days'));
        $rekomen = $this->ApsPerstyleModel->getSisaOrderforRec($jarum, $start, $deliveryTerjauh);
        $rekomendasi = [];
        foreach ($rekomen as $rec) {
            $sisa = round($rec['sisa'] / 24);
            $area = $rec['factory'];
            $mesinPerarea = $this->jarumModel->mesinPerArea($jarum, $area);
            if (!empty($mesinPerarea)) {
                $target = $mesinPerarea[0]['target'];
                $totalMesin = $mesinPerarea[0]['totalMesin'];
                $kapasitasPerhari = ($target * $totalMesin);
                $usedCapacityDaily = round($sisa / $hariTarget);
                $availCapacityDaily = $kapasitasPerhari - $usedCapacityDaily;

                // Tetap simpan area, tapi label avail sebagai 'N/A' jika kapasitas avail kurang dari target
                $avail = ($availCapacityDaily >= $targetPerhari) ? $availCapacityDaily : 'Only ' . $availCapacityDaily;

                $rekomendasi[$area] = [
                    'area' => $area,
                    'max' => $kapasitasPerhari,
                    'used' => $usedCapacityDaily,
                    'avail' => $avail
                ];
            }
        }
        usort($rekomendasi, function ($a, $b) {
            // Handle string "Only" vs angka
            if (is_string($a['avail'])) return 1; // Push "Only" ke belakang
            if (is_string($b['avail'])) return -1; // Push "Only" ke belakang

            // Jika keduanya angka, bandingkan nilai 'avail'
            return $b['avail'] <=> $a['avail'];
        });
        $top3Rekomendasi = array_slice($rekomendasi, 0, 3);
        $dataMc = $this->jarumModel->getAreaModel($noModel);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'order' => $sisaPerDeliv,
            'headerRow' => $pdk,
            'noModel' => $noModel,
            'dataMc' => $dataMc,
            'jarum' => $jarum,
            'kebMesin' => $mesin,
            'target' => $targetPerhari,
            'hari' => $hari,
            'rekomendasi' => $top3Rekomendasi,
            'totalPo' => $totalPo
        ];
        return view(session()->get('role') . '/Order/detailPdk', $data);
    }

    public function orderBlmAdaAreal()
    {

        $tampilperdelivery = $this->orderModel->tampilPerModelBlmAdaArea();
        foreach ($tampilperdelivery as &$key) {
            $delivery = new DateTime($key['delivery']);
            $ayeuna = new DateTime(); // Assuming $today is already set as $ayeuna

            $sisahari = $ayeuna->diff($delivery)->days;
            $key['sisahari'] = $sisahari;
        }

        $product = $this->productModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperdelivery,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Order/orderBlmAdaArea', $data);
    }
    public function semuaOrderPlan()
    {
        $tampilperdelivery = $this->orderModel->tampilPerdelivery();
        $product = $this->productModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperdelivery,
            'product' => $product,


        ];
        return view(session()->get('role') . '/Order/semuaorder2', $data);
    }
    public function orderPerJarumPlan()
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
            'active7' => '',
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/orderjarum', $data);
    }
    public function orderPerAreaPlan()
    {
        $totalMesin = $this->jarumModel->getArea();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/orderarea', $data);
    }
    public function statusOrder()
    {
        $totalMesin = $this->jarumModel->getArea();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/statusorder', $data);
    }
    public function statusOrderArea($area)
    {
        $areaProgress =  $this->ApsPerstyleModel->getProgressperArea($area);
        $lastmonth = date('Y-m-d', strtotime('- 1 month'));

        // Grup by mastermodel
        $grouped = [];
        foreach ($areaProgress as $item) {
            $model = $item['mastermodel'];
            if (!isset($grouped[$model])) {
                $grouped[$model] = [
                    'mastermodel' => $model,
                    'target' => 0,
                    'remain' => 0,
                    'delivery' => $item['delivery'],
                    'percentage' => 0,
                ];
            }

            // Jumlahkan target dan remain
            $grouped[$model]['target'] += (int)$item['target'];
            $grouped[$model]['remain'] += (int)$item['remain'];
            $produksi = $grouped[$model]['target'] - $grouped[$model]['remain'];

            // Hitung percentage hanya jika produksi > 0
            if ($produksi > 0) {
                $grouped[$model]['percentage'] = round(($produksi / $grouped[$model]['target']) * 100);
            }

            // Ambil delivery paling akhir
            if ($grouped[$model]['delivery'] < $item['delivery']) {
                $grouped[$model]['delivery'] = $item['delivery'];
            }
        }

        // Filter yang delivery >= hari ini
        $filtered = array_filter($grouped, function ($item) use ($lastmonth) {
            return $item['delivery'] >= $lastmonth;
        });
        usort($filtered, function ($a, $b) {
            return $a['percentage'] <=> $b['percentage'];
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'progress' => $filtered,
            'area' => $area
        ];
        return view(session()->get('role') . '/Order/statusorderArea', $data);
    }
    public function getTurunOrder()
    {
        $resultTurunOrder = $this->orderModel->getTurunOrder();
        $charts = $this->orderModel->chartTurun();
        $bulan = array_keys($charts['details']);
        $jumlahTurun = array_values($charts['totals']);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Summary Turun Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'details' => $resultTurunOrder,
            'bulan' => $bulan,
            'jumlahPembatalan' => $jumlahTurun,
            'totalChart' => $charts['totals']
        ];
        return view(session()->get('role') . '/Order/turunOrder', $data);
    }

    public function detailturunorder($week, $buyer)
    {
        $resultTurun = $this->orderModel->getDetailTurunOrder($week, $buyer);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Detail Confirm Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'data' => $resultTurun,
        ];
        return view(session()->get('role') . '/Order/detailturunorder', $data);
    }

    public function importsmv()
    {
        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $startRow = 4; // Ganti dengan nomor baris mulai
            $errorRows = [];

            foreach ($sheet->getRowIterator($startRow) as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                if ($rowData[19] == null) {
                    break;
                }
                if (!empty($rowData)) {
                    $no_models = $rowData[29];
                    $firstSpacePosition = strpos($no_models, ' '); // Cari posisi spasi pertama
                    $no_model = substr($no_models, 0, $firstSpacePosition);
                    $size = $rowData[19];
                    $smv = $rowData[20];
                    $validate = [
                        'mastermodel' => $no_model,
                        'size' => $rowData[19],
                        'smv' => $smv
                    ];
                    $id = $this->ApsPerstyleModel->getIdSmv($validate);
                    if ($id === null) {
                        $errorRows[] = "ID not found at row " . ($rowIndex + $startRow);
                        continue;
                    }
                    $Id = $id['idapsperstyle'] ?? 0;

                    $update = $this->ApsPerstyleModel->update($Id, ['smv' => $smv]);

                    if (!$update) {
                        $errorRows[] = "Failed to update row " . ($rowIndex + $startRow);
                    }
                }
            }

            if (!empty($errorRows)) {
                $errorMessage = "Errors occurred:\n" . implode("\n", $errorRows);
                dd($errorMessage);
                return redirect()->to(base_url(session()->get('role') . '/smvimport'))->withInput()->with('error', $errorMessage);
            } else {
                return redirect()->to(base_url(session()->get('role') . '/smvimport'))->withInput()->with('success', 'Data Berhasil di Update');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/smvimport'))->withInput()->with('error', 'No data found in the Excel file');
        }
    }
    public function importupdatesmv()
    {
        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $startRow = 5; // Ganti dengan nomor baris mulai
            $errorRows = [];

            foreach ($sheet->getRowIterator($startRow) as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                if ($rowData[2] == null) {
                    break;
                }
                if (!empty($rowData)) {
                    $no_model = $rowData[1];
                    $size = $rowData[2];
                    $smv = $rowData[3];
                    $validate = [
                        'mastermodel' => $no_model,
                        'size' => $size,
                        'smv' => $smv
                    ];
                    $id = $this->ApsPerstyleModel->getIdSmv($validate);
                    if ($id === null) {
                        $errorRows[] = "ID not found at row " . ($rowIndex + $startRow);
                        continue;
                    }
                    foreach ($id as $Id) {
                        $idaps = $Id['idapsperstyle'];
                        $update = $this->ApsPerstyleModel->update($Id, ['smv' => $smv]);
                        $insert = [
                            'style' => $size,
                            'smv_old' => $Id['smv'],
                        ];
                        if (!$update) {
                            $errorRows[] = "Failed to update row " . ($rowIndex + $startRow);
                        } else {
                            $this->historysmv->insert($insert);
                        }
                    }
                }
            }
            if (!empty($errorRows)) {
                $errorMessage = "Errors occurred:\n" . implode("\n", $errorRows);
                dd($errorMessage);
                return redirect()->to(base_url(session()->get('role') . '/updatesmv'))->withInput()->with('error', $errorMessage);
            } else {
                return redirect()->to(base_url(session()->get('role') . '/updatesmv'))->withInput()->with('success', 'Data Berhasil di Update');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/updatesmv'))->withInput()->with('error', 'No data found in the Excel file');
        }
    }
    public function smvimport()
    {
        return view(session()->get('role') . '/smvimport');
    }
    public function sisaOrder()
    {
        $role = session()->get('role');
        $buyer = $this->orderModel->getBuyer();
        $data = [
            'role' => $role,
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'buyer' => $buyer,
            'role' => $role
        ];
        return view($role . '/Order/sisaOrder', $data);
    }
    public function sisaOrderBuyer($buyer)
    {
        $role = session()->get('role');
        $month = $this->request->getPost('month');
        $yearss = $this->request->getPost('year');

        // Jika bulan atau tahun tidak diisi, gunakan bulan dan tahun ini
        if (empty($month) || empty($yearss)) {
            $bulan = date('Y-m-01', strtotime('this month')); // Bulan ini
        } else {
            // Atur tanggal berdasarkan input bulan dan tahun dari POST
            $bulan = date('Y-m-01', strtotime("$yearss-$month-01"));
        }

        $years = [];
        $currentYear = date('Y');
        $startYear = $currentYear - 2;
        $endYear = $currentYear + 7;

        // Loop dari tahun ini sampai 10 tahun ke depan
        for ($year = $startYear; $year <= $endYear; $year++) {
            $months = [];

            // Loop untuk setiap bulan dalam setahun
            for ($i = 1; $i <= 12; $i++) {
                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
                $months[] = $monthName;
            }

            // Simpan data tahun dengan bulan-bulannya
            $years[$year] = array_unique($months); // array_unique memastikan bulan unik meskipun tidak perlu dalam kasus ini
        }

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
            $months[] = $monthName;
        }
        $months = array_unique($months);

        // Ambil data dari model
        $startDate = new \DateTime($bulan); // Awal bulan
        $startDate->setTime(0, 0, 0);
        $endDate = (clone $startDate)->modify('last day of this month');   // Akhir bulan

        // Loop data
        $data = $this->ApsPerstyleModel->getBuyerOrder($buyer, $bulan);
        // dd($data);
        $allData = [];
        $week = [];
        $totalPerWeek = [];

        foreach ($data as $id) {
            $mastermodel = $id['mastermodel'];
            $machinetypeid = $id['machinetypeid'];
            $factory = $id['factory'];

            // Ambil data qty, sisa, dan produksi
            $qty = $id['qty'];
            $sisa = $id['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($id['delivery']); // Asumsikan ada field delivery

            // Loop untuk membagi data ke dalam minggu
            $weekCount = 1;
            $currentStartDate = clone $startDate;

            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);
                $dateWeek = $currentStartDate->format('d') . " - " . $endOfWeek->format('d');
                $week[$weekCount] = $dateWeek;
                // dd($currentStartDate);
                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {

                    // Ambil total jl_mc untuk minggu ini dan jumlahkan jika sudah ada data sebelumnya
                    $dataOrder = [
                        'model' => $mastermodel,
                        'jarum' => $machinetypeid,
                        'area' => $factory,
                        'delivery' => $id['delivery'],
                    ];
                    $jlMc = 0;
                    $jlMcData = $this->produksiModel->getJlMc($dataOrder);

                    // Pastikan data jl_mc ada
                    if ($jlMcData) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcData as $mc) {
                            $jlMc += $mc['jl_mc'];
                        }
                    }
                    $allData[$mastermodel][$machinetypeid][$factory][$weekCount][] = json_encode([
                        'del' => $id['delivery'],
                        'qty' => $qty,
                        'prod' => $produksi,
                        'sisa' => $sisa,
                        'jlMc' => $jlMc,
                    ]);

                    // Hitung total per minggu
                    if (!isset($totalPerWeek[$weekCount])) {
                        $totalPerWeek[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeek[$weekCount]['totalQty'] += $qty;
                    $totalPerWeek[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeek[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeek[$weekCount]['totalJlMc'] += $jlMc;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }

        // Proses data per jarum
        $dataPerjarum = $this->ApsPerstyleModel->getBuyerOrderPejarum($buyer, $bulan);
        $allDataPerjarum = [];
        $totalPerWeekJrm = [];

        foreach ($dataPerjarum as $id2) {
            $machinetypeid = $id2['machinetypeid'];
            $delivery = $id2['delivery'];
            $qty = $id2['qty'];
            $sisa = $id2['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($delivery); // Tanggal pengiriman

            $weekCount = 1;
            $currentStartDate = clone $startDate;
            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    $jlMcJrm = 0;
                    $dataOrder2 = [
                        'buyer' => $buyer,
                        'jarum' => $machinetypeid,
                        'delivery' => $delivery,
                    ];
                    $jlMcJrmData = $this->produksiModel->getJlMcJrm($dataOrder2);
                    if ($jlMcJrmData) {
                        foreach ($jlMcJrmData as $mcJrm) {
                            $jlMcJrm += $mcJrm['jl_mc'];
                        }
                    }

                    // Pastikan array utama memiliki key jarum
                    if (!isset($allDataPerjarum[$machinetypeid])) {
                        $allDataPerjarum[$machinetypeid] = [];
                    }
                    // Pastikan minggu tersedia
                    if (!isset($allDataPerjarum[$machinetypeid][$weekCount])) {
                        $allDataPerjarum[$machinetypeid][$weekCount] = [
                            'qtyJrm' => 0,
                            'prodJrm' => 0,
                            'sisaJrm' => 0,
                            'jlMcJrm' => 0,
                        ];
                    }

                    // Tambahkan data minggu
                    $allDataPerjarum[$machinetypeid][$weekCount]['qtyJrm'] += $qty;
                    $allDataPerjarum[$machinetypeid][$weekCount]['prodJrm'] += $produksi;
                    $allDataPerjarum[$machinetypeid][$weekCount]['sisaJrm'] += $sisa;
                    $allDataPerjarum[$machinetypeid][$weekCount]['jlMcJrm'] += $jlMcJrm;

                    // Hitung total per minggu
                    if (!isset($totalPerWeekJrm[$weekCount])) {
                        $totalPerWeekJrm[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeekJrm[$weekCount]['totalQty'] += $qty;
                    $totalPerWeekJrm[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeekJrm[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeekJrm[$weekCount]['totalJlMc'] += $jlMcJrm;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }
        $maxWeekCount = $weekCount - 1;

        $data = [
            'role' => $role,
            'title' => 'Data Sisa Order ' . $buyer,
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'buyer' => $buyer,
            'bulan' => $bulan,
            'maxWeek' => $maxWeekCount,
            'allData' => $allData,
            'totalData' => $totalPerWeek,
            'allDataJrm' => $allDataPerjarum,
            'totalDataJrm' => $totalPerWeekJrm,
            'years' => $years,
            'months' => $months,
            'week' => $week,
        ];
        // dd($data);
        return view(session()->get('role') . '/Order/detailSisaOrder', $data);
    }
    public function sisaOrderArea()
    {
        $role = session()->get('role');
        $area = $this->areaModel->getArea();
        $data = [
            'role' => $role,
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'role' => $role
        ];
        return view($role . '/Order/sisaOrderArea', $data);
    }
    public function detailSisaOrderArea($ar)
    {
        $role = session()->get('role');
        $month = $this->request->getPost('month');
        $yearss = $this->request->getPost('year');

        // Jika bulan atau tahun tidak diisi, gunakan bulan dan tahun ini
        if (empty($month) || empty($yearss)) {
            $bulan = date('Y-m-01', strtotime('this month')); // Bulan ini
        } else {
            // Atur tanggal berdasarkan input bulan dan tahun dari POST
            $bulan = date('Y-m-01', strtotime("$yearss-$month-01"));
        }

        $years = [];
        $currentYear = date('Y');
        $startYear = $currentYear - 2;
        $endYear = $currentYear + 7;

        // Loop dari tahun ini sampai 10 tahun ke depan
        for ($year = $startYear; $year <= $endYear; $year++) {
            $months = [];

            // Loop untuk setiap bulan dalam setahun
            for ($i = 1; $i <= 12; $i++) {
                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
                $months[] = $monthName;
            }

            // Simpan data tahun dengan bulan-bulannya
            $years[$year] = array_unique($months); // array_unique memastikan bulan unik meskipun tidak perlu dalam kasus ini
        }

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
            $months[] = $monthName;
        }
        $months = array_unique($months);
        // dd($bulan);

        // Ambil tanggal awal dan akhir bulan
        $startDate = new \DateTime($bulan); // Awal bulan
        $startDate->setTime(0, 0, 0);
        $endDate = (clone $startDate)->modify('last day of this month');   // Akhir bulan

        $data = $this->ApsPerstyleModel->getAreaOrder($ar, $bulan);
        $allData = [];
        $totalPerWeek = []; // Untuk menyimpan total produksi per minggu

        foreach ($data as $id) {
            $mastermodel = $id['mastermodel'];
            $machinetypeid = $id['machinetypeid'];
            $factory = $id['factory'];

            // Ambil data qty, sisa, dan produksi
            $qty = $id['qty'];
            $sisa = $id['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($id['delivery']); // Asumsikan ada field delivery

            // Loop untuk membagi data ke dalam minggu
            $weekCount = 1;
            $currentStartDate = clone $startDate;

            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);
                $dateWeek = $currentStartDate->format('d') . " - " . $endOfWeek->format('d');
                $week[$weekCount] = $dateWeek;

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    // Ambil total jl_mc untuk minggu ini dan jumlahkan jika sudah ada data sebelumnya
                    $dataOrder = [
                        'model' => $mastermodel,
                        'jarum' => $machinetypeid,
                        'area' => $factory,
                        'delivery' => $id['delivery'],
                    ];
                    $jlMc = 0;
                    $jlMcData = $this->produksiModel->getJlMc($dataOrder);

                    // Pastikan data jl_mc ada
                    if ($jlMcData) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcData as $mc) {
                            $jlMc += $mc['jl_mc'];
                        }
                    }

                    $allData[$mastermodel][$machinetypeid][$factory][$weekCount][] = json_encode([
                        'del' => $id['delivery'],
                        'qty' => $qty,
                        'prod' => $produksi,
                        'sisa' => $sisa,
                        'jlMc' => $jlMc,
                    ]);

                    // Hitung total per minggu
                    if (!isset($totalPerWeek[$weekCount])) {
                        $totalPerWeek[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeek[$weekCount]['totalQty'] += $qty;
                    $totalPerWeek[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeek[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeek[$weekCount]['totalJlMc'] += $jlMc;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }

        $allDataPerjarum = [];
        $totalPerWeekJrm = []; // Total per minggu
        $dataPerjarum = $this->ApsPerstyleModel->getAreaOrderPejarum($ar, $bulan);

        foreach ($dataPerjarum as $id2) {
            $machinetypeid = $id2['machinetypeid'];
            $delivery = $id2['delivery'];
            $qty = $id2['qty'];
            $sisa = $id2['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($delivery); // Tanggal pengiriman

            $weekCount = 1;
            $currentStartDate = clone $startDate;
            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    $jlMcJrm = 0;
                    $dataOrder2 = [
                        'area' => $ar,
                        'jarum' => $machinetypeid,
                        'delivery' => $delivery,
                    ];
                    $jlMcJrmData = $this->produksiModel->getJlMcJrmArea($dataOrder2);
                    if ($jlMcJrmData) {
                        foreach ($jlMcJrmData as $mcJrm) {
                            $jlMcJrm += $mcJrm['jl_mc'];
                        }
                    }

                    // Pastikan array utama memiliki key jarum
                    if (!isset($allDataPerjarum[$machinetypeid])) {
                        $allDataPerjarum[$machinetypeid] = [];
                    }
                    // Pastikan minggu tersedia
                    if (!isset($allDataPerjarum[$machinetypeid][$weekCount])) {
                        $allDataPerjarum[$machinetypeid][$weekCount] = [
                            'qtyJrm' => 0,
                            'prodJrm' => 0,
                            'sisaJrm' => 0,
                            'jlMcJrm' => 0,
                        ];
                    }

                    // Tambahkan data minggu
                    $allDataPerjarum[$machinetypeid][$weekCount]['qtyJrm'] += $qty;
                    $allDataPerjarum[$machinetypeid][$weekCount]['prodJrm'] += $produksi;
                    $allDataPerjarum[$machinetypeid][$weekCount]['sisaJrm'] += $sisa;
                    $allDataPerjarum[$machinetypeid][$weekCount]['jlMcJrm'] += $jlMcJrm;

                    // Hitung total per minggu
                    if (!isset($totalPerWeekJrm[$weekCount])) {
                        $totalPerWeekJrm[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeekJrm[$weekCount]['totalQty'] += $qty;
                    $totalPerWeekJrm[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeekJrm[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeekJrm[$weekCount]['totalJlMc'] += $jlMcJrm;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }

        $maxWeekCount = $weekCount - 1;

        $data = [
            'role' => $role,
            'title' => 'Data Sisa Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $ar,
            'bulan' => $bulan,
            'maxWeek' => $maxWeekCount,
            'allData' => $allData,
            'totalData' => $totalPerWeek,
            'allDataJrm' => $allDataPerjarum,
            'totalDataJrm' => $totalPerWeekJrm,
            'years' => $years,
            'months' => $months,
            'week' => $week,
        ];
        // dd($data);
        return view($role . '/Order/detailSisaOrderArea', $data);
    }


    public function reviseorder()
    {
        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $row = $spreadsheet->getActiveSheet();
            $nomodel = $this->request->getVar('no_model');
            $idModel = $this->orderModel->getId($nomodel);
            $this->ApsPerstyleModel->setZero($nomodel);
            $startRow = 4; // Ganti dengan nomor baris mulai
            foreach ($spreadsheet->getActiveSheet()->getRowIterator($startRow) as $row) {
                $rowIndex = $row->getRowIndex();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $row = [];
                foreach ($cellIterator as $cell) {
                    $row[] = $cell->getValue();
                }
                if (!empty($row)) {
                    $no_models = $row[29];
                    $firstSpacePosition = strpos($no_models, ' '); // Cari posisi spasi pertama
                    $no_model = substr($no_models, 0, $firstSpacePosition);
                    $machinetypeid = $row[22];
                    if ($machinetypeid == "DC168L") {
                        $machinetypeid = $machinetypeid . "SF";
                    }
                    if ($row[5] == null) {
                        return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $nomodel . '/' . $machinetypeid))->withInput()->with('success', 'Data Berhasil di revise');
                    } else {
                        if ($no_model != $nomodel) {
                            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $nomodel . '/' . $machinetypeid))->with('error', 'Nomor Model Tidak Sama. Silahkan periksa kembali' . $rowIndex);
                        } else {
                            $recordID = $row[0];
                            $articleNo = $row[30];
                            $producttype = $row[5];
                            $custCode = $row[7];
                            $description = $row[10];
                            $delivery = $row[11];
                            $rdelivery = str_replace('/', '-', (substr($delivery, -10)));
                            $delivery2 = date('Y-m-d', strtotime($rdelivery));
                            $qty = $row[12];
                            $country = $row[17];
                            $color = $row[18];
                            $size = $row[19];
                            $sam = $row[20];
                            if ($sam == null) {
                                $sam = 185;
                            }

                            $prodtype = [
                                'jarum' => $machinetypeid,
                                'prodtype' => $producttype
                            ];
                            $idProduct = $this->productModel->getId($prodtype);

                            $leadtime = $row[24];
                            $processRoute = $row[25];
                            $lcoDate = $row[26];
                            $rlcoDate = str_replace('/', '-', (substr($lcoDate, -10)));
                            $lcoDate2 = date('Y-m-d', strtotime($rlcoDate));


                            $simpandata = [
                                'machinetypeid' => $machinetypeid,
                                'size' => $size,
                                'mastermodel' => $nomodel,
                                'no_order' => $articleNo,
                                'delivery' => $delivery2,
                                'qty' => $qty,
                                'sisa' => $qty,
                                'country' => $country,
                                'color' => $color,
                                'seam' => $processRoute,
                                'smv' => $sam,
                                'production_unit' => 'PU Belum Dipilih',
                                'factory' => 'Belum Ada Area'
                            ];

                            $updateData = [
                                'kd_buyer_order' => $custCode,
                                'id_product_type' => $idProduct,
                                'seam' => $processRoute,
                                'leadtime' => $leadtime,
                                'description' => $description
                            ];
                            $validate = [
                                'size' => $size,
                                'delivery' => $delivery2,
                                'mastermodel' => $nomodel,
                                'qty' => $qty,
                                'country' => $country,
                            ];

                            $existingAps = $this->ApsPerstyleModel->checkAps($validate);
                            if (!$existingAps) {
                                $this->ApsPerstyleModel->insert($simpandata);
                            } else {
                                $id = $existingAps['idapsperstyle'];
                                $qtyLama = $existingAps['qty'];
                                $qtyBaru = $qty + $qtyLama;
                                $this->ApsPerstyleModel->update($id, ['qty' => $qtyBaru]);
                            }
                            $this->orderModel->update($idModel, $updateData);

                            // }
                        }
                    }
                }
            }
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $nomodel . '/' . $machinetypeid))->withInput()->with('success', 'Data Berhasil di revise');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $nomodel . '/' . $machinetypeid))->with('error', 'No data found in the Excel file');
        }
    }
    public function pdkDetail($noModel, $jarum)
    {
        $pdk = $this->ApsPerstyleModel->StylePerDelive($noModel, $jarum);
        $pdkPerdlv = [];
        foreach ($pdk as $perdeliv) {
            $deliv = $perdeliv['delivery'];
            $pdkPerdlv[$deliv] = $this->ApsPerstyleModel->StylePerDlv($noModel, $jarum, $deliv);
        }
        foreach ($pdkPerdlv as $deliv => $list) {
            $totalqty = 0;
            $qty = 0;
            if (is_array($list)) {
                foreach ($list as $val) {
                    if (isset($val['sisa'])) {
                        $qty += $val['qty'];
                        $totalqty = $qty;
                    }
                }
            }
            $pdkPerdlv[$deliv]['totalQty'] = $totalqty;
        }
        $totalPo = $this->ApsPerstyleModel->totalPo($noModel);
        // ini ngambil jumlah qty
        $sisaArray = array_column($pdk, 'sisa');
        $maxValue = max($sisaArray);
        $indexMax = array_search($maxValue, $sisaArray);
        $totalQty = 0;
        for ($i = 0; $i <= $indexMax; $i++) {
            $totalQty += $sisaArray[$i];
        }



        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'order' => $pdkPerdlv,
            'headerRow' => $pdk,
            'noModel' => $noModel,
            'jarum' => $jarum,
            'totalPo' => $totalPo
        ];
        return view(session()->get('role') . '/Order/detailPdk', $data);
    }
}
