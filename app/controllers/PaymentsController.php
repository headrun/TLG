<?php
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Crypt;
class PaymentsController extends \BaseController {
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
                
		$inputs = Input::all ();
                $batch_data=  Batches::find($inputs['batchId']);
                $eachClassCost=$batch_data->class_amount;
		$availableSession = $inputs ['availableSession'];
		
		$paymentTypes = array ();
		
		$modulus = "";
		$round = "";
		if ($availableSession > 30) {
			
			$modulus = $availableSession % 20;
			if ($modulus) {
				$round = ($availableSession - $modulus);
			}
			
			$bipayInstallments = 2;
			$bipayFirstInstallment = $modulus;
			$bipaySecondInstallment = 20;
			
			$arrayCount ['bipay'] ['eligible'] = "YES";
			$arrayCount ['bipay'] ['installments'] = 2;
			if ($modulus) {
				$arrayCount ['bipay'] ['pays'] ['0'] ['dues'] = $modulus;
				$arrayCount ['bipay'] ['pays'] ['0'] ['amount'] = ($modulus * $eachClassCost);
			} else {
				$arrayCount ['bipay'] ['pays'] ['0'] ['dues'] = 20;
				$arrayCount ['bipay'] ['pays'] ['0'] ['amount'] = (20 * $eachClassCost);
			}
			$arrayCount ['bipay'] ['pays'] ['1'] ['dues'] = 20;
			$arrayCount ['bipay'] ['pays'] ['1'] ['amount'] = (20 * $eachClassCost);
			
			$modulus = $availableSession % 10;
			if ($modulus) {
				$round = ($availableSession - $modulus);
			}
			
			$arrayCount ['multipay'] ['eligible'] = "YES";
			$arrayCount ['multipay'] ['installments'] = 4;
			
			if ($modulus) {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = $modulus;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = ($modulus * $eachClassCost);
			} else {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = 10;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = (10 * $eachClassCost);
			}
			
			$arrayCount ['multipay'] ['pays'] ['1'] ['dues'] = 10;
			$arrayCount ['multipay'] ['pays'] ['1'] ['amount'] = (10 * $eachClassCost);
			
			$arrayCount ['multipay'] ['pays'] ['2'] ['dues'] = 10;
			$arrayCount ['multipay'] ['pays'] ['2'] ['amount'] = (10 * $eachClassCost);
			
			$arrayCount ['multipay'] ['pays'] ['3'] ['dues'] = 10;
			$arrayCount ['multipay'] ['pays'] ['3'] ['amount'] = (10 * $eachClassCost);
		} else if ($availableSession > 10 && $availableSession <= 20) {
			$modulus = $availableSession % 10;
			if ($modulus) {
				$round = ($availableSession - $modulus);
			}
			$bipayInstallments = 2;
			$bipayFirstInstallment = $modulus;
			$bipaySecondInstallment = 10;
			
			$arrayCount ['bipay'] ['eligible'] = "NO";
			$arrayCount ['multipay'] ['eligible'] = "YES";
			$arrayCount ['multipay'] ['installments'] = $bipayInstallments;
			if ($modulus) {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = $modulus;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = ($modulus * $eachClassCost);
			} else {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = 10;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = (10 * $eachClassCost);
			}
			
			$arrayCount ['multipay'] ['pays'] ['1'] ['dues'] = $bipaySecondInstallment;
			$arrayCount ['multipay'] ['pays'] ['1'] ['amount'] = ($bipaySecondInstallment * $eachClassCost);
		} else if ($availableSession > 20 && $availableSession <= 30) {
			$modulus = $availableSession % 10;
			if ($modulus) {
				$round = ($availableSession - $modulus);
			}
			
			$bipayInstallments = 2;
			$bipayFirstInstallment = $modulus;
			$bipaySecondInstallment = 10;
			
			$arrayCount ['bipay'] ['eligible'] = "YES";
			$arrayCount ['bipay'] ['installments'] = 2;
			if ($modulus) {
				$arrayCount ['bipay'] ['pays'] ['0'] ['dues'] = $modulus;
				$arrayCount ['bipay'] ['pays'] ['0'] ['amount'] = ($modulus * $eachClassCost);
			} else {
				$arrayCount ['bipay'] ['pays'] ['0'] ['dues'] = 10;
				$arrayCount ['bipay'] ['pays'] ['0'] ['amount'] = (10 * $eachClassCost);
			}
			$arrayCount ['bipay'] ['pays'] ['1'] ['dues'] = 20;
			$arrayCount ['bipay'] ['pays'] ['1'] ['amount'] = (20 * $eachClassCost);
			
			$arrayCount ['multipay'] ['eligible'] = "YES";
			$arrayCount ['multipay'] ['installments'] = 3;
			
			if ($modulus) {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = $modulus;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = ($modulus * $eachClassCost);
			} else {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = 10;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = (10 * $eachClassCost);
			}
			
			$arrayCount ['multipay'] ['pays'] ['1'] ['dues'] = 10;
			$arrayCount ['multipay'] ['pays'] ['1'] ['amount'] = (10 * $eachClassCost);
			
			$arrayCount ['multipay'] ['pays'] ['2'] ['dues'] = 10;
			$arrayCount ['multipay'] ['pays'] ['2'] ['amount'] = (10 * $eachClassCost);
		} else if ($availableSession <= 10) {
			$modulus = $availableSession % 10;
			if ($modulus) {
				$round = ($availableSession - $modulus);
			}
			
			$bipayInstallments = 2;
			$bipayFirstInstallment = $modulus;
			$bipaySecondInstallment = 10;
			
			$arrayCount ['bipay'] ['eligible'] = "NO";
			$arrayCount ['bipay'] ['installments'] = 2;
			if ($modulus) {
				$arrayCount ['bipay'] ['pays'] ['0'] ['dues'] = $modulus;
				$arrayCount ['bipay'] ['pays'] ['0'] ['amount'] = ($modulus * $eachClassCost);
			} else {
				$arrayCount ['bipay'] ['pays'] ['0'] ['dues'] = 10;
				$arrayCount ['bipay'] ['pays'] ['0'] ['amount'] = (10 * $eachClassCost);
			}
			$arrayCount ['bipay'] ['pays'] ['1'] ['dues'] = 20;
			$arrayCount ['bipay'] ['pays'] ['1'] ['amount'] = (20 * $eachClassCost);
			
			$arrayCount ['multipay'] ['eligible'] = "NO";
			$arrayCount ['multipay'] ['installments'] = 3;
			
			if ($modulus) {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = $modulus;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = ($modulus * $eachClassCost);
			} else {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = 10;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = (10 * $eachClassCost);
			}
			
			$arrayCount ['multipay'] ['pays'] ['1'] ['dues'] = 10;
			$arrayCount ['multipay'] ['pays'] ['1'] ['amount'] = (10 * $eachClassCost);
			
			$arrayCount ['multipay'] ['pays'] ['2'] ['dues'] = 10;
			$arrayCount ['multipay'] ['pays'] ['2'] ['amount'] = (10 * $eachClassCost);
		
		} else if ($availableSession === 10) {
			$modulus = $availableSession % 10;
			if ($modulus) {
				$round = ($availableSession - $modulus);
			}
				
			$bipayInstallments = 2;
			$bipayFirstInstallment = $modulus;
			$bipaySecondInstallment = 10;
				
			$arrayCount ['bipay'] ['eligible'] = "NO";
			$arrayCount ['bipay'] ['installments'] = 2;
			if ($modulus) {
				$arrayCount ['bipay'] ['pays'] ['0'] ['dues'] = $modulus;
				$arrayCount ['bipay'] ['pays'] ['0'] ['amount'] = ($modulus * $eachClassCost);
			} else {
				$arrayCount ['bipay'] ['pays'] ['0'] ['dues'] = 10;
				$arrayCount ['bipay'] ['pays'] ['0'] ['amount'] = (10 * $eachClassCost);
			}
			$arrayCount ['bipay'] ['pays'] ['1'] ['dues'] = 20;
			$arrayCount ['bipay'] ['pays'] ['1'] ['amount'] = (20 * $eachClassCost);
				
			$arrayCount ['multipay'] ['eligible'] = "NO";
			$arrayCount ['multipay'] ['installments'] = 3;
				
			if ($modulus) {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = $modulus;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = ($modulus * $eachClassCost);
			} else {
				$arrayCount ['multipay'] ['pays'] ['0'] ['dues'] = 10;
				$arrayCount ['multipay'] ['pays'] ['0'] ['amount'] = (10 * $eachClassCost);
			}
				
			$arrayCount ['multipay'] ['pays'] ['1'] ['dues'] = 10;
			$arrayCount ['multipay'] ['pays'] ['1'] ['amount'] = (10 * $eachClassCost);
				
			$arrayCount ['multipay'] ['pays'] ['2'] ['dues'] = 10;
			$arrayCount ['multipay'] ['pays'] ['2'] ['amount'] = (10 * $eachClassCost);
		}
		
