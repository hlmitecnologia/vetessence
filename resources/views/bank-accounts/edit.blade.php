@php $title = 'Editar Conta Bancária'; @endphp
@extends('layouts.adminlte')
@section('content')
@include('bank-accounts.form', ['bankAccount' => $bankAccount])
@endsection
