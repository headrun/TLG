<?php

class BatchLimit extends \Eloquent {

    protected $fillable = [];
    protected $table = 'batches_limit';

    static function addBatchLimit($input) {
        $batchlimit = new BatchLimit();

        $batchlimit->franchisee_id = Session::get('franchiseId');
        $batchlimit->batches_limit_no = (BatchLimit::max('batches_limit_no'))+1;
        $batchlimit->batch_limit_receptionist = $input['batch_limit_recep'];
        $batchlimit->batch_limit_admin = $input['batch_limit_admin'];
        $batchlimit->created_by = Session::get('userId');
        $batchlimit->created_at = date("Y-m-d H:i:s");
        $batchlimit->save();

        return $batchlimit;
    }

    static function getAllBatchesLimitbyFranchiseId() {
        return BatchLimit::where('franchisee_id', '=', Session::get('franchiseId'))
                        ->get();
    }
    
    static function deleteBatchLimitById($batcheslimitId){
            return BatchLimit::where('id','=',$batcheslimitId)
                                  ->delete();
    }

}