		$arrayCount ['round'] = $round;
		$arrayCount ['modulus'] = $modulus;
		$arrayCount ['singlepay'] = $availableSession * $eachClassCost;
		$arrayCount ['status'] = "success";
		
                
                //working for simple bipay  bipay=(enrolled classes/2)
                if($availableSession>5){
                    $arrayCount['modifiedbipay']['elligible']='YES';
                    $arrayCount['modifiedbipay']['installments']=2;
                    if($availableSession%2==0){
                     $arrayCount['modifiedbipay']['pays']['0']['dues']=$availableSession/2;
                     $arrayCount['modifiedbipay']['pays']['0']['amount']=($availableSession/2)*$eachClassCost;
                     $arrayCount['modifiedbipay']['pays']['1']['dues']=$availableSession/2;
                     $arrayCount['modifiedbipay']['pays']['1']['amount']=($availableSession/2)*$eachClassCost;
                    }else{
                     $firstsessioncount=(int)($availableSession/2);
                     $secondsessioncount=((int)($availableSession/2))+1;
                     $arrayCount['modifiedbipay']['pays']['0']['dues']=$firstsessioncount;
                     $arrayCount['modifiedbipay']['pays']['0']['amount']=$firstsessioncount*$eachClassCost;
                     $arrayCount['modifiedbipay']['pays']['1']['dues']=$secondsessioncount;
                     $arrayCount['modifiedbipay']['pays']['1']['amount']=$secondsessioncount*$eachClassCost;
                    }
                }
                
