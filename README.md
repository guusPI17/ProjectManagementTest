# Project Management API

REST API для управления проектами компании. PHP 8.3 + Symfony-компоненты + PostgreSQL 16.

## Требования

- PHP 8.3+
- PostgreSQL 16
- Composer
- Расширения: `pdo_pgsql`

## Установка

```bash
# Клонировать репозиторий
git clone <repo-url>
cd projectManagement

# Скопировать и настроить .env
cp .env.example .env
# Отредактировать .env: DB_HOST, DB_USER, DB_PASSWORD, DB_NAME

# Создать базу данных
composer db:create
```

## Запуск (разработка)

```bash
composer dev:start
```

API будет доступен по адресу `http://localhost:8000` (порт настраивается через `APP_PORT` в `.env`).

## Запуск (продакшен)

```bash
sudo composer prod:start
```

Настраивает nginx и php-fpm.

## API Endpoints

| Метод  | URL                          | Описание                          |
|--------|------------------------------|-----------------------------------|
| GET    | `/api/projects`              | Список проектов (с фильтрацией)   |
| POST   | `/api/projects`              | Создать проект                    |
| GET    | `/api/projects/{id}`         | Получить проект по ID             |
| PUT    | `/api/projects/{id}`         | Обновить проект                   |
| DELETE | `/api/projects/{id}`         | Удалить проект                    |
| POST   | `/api/projects/{id}/check`   | Проверить доступность сайта       |
| GET    | `/api/platforms`             | Список платформ (с фильтрацией)   |
| GET    | `/api/statuses`              | Список статусов (с фильтрацией)   |

### Фильтрация и пагинация

```
GET /api/projects?status=production&platform=wordpress&page=1&per_page=15
GET /api/platforms?code=wordpress&page=1&per_page=15
GET /api/statuses?code=production&page=1&per_page=15
```

## Тестирование

```bash
# Создать тестовую БД
DB_NAME=project_management_test composer db:create

# Применить миграции для тестовой БД
APP_ENV=test composer db:migrate

# Запустить тесты (cs:check + phpunit)
composer test

# Только unit-тесты
composer test -- --testsuite Unit

# Только функциональные тесты
composer test -- --testsuite Functional
```

### Структура тестов

Каждый Action покрыт отдельным тестовым файлом:

```
tests/
├── Functional/Api/
│   ├── Platform/
│   ├── Project/
│   ├── ProjectStatus/
│   └── Routing/
├── Support/
│   ├── AppTestCase.php          # Базовый класс функциональных тестов
│   └── DatabaseTestCase.php     # Подключение к тестовой БД, транзакции
└── Unit/
    ├── Models/
    │   └── ProjectTest.php
    └── Service/
        ├── ProjectCheckerServiceTest.php
        └── ProjectServiceTest.php
```

## Стиль кода

```bash
# Проверить
composer cs:check

# Исправить
composer cs:fix
```

## Кеш

```bash
# Очистить кеш DI-контейнера
composer cache:clear
```

Скомпилированный DI-контейнер кешируется в `var/cache/`. В `dev` окружении кеш автоматически инвалидируется при изменении файлов. В `prod` — необходимо очищать вручную после деплоя.

## OpenAPI документация

```bash
composer openapi
```

Генерирует файл `public/docs/openapi.yaml`. Swagger UI доступен по адресу `http://localhost:8000/docs/`.

## Структура проекта

```
bin/                        # CLI-скрипты
config/                     # Файлы конфигурации
database/
└── migrations/             # Миграции Phinx
deploy/
├── dev/                    # Конфигурация для разработки
└── prod/                   # Конфигурация для продакшена
public/                     # Точка входа и статика
src/
├── Actions/
│   ├── Platform/           # Endpoints платформ
│   ├── Project/            # Endpoints проектов
│   └── ProjectStatus/      # Endpoints статусов проектов
├── Config/                 # Типизированная конфигурация (DatabaseConfiguration)
├── Database/               # PDO Connection
├── Enums/                  # Enum-классы
├── Exceptions/             # Исключения
├── Filters/                # Фильтры запросов
├── Kernel.php              # Bootstrap
├── Models/                 # Модели
├── Repositories/           # Репозитории
├── Responses/              # Стандартизированные ответы
├── Routing/                # Загрузчик маршрутов
└── Services/               # Бизнес-логика
tests/
├── Functional/Api/         # Функциональные тесты API (по файлу на каждый Action)
│   ├── Platform/           # Тесты платформ
│   ├── Project/            # Тесты проектов
│   ├── ProjectStatus/      # Тесты статусов
│   └── Routing/            # Тесты маршрутизации (404, 405)
├── Support/                # Базовые классы тестов
└── Unit/                   # Unit-тесты сервисов и моделей
```

