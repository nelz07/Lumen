<template>
<div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">                
                <div class="card-header">   
                    <h3 class="h3"> Deposit Reports</h3>    

                    <div class="row">
                        <div class="co-lg-4 px-3">
                            <label for="" style="color:white" class="lead mr-2">Branch:</label>
                            <v2-select @officeSelected="assignOffice" style="width:500px;" ></v2-select>
                        </div>
                        <div class="col-lg-3">
                            <label for="" style="color:white" class="lead">Deposit</label>
                            <products @productSelected="depositProductSelected" list="deposit" status="1" multi_values="true"></products>
                        </div>
                        <div class="col-lg-3">
                            <label for="date" style="color:white" class="lead mr-2"> Deposit Type:</label>
                            <transaction-method @transactionSelected="transactionSelected" :allow_multiple="true" type="deposit"></transaction-method>
                        </div>
                        <div class="col-lg-2">
                             <button class="btn btn-primary" @click="download" v-if="exportable">Export Report</button>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-4">
                            <label for="date" style="color:white" class="lead mr-2"> Transaction By: </label>
                            <user-list @userSelected="userSelected" :multiple="true" class="w-100"></user-list>
                        </div>
                        <div class="col-lg-3">
                            <label for="date" style="color:white" class="lead"> From:</label>
                            <input type="date" class="form-control" :class="hasErrors('from_date') ? 'is-invalid' : ''" v-model="request.from_date">
                            <div class="invalid-feedback" v-if="hasErrors('from_date')">
                                {{ errors.from_date[0]}}
                            </div>
                        </div>
                        
                        <div class="col-lg-3">
                            <label for="date" style="color:white" class="lead mr-2"> To:</label>
                            <input type="date" class="form-control" :class="hasErrors('to_date') ? 'is-invalid' : ''" v-model="request.to_date">
                            <div class="invalid-feedback" v-if="hasErrors('to_date')">
                                {{ errors.to_date[0]}}
                            </div>
                        </div>
                    </div>    

                    <div class="row mt-4">
                        <div class="col-lg-2">
                            <label for="date" style="color:white" class="lead mr-2">Amount from:</label>
                            <input type="number" class="form-control" v-model="request.amount_from">
                        </div>
                        <div class="col-lg-2">
                            <label for="date" style="color:white" class="lead mr-2">Amount to:</label>
                            <input type="number" class="form-control" v-model="request.amount_to">
                        </div>
                    </div>
                    <button class="btn btn-primary mt-4" @click="search">Search</button>
                   

                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <template v-if="report_class=='detailed'">
                            <tr>
                                <td><p class="title">Office Level</p></td>
                                <td><p class="title">Name</p></td>
                                <td><p class="title">Type</p></td>
                                <td><p class="title">Transaction</p></td>
                                <td><p class="title"> Amount</p> </td>
                                <td><p class="title"> Balance</p> </td>
                                <td><p class="title"> Payment Method </p></td>
                                <td><p class="title">Transaction Date</p></td>
                            </tr>
                            </template>
                            <template v-if="report_class=='summary'">
                            <tr>
                                <td><p class="title">Office Level</p></td>
                                <td><p class="title">Number of Transactions</p></td>
                                <td><p class="title"> Transaction Type</p> </td>
                                <td><p class="title"> Deposit Type</p> </td>
                                <td><p class="title"> Amount</p> </td>
                                <td><p class="title"> Balance</p></td>
                            </tr>
                            </template>
                        </thead>
                        <tbody>
                             <template v-if="report_class=='detailed' && hasRecords">
                                <tr v-for="(item,key) in list.data" :key="key">
                                    <td>{{item.office_name}}</td>
                                    <td><a :href="clientLink(item.client_id)">{{item.client_name}}</a></td>
                                    <td><a :href="depositLink(item.client_id,item.deposit_account_id)">{{item.deposit_type}}</a></td>
                                    <td>{{item.transaction_type}}</td>
                                    <td>{{moneyFormat(item.amount)}}</td>
                                    <td>{{moneyFormat(item.balance)}}</td>
                                    <td>{{(item.payment_method_name)}}</td>
                                    <td>{{moment(item.transaction_date,'MMMM D, YYYY')}}</td>
                                </tr>
                             </template>
                            <template v-if="report_class=='summary' && hasRecords">
                            <tr v-for="(item,key) in list.data" :key="key">
                                <td><p class="title">{{item.office_name}}</p></td>
                                <td><p class="title">{{item.number_of_transactions}}</p></td>
                                <td><p class="title">{{(item.transaction_type)}}</p></td>
                                <td><p class="title">{{(item.deposit_type)}}</p></td>
                                <td><p class="title">{{moneyFormat(item.amount)}}</p></td>
                                <td><p class="title">{{moneyFormat(item.balance)}}</p></td>
                            </tr>
                            </template>
                            
                             <template v-if="report_class=='detailed' && hasRecords">
                                <tr style="border-style:none;">
                                    <td><p class="title"></p></td>
                                    <td><p class="title"></p></td>
                                    <td><p class="title"></p></td>
                                    <td><p class="title text-right"># of Transactions: </p></td>
                                    <td><p class="title text-center">{{(summary.number_of_transactions)}}</p></td>
                                    <!-- <td><p class="title">{{moneyFormat(summary.total_amount)}}</p></td>
                                    <td><p class="title">{{moneyFormat(summary.balance)}}</p></td> -->
                                    <td><p class="title"></p></td>
                                    <td><p class="title"></p></td>
                                    <td><p class="title"></p></td>
                                    <td><p class="title"></p></td>
                                </tr>
                            </template>
                            <template v-if="report_class=='summary' && hasRecords">
                                <tr style="border-style:none;">
                                    <td><p class="title"></p></td>
                                    <td><p class="title"></p></td>
                                    <td><p class="title"></p></td>
                                    <td><p class="title text-center"># of Transactions: </p></td>
                                    <td><p class="title">{{(summary.number_of_transactions)}}</p></td>
                                </tr>
                            </template>
                            
                        </tbody>
                    </table>
                    <p style="color:white" v-if="hasRecords">Showing Records: {{this.list.from.toLocaleString()}} - {{this.list.to.toLocaleString()}}  of {{this.list.total.toLocaleString()}}</p>

                    <paginator :dataset="list" @pageSelected="pageSelected"></paginator>
                </div>
            </div>
            
        </div>
    </div>
 <loading :is-full-page="true" :active.sync="isLoading" ></loading>
