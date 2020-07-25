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
            margin-top: 1.5cm;
        }
        .section-dean{
            width: 100%;
            text-align: center;
        }
        .title{
            text-align: center;
            text-transform:uppercase;
            font-size: 15pt;
            padding-top: 0.5cm;
            margin-bottom: 0.5cm;
        }
        .article{
            text-indent: 0.5cm;
            margin-top: 1cm;
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
            border: 1px solid black;
            font-size: 10pt;
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
            <div class="article">
                @if($data['coordinator_data']['sex']=='M')
                    El Coordinador
                @else
                    La Coordinadora
                @endif del Postgrado en Geoquímica adscrito a la Dirección de Postgrado de la Facultad de Ciencias
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
            <div>
                <table>
                    <tr>
                        <th colspan="4">Código</th>
                        <th colspan="8">Asignatura</th>
                        <th style="text-align: center" colspan="2">Horas Teóricas</th>
                        <th style="text-align: center" colspan="2">Horas Prácticas</th>
                        <th style="text-align: center" colspan="2">Horas Laboratorios</th>
                        <th style="text-align: center" colspan="2">Créditos</th>
                    </tr>
                    @foreach($data['subjects_data'] as $subject)
                        <tr>
                            <th colspan="4">{{$subject['code']}}</th>
                            <th colspan="8">{{$subject['name']}}</th>
                            <th style="text-align: center" colspan="2">{{$subject['theoretical_hours']}}</th>
                            <th style="text-align: center" colspan="2">{{$subject['practical_hours']}}</th>
                            <th style="text-align: center" colspan="2">{{$subject['laboratory_hours']}}</th>
                            <th style="text-align: center" colspan="2">{{$subject['uc']}}</th>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="18">Total de creditos </th>
                        <th style="text-align: center" colspan="2">{{$data['total_credits']}}</th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="section">
            <div class="coordinator">
                {{$data['coordinator_data']['level_instruction']}}. {{$data['coordinator_data']['first_name']}}
                {{$data['coordinator_data']['second_name']}} {{$data['coordinator_data']['first_surname']}}
                {{$data['coordinator_data']['second_surname']}}
                <br>
                Coordinador del Postgrado en Geoquímica
            </div>
        </div>
        <div class="">
            <div class="article">
                UNIVERSIDAD CENTRAL DE VENEZUELA. FACULTAD DE CIENCIAS. Caracas, {{$data['day']}} de {{$data['month']}} de
                {{$data['year']}}. Años 197° Y 149°. Dr. Ventura Echandía L., Decano de la Facultad de Ciencias de la
                Universidad Central de Venezuela, certifica que la firma que antecede es auténtica de puño y letra del/la
                @if($data['coordinator_data']['sex']=='M')
                    Ciudadano
                @else
                    Ciudadana
                @endif
                {{$data['coordinator_data']['level_instruction']}}. {{$data['coordinator_data']['first_name']}}
                {{$data['coordinator_data']['first_surname']}}, quien en la actualidad es como se titula
                @if($data['coordinator_data']['sex']=='M')
                    Coordinador
                @else
                    Coordinadora
                @endif del
                Postgrado en Geoquímica adscrito a la Dirección de Postgrado de la Facultad de Ciencias – Facultad de
                Ingeniería de la Universidad Central de Venezuela.
            </div>

        </div>
        <div class="section">
            <div class="coordinator">
                Dr. Ventura Echandía
                <br>
                Decano
            </div>
        </div>
    </main>
</body>
</html>
