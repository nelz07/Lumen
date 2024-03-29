<?php

namespace App;
use App\Client;
use App\Holiday;
use App\LoanAccount;
use App\PaymentMethod;
use App\DepositAccount;
use Faker\Factory as Faker;
use App\DefaultPaymentMethod;
use Illuminate\Support\Facades\Schema;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Office extends Model
{
    // protected $with = ['parent'];
    protected $fillable = ['name','code','parent_id','level','level_in_number'];
    protected $schema;
    protected $searchables = [
        'name',
    ];
    
    public static function lowerOffices($office_id, $id_only=true, $include_self = false){
        
    $query = \DB::table('offices')
                ->select('id','code','name','parent_id','level')
                ->where('id',$office_id)
                ->unionAll(
                    \DB::table('offices as o')
                        ->select('o.id','o.code','o.name','o.parent_id','o.level')
                        ->join('tree','tree.id','=','o.parent_id')
                );
        $tree = \DB::table('tree')
                ->withRecursiveExpression('tree',$query)
                ->when($include_self, function($q, $data) use ($office_id){
                    if(!$data){
                        $q->where('id','!=', $office_id);
                    }
                });
                // ->get();
                // ->when($id_only, function($q,$data){
                //     if($data){
                //         $q->get();
                //         $q->pluck('id');
                //     }else{
                //         $q->get();
                //     }
                // });
        $offices = [];    
        if($id_only){
            $tree = $tree->pluck('id');
            $tree->map(function($value, $key) use (&$offices){
                $offices[] = (int) $value;

            })->all();
        }else{
            $offices = $tree->get();
        }
        
        
        return $offices;
    }
    public function seed($count, $with_loans=false, $start_date = null){
        $office_id = $this->id;
        \DB::beginTransaction();
        try {
            $offices = Office::find($office_id)->children;
            $ids = [];
            if($offices->count() == 0){
                $office = Office::find($office_id);
                for ($x=1; $x<= $count; $x++) {
                    $faker = Faker::create();
                
                    $gender = $faker->randomElement(['MALE', 'FEMALE']);
                    $civil_status = $faker->randomElement(['SINGLE', 'MARRIED','DIVORCED']);
                    $education = $faker->randomElement(['ELEMENTARY', 'HIGH SCHOOL','COLLEGE','VOCATIONAL']);
                    $barangay = $faker->randomElement(['San Jose', 'Sta. Rita','Gordon Heights','Pag-asa']);
                    $province = $faker->randomElement(['Zambales', 'Pampanga','Bataan']);
                    $dependents = rand(1, 5);
                    $house_type = $faker->randomElement(['RENTED','OWNED']);
                    $mobile_number = '09'.rand(100000000, 199999999);
                    // $office = Office::where('name', '')->first();
                    static $id = 1;
                    $user  = Client::create([
                        'client_id' => Office::makeClientID($office->id),
                        'firstname' => $faker->firstName,
                        'middlename'=>$faker->lastname,
                        'lastname'  =>$faker->lastname,
                        'suffix'=>$faker->suffix,
                        'nickname'=>$faker->firstname,
                        'gender'=> $gender,
                        'profile_picture_path' => 'https://via.placeholder.com/150',
                        'signature_path' => 'https://via.placeholder.com/150',
                        'birthday' => $faker->dateTimeThisCentury->format('Y-m-d'),
                        'birthplace' => $faker->city,
                        'civil_status' => $civil_status,
                        'education' => $education,
                        'fb_account' => 'fb.com/primoashbee',
                        'contact_number'=>$mobile_number,
                        'street_address'=> $faker->address,
                        'barangay_address' => $barangay,
                        'city_address' => $faker->city,
                        'province_address' => $province,
                        'zipcode' => $faker->postCode,
                        'spouse_name' => $faker->name,
                        'spouse_contact_number' => $mobile_number,
                        'spouse_birthday' =>  $faker->dateTimeThisCentury->format('Y-m-d'),
                        'number_of_dependents' => $dependents,
                        'household_size' =>$dependents +2,
                        'years_of_stay_on_house' => $dependents + 5,
                        'house_type' => $house_type,
                        'tin' => rand(100000, 199999),
                        'umid' => rand(10000, 19999),
                        'sss' =>rand(10000, 19999),
                        'mother_maiden_name' => $faker->firstNameFemale.' '.$faker->lastname,
                        'notes' => $faker->realText($faker->numberBetween(10, 200)),
                        'office_id' => $office->id,
                        'created_by' => 0
                    ]);
                    $ids[]=  $user->id;
                    $application_number = rand(1000000,2);

                    $unit_of_plan = rand(1,2);
                    $member_first = $user->firstname;
                    $member_middle = $user->middlename;
                    $member_last = $user->lastname;
                    $birthday= $user->getRawOriginal('birthday');
                    $user->dependents()->create([
                        'application_number'=>$application_number,
                        'unit_of_plan'=>$unit_of_plan,
                        'member_firstname'=>$member_first,
                        'member_middlename'=>$member_middle,
                        'member_lastname'=>$member_last,
                        'created_by'=>2,
                        'member_birthday'=>$birthday
                    ]);
                }
            }
            $offices->map(function($office) use($count, $with_loans){
                for ($x=1; $x<= $count; $x++) {
                    $faker = Faker::create();
                
                    $gender = $faker->randomElement(['MALE', 'FEMALE']);
                    $civil_status = $faker->randomElement(['SINGLE', 'MARRIED','DIVORCED']);
                    $education = $faker->randomElement(['ELEMENTARY', 'HIGH SCHOOL','COLLEGE','VOCATIONAL']);
                    $barangay = $faker->randomElement(['San Jose', 'Sta. Rita','Gordon Heights','Pag-asa']);
                    $province = $faker->randomElement(['Zambales', 'Pampanga','Bataan']);
                    $dependents = rand(1, 5);
                    $house_type = $faker->randomElement(['RENTED','OWNED']);
                    $mobile_number = '09'.rand(100000000, 199999999);
                    // $office = Office::where('name', '')->first();
                    static $id = 1;
                    $user  = Client::create([
                        'client_id' => Office::makeClientID($office->id),
                        'firstname' => $faker->firstName,
                        'middlename'=>$faker->lastname,
                        'lastname'  =>$faker->lastname,
                        'suffix'=>$faker->suffix,
                        'nickname'=>$faker->firstname,
                        'gender'=> $gender,
                        'profile_picture_path' => 'https://via.placeholder.com/150',
                        'signature_path' => 'https://via.placeholder.com/150',
                        'birthday' => $faker->dateTimeThisCentury->format('Y-m-d'),
                        'birthplace' => $faker->city,
                        'civil_status' => $civil_status,
                        'education' => $education,
                        'fb_account' => 'fb.com/primoashbee',
                        'contact_number'=>$mobile_number,
                        'street_address'=> $faker->address,
                        'barangay_address' => $barangay,
                        'city_address' => $faker->city,
                        'province_address' => $province,
                        'zipcode' => $faker->postCode,
                        'spouse_name' => $faker->name,
                        'spouse_contact_number' => $mobile_number,
                        'spouse_birthday' =>  $faker->dateTimeThisCentury->format('Y-m-d'),
                        'number_of_dependents' => $dependents,
                        'household_size' =>$dependents +2,
                        'years_of_stay_on_house' => $dependents + 5,
                        'house_type' => $house_type,
                        'tin' => rand(100000, 199999),
                        'umid' => rand(10000, 19999),
                        'sss' =>rand(10000, 19999),
                        'mother_maiden_name' => $faker->firstNameFemale.' '.$faker->lastname,
                        'notes' => $faker->realText($faker->numberBetween(10, 200)),
                        'office_id' => $office->id,
                        'created_by' => 0
                    ]);
                    $application_number = rand(1000000,2);

                    $unit_of_plan = rand(1,2);
                    $member_first = $user->firstname;
                    $member_middle = $user->middlename;
                    $member_last = $user->lastname;
                    $birthday= $user->getRawOriginal('birthday');
                    $user->dependents()->create([
                        'application_number'=>$application_number,
                        'unit_of_plan'=>$unit_of_plan,
                        'member_firstname'=>$member_first,
                        'member_middlename'=>$member_middle,
                        'member_lastname'=>$member_last,
                        'created_by'=>2,
                        'member_birthday'=>$birthday
                    ]);
                }
            });

            if($with_loans){
                $bulk_disbursement_id = sha1(time());
                if(is_null($start_date)){
                    $start = now()->startOfDay()->subDays(6);
                    for($x=0;$x<=6;$x++){
                        $dates[] = $start->copy()->addDays($x);
                    }
                    $disbursement_date =  $dates[rand(0,count($dates)-1)];
                }else {
                    $disbursement_date = $start_date;
                }

                foreach(Client::whereIn('id',$ids)->get() as $client){
                    createLoan($client, $bulk_disbursement_id, uniqid(),$disbursement_date, $start_date);
                }
            }
            \DB::commit();
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    public static function makeClientID($office_id,$increment=null){
        $office = Office::find($office_id);


        // check level if branch
        if ($office->level =='branch') {
            $office_ids = $office->getLowerOfficeIDS();
        } else {
            $office = $office->getTopOffice('branch');
            if (is_null($office)) {
                return null;
            }
            $office_ids = $office->getLowerOfficeIDS();
        }
        if (is_null($increment)) {
            $office_ids = $office->getLowerOfficeIDS();
            $increment = Client::whereIn('office_id', $office_ids)->count()+1;
        } else {
            $increment++;
        }
       
        $code = $office->code;
        $client_id = $code . '-PC' . pad($increment, 5);
        if (Client::where('client_id', $client_id)->count() > 0) {
            return Office::makeClientID($office_id, $increment);
        }
        return $client_id;
    }
       
  
    public static function levelCount($level){
        $me = new static;
        return $me->where('level',$level)->count();
    }
    
    public static function getLevelList($level){
        $me = new static;
        return $me->where('level',$level)->get();
    }

    public function parent(){
        return $this->belongsTo(static::class, 'parent_id');
    }
    
    public function children(){
        return $this->hasMany(static::class, 'parent_id');
    }   

    public function user(){
        return $this->belongsToMany(User::class);
    }

    public function clients(){
        return $this->hasMany(Client::class);
    }

    public function staffs(){
        return $this->belongsToMany(User::class);
    }

    public function clusters(){
        $children = $this->children;
        $ids = [];
        foreach($children as $child){
            if($child->level =='cluster'){
                array_push($ids,$child);
            }
            $ids = array_merge($ids, $child->clusters());

        }
        return $ids;
    }
    public function getChildIDS(){
        $children = $this->children;
        
        $count = $this->children->count();
        $ids = [];
        //if ($count>0) {
            foreach ($children as $child) {
                array_push($ids,$child->id);
                $ids = array_merge($ids, $child->getChildIDS());
            }
        //}

        return $ids;
    }

    public function getChild(){
        $children = $this->children;
        
        // $count = $this->children->count();
        $result = [];
        //if ($count>0) {
            foreach ($children as $child) {
                array_push($result,$child);
                $result = array_merge($result, $child->getChild());
            }
        //}

        return $result;
    }
    public function getAllChildren($level=false){
        $children = $this->children;
        $ids = [];
            foreach ($children as $child) {
                if($level != false){
                    if($child->level==$level){
                        array_push($ids, $child);
                        // return ($ids);
                        $child->getAllChildren($level);
                        // $ids = array_merge($ids, $child->getAllChildren($level));    
                        
                        // dd($ids);

                    }else{
                        $child->getAllChildren($level);

                    }
                }else{
                    array_push($ids, $child);
                    $ids = array_merge($ids, $child->getAllChildren());
                }
            }
        return $ids;
    }
    public function getAllChildrenIDS($level=false){
        $children = $this->children;
        $ids = [];
            foreach ($children as $child) {
                if ($level != false) {
                    if ($child->level==$level) {
                        array_push($ids, $child->id);
                        $child->getAllChildrenIDS($level);
                    }
                }else{
                    array_push($ids, $child->id);
                    $ids = array_merge($ids, $child->getAllChildrenIDS());
                }
            }
        return $ids;
    }
    public function getAllParentIDS($with_self=false){
        $parent = $this->parent;
        $ids = [];

        if(is_null($parent)){
            return $ids;
        }
        array_push($ids,$parent->id);
        $ids = array_merge($ids,$parent->getAllParentIDS());
        return $ids;
    }
    //parameters insert_self if we want to add the parent id to the return lower office ids
    public function getLowerOfficeIDS($insert_self = true){
        $id = $this->id;
        $child_ids = $this->getAllChildrenIDS();
        if ($insert_self) {
            return array_merge($child_ids, [$id]);
        }
        return $child_ids;
    }
    public function getParent(){
        return $this->parent;
    }
    public function getBranch(){
        $parents = [];
        echo $this->name;
        if($this->level=="branch"){
            return 'nice';
        }

        array_push($parents, $this);
        $this->getBranch();
        return $parents;
    
                
    }

    public static function getUpperOfficesV2($office_id, $level=false, $id_only=true, $include_self = false){
        if($level != false){
            $office = Office::find($office_id);
            if($office->level == $level){
                return $office;
            }
            
        }
        $query = \DB::table('offices')
                ->select('id','code','name','parent_id','level')
                ->where('id',$office_id)

                ->unionAll(
                    \DB::table('offices as o')
                        ->select('o.id','o.code','o.name','o.parent_id','o.level')
                        ->join('tree','tree.parent_id','=','o.id')
                )
                ->when($level, function($q, $data) use ($office_id){
                    if($data){
                        $q->where('level',$data);
                    }
                });
        $tree = \DB::table('tree')
                ->withRecursiveExpression('tree',$query)
                ->when($include_self, function($q, $data) use ($office_id){
                    if(!$data){
                        $q->where('id','!=', $office_id);
                    }
                });
                // ->get();
                // ->when($id_only, function($q,$data){
                //     if($data){
                //         $q->get();
                //         $q->pluck('id');
                //     }else{
                //         $q->get();
                //     }
                // });
        $offices = [];    
        if($id_only){
            $tree = $tree->pluck('id');
            $tree->map(function($value, $key) use (&$offices){
                $offices[] = (int) $value;

            })->all();
        }else{
            if ($level != false) {
                $offices = $tree->where('level', $level)->get();
            }else{
                $offices = $tree->get();
            }
        }
        
        
        return $offices;
    }
    public static function getTopOfficeV2($office_id){
        $office = Office::find($office_id);
        if($office->level=="branch"){
            return $office;
        }
    }

    function getTopOffice($level="main_office"){
        
        if($this->level==$level){
            return $this;
        }
        $parent = $this->getParent();
        if($parent == null){
            return $parent;
        }
        if($parent->level == $level){
         return $parent;
        }else{
            return $parent->getTopOffice($level);
        }
    }

    public static function schema(){
    $schema = array(array(
                    "level"=>"main_office",
                    "parent"=>null,
                    "children" =>['region','area','branch','unit','cluster','account_officer']
                ),
                array(
                    "level"=>"region",
                    "parent"=>"main_office",
                    "children" =>['area','branch','unit','cluster','account_officer']
                ),
                array(
                    "level"=>"area",
                    "parent"=>"region",
                    "children" =>['branch','unit','cluster','account_officer']
                ),
                array(
                    "level"=>"branch",
                    "parent"=>"area",
                    "children" =>['unit','cluster','account_officer']
                ),
                array(
                    "level"=>"unit",
                    "parent"=>"branch",
                    "children" =>['cluster']
                ),
                array(
                    "level"=>"cluster",
                    "parent"=>"unit",
                    "children" =>[]
                ),
                array(
                    "level"=>"account_officer",
                    "parent"=>"branch",
                    "children" =>[]
                )
            );
            
        return collect($schema);
    }

    public static function getParentOfLevel($level){
      
        $me = new static;
        $schema = $me->schema();
        $curr_level =  $schema->filter(function($item) use ($level){
            
            return $item['level'] == $level;
        })->values();
        return $curr_level->first()['parent'];
    }
    public static function isChildOf($parent_level, $level){
        $me = new static;
        $schema = $me->schema();

        $list = $schema->filter(function($item) use ($parent_level){
            if ($item['level']==$parent_level) {
                return $item;
            }
        })->values()->first()['children'];
        return in_array($level,$list) ? true : false;
    }

    
    


    public static function like($level, $query, $office_id=null){
        $me = new static;
        $searchables = $me->searchables;
        
        $office = Office::where('level', $level)->get();
        
        if(count($office)>0){
            if($query!=null){
                $office = Office::with('parent')->where('level',$level)->where(function(Builder $dbQuery) use($searchables, $query){
                    foreach($searchables as $item){  
                        $dbQuery->where($item,'LIKE','%'.$query.'%');
                    }
                });
                
                return $office;
            }

            $office = Office::with('parent')->where('level',$level);
            
            return $office;
        }
    }
    public function defaultPaymentMethod(){
        if($this->isChildOf('area',$this->level)){
            $level = $this->getTopOffice('branch');
            return $level->hasOne(DefaultPaymentMethod::class);
        }
            return $this->hasOne(DefaultPaymentMethod::class);
        
    }

    public function getClients(){
        $ids = $this->getLowerOfficeIDS();
        return Client::whereIn('office_id',$ids)->orderBy('lastname')->get();
    }


    public function accounts(array $query = [], $limited_fields = true){
        $ids = Office::lowerOffices($this->id,true, true);
        $status = $query['status'];
        // $loan_ids = is_null($query['loan_ids']) ? [] : $query['loan_ids'] ;
        // $deposit_ids = is_null($query['deposit_ids']) ? [] : $query['deposit_ids'] ;
        $space = " ";
        $clients = \DB::table('clients');
        $offices = \DB::table('offices');
        $loans = \DB::table('loans');
        $deposits = \DB::table('deposits');
        $users = \DB::table('users');
        $office_user = \DB::table('office_user');   
        $space=' ';
        $deposits_accounts = \DB::table('deposit_accounts');

        if($query['type'] == 'all'){
            if($limited_fields){
                $select = [
                    'offices.name as office',
                    'clients.client_id as client_id',
                    'loan_accounts.id as id',
                    'loan_accounts.amount as loan_amount',
                    \DB::raw("CONCAT(clients.firstname,'{$space}',clients.middlename,'{$space}',clients.lastname) as fullname"),
                    'loans.code as code',
                    'loan_accounts.principal as principal',
                    'loan_accounts.interest as interest',
                    'loan_accounts.total_loan_amount as total_loan_amount',
                    'loan_accounts.principal_balance as principal_balance',
                    'loan_accounts.interest_balance as interest_balance',
                    'loan_accounts.total_balance as total_balance',
                    'loan_accounts.disbursed_amount as disbursed_amount',
                    'loan_accounts.status as status',
                    'rcbu.balance as RCBU',
                    'mcbu.balance as MCBU',
                    \DB::raw("CONCAT(users.firstname,'{$space}',users.lastname) as loan_officer"),
                ];
            }else{
                $select = [
                    'offices.name as office',
                    'clients.client_id as client_id',
                    'loan_accounts.id as id',
                    'loan_accounts.amount as loan_amount',
                    \DB::raw("CONCAT(clients.firstname,'{$space}',clients.middlename,'{$space}',clients.lastname) as fullname"),
                    'loans.code as code',
                    'loan_accounts.principal as principal',
                    'loan_accounts.interest as interest',
                    'loan_accounts.total_loan_amount as total_loan_amount',
                    'loan_accounts.principal_balance as principal_balance',
                    'loan_accounts.interest_balance as interest_balance',
                    'loan_accounts.total_balance as total_balance',
                    'loan_accounts.status as status',
                    'loan_accounts.interest_rate as interest_rate',
                    'loan_accounts.number_of_months as number_of_months',
                    'loan_accounts.number_of_installments as number_of_installments',
                    'loan_accounts.total_deductions as total_deductions',
                    'loan_accounts.disbursed_at as disbursed_at',
                    'loan_accounts.disbursed_amount as disbursed_amount',
                    'loan_accounts.first_payment_date as first_payment_date',
                    'loan_accounts.last_payment_date as last_payment_date',
                    'rcbu.balance as RCBU',
                    'mcbu.balance as MCBU',
                    \DB::raw("CONCAT(users.firstname,'{$space}',users.lastname) as loan_officer"),
                ];
            }
            $accounts = \DB::table('loan_accounts')
                    ->select(
                       $select
                    )
                    ->when($query['office_id'], function($q, $data){
                        $ids = Office::lowerOffices($data,true,true);
                        $q->whereIn('clients.office_id',$ids);
                    })
                    ->when($query['products'], function($q, $data){
                        $q->whereIn('loans.id',$data);
                    })
                    ->when($query['status'], function($q, $data){
                        $q->whereIn('loan_accounts.status',$data);
                    })
                    ->leftJoinSub($clients, 'clients', function ($join) {
                        $join->on('clients.client_id', '=', 'loan_accounts.client_id');
                    })
                    ->leftJoinSub($offices, 'offices', function ($join) {
                        $join->on('offices.id', '=', 'clients.office_id');
                    })
                    ->leftJoinSub($loans, 'loans', function ($join) {
                        $join->on('loans.id', '=', 'loan_accounts.loan_id');
                    })
                    ->leftJoinSub($deposits_accounts,'rcbu', function($join){
                        $join->on('clients.client_id','rcbu.client_id')
                        ->where('rcbu.deposit_id','1');
                    })
                    ->leftJoinSub($deposits_accounts,'mcbu', function($join){
                        $join->on('clients.client_id','mcbu.client_id')
                        ->where('mcbu.deposit_id','2');
                    })
                    ->leftJoinSub($office_user,'office_user', function($join){
                        $join->on('office_user.office_id','offices.id')
                        ->where('offices.level','cluster');
                    })
                    ->leftJoinSub($users,'users',function($join){
                        $join->on('users.id','office_user.user_id');
                    });
            $summary = clone $accounts;
            $summary = $summary->select(
                \DB::raw('COUNT(loan_accounts.id) as total_accounts'),
                \DB::raw('ROUND(SUM(principal),2) as total_principal'),
                \DB::raw('ROUND(SUM(interest),2) as total_interest'),
                \DB::raw('ROUND(SUM(total_loan_amount),2) as total_loan_amount'),
                \DB::raw('ROUND(SUM(principal_balance),2) as total_principal_balance'),
                \DB::raw('ROUND(SUM(total_balance),2) as total_balance'),
                \DB::raw('ROUND(SUM(rcbu.balance),2) as total_rcbu'),
                \DB::raw('ROUND(SUM(mcbu.balance),2) as total_mcbu'),
                
            )
            ->first();
            return compact('accounts','summary');
        }
        
        if($query['type'] == 'loan'){
            if($limited_fields){
                $select = [
                    'offices.name as office',
                    'clients.client_id as client_id',
                    'loan_accounts.id as id',
                    'loan_accounts.amount as loan_amount',
                    \DB::raw("CONCAT(clients.firstname,'{$space}',clients.middlename,'{$space}',clients.lastname) as fullname"),
                    'loans.code as code',
                    'loan_accounts.principal as principal',
                    'loan_accounts.interest as interest',
                    'loan_accounts.total_loan_amount as total_loan_amount',
                    'loan_accounts.principal_balance as principal_balance',
                    'loan_accounts.interest_balance as interest_balance',
                    'loan_accounts.total_balance as total_balance',
                    'loan_accounts.disbursed_amount as disbursed_amount',
                    'loan_accounts.status as status',
                    \DB::raw("CONCAT(users.firstname,'{$space}',users.lastname) as loan_officer")
                ];
            }else{
                $select = [
                    'offices.name as office',
                    'clients.client_id as client_id',
                    'loan_accounts.id as id',
                    'loan_accounts.amount as loan_amount',
                    \DB::raw("CONCAT(clients.firstname,'{$space}',clients.middlename,'{$space}',clients.lastname) as fullname"),
                    'loans.code as code',
                    'loan_accounts.principal as principal',
                    'loan_accounts.interest as interest',
                    'loan_accounts.total_loan_amount as total_loan_amount',
                    'loan_accounts.principal_balance as principal_balance',
                    'loan_accounts.interest_balance as interest_balance',
                    'loan_accounts.total_balance as total_balance',
                    'loan_accounts.status as status',
                    'loan_accounts.interest_rate as interest_rate',
                    'loan_accounts.number_of_months as number_of_months',
                    'loan_accounts.number_of_installments as number_of_installments',
                    'loan_accounts.total_deductions as total_deductions',
                    'loan_accounts.disbursed_at as disbursed_at',
                    'loan_accounts.disbursed_amount as disbursed_amount',
                    'loan_accounts.first_payment_date as first_payment_date',
                    'loan_accounts.last_payment_date as last_payment_date',
                    \DB::raw("CONCAT(users.firstname,'{$space}',users.lastname) as loan_officer"),
                ];
            }
            $accounts = \DB::table('loan_accounts')
                    ->select(
                       $select
                    )
                    ->when($query['office_id'], function($q, $data){
                        $ids = Office::lowerOffices($data,true,true);
                        $q->whereIn('clients.office_id',$ids);
                    })
                    ->when($query['products'], function($q, $data){
                        $q->whereIn('loans.id',$data);
                    })
                    ->when($query['status'], function($q, $data){
                        $q->whereIn('loan_accounts.status',$data);
                    })
                    ->leftJoinSub($clients, 'clients', function ($join) {
                        $join->on('clients.client_id', '=', 'loan_accounts.client_id');
                    })
                    ->leftJoinSub($offices, 'offices', function ($join) {
                        $join->on('offices.id', '=', 'clients.office_id');
                    })
                    ->leftJoinSub($loans, 'loans', function ($join) {
                        $join->on('loans.id', '=', 'loan_accounts.loan_id');
                    })
                    ->leftJoinSub($office_user,'office_user', function($join){
                        $join->on('office_user.office_id','offices.id')
                        ->where('offices.level','cluster');
                    })
                    ->leftJoinSub($users,'users',function($join){
                        $join->on('users.id','office_user.user_id');
                    });
            $summary = clone $accounts;
            $summary = $summary->select(
                \DB::raw('COUNT(loan_accounts.id) as total_accounts'),
                \DB::raw('ROUND(SUM(principal),2) as total_principal'),
                \DB::raw('ROUND(SUM(interest),2) as total_interest'),
                \DB::raw('ROUND(SUM(total_loan_amount),2) as total_loan_amount'),
                \DB::raw('ROUND(SUM(principal_balance),2) as total_principal_balance'),
                \DB::raw('ROUND(SUM(total_balance),2) as total_balance'),
            )
            ->first();
            return compact('accounts','summary');
        }
        
        if($query['type'] == 'deposit'){
            $accounts = \DB::table('deposit_accounts')
                    ->select(
                        'offices.name as office',
                        'clients.client_id as client_id',
                        'deposit_accounts.id as id',
                        \DB::raw("CONCAT(clients.firstname,'{$space}',clients.middlename,'{$space}',clients.lastname) as fullname"),
                        'deposits.product_id as code',
                        \DB::raw("CONCAT(users.firstname,'{$space}',users.lastname) as loan_officer"),
                        'deposit_accounts.accrued_interest as accrued_interest',
                        'deposit_accounts.balance as balance',
                        'deposit_accounts.status as status',
                        \DB::raw("CONCAT(users.firstname,'{$space}',users.lastname) as loan_officer"),
                    )
                    ->when($query['products'], function($q, $data){
                        $q->whereIn('deposits.id',$data);
                    })
                    ->when($query['office_id'], function($q, $data){
                        $ids = Office::lowerOffices($data,true,true);
                        $q->whereIn('clients.office_id',$ids);
                    })
                    ->when($query['status'], function($q, $data){
                        $q->whereIn('deposit_accounts.status',$data);
                    })
                    ->leftJoinSub($clients, 'clients', function ($join) {
                        $join->on('clients.client_id', '=', 'deposit_accounts.client_id');
                    })
                    ->leftJoinSub($offices, 'offices', function ($join) {
                        $join->on('offices.id', '=', 'clients.office_id');
                    })
                    ->leftJoinSub($deposits, 'deposits', function ($join) {
                        $join->on('deposits.id', '=', 'deposit_accounts.deposit_id');
                    })
                    ->leftJoinSub($office_user,'office_user', function($join){
                        $join->on('office_user.office_id','offices.id')
                        ->where('offices.level','cluster');
                    })
                    ->leftJoinSub($users,'users',function($join){
                        $join->on('users.id','office_user.user_id');
                    });;
            $summary = clone $accounts;
            $summary = $summary->select(
                \DB::raw('COUNT(deposit_accounts.id) as total_accounts'),
                \DB::raw('ROUND(SUM(balance),2) as total_balance'),
                \DB::raw('ROUND(SUM(accrued_interest),2) as total_accrued_interest'),
            )
            ->first();
            
            
            
            return compact('accounts','summary');
            
        }

        
        
        
    }

    public function loanAccounts(array $query = []){
        
        $ids = $this->getLowerOfficeIDS();
        
        return Client::select('office_id','client_id','firstname','lastname')->whereIn('office_id',$ids)
        ->whereHas('loanAccounts', function($q)  use ($query) { 
            foreach($query as $key=>$value){
                if($key=='loan_id'){
                    $q->whereIn($key,$value);
                }elseif($key=='status'){
                    if($value!="All"){
                        $q->where($key, $value);
                    }
                }else{
                    $q->where($key, $value);
                }
            }
        })
        ->with([
        'loanAccounts',
        'office'=>function($q){
            $q->select('id','name');
        }]);
        
    }
    public function depositAccountsV2(array $query = []){
        
        $ids = $this->getLowerOfficeIDS();
        
        return Client::select('office_id','client_id','firstname','lastname')->whereIn('office_id',$ids)
        ->whereHas('deposits', function($q)  use ($query) { 
            foreach($query as $key=>$value){
                if($key=='deposit_id'){
                    $q->whereIn($key,$value);
                }elseif($key=='status'){
                    if($value!="All"){
                        $q->where($key, $value);
                    }
                }else{
                    $q->where($key, $value);
                }
            }
        })
        ->with([
        'deposits',
        'office'=>function($q){
            $q->select('id','name');
        }]);
        
    }



    public function getLoanAccounts($type=null,$loan_product_id=null){
        if (is_null($loan_product_id)) {
            if ($type==null) {

            }
            if ($type=='pending') {
                $ids = $this->getLowerOfficeIDS();
                $client_ids = Client::select('id', 'client_id')->whereIn('office_id', $ids)->orderBy('lastname')->pluck('client_id')->toArray();
                return $accounts = LoanAccount::whereIn('client_id', $client_ids)->where('approved', false)->get();
            }
            if ($type=='approved') {
                $ids = $this->getLowerOfficeIDS();
                $client_ids = Client::select('id', 'client_id')->whereIn('office_id', $ids)->orderBy('lastname')->pluck('client_id')->toArray();
                return $accounts = LoanAccount::whereIn('client_id', $client_ids)->whereNull('disbursed_at')->whereNull('disbursed_by')->get();
            }
            if ($type=='active') {
                $ids = $this->getLowerOfficeIDS();
                $client_ids = Client::select('id', 'client_id')->whereIn('office_id', $ids)->orderBy('lastname')->pluck('client_id')->toArray();
                return $accounts = LoanAccount::whereIn('client_id', $client_ids)->whereNotNull('disbursed_at')->get();
            }
        }else{
            //if product type is selected
            if ($type==null) {
            }
            if ($type=='pending') {
                $ids = $this->getLowerOfficeIDS();
                $client_ids = Client::select('id', 'client_id')->whereIn('office_id', $ids)->orderBy('lastname')->pluck('client_id')->toArray();
                return $accounts = LoanAccount::whereIn('client_id', $client_ids)->where('approved', false)->where('loan_id',$loan_product_id)->get();
            }
            if ($type=='approved') {
                $ids = $this->getLowerOfficeIDS();
                $client_ids = Client::select('id', 'client_id')->whereIn('office_id', $ids)->orderBy('lastname')->pluck('client_id')->toArray();
                return $accounts = LoanAccount::whereIn('client_id', $client_ids)->whereNull('disbursed_at')->whereNull('disbursed_by')->where('loan_id',$loan_product_id)->get();
            }
            if ($type=='active') {
                $ids = $this->getLowerOfficeIDS();
                $client_ids = Client::select('id', 'client_id')->whereIn('office_id', $ids)->orderBy('lastname')->pluck('client_id')->toArray();
                return $accounts = LoanAccount::whereIn('client_id', $client_ids)->whereNotNull('disbursed_at')->where('loan_id',$loan_product_id)->get();
            }



        }
    }

    public static function depositAccounts($office_id, $deposit_id=null){
        if ($deposit_id!=null) {
            $client_ids = Office::find($office_id)->getClients()->pluck('client_id');
            return DepositAccount::with('type', 'client.office')->whereIn('client_id', $client_ids)->where(function ($query) use ($deposit_id) {
                $query->where('deposit_id', $deposit_id);
            });
        }
        
        $client_ids = Office::find($office_id)->getClients()->pluck('client_id');
        return DepositAccount::with('type', 'client.office')->whereIn('client_id', $client_ids);
    }
    public function defaultPaymentMethods(){
        
        $pm = $this->defaultPaymentMethod;
        
        if($pm==null){
            $res['for_disbursement'] = null;
            $res['for_repayment'] = null;
            $res['for_deposit'] = null;
            $res['for_withdrawal'] = null;
            $res['for_recovery'] = null;
            return $res;
        }
        $res['for_disbursement'] = $pm->disbursement_payment_method_id;
        $res['for_repayment'] = $pm->repayment_payment_method_id;
        $res['for_deposit'] = $pm->deposit_payment_method_id;
        $res['for_withdrawal'] = $pm->withdrawal_payment_method_id;
        $res['for_recovery'] = $pm->recovery_payment_method_id;
        return $res;
    }

    public function holidays(){
        return $this->hasMany(Holiday::class);
    }

    public function getLevelInNumberAttribute(){
        $level = $this->level;

        if($level == 'main_office'){
            return 1;
        }
        if($level == 'region'){
            return 2;
        }
        if($level == 'area'){
            return 3;
        }
        if($level == 'branch'){
            return 4;
        }
        if($level == 'unit'){
            return 5;
        }
        if($level == 'cluster'){
            return 6;
        }
        if($level == 'account_officer'){
            return 6;
        }
        if($level == 'loan_officer'){
            return 7;
        }
    }

    public function getUpperOfficeIDS($insert_self=true){
        
        $parent_ids = $this->getAllParentIDS();
        if ($insert_self) {
            return array_merge($parent_ids, [$this->id]);
        }
        return $parent_ids;
    }

//    public function parMovement(){

//    }
}