</div>
</template>

<script>
import MultiSearchComponent from './../MultiSearchComponent';
import _ from 'lodash'
import Paginator from './../PaginatorComponent';
import Loading from 'vue-loading-overlay';
export default {
    props : ['report_class'],
    components : {
        Loading
    },
    data(){
        return {
            errors:{},
            request : {
                is_summarized : false,
                office_id:null,
                from_date: null,
                to_date:null,
                transaction_by: [],
                transaction_type : [],
                amount_from: null,
                amount_to: null,
                page: 1,
                per_page: 20,
                reverted: false,
                deposit_ids: [],
            },
            list : [],
            isLoading : false,
            exportable : false,
            summary : [],
            url : '/wApi/reports/deposit-transactions'

        }
    },
    mounted(){
        if(this.report_class =='detailed'){
            this.request.is_summarized  = false
        }else{
            this.request.is_summarized = true
        }
    },
    methods : {
        hasErrors(field){
            return this.errors.hasOwnProperty(field)
        },
        moneyFormat(value){
            return moneyFormat(value);
        },
        moment(value, format='MMMM D, YYYY, h:mm:ss a'){
            return moment(value).format(format)
        },
        assignOffice(value){
            this.request.office_id = value['id'];
        },
        pageSelected(value){
            this.request.page = value
            this.search()
        },
        search(){
            this.isLoading = true
            axios.post(this.url+'?page='+this.request.page, this.request)
                .then(res=>{
                    this.isLoading = false
                    this.list = res.data.data
                    this.summary = res.data.summary
                    this.exportable = false;
                    if(this.list.data.length > 0){
                        this.exportable = true;
                    }
                }).catch(e => {
                    
                    this.errors = e.response.data.errors
                    this.isLoading = false
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: e.message,
                    })
                })
        },
        userSelected(value){
            this.request.users = _.map(value,'id');
        },
        transactionSelected(value){
            
            this.request.transaction_type = _.map(value, 'name');
        },
        statusSelected(value){
            this.request.status = value;
        },
        depositProductSelected(value){
            this.request.deposit_ids = _.map(value,'id');
        },
        depositLink(client_id,deposit_account_id){
            return '/client/'+client_id+'/deposit/'+deposit_account_id
        },
        loanLink(client_id,loan_account_id){
            return '/client/'+client_id+'/loans/'+loan_account_id
        },
        clientLink(client_id){
            return '/client/'+client_id
        },
        download(){
            var data = Object.assign({},this.request);
            data['export'] = true;
            this.isLoading = true

            axios.post(this.url+'?page='+this.request.page, data, {
                     headers:
                        {
                            'Content-Disposition': "attachment; filename=template.xlsx",
                            'Content-Type': 'application/json',

                        },
                    responseType: 'arraybuffer',
                    }
                )
                .then(res=>{
                    this.isLoading = false

                    const url = window.URL.createObjectURL(new Blob([res.data],{type:'application/vnd.ms-excel'}));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', res.headers.filename);
                    document.body.appendChild(link);
                    link.click();
                    this.isLoading = false
                })
                .catch(err=>{
                    this.isLoading = false

                })
        }
    },
    computed : {
        hasRecords(){
            if(this.list.hasOwnProperty('data')){
                if(this.list.data.length > 0){
                    return true;
                }
            }
            return false;
        }
    },
    watch: {
        request : {
            handler(){
                this.exportable = false;
            },
            deep:true
    }
}
}

</script>