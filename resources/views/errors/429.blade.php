@extends('errors::minimal')

@section('title', __('Too many requests'))
@section('code', '429')
@section('message', __('Too many requests'))
