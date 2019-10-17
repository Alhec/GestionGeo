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
            position: relative;
            width: 100%;
            height: 2cm;
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
            width: 3.19cm;
        }
        .section{
            margin-top: 3cm;
        }
        .title{
            text-align: center;
            text-transform:uppercase;
            font-weight: bold;
            font-size: 18pt;
        }
        .article{
            text-indent: 2cm;
            margin-top: 1cm;
            text-align: justify;
            line-height: 1cm;
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
            constancia notas
        </div>
        <div class="content">
            <div class="article">
                Quien suscribe, Coordinador del Postgrado en Geoquímica de la Facultad de Ciencias,
                Universidad de Central de Venezuela, hace constar por medio de la presente que el <strong>{{$data['user_data']['level_instruction']}}.
                    {{strtoupper($data['user_data']['first_name'])}} {{strtoupper($data['user_data']['second_name'])}}
                    {{strtoupper($data['user_data']['first_surname'])}} {{strtoupper($data['user_data']['second_surname'])}}
                </strong>  , titular de la cédula de identidad
                Nº {{$data['user_data']['identification'] }}, cursó y aprobó las siguientes asignaturas, obteniendo un promedio general de {{$data['porcentual_data']['porcentual']}}
                puntos y un total de {{$data['porcentual_data']['enrolled_credits']}} créditos de las {{$data['school_program_data']['num_cu']}} unidades créditos exigidas en el programa de {{$data['school_program_data']['school_program_name']}} de este Postgrado.
            </div>
            <div class="table">
                <table border="1" >
                    <tr>
                      <th>Semestre</th>                                        
                      <th>Asignaturas</th>                  
                      <th>Calificación</th>                  
                      <th>U.C.</th>                  
                    </tr>
                    @foreach($data['historical_data'] as $schoolPeriod)
                        <tr>
                            <td rowspan="{{count($schoolPeriod['inscriptions']['enrolled_subjects'])+1}}">Sem.
                                {{\App\Services\ConstanceService::numberToMonth(intval(substr($schoolPeriod['start_date'],5,-3)))}}
                                {{substr($schoolPeriod['start_date'],0,-6)}} – {{\App\Services\ConstanceService::numberToMonth(intval(substr($schoolPeriod['end_date'],5,-3)))}}
                                {{substr($schoolPeriod['end_date'],0,-6)}}</td>
                        </tr>
                        @foreach($schoolPeriod['inscriptions']['enrolled_subjects'] as $subject)
                            <tr>
                                <td>{{$subject['data_subject']['subject']['subject_name']}}</td>
                                @if($subject['qualification']!=null)
                                    <td>{{$subject['qualification']}}.</td>
                                @else
                                    <td>SC.</td>
                                @endif
                                <td>{{$subject['data_subject']['subject']['uc']}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            </div>
            <div class="article">
                Constancia que se expide a petición de la parte interesada, en Caracas, en el mes de  {{$data['month']}}  de  {{$data['year']}}.
            </div>
        </div>
    </div>
    <div class="section">
       
        <div class="coordinador">

            {{$data['coordinator_data']['level_instruction']}}. {{$data['coordinator_data']['first_name']}} {{$data['coordinator_data']['second_name']}}
            {{$data['coordinator_data']['first_surname']}} {{$data['coordinator_data']['second_surname']}} <br>
            Coordinador del Postgrado <br>
            En Geoquímica
        </div>
    </div>
    <div class="footer">
            Instituto de Ciencias de la Tierra. Fac. Ciencias UCV. Av. Los Ilustres, Los Chaguaramos. <br/>
            Apartado: 3895. Caracas-1010A. Telf: 58-0212-6051082. 
    </div>
</body>
</html>
