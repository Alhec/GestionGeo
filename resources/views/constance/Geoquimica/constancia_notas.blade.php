<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            margin: 3.5cm 1cm 1cm 1cm;
            font-size: 11pt;
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
            margin-top: 1cm;
        }
        .title{
            text-align: center;
            text-transform:uppercase;
            font-weight: bold;
            font-size: 18pt;
            padding-top: 1.5cm;
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
            font-size: 9pt;
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
            border: 1px solid black;
            padding: 1px 1px 1px 1px;
        }
        table tr td, table tr th{
            text-align: center;
            font-size: 6pt;
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
    <div  class="section">
        <div class="title">
            CONSTANCIA DE NOTAS
        </div>
        <div class="article">
            Quien suscribe,
            @if($data['coordinator_data']['sex']=='M')
                Coordinador
            @else
                Coordinadora
            @endif del Postgrado en Geoquímica de la Facultad de Ciencias, Universidad Central de
            Venezuela, hace constar por medio de la presente que el <strong>
                {{$data['user_data']['user']['level_instruction']}}.
                {{strtoupper($data['user_data']['user']['first_name'])}}
                {{strtoupper($data['user_data']['user']['second_name'])}}
                {{strtoupper($data['user_data']['user']['first_surname'])}}
                {{strtoupper($data['user_data']['user']['second_surname'])}}
            </strong>, titular de la cédula de identidad <strong>N°
                {{$data['user_data']['user']['identification']}} </strong>, cursó y aprobó las siguientes asignaturas,
            obteniendo un promedio general de {{$data['percentage_data']['percentage']}} puntos y un total de
            {{$data['percentage_data']['enrolled_credits']}} créditos en el programa de
            {{strtoupper($data['school_program_data']['school_program_name'])}}, de este Postgrado.
        </div>
        <div>
            <div class="article">
                <table>
                    <tr>
                        <th colspan="4">SEMESTRE</th>
                        <th colspan="8">ASIGNATURA</th>
                        <th colspan="4">CALIFICACIÓN</th>
                        <th colspan="2">U.C.</th>
                    </tr>
                    @foreach($data['enrolled_subjects'] as $schoolPeriod)
                        @foreach($schoolPeriod['enrolled_subjects'] as $subject)
                            @if($loop->first)
                                <tr>
                                    <td colspan="4" rowspan="{{$schoolPeriod['cant_subjects']}}">
                                        {{$schoolPeriod['school_period']['cod_school_period']}}</td>
                                    <td colspan="8">{{$subject['data_subject']['subject']['name']}}</td>
                                    @if($subject['status']=='APR')
                                        <td colspan="4">{{$subject['qualification']}}</td>
                                        <td colspan="2">{{$subject['data_subject']['subject']['uc']}}</td>
                                    @elseif($subject['status']=='RET')
                                        <td colspan="4">RET</td>
                                        <td colspan="2"> - </td>
                                    @else
                                        <td colspan="4">CUR</td>
                                        <td colspan="2"> - </td>
                                    @endif
                                </tr>
                            @else
                                <tr>
                                    <td colspan="8">{{$subject['data_subject']['subject']['name']}}</td>
                                    @if($subject['status']=='APR')
                                        <td colspan="4">{{$subject['qualification']}}</td>
                                        <td colspan="2">{{$subject['data_subject']['subject']['uc']}}</td>
                                    @elseif($subject['status']=='RET')
                                        <td colspan="4">RET</td>
                                        <td colspan="2"> - </td>
                                    @else
                                        <td colspan="4">CUR</td>
                                        <td colspan="2"> - </td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                        @foreach($schoolPeriod['final_work_data'] as $finalWork)
                            @if($finalWork['status']==='APPROVED')
                                @if(count($schoolPeriod['enrolled_subjects'])<1)
                                    <tr>
                                        <td colspan="4" rowspan="{{$schoolPeriod['cant_subjects']}}">
                                            {{$schoolPeriod['school_period']['cod_school_period']}}</td>
                                        <td colspan="8">{{$finalWork['final_work']['title']}}</td>
                                        <td colspan="4">Aprobado</td>
                                        <td colspan="2">-</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="8">{{$finalWork['final_work']['title']}}</td>
                                        <td colspan="4">Aprobado</td>
                                        <td colspan="2">-</td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                    @endforeach
                </table>
            </div>
            <div class="article">
                Constancia que se expide a petición de la parte interesada, en Caracas, el {{$data['day']}} de
                {{$data['month']}} de {{$data['year']}}.
            </div>
        </div>
    </div>
    <div class="section">
        <div class="coordinator">
            {{$data['coordinator_data']['level_instruction']}}. {{$data['coordinator_data']['first_name']}}
            {{$data['coordinator_data']['second_name']}}
            {{$data['coordinator_data']['first_surname']}} {{$data['coordinator_data']['second_surname']}}
            <br>
            @if($data['coordinator_data']['sex']=='M')
                Coordinador
            @else
                Coordinadora
            @endif del Postgrado
            <br>
            En Geoquímica
        </div>
    </div>
</body>
</html>
