<html>
<head>
    <style>
        body { margin: 0; font-size: 12px; font-family: "Arrial Narrow";}
            /** Define the margins of your page **/
        @page {
            margin: 100px 55px;
        }
        table th, td{padding: 5px}
        header {
            position: fixed;
            top: -45px;
            left: 0px;
            right: 0px;
            height: 50px;
            /** Extra personal styles **/

            color: white;
            text-align: center;
            line-height: 35px;
        }

        footer {
            position: fixed;
            bottom: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        h1 {
            border-bottom-style: solid;
        }
    </style>
</head>
<body>
<div class="row expanded" style="background-image: url('dist/img/bg.jpg')">
    <main class="columns">
        <div class="inner-container">
            
            <section class="" style="#ccc ;padding: 5px">
                
                <div class="row">
                    <h2 style=" text-align: center;">Sales Order List (Pending)</h2>
                    <h4 style=" text-align: center;">{{ date('d-m-Y', strtotime($fromdate)) }} TO
                {{ date('d-m-Y', strtotime($todate)) }}</h4>
                </div>

                <div class="col-md-12">
                    <table border="1">
                        <thead>
                            <tr>
                                <th class="text-center" align="center" scope="col">#</th>
                                <th class="text-center" align="center" scope="col">Order No</th>
                                <th class="text-center" align="center" scope="col">Order Date</th>
                                <th class="text-center" align="center" scope="col">Party Name</th>
                                <th class="text-center" align="center" scope="col">FPO No</th>
                                <th class="text-center" align="center" scope="col">FPO Date</th>
                                <th class="text-center" align="center" scope="col">Exp Del Date</th>
                                <th class="text-center" align="center" scope="col">Description</th>
                                <th class="text-center" align="center" scope="col">Size</th>
                                <th class="text-center" align="center" scope="col">Per Pcs Weight</th>
                                <th class="text-center" align="center" scope="col">Order qty(pcs)</th>
                                <!-- <th class="text-center" align="center" scope="col">Order qty(kg)</th> -->
                                <th class="text-center" align="center" scope="col">Delivery(pcs)</th>
                                <!-- <th class="text-center" align="center" scope="col">Delivery(kg)</th> -->
                                <th class="text-center" align="center" scope="col">Pending(pcs)</th>
                                <!-- <th class="text-center" align="center" scope="col">Pending(kg)</th> -->
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $orderPcs = 0;
                            $del_item_pcs = 0;
                            $pending_pcs = 0;
                        ?>
                            @foreach($pendingItems as $key => $row)
                            <?php 
                                $orderPcs += $row->orderPcs;
                                $del_item_pcs += $row->del_item_pcs;
                                $pending_pcs += $row->orderPcs - $row->del_item_pcs;
                            ?>
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-center">{{ $row->order_no }}</td>
                                <td class="text-center">{{ $row->so_order_date }}</td>
                                <td class="text-center">{{ $row->party }}</td>
                                <td class="text-center">000{{ $row->fpo }}</td>
                                <td class="text-center">{{ $row->fpoDate }}</td>
                                <td class="text-center">{{ $row->expDate }}</td>
                                <td class="text-center">{{ $row->spec }}</td>
                                <td class="text-center">{{ $row->size }}</td>
                                <td class="text-center">{{ $row->perPcsWeight }}</td>
                                <td class="text-center">{{ $row->orderPcs }}</td>
                                <!-- <td class="text-center">{{ $row->orderKg }}</td> -->
                                <td class="text-center">{{ $row->del_item_pcs }}</td>
                                <!-- <td class="text-center">{{ $row->del_item_kg }}</td> -->
                                <td class="text-center">{{ $row->orderPcs - $row->del_item_pcs }}</td>
                                <!-- <td class="text-center">{{ $row->orderKg - $row->del_item_kg }}</td> -->
                            </tr>
                            @endforeach

                            <tr>
                                <td colspan="10" align="right"><b>Total:</b></td>
                                <td align="right"><b>{{ $orderPcs }}</b></td>
                                <td align="right"><b>{{ $del_item_pcs }}</b></td>
                                <td align="right"><b>{{ $pending_pcs }}</b></td>
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