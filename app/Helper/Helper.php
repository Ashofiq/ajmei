<?php

namespace App\Helper;
use App\Models\Rawmaterials\RawMaterialsReceives;
use DB;
use App\Models\AccTransactionDetails;
use App\Models\Chartofaccounts;
use App\Models\Sales\SalesOrders;
use App\Models\Sales\SalesReturns;
use App\Models\Sales\SalesDeliveries;

class Helper{

    
    public static function countTotalDaysOfMonth($data){
        $data = strtotime($data);
        $month = date("m", $data);
        $year = date("Y", $data);
        $dayCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        return $dayCount;
    }
    
    public static function rawMetarialsImage($orderNo){
        $receive = RawMaterialsReceives::where('raw_order_no', $orderNo)->first();
        if($receive == null){
            return '';
        }
        return $receive->purchaseImages;
    }
    
    function convert_number_to_words($amount){ 
        $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
        // Check if there is any number after decimal
        $amt_hundred = null;
        $count_length = strlen($num);
        $x = 0;
        $string = array();
        $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
          3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
          7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
          10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
          13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
          16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
          19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
          40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
          70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
        $here_digits = array('', 'Hundred','Thousand','Lac', 'Crore');
        while( $x < $count_length ) {
            $get_divider = ($x == 2) ? 10 : 100;
            $amount = floor($num % $get_divider);
            $num = floor($num / $get_divider);
            $x += $get_divider == 10 ? 1 : 2;
            if ($amount) {
              $add_plural = (($counter = count($string)) && $amount > 9) ? '' : null;
              $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
              $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
              '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
              '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
              }else $string[] = null;
            }
        $implode_to_Rupees = implode('', array_reverse($string));
        $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
        " . $change_words[$amount_after_decimal % 10]) . ' Paisa' : '';
        return ($implode_to_Rupees ? $implode_to_Rupees . 'TK ' : '') . $get_paise; 
    }

    public static function cuttingValue($fromdate){
        $sql =  "SELECT SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,SUM(RT) as RT,SUM(GI) as GI,SUM(CI) as CI, SUM(FR) as FR,SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA FROM view_item_ledger Where item_ref_id =  737 AND item_op_comp_id = 1 AND item_op_dt < '".$fromdate."'";
    
        $opening = DB::select($sql); 

        $OPP = 0;
        foreach($opening as $op){
            $OPP = $op->OP + $op->GR + $op->SA + $op->RT + $op->GI + $op->CI + $op->FR +
             $op->SH + $op->EX + $op->DA;  
        }

        return $OPP;
    }


    public static function cuttingPurchase($date){
        $sql =  "SELECT item_op_dt,
        SUM(OP) as OP,SUM(GR) as GR,SUM(SA) as SA,SUM(RT) as RT,SUM(GI) as GI,SUM(CI) as CI, SUM(FR) as FR, SUM(SH) as SH,SUM(EX) as EX, SUM(DA) as DA  FROM view_item_ledger
        Where item_ref_id in (737) AND item_op_comp_id = 1
        AND item_op_dt = '".$date."'
        GROUP BY item_op_dt
        order by item_op_dt asc";
        $transactions = DB::select($sql);

        if(COUNT($transactions) == 0){
            return 0;
        }
        return $transactions[0]->GR;
    }

    public static function juteIssue($r_issue_item_id, $data){
        $issue = DB::table('raw_materials_issues_details')
            ->join('raw_materials_issues', 'raw_materials_issues.id', '=', 'raw_materials_issues_details.r_issue_order_id')
            ->selectRaw('r_issue_item_id, SUM(r_issue_item_qty) as r_issue_item_qty, r_issue_order_date')
            ->where('r_issue_item_id', $r_issue_item_id)
            ->where('r_issue_order_date', $data)
            ->groupBy('r_issue_item_id', 'r_issue_order_date')->get();

        if (COUNT($issue) == 0) {
            return 0;
        }
        return $issue[0]->r_issue_item_qty;
    }


    public static function getVoucherNo($orderNo)
    {
        $acctrans = AccTransactionDetails::query()
        ->join('acc_transactions', 'acc_transactions.id', '=', 'acc_transaction_details.acc_trans_id')
        ->where('acc_transaction_details.acc_invoice_no', '=', $orderNo)->first();

        if($acctrans == null){
            return '';
        }

        return $acctrans->trans_type.'-'.$acctrans->voucher_no;
    }

