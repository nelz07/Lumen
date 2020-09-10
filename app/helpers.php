<?php 

use App\Fee;
use App\Loan;
use App\User;
use App\Client;
use App\Office;
use App\Cluster;
use App\Deposit;
use Carbon\Carbon;

use App\OfficeUser;

use App\PaymentMethod;
use Illuminate\Support\Str;
use App\DefaultPaymentMethod;
use App\Imports\OfficeImport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
    function carbon(){
        return new \Carbon\Carbon();
    }
    function structure(){
        $struc = [
            "level"=>'main_office',
            "parent_level"=>null,
            "child"=> [
                "parent_level"=>"main_office",
                "level"=>"region",
                "child"=> [
                    "parent_level"=>"region",
                    "level"=>"area",
                    "child"=> [
                        "parent_level"=>"area",
                        "level"=>"branch"
                    ]
                ]
            ]
        ];
       
        
        return collect($struc);
        
    }
    function createAdminAccount(){
        $user = User::create([
            'firstname' => 'Scheduler',
            'lastname' => 'Scheduler',
            'middlename' => 'Scheduler',
            'gender' => 'Male',
            'birthday' => Carbon::parse('1994-11-26'),
            'email' => 'scheduler@icloud.com',
            'notes'=>'scheduler account',
            'password' => Hash::make('sv9h4pld')
        ]);

        OfficeUser::create([
            'user_id'=>$user->id,
            'office_id'=>1
        ]);  

        $user = User::create([
            'firstname' => 'Ashbee',
            'lastname' => 'Morgado',
            'middlename' => 'Allego',
            'gender' => 'Male',
            'birthday' => Carbon::parse('1994-11-26'),
            'email' => 'ashbee.morgado@icloud.com',
            'notes'=>'ajalksdjfdlksafjaldf',
            'password' => Hash::make('sv9h4pld')
        ]);
        
            OfficeUser::create([
                'user_id'=>$user->id,
                'office_id'=>1
            ]);  

            $user = User::create([
                'firstname' => 'Nelson',
                'lastname' => 'Abilgos',
                'middlename' => 'Tan',
                'gender' => 'Male',
                'birthday' => Carbon::parse('1995-11-28'),
                'email' => 'nelsontan1128@gmail.com',
                'notes'=>'ajalksdjfdlksafjaldf',
                'password' => Hash::make('tannelsona')
            ]);
        
            OfficeUser::create([
                'user_id'=>$user->id,
                'office_id'=>1
            ]);   
            $user = User::create([
                'firstname' => 'Hannah Arien',
                'lastname' => 'Mangalindan',
                'middlename' => 'Morgado',
                'gender' => 'Female',
                'birthday' => Carbon::parse('1997-05-31'),
                'email' => 'arien@morgado.com',
                'notes'=>'ajalksdjfdlksafjaldf',
                'password' => Hash::make('sv9h4pld')
            ]);
        
            OfficeUser::create([
                'user_id'=>$user->id,
                'office_id'=>21
            ]);   
    }

    function createDeposits(){
        Deposit::create([
            'name'=>'RESTRICTED CBU',
            'product_id'=>'RCBU',
            'description'=>'aba ewan ko sa inyo',
            'minimum_deposit_per_transaction'=>0,
            'account_per_client'=>1,
            'auto_create_on_new_client'=>true,
            'interest_rate'=>2,
            'deposit_portfolio' => 0,
            'deposit_interest_expense' => 0,
        ]);
        Deposit::create([
            'name'=>'VOLUNTARY CBU',
            'product_id'=>'VCBU',
            'description'=>'aba ewan ko sa inyo',
            'minimum_deposit_per_transaction'=>50,
            'account_per_client'=>1,
            'auto_create_on_new_client'=>true,
            'interest_rate'=>2,
            'deposit_portfolio' => 0,
            'deposit_interest_expense' => 0,
        ]);
    }
    function generateFees(){
        Fee::create([
            'name'=>'MI FEE',
            'automated'=>true,
            'calculation_type'=>'fixed',
            'gl_account'=>523
        ]);
        Fee::create([
            'name'=>'Processing Fee 3%',
            'automated'=>true,
            'calculation_type'=>'percentage',
            'percentage' => 0.03,
            'gl_account'=>523,
        ]);
        Fee::create([
            'name'=>'Processing Fee 5%',
            'automated'=>true,
            'calculation_type'=>'percentage',
            'percentage' => 0.05,
            'gl_account'=>523,
        ]);
        Fee::create([
            'name'=>'CGLI Fee',
            'automated'=>true,
            'calculation_type'=>'matrix',
            
            'gl_account'=>526,
        ]);
        Fee::create([
            'name'=>'MI Premium',
            'automated'=>true,
            'calculation_type'=>'matrix',
            'gl_account'=>526,
        ]);
        Fee::create([
            'name'=>'PHIC Premium',
            'automated'=>true,
            'calculation_type'=>'matrix',
            'gl_account'=>526,
        ]);
      
    }
    function generateLoanProducts(){
        $id = Loan::create([
            "name"=>'MULTI-PURPOSE LOAN',
            "code"=>'MPL',
            "description"=>"Multi-Purpose Loan is a flexible Microfinance Loan for growth and expansion of business, for education, housing, asset acquisitions and farm needs amounting to 4k-99k and must qualify based on credit limit and loan performance criteria. Payable in 6 or 12 months only on a weekly cash basis. This is an individual yet CLUSTERED loan with minimum of 20 PARTNER CLIENTS. Pre-termination is allowed if 50% of loan is paid and with either the following reason: (1) Resigning from the program; (2) Transferring to another product",
            "account_per_client"=>2,
            "interest_calculation_method_id"=>103,

            "minimum_installment"=>12,
            "default_installment"=>22,
            "maximum_installment"=>24,

            "installment_length"=>1,
            "installment_method"=>'weeks',

            "interest_interval"=>'Monthly',
            "interest_rate"=>5.475225,
            

            "loan_minimum_amount"=>2000,
            "loan_maximum_amount"=>99000,

            "grace_period"=>'NO GRACE PERIOD',
            "has_tranches"=>false,
        
            "loan_portfolio_active"=>26,
            "loan_portfolio_in_arrears"=>26,
            "loan_portfolio_matured"=>26,

            "loan_interest_income_active"=>26,
            "loan_interest_income_in_arrears"=>26,
            "loan_interest_income_matured"=>26,

            "loan_write_off"=>26,
            "loan_recovery"=>26,
            "created_by"=>2,
            "status"=>1
        ])->id;
        Loan::find($id)->fees()->attach([Fee::find(1)->id]);
        Loan::find($id)->fees()->attach([Fee::find(2)->id]);
        Loan::find($id)->fees()->attach([Fee::find(4)->id]);
        Loan::find($id)->fees()->attach([Fee::find(5)->id]);
        $id = Loan::create([
            "code"=>'AGL',
            "name"=>'AGRICULTURAL LOAN',
            "description"=>"An individual agricultural production loan of 5k-150k for income rice farming households intended for production inputs and/or labor expenditures only. Loan term is from 3-6 months with monthly payment of interest and balloon payment of principal upon harvest and within maturity date. Pre-termination is allowed if 50% of loan is paid when yield happens in advance of the scheduled harvest but within the loan term applied.",
            
            "account_per_client"=>1,
            "interest_calculation_method_id"=>101,

            "minimum_installment"=>1,
            "default_installment"=>1,
            "maximum_installment"=>1,

            "installment_length"=>4,
            "installment_method"=>'weeks',

            "interest_interval"=>'Monthly',
            "interest_rate"=>2.5,

            "loan_minimum_amount"=>5000,
            "loan_maximum_amount"=>150000,

            "grace_period"=>'NO GRACE PERIOD',
            "has_tranches"=>true,
            "number_of_tranches"=>2,

            "loan_portfolio_active"=>26,
            "loan_portfolio_in_arrears"=>26,
            "loan_portfolio_matured"=>26,

            "loan_interest_income_active"=>26,
            "loan_interest_income_in_arrears"=>26,
            "loan_interest_income_matured"=>26,

            "loan_write_off"=>26,
            "loan_recovery"=>26,
            "created_by"=>2,
            "status"=>1
        ])->id;
        Loan::find($id)->fees()->attach([Fee::find(1)->id]);
        Loan::find($id)->fees()->attach([Fee::find(3)->id]);
        Loan::find($id)->fees()->attach([Fee::find(4)->id]);
        Loan::find($id)->fees()->attach([Fee::find(5)->id]);
        $id = Loan::create([
            "code"=>'GML',
            "name"=>'GROWTH ORIENTED MICROFINANCE ENTERPRISE LOAN',
            "description"=>"Growth Oriented Microfinance Enterprise Loan or GML is an individual productive loan for the growth and expansion of micro-enterprise sectors with loan amount of 100k-150k and must qualify based on credit limit and loan performance criteria. Payable in 6 or 12 months only on a Bi-monthly basis thru PDC (Loan and CBU). Pre-termination is allowed if 50% of loan is paid and with either of the following reason: (1) Resigning from the program; (2) Business expansion; (3) Transferring to another product.",

            "account_per_client"=>1,
            "interest_calculation_method_id"=>101,

            "minimum_installment"=>12,
            "default_installment"=>22,
            "maximum_installment"=>24,

            "installment_length"=>14,
            "installment_method"=>'days',

            "interest_interval"=>'Monthly',
            "interest_rate"=>5.18461,

            "loan_minimum_amount"=>100000,
            "loan_maximum_amount"=>150000,

            "grace_period"=>'NO GRACE PERIOD',
            "has_tranches"=>false,


            "loan_portfolio_active"=>26,
            "loan_portfolio_in_arrears"=>26,
            "loan_portfolio_matured"=>26,

            "loan_interest_income_active"=>26,
            "loan_interest_income_in_arrears"=>26,
            "loan_interest_income_matured"=>26,

            "loan_write_off"=>26,
            "loan_recovery"=>26,
            "created_by"=>2,
            "status"=>1
        ])->id;
        Loan::find($id)->fees()->attach([Fee::find(1)->id]);
        Loan::find($id)->fees()->attach([Fee::find(3)->id]);
        Loan::find($id)->fees()->attach([Fee::find(4)->id]);
        Loan::find($id)->fees()->attach([Fee::find(5)->id]);
        
    }
    function generatePaymentMethods(){
        $methods = array(
            [
            'name'=>'CASH ON HAND',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - BANK OF COMMERCE',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - BDO',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - BPI',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - CHINA BANK',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - EAST WEST',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - EAST WEST (RURAL)',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - LAND BANK',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - PBB',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - PNB',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - PNB (SAVINGS)',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - RCBC SAVINGS',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - SECURITY BANK',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CASH IN BANK - UCPB',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ],
            [
            'name'=>'CTLP',
            'for_disbursement'=>true,
            'for_repayment'=>true,
            'for_deposit'=>true,
            'for_withdrawal'=>true,
            'for_recovery'=>true,
            'gl_account_code'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
            ]            
        );
        PaymentMethod::insert($methods);
    }

    function generateDefaultPaymentMethods(){
        $list = Office::where('level','branch')->get();

        $list->map(function($branch){
            DefaultPaymentMethod::create([
                'office_id'=>$branch->id,
                'disbursement_payment_method_id'=>1,
                'repayment_payment_method_id'=>1,
                'deposit_payment_method_id'=>1,
                'withdrawal_payment_method_id'=>1,
                'recovery_payment_method_id'=>1,
                ]);
        });
    }

    function generateStucture   (){
        $structure = Excel::toCollection(new OfficeImport, "public/OFFICE STRUCTURE.xlsx");
        $data = array();
        $ctr = 0;
    
        foreach($structure[0] as $level){
            if ($ctr>0) {
                $data[] = array(
                'id'=>$level[0],
                'parent_id'=>$level[2],
                'level'=>$level[4],
                'name'=>$level[3],
                'code'=>$level[1],
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
                );
                // echo $level[3].' : '.$level[4].'<br>';
            }
            $ctr++;
        }
     
        Office::insert($data);
    }
    
    function createUser($branches=5){
        $data = array(
            'firstname'=>'Angeles',
            'middlename'=>'-',
            'lastname'=>'Branch',
            'gender'=>'Male',
            'birthday'=>'1994-11-26',
            'notes'=>'Notes are here. Wala lang. Test notes.',
            'email'=>'angeles@light.org.ph',
            'password'=> Hash::make('sv9h4pld'),
            'created_by'=>0

        );

        $user = User::create($data);

        OfficeUser::create([
            'user_id'=>$user->id,
            'office_id'=>Office::where('name','ANGELES')->first()->id
        ]);
        
        $data = array(
            'firstname'=>'Ashbee',
            'middlename'=>'Allego',
            'lastname'=>'Morgado',
            'gender'=>'Male',
            'birthday'=>'1994-11-26',
            'notes'=>'Notes are here. Wala lang. Test notes.',
            'email'=>'ashbee.morgado@icloud.com',
            'password'=> Hash::make('sv9h4pld'),
            'created_by'=>0

        );

        $user = User::create($data);

        

        $offices = Office::where('level','branch')->get()->random($branches);
    
        foreach($offices as $office){
            OfficeUser::create([
                'user_id'=>$user->id,
                'office_id'=>$office->id
            ]);
        }
    }

    function unauthorized(){
        return (array('msg'=>'Unauthorized Request'));
    }

    function generateCluster(){
        $office = Office::where('name','ANGELES')->first();
        $user = User::all()->random(1)->first();
        

        for($x=1;$x<=100;$x++){
            $client = Client::all()->random(1)->first();
            Cluster::create([
                'officer_id'=>$user->id,
                'office_id'=>$office->id,
                'client_id'=>$client->id,
                'code'=> 'ANG'.pad($x,3),
                'notes'=> 'hahaha'
            ]);
        }
            
    }
    function pad($number, $character, $padder='0'){
        return str_pad($number, $character, $padder, STR_PAD_LEFT);
    }
    function numberFormat($number, $decimals = 2, $sep = ".", $k = ","){
        $number = bcdiv($number, 1, $decimals); // Truncate decimals without rounding
        return number_format($number, $decimals, $sep, $k); // Format the number
    }

    function generateClientID($count=100){
        $branch = "010ANG";
        $ids = [];
        
        for($x=1; $x<=1000;$x++){
            $client_id = $branch."-PC".pad($x,5);
            $ids[] = $client_id;
        }

        return $ids;
    }

    function makeClientID($office_id){
        
        $office = Office::find($office_id);
        
        if($office->level=="branch"){
            $code = $office->code;
            $office_ids = $office->getLowerOfficeIDS();
            $count = Client::whereIn('office_id',$office_ids)->count();
            return $code . '-PC' . pad($count + 1, 5);
        }
        
        $office = $office->getTopOffice('branch');
        $code = $office->code;
        $office_ids = $office->getLowerOfficeIDS();
        $count = Client::whereIn('office_id',$office_ids)->count();
        return $code . '-PC' . pad($count + 1, 5);

    }
    function getNextID($string){
        // substr("ASDASDAS",)
        return substr($string, -5, 5);
    }

    function hasString($string, $match){
        return  Str::contains($string, $match);
    }

    function checkClientPaths(){
        if(!Storage::disk('local')->exists('signatures')){
            Storage::makeDirectory('public/signatures');
        }
        if(!Storage::disk('local')->exists('profile_photos')){
            Storage::makeDirectory('public/profile_photos');
        }
    }

    function breadcrumbize($string){
        $str = str_replace('/',' / ',$string);
        return str_replace('_',' ',$str);
    }
?>