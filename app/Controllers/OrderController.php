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

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
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
    public function order()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
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
        return view('Capacity/Order/ordermaster', $data);
    }


    public function detailModelCapacity($noModel, $delivery)
    {
        $dataApsPerstyle = $this->ApsPerstyleModel->detailModel($noModel, $delivery); // Call the model method
        $data = [
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
        ];
        return view('Capacity/Order/detailOrder', $data);
    }

    public function semuaOrder()
    {
        $tampilperdelivery = $this->orderModel->tampilPerdelivery();
        $product = $this->productModel->findAll();
        $data = [
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
        return view('Capacity/Order/semuaorder', $data);
    }

    public function belumImport()
    {
        $tampilperdelivery = $this->orderModel->tampilbelumImport();
        $product = $this->productModel->findAll();
        $data = [
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
        return view('Capacity/Order/semuaorder', $data);
    }

    public function orderPerJarum()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
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
        return view('Capacity/Order/orderjarum', $data);
    }
    public function orderPerJarumBln()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
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
        return view('Capacity/Order/orderjarumbln', $data);
    }

    public function detailmodeljarum($noModel, $delivery, $jarum)
    {
        $apsPerstyleModel = new ApsPerstyleModel(); // Create an instance of the model
        $dataApsPerstyle = $apsPerstyleModel->detailModelJarum($noModel, $delivery, $jarum); // Call the model method
        $data = [
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

        return view('Capacity/Order/detailModelJarum', $data);
    }



    public function DetailOrderPerJarum($jarum)
    {
        $tampilperdelivery = $this->orderModel->tampilPerjarum($jarum);
        $product = $this->productModel->findAll();
        $booking = $data = [
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
        return view('Capacity/Order/semuaorderjarum', $data);
    }

    public function DetailOrderPerJarumBlnDetail($bulan, $tahun, $jarum)
    {
        $tampilperdelivery = $this->orderModel->tampilPerjarumBulan($bulan, $tahun, $jarum);
        $product = $this->productModel->findAll();
        $booking = $data = [
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
        return view('Capacity/Order/semuaorderjarum', $data);
    }

    public function DetailOrderPerJarumBln($jarum)
    {
        $bulan = $this->ApsPerstyleModel->getBulan($jarum);
        $product = $this->productModel->findAll();
        $booking = $data = [
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
        return view('Capacity/Order/orderjarumblngroup', $data);
    }
    public function DetailOrderPerJarumPlan($jarum)
    {
        $tampilperdelivery = $this->orderModel->tampilPerjarum($jarum);
        $product = $this->productModel->findAll();
        $booking = $data = [
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
        return view('Planning/Order/semuaorderjarum', $data);
    }
    public function DetailOrderPerAreaPlan($area)
    {
        $tampilperdelivery = $this->orderModel->tampilPerarea($area);
        $product = $this->productModel->findAll();
        $booking = $data = [
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
        return view('Planning/Order/semuaorderarea', $data);
    }
    public function updateorder($idOrder)
    {

        $data = [
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
            return redirect()->to(base_url('capacity/detailmodel/' . $modl . '/' . $del))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url('capacity/detailmodel/' . $modl . '/' . $del))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function updateorderjarum($idOrder)
    {

        $data = [
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
            return redirect()->to(base_url('capacity/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url('capacity/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('error', 'Gagal Update Data');
        }
    }

    public function updatemesinperjarum($idDataMesin)
    {

        $data = [
            'total_mesin' => $this->request->getPost("total_mc"),
            'brand' => $this->request->getPost("brand"),
            'mesin_jalan' => $this->request->getPost("mesin_jalan"),
        ];
        $id = $idDataMesin;
        $update = $this->jarumModel->update($id, $data);
        $area = $this->request->getPost("area");
        if ($update) {
            return redirect()->to(base_url('capacity/datamesinperjarum/' . $area))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url('capacity/datamesinperjarum/' . $area))->withInput()->with('error', 'Gagal Update Data');
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
            return redirect()->to(base_url('capacity/detailmodel/' . $modl . '/' . $del))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url('capacity/detailmodel/' . $modl . '/' . $del))->withInput()->with('error', 'Gagal Hapus Data');
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
            return redirect()->to(base_url('capacity/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url('capacity/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('error', 'Gagal Hapus Data');
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
                'sisa_booking' => $sisa_booking,
                'status' => $status
            ];
            $this->bookingModel->update($id, $data);
            return redirect()->to(base_url('capacity/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
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
                return redirect()->to(base_url('capacity/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
            }
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
                        return redirect()->to(base_url('/capacity/semuaOrder'))->with('error', 'Nomor Model Tidak Sama. Silahkan periksa kembali');
                    } else {
                        if ($row[5] == null) {
                            break;
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
                                'delivery' => $delivery2
                            ];

                            $existingAps = $this->ApsPerstyleModel->checkAps($validate);
                            if (!$existingAps) {
                                $this->ApsPerstyleModel->insert($simpandata);

                                $this->orderModel->update($idModel, $updateData);
                            } else {
                                $sumqty = $existingAps->qty + $qty;
                                $sumsisa = $existingAps->sisa + $qty;
                                $idAps = $existingAps->idapsperstyle;
                                $this->ApsPerstyleModel->update($idAps, ['qty' => $sumqty, 'sisa' => $sumsisa]);
                            }
                        }
                    }
                }
            }
            return redirect()->to(base_url('/capacity/semuaOrder'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url('/capacity/semuaOrder'))->with('error', 'No data found in the Excel file');
        }
    }
    public function deletedetailorder($idModel)
    {

        $idModel = $this->request->getPost("no_model");
        $id = $idModel;
        $delete = $this->ApsPerstyleModel->where('Mastermodel', $id)->delete();
        if ($delete) {
            return redirect()->to(base_url('capacity/semuaOrder/'))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url('capacity/semuaOrder/'))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }

    // Planning
    public function detailModelPlanning($noModel, $delivery)
    {
        $dataApsPerstyle = $this->ApsPerstyleModel->detailModel($noModel, $delivery);
        $dataMc = $this->jarumModel->getAreaModel($noModel);
        $data = [
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
        return view('Planning/Order/detailOrder', $data);
    }

    public function orderBlmAdaAreal()
    {
        $tampilperdelivery = $this->orderModel->tampilPerModelBlmAdaArea();
        $product = $this->productModel->findAll();
        $data = [
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
        return view('Planning/Order/orderBlmAdaArea', $data);
    }
    public function semuaOrderPlan()
    {
        $tampilperdelivery = $this->orderModel->tampilPerdelivery();
        $product = $this->productModel->findAll();
        $data = [
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
        return view('Planning/Order/semuaorder', $data);
    }
    public function orderPerJarumPlan()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
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
        return view('Planning/Order/orderjarum', $data);
    }
    public function orderPerAreaPlan()
    {
        $totalMesin = $this->jarumModel->getArea();
        $data = [
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
        return view('Planning/Order/orderarea', $data);
    }
    public function getTurunOrder()
    {
        $resultTurunOrder = $this->orderModel->getTurunOrder();
        $charts = $this->orderModel->chartTurun();
        $bulan = array_keys($charts['details']);
        $jumlahTurun = array_values($charts['totals']);
        $data = [
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
        return view('Capacity/Order/turunOrder', $data);
    }

    public function detailturunorder($week, $buyer)
    {
        $resultTurun = $this->orderModel->getDetailTurunOrder($week, $buyer);
        $data = [
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
        return view('Capacity/Order/detailturunorder', $data);
    }
}
