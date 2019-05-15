@extends('admin::layouts.base')

@section('content')
    <router-view name="header"></router-view>
    <router-view name="sidebar"></router-view>
    <div id="content">
        <router-view></router-view>
    </div>
    <popups></popups>
@endsection

@push('css')
    <!-- Include external CSS. -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.css">

    <!-- Include Editor style. -->
    <link href="https://cdn.jsdelivr.net/npm/froala-editor@2.9.3/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/froala-editor@2.9.3/css/froala_style.min.css" rel="stylesheet" type="text/css" />

    <link href='https://cdn.jsdelivr.net/npm/froala-editor@2.9.3/css/third_party/spell_checker.min.css' rel="stylesheet" type="text/css" />
    <link href='https://cdn.jsdelivr.net/npm/froala-editor@2.9.3/css/plugins/image.min.css' rel="stylesheet" type="text/css" />
@endpush

@push('js')
    <!-- Include external JS libs. -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/mode/xml/xml.min.js"></script>

    <!-- Include Editor JS files. -->
    <script type="text/javascript" src="/admin-assets/js/froala/froala_editor.pkgd.min.js"></script>

    <script src='/admin-assets/js/froala/languages/ru.js'></script>
    <script src='/admin-assets/js/froala/third_party/spell_checker.min.js'></script>
    <script src='/admin-assets/js/froala/plugins/image.min.js'></script>
@endpush