<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            margin: 3.5cm 1cm 0.5cm 1cm;
            font-size: 12pt;
        }
        .header{
            position: fixed;
            width: 100%;
            height: 3cm;
            top: 0cm;
            left: 0cm;
            right: 0cm;
        }
        .image-geoquimica{
            position: absolute;
            left: 0;
        }
        .image-ucv{
            position: absolute;
            right: 0;
        }
        .image-geoquimica img{
            width: 7.5cm;
            height: 3cm;
        }
        .image-ucv img{
            width: 3cm;
        }
        .section{
            margin-top: 2cm;
        }
        .title{
            text-align: center;
            text-transform:uppercase;
            font-weight: bold;
            font-size: 18pt;
            padding-top: 0.5cm;
        }
        .article{
            text-indent: 0.5cm;
            margin-top: 0.5cm;
            text-align: justify;
            line-height: 0.5cm;
            clear: both;
        }
        .footer{
            width: 100%;
            text-align: center;
            position: fixed;
            left: 0;
            bottom: 0;
            color: #1A3E86;
            font-weight: bold;
            font-size: 10pt;
        }
        .coordinator{
            text-align: center;
            font-weight: bold;
            font-style: italic;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            font-size: 10pt;
            border: 1px solid black;
        }
        table tr td{
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="image-geoquimica">
            <img src="{{ public_path() ."/images/icon-banner.png" }}">
        </div>
        <div class="image-ucv">
            <img src="{{ public_path() ."/images/logo-ucv.png" }}">
        </div>
    </header>
    <footer class="footer">
        Instituto de Ciencias de la Tierra. Fac. Ciencias UCV. Av. Los Ilustres, Los Chaguaramos. <br/>
        Apartado: 3895. Caracas-1010A. Telf: 58-0212-6051082.
    </footer>
    <main>
        <div  class="section">
            <div class="title">
                CONSTANCIA
            </div>
            <div class="article">
                Por medio de la presente hago constar que
                @if($data['user_data']['sex']=='M')
                    el
                @else
                    la
                @endif <strong>{{$data['user_data']['level_instruction']}}.
                    {{strtoupper($data['user_data']['first_name'])}} {{strtoupper($data['user_data']['second_name'])}}
                    {{strtoupper($data['user_data']['first_surname'])}} {{strtoupper($data['user_data']['second_surname'])}}
                </strong> , titular de la cedula de identidad  <strong>N° {{$data['user_data']['identification']}} </strong>,
                ha dictado en el Postgrado en Geoquímica como <strong> Profesor
                    @switch($data['user_data']['teacher']['dedication'])
                        @case('INV')
                        Invitado
                        @break
                        @case('MT')
                        Medio Tiempo
                        @break
                        @case('CON')
                        Contratado
                        @break
                        @case('TC')
                        Tiempo Completo
                        @break
                        @case('EXC')
                        Exclusivo
                        @break
                        @default
                        Contratado
                    @endswitch
                </strong>
                las siguientes asignaturas.
            </div>
            <div>
                <div class="article">
                    <table>
                        <tr>
                            <th colspan="3">SEMESTRE</th>
                            <th colspan="7">ASIGNATURA</th>
                        </tr>
                        @foreach($data['historical_data'] as $schoolPeriod)
                            @foreach($schoolPeriod['subjects'] as $subject)
                                @if ($loop->first)
                                    <tr>
                                        <td colspan="3" rowspan="{{(count($schoolPeriod['subjects']))}}">
                                            {{$schoolPeriod['cod_school_period']}}</td>
                                        <td colspan="7">{{$subject['subject']['name']}}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="7">{{$subject['subject']['name']}}</td>
                                    </tr>
                                @endif
                            @endforeach
                                @foreach($schoolPeriod['subjects'] as $subject)
                                    @if ($loop->first)
                                        <tr>
                                            <td colspan="3" rowspan="{{(count($schoolPeriod['subjects']))}}">
                                                {{$schoolPeriod['cod_school_period']}}</td>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach($schoolPeriod['subjects'] as $subject)
                                    @if ($loop->first)
                                        <tr>
                                            <td colspan="3" rowspan="{{(count($schoolPeriod['subjects']))}}">
                                                {{$schoolPeriod['cod_school_period']}}</td>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach($schoolPeriod['subjects'] as $subject)
                                    @if ($loop->first)
                                        <tr>
                                            <td colspan="3" rowspan="{{(count($schoolPeriod['subjects']))}}">
                                                {{$schoolPeriod['cod_school_period']}}</td>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach($schoolPeriod['subjects'] as $subject)
                                    @if ($loop->first)
                                        <tr>
                                            <td colspan="3" rowspan="{{(count($schoolPeriod['subjects']))}}">
                                                {{$schoolPeriod['cod_school_period']}}</td>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach($schoolPeriod['subjects'] as $subject)
                                    @if ($loop->first)
                                        <tr>
                                            <td colspan="3" rowspan="{{(count($schoolPeriod['subjects']))}}">
                                                {{$schoolPeriod['cod_school_period']}}</td>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach($schoolPeriod['subjects'] as $subject)
                                    @if ($loop->first)
                                        <tr>
                                            <td colspan="3" rowspan="{{(count($schoolPeriod['subjects']))}}">
                                                {{$schoolPeriod['cod_school_period']}}</td>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach($schoolPeriod['subjects'] as $subject)
                                    @if ($loop->first)
                                        <tr>
                                            <td colspan="3" rowspan="{{(count($schoolPeriod['subjects']))}}">
                                                {{$schoolPeriod['cod_school_period']}}</td>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach($schoolPeriod['subjects'] as $subject)
                                    @if ($loop->first)
                                        <tr>
                                            <td colspan="3" rowspan="{{(count($schoolPeriod['subjects']))}}">
                                                {{$schoolPeriod['cod_school_period']}}</td>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="7">{{$subject['subject']['name']}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                        @endforeach
                    </table>
                </div>
                <div class="article">
                    Constancia que se expide a petición de la parte interesada, en Caracas, el {{$data['day']}} del mes
                    de {{$data['month']}} de {{$data['year']}}.
                </div>
            </div>
        </div>
        <div class="section">
            <br>
            <br>
            <div class="coordinator">
                {{$data['coordinator_data']['level_instruction']}}. {{$data['coordinator_data']['first_name']}}
                {{$data['coordinator_data']['second_name']}} {{$data['coordinator_data']['first_surname']}}
                {{$data['coordinator_data']['second_surname']}} <br>
                @if($data['coordinator_data']['sex']=='M')
                    Coordinador
                @else
                    Coordinadora
                @endif del Postgrado <br>
                En Geoquímica
            </div>
        </div>
        </div>
    </main>
</body>
</html>