		if ($arrayCount) {
			return Response::json ( array (
					"payments" => $arrayCount 
			) );
		}
		return Response::json ( array (
				"status" => "failed" 
		) );
	}
	
	
	
	
	public function printOrder($id){
            if(Auth::check()){
		$totalSelectedClasses = '';
		$totalAmountForAllBatch = '';
		$payment_no = Crypt::decrypt($id);
		$invoice_data = InvoiceData::where('franchise_id', '=', Session::get('franchiseId'))->get();
		$paymentDueDetails = PaymentDues::where('payment_no', '=', $payment_no)->get();
		for($i = 0; $i < count($paymentDueDetails); $i++){
			$totalSelectedClasses = $totalSelectedClasses + $paymentDueDetails[$i]['selected_sessions'];
			$getBatchNname[]  = Batches::where('id', '=', $paymentDueDetails[$i]['batch_id'])->get();
			$getSeasonName[]  = Seasons::where('id', '=', $paymentDueDetails[$i]['season_id'])->get();
			$selectedSessionsInEachBatch[] = $paymentDueDetails[$i]['selected_sessions'];
			$classStartDate[] = $paymentDueDetails[$i]['start_order_date'];
			$classEndDate[] = $paymentDueDetails[$i]['end_order_date'];
			$totalAmountForEachBach[] = (int)$paymentDueDetails[$i]['payment_batch_amount'];
			$totalAmountForAllBatch = $totalAmountForAllBatch + (int)$paymentDueDetails[$i]['payment_batch_amount'];
		}
                if($paymentDueDetails[0]['membership_type_id']!=0){
                      $membership_data= MembershipTypes::find($paymentDueDetails[0]['membership_type_id']);
                      $paymentDueDetails[0]['membership_type']=$membership_data->description;
                }
		$getCustomerName = Customers::select('customer_name','customer_lastname')->where('id', '=', $paymentDueDetails[0]['customer_id'])->get();
		$getStudentName = Students::select('student_name')->where('id', '=', $paymentDueDetails[0]['student_id'])->get();
		$paymentMode = Orders::where('payment_no', '=', $payment_no)->get();
		$getTermsAndConditions = TermsAndConditions::where('franchisee_id', '=', Session::get('franchiseId'))->get();
        $franchisee_name=Franchisee::find(Session::get('franchiseId'));
        if ($paymentDueDetails[0]['tax_percentage'] <= 0) {
        	$tax_data[0]['tax_percentage'] = 0;
        } else {
        	$tax_data=TaxParticulars::where('franchisee_id','=',Session::get('franchiseId'))->get();
        }
        if (Session::get('franchiseId') === 11) {
        	$tax_data[0]['tax_particular'] = 'VAT';
        }
                $data = compact('totalSelectedClasses', 'getBatchNname',
		 'getSeasonName', 'selectedSessionsInEachBatch', 'classStartDate','franchisee_name',
		  'classEndDate', 'totalAmountForEachBach', 'getCustomerName', 'getStudentName','tax_data',
		   'paymentDueDetails', 'totalAmountForAllBatch', 'paymentMode', 'getTermsAndConditions', 'invoice_data');
		return View::make('pages.orders.printorder', $data);
		//return $discounts_amount;	
                }else{
                    return Redirect::action('VaultController@logout');
                }
	}

	public function printSummerOrder($id){
		if(Auth::check()){
                $payment_no = Crypt::decrypt($id);
                $invoice_data = InvoiceData::where('franchise_id', '=', Session::get('franchiseId'))->get();
                $paymentDueDetails = PaymentDues::where('payment_no', '=', $payment_no)->get();
		$getCustomerName = Customers::select('customer_name','customer_lastname')->where('id', '=', $paymentDueDetails[0]['customer_id'])->get();
                $getStudentName = Students::select('student_name')->where('id', '=', $paymentDueDetails[0]['student_id'])->get();
                $paymentMode = Orders::where('payment_no', '=', $payment_no)->get();
                $getTermsAndConditions = TermsAndConditions::where('franchisee_id', '=', Session::get('franchiseId'))->get();
                $franchisee_name=Franchisee::find(Session::get('franchiseId'));
                $tax_data=TaxParticulars::where('franchisee_id','=',Session::get('franchiseId'))->get();
                $data = compact('classStartDate','franchisee_name',
                  'classEndDate', 'getCustomerName', 'getStudentName','tax_data',
                   'paymentDueDetails', 'paymentMode', 'getTermsAndConditions', 'invoice_data');
                return View::make('pages.orders.printSummerOrder', $data);
                //return $discounts_amount;     
                }else{
                    return Redirect::action('VaultController@logout');
                }
			$payment_no = Crypt::decrypt($id);
			return $payment_no;
		
	}	
	
	public function printBdayOrder($oid) {
     	if(Auth::check()){
		$orderid = Crypt::decrypt($oid);
		$order_data = Orders::where ( 'orders.id', '=', $orderid )->get();
		$customer_data = Customers::where ( 'id', '=', $order_data [0] ['customer_id'] )->get ();
		$birthday_data = BirthdayParties::where ( 'id', '=', $order_data [0] ['birthday_id'] )->get ();
		$student_data = Students::where ( 'id', '=', $order_data [0] ['student_id'] )->get ();
		$order_data = $order_data [0];
        if(isset($order_data['payment_dues_id'])){
        $payment_due_data=  PaymentDues::where('id','=',$order_data['payment_dues_id'])->get();
        $payment_due_data=$payment_due_data[0];
             if(isset($payment_due_data->membership_id)){
                $membershipData=  CustomerMembership::find($payment_due_data->membership_id);
                $membershipTypeData=  MembershipTypes::getMembershipTypeByID($membershipData->membership_type_id);
                $payment_due_data->description=$membershipTypeData->description;
             }
        }
        $franchisee_name=Franchisee::find(Session::get('franchiseId'));
        $getTermsAndConditions = TermsAndConditions::where('franchisee_id', '=', Session::get('franchiseId'))->get();
		$customer_data = $customer_data [0];
		$birthday_data = $birthday_data [0];
		$student_data = $student_data [0];
		$paymentMode = Orders::where('payment_no', '=', $orderid)->get();
        if ($payment_due_data['tax_percentage'] <= 0) {
        	$tax_data[0]['tax_percentage'] = 0;
        } else {
        	$tax_data=TaxParticulars::where('franchisee_id','=',Session::get('franchiseId'))->get();
        }
        if (Session::get('franchiseId') === 11) {
        	$tax_data[0]['tax_particular'] = 'VAT';
        }
		$data = array (
				'order_data',
				'customer_data',
				'birthday_data',
				'student_data',
                                'payment_due_data',
                                'tax_data',
                                'franchisee_name',
                                'getTermsAndConditions',
				'paymentMode'
		);
		
		// print_r($data);
		return View::make ( 'pages.orders.bdayprintorder', compact ( $data ) );
        }else{
                    return Redirect::action('VaultController@logout');
        }
	}

	public static function printMembershipOrder($oid) {
		
		if(Auth::check()){

			//getting data for printing 
			$orderid = Crypt::decrypt($oid);
			$order_data = Orders::find($orderid);
			if($order_data->payment_for=='birthday'){
				PaymentsController:: printBdayOrder($oid);
			}
			if($order_data->payment_for=='enrollment'){
				PaymentsController:: printOrder(Crypt::encrypt($order_data->payment_no));	
			}
			$customer_data = Customers::find($order_data ['customer_id']);
			$membership_data = CustomerMembership::find($order_data ['membership_id']);
			$membership_type = MembershipTypes::find($order_data ['membership_type']);
			$paymentDueDetails = PaymentDues::find($order_data ['payment_dues_id']);
			$franchisee_data=Franchisee::find(Session::get('franchiseId')); 
			$getTermsAndConditions = TermsAndConditions::where('id', '=', (TermsAndConditions::max('id')))->get();
			$getTermsAndConditions = $getTermsAndConditions[0];
			if ($order_data['tax_percentage'] <= 0) {
				$order_data['tax_percentage'] = 0;
			} else {
				$tax_data=TaxParticulars::where('franchisee_id','=',Session::get('franchiseId'))->get();
			}
			if (Session::get('franchiseId') === 11) {
				$order_data['tax_particular'] = 'VAT';
			}
			//serializing data and making view
			if ($order_data['tax_percentage'] <= 0) {
				$order_data['tax_percentage'] = 0;
			} else {
				$tax_data=TaxParticulars::where('franchisee_id','=',Session::get('franchiseId'))->get();
			}
			if (Session::get('franchiseId') === 11) {
				$order_data['tax_particular'] = 'VAT';
			}
			$data=array('order_data','customer_data','membership_data',
						'membership_type','paymentDueDetails','franchisee_data',
						'getTermsAndConditions');
			return View::make( 'pages.orders.membershipprintorder',compact ($data) ); 

		}else{

			return Redirect::action('VaultController@logout');	
		
		}

	}


  public static function addorviewprices(){
      if(Auth::check()){
          $inputs=Input::all();
          $currentPage = "AddPrices_LI";
          $mainMenu = "DISCOUNTS_MENU_MAIN";
          if(isset($inputs['base_price'])){
              ClassBasePrice::insertBasePrice($inputs);
              return Redirect::action('PaymentsController@addorviewprices');
          }
          $base_price_data=ClassBasePrice::getBasePricebyFranchiseeId();
          for($i=0;$i<count($base_price_data);$i++){
              $user=User::find($base_price_data[$i]['created_by']);
              $base_price_data[$i]['created_by']=$user->first_name.$user->last_name;
              if($base_price_data[$i]['updated_by']!=0){
                  $user1=User::find($base_price_data[$i]['updated_by']);
                  $base_price_data[$i]['updated_by']=$user1->first_name.$user1->last_name;
              }
          }
          $data=array('currentPage','mainMenu','base_price_data');
         return View::make('pages.prices.add_or_view_prices',compact($data));
      }else{
        return Redirect::action('VaultController@logout');
      }
  }      
  
  public static function deletebaseprice(){
      if(Auth::check()){
          $inputs=Input::all();
          ClassBasePrice::where('id','=',$inputs['baseprice_id'])->delete();
          return Response::json(array('status'=>'success'));
      }
  }
  
  public static function updatebaseprice(){
      if(Auth::check()){
          $inputs=Input::all();
          ClassBasePrice::where('id','=',$inputs['baseprice_id'])->update(array('base_price'=>$inputs['base_price'],'updated_by'=>Session::get('userId')));
          return Response::json(array('status'=>'success'));
      }
  }
        
}

