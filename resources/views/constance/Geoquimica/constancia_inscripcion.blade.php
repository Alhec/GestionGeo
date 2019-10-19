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
            width: 3.19cm;
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
            width: 5cm;
            border-top: 1px solid black;
        }
        .firma-name{
            text-align: center
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
                        <td colspan="5">{{strtoupper($data['user_data']['first_surname'])}} {{strtoupper($data['user_data']['second_surname'])}}
                            {{strtoupper($data['user_data']['first_name'])}} {{strtoupper($data['user_data']['second_name'])}}
                            </td>
                        <th colspan="1">C.I</th>
                        <td colspan="4"> {{$data['user_data']['identification'] }}</td>
                    </tr>
                     <tr>
                        <th colspan="4">TELF. HABT</th>
                        <td colspan="4">{{$data['user_data']['telephone'] }}</td>
                        <th colspan="2">TELF. TRAB.</th>
                        <td colspan="4">{{$data['user_data']['work_phone'] }}</td>
                    </tr>
                    <tr>
                        <th colspan="2">PROFESOR GUIA </th>
                        <td colspan="5">Prof.</td>
                        <th colspan="2">E-MAIL ESTU.</th>
                        <td colspan="5">{{$data['user_data']['email'] }}</td>
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
                        <td colspan="3"> {{substr($data['inscription']['school_period']['start_date'],8)}} {{substr($data['inscription']['school_period']['start_date'],5,-3)}}  {{substr($data['inscription']['school_period']['start_date'],0,-6)}}</td>
                        <th colspan="3">FECHA PROBABLE CULMINACION</th>
                        <td colspan="4">{{substr($data['inscription']['school_period']['start_date'],8)}} {{substr($data['inscription']['school_period']['start_date'],5,-3)}}  {{substr($data['inscription']['school_period']['start_date'],0,-6)}}</td>
                    </tr>
                    <tr>
                        <th colspan="4">UNIDADES POR CONVALIDACION</th>
                        <td colspan="3"></td>
                        <th colspan="3">UNIDADES CURSADAS HASTA LA FECHA</th>
                        <td colspan="4">{{$data['porcentual_data']['enrolled_credits']}}</td>
                    </tr>
                     <tr>
                        <th colspan="14">TITULOS OBTENIDOS</th>
                    </tr>
                     <tr>
                         <td colspan="14"> - </td>
                         {{--<td colspan="14">Ing. Procesos Industriales - UNIVERSIDAD</td>    --}}
                    </tr>
                     <tr>
                         <td colspan="14"> - </td>
                         {{--<td colspan="14">Ing. Procesos Industriales - UNIVERSIDAD</td>--}}
                    </tr>
                     <tr>
                         <td colspan="14"> - </td>
                         {{--<td colspan="14">Ing. Procesos Industriales - UNIVERSIDAD</td>--}}
                    </tr>
                    <tr>
                        <th colspan="2">FINANCIAMIENTO:</th>
                        <td colspan="14">
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
                            <td> {{$subject['data_subject']['subject']['subject_name']}} </td>
                            <td> {{$subject['data_subject']['subject']['uc']}} </td>
                            <td> {{$subject['data_subject']['teacher']['user']['first_name']}} {{$subject['data_subject']['teacher']['user']['first_surname']}}</td>
                        </tr>
                        @endforeach

                    </table>
                </div>
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
                        <th>Período Académico:</th>
                        <td>Octubre 2017 - Febrero 2018</td>
                        <th>Fecha de Inscripción</th>
                        <td>26 - 10 - 2017</td>
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
