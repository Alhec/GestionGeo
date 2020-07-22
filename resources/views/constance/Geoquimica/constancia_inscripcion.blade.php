<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            margin: 0cm 0cm 0cm 0cm;
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
        }
        .section{
            margin-top: 3cm;
        }
        .title{
            text-align: center;
            text-transform:uppercase;
            font-weight: bold;
            font-size: 14pt;
        }
        .article{
            margin-top: 1cm;
            line-height: 0.5cm;
            font-size: 10pt;
            text-align: center;
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
        .container-firma{
            display: inline-block;
            width: 6cm;
            border-top: 1px solid black;
        }
        .firma-name{
            text-align: center
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black;
            padding: 0 0 0 0;
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
            <img src="{{ public_path() ."/images/logo-ucv.png" }}" alt="">
        </div>
    </div>
    <div  class="section">
        <div class="title">
            Planilla de Inscripcion semestral {{$data['inscription']['school_period']['cod_school_period']}}
        </div>
        <div class="content">
            <div class="article">
                <table>
                    <tr>
                        <th colspan="4">APELLIDOS Y NOMBRES</th>
                        <td colspan="5">{{strtoupper($data['user_data']['user']['first_surname'])}} {{strtoupper($data['user_data']['user']['second_surname'])}}
                            {{strtoupper($data['user_data']['user']['first_name'])}} {{strtoupper($data['user_data']['user']['second_name'])}}
                            </td>
                        <th colspan="1">C.I</th>
                        <td colspan="4"> {{$data['user_data']['user']['identification'] }}</td>
                    </tr>
                     <tr>
                        <th colspan="4">TELF. HABT</th>
                        <td colspan="4">{{$data['user_data']['user']['telephone'] }}</td>
                        <th colspan="2">TELF. TRAB.</th>
                        <td colspan="4">{{$data['user_data']['user']['work_phone'] }}</td>
                    </tr>
                    <tr>
                        <th colspan="2">PROFESOR GUIA </th>
                        <td colspan="5">{{$data['user_data']['guide_teacher']['user']['level_instruction']}}.
                            {{$data['user_data']['guide_teacher']['user']['first_name']}} {{$data['user_data']['guide_teacher']['user']['first_surname']}}</td>
                        <th colspan="2">E-MAIL ESTU.</th>
                        <td colspan="5">{{$data['user_data']['user']['email'] }}</td>
                    </tr>
                     <tr>
                        <th>Doctorado</th>
                        <td>-</td>
                        <th>Maestria</th>
                        <td></td>
                        <th>Especialización</th>
                        <td>-</td>
                        <th>Ampliación</th>
                        <td>-</td>
                        <th>Nivelación</th>
                        <td>-</td>
                        <th>Oyente</th>
                        <td>-</td>
                        <th>Otro</th>
                        <td>-</td>
                    </tr>
                     <tr>
                        <th colspan="4">FECHA INICIO DEL PROGRAMA</th>
                        <td colspan="3"> {{substr($data['historical_data'][0]['inscription_date'],8)}}
                            {{substr($data['historical_data'][0]['inscription_date'],5,-3)}}
                            {{substr($data['historical_data'][0]['inscription_date'],0,-6)}}</td>
                        <th colspan="3">FECHA PROBABLE CULMINACION</th>
                        <td colspan="4">{{substr($data['historical_data'][0]['inscription_date'],8)}}
                            {{substr($data['historical_data'][0]['inscription_date'],5,-3)}}
                            {{intval(substr($data['historical_data'][0]['inscription_date'],0,-6)) +
                            $data['school_program_data']['duration']}}</td>
                    </tr>
                    <tr>
                        <th colspan="4">UNIDADES POR CONVALIDACION</th>
                        <td colspan="3">{{$data['user_data']['credits_granted']}}</td>
                        <th colspan="3">UNIDADES CURSADAS HASTA LA FECHA</th>
                        <td colspan="4">{{$data['percentage_data']['enrolled_credits']}}</td>
                    </tr>
                    @if(count($data['user_data']['degrees'])>0)
                        <tr>
                            <th colspan="14">TITULOS OBTENIDOS</th>
                        </tr>
                    @endif

                    @foreach($data['user_data']['degrees'] as $degree)
                        <tr>
                            <td colspan="14">{{$degree['degree_obtained']}} {{$degree['degree_name']}} -
                                {{$degree['university']}}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <th colspan="2">FINANCIAMIENTO:</th>
                        <td colspan="12">
                            @switch($data['inscription']['financing'])
                                @case('EXO')
                                Exonerado
                                @break
                                @case('FUN')
                                Financiado
                                @break
                                @case('SCS')
                                Becado
                                @break
                                @case('SFI')
                                Propio
                                @break
                                @default
                                PROPIO
                            @endswitch
                        </td>
                    </tr>
                </table>
            </div>
            <div class="article">
                    <table>
                        <caption>ASIGNATURAS DE NIVELACION</caption>
                         <tr>
                            <th>ASIGNATURA</th>                       
                            <th>UNIDADES</th>
                            <th>PROFESOR</th>
                        </tr>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>

                    </table>
                </div>
            <div class="article">
                <table>
                    <caption>ASIGNATURAS  DE   POSTGRADO</caption>
                         <tr>
                            <th>ASIGNATURA</th>                       
                            <th>UNIDADES</th>
                            <th>PROFESOR</th>
                        </tr>
                        @foreach($data['inscription']['enrolled_subjects'] as $subject)
                        <tr>
                            <td> {{$subject['data_subject']['subject']['name']}} </td>
                            <td> {{$subject['data_subject']['subject']['uc']}} </td>
                            <td> {{$subject['data_subject']['teacher']['user']['first_name']}}
                                {{$subject['data_subject']['teacher']['user']['first_surname']}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            <br>
            <br>
            <div class="article">
                <div class="container-firma">
                    <div class="firma-line"></div>
                    <div class="firma-name">Alumno</div>
                </div>
                 <div class="container-firma">
                    <div class="firma-line"></div>
                    <div class="firma-name">Profesor Guía</div>
                </div>
                 <div class="container-firma">
                    <div class="firma-line"></div>
                    <div class="firma-name">Coordinador del Postgrado</div>
                </div>
            </div>
            <div class="article">
                <table>
                    <tr>
                        {{substr($data['inscription']['school_period']['start_date'],8)}}
                        {{substr($data['inscription']['school_period']['start_date'],5,-3)}}
                        {{substr($data['inscription']['school_period']['start_date'],0,-6)}}
                        <th>Período Académico:</th>
                        <td>{{\App\Services\ConstanceService::numberToMonth(intval(substr($data['inscription']['school_period']['start_date'],5,-3)))}}
                            {{substr($data['inscription']['school_period']['start_date'],0,-6)}} –
                            {{\App\Services\ConstanceService::numberToMonth(intval(substr($data['inscription']['school_period']['end_date'],5,-3)))}}
                            {{substr($data['inscription']['school_period']['end_date'],0,-6)}}
                        </td>
                        <th>Fecha de Inscripción</th>
                        <td> {{substr($data['inscription']['inscription_date'],8)}} -
                            {{substr($data['inscription']['inscription_date'],5,-3)}} - {{substr($data['inscription']['inscription_date'],0,-6)}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
            Instituto de Ciencias de la Tierra. Fac. Ciencias UCV. Av. Los Ilustres, Los Chaguaramos. <br/>
            Apartado: 3895. Caracas-1010A. Telf: 58-0212-6051082. 
    </div>
</body>
</html>
