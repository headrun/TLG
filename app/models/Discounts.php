<?php

class Discounts extends \Eloquent {
      protected $table = 'discount_enrollment';
	protected $fillable = [];
     /*     // for old table discount  
     public static function createDiscountForSeason($data){
         $discount=new Discounts();
         $discount->season_id=$data['id'];
         $discount->franchisee_id=$data['franchisee_id'];
         $discount->discount_second_child=$data['discount_second_child'];
         $discount->discount_second_class=$data['discount_second_class'];
         $discount->created_by=Session::get('userId');
         $discount->created_at=date("Y-m-d H:i:s");
         $discount->save();
         return $discount;
     }
       
      */

        static function InsertMultipleDiscounts($data){
            $discount = new Discounts();
            $discount->franchisee_id = Session::get('franchiseId');
            $discount->number_of_classes = $data['no_of_class'];
            $discount->discount_percentage = $data['discount_prcnt'];
            $discount->discount_second_child = 0;
            $discount->discount_second_class = 0;
            $discount->created_by = Session::get('userId');
            $discount->updated_by = Session::get('userId');
            $discount->created_at = date("Y-m-d H:i:s");
            $discount->updated_at = date("Y-m-d H:i:s");
            $discount->save();
            //return $discount;
            return $discount;
        }
        
        
        public static function getEnrollmentDiscontByFranchiseId(){
            return Discounts::where('franchisee_id','=',Session::get('franchiseId'))
                             ->get();
        }

        static function approveDiscounts($data){
            return Discounts::where('franchisee_id', '=', Session::get('franchiseId'))
                                ->update(array('discount_second_class_approve'=> $data['classCheck'], 'discount_second_child_approve'=> $data['childCheck']));

        }


        static function updateDiscounts($data){                             
            $update = Discounts::find($data['id']);
            $update->number_of_classes = $data['no_of_classes'];
            $update->discount_percentage = $data['Discount_percentage'];
            $update->save();
            return $update;
        }

        static function deleteDiscounts($data){                             
            $delete =  Discounts::find($data['id']);
            $delete->delete();
            return $delete;
        }

        static function updateSecondChild_ClassDisc($data){                             
            return Discounts::where('franchisee_id', Session::get('franchiseId'))
                                ->update(array('discount_second_child'=> $data['editChild'], 'discount_second_class'=> $data['editClass']));
        }

        static function insertSecondChild_ClassDisc($data){                             
            $discount = new Discounts();
            $discount->franchisee_id = Session::get('franchiseId');
            $discount->number_of_classes = 0;
            $discount->discount_percentage = 0;
            $discount->discount_second_child = $data['editChild'];
            $discount->discount_second_class = $data['editClass'];
            $discount->created_by = Session::get('userId');
            $discount->updated_by = Session::get('userId');
            $discount->created_at = date("Y-m-d H:i:s");
            $discount->updated_at = date("Y-m-d H:i:s");
            $discount->save();
            //return $discount;
            return $discount;
        }
}