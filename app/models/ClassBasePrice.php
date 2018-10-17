<?php

class ClassBasePrice extends \Eloquent {
    protected $fillable = [];
    protected $table = 'classes_baseprice';
    
    public function Users(){
        return $this->belongsTo('User','created_by');
    }
    
    public static function insertBasePrice($inputs){
        $base_price=new ClassBasePrice();
        $temp=ClassBasePrice::where('franchise_id','=',Session::get('franchiseId'))->max('base_price_no');
        $temp=$temp+1;
        $base_price->base_price_no=$temp;
        $base_price->base_price=$inputs['base_price'];
        $base_price->franchise_id=Session::get('franchiseId');
        $base_price->created_by=Session::get('userId');
        $base_price->created_at=date("Y-m-d H:i:s");
        $base_price->save();
        return $base_price;
    }
    public static function getBasePricebyFranchiseeId(){
        return ClassBasePrice::where('franchise_id','=',Session::get('franchiseId'))->get();
    }
    
    public static function insertNewBasePrice($inputs, $franchiseId) {
        $base_price = new ClassBasePrice();
        $base_price->base_price_no = 1;
        $base_price->base_price = $inputs['base_price'];
        $base_price->franchise_id = $franchiseId;
        $base_price->created_by = Session::get('userId');
        $base_price->created_at = date("Y-m-d H:i:s");
        $base_price->save();
        return $base_price;
    }

    public static function updateClassBasePrice($inputs) {
        $updateClsBasePrice = ClassBasePrice::where('franchise_id','=', $inputs['franchisee_id'])
                                            ->update([
                                                'base_price' => $inputs['base_price'],
                                                'updated_at' => date("Y-m-d H:i:s")
                                            ]);
        return $updateClsBasePrice;
    }
}