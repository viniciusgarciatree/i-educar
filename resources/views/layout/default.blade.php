<!DOCTYPE html>
<html lang="pt" class="no-js">
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">

    <title>i-Educar @if(isset($title)) - {!! $title !!} @endif</title>

    <script>
        dataLayer = [{
            'slug': '{{$config['app']['database']['dbname']}}',
            'user_id': '{{$loggedUser->personId}}',
            'user_name': '{{$loggedUser->name}}',
            'user_email': '{{$loggedUser->email}}',
            'user_role': '{{$loggedUser->role}}',
            'user_created_at': parseInt('{{$loggedUser->created_at}}', 10),
            'institution': '{{ $loggedUser->institution }}',
            'city': '{{ $loggedUser->city }}',
            'state': '{{ $loggedUser->state }}',
            'students_count': '{{ $loggedUser->students_count }}',
            'teachers_count': '{{ $loggedUser->teachers_count }}',
            'classes_count': '{{ $loggedUser->classes_count }}',
        }];
    </script>

    @if(!empty($config['app']['gtm']['id']))
        <!-- Google Tag Manager -->
        <script>
            (function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ $config['app']['gtm']['id'] }}');
        </script>
        <!-- End Google Tag Manager -->
    @endif

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/main.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/novo.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/font-awesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/min-portabilis.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/mytdt.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/jquery.modal.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/localizacaoSistema.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/flash-messages.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get("/intranet/scripts/select2/select2.min.css") }}">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
    <link rel="stylesheet" type='text/css' href='{{ Asset::get('css/base.css') }}'>
    <link rel="stylesheet" type="text/css" href='{{ Asset::get('/intranet/scripts/jquery/jquery-ui.min-1.9.2/css/custom/jquery-ui-1.9.2.custom.min.css') }}'>

    @stack('styles')

    <script>
        (function (e, t, n) {
            var r = e.querySelectorAll("html")[0];
            r.className = r.className.replace(/(^|\s)no-js(\s|$)/, "$1js$2")
        })(document, window, 0);
    </script>

    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true" charset="utf-8"></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/padrao.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/novo.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/dom.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/menu.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/ied/forms.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/ied/phpjs.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/jquery/jquery-1.8.3.min.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/jquery/jquery.modal.min.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/prototype/prototype-1.7.1.0.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/scriptaculous/effects.js") }} "></script>
    <script type="text/javascript" src="{{ Asset::get("/intranet/scripts/jquery.mask.min.js") }} "></script>
    <script type='text/javascript' src='{{ Asset::get('/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/SimpleSearch.js') }}'></script>
    <script type='text/javascript' src='{{ Asset::get('/modules/Portabilis/Assets/Javascripts/Utils.js') }}'></script>
    <script type='text/javascript' src='{{ Asset::get('/intranet/scripts/jquery/jquery-ui.min-1.9.2/js/jquery-ui-1.9.2.custom.min.js') }}'></script>

    <script type="text/javascript">
        window.ambiente = 'development';

        var running = false;
        var altura = null;

        function changeImage(div_id) {
            var id = /[0-9]+/.exec(div_id.element.id);
            var imagem = $('seta_' + id);
            var src = imagem.src.indexOf('arrow-up');

            imagem.src = (src != -1)
                ? 'imagens/arrow-down2.png'
                : 'imagens/arrow-up2.png';

            imagem.title = (src != -1)
                ? imagem.title.replace('Abrir', 'Fechar')
                : imagem.title.replace('Fechar', 'Abrir');

            if (src != -1) {
                setCookie('menu_' + id, 'I', 30);
            } else {
                setCookie('menu_' + id, 'V', 30);
            }

            running = false;

            $('tablenum1').style.height = $('tablenum1').offsetHeight - altura;
        }

        function teste(div_id) {
            altura = div_id.element.offsetHeight;
        }

        function toggleMenu(div_id) {
            if (running) {
                return;
            }

            var src = $('link1_' + div_id).title.indexOf('Abrir');

            $('link1_' + div_id).title = (src != -1)
                ? $('link1_' + div_id).title.replace('Abrir', 'Fechar')
                : $('link1_' + div_id).title.replace('Fechar', 'Abrir');

            $('link2_' + div_id).title = (src != -1)
                ? $('link2_' + div_id).title.replace('Abrir', 'Fechar')
                : $('link2_' + div_id).title.replace('Fechar', 'Abrir');

            running = true;

            new Effect.toggle($('div_' + div_id), 'slide', {
                afterFinish: changeImage,
                duration: 0.3,
                beforeStart: teste
            });
        }
    </script>

    <!-- #&SCRIPT&# -->
</head>
<body>

@if(!empty($config['app']['gtm']['id']))
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{$config['app']['gtm']['id']}}" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
@endif

<div id="DOM_expansivel" class="DOM_expansivel"></div>

<table summary="" class='tabelanum1' id="tablenum1" border='0' cellspacing='0' cellpadding='0'>
    <tr id="topo" class="topo">
        <td colspan="2">
            <header class="ieducar-header">
                <div class="ieducar-header-logo">
                    <h1><a href="{{ url('/') }}">i-Educar</a></h1>
                </div>
                <div class="ieducar-header-links">
                    <div class="dropdown">
                        <div class="dropbtn">{{ $loggedUser->name }}</div>
                        <div class="dropdown-content">
                            <a href="{{ url('intranet/agenda.php') }}">Agenda</a>
                            <a href="{{ url('intranet/index.php') }}">Calendário</a>
                            <a href="{{ url('intranet/meusdados.php') }}">Meus dados</a>
                            <a href="{{ url('intranet/logof.php') }}" id="logout">Sair</a>
                        </div>
                    </div>
                    <a href="{{ url('intranet/meusdados.php') }}" class="avatar" title="Meus dados">
                        <img height="35" src="{{ url('intranet/imagens/user-perfil.png') }}" alt="Perfil">
                    </a>
                    <a href="#" class="notifications">
                        <img alt="Notificação" id="notificacao" src="{{ url('intranet/imagens/icon-nav-notifications.png') }}">
                    </a>
                </div>
            </header>
        </td>
    </tr>

    <tr>
        <td colspan="3">
            <table summary="" class='tabelanum2' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                    <td id="menu_lateral" class="r3c1" width='170'>
                        @include('layout.menu')
                    </td>

                    <td valign=top>
                        <table summary="" class='tabelanum2' border='0' cellspacing='0' cellpadding='0'>
                            <tr>
                                <td>
                                    @include('layout.topmenu')
                                </td>
                            </tr>

                            <tr>
                                <td height="100%" valign="top" id="corpo">
                                    <table class='tablelistagem' width='100%' border='0' cellpadding='0' cellspacing='0'>
                                        <tr height='10px'>
                                            <td class='fundoLocalizacao' colspan='2'>
                                                @include('layout.breadcrumb')
                                            </td>
                                        </tr>
                                    </table>

                                    @yield('content')
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr class="rodape">
        <td colspan="3">
            <center>
                @include('layout.footer')
            </center>
        </td>
    </tr>
</table>

@include('partials.flash-message')

<script type="text/javascript">
    function go(url) {
        document.location = url;
    }

    var goodIE = (document.all) ? 1 : 0;
    var netscape6 = (document.getElementById && !document.all) ? 1 : 0;
    var aux = '';
    var aberto = false;

    function AdicionaItem(chave, item, nome_pai, submete) {
        var x = document.getElementById(nome_pai);

        opt = document.createElement('OPTION');
        opt.value = chave;
        opt.selected = true;
        opt.appendChild(document.createTextNode(item));

        x.appendChild(opt);
        if (submete) {

            document.formcadastro.submit();
        }
    }

    function go(url) {
        document.location = url;
    }

    function excluir() {
        document.formcadastro.reset();

        if (confirm('Excluir registro?')) {
            document.formcadastro.tipoacao.value = 'Excluir';
            document.formcadastro.submit();
        }
    }

    function ExcluirImg() {
        document.formcadastro.reset();
        if (confirm('Excluir imagem?')) {
            document.formcadastro.tipoacao.value = 'ExcluirImg';
            document.formcadastro.submit();
        }
    }
</script>

<script type='text/javascript' src='{{ Asset::get('/modules/Portabilis/Assets/Javascripts/Utils.js') }}'></script>

<script type='text/javascript'>(function ($) {
    $(document).ready(function () {
            fixupFieldsWidth();
        });
    })(jQuery);
</script>

<script type="text/javascript" src="{{ Asset::get("/intranet/scripts/custom-file-input.js") }}"></script>
<script type="text/javascript" src="{{ Asset::get("/intranet/scripts/select2/select2.full.min.js") }}"></script>
<script type="text/javascript" src="{{ Asset::get("/intranet/scripts/select2/pt-BR.js") }}"></script>
<script type="text/javascript" src="{{ Asset::get("/intranet/scripts/flash-messages.js") }}"></script>

@include('layout.vue')

@stack('scripts')

@stack('end')

</body>
</html>