## Технологии

### Symfony-компоненты

- **symfony/dependency-injection** — DI-контейнер с autowiring и автоконфигурацией. Автоматически разрешает зависимости сервисов по type-hint'ам. Без него: ручное создание и передача зависимостей в каждом месте использования, дублирование конфигурации, невозможность легко подменить реализацию для тестирования.

- **symfony/routing** — маршрутизация через `#[Route]` атрибуты на Action-классах. Поддерживает параметры в URL, HTTP-методы, requirements. Без неё: ручной парсинг `$_SERVER['REQUEST_URI']`, регулярные выражения для извлечения параметров, switch/case или массив маршрутов для dispatch.

- **symfony/http-foundation** — объектная обёртка над HTTP-запросом и ответом (`Request`, `JsonResponse`). Без неё: прямая работа с суперглобалами `$_GET`, `$_POST`, `$_SERVER`, ручная установка заголовков через `header()`, ручная сериализация тела ответа через `echo json_encode()`.

- **symfony/serializer** — сериализация/десериализация JSON в объекты с поддержкой групп и `object_to_populate`. Группы позволяют использовать разные наборы полей для create/update/response. `object_to_populate` заполняет существующий объект, что решает проблему частичного обновления (различие «поле не отправлено» и «поле = null»). Без неё: ручной `json_decode()` + маппинг каждого поля на модель, отдельная логика для partial update.

- **symfony/validator** — валидация через `#[Assert\...]` атрибуты на свойствах модели. Правила объявлены рядом с данными, поддерживаются группы валидации (разные правила для create и update). Без неё: ручные `if/else` проверки в каждом endpoint, дублирование правил валидации, сложность поддержки разных наборов правил.

- **symfony/config** — типизированная конфигурация через TreeBuilder. Описывает структуру конфигурации (типы, значения по умолчанию, обязательность), валидирует при загрузке. Без неё: ручной доступ к `$_ENV` с приведением типов и проверкой наличия в каждом месте использования.

- **symfony/dotenv** — загрузка переменных окружения из `.env` файлов с поддержкой `.env.local`, `.env.test`. Без неё: обязательная установка переменных на уровне системы/контейнера или ручной парсинг `.env` файла.

- **symfony/http-client** — HTTP-клиент для проверки доступности сайтов проектов. Предоставляет единый интерфейс, автоматические редиректы, таймауты. Без него: низкоуровневый cURL (`curl_init`, `curl_setopt`, `curl_exec`) или `file_get_contents()` — нет единого интерфейса, сложная обработка ошибок и таймаутов.

- **symfony/property-access** — компонент для доступа к свойствам объектов через геттеры/сеттеры или напрямую. Необходим для корректной работы `symfony/serializer` при десериализации в модели.

### Инструменты

- **robmorgan/phinx** — миграции базы данных с версионированием. Отслеживает применённые миграции, поддерживает откат (`rollback`), позволяет воспроизвести схему на любом окружении. Без него: ручные SQL-скрипты без отслеживания состояния, невозможность отката, ручная синхронизация схемы между окружениями.

- **phpunit/phpunit** — стандарт де-факто для unit и функционального тестирования в PHP. Поддерживает data providers, моки, test suites, code coverage.

- **friendsofphp/php-cs-fixer** — автоматическое форматирование кода по настроенным правилам (PSR-12+). Обеспечивает единый стиль кода без ручного контроля.

- **zircote/swagger-php** — генерация OpenAPI-спецификации из `#[OA\...]` атрибутов в коде. Документация живёт рядом с кодом и обновляется автоматически. Без неё: ручное написание YAML/JSON-спецификации, которая быстро рассинхронизируется с реальным API.
