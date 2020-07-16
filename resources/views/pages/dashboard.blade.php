@extends('layouts.user')

@section('content')
<div class="content pl-32 pl-64 pr-8 mt-4" id="content-full">
    <div class="row">
        <div class="col-md-6">
            <div class="card chart-container">
                <div class="card-header">
                    
                        <div class="col-md-6" style="max-height: 200px">
                            <h5 class="chart-header">{{ \Carbon\Carbon::now()->format('F d, Y')}}</h5>
                            <h2 class="chart-title">Par Movement</h2>
                    
                        </div>
                    
                </div>
                <div class="card-body">
                    <chart-par-movement></chart-par-movement>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card chart-container">
                <div class="card-header">
                        <div class="col-md-6" style="max-height: 200px">
                            <h5 class="chart-header">{{ \Carbon\Carbon::now()->format('F d, Y')}}</h5>
                            <h2 class="chart-title">Repayment Trend</h2>
                        </div>
                    
                </div>
                <div class="card-body">
                    <chart-repayment-trend></chart-repayment-trend>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card chart-container" style="height: 450px">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="chart-header">{{ \Carbon\Carbon::now()->format('F d, Y')}}</h5>
                            <h2 class="chart-title">Disbursement Trend</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <chart-disbursement-trend></chart-disbursement-trend>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card chart-container" style="height: 550px">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="chart-header">{{ \Carbon\Carbon::now()->format('F d, Y')}}</h5>
                            <h2 class="chart-title">Resigned Vs New Loans</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <chart-client-loans-trend></chart-client-loans-trend>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card chart-container">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="chart-header">{{ \Carbon\Carbon::now()->format('F d, Y')}}</h5>
                            <h2 class="chart-title">Clients</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height:400px">
                        <chart-clients></chart-clients>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card chart-container">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="chart-header">{{ \Carbon\Carbon::now()->format('F d, Y')}}</h5>
                            <h2 class="chart-title">Summary</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height:400px">
                        <chart-summary></chart-summary>
                    </div>
                </div>
            </div>
        </div>
    </div>	
{{-- 
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5>Tables</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>ID</td>
                                <td>Body</td>
                                <td>Actions</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>
                                    <p class="title">Sample Data</p>
                                    <p class="text-body">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                </td>
                                <td class="float-right">
                                    <a href="">
                                        <i class="fas fa-2x fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div> --}}
@endsection