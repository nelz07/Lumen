<?php
// ini_set('xdebug.max_nesting_level', 9999);

use App\BulkDisbursement;
use App\LoanAccount;
use App\Imports\TestImport;
use App\Events\BulkLoanDisbursed;
use App\LoanAccountDisbursement;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
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

// Route::get('/sample',function(Request $request){
//     echo 'test';
// });
// Route::get('/excel',function(){
//     $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//     $spreadsheet = $reader->load(public_path('templates/DST.xlsx'));
//     dd($spreadsheet);
//     return LoanAccount::first()->installments->each->append('status');
// });

Route::get('/hey',function(){
    $file = public_path('templates/DSTv1.xlsx');
    
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    // $spreadsheet = $reader->load($file);
    $sheet =$spreadsheet->getSheet(0);

    $accounts = BulkDisbursement::where('bulk_disbursement_id','24afdcfd53ee07d209cd2c6dab4447c70d49db2c')->get();
    $ctr = 1;
    $accounts->map(function($acc) use ($sheet,$spreadsheet,&$ctr){
        $cw = clone $sheet;
        $loan_account = $acc->loanAccount;
        $type = $loan_account->type;
        $feePayments  = $loan_account->feePayments->sortBy('fee_id');


        $cw->setTitle('#'.$ctr.' '.$acc->loanAccount->client->full_name);
        $dst = $spreadsheet->addSheet($cw);
        $dst->getCell('C18')->setValueExplicit($loan_account->amount,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->setCellValue('D8',$type->code);
        $dst->getCell('D9')->setValueExplicit($loan_account->amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        
        $dst->getCell('D10')->setValueExplicit($loan_account->installments->first()->amortization, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->getCell('D11')->setValueExplicit($type->interest_rate,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->getStyle('D11')->getNumberFormat()->setFormatCode('0.00'); 
        $dst->getCell('D12')->setValueExplicit($type->interest_rate / 4,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->getStyle('D12')->getNumberFormat()->setFormatCode('0.00'); 
        if ($type->code == "MPL") {
            $dst->getCell('D13')->setValueExplicit($feePayments->where('fee_id', 6)->first()->fee->percentage, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        }
        $dst->setCellValue('D14',$loan_account->number_of_installments);
        
        $dst->setCellValue('F19','=ROUND((H11*D13),2)');
        $dst->setCellValue('G19','=C18-F19');
        
        
        $dst->getCell('H9')->setValueExplicit($loan_account->interest, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->setCellValue('H10',$loan_account->number_of_months);
        $dst->getCell('H11')->setValueExplicit($loan_account->amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->setCellValue('H12',$loan_account->client->loanCycle());

        // $dst->setCellValue('H19',$loan_account->number_of_months);
  
        $dst->getCell('I19')->setValueExplicit($loan_account->total_loan_amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

        $dst->getCell('J19')->setValueExplicit($loan_account->interest, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

        $row = 20;
        $amortizartion_schedule_row = 5;


        $dst->getCell('AC4')->setValueExplicit($loan_account->amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->getCell('AD4')->setValueExplicit($loan_account->interest, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->getCell('AE4')->setValueExplicit($loan_account->total_loan_amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->getCell('AF4')->setValueExplicit($loan_account->total_loan_amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

        foreach($loan_account->installments as $item){
            $dst->setCellValue('C'.$row , $item->date->toDateString());
            $dst->getCell('D'.$row)->setValueExplicit($item->original_principal, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $dst->getCell('E'.$row)->setValueExplicit($item->original_interest, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $dst->getCell('G'.$row)->setValueExplicit(($item->amortization) * (-1), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $dst->getCell('H'.$row)->setValueExplicit($item->principal_balance, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $dst->getCell('I'.$row)->setValueExplicit(round($item->principal_balance + $item->interest_balance,2), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $dst->getCell('I'.$row)->setValueExplicit($item->interest_balance, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            
            

            $dst->setCellValue('AB'.$amortizartion_schedule_row, $item->date->toDateString());
            $dst->getCell('AC'.$amortizartion_schedule_row)->setValueExplicit($item->original_principal, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $dst->getCell('AD'.$amortizartion_schedule_row)->setValueExplicit($item->original_interest, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $dst->getCell('AE'.$amortizartion_schedule_row)->setValueExplicit($item->amortization, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $dst->getCell('AF'.$amortizartion_schedule_row)->setValueExplicit($item->principal_balance, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

            $amortizartion_schedule_row++;
            $row++;
        }

        $dst->setCellValue('Q5',$loan_account->client->full_name);
        $dst->setCellValue('Q6',$loan_account->client->address());
        $dst->setCellValue('Y7','=D9');
        


        $ctr++;
        $str = "(  ) Weekly               (  ) Semi-monthly          (  ) Monthly ";
        $str2 = "(  ) Quarterly           (  ) Semi-Annual            (  ) Annually";
        if($type->installment_method == 'weeks'){
            $str = "(X) Weekly               (  ) Semi-monthly          (  ) Monthly ";
        }
        $dst->setCellValue('M10',$str);
        $dst->setCellValue('M11',$str2);

        if($type->code == "MPL"){
            $dst->setCellValue('N14', $feePayments->where('fee_id', 6)->first()->fee->name);
            $dst->getCell('Y14')->setValueExplicit($feePayments->where('fee_id', 6)->first()->amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $dst->getCell('Y16')->setValueExplicit($feePayments->where('fee_id', 6)->first()->amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        }

        $row = 19;
        
        $non_finance_charges = $loan_account->nonFinanceCharges();
        $non_finance_charges->map(function($fee) use (&$row, &$dst){
            $dst->setCellValue('N'.$row,$fee->fee->name);
            $dst->getCell('Y'.$row)->setValueExplicit($fee->amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

            $row++;
        });
        
        $dst->setCellValue('Y24','=SUM(Y19:Y23)');
        $dst->setCellValue('Y26','=Y16+Y24');
        $dst->setCellValue('Y28','=Y7-Y26');
        $dst->setCellValue('Y30','=D13');
        $dst->setCellValue('Y32','=H80');
        $dst->setCellValue('Y38','=I19');

        $dst->setCellValue('R36',$loan_account->installments->first()->date->format('F d, Y'));
        $dst->getCell('Y36')->setValueExplicit($loan_account->installments->first()->amortization, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->getCell('O39')->setValueExplicit($loan_account->number_of_installments, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $dst->getCell('O40')->setValueExplicit($loan_account->installments->first()->amortization, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        

        $dst->setCellValue('D69','=SUM(D19:D67)');
        $dst->setCellValue('E69','=SUM(E19:E67)');
        $dst->setCellValue('F69','=SUM(F19:F67)');
        $dst->setCellValue('H76','=(1+G69)^52-1');
        $dst->setCellValue('H80','=((1+G69)^(52/12)-1)');


    });
    $spreadsheet->removeSheetByIndex(0);
    $spreadsheet->setActiveSheetIndex(0);
    
    $writer = new Xlsx($spreadsheet);
    $writer->setPreCalculateFormulas(false);
    $newFile = public_path('templates/test.xlsx');
    $writer->save($newFile);
    
    $headers = ['Content-Type'=> 'application/pdf','Content-Disposition'=> 'attachment;','filename'=>'DST.xlsx'];
    return response()->download($newFile,'DST.xlsx',$headers)->deleteFileAfterSend(true);
})->name('testiiiiing');
Route::get('/snappy',function(){
    $summary = session('ccr');
    
    return view('exports.test',compact('summary'));
    $pdf = App::make('snappy.pdf.wrapper');
    $file = public_path('temp/').$summary->office.' - '.$summary->repayment_date.'.pdf';            

    // $pdf->loadView('exports.test',compact('summary'))->setPaper('a4','landscape')->save($file);
    $pdf->loadView('exports.test',compact('summary'));
    return $pdf->stream();
    return;
    $headers = ['Content-Type'=> 'application/pdf','Content-Disposition'=> 'attachment;','filename'=>$summary->name];
    return response()->download($file,$summary->name,$headers);

});
Route::get('/import',function(){
    return view('test');
});
Route::post('/import',function(Request $request){
    Excel::import(new TestImport , $request->file('file'));
    
});
Route::get('/download/dst/{loan_account_id}','DownloadController@dst');
Route::get('/download/ccr',function(Request $request){

    $summary = session('ccr');
    $file = public_path('temp/').$summary->office.' - '.$summary->repayment_date.'.pdf';            
    // $pdf = app()->make('dompdf.wrapper');
    $pdf = App::make('snappy.pdf.wrapper');
    $headers = ['Content-Type'=> 'application/pdf','Content-Disposition'=> 'attachment;','filename'=>$summary->name];

    $pdf->loadView('exports.test',compact('summary'))->save($file,true);
    return response()->download($file,$summary->name,$headers);
    // $pdf->loadView('exports.ccrv2', compact('summary'))->setPaper('a4', 'landscape')->save($file);
    // $headers = ['Content-Type'=> 'application/pdf','Content-Disposition'=> 'attachment;','filename'=>$summary->name];
    // return response()->download($file,$summary->name,$headers);

});
Route::post('/ccr',function(Request $request){
    $pdf = App::make('dompdf.wrapper');
    $d_ids = array(2,1);
    sort($d_ids);   
    $data = [
        'office_id' => 21,
        'date'=>"2021-02-04",
        'loan_account_id' => 1,
        'deposit_product_ids'=>$d_ids,
    ];
    $d_ids = collect($request->deposit_products)->pluck('id')->sort();
    
    $request->merge([
        'deposit_product_ids' => $d_ids,
        'loan_account_id' => $request->loan_product_id
    ]);
    $request->request->deposit_product_ids = $d_ids;
    $request->request->loan_account_id = $request->loan_product_id;
    $data = $request->all();
    $summary  = \App\Account::repaymentsFromDate($data);
    
    $file = public_path('temp/').$summary->office.' - '.$summary->repayment_date.'.pdf';
    
    $pdf->loadView('exports.ccrv2',compact('summary'))->setPaper('a4','landscape')->save($file);
    // return $pdf->stream();
    
    $headers = ['Content-Type: application/zip','Content-Disposition: attachment; filename={$file}'];

    return response()->download($file, 200,$headers);
});

Route::get('/ccr',function(Request $request){
    $pdf = App::make('dompdf.wrapper');
    $d_ids = array(2,1);
    sort($d_ids);   
    $data = [
        'office_id' => 21,
        'date'=>"2021-02-04",
        'loan_account_id' => 1,
        'deposit_product_ids'=>$d_ids,
    ];
    $d_ids = collect($request->deposit_products)->pluck('id')->sort();
    $summary  = \App\Account::repaymentsFromDate($data);
    
    $file = public_path('temp/').$summary->office.' - '.$summary->repayment_date.'.pdf';
    
    $pdf->loadView('exports.ccrv2',compact('summary'))->setPaper('a4','landscape')->save($file);
    return $pdf->stream();

    $headers = ['Content-Type: application/zip','Content-Disposition: attachment; filename={$file}'];
    return response()->download($file, 200,$headers);
});
Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::get('/z',function(){
    $role = Role::firstOrCreate(['name' => 'Branch Accountant']);

    Permission::firstOrCreate(['name' => 'create client']);
    Permission::firstOrCreate(['name' => 'view dashboard']);

    $role->givePermissionTo(['view dashboard']);
    $role->givePermissionTo(['create client']);
    // $role->revokePermissionTo(['create client']);

    auth()->user()->assignRole($role);

});


Route::get('/loan/products','LoanController@');
Route::get('/random',function(){
    return view('random-picker');
});
Auth::routes();
Route::get('/fees','FeeController@getList');
Route::get('/ssss',function(){
    // \App\LoanAccount::first()->updateStatus();
});
Route::group(['middleware' => ['auth']], function () {

    Route::get('/stepper','ClientController@step');
    Route::get('/pay','RepaymentController@repayLoan');
    Route::post('/loan/calculator', 'LoanAccountController@calculate')->name('loan.calculator');
    Route::post('/products','ProductController@index');

    Route::get('/client/{client_id}/create/dependents', 'ClientController@toCreateDependents')->name('client.create.dependents');
    Route::post('/client/create/dependent', 'DependentController@createDependents')->name('create.dependents.post');
    Route::get('/client/update/dependent', 'DependentController@updateDependentStatus')->name('create.dependents.activate');
    Route::get('/client/{client_id}/manage/dependents', 'ClientController@dependents')->name('client.manage.dependents');
    Route::get('/dependents/{client_id}', 'ClientController@listDependents')->name('client.dependents.list');
    Route::get('/client/{client_id}/create/loan', 'LoanAccountController@index')->name('client.loan.create');
    Route::post('/client/create/loan', 'LoanAccountController@createLoan')->name('client.loan.create.post');
    Route::get('/client/{client_id}/loans', 'LoanAccountController@clientLoanList')->name('client.loan.list');
    Route::get('/loan/approve/{loan_id}','LoanAccountController@approve')->name('loan.approve');
    Route::get('/loan/disburse/{loan_id}','LoanAccountController@disburse')->name('loan.disburse');
    
    Route::get('/client/{client_id}/loans/{loan_id}','LoanAccountController@account')->name('loan.account');
    Route::post('/loans/repay','RepaymentController@accountPayment');
    Route::post('/loans/preterm','RepaymentController@preTerminate');
    Route::post('/revert','RevertController@revert')->name('revert.action');
    Route::get('/dashboard','DashboardController@index')->name('dashboard');
    Route::group(['middleware' => []], function () { 
        Route::get('/create/client','ClientController@index')->name('precreate.client');
        Route::post('/create/client','ClientController@createV1')->name('create.client'); 
    });
    Route::get('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/scopes', function(){
        return auth()->user()->scopesBranch();
    });
    Route::get('/usr/branches','UserController@branches');
    Route::get('/clients','ClientController@list')->name('client.list');
    Route::get('/clients/list','ClientController@getList')->name('get.client.list');
    Route::get('/client/{client_id}','ClientController@view')->name('client.profile');
    Route::get('/edit/client/{client_id}','ClientController@editClient');
    Route::post('/edit/client','ClientController@update');
    
    Route::post('/create/office/', 'OfficeController@createOffice');

    Route::get('/office/{level}', 'OfficeController@viewOffice')->name('offices.view');
    Route::get('/office/list/{level}','OfficeController@getOfficeList');

    Route::get('/edit/office/{id}', 'OfficeController@editOffice');
    Route::post('/edit/office/{id}', 'OfficeController@updateOffice');

    Route::get('/client/{client_id}/deposit/{deposit_account_id}', 'ClientController@depositAccount')->name('client.deposit'); 

    Route::post('/deposit/{deposit_account_id}','DepositAccountController@deposit')->name('client.make.deposit'); //make deposit transaction individually
    Route::get('/payment/methods','PaymentMethodController@fetchPaymentMethods');

    
    Route::get('/bulk/deposit', 'DepositAccountController@showBulkView')->name('bulk.deposit.deposit');
    Route::get('/bulk/withdraw', 'DepositAccountController@showBulkView')->name('bulk.deposit.withdraw');
    Route::get('/bulk/post_interest', 'DepositAccountController@showBulkView')->name('bulk.deposit.post_interest');
    
    Route::get('/bulk/create/loans', 'LoanAccountController@bulkCreateForm')->name('bulk.create.loans');
    Route::post('/loans/pending/list', 'LoanAccountController@pendingLoans');
    Route::post('/bulk/create/loans', 'LoanAccountController@bulkCreateLoan')->name('bulk.create.loans.post');
    
    Route::get('/bulk/approve/loans','LoanAccountController@bulkApproveForm')->name('bulk.approve.loans');
    Route::post('/bulk/approve/loans','LoanAccountController@bulkApprove')->name('bulk.approve.loans.post');
    
    Route::post('/loans/approved/list','LoanAccountController@approvedLoans');
    Route::get('/bulk/disburse/loans','LoanAccountController@bulkDisburseForm')->name('bulk.disburse.loans');
    Route::post('/bulk/disburse/loans','LoanAccountController@bulkDisburse')->name('bulk.disburse.loans.post');
    
    Route::post('/bulk/deposit', 'DepositAccountController@bulkDeposit')->name('bulk.deposit.deposit.post');
    Route::post('/bulk/withdraw', 'DepositAccountController@bulkWithdraw')->name('bulk.deposit.withdraw.post');
    Route::post('/bulk/post_interest', 'DepositAccountController@bulkPostInterest')->name('bulk.deposit.interst_post.post');
    
    Route::get('/bulk/repayment','RepaymentController@showBulkForm')->name('bulk.repayment');
    Route::post('/bulk/repayments','RepaymentController@bulkRepayment')->name('bulk.repayment.post');
    Route::post('/loans/scheduled/list','RepaymentController@scheduledList');
    
    
    Route::get('/deposits','DepositAccountController@showList');
    Route::get('/product','ProductController@getItems');
    Route::post('/deposit/{deposit_account_id}','DepositAccountController@deposit')->name('client.make.deposit');
    Route::post('/deposit/account/post/interest','DepositAccountController@postInterestByUser')->name('deposit.account.post.interest');


    Route::get('/accounts/{type}','AccountController@index')->name('accounts.list');

    // Route::post('/accounts/{type}','AccountController@filter')->name('accounts.all');

    Route::post('/loans/list','LoanController@postInterestByUser')->name('deposit.account.post.interest');


    Route::get('/loan/products','LoanController@loanProducts')->name('loan.products');
    Route::get('/settings/loan','LoanController@index')->name('settings.loan-products');
    Route::get('/settings/api/get/loans','LoanController@loanProducts')->name('settings.loan-list');
    Route::get('/auth/structure', 'UserController@authStructure')->name('auth.structure');


    Route::get('/settings/create/role', function(){
        return view('pages.create-role');
    });
    Route::get('/settings/create/user', function(){
        return view('pages.create-user');
    })->name('create.user');

    Route::get('/settings/create/fee', function(){
        return view('pages.create-fees');
    });

    Route::get('/settings/create/penalty', function(){
        return view('pages.create-penalty');
    });

    Route::get('/settings/create/office/{level}', 'OfficeController@createLevel')->name('create.office');

    Route::post('/search','SearchController@search');

    Route::get('/settings', function(){
        return view('pages.settings');
    })->name('administration');

    Route::get('/user/{user}','UserController@get');
    Route::get('/settings/create/loan', function(){
        return view('pages.create-loan');
    });

    Route::get('/settings/loan/edit/{loan}','LoanController@updateLoan'); //render view
    Route::get('/settings/loan/product/edit/{id}','LoanController@loanProduct'); //get product via id

    Route::post('/settings/loan/edit/{id}','LoanController@updateLoanProduct'); //post view
    
    Route::get('/settings/loan/view/{loan}','LoanController@viewLoan');
    
    Route::post('/settings/create/loan','LoanController@create');
    
    });
 

