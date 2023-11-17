<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', 'HomeController@test')->name('test'); 

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('optimize:clear');
    return "Cache is cleared";
});

Auth::routes();

Route::group(['middleware' => 'auth'], function(){

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home'); 
Route::get('home', 'HomeController@index')->name('index');
Route::get('user/password/edit', 'HomeController@passwordEdit')->name('password.edit');
Route::post('user/password/edit', 'HomeController@passwordUpdate');

// -------------Start CompanyController-----------------------------------

Route::get('/company/company/index', 'Company\CompanyController@index')->name('company.index');
Route::get('/company/table', 'Company\CompanyController@fetch_onlinepricetable')->name('company.table');

Route::get('/company/company', 'Company\CompanyController@create')->name('company.create');
Route::post('/company/store','Company\CompanyController@store')->name('company.store');
Route::post('/company/edit','Company\CompanyController@store')->name('company.edit');
Route::post('/company/update','Company\CompanyController@update')->name('company.update');
Route::get('/company/destroy/{id}/{comid}','Company\CompanyController@destroy');
// -------------End CompanyController-----------------------------------

Route::get('sys-dropdown-index', 'Settings\SysinfoController@index')->name('sys.dropdown.index');
Route::post('/sysinfo-store','Settings\SysinfoController@store')->name('sysinfo.store');
Route::post('/sysinfo-update','Settings\SysinfoController@update')->name('sysinfo.update');
Route::delete('/dropdowns/destroy/{id}','Settings\SysinfoController@destroy');

Route::post('/sysinfo-ajax_store','Settings\SysinfoController@ajax_store')->name('sysinfo.ajax.store');
Route::any('/sysinfo-search','Settings\SysinfoController@search')->name('sysinfo.search');
// -------------End SysinfoController-----------------------------------

Route::get('setting-accounts-create', 'Settings\SettingsController@index')->name('setting.accounts.create');
Route::post('/sett-mapping-store','Settings\SettingsController@store')->name('sett.mapping.store');
Route::get('/accountNameLookup/{headid}', 'Settings\SettingsController@accountNameLookup');
Route::get('/childItemCateNameLookup/{cateheadid}', 'Settings\SettingsController@childItemCateNameLookup');
Route::delete('/sett-mapping/destroy/{id}','Settings\SettingsController@destroy');
// -------------End SettingsController-----------------------------------

Route::get('/cust-index', 'Customers\CustomersController@index')->name('cust.index');
Route::post('/cust-search', 'Customers\CustomersController@search')->name('cust.search');
Route::get('/cust-create', 'Customers\CustomersController@create')->name('cust.create');
Route::get('/cust-add', 'Customers\CustomersController@add')->name('cust.add');
Route::post('/cust-store','Customers\CustomersController@store')->name('cust.store');
Route::get('/cust-edit/{id}','Customers\CustomersController@edit')->name('cust.edit');
Route::post('/cust-update','Customers\CustomersController@update')->name('cust.update');
Route::get('/cust/destroy/{id}/{comid}','Customers\CustomersController@destroy');
Route::post('/cust/get-cust','Customers\CustomersController@getCustFromChartOfAcc')->name('get-cust');
Route::get('cust-code', 'Customers\CustomersController@get_cust_code')->name('cust.get-customer-code');
Route::get('cust-code', 'Customers\CustomersController@get_cust_code')->name('cust.get-customer-code');
// -------------End CustomersController-----------------------------------

Route::get('/cust-delv-index/{id}', 'Customers\CustDeliveryInfsController@index')->name('cust.delv.index');
Route::post('/cust-delv-store','Customers\CustDeliveryInfsController@store')->name('cust.delv.store');
Route::delete('/cust/delv/destroy/{id}','Customers\CustDeliveryInfsController@destroy');
// -------------End CustDeliveryInfsController-----------------------------------

Route::get('/cust-price-index/{id}/{comp}', 'Customers\CustPriceController@index')->name('cust.price.index');
Route::get('/cust-price-index1/{id}/{comp}', 'Customers\CustPriceController@index1')->name('cust.price.index1');
Route::post('/cust-price-store','Customers\CustPriceController@store')->name('cust.price.store');
Route::post('/cust-price-store1','Customers\CustPriceController@store1')->name('cust.price.store1');
Route::any('/cust-price-search','Customers\CustPriceController@search')->name('cust.price.search');
Route::post('/cust-price-update','Customers\CustPriceController@update')->name('cust.price.update');
Route::delete('/cust/price/destroy/{id}/{cid}/{comp}','Customers\CustPriceController@destroy');
//-------------End CustPriceController-----------------------------------

Route::any('/rpt-pending-sp-cust-list','Customers\ReportController@getPendingSPCustList')->name('rpt.pending.sp.cust.list');
Route::any('/rpt-customer-list','Customers\ReportController@getCustomerList')->name('rpt.customer.list');
Route::any('/rpt-sp-wise-cust-list','Customers\ReportController@getSpWiseCustomerList')->name('rpt.sp.wise.cust.list');
Route::any('/rpt-cust-wise-price-list','Customers\ReportController@getCustWisePriceList')->name('rpt.cust.wise.price.list');
//-------------End ReportController-----------------------------------


Route::get('/sales-persons-index', 'SalesPersons\SalesPersonsController@index')->name('sales.persons.index');
Route::post('/sales-persons-store','SalesPersons\SalesPersonsController@store')->name('sales.persons.store');
Route::post('/sales-persons-update','SalesPersons\SalesPersonsController@update')->name('sales.persons.update');
Route::delete('/sales-persons/destroy/{id}','SalesPersons\SalesPersonsController@destroy');
//-------------End SalesPersonsController-----------------------------------

Route::any('/rpt-salesperson-list','SalesPersons\ReportController@getSalesPersonList')->name('rpt.salesperson.list');
//-------------End ReportController-----------------------------------

Route::post('/get-suppliers','Suppliers\SuppliersController@getSupplierFromChartOfAcc')->name('get.suppliers');
Route::get('/suppliers-index', 'Suppliers\SuppliersController@index')->name('suppliers.index');
Route::post('/suppliers-search', 'Suppliers\SuppliersController@search')->name('suppliers.search');
Route::get('/suppliers-create', 'Suppliers\SuppliersController@create')->name('suppliers.create');
Route::post('/suppliers-store','Suppliers\SuppliersController@store')->name('suppliers.store');
Route::get('/suppliers-edit/{id}','Suppliers\SuppliersController@edit')->name('suppliers.edit');
Route::post('/suppliers-update','Suppliers\SuppliersController@update')->name('suppliers.update');
Route::delete('/suppliers/destroy/{id}','Suppliers\SuppliersController@destroy');
//-------------End SuppliersController-----------------------------------


Route::any('/itm-cat-index', 'Items\ItemCategoryController@p_index')->name('itm.cat.index');
Route::post('/itm-cat-store','Items\ItemCategoryController@p_store')->name('itm.cat.store');
Route::post('/itm-cat-update','Items\ItemCategoryController@p_update')->name('itm.cat.update');

Route::delete('/itm-cat/destroy/{id}','Items\ItemCategoryController@destroy');

Route::get('/itm-cat-mkchild/{id}','Items\ItemCategoryController@mk_childcat')->name('itm.cat.mkchild');
Route::post('/itm-cat-child-store','Items\ItemCategoryController@mk_childcat_store')->name('itm.cat.child.store');
Route::post('/itm-cat-child-update','Items\ItemCategoryController@updateChild')->name('itm.cat.child.update');
Route::delete('/itm-cat-child/destroy/{id}','Items\ItemCategoryController@child_destroy');
Route::post('/itm-cat-tree-view',['uses'=>'Items\ItemCategoryController@manageItemCatTree'])->name('itm.cat.tree.view');

//-------------End ItemCategoryController-----------------------------------

Route::get('/itm-index', 'Items\ItemController@index')->name('itm.index');
Route::get('/itm-create','Items\ItemController@create')->name('itm.create');
Route::POST('/get-itm','Items\ItemController@getItem')->name('get.itm');
Route::post('/itm-update','Items\ItemController@update')->name('itm.update');
Route::get('/itm-barcode','Items\BarcodeController@index')->name('itm.barcode');
Route::any('/itm-search','Items\ItemController@p_search')->name('itm.search');
Route::any('/itm-search-barcode','Items\BarcodeController@getSearchBarcode')->name('itm.search.barcode');
Route::get('barcode/print/{id}','Items\BarcodeController@print')->name('barcode.print');

Route::post('/itm-store','Items\ItemController@store')->name('itm.store');
Route::delete('/itm/destroy/{id}','Items\ItemController@destroy');
//-------------End ItemController-----------------------------------

Route::any('/itm-barcode-view-rpt', 'Items\ReportController@getItemBarcodeList')->name('itm.barcode.view.rpt');
//-------------End ReportController-----------------------------------


Route::get('/itm-op-index', 'Items\ItemOpeningController@index')->name('itm.op.index');
Route::get('/itm-op-create','Items\ItemOpeningController@create')->name('itm.op.create');
Route::post('/itm-op-store','Items\ItemOpeningController@store')->name('itm.op.store');
Route::post('/itm-op-update','Items\ItemOpeningController@update')->name('itm.op.update');
Route::get('get-item-op-code/getdetails/{id}/getfirst', 'Items\ItemOpeningController@get_op_item');
Route::get('get-item-op-code/all', 'Items\ItemOpeningController@getItemCode')->name('op.get.item.code');
Route::any('/itm-op-search','Items\ItemOpeningController@op_search')->name('itm.op.search');
Route::delete('/itm/op/destroy/{stock}/{itmid}/{id}','Items\ItemOpeningController@destroy');
//-------------End ItemOpeningController-----------------------------------

Route::any('/itm-tree-view-rpt', 'Inventory\ReportController@getItemTreeViewReport')->name('itm.tree.view.rpt');
Route::any('/itm-opening-rpt', 'Inventory\ReportController@getItemOpeningReport')->name('itm.opening.rpt');
Route::any('/itm-stock-rpt', 'Inventory\ReportController@getItemStockReport')->name('itm.stock.rpt');
Route::any('/itm-stock-ledger-det-rpt', 'Inventory\ReportController@getItemStockLedgerDetailsReport')->name('itm.stock.ledger.det.rpt'); // not using
Route::any('/itm-stock-ledger-rpt', 'Inventory\ReportController@getItemStockLedgerReport')->name('itm.stock.ledger.rpt'); // not using 
Route::any('/itm-stock-ledger1-rpt', 'Inventory\ReportController@getItemStockLedgerReport1')->name('itm.stock.ledger1.rpt');
Route::any('/month-wise-report', 'Inventory\ReportController@month_wise_report')->name('month.wise.report');
Route::any('/rpt-daily-order-statement','Inventory\ReportController@getCustomerStatementReport')->name('rpt.daily.order.statement');


// Sp Report 
Route::any('/itm-stock-ledger2-rpt', 'Inventory\ReportController@getItemStockLedgerReport2')->name('itm.stock.ledger2.rpt');
Route::any('/yearly-finish-good-report', 'FinishGoods\FinishGoodsReceivesController@yearlyReports')->name('yearlyReports');

//  end Sp Report

Route::any('/itm-wise-purchase-rpt', 'Inventory\ReportController@getItemWisePurchaseReport')->name('itm.wise.purchase.rpt'); 
Route::any('/itm-sup-wise-purchase-rpt', 'Inventory\ReportController@getItemSuppWisePurchaseReport')->name('itm.sup.wise.purchase.rpt');
Route::any('/itm-dt-wise-damages-rpt', 'Inventory\ReportController@getDateWiseDamagesReport')->name('itm.dt.wise.damages.rpt');
Route::any('/itm-dt-wise-shortages-rpt', 'Inventory\ReportController@getDateWiseShortagesReport')->name('itm.dt.wise.shortages.rpt');
Route::any('/itm-dt-wise-expired-rpt', 'Inventory\ReportController@getDateWiseExpiredReport')->name('itm.dt.wise.expired.rpt');
//-------------End ReportController-----------------------------------

Route::any('/itm-purchase-index', 'Purchases\PurchaseOrderController@index')->name('itm.purchase.index');
Route::get('/itm-purchase-create', 'Purchases\PurchaseOrderController@create')->name('itm.purchase.create');
Route::post('/itm-purchase-store','Purchases\PurchaseOrderController@store')->name('itm.purchase.store');
Route::get('/itm-purchase-edit/{id}','Purchases\PurchaseOrderController@edit')->name('itm.purchase.edit');
Route::post('/itm-purchase-update','Purchases\PurchaseOrderController@update')->name('itm.purchase.update');
Route::get('/itm-purchase-approved/{id}','Purchases\PurchaseOrderController@approved')->name('itm.purchase.approved');
Route::delete('/itm-purchase/destroy/{id}','Purchases\PurchaseOrderController@destroy');

Route::get('/itm-purchase/item-m-view/{id}','Purchases\PurchaseOrderController@purchase_modal_view')->name('purchase.item.mview');
Route::get('/purchase-order-pdf/{id}/{curr}', 'Purchases\PurchaseOrderController@purchase_order_form')->name('purchase.order.pdf');
Route::get('/get-po-item-code/getdetails/{id}/getfirst', 'Purchases\PurchaseOrderController@get_pur_item');
//-------------End PurchaseOrderController-----------------------------------

 
Route::any('/raw-itm-receive-index', 'Rawmaterials\RawMaterialsStockController@index')->name('raw.itm.receive.index');
Route::get('/raw-itm-receive-create', 'Rawmaterials\RawMaterialsStockController@create')->name('raw.itm.receive.create');
Route::post('/raw-itm-receive-store','Rawmaterials\RawMaterialsStockController@store')->name('raw.itm.receive.store');
Route::get('/raw-itm-receive-edit/{id}','Rawmaterials\RawMaterialsStockController@edit')->name('raw.itm.receive.edit');
Route::post('/raw-itm-receive-update','Rawmaterials\RawMaterialsStockController@update')->name('raw.itm.receive.update');
Route::get('/get-raw-item-code/getdetails/{id}/getfirst','Rawmaterials\RawMaterialsStockController@get_pur_item');
Route::delete('/itm-raw/destroy/{id}','Rawmaterials\RawMaterialsStockController@destroy');
Route::get('/itm-raw/item-m-view/{id}','Rawmaterials\RawMaterialsStockController@raw_modal_view')->name('purchase.item.mview');
Route::get('/itm-raw/mrr/{id}','Rawmaterials\RawMaterialsStockController@raw_mrr_view')->name('purchase.mrr.view');

Route::get('/raw-invoice/acc-m-view/{id}/{fin_year_id}','Rawmaterials\RawMaterialsStockController@acc_modal_view')->name('invoice.jv.mview');

//-------------End RawMaterialsStockController-----------------------------------


Route::any('/raw-itm-issue-prod-index', 'Rawmaterials\RawMaterialsIssuesController@index')->name('raw.itm.issue.prod.index');
Route::get('/raw-itm-issue-create', 'Rawmaterials\RawMaterialsIssuesController@create')->name('raw.itm.issue.create');
Route::post('/raw-itm-issue-store','Rawmaterials\RawMaterialsIssuesController@store')->name('raw.itm.issue.store');
Route::get('/raw-itm-issue-edit/{id}','Rawmaterials\RawMaterialsIssuesController@edit')->name('raw.itm.issue.edit');
Route::post('/raw-itm-issue-update','Rawmaterials\RawMaterialsIssuesController@update')->name('raw.itm.issue.update');
Route::get('/get-raw-item-code/getdetails/{id}/getfirst','Rawmaterials\RawMaterialsIssuesController@get_pur_item');
Route::delete('/itm-raw-issue/destroy/{id}','Rawmaterials\RawMaterialsIssuesController@destroy');
Route::get('/itm-raw-issue/item-m-view/{id}','Rawmaterials\RawMaterialsIssuesController@raw_modal_view')->name('purchase.item.mview');

Route::get('/raw-issue-invoice/acc-m-view/{id}/{fin_year_id}','Rawmaterials\RawMaterialsIssuesController@acc_modal_view')->name('invoice.jv.mview');

Route::get('/raw-itm-issue-confirm/{id}','Rawmaterials\RawMaterialsIssuesController@issue_confirmed')->name('raw.itm.issue.confirm');
 
//-------------End RawMaterialsIssuesController-----------------------------------


Route::any('/itm-consumable-index', 'Rawmaterials\RawMaterialsConsumableController@index')->name('itm.consumable.index');
Route::get('/itm-consumable-create', 'Rawmaterials\RawMaterialsConsumableController@create')->name('itm.consumable.create');
Route::post('/itm-consumable-store','Rawmaterials\RawMaterialsConsumableController@store')->name('itm.consumable.store');
  
Route::get('/itm-consumable-edit/{id}','Rawmaterials\RawMaterialsConsumableController@edit')->name('itm.consumable.edit');
Route::post('/itm-consumable-update','Rawmaterials\RawMaterialsConsumableController@update')->name('itm.consumable.update');

Route::delete('/itm-consumable/destroy/{id}','Rawmaterials\RawMaterialsConsumableController@destroy');

Route::get('/itm-consumable/item-m-view/{id}','Rawmaterials\RawMaterialsConsumableController@raw_modal_view');

Route::get('/itm-consumable-invoice/acc-m-view/{id}/{fin_year_id}','Rawmaterials\RawMaterialsConsumableController@acc_modal_view')->name('invoice.jv.mview');

Route::get('/itm-consumable-confirm/{id}','Rawmaterials\RawMaterialsConsumableController@issue_confirmed')->name('itm.consumable.confirm');

//-------------End RawMaterialsConsumableController-----------------------------------


Route::any('/fin-goods-rec-index', 'FinishGoods\FinishGoodsReceivesController@index')->name('fin.goods.rec.index');
Route::get('/fin-goods-rec-create', 'FinishGoods\FinishGoodsReceivesController@create')->name('fin.goods.rec.create');
Route::any('/fin/get-issue_prod_list','FinishGoods\FinishGoodsReceivesController@getIssueProdData')->name('get.issue_prod_list');
Route::post('/fin-goods-rec-store','FinishGoods\FinishGoodsReceivesController@store')->name('fin.goods.rec.store');
Route::get('/fin-goods-rec-edit/{id}','FinishGoods\FinishGoodsReceivesController@edit')->name('fin.goods.rec.edit');
Route::post('/fin-goods-rec-update','FinishGoods\FinishGoodsReceivesController@update')->name('fin.goods.rec.update');

Route::get('/get-fin-goods-rec-code/getdetails/{id}/getfirst','FinishGoods\FinishGoodsReceivesController@get_pur_item');
Route::delete('/fin-goods-rec/destroy/{id}','FinishGoods\FinishGoodsReceivesController@destroy');

Route::get('/fin-goods-rec/item-m-view/{id}','FinishGoods\FinishGoodsReceivesController@raw_modal_view')->name('purchase.item.mview');

Route::get('/fin-goods-rec-invoice/acc-m-view/{id}/{fin_year_id}','FinishGoods\FinishGoodsReceivesController@acc_modal_view')->name('invoice.jv.mview');

Route::get('/fin-goods-rec-confirm/{id}','FinishGoods\FinishGoodsReceivesController@issue_confirmed')->name('fin.goods.rec.confirm');
 
//-------------End FinishGoodsReceivesController-----------------------------------





//Route::get('get-item-code/getdetails/{id}/getfirst', 'Inventory\InventoryController@get_pur_item');
Route::get('get-po-item-code/getdetails/{id}/{poid}/getfirst', 'Inventory\InventoryController@get_pur_item');
Route::any('/itm-inventory-index', 'Inventory\InventoryController@index')->name('itm.inventory.index');
Route::get('/itm-inventory-create', 'Inventory\InventoryController@create')->name('itm.inventory.create');
Route::post('/itm-inventory-store','Inventory\InventoryController@store')->name('itm.inventory.store'); 
Route::get('/itm-inventory-edit/{id}/{poid}','Inventory\InventoryController@edit')->name('itm.inventory.edit'); 
Route::post('/itm-inventory-update','Inventory\InventoryController@update')->name('itm.inventory.update');
Route::delete('/itm-inventory/destroy/{id}/{po}','Inventory\InventoryController@destroy');

Route::get('/itm-inventory/item-m-view/{id}','Inventory\InventoryController@inventory_modal_view')->name('inventory.item.mview');
Route::get('/purchase-voucher-pdf/{id}', 'Inventory\InventoryController@purchase_voucher')->name('purchase.voucher.pdf');
Route::get('get-storage-inf/getdetails/{compid}/{wid}/getfirst', 'Inventory\InventoryController@get_storageId');
Route::get('/pur-invoice/acc-m-view/{id}','Inventory\InventoryController@acc_modal_view')->name('invoice.jv.mview');
Route::any('/pur/get-po','Inventory\InventoryController@getPOData')->name('get.po');
//-------------End InventoryController-----------------------------------

Route::any('/itm-inv-dam-index', 'Inventory\InvDamagesController@index')->name('itm.inv.dam.index');
Route::get('/itm-inv-dam-create', 'Inventory\InvDamagesController@create')->name('itm.inv.dam.create');
Route::post('/itm-inv-dam-store','Inventory\InvDamagesController@store')->name('itm.inv.dam.store');
Route::get('/itm-inv-dam-edit/{id}','Inventory\InvDamagesController@edit')->name('itm.inv.dam.edit');
Route::post('/itm-inv-dam-update','Inventory\InvDamagesController@update')->name('itm.inv.dam.update');
Route::delete('/itm-inv-dam/destroy/{id}','Inventory\InvDamagesController@destroy');
Route::get('/itm-inv-dam/item-m-view/{id}','Inventory\InvDamagesController@damage_modal_view')->name('inv.dam.item.mview');
//-------------End InvDamagesController-----------------------------------

Route::get('/get-item-code/getdetails/{id}/getfirst', 'Inventory\InvShortagesController@get_sel_item');
Route::any('/itm-inv-short-index', 'Inventory\InvShortagesController@index')->name('itm.inv.short.index');
Route::get('/itm-inv-short-create', 'Inventory\InvShortagesController@create')->name('itm.inv.short.create');
Route::post('/itm-inv-short-store','Inventory\InvShortagesController@store')->name('itm.inv.short.store');
Route::get('/itm-inv-short-edit/{id}','Inventory\InvShortagesController@edit')->name('itm.inv.short.edit');
Route::post('/itm-inv-short-update','Inventory\InvShortagesController@update')->name('itm.inv.short.update');
Route::delete('/itm-inv-short/destroy/{id}','Inventory\InvShortagesController@destroy');
Route::get('/itm-inv-short/item-m-view/{id}','Inventory\InvShortagesController@shortage_modal_view')->name('inv.short.item.mview');
//-------------End InvShortagesController-----------------------------------

Route::any('/itm-inv-exp-index', 'Inventory\InvExpiredController@index')->name('itm.inv.exp.index');
Route::get('/itm-inv-exp-create', 'Inventory\InvExpiredController@create')->name('itm.inv.exp.create');
Route::post('/itm-inv-exp-store','Inventory\InvExpiredController@store')->name('itm.inv.exp.store');
Route::get('/itm-inv-exp-edit/{id}','Inventory\InvExpiredController@edit')->name('itm.inv.exp.edit');
Route::post('/itm-inv-exp-update','Inventory\InvExpiredController@update')->name('itm.inv.exp.update');
Route::delete('/itm-inv-exp/destroy/{id}','Inventory\InvExpiredController@destroy');
Route::get('/itm-inv-exp/item-m-view/{id}','Inventory\InvExpiredController@expired_modal_view')->name('inv.exp.item.mview');
//-------------End InvExpiredController-----------------------------------

Route::any('/itm-inv-transfer-index', 'Inventory\InvTransferredController@index')->name('itm.inv.transfer.index');
Route::get('/itm-inv-transfer-create', 'Inventory\InvTransferredController@create')->name('itm.inv.transfer.create');
Route::post('/itm-inv-transfer-store','Inventory\InvTransferredController@store')->name('itm.inv.transfer.store');
Route::get('/itm-inv-transfer-edit/{id}','Inventory\InvTransferredController@edit')->name('itm.inv.transfer.edit');
Route::post('/itm-inv-transfer-update','Inventory\InvTransferredController@update')->name('itm.inv.transfer.update');
Route::delete('/itm-inv-transfer/destroy/{id}','Inventory\InvTransferredController@destroy');
Route::get('/itm-inv-transfer/item-m-view/{id}','Inventory\InvTransferredController@transfer_modal_view')->name('inv.transfer.item.mview');
//-------------End InvTransferredController-----------------------------------

Route::any('/itm-inv-received-pending', 'Inventory\InvReceivedController@pending')->name('itm.inv.received.pending');
Route::any('/itm-inv-received-index', 'Inventory\InvReceivedController@index')->name('itm.inv.received.index');
Route::get('/itm-inv-received-create/{id}', 'Inventory\InvReceivedController@create')->name('itm.inv.received.create');
Route::post('/itm-inv-received-store','Inventory\InvReceivedController@store')->name('itm.inv.received.store');
Route::get('/itm-inv-received-edit/{id}','Inventory\InvReceivedController@edit')->name('itm.inv.received.edit');
Route::post('/itm-inv-received-update','Inventory\InvReceivedController@update')->name('itm.inv.received.update');
Route::delete('/itm-inv-received/destroy/{id}/{transferid}','Inventory\InvReceivedController@destroy');
Route::get('/itm-inv-received/item-m-view/{id}','Inventory\InvReceivedController@received_modal_view')->name('inv.received.item.mview');
//-------------End InvReceivedController-----------------------------------


Route::any('/rpt/so.list', 'Sales\ReportController@getSOReport')->name('rpt.so.list');
Route::any('/rpt/del.list', 'Sales\ReportController@getDelReport')->name('rpt.del.list'); 
Route::any('/rpt/rpt-inv-date-list', 'Sales\ReportController@getInvDateWiseReport')->name('rpt.inv.date.list');
Route::any('/rpt/rpt-inv-date-summ-list', 'Sales\ReportController@getInvDateWiseSummaryReport')->name('rpt.inv.date.summ.list');
Route::any('/rpt/inv-list', 'Sales\ReportController@getInvReport')->name('rpt.inv.list');
Route::any('/rpt/rpt-inv-itm-list', 'Sales\ReportController@getInvItemWiseReport')->name('rpt.inv.itm.list');
Route::any('/rpt/rpt-inv-cust-list', 'Sales\ReportController@getInvCustWiseReport')->name('rpt.inv.cust.list');
Route::any('/rpt/cond-inv-list', 'Sales\ReportController@getCondInvReport')->name('rpt.cond.inv.list'); 
Route::any('/rpt/rpt-inv-comm-list', 'Sales\ReportController@getInvCommissionReport')->name('rpt.inv.comm.list'); 
Route::any('/rpt/rpt-cust-wise-sales-stm','Sales\ReportController@getCustWiseSalesReport')->name('rpt.cust.wise.sales.stm');
Route::any('/rpt/rpt-salesperson-wise-sales-stm','Sales\ReportController@getSPWiseSalesReport')->name('rpt.salesperson.wise.sales.stm');
Route::any('/rpt/rpt-top-item-sales-qty', 'Sales\ReportController@getTopItemSalesQty')->name('rpt.top.item.sales.qty');
Route::any('/rpt/rpt-top-item-sales-volume', 'Sales\ReportController@getTopItemSalesVolume')->name('rpt.top.item.sales.volume');
Route::any('/rpt/rpt-inv-wise-profit_loss', 'Sales\ReportController@getInvWiseProfitLossReport')->name('rpt.inv.wise.profit_loss');
Route::any('/rpt/rpt-item-wise-profit_loss', 'Sales\ReportController@getItemWiseProfitLossReport')->name('rpt.item.wise.profit_loss');
Route::any('/rpt/sales-order-list-pending', 'Sales\ReportController@salesOrderListPending')->name('rpt.sales.order.list.pending');
Route::any('/rpt/rpt-cust-order-statement','Sales\ReportController@getCustomerStatementReport')->name('rpt.cust.order.statement');

//-------------End Sales\ReportController-----------------------------------

Route::delete('/return-order/destroy/{id}/{retno}','Sales\SalesReturnController@destroy');
Route::get('/return/acc-m-view/{id}','Sales\SalesReturnController@acc_modal_view')->name('return.jv.mview');
Route::post('/sales-return-update','Sales\SalesReturnController@update')->name('sales.return.update');
Route::get('/sales-return-edit/{id}','Sales\SalesReturnController@edit')->name('sales.return.edit');
Route::post('/sales-return-store','Sales\SalesReturnController@store')->name('sales.return.store');
Route::get('/sales-return-create/{id}', 'Sales\SalesReturnController@create')->name('sales.return.create');
Route::any('/sales-return-index', 'Sales\SalesReturnController@index')->name('sales.return.index');
Route::get('/sales/return-m-view/{id}','Sales\SalesReturnController@return_modal_view')->name('return.item.mview');
//-------------End SalesReturnController-----------------------------------


Route::get('/sales-invoice-create', 'Sales\SalesInvoiceController@create')->name('sales.invoice.create');
Route::any('/sales.invoice.index', 'Sales\SalesInvoiceController@index')->name('sales.invoice.index');
Route::get('/sales-invoice-create', 'Sales\SalesInvoiceController@create')->name('sales.invoice.create');
Route::get('/invoice/acc-m-view/{id}/{fin_year_id}','Sales\SalesInvoiceController@acc_modal_view')->name('invoice.jv.mview');
Route::get('/sales.challan.pdf/{id}', 'Sales\SalesInvoiceController@sal_challan')->name('sales.challan.pdf');
Route::get('/sales-invoice-pdf/{id}', 'Sales\SalesInvoiceController@sal_invoice')->name('sales.invoice.pdf');
Route::get('/invoice/sales-m-view/{id}','Sales\SalesInvoiceController@sales_modal_view')->name('invoice.item.mview');
Route::get('/invoice/sales-inv-view/{id}/{fin_year_id}','Sales\SalesInvoiceController@sales_invoice_view')->name('sales.invoice.view');
Route::any('/sales.invoice.locked', 'Sales\SalesInvoiceController@locked')->name('sales.invoice.locked');
Route::post('/sales.invoice.locking', 'Sales\SalesInvoiceController@locking')->name('sales.invoice.locking');
//-------------End SalesInvoiceController-----------------------------------

Route::any('/sales-delivery-index', 'Sales\SalesDeliveryController@index')->name('sales.delivery.index');
Route::any('/sales-order-pending', 'Sales\SalesDeliveryController@so_pending')->name('sales.order.pending');
Route::any('/sales-order-pending-item', 'Sales\SalesDeliveryController@so_pending_item')->name('sales.order.pending_item');
Route::any('/sales-delivery-create/{id}', 'Sales\SalesDeliveryController@create')->name('sales.delivery.create');
Route::any('/sales-delivery-select', 'Sales\SalesDeliveryController@sales_select')->name('sales.delivery.select');

Route::post('/sales-delivery-store','Sales\SalesDeliveryController@store')->name('sales.delivery.store');
Route::get('/sales-delivery-edit/{id}','Sales\SalesDeliveryController@edit')->name('sales.delivery.edit');
Route::post('/sales-delivery-update','Sales\SalesDeliveryController@update')->name('sales.delivery.update');
Route::get('/sales-delivery-invoice/{delid}/{finyearid}','Sales\SalesDeliveryController@generateInvoice')->name('sales.delivery.invoice');

Route::get('get-item-del-code/all', 'Sales\SalesDeliveryController@getDelItemCode')->name('del.get.item.code');
Route::get('get-item-del-code/getdetails/{id}/{soid}/getfirst', 'Sales\SalesDeliveryController@get_so_del_item');
//Route::get('get-item-barcode/all', 'Sales\SalesOrdersController@getItemBarCode')->name('sales.get.item.bar.code');
Route::get('get-delivered-inf/getdetails/{id}/getfirst', 'Sales\SalesDeliveryController@get_delivered_inf');
Route::get('get-delivered-inf_d/getdetails/{id}/getfirst', 'Sales\SalesDeliveryController@get_delivered_inf_default');
Route::get('get-cust-oustanding-inf/getdetails/{compid}/{custid}/getfirst', 'Sales\SalesDeliveryController@get_outstanding_inf');
Route::get('get-cust-courrier-inf/getdetails/{compid}/{custid}/getfirst', 'Sales\SalesDeliveryController@get_courrier_inf');
Route::get('acctrans/get-cust-invoice-inf/getdetails/{custid}/getfirst', 'Sales\SalesDeliveryController@get_invoice_inf'); 
Route::get('get-cust-vat-inf/getdetails/{compid}/{custid}/getfirst', 'Sales\SalesDeliveryController@get_vat_inf');
Route::get('/itemOrderLookup/{so}', 'Sales\SalesDeliveryController@itemOrderLookup');

Route::any('/delivery-invoice-pdf/{id}', 'Sales\SalesDeliveryController@sal_invoice')->name('delivery.invoice.pdf');
Route::any('/delivery-challan-pdf/{id}', 'Sales\SalesDeliveryController@del_challan')->name('delivery.challan.pdf');
Route::any('/delivery-gatepass-pdf/{id}', 'Sales\SalesDeliveryController@gate_pass')->name('delivery.gatepass.pdf');
Route::get('/delivery/item-m-view/{id}','Sales\SalesDeliveryController@delivery_modal_view')->name('delivery.item.mview');
//-------------End SalesDeliveryController-----------------------------------

Route::any('/sales-prod-index', 'Sales\SalesProductionController@so_prod_index')->name('sales.prod.index');
Route::get('/sales-prod-confirmed/{id}','Sales\SalesProductionController@so_prod_confirmed')->name('sales.prod.confirmed');
 
Route::any('/sales-pp-prod-index', 'Sales\SalesProductionController@so_pp_prod_index')->name('sales.pp.prod.index');
Route::post('/sales-pp-prod-confirmed','Sales\SalesProductionController@so_confirmed')->name('sales.pp.prod.confirmed');

Route::any('/sales-jute-prod-index', 'Sales\SalesProductionController@so_jute_prod_index')->name('sales.jute.prod.index');
Route::post('/sales-jute-prod-confirmed','Sales\SalesProductionController@so_confirmed')->name('sales.jute.prod.confirmed');
Route::any('/sales-jute-prod-agree/{id}', 'Sales\SalesProductionController@so_jute_prod_agree')->name('sales.jute.prod.agree');
Route::any('/sales-prod-report-daily','Sales\SalesProductionController@so_prod_report_daily')->name('sales.prod.report.daily');
Route::any('/sales-delivery-prod-report-daily','Sales\SalesProductionController@sd_prod_report_daily')->name('sales.delivery.prod.report.daily');

//-------------End SalesProductionController-----------------------------------

Route::get('/direct-delivery-create', 'Sales\SalesOrdersController@direct_order_delivery')->name('direct.delivery.create');
Route::get('/direct-delivery-edit/{id}', 'Sales\SalesOrdersController@direct_order_delivery_edit')->name('direct.delivery.edit');
Route::post('/sales-order-delivery-store','Sales\SalesOrdersController@store_to_delivery')->name('sales.order.delivery.store');
Route::post('/sales-order-delivery-update','Sales\SalesOrdersController@so_update_to_delivery')->name('sales.order.delivery.update');

Route::get('/direct-order-create', 'Sales\SalesOrdersController@direct_order_invoice')->name('direct.order.create');
Route::get('/direct-order-edit/{id}', 'Sales\SalesOrdersController@direct_order_invoice_edit')->name('direct.order.edit');
Route::any('/sales-order-index', 'Sales\SalesOrdersController@so_index')->name('sales.order.index'); 
Route::get('/sales-order-create', 'Sales\SalesOrdersController@so_create')->name('sales.order.create');

Route::any('/sales-order-direct', 'Sales\SalesOrdersController@sales_order_direct')->name('sales.order.direct'); 
Route::Get('/sales-order-direct-create', 'Sales\SalesOrdersController@direct_sale')->name('sales.order.direct.create'); 
Route::Post('/sales-order-direct-create', 'Sales\SalesOrdersController@direct_sale_stora')->name('sales.order.direct.store'); 
Route::Get('/sales-order-direct-edit/{id}', 'Sales\SalesOrdersController@direct_sale_edit')->name('sales.order.direct.edit'); 
Route::Post('/sales-order-direct-update/{id}', 'Sales\SalesOrdersController@direct_sale_update')->name('sales.order.direct.update'); 
Route::Get('/sales-order-direct-confirm/{id}', 'Sales\SalesOrdersController@so_direct_confirmed')->name('sales.order.direct.confirm'); 

Route::get('/sales-direct-order-pdf/{id}', 'Sales\SalesOrdersController@sal_direct_order_pdf')->name('sales.direct.order.pdf');


Route::post('/sales-order-store','Sales\SalesOrdersController@store')->name('sales.order.store');
Route::post('/sales-order-store1','Sales\SalesOrdersController@store_to_invoiced')->name('sales.order.store1');
Route::get('/sales-order-edit/{id}','Sales\SalesOrdersController@so_edit')->name('sales.order.edit');
Route::post('/sales-order-update','Sales\SalesOrdersController@so_update')->name('sales.order.update');
Route::post('/sales-order-update1','Sales\SalesOrdersController@so_update_to_invoiced')->name('sales.order.update1');
Route::get('/sales-conf-confirmed1/{id}','Sales\SalesOrdersController@so_confirmed1')->name('sales.conf.confirmed1');
Route::get('/sales-conf-creation/{id}','Sales\SalesOrdersController@conf_creation')->name('sales.conf.creation');
Route::post('/sales-conf-confirmed','Sales\SalesOrdersController@so_confirmed')->name('sales.conf.confirmed');
Route::delete('/sales-order/destroy/{id}/{isconfirmed}','Sales\SalesOrdersController@destroy');

Route::get('get-item-code/all', 'Sales\SalesOrdersController@getItemCode')->name('sales.get.item.code');
Route::get('get-item-code/getdetails/{id}/{custid}/getfirst', 'Sales\SalesOrdersController@get_sel_item');
//Route::get('get-item-barcode/all', 'Sales\SalesOrdersController@getItemBarCode')->name('sales.get.item.bar.code');
Route::get('get-delivered-inf/getdetails/{id}/getfirst', 'Sales\SalesOrdersController@get_delivered_inf');
Route::get('/sales-order-pdf/{id}', 'Sales\SalesOrdersController@sal_order_pdf')->name('sales.order.pdf');
Route::get('/sales-order-prod-pdf/{id}/{tag}', 'Sales\SalesOrdersController@sal_order_prod_pdf')->name('sales.order.prod.pdf');

Route::get('/itemLookup/{compid}/{custid}', 'General\DropdownsController@itemLookup');
Route::get('/itemLookup1/{compid}', 'General\DropdownsController@itemLookup1');

Route::get('/catitemLookup/{compid}/{catid}', 'General\DropdownsController@catitemLookup');
Route::get('/get-child-cat/{catid}', 'General\DropdownsController@getChildCat');
Route::get('/categoryLookup/{compid}', 'General\DropdownsController@categoryLookup');
Route::get('/rawMetarilasCategory/{compid}', 'General\DropdownsController@rawMetarilasCategory');

Route::get('/rawitemLookup/{compid}', 'General\DropdownsController@rawitemLookup');
Route::get('/rawitemissueLookup/{compid}', 'General\DropdownsController@rawitemissueLookup');
Route::get('/consitemLookup/{compid}', 'General\DropdownsController@consitemLookup');
Route::get('/rawUnitLookup/{compid}', 'General\DropdownsController@rawUnitLookup');
Route::get('/finishgoodsLookup/{compid}', 'General\DropdownsController@finishgoodsLookup'); 

Route::get('/deliveredToLookup/{custid}', 'General\DropdownsController@deliveredToLookup');
Route::get('/courrierToLookup/{compid}', 'General\DropdownsController@courrierToLookup');
Route::get('/storageLookup/{compid}/{wid}', 'General\DropdownsController@storageLookup');
Route::get('/LotLookup/{itemid}/{storageid}', 'General\DropdownsController@LotLookup');
Route::get('get-stock-inf/{warehouseid}/{itemid}/getfirst', 'General\GeneralsController@get_stock_wh_Lookup');
Route::get('get-stock-inf/{storgaeid}/{itemid}/{lotno}/getfirst', 'General\GeneralsController@get_stockLookup');
Route::get('/warehouseLookup/{compid}', 'General\DropdownsController@warehouseLookup'); 
Route::get('/warehouseLookup1/{compid}', 'General\DropdownsController@warehouseLookup1');
//-------------End SalesOrdersController-----------------------------------

Route::any('/sales-loan-issue', 'Sales\SalesLoanController@issue_index')->name('sales.loan.issue'); 
Route::get('/sales-loan-issue-create', 'Sales\SalesLoanController@issue_create')->name('sales.loan.issue.create');
Route::post('/sales-loan-issue-store','Sales\SalesLoanController@issue_store')->name('sales.loan.issue.store');
Route::get('/sales-loan-issue-edit/{id}','Sales\SalesLoanController@issue_edit')->name('sales.loan.issue.edit');
Route::post('/sales-loan-issue-update','Sales\SalesLoanController@issue_update')->name('sales.loan.issue.update');
Route::delete('/sales-loan-issue/destroy/{id}/{isconfirmed}','Sales\SalesLoanController@issue_destroy');


Route::any('/sales-loan-received', 'Sales\SalesLoanController@received_index')->name('sales.loan.received');

//-------------End SalesLoanController-----------------------------------

Route::any('/sales-loan-return', 'Sales\SalesLoanReturnController@index')->name('sales.loan.return'); 
Route::get('/sales-loan-return-create/{id}','Sales\SalesLoanReturnController@create')->name('sales.loan.return.create');
Route::post('/sales-loan-return-store','Sales\SalesLoanReturnController@store')->name('sales.loan.return.store');
Route::get('/sales-loan-return-edit/{id}','Sales\SalesLoanReturnController@edit')->name('sales.loan.return.edit');
Route::delete('/sales-loan-return/destroy/{id}','Sales\SalesLoanReturnController@destroy');


//-------------End SalesLoanReturnController-----------------------------------

Route::get('/sales-quot-index', 'Sales\SalesQuotationController@so_quot_index')->name('sales.quot.index');
Route::get('/sales-quot-create', 'Sales\SalesQuotationController@so_quot_create')->name('sales.quot.create');
Route::post('/sales-quot-save', 'Sales\SalesQuotationController@so_quot_save')->name('sales.quot.save');
Route::post('/sales-quot-update', 'Sales\SalesQuotationController@so_quot_update')->name('sales.quot.update');

Route::get('/sales-quot-edit/{e}/{id}', 'Sales\SalesQuotationController@so_quot_edit')->name('sales.quot.edit');
Route::get('/sales-quot-copy/{c}/{id}', 'Sales\SalesQuotationController@so_quot_edit')->name('sales.quot.copy');
Route::get('/sales-quot-print/{id}', 'Sales\SalesQuotationController@so_quot_print')->name('sales.quot.print');
Route::get('get-quot-item-code/getdetails/{id}/getfirst', 'Sales\SalesQuotationController@get_quot_item');
Route::get('get-quot-cust-add/getcustAddress/{id}', 'Sales\SalesQuotationController@get_quot_cust_add');
Route::any('/sales-quot-search','Sales\SalesQuotationController@sales_quot_search')->name('sales.quot.search');
Route::delete('/sales-quot-del/destroy/{id}','Sales\SalesQuotationController@destroy');
//-------------End SalesQuotationController-----------------------------------

// -------------Start ChartOfAccountController-----------------------------------
Route::get('/chartofacc/chartofacc/index', 'Accounts\ChartOfAccountController@index')->name('chartofacc.index');
Route::post('/chartofacc/chartofacc/index', 'Accounts\ChartOfAccountController@index')->name('chartofacc.index');
Route::get('/chartofacc/chartofacc/getchart', 'Accounts\ChartOfAccountController@get_chart_data')->name('chartofacc.getchart');
Route::post('/chartofacc/store','Accounts\ChartOfAccountController@store')->name('chartofacc.acchead.store');
Route::post('/chartofacc/update','Accounts\ChartOfAccountController@update')->name('chartofacc.acchead.update');
Route::delete('/chartofacc/destroy/{id}','Accounts\ChartOfAccountController@destroy');
Route::get('/chartofacc/after_save/{comid}','Accounts\ChartOfAccountController@after_save')->name('chartofacc.after_save');
//Child
Route::get('/chartofacc-child-head/{id}','Accounts\ChartOfAccountController@makeacc_childhead')->name('chartofacc.makechildhead');
Route::post('/chartofacc/child/store','Accounts\ChartOfAccountController@childstore')->name('chartofacc.acchead.child.store');
Route::post('/chartofacc/child/store2','Accounts\ChartOfAccountController@childstore2')->name('chartofacc.acchead.child.store2');
Route::post('/chartofacc/child/update','Accounts\ChartOfAccountController@updateChild')->name('chartofacc.acchead.child.update');
Route::post('/chartofacc/child/update2','Accounts\ChartOfAccountController@updateChild2')->name('chartofacc.acchead.child.update2');
Route::any('/chartofacc/child/search','Accounts\ChartOfAccountController@childsearch')->name('chartofacc.acchead.child.search');
Route::post('/chartofacc/child/destroy','Accounts\ChartOfAccountController@child_destroy');
Route::post('/chartofacc/child/destroy2','Accounts\ChartOfAccountController@child_destroy2');
Route::get('acchead-tree-view-list',['uses'=>'Accounts\ChartOfAccountController@manageAccountHeadTreeList'])->name('acchead.tree.view.list');
Route::get('/chartofacc-child-head2/{id}','Accounts\ChartOfAccountController@makeacc_childhead2')->name('chartofacc.makechildhead2');

Route::get('acchead-tree-view/{code}',['uses'=>'Accounts\ChartOfAccountController@manageAccountHeadTree'])->name('acchead.tree.view');

Route::get("addProduct","Accounts\ChartOfAccountController@addChildhead"); // Not included in Project
Route::post("addProduct","Accounts\ChartOfAccountController@addChildheadMore"); // Not included in Project
Route::get('category-tree-view',['uses'=>'CategoryController@manageCategory']); // Not included in Project
Route::post('add-category',['as'=>'add.category','uses'=>'CategoryController@addCategory']); // Not included in Project
//--------------End ChartOfAccountController-------------------------------------


Route::post('/acctrans/acctrans-store','Accounts\AccountTransController@acctrans_store')->name('acctrans.store');
//Route::get('/acctrans/jv-index','Accounts\AccountTransController@jv_index')->name('acctrans.jv.index');
//Route::get('/acctrans/jv-create', 'Accounts\AccountTransController@jv_create')->name('acctrans.jv.create');
//--------------End AccountTransController-------------------------------------

Route::get('/billtobill-cr-create','Accounts\BilltobillTransController@billtobill_cash_create')->name('billtobill.cr.create');
Route::post('/billtobill-store','Accounts\BilltobillTransController@billtobill_store')->name('billtobill.store');
Route::get('/billtobill-br-create','Accounts\BilltobillTransController@billtobill_bank_create')->name('billtobill.cr.create');

Route::get('/billtobill-jv-create', 'Accounts\BilltobillTransController@billtobill_jv_create')->name('billtobill.jv.create');
Route::post('/billtobill-jv-store','Accounts\BilltobillTransController@billtobill_jv_store')->name('billtobill.jv.store');
Route::get('/billtobill-jv-edit/{type}/{id}', 'Accounts\BilltobillTransController@billtobill_jv_edit')->name('billtobill.jv.edit');
Route::post('/billtobill-jv-update', 'Accounts\BilltobillTransController@billtobill_jv_update')->name('billtobill.jv.update');
Route::get('/accInvoiceJVLookup/{compcode}/{accid}/{transtype}', 'Accounts\BilltobillTransController@accInvoiceJVLookup');
//--------------End BilltobillTransController-------------------------------------

Route::get('/acctrans/jv-index','Accounts\JournalTransController@jv_index')->name('acctrans.jv.index');
Route::get('/acctrans/jv-voucher/{id}','Accounts\ReportController@journalVoucherPrint')->name('acctrans.jv.voucherPrint');
Route::get('/acctrans/cr-voucher/{id}','Accounts\ReportController@cashReceiverPrint')->name('acctrans.cr.moneyRecive');
Route::get('/voucher','Accounts\ReportController@voucher')->name('voucher');


Route::any('/acctrans/jv-search','Accounts\JournalTransController@jv_search')->name('acctrans.jv.search');
Route::get('/acctrans/jv-create', 'Accounts\JournalTransController@jv_create')->name('acctrans.jv.create');
Route::get('/acctrans/jv-m-view/{id}','Accounts\JournalTransController@jv_modal_view')->name('acctrans.jv.mview');
// -------------End JournalTransController-----------------------------------

Route::get('/acctrans/con-create', 'Accounts\ContraTransController@con_create')->name('acctrans.con.create');
Route::get('/acctrans/con-index', 'Accounts\ContraTransController@con_index')->name('acctrans.con.index');
Route::any('/acctrans/con-search','Accounts\ContraTransController@con_search')->name('acctrans.con.search');
// -------------End ContraTransController-----------------------------------

Route::get('/acctrans/cr-create', 'Accounts\CashReceivedTransController@cr_create')->name('acctrans.cr.create');
Route::get('/acctrans/cr-index', 'Accounts\CashReceivedTransController@cr_index')->name('acctrans.cr.index');
Route::any('/acctrans/cr-search','Accounts\CashReceivedTransController@cr_search')->name('acctrans.cr.search');
// -------------End CashReceivedTransController-----------------------------------

Route::get('/acctrans/cp-create', 'Accounts\CashPaymentTransController@cp_create')->name('acctrans.cp.create');
Route::get('/acctrans/cp-index', 'Accounts\CashPaymentTransController@cp_index')->name('acctrans.cp.index');
Route::any('/acctrans/cp-search','Accounts\CashPaymentTransController@cp_search')->name('acctrans.cp.search');
// -------------End CashPaymentTransController-----------------------------------

Route::get('/acctrans/br-create', 'Accounts\BankReceivedTransController@br_create')->name('acctrans.br.create');
Route::get('/acctrans/br-index', 'Accounts\BankReceivedTransController@br_index')->name('acctrans.br.index');
Route::any('/acctrans/br-search','Accounts\BankReceivedTransController@br_search')->name('acctrans.br.search');
// -------------End CashReceivedTransController-----------------------------------

Route::get('/acctrans/bp-create', 'Accounts\BankPaymentTransController@bp_create')->name('acctrans.bp.create');
Route::get('/acctrans/bp-index', 'Accounts\BankPaymentTransController@bp_index')->name('acctrans.bp.index');
Route::any('/acctrans/bp-search','Accounts\BankPaymentTransController@bp_search')->name('acctrans.bp.search');
// -------------End BankPaymentTransController-----------------------------------

Route::get('/accPurchaseBalanceLookup/{compcode}/{accid}/{invoiceno}', 'Accounts\AccountTransController@accPurchaseBalanceLookup');
Route::get('/accInvoiceBalanceLookup/{compcode}/{accid}/{invoiceno}', 'Accounts\AccountTransController@accInvoiceBalanceLookup');
Route::get('/accInvoiceLookup/{compcode}/{accid}/{transtype}', 'Accounts\AccountTransController@accInvoiceLookup');
Route::get('/accheadLookup/{compcode}/{transtype}', 'Accounts\AccountTransController@accheadLookup');
Route::get('get-acchead/all', 'Accounts\AccountTransController@getAccHead')->name('acc.get.head');
Route::get('acctrans/get-acchead/getdetails/{id}/getfirst', 'Accounts\AccountTransController@ghani');
Route::post('acctrans/jv-store', 'Accounts\AccountTransController@acctrans_store')->name('acctrans.jv.store');
Route::get('/acctrans/acctrans-edit/{type}/{id}', 'Accounts\AccountTransController@acctrans_edit')->name('acctrans.edit');
Route::post('/acctrans/acctrans-update', 'Accounts\AccountTransController@acctrans_update')->name('acctrans.update');
Route::get('/acctrans/acctrans-checking', 'Accounts\AccountTransController@acctrans_checking')->name('acctrans.check');
Route::delete('/acctrans/destroy/{id}','Accounts\AccountTransController@destroy');

// -------------Start AccountTransController-----------------------------------

Route::get('/finyeardec/index', 'Accounts\FinancialYearDeclarationController@index')->name('finyeardec.index');
Route::post('/finyeardec/store','Accounts\FinancialYearDeclarationController@store')->name('finyeardec.store');
Route::delete('/finyeardec/destroy/{id}','Accounts\FinancialYearDeclarationController@destroy');
Route::get('/finyeardec/finyeardec-status/{action}/{id}/{compid}','Accounts\FinancialYearDeclarationController@getStatusChange')->name('finyeardec.status');
//--------------End FinancialYearDeclarationController-------------------------------------

Route::get('report/get-ledger-head', 'Accounts\ReportController@getLedgerHead')->name('get.ledger.head');
Route::get('report/get-con-ledger-head', 'Accounts\ReportController@getControlLedgerHead')->name('get.con.ledger.head');
Route::get('/rpt/voucher-print/{id}', 'Accounts\ReportController@print')->name('voucher.print');
Route::any('/rpt/vh-list', 'Accounts\ReportController@getVoucherReport')->name('rpt.vh.list');
Route::any('/rpt/sub-ledger','Accounts\ReportController@getSubsidiaryLedger')->name('rpt.sub.ledger');
Route::any('/rpt/sub-ledger1/{sdate}/{compid}/{custid}','Accounts\ReportController@getSubsidiaryLedger1')->name('rpt.sub.ledger1');
Route::any('/rpt/con-sub-ledger','Accounts\ReportController@getConSubsidiaryLedger')->name('rpt.con.sub.ledger');
Route::any('/rpt/rpt-trial-bal1','Accounts\ReportController@getTrialBalance1')->name('rpt.trial.bal1');
Route::any('/rpt/rpt-trial-bal2','Accounts\ReportController@getTrialBalance2')->name('rpt.trial.bal2');
Route::any('/rpt/rpt-trial-bal3','Accounts\ReportController@getTrialBalance3')->name('rpt.trial.bal3');
Route::any('/rpt/rpt-cash-sheet','Accounts\ReportController@getDailyCashSheet')->name('rpt.cash.sheet');
Route::any('/rpt/rpt-cash-sheet-summary','Accounts\ReportController@getDailyCashSheetSummary')->name('rpt.cash.sheet.summary');
Route::any('/rpt/rpt-liquid-cash-sheet','Accounts\ReportController@getLiquidCashReport')->name('rpt.liquid.cash.sheet');
Route::any('/rpt/rpt-cust-statement','Accounts\ReportController@getCustomerStatementReport')->name('rpt.cust.statement');
Route::any('/rpt/rpt-cust-statement-summary','Accounts\ReportController@getCustomerSummaryStatementReport')->name('rpt.cust.statement.summary');
Route::any('/rpt/rpt.cust.dues','Accounts\ReportController@getCustomerDuesReport')->name('rpt.cust.dues');
//--------------End ReportController-------------------------------------


Route::any('report/get-emp-leave-data', 'Leave\ReportController@getEmpLeaveData')->name('get.emp.leave.data'); 
Route::any('report/get-emp-leave-summary-data', 'Leave\ReportController@getEmpLeaveSummaryData')->name('get.emp.leave.summary.data'); 

// -------------Start LeaveController-----------------------------------

Route::get('/leave/index', 'Leave\LeaveController@index')->name('leave.index');
Route::post('/leave/search', 'Leave\LeaveController@search')->name('leave.search');
Route::get('/leave/create', 'Leave\LeaveController@create')->name('leave.create');
Route::post('/leave/store','Leave\LeaveController@store')->name('leave.store'); 
Route::get('/leave-edit/{id}','Leave\LeaveController@edit')->name('leave.edit');
Route::post('/leave/update','Leave\LeaveController@update')->name('leave.update'); 
Route::delete('/leave/destroy/{id}','Leave\LeaveController@destroy');
Route::get('/leave/get-emp-details-inf/getdetails/{compid}/{empid}/getfirst', 'Employees\EmployeesController@get_employee_inf');
Route::get('/leave/get-emp-leave-bal-inf/getdetails/{compid}/{empid}/{leavetypeid}/getfirst', 'Leave\LeaveController@get_emp_leave_bal_inf');
// -------------Start LeaveController-----------------------------------

// -------------Start LeaveTypesController-----------------------------------

Route::get('/leave-types/index', 'Leave\LeaveTypesController@index')->name('leave.types.index');
Route::post('/leave-types/store','Leave\LeaveTypesController@store')->name('leave.types.store'); 
Route::delete('/leave-types/destroy/{id}','Leave\LeaveTypesController@destroy');
// -------------Start LeaveTypesController-----------------------------------


// -------------Start EmpTimesheetController-----------------------------------

Route::get('/timesheet/index', 'Attendance\EmpTimesheetController@index')->name('timesheet.index');
Route::post('/timesheet/search', 'Attendance\EmpTimesheetController@search')->name('timesheet.search');
Route::get('/timesheet/create', 'Attendance\EmpTimesheetController@create')->name('timesheet.create');
Route::post('/timesheet/store','Attendance\EmpTimesheetController@store')->name('timesheet.store'); 
Route::get('/timesheet-edit/{id}','Attendance\EmpTimesheetController@edit')->name('timesheet.edit');
Route::post('/timesheet/update','Attendance\EmpTimesheetController@update')->name('timesheet.update'); 
Route::delete('/timesheet/destroy/{id}','Attendance\EmpTimesheetController@destroy');
// -------------Start EmpTimesheetController-----------------------------------

// -------------Start HolidayController-----------------------------------

Route::get('/holiday/index', 'Attendance\HolidayController@index')->name('holiday.index');
Route::post('/holiday/search', 'Attendance\HolidayController@search')->name('holiday.search');
Route::get('/holiday/create', 'Attendance\HolidayController@create')->name('holiday.create');
Route::post('/holiday/store','Attendance\HolidayController@store')->name('holiday.store'); 
Route::get('/holiday-edit/{id}','Attendance\HolidayController@edit')->name('holiday.edit');
Route::post('/holiday/update','Attendance\HolidayController@update')->name('holiday.update'); 
Route::delete('/holiday/destroy/{id}','Attendance\HolidayController@destroy');
// -------------Start HolidayController-----------------------------------


// -------------Start AttendanceController-----------------------------------


Route::get('/attendance-process', 'Attendance\AttendanceController@process')->name('attendance.process');
Route::post('/attendance/process1', 'Attendance\AttendanceController@process1')->name('attendance.process1');
// -------------Start AttendanceController-----------------------------------


// -------------Start ManualAttendanceController-----------------------------------

Route::get('/attendance/index', 'Attendance\ManualAttendanceController@index')->name('attendance.index');
Route::post('/attendance/search', 'Attendance\ManualAttendanceController@search')->name('attendance.search');
Route::get('/attendance/create', 'Attendance\ManualAttendanceController@create')->name('attendance.create');
Route::post('/attendance/store','Attendance\ManualAttendanceController@store')->name('attendance.store'); 
Route::get('/attendance-edit/{id}','Attendance\ManualAttendanceController@edit')->name('attendance.edit');
Route::post('/attendance/update','Attendance\ManualAttendanceController@update')->name('attendance.update'); 
Route::delete('/attendance/destroy/{id}','Attendance\ManualAttendanceController@destroy');
Route::get('/attendance/list', 'Attendance\ManualAttendanceController@list')->name('attendance.list');
Route::get('/attendance/wages-sheet/{id}', 'Attendance\ManualAttendanceController@wages_sheet')->name('attendance.wages-sheet');
Route::get('/attendance/details/{id}/{sectionId}', 'Attendance\ManualAttendanceController@details')->name('attendance.details');
Route::get('/attendance/edit/{id}', 'Attendance\ManualAttendanceController@details')->name('attendance.edit');
Route::Post('/attendance/index', 'Attendance\ManualAttendanceController@index')->name('attendance.index.post');
Route::Post('/attendance/weekendentry', 'Attendance\ManualAttendanceController@WeekEndEntry')->name('attendance.WeekEndEntry');



Route::get('/attendance/get-emp-details-inf/getdetails/{compid}/{empid}/getfirst', 'Employees\EmployeesController@get_employee_inf'); 
//--------------End ManualAttendanceController-------------------------------------


Route::any('report/get-raw-attendance-data', 'Attendance\ReportController@getRawAttendanceData')->name('get.raw.attendance.data');
Route::any('report/get-attend-present-data', 'Attendance\ReportController@getAttendPresentData')->name('get.attend.present.data');
Route::any('report/get-attend-absent-data', 'Attendance\ReportController@getAttendAbsentData')->name('get.attend.absent.data');
Route::any('report/get-attend-error-data', 'Attendance\ReportController@getAttendErrorData')->name('get.attend.error.data');
Route::any('report/get-job-card-report', 'Attendance\ReportController@getJobCardReport')->name('get.job.card.report');

// -------------Start EmployeesController-----------------------------------

Route::get('/employees/index', 'Employees\EmployeesController@index')->name('employees.index');
Route::post('/employees/search', 'Employees\EmployeesController@search')->name('employees.search');
Route::get('/employees/create', 'Employees\EmployeesController@create')->name('employees.create');
Route::post('/employees/store','Employees\EmployeesController@store')->name('employees.store'); 
Route::get('/employees-edit/{id}','Employees\EmployeesController@edit')->name('employees.edit');
Route::post('/employees/update','Employees\EmployeesController@update')->name('employees.update'); 
Route::get('/employees-m-view/{id}','Employees\EmployeesController@employee_m_view')->name('employees.mview');
Route::get('/comboToLookup/{compid}/{combo_type}', 'General\DropdownsController@comboToLookup');
Route::delete('/employees/destroy/{id}','Employees\EmployeesController@destroy');

Route::get('/employeeToLookup/{compid}', 'General\DropdownsController@employeeToLookup');  
Route::get('/leaveTypeToLookup/{compid}', 'General\DropdownsController@leaveTypeToLookup'); 
//--------------End EmployeesController-------------------------------------

Route::any('report/get-dept-sec-list', 'Employees\ReportController@getDeptSectWiseList')->name('get.dept.sec.list');
Route::any('report/get-pend-machine-list', 'Employees\ReportController@getPendMachineList')->name('get.pend.machine.list');
//--------------End ReportController-------------------------------------

// -------------Start SalaryController-----------------------------------

Route::get('/salary-process', 'Salary\SalaryController@process')->name('salary.process');
Route::get('/edit-salary-process/{id}', 'Salary\SalaryController@processedit')->name('salary.process.edit');
Route::Post('/edit-salary-process/{id}', 'Salary\SalaryController@processUpdate')->name('salary.processupdate');
Route::Post('/salary-process', 'Salary\SalaryController@process_add')->name('salary.process.post');
Route::Get('/salary-process-update/{id}', 'Salary\SalaryController@process_update')->name('salary.process.update');
Route::post('/salary/process1', 'Salary\SalaryController@process1')->name('salary.process1');
// -------------Start SalaryController-----------------------------------
// -------------Start ReportController-----------------------------------

Route::any('report/get-monthly-sal-report', 'Salary\ReportController@getMonthlySalaryData')->name('get.monthly.sal.report');

// -------------End ReportController-----------------------------------


Route::get('/companyassign/index', 'Company\CompanyAssignController@index')->name('companyassign.index');
Route::post('/companyassign/store','Company\CompanyAssignController@store')->name('companyassign.store');
Route::post('/companyassign/update','Company\CompanyAssignController@update')->name('companyassign.update');
Route::delete('/companyassign/destroy/{id}','Company\CompanyAssignController@destroy');
//--------------End CompanyAssignController-------------------------------------

Route::delete('/currency/destroy/{id}','Settings\CurrencyController@destroy');
Route::post('/currency-store','Settings\CurrencyController@store')->name('currency.store');
Route::get('/acctrans-currency', 'Settings\CurrencyController@index')->name('acctrans.currency');
//--------------End CompanyAssignController-------------------------------------


Route::get('/role/index', 'RoleController@index')->name('role.index');
Route::get('/role/create','RoleController@create')->name('role.create');
Route::post('/role/store','RoleController@store')->name('role.store');
Route::put('/role/update','RoleController@update')->name('role.update');
Route::delete('/role/destroy/{id}','RoleController@destroy');
Route::get('/role-access/{id}', 'RoleController@access_index')->name('role.access');
Route::post('/role-access/store','RoleController@access_store')->name('role.access.store');
//--------------End RoleController-------------------------------------

Route::get('users', 'UserController@index')->name('user.index');
Route::get('users/create', 'UserController@create')->name('user.create');
Route::get('users/{user}/edit', 'UserController@edit')->name('user.edit');
Route::put('users/{user}/edit', 'UserController@update')->name('user.update');
Route::post('users', 'UserController@store')->name('user.store');
Route::get('users/{user}/change-password', 'UserController@passChangeShowForm')->name('password.change');
Route::post('users/{user}/change-password', 'UserController@updatePassword');
Route::post('users/user-search','UserController@u_search')->name('user.search');

Route::get('/user-sp-index/{id}', 'UserController@u_sp_index')->name('user.sp.index');
Route::post('/user-sp-create', 'UserController@u_sp_store')->name('user.sp.create');
Route::delete('/user-sp/destroy/{id}','UserController@destroy');
//--------------End UserController-------------------------------------

});


Route::POST('/upload-image', 'Image\ImageController@upload_image')->name('image');


Route::any('/rpt/rpt-trial-bal3','Accounts\ReportController@getTrialBalance3')->name('rpt.trial.bal3');


Route::get('/dump', 'General\GeneralsController@dump'); 
