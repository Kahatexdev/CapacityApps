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

    public function __construct()
    {
        session();
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
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
        ];
        $id = $idOrder;
        $update = $this->ApsPerstyleModel->update($id, $data);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/detailmodel/' . $modl . '/' . $del))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodel/' . $modl . '/' . $del))->withInput()->with('error', 'Gagal Update Data');
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
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('error', 'Gagal Update Data');
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
                        dd($no_models);
                        return redirect()->to(base_url(session()->get('role') . '/semuaOrder'))->with('error', 'Nomor Model Tidak Sama. Silahkan periksa kembali');
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
                                $sam = 10;
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

                            // $existingAps = $this->ApsPerstyleModel->checkAps($validate);
                            // if (!$existingAps) {
                            $this->ApsPerstyleModel->insert($simpandata);

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

    public function orderBlmAdaAreal()
    {
        $tampilperdelivery = $this->orderModel->tampilPerModelBlmAdaArea();
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
        return view(session()->get('role') . '/Order/semuaorder', $data);
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
            $startRow = 2; // Ganti dengan nomor baris mulai
            $errorRows = [];

            foreach ($sheet->getRowIterator($startRow) as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                if (!empty($rowData)) {
                    $validate = [
                        'mastermodel' => $rowData[6],
                        'size' => $rowData[7]
                    ];
                    $id = $this->ApsPerstyleModel->getIdSmv($validate);
                    if ($id === null) {
                        $errorRows[] = "ID not found at row " . ($rowIndex + $startRow);
                        continue;
                    }
                    $Id = $id['idapsperstyle'] ?? 0;

                    $smv = $rowData[8];
                    $update = $this->ApsPerstyleModel->update($Id, ['smv' => $smv]);

                    if (!$update) {
                        $errorRows[] = "Failed to update row " . ($rowIndex + $startRow);
                    }
                }
            }

            if (!empty($errorRows)) {
                $errorMessage = "Errors occurred:\n" . implode("\n", $errorRows);
                dd($errorMessage);
                return redirect()->to(base_url(session()->get('role') . '/databooking'))->withInput()->with('error', $errorMessage);
            } else {
                return redirect()->to(base_url(session()->get('role') . '/databooking'))->withInput()->with('success', 'Data Berhasil di Update');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/databooking'))->withInput()->with('error', 'No data found in the Excel file');
        }
    }
    public function smvimport()
    {
        return view(session()->get('role') . '/smvimport');
    }
}
