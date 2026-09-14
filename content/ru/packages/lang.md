---
title: larena/lang
description: Языковые ресурсы, fallback и локализованный интерфейс.
tags: [larena-lang, localization, fallback]
translation_key: larena.package.lang
---

# `larena/lang`

> **Для кого:** разработчик интерфейса. **Результат:** понять текущую границу
> локализации. **Источник:** package README, Specs и Root integration.
> **Статус:** package pre-codegen; интеграционный fallback проверен ограниченно.

Lang предназначен для интерфейсного текста, метаданных пакетов, сообщений,
locale fallback и форматирования. Текущий package repository сообщает, что
реализация ещё не начата, а Root проверяет только существующий in-memory
fallback в базовом сценарии.

- Composer: `larena/lang`.
- Владелец: interface language resources and fallback.
- Зрелость: package pre-codegen, ограниченная Root integration.

Пакет зависит от `larena/core`, PHP 8.3 и Illuminate Support 13.

## Практическое правило

Храните пользовательские строки в package language resources и сохраняйте
паритет обязательных `ru`/`en` ресурсов. Не принимайте текущий fallback как
стабильный полный API, пока package-owned реализация и документация не приняты.

## Диагностика

Если строка отсутствует, проверьте регистрацию пакета, выбранную locale и
наличие ресурса. Полный перевод интерфейса не входит в принятый core-12 срез.
