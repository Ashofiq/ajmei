<html>
<head>
    <style>
        @page {
            margin-top: 1.5cm;
        }

        footer {
            width: 100%;
            bottom: 5%;
        }

        body {
            font-family: sans-serif;
            font-size: 9pt;
        }

        table.items {
            border: 0.1mm solid #000000;
        }

        td {
            vertical-align: top;
        }

        .items td {
            border-left: 0.1mm solid #000000;
            border-right: 0.1mm solid #000000;
        }

        table thead td {
            background-color: #EEEEEE;
            text-align: center;
            border: 0.1mm solid #000000;
            font-variant: small-caps;
        }

        .items td.blanktotal {
            background-color: #EEEEEE;
            border: 0.1mm solid #000000;
            background-color: #FFFFFF;
            border: 0mm none #000000;
            border-top: 0.1mm solid #000000;
            border-right: 0.1mm solid #000000;
        }

        .items td.totals {
            text-align: right;
            border: 0.1mm solid #000000;
        }

        .items td.cost {
            text-align: center;
        }

        .barcode {
            margin-left: 0px;
            margin-right: -15px;

            vertical-align: middle;
            color: #000000;
            float: right;
        }

        .barcodecell {
            text-align: right;
            vertical-align: middle;
            padding: 0;
            margin-left: 0px;

        }

    </style>

    <style>
        body {
            font-family: "Montserrat", sans-serif;
            font-weight: 400;
            color: #322d28;
        }

        header.top-bar h1 {
            font-family: "Montserrat", sans-serif;
        }

        main {
            margin-top: 0rem;
            min-height: calc(100vh - 107px);
        }

        main .inner-container {
            max-width: 800px;
            margin: 0 auto;
        }

        table.invoice {
            background: #fff;
        }

        table.invoice .num {
            font-weight: 200;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: .8em;
        }

        table.invoice tr, table.invoice td {
            background: #fff;
            text-align: left;
            font-weight: 400;
            color: #322d28;
        }

        table.invoice tr.header td img {
            max-width: 300px;
        }

        table.invoice tr.header td h2 {
            text-align: right;
            font-family: "Montserrat", sans-serif;
            font-weight: 200;
            font-size: 2rem;
            color: #1779ba;
        }

        table.invoice tr.intro td:nth-child(2) {
            text-align: right;
        }

        table.invoice tr.details > td {
            padding-top: 4rem;
            padding-bottom: 0;
        }

        table.invoice tr.details td.id, table.invoice tr.details td.qty, table.invoice tr.details th.id, table.invoice tr.details th.qty {
            text-align: center;
        }

        table.invoice tr.details td:last-child, table.invoice tr.details th:last-child {
            text-align: right;
        }

        table.invoice tr.details table thead, table.invoice tr.details table tbody {
            position: relative;
        }

        table.invoice tr.details table thead:after, table.invoice tr.details table tbody:after {
            content: '';
            height: 1px;
            position: absolute;
            width: 100%;
            left: 0;
            margin-top: -1px;
            background: #c8c3be;
        }

        table.invoice tr.totals td {
            padding-top: 0;
        }

        table.invoice tr.totals table tr td {
            padding-top: 0;
            padding-bottom: 0;
        }

        table.invoice tr.totals table tr td:nth-child(1) {
            font-weight: 500;
        }

        table.invoice tr.totals table tr td:nth-child(2) {
            text-align: right;
            font-weight: 200;
        }

        table.invoice tr.totals table tr:nth-last-child(2) td {
            padding-bottom: .5em;
        }

        table.invoice tr.totals table tr:nth-last-child(2) td:last-child {
            position: relative;
        }

        table.invoice tr.totals table tr:nth-last-child(2) td:last-child:after {
            content: '';
            height: 4px;
            width: 110%;
            border-top: 1px solid #1779ba;
            border-bottom: 1px solid #1779ba;
            position: relative;
            right: 0;
            bottom: -.575rem;
            display: block;
        }

        table.invoice tr.totals table tr.total td {
            font-size: 1.2em;
            padding-top: .5em;
            font-weight: 700;
        }

        table.invoice tr.totals table tr.total td:last-child {
            font-weight: 700;
        }

        .additional-info h5 {
            font-size: .8em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1779ba;
        }

        .myButton {
            background-color: #44c767;
            border-radius: 28px;
            border: 1px solid #18ab29;
            display: inline-block;
            cursor: pointer;
            color: #ffffff;
            font-family: Arial;
            font-size: 17px;
            padding: 16px 31px;
            text-decoration: none;
            text-shadow: 0px 1px 0px #2f6627;
        }

        .myButton:hover {
            background-color: #5cbf2a;
        }

        .myButton:active {
            position: relative;
            top: 1px;
        }


    </style> 