    public static function round($number)
    {
        return round($number, 2);
    }


    public static function trialBalance($company_code, $fromdate, $todate, $orderby)
    {
        $sql = "SELECT current_parent_id, acc_origin, order_by, parent_id, p_acc_head,acc_head, acc_code, SUM(op_d_amount) as op_debit,SUM(op_c_amount) as op_credit ,
        SUM(t_d_amount) as tr_debit,SUM(t_c_amount) as tr_credit
        FROM
        (SELECT c.parent_id as current_parent_id, c.acc_origin as acc_origin, p.order_by as order_by, p.parent_id as parent_id, p.acc_head as p_acc_head, p.acc_code as acc_code, c.acc_head as acc_head,SUM(d_amount) as op_d_amount,SUM(c_amount) as op_c_amount, 0 as t_d_amount,0 as t_c_amount
        FROM acc_transactions t
        INNER JOIN acc_transaction_details on t.id = acc_trans_id
        INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
        inner join chartofaccounts p on p.id = c.parent_id
        Where com_ref_id =  $company_code and voucher_date < '". date('Y-m-d', strtotime($fromdate))."'
        GROUP BY c.parent_id, c.acc_origin, p.order_by, p.parent_id, p.acc_head, p.acc_code, c.acc_head
        UNION ALL
        SELECT c.parent_id as current_parent_id, c.acc_origin as acc_origin, p.order_by as order_by, p.parent_id as parent_id, p.acc_head as p_acc_head, p.acc_code as acc_code, c.acc_head as acc_head,0 as op_d_amount,0 as op_c_amount,SUM(d_amount) as t_d_amount,SUM(c_amount) as t_c_amount
        FROM acc_transactions t
        INNER JOIN acc_transaction_details on t.id = acc_trans_id
        INNER JOIN chartofaccounts c on c.id = chart_of_acc_id
        inner join chartofaccounts p on p.id = c.parent_id
        Where com_ref_id = $company_code and voucher_date BETWEEN '". date('Y-m-d', strtotime($fromdate))."' and '".date('Y-m-d', strtotime($todate))."'
        GROUP BY c.parent_id, c.acc_origin, p.order_by, p.parent_id, p.acc_head, p.acc_code, c.acc_head ) as M 
        where order_by = $orderby GROUP BY current_parent_id, acc_origin, order_by, parent_id, p_acc_head, acc_code, acc_head 
        ORDER BY order_by, acc_origin ASC";
 
        $rows = DB::select($sql);

        return $rows;
    }


    public function getOrdeAmount($date, $ledger_id)
    {   
        $customerId = Chartofaccounts::where('id', $ledger_id)->first()->customerId;

        if($customerId == null){
            return 0;
        }

        $order = SalesOrders::where('so_cust_id', $customerId)
        ->where('so_order_date', $date)
        ->selectRaw('SUM(so_net_amt) as amount')
        ->groupBy('so_cust_id')
        ->get();

        if (COUNT($order) > 0) {
            return $order[0]->amount;
        }
        
        return 0;
    }


    public static function getDeliveryPcs($orderId, $itemId)
    {   
        return SalesOrders::find($orderId)
        ->join('sales_deliveries', 'sales_deliveries.del_sal_ord_id', '=', 'sales_orders.id')
        ->join('sales_delivery_details', 'sales_delivery_details.del_ref_id', '=', 'sales_deliveries.id')
        ->where('del_item_id', $itemId)->where('del_sal_ord_id', $orderId)
        ->selectRaw('SUM(del_item_pcs) as del_item_pcs')
        ->first()->del_item_pcs ?? 0;
    }
    
    public static function getDeliveryReturnPcs($orderId, $itemId)
    {   
        return SalesReturns::where('ret_sal_ord_id',$orderId)
        ->join('sales_return_details', 'sales_return_details.ret_order_id', '=', 'sales_returns.id')
        ->where('ret_item_id', $itemId)->where('ret_sal_ord_id', $orderId)
        ->selectRaw('SUM(ret_pcs) as ret_pcs')
        ->first()->ret_pcs ?? 0;
    }
   
}


