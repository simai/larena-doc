---
title: Начало работы
description: Как запустить и проверить текущую developer-сборку Larena.
tags: [install, start, doctor]
translation_key: larena.start
---

# Начало работы

> **Для кого:** разработчик. **Результат:** проверенная локальная сборка и путь
> к первому администратору. **Источники:** Root README, `local-setup.md` и
> `developer-alpha-bundle.md`. **Статус:** проверено по исходникам; команды
> установки не выполнялись повторно над пользовательским сайтом.

## Требования

- PHP 8.3 или новее с PDO, OpenSSL и mbstring;
- Composer для работы из репозитория;
- SQLite для переносимого developer bundle либо подготовленная MySQL 8/9;
- приложение `simai/larena` или проверенный Developer Alpha bundle.

## Работа из репозитория

1. Установите зависимости Composer.
2. Создайте локальный application key.
3. Выполните `larena:doctor` и dry-run установки.
4. Запустите runtime smoke и проверьте ожидаемый защитный отказ installer.

В корне entry-приложения выполните безопасную диагностику:

```bash
composer install
php artisan key:generate
php artisan larena:doctor
php artisan larena:install --dry-run
php artisan larena:runtime-security-smoke
```

Команда установки без launch record должна завершаться защитным отказом. Это
ожидаемое поведение текущего контура, а не признак сломанной установки.

## Первый администратор

В установленном Developer Alpha bundle откройте `/admin/setup`. Экран доступен
только до создания первой учётной записи. После завершения вход выполняется по
адресу `/admin/login`.

## Проверка результата

`larena:doctor` должен подтвердить среду, а runtime smoke — базовые границы
операций. Для полного текущего набора проверок используйте `composer
quality:full` в entry-приложении. Не применяйте миграции или installer-команды
к существующим данным только ради знакомства с документацией.

Дальше прочитайте [устройство платформы](/concepts/) и откройте
[справочник базовых пакетов](/packages/).
