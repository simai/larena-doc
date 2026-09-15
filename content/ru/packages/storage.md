---
title: larena/storage
description: Структуры, версии и контролируемые мутации записей.
tags: [larena-storage, storage, records]
translation_key: larena.package.storage
---

# `larena/storage`

> **Для кого:** разработчик данных. **Результат:** работать с версионированной
> записью через package runtime. **Источник:** package README, developer docs,
> Specs и Root tests. **Статус:** developer-testable persistent foundation.

Storage владеет структурами хранения, версиями схем и записей, а также
контролируемыми мутациями. Текущий интеграционный вход —
`Larena\Storage\Runtime\DatabaseStorageWorkbench`.

- Composer: `larena/storage`.
- Владелец: record/schema versions and mutations.
- Зрелость: developer-testable persistent foundation.

Пакет зависит от `larena/core`, `larena/property` и `larena/access`. Тип поля
сначала нормализует Property, решение операции принимает Access, затем Storage
создаёт или меняет запись с проверкой версии.

## Проверка

Root quality gate проверяет базовые мутации, CRUD и повторное чтение. Отдельный
hierarchy test ранее блокировался package test autoload в общей vendor-сборке;
не используйте его отсутствие как доказательство корректности иерархии.

## Ограничение

Прямой доступ к таблицам обходит версии, права и audit-контракт. Публичный
универсальный генератор моделей не принят в рамках двенадцати пакетов.

## Содержимое композиции

В профиле Minimal CMS Storage владеет структурированным содержимым и его
ревизиями. Layout хранит только типизированный `ContentBinding` с ожидаемой
ревизией. HTML не является форматом сохранения редакторского содержимого;
блочная модель может использовать JSON-подход EditorJS и проектироваться в
зарегистрированные компоненты при сборке.
