@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Translation Test Page</h1>
            
            <div class="card">
                <div class="card-header">
                    <h5>Current Language: {{ getCurrentLanguage() }} ({{ getLanguageDirection() }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Navigation Items:</h6>
                            <ul class="list-group">
                                <li class="list-group-item">{{ __t('dashboard') }}</li>
                                <li class="list-group-item">{{ __t('vehicles') }}</li>
                                <li class="list-group-item">{{ __t('owners') }}</li>
                                <li class="list-group-item">{{ __t('cash_book') }}</li>
                                <li class="list-group-item">{{ __t('journey_vouchers') }}</li>
                                <li class="list-group-item">{{ __t('reports') }}</li>
                                <li class="list-group-item">{{ __t('notifications') }}</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Common Actions:</h6>
                            <ul class="list-group">
                                <li class="list-group-item">{{ __t('add') }}</li>
                                <li class="list-group-item">{{ __t('edit') }}</li>
                                <li class="list-group-item">{{ __t('delete') }}</li>
                                <li class="list-group-item">{{ __t('view') }}</li>
                                <li class="list-group-item">{{ __t('save') }}</li>
                                <li class="list-group-item">{{ __t('cancel') }}</li>
                                <li class="list-group-item">{{ __t('search') }}</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6>Vehicle Management:</h6>
                            <ul class="list-group">
                                <li class="list-group-item">{{ __t('vehicle_registration_number') }}</li>
                                <li class="list-group-item">{{ __t('vehicle_owner') }}</li>
                                <li class="list-group-item">{{ __t('driver_name') }}</li>
                                <li class="list-group-item">{{ __t('vehicle_capacity') }}</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Journey Vouchers:</h6>
                            <ul class="list-group">
                                <li class="list-group-item">{{ __t('journey_number') }}</li>
                                <li class="list-group-item">{{ __t('loading_point') }}</li>
                                <li class="list-group-item">{{ __t('destination') }}</li>
                                <li class="list-group-item">{{ __t('freight_rate') }}</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Status Messages:</h6>
                            <div class="alert alert-success">{{ __t('success') }}</div>
                            <div class="alert alert-danger">{{ __t('error') }}</div>
                            <div class="alert alert-warning">{{ __t('warning') }}</div>
                            <div class="alert alert-info">{{ __t('info') }}</div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Currency: {{ __t('currency') }} {{ __t('currency_symbol') }}</h6>
                            <p>{{ __t('total') }}: {{ __t('currency_symbol') }}1,000.00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
