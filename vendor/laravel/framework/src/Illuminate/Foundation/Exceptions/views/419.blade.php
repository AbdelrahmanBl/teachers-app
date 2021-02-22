@extends('errors::minimal')

<script>
    window.open( window.location.href , '_self');
</script>
@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