</head>
<body>
<div class="row expanded" style="background-image: url('dist/img/bg.jpg')">
    <main class="columns">
        <div class="inner-container">

            <section class="" style="#ccc ;padding: 5px">
                <header>
                    <table width="100%">
                        <tr>
                            <td align="left" valign="top" style="width:15%; ">
                                <img src="dist/img/grouplogo.png" alt="Ajmeri" style="width:30%;">
                            </td>
                            <td align="center" valign="top" style="width:55%; font-size: 12px;">
                                <p style="font-size: 15px;"><span style="font-size: 20px; text-align: center; font-weight: bold">AJMERI GOLDEN FIBER</span> <br/> Silo Road, Santahar.<br/>Phone : 01780090228, 01780090375<br/>Email:ajmerigoldenfiber@gmail.com <br/> www.ajmerigroup.com 
                            </td>
                            <td align="right" valign="top" style="width:30%; font-size: 12px;">
                                <img src="dist/img/logo.jpg" alt="Ajmeri" style="width:18%;">
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" style="width:25%; "></td>
                            <td align="center" valign="top" style="width:25%; "><p style="font-size: 20px; text-align: center; font-weight: bold">Sales Order</p>
                            <!-- <p style="float:center; width: 100%;">  {{ $rows_m->remark }}</p></td> -->
                            <td valign="top" style="width:25%; "></td>
                        </tr>

                    </table>
                    <br>
                    <br>

                </header>


                <div>
                    <!--    -->
                    <table width="100%" style="font-family: serif; margin-top:-10px;" cellpadding="10">
                        <tr>
                            <td width="35%" style="border: 0.1mm solid #888888;">
                                <b>Name : {{ $rows_m->cust_name }}</b><br/>
                                Customer ID : AJS-000{{ $rows_m->cust_slno }}<br/>
                                Address : {{ $rows_m->cust_add1 }} <br/>
                                Office : {{ $rows_m->cust_mobile }}<br/> 
                                Personal : {{ $rows_m->personalMobileno }}<br/> 
                            </td>
                            
                            <td width="20%">
                                <p style="float:center; width: 100%">  {{ $rows_m->remark }}</p>
                            </td>
                            <td width="30%" style="border: 0.1mm solid #888888;">
                                <span>Order No :</span> {{ $rows_m->so_order_no }}<br/>  
                                Order Date : {{ date('d/m/Y', strtotime($rows_m->so_order_date)) }} <br>
                                Delivery Address : {{ $rows_m->cust_add2 }}
                            </td>

                        </tr>
                    </table>

                    <br/>

                    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
                        <thead>

                        <tr>
                            <td width="5%">Sl. No.</td>
                            <td width="40%">Item & Description</td> 
                            <td width="15%">Size</td>
                            <td width="6%">Weight/<br/>Pcs</td>
                            <td width="6%">Qty<br/>Pcs</td>
                            <!-- <td width="10%">Total Weight (KG)</td> -->
                            <td width="7%">Sales Unit</td>
                            <td width="10%">Unit Price</td>
                        <?php $colspan = 6; $diskCol = false; ?>
                        @foreach($rows_d as $sale_details)
                            
                            @if($sale_details->so_order_disc > 0.00)
                            <?php $colspan = 8; $diskCol = true; ?>
                            <td width="10%">Disc.</td>
                            <td width="10%">Disc Amount</td>
                                @break
                            @endif
                            
                        @endforeach
                            <td width="10%">Amount</td>
                        </tr>

                        </thead>
                        <tbody>
                        <!-- ITEMS HERE -->
                        <?php
                          $i = 1;  
                          $tot_qty = 0; 
                          $total = 0; 
                          $s_itm_disc = 0; 
                        ?>
                  
                        @foreach($rows_d as $sale_details)
                         <?php 
                          if($sale_details->so_item_unit == 'KG'){
                            $sub_total = (($sale_details->so_item_weight * $sale_details->so_item_pcs) / 1000) * $sale_details->so_item_price;
                          }else{
                            $sub_total = $sale_details->so_item_price * $sale_details->so_item_pcs;  
                          }
                         
                          $inv_itm_disc = round(($sub_total*$sale_details->so_order_disc)/100,2);

                          $s_total = round($sub_total, 2) - round($inv_itm_disc,2);
                          $total += $s_total;
                          $s_itm_disc += $inv_itm_disc; 
                        //   $tot_qty  += $sale_details->so_order_qty; 
                          ?>
                            <tr>
                                <td align="center" width="5%"
                                    style="border-bottom: 1px solid #cccccc;"><?php echo $i ?></td>
                                <td align="left" width="15%" style="border-bottom: 1px solid #cccccc;">
                                <b>{{ $sale_details->itm_cat_name }} -  {{ $sale_details->nd_name }}</b> <br/>
                                {{ $sale_details->item_name }}<br/>
                                {{ $sale_details->so_item_spec }}
                                </td>
                                <td align="center" width="15%"
                                    style="border-bottom: 1px solid #cccccc;">
                                    @if($sale_details->so_item_size != '' or $sale_details->so_item_size != null)
                                        {{ $sale_details->so_item_size }} Inch.
                                    @endif
                                </td> 

                                <td align="center" width="5%"
                                    style="border-bottom: 1px solid #cccccc;">
                                    {{ $sale_details->so_item_weight }} gm</td>
                                
                                <td align="center" width="5%"
                                    style="border-bottom: 1px solid #cccccc;">
                                    {{ $sale_details->so_item_pcs }}</td>
                                
                                <!-- <td align="center" width="5%"
                                    style="border-bottom: 1px solid #cccccc;">{{ $sale_details->so_order_qty }}</td>    -->
                                
                                <td align="center" width="5%"
                                    style="border-bottom: 1px solid #cccccc;">
                                    {{ $sale_details->so_item_unit }}</td>
                                
                                <td align="center" width="5%"
                                    style="border-bottom: 1px solid #cccccc;">{{ $sale_details->so_item_price }}</td>
                                
                                @if($sale_details->so_order_disc  > 0.00)
                                <td align="center" width="5%"
                                    style="border-bottom: 1px solid #cccccc;">{{ $sale_details->so_order_disc }}</td> 
                                    
                                <td align="center" width="5%"
                                    style="border-bottom: 1px solid #cccccc;">
                                    {{ number_format($inv_itm_disc, 0) }} </td> 
                                    
                                @else
                                    @if($diskCol == true)
                                    <td align="center" width="5%"
                                        style="border-bottom: 1px solid #cccccc;">0.00</td> 
                                        
                                    <td align="center" width="5%"
                                        style="border-bottom: 1px solid #cccccc;">
                                        0.00 </td> 
                                    @endif
                                @endif

                                <td align="right" style="text-align:right; border-bottom: 1px solid #cccccc;"
                                    width="15%">{{ number_format($s_total, 0) }} Tk
                                </td>
                            </tr>
                            <?php
                            $i++; ?>
                          @endforeach
                        <?php
                          //$Total_Itm_Disc = $rows_m->so_total_disc; 
                         // $Total_Disc = $rows_m->so_total_disc   // total item disc + overll disc
 $Total_Disc = $rows_m->so_disc_value   // total item disc + overll disc

                         
                        ?>

                        <tr>
                            <td class="totals" colspan="{{ $colspan }}" rowspan="8" align = "left"></td>
                            <td class="totals"><b>Total:</b></td>
                            <td style="text-align:right" class="totals cost">
                                <b>{{ number_format($total , 0) }} Tk</b></td>
                        </tr>
                    @if($Total_Disc > 0)   
                        <tr>  
                            <td class="totals"><b>Discount:</b></td>
                            <td style="text-align:right" class="totals cost">
                                <b>{{ number_format($Total_Disc , 0) }} Tk</b></td>
                        </tr>
                    @endif
                    @if($rows_m->so_carring_cost > 0)   
                        <tr> 
                            <td class="totals"><b>Carring Cost:</b></td>
                            <td style="text-align:right" class="totals cost">
                                <b>{{ number_format($rows_m->so_carring_cost , 0) }} Tk</b></td>
                        </tr>
                    @endif
                    @if($rows_m->so_labour_cost > 0)
                        <tr> 
                            <td class="totals"><b>Labour Cost:</b></td>
                            <td style="text-align:right" class="totals cost">
                                <b>{{ number_format($rows_m->so_labour_cost , 0) }} Tk</b></td>
                        </tr>
                    @endif
                    @if($rows_m->so_load_unload_cost > 0)
                        <tr> 
                            <td class="totals"><b>Load/Unload Cost:</b></td>
                            <td style="text-align:right" class="totals cost">
                                <b>{{ number_format($rows_m->so_load_unload_cost , 0) }} Tk</b></td>
                        </tr>
                    @endif
                    @if($rows_m->so_service_charge > 0)
                        <tr> 
                            <td class="totals"><b>Service Charge:</b></td>
                            <td style="text-align:right" class="totals cost">
                                <b>{{ number_format($rows_m->so_service_charge , 0) }} Tk</b></td>
                        </tr>
                    @endif    
                    @if($rows_m->so_other_cost > 0)
                        <tr> 
                            <td class="totals"><b>Other Cost:</b></td>
                            <td style="text-align:right" class="totals cost">
                                <b>{{ number_format($rows_m->so_other_cost , 0) }} Tk</b></td>
                        </tr>
                    @endif   
                    
                    @if($rows_m->so_special_offer > 0)
                        <tr> 
                            <td class="totals"><b>Special Offer:</b></td>
                            <td style="text-align:right" class="totals cost">
                                <b>{{ $rows_m->so_special_offer }} Tk</b></td>
                        </tr>
                    @endif
                        
                        <!--<tr> -->
                        <!--    <td class="totals"><b>Special Offer:</b></td>-->
                        <!--    <td style="text-align:right" class="totals cost">-->
                        <!--        <b>{{ $rows_m->so_special_offer }} Tk</b></td>-->
                        <!--</tr>-->
                        
                        <tr> 
                            <td class="totals"><b>Net Payable:</b></td>
                            <td style="text-align:right" class="totals cost">
                                <!--<b><?php echo $rows_m->so_net_amt - $rows_m->so_special_offer; ?> Tk</b></td>-->
                                <b><?php echo $total - $rows_m->so_special_offer; ?> Tk</b></td>
                                
                        </tr>

                         
                        </tbody>
                    </table>


                </div>
                <footer>

                    <div>
                        <p style="text-align:right; text-transform: capitalize;"><b>Payment in words
                                : {{$inWordAmount}} Only</b></p>
                    </div>
                   
                    <table width="100%" style="margin-top: 2px;">
                    
                    <tr>
                        <td style="text-align:left;">
                        <p>Expected Delivery Date : {{ date('d/m/Y', strtotime($rows_m->so_req_del_date)) }} </p>
                        </td>
                    </tr>
                        <tr>
                            <td style="text-align:left;">
                                <p>
                                    <span style="text-decoration: underline; font-weight: bold;">Terms & Condition</span> <br>
                                    1. <b>Delivery Delay </b>: Delivery date may be changed for any unavoidable circumstances.<br/>
                                    2. <b>Office Time </b>: 10.00 AM to 6.00 PM (Friday Close ).<br/>
                                    3. <b>Contact Information </b>: 01780090228, 01780090375, ajmerigoldenfiber@gmail.com<br/>
                                    4.<b> Terms of Payment </b>: 50% Advance with sales order/work order and balance 50% before delivery. <br>
                                    5. <b>Mode of payment</b> : By Bank Transfer / Cheque. cheque payment should be in favour of "AJMERI GOLDEN FIBER".<br>
                                    IF payment were made by cheque the delivery would be after encashment of cheque.<br>
                                    <b>Bank Information : A/C Name : Ajmeri Golden Fiber,</b> <br>
                                    A/C No. : 3162901800001 , Branch : Head Office , The City Bank Ltd. Routing No # 225272684.<br/>
                                   A/C No. : 0305102000554 , Branch : Santahar , Pubali Bank Ltd. Routing No # 175102417<br/>
                                   A/C No. : 20500130900002203 , Branch : Santahar , Islami Bank Bangladesh Ltd. Routing No # 125102425<br/>  
                                </p>

                            </td>

                        </tr> 
                    </table>
                    <table width="100%" style="margin-top: 30px;">
                        <tr>
                            <td style="width:45%; text-align:left;"><span style=" border-top: 1px solid #111111;">Customer Signature</span>
                            </td>
                            <td style="width:45%; text-align:right;"><span style=" border-top: 1px solid #111111;">Ajmeri Authority Signature</span>
                            </td>
                        </tr> 
                    </table>
                    <br/>
                    Print Time : <?php
                                date_default_timezone_set('Asia/Dhaka');
                                echo date("M,d,Y h:i:s A") . "\n";?>
                </footer> 
            </section>
        </div>
    </main>
</div>
</body>
</html>