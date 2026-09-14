---
title: larena/core
description: Общие runtime-контракты, реестр пакетов и ограниченные операции.
tags: [larena-core, core, runtime]
translation_key: larena.package.core
---

# `larena/core`

> **Для кого:** разработчик платформы. **Результат:** понять общий runtime и
> безопасную границу операций. **Источник:** package README, module manifest,
> Specs и Root integration. **Статус:** contract foundation; интеграционный
> реестр и ограниченный runtime проверены в Root.

Core задаёт фундаментальные DTO, интерфейсы и правила исполнения операций. Он
не владеет пользовательским интерфейсом, правами конкретного домена или данными
другого пакета.

- Composer: `larena/core`.
- Владелец: общий runtime и package registry foundation.
- Зрелость: contract foundation с Root integration evidence.

## Зависимости и точки входа

Пакет требует PHP 8.3 и Illuminate 13. Ключевая точка текущего среза —
`Larena\Core\Runtime\SyncOperationRuntime`; контрактная граница включает
`OperationRuntime` и DTO операции, контекста, решения и результата.

Минимальное использование происходит через пакет, который регистрирует свою
операцию и передаёт её Core runtime. Неизвестный или небезопасный режим должен
завершаться отказом.

## Проверка

В entry-приложении выполните `php artisan larena:runtime-security-smoke`.
Полный Root quality gate дополнительно проверяет Minimal CMS dependency closure
и состав реестра.

## Ограничение

Package README всё ещё описывает часть Core как contract skeleton без
собственной production persistence. Не используйте интеграционную готовность
Root как обещание автономной готовности всех поверхностей Core.
