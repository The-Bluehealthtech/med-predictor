@extends('layouts.app')

@section('title', 'Sélections du club — ' . trim(($selection->player->first_name ?? '') . ' ' . ($selection->player->last_name ?? '')))

@section('content')
@include('dtn.partials.selection-detail', ['space' => 'club'])
@endsection
