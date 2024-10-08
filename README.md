<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 설명

AI server 를 이용하여 image 를 생성할 수 있습니다\
text to image, image to image 등 기능을 제공합니다

## 개발 환경

- php 8.2
- laravel 10
- sail (docker)
    - mariadb

## 배포 환경

AWS elastic beanstalk, Laravel Forge 등을 이용해서 배포를 했었다가 비용 절약, 배포 편의 등 을 위해 AWS Lambda 로 변경하였습니다

- Git Action
    - development, master 브랜치로 push 가 되면 workflow 를 통해 vapor 로 배포가 됩니다
- Vapor
    - AWS Lambda 서버리스로 배포가 됩니다
- AWS
    - RDS
        - Aurora (Mysql) Cluster 를 사용합니다
    - S3
        - 이미지 저장소로 사용합니다
    - Cloud Front
        - 이미지 CDN 으로 사용합니다
    - Route53
        - 도메인을 서버, CDN 등 과 연결 합니다

## Flow

- Apache/Nginx 웹 서버
- public/index.php
    - composer autoload 를 로드 합니다
    - bootstrap/app.php 를 통해 application 의 인스턴스를 검색 합니다
- Kernel
    - middleware 를 통해 request 를 전달 합니다
- Middleware
    - HTTP 요청을 처리하기 전에 미들웨어를 사용하여 요청을 검사하거나 수정할 수 있습니다
- Service Provider
    - 서비스 제공자는 서비스 컨테이너에 바인딩을 등록하거나 애플리케이션을 부팅할 때 추가적인 설정을 수행할 수 있습니다
- Routing
    - 요청을 경로 또는 컨트롤러로 전송하고 경로별 미들웨어를 실행합니다
- FormRequest
    - HTTP 요청을 검증하고 사용자가 인증되었는지 확인합니다
- Controller
    - 요청을 처리하고 응답을 반환합니다
- Service
    - Business Logic 을 처리합니다
- Response
    - HTTP 응답을 생성합니다

## Relation

### Module

- [ai-generate-services](https://github.com/WilsonParker/ai-generate-services)
- [ai-generate-models](https://github.com/WilsonParker/ai-generate-models)

### Admin

- [ai-generate-admin](https://github.com/WilsonParker/ai-generate-admin)

### AI Server

- [ai-generate-server](https://github.com/WilsonParker/ai-generate-server)
