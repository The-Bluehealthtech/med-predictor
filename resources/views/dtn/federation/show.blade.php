@extends('layouts.app')

@section('title', 'Espace fédération — ' . trim(($selection->player->first_name ?? '') . ' ' . ($selection->player->last_name ?? '')))

@section('content')
@include('dtn.partials.selection-detail', ['space' => 'federation'])
@endsection
