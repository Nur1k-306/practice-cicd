# Practice CI/CD — URL Shortener

## Описание проекта

Practice CI/CD — учебный проект для демонстрации автоматизации сборки приложения, упаковки в Docker-контейнеры и публикации результата сборки в GitHub Container Registry с использованием GitHub Actions.

В качестве приложения используется веб-сервис сокращения ссылок. Пользователь вводит длинный URL, после чего приложение генерирует короткий код и сохраняет информацию о ссылке в базе данных PostgreSQL. При переходе по короткой ссылке приложение выполняет редирект на исходный URL и увеличивает счётчик переходов.

## Используемый стек

В проекте используются следующие технологии:

* PHP 8.3;
* PostgreSQL 16;
* Docker;
* Docker Compose;
* GitHub Actions;
* GitHub Container Registry;
* Telegram Bot API.

## Структура проекта

```text
practice-cicd/
├── .github/
│   └── workflows/
│       └── docker-build.yml
├── app/
│   ├── config.php
│   ├── db.php
│   ├── functions.php
│   └── schema.sql
├── public/
│   ├── css/
│   │   └── style.css
│   ├── index.php
│   ├── redirect.php
│   ├── router.php
│   └── shorten.php
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── .gitignore
└── README.md
```

## Основные возможности приложения

Приложение выполняет следующие функции:

* создание короткой ссылки на основе длинного URL;
* сохранение ссылки, короткого кода, времени создания и количества переходов в PostgreSQL;
* редирект пользователя по короткой ссылке на исходный URL;
* подсчёт количества переходов;
* поддержка TTL для ограничения времени действия ссылки.

## Ручной запуск через Docker Compose

Для запуска приложения локально необходимо выполнить команду:

```bash
docker compose up --build
```

После запуска приложение будет доступно по адресу:

```text
http://localhost:8000
```

При запуске через Docker Compose создаются два контейнера:

* `practice-cicd-app` — контейнер с PHP-приложением;
* `practice-cicd-postgres` — контейнер с базой данных PostgreSQL.

PostgreSQL внутри Docker Compose использует следующие параметры:

```text
POSTGRES_DB=url_shortener
POSTGRES_USER=postgres
POSTGRES_PASSWORD=postgres
```

Внешний порт базы данных проброшен как `5433`, чтобы не конфликтовать с локальным PostgreSQL на порту `5432`.

Для остановки контейнеров используется команда:

```bash
docker compose down
```

Для остановки контейнеров с удалением тома базы данных:

```bash
docker compose down -v
```

## Ручная сборка Docker-образа

Docker-образ приложения можно собрать вручную командой:

```bash
docker build -t practice-cicd .
```

После сборки контейнер можно запустить командой:

```bash
docker run -p 8000:8000 practice-cicd
```

Однако для полноценной работы приложения требуется база данных PostgreSQL, поэтому рекомендуемый способ локального запуска — через Docker Compose.

## Условия запуска автоматической сборки

Автоматическая сборка настроена с помощью GitHub Actions.

Workflow запускается в двух случаях:

1. при выполнении `push` в ветку `main`;
2. вручную через интерфейс GitHub Actions с помощью события `workflow_dispatch`.

Файл автоматизации расположен по пути:

```text
.github/workflows/docker-build.yml
```

## Общий принцип автоматической сборки

После отправки изменений в ветку `main` GitHub Actions запускает workflow `Docker Build and Telegram Notify`.

Pipeline выполняет следующие шаги:

1. отправляет Telegram-уведомление о начале сборки;
2. получает исходный код репозитория;
3. выполняет авторизацию в GitHub Container Registry;
4. настраивает Docker Buildx;
5. собирает Docker-образ приложения;
6. публикует Docker-образ в GitHub Container Registry;
7. отправляет Telegram-уведомление об успешном завершении или ошибке сборки.

## Хранение результатов сборки

Результатом автоматической сборки является Docker-образ приложения.

Docker-образ публикуется в GitHub Container Registry:

```text
ghcr.io/nur1k-306/practice-cicd
```

Образ публикуется с двумя тегами:

```text
latest
<commit_sha>
```

Пример загрузки последней версии образа:

```bash
docker pull ghcr.io/nur1k-306/practice-cicd:latest
```

Пример загрузки образа по SHA коммита:

```bash
docker pull ghcr.io/nur1k-306/practice-cicd:aed387cdf97404127ec80cb921a78cbdfc5b6e34
```

## Telegram-уведомления

В workflow добавлена отправка уведомлений в Telegram.

Уведомления отправляются:

* при начале сборки;
* при успешном завершении сборки;
* при ошибке сборки.

Для работы Telegram-уведомлений в настройках GitHub-репозитория используются секреты:

```text
TELEGRAM_BOT_TOKEN
TELEGRAM_CHAT_ID
```

Секреты хранятся в разделе:

```text
Settings → Secrets and variables → Actions
```

Значения секретов не хранятся в коде проекта и не публикуются в репозитории.

## Проверка опубликованного образа

Для проверки опубликованного Docker-образа можно выполнить:

```bash
docker pull ghcr.io/nur1k-306/practice-cicd:latest
```

Так как приложение использует PostgreSQL, для полноценного запуска рекомендуется использовать `docker-compose.yml` из репозитория.

## Назначение проекта в рамках практики

Проект разработан в рамках учебной практики для изучения принципов CI/CD. В ходе работы было выполнено:

* контейнеризация PHP-приложения;
* настройка запуска приложения и PostgreSQL через Docker Compose;
* настройка автоматической сборки Docker-образа через GitHub Actions;
* публикация результата сборки в GitHub Container Registry;
* добавление Telegram-уведомлений о процессе и результате сборки.
