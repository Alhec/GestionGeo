<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            margin: 1cm 2cm 1cm 2cm;
            font-size: 12pt;
        }
        .header{
            position: relative;
            width: 100%;
            height: 3cm;
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
            position: absolute;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="image-geoquimica">
            <img src="{{ public_path() ."/images/icon-banner.png" }}">
        </div>
        <div class="image-ucv">
            <img src="{{ public_path() ."/images/logo-ucv.png" }}" >
        </div>
    </div>
    <div  class="section">
        <div class="title">
            constancia
        </div>
        <div class="content">
            <div class="article">
                Quien  suscribe,  Coordinador  del  Postgrado  en  Geoquímica,  Facultad  de  Ciencias de  la
                Universidad   Central  de Venezuela, por  medio  de   la   presente   hace  constar  que el
                <strong>
                    @if($data['user_data']['administrator']['rol']=='COORDINATOR')
                        Coordinador/a
                    @else
                        Secretario/a
                    @endif
                    {{strtoupper($data['user_data']['first_name'])}} {{strtoupper($data['user_data']['second_name'])}}
                    {{strtoupper($data['user_data']['first_surname'])}}
                    {{strtoupper($data['user_data']['second_surname'])}}</strong>, titular de la cédula de identidad
                    N° {{$data['user_data']['identification'] }}, ejerce sus funciones laborales en <strong>
                    {{strtoupper($data['organization_data']['name'])}}</strong>.
            </div>
            <div class="article">
                Constancia que se expide a petición de la parte interesada, en Caracas, en el mes  de {{$data['month']}}
                de {{$data['year']}}.
            </div>
        </div>
    </div>
    <div class="section">
        <div class="coordinador">
            {{$data['coordinator_data']['level_instruction']}}. {{$data['coordinator_data']['first_name']}}
            {{$data['coordinator_data']['second_name']}} {{$data['coordinator_data']['first_surname']}}
            {{$data['coordinator_data']['second_surname']}}
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
</body>
</html>
