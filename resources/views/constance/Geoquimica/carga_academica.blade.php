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
            text-align: center;
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
        <div class="article" style="padding-top: 1.5cm">
            El Coordinador del Postgrado en Geoquímica adscrito a la Dirección de Postgrado de la Facultad de Ciencias
            – Facultad de Ingeniería de la Universidad Central de Venezuela, certifica que el
            <strong>{{$data['user_data']['user']['level_instruction']}}.
                {{strtoupper($data['user_data']['user']['first_surname'])}}
                {{strtoupper($data['user_data']['user']['second_surname'])}}
                {{strtoupper($data['user_data']['user']['first_name'])}}
                {{strtoupper($data['user_data']['user']['second_name'])}}
            </strong>, titular de la Cédula de Identidad N° <strong>{{$data['user_data']['user']['identification']}}
            </strong> cursó y aprobó las asignaturas del Programa de Estudios de
            {{strtoupper($data['school_program_data']['school_program_name'])}}. A continuación se dá una descripción de
            las asignaturas cursadas indicando el número de horas por semana y las unidades de la misma T: Teoría, P:
            Práctica L: Laboratorio y U: Unidades de Créditos.
        </div>
        <div class="title">
            CARGA HORARIA
        </div>
        <div class="content">
            <div class="article">
                <table>
                    <tr>
                        <th colspan="4">Código</th>
                        <th colspan="8">Asignatura</th>
                        <th colspan="2">Horas Teóricas</th>
                        <th colspan="2">Horas Prácticas</th>
                        <th colspan="2">Horas Laboratorios</th>
                        <th colspan="2">Créditos</th>
                    </tr>
                    @foreach($data['subjects_data'] as $subject)
                        <tr>
                            <th colspan="4">{{$subject['code']}}</th>
                            <th colspan="8">{{$subject['name']}}</th>
                            <th colspan="2">{{$subject['theoretical_hours']}}</th>
                            <th colspan="2">{{$subject['practical_hours']}}</th>
                            <th colspan="2">{{$subject['laboratory_hours']}}</th>
                            <th colspan="2">{{$subject['uc']}}</th>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="18">Total de creditos </th>
                        <th colspan="2">{{$data['total_credits']}}</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="section">
        <div class="coordinador">
            {{$data['coordinator_data']['level_instruction']}}. {{$data['coordinator_data']['first_name']}}
            {{$data['coordinator_data']['second_name']}} {{$data['coordinator_data']['first_surname']}}
            {{$data['coordinator_data']['second_surname']}}
            <br>
            Coordinador del Postgrado en Geoquímica
        </div>
    </div>
    <div class="section">
        <div class="article">
            UNIVERSIDAD CENTRAL DE VENEZUELA. FACULTAD DE CIENCIAS. Caracas, {{$data['day']}} de {{$data['month']}} de
            {{$data['year']}}. Años 197° Y 149°. Dr. Ventura Echandía L., Decano de la Facultad de Ciencias de la
            Universidad Central de Venezuela, certifica que la firma que antecede es auténtica de puño y letra del/la
            Ciudadano/a {{$data['coordinator_data']['level_instruction']}}. {{$data['coordinator_data']['first_name']}}
            {{$data['coordinator_data']['first_surname']}}, quien en la actualidad es como se titula Coordinador del
            Postgrado en Geoquímica adscrito a la Dirección de Postgrado de la Facultad de Ciencias – Facultad de
            Ingeniería de la Universidad Central de Venezuela.
        </div>
    </div>
    <div class="section">
        <div class="article">
            Dr. Ventura Echandía
            <br>
            Decano
        </div>
    </div>
</body>
</html>
