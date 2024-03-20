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
        $booking = $data = [
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

        $booking = $this->bookingModel->getDataById($idBooking);
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'booking' => $booking,
            'jarum' => $needle

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
                $field = 'sisa_booking';
                $value = $sisa_booking;
                $this->bookingModel->update($id, [$field => $value]);
                return redirect()->to(base_url('capacity/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
            }
        }
    }

    public function importModel(){
            $request = \Config\Services::request();
			helper(['form', 'url']);
			$nomodel = $this->request->getVar('no_model');
			$file_excel = $this->request->getFile('fileexcel');
			$ext = $file_excel->getClientExtension();
			if($ext == 'xls') {
				$render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
			} else {
				$render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			}
			$spreadsheet = $render->load($file_excel);
	
			$data = $spreadsheet->getActiveSheet()->toArray();
			$db = \Config\Database::connect();

			foreach($data as $x => $row) {
				if ($x == 0) {
					continue;
				}
				
				$recordID = $row[0];
				$delFlag = $row[1];
				$articleNo = $row[2];
				$merchandiser = $row[3];
				$priority = $row[4];
				$producttype = $row[5];                
				$factorycd = $row[6];                
				$custCode = $row[7];                          
				$customerStyle = $row[8];                          
				$style = $row[9];                
				$description = $row[10];           
				$delivery = $row[11];
				$rdelivery = str_replace('/', '-', (substr($delivery,-10)));
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
				$rlcoDate = str_replace('/', '-', (substr($lcoDate,-10)));
				$lcoDate2 = date('Y-m-d', strtotime($rlcoDate));   
				$no_model = $row[27];           
				$area = $row[28];           
				$orderNo = $row[29];           
				$custOrder = $row[30];     
	
	
				$simpandata = [
					'recordID' =>$recordID,
					'articleNo' => $articleNo,
					'delivery'=> $delivery2,
					'qty'=> $qty,
					'country'=> $country,
					'color'=> $color,
					'size' => $size,
					'smv' => $sam,
					'machinetypeid' => $machinetypeid,
					'processRoute' => $processRoute,
					'lcoDate' => $lcoDate2,
					'no_model' =>$nomodel,
				];
				$db->table('aps_order_report')->insert($simpandata);

		    }			
        $updateData = [
            'seam' => $processRoute,
            'id_product_type' => $producttype,
            'kd_buyer_order' => $custCode,
            'leadtime' => $leadtime,
            'description' => $description
        ];

        $db->table('data_model')
            ->where('no_model', $nomodel)
            ->update($updateData);

        // Delete query for aps_order_report table
        $db->table('aps_order_report')
            ->where('qty IS NULL')
            ->delete();

        $query = $db->table('aps_order_report')
            ->distinct()
            ->select('"" AS column1, aps_order_report.machinetypeid, aps_order_report.no_model, aps_order_report.size, aps_order_report.delivery')
            ->select('SUM(aps_order_report.qty) AS qty, SUM(aps_order_report.qty) AS sisa')
            ->select('data_model.seam AS seam, "BELUM ADA AREAL" AS column2')
            ->join('data_model', 'aps_order_report.no_model = data_model.no_model', 'inner')
            ->where('aps_order_report.no_model', $nomodel)
            ->groupBy('aps_order_report.delivery, aps_order_report.size, aps_order_report.no_model');
        
            $results = $query->get()->getResult();

		if($results){
			return redirect()->to(base_url('capacity'))->withInput()->with('success', 'Data Berhasil Diinput');
		}
		else{
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
}
