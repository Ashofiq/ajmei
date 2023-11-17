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
            border: 0.1mm solid #000000;
        }

        th {
            vertical-align: top;
            border: 0.1mm solid #000000;
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
                   

                <div class="row">
                    <h4 style=" text-align: center;">Customer Wise Sales Report</h4>
                </div>

                

                <div class="col-md-12">
                    <table>
                        <thead>
                            <tr>
                                <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
                                <th class="text-center" scope="col">Invoice&nbsp;Date</th>
                                <th class="text-center" scope="col">Invoice No</th>
                                <th class="text-center" scope="col">Customer</th>
                                <th class="text-center" scope="col">Address</th>
                                <th class="text-center" scope="col">Item Name</th>
                                <th class="text-center" scope="col">LotNo</th>
                                <th class="text-center" scope="col">Rate</th>
                                <th class="text-center" scope="col">Qty</th>
                                <th class="text-center" scope="col">Total Disc</th>
                                <th class="text-center" scope="col">Net Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
          
                            $inv_qty = 0;
                            $inv_disc_value = 0; 
                            $inv_net_amt = 0;
                        ?>
                            @foreach($rows as $row)
                            <?php 
                            $inv_qty += $row->inv_qty;
                            $inv_disc_value += $row->inv_disc_value; 
                            $inv_net_amt += $row->inv_net_amt;
                            
                            ?>
                            <tr>
                                <td style=display:none;>{{ $row->id }}</td>
                                <td>{{ $row->inv_date }}</td>
                                <td>{{ $row->inv_no }}</td>
                                <td>{{ $row->cust_name }}</td>
                                <td>{{ $row->cust_add1 }} {{ $row->cust_add2 }}</td>
                                <td>{{ $row->item_name }} ({{ $row->itm_cat_name }})</td>
                                <td>{{ $row->inv_lot_no }}</td>
                                <td>{{ $row->inv_item_price }}</td>
                                <td align="right">{{ $row->inv_qty }}</td>
                                <td align="right">{{ $row->inv_disc_value }}</td>
                                <td align="right">{{ $row->inv_net_amt }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="7">&nbsp;</td> 
                                <td align="right"><b>{{ number_format($inv_qty,2) }}</b></td>
                                <td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></td>
                                <td align="right"><b>{{ number_format($inv_net_amt,2) }}</b></td>
                            </tr>
                         
                        </tbody>
                    </table>
                </div>

        
            </section>
        </div>
    </main>
</div>
</body>
</html>