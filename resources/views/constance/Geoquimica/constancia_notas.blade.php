<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            margin: 1cm 2cm 2cm 2cm;
            font-size: 12pt;
        }
        .header{
            position: fixed;
            width: 100%;
            height: 1.5cm;
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
            width: 7.61cm;
            height: 3.61cm;
        }
        .image-ucv img{
            width: 3.49cm;
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
            font-size: 10pt;
        }
        .coordinador{
            text-align: center;
            font-weight: bold;
            font-style: italic;
        }
        table {
            border-collapse: collapse;
            margin: 0 auto;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black;
            padding: 0 0.1cm 0 0.1cm;
        }
        table tr td{
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="image-geoquimica">
            <img src="{{ public_path() ."/images/icon-banner.png" }}">
        </div>
        <div class="image-ucv">
            <img src="{{ public_path() ."/images/logo-ucv.png" }}">
        </div>
    </div>
    <div  class="section">
        <div class="title">
            CONSTANCIA DE NOTAS
        </div>
        <div class="article" style="padding-top: 1.5cm">
            Quien suscribe, Coordinador del Postgrado en Geoquímica de la Facultad de Ciencias, Universidad Central de
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
        <div class="content">
            <div class="article">
                <table>
                    <tr>
                        <th colspan="4">SEMESTRE</th>
                        <th colspan="8">ASIGNATURA</th>
                        <th colspan="4">CALIFICACIÓN</th>
                        <th colspan="2">U.C.</th>
                    </tr>
                    @foreach($data['enrolled_subjects'] as $schoolPeriod)
                        <tr>
                            <td colspan="4" rowspan="{{count($schoolPeriod['enrolled_subjects'])}}">
                                {{$schoolPeriod['school_period']['cod_school_period']}}</td>
                            @foreach($schoolPeriod['enrolled_subjects'] as $subject)
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
                            @endforeach
                        </tr>
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
        <div class="coordinador">
            {{$data['coordinator_data']['level_instruction']}}. {{$data['coordinator_data']['first_name']}}
            {{$data['coordinator_data']['second_name']}}
            {{$data['coordinator_data']['first_surname']}} {{$data['coordinator_data']['second_surname']}}
            <br>
            Coordinador del Postgrado
            <br>
            En Geoquímica
        </div>
    </div>
    <div class="footer">
        Instituto de Ciencias de la Tierra. Fac. Ciencias UCV. Av. Los Ilustres, Los Chaguaramos. <br/>
        Apartado: 3895. Caracas-1010A. Telf: 58-0212-6051082.
    </div>
    </div>
</body>
</html>
