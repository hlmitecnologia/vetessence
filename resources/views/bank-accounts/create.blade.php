@php $title = 'Nova Conta Bancária'; @endphp
@extends('layouts.adminlte')
@section('content')
@include('bank-accounts.form', ['bankAccount' => null])
@endsection
