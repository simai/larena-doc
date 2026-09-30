---
title: larena/property
description: Версионированные типы полей, нормализация и валидация.
tags: [larena-property, field, types]
translation_key: larena.package.property
---

# `larena/property`

> **Для кого:** разработчик схем данных и форм. **Результат:** выбрать точный
> `type@version` и проверить значение до сохранения. **Источник:** package README,
> Specs и Root tests. **Статус:** package-owned in-memory foundation.

Property владеет реестром типов, определениями полей, нормализацией, валидацией
и структурированной render model. Текущий реестр (снимок версии 4) содержит
`string@1`, безопасный `string@2`, `integer@1`, `boolean@1`, `text@1`,
`number@1`, `date@1`, `file@1`, `relation@1`, а также `choice@1` (один вариант
из списка), `choices@1` (несколько вариантов), `user@1` (ссылка на
пользователя) и `datetime@1` (локальные дата и время).

Если задача сформулирована как «создать поле», сначала выберите точный
`type@version`, затем выполните нормализацию и серверную валидацию.

- Composer: `larena/property`.
- Владелец: type registry, normalization and validation.
- Зрелость: in-memory foundation.

## Точка входа

Используйте `Larena\Property\Runtime\PropertyTypeRegistry` и выбирайте версию
явно. Нормализация и валидация являются отдельными серверными операциями; перед
записью можно применить их как единый fail-closed шаг.

## Отображение значения

`Larena\Property\Runtime\PropertyDisplayProjector` только читает: он превращает
проверенное сохранённое значение в простой текст с видом поля и статусом
`present`, `empty` или `unavailable`. Числа форматируются без плавающей точки.
Перед вставкой в HTML текст нужно экранировать.

Для `file`, `relation` и `user` подпись даёт владелец данных через
`PropertyReferenceLabelResolver`; Property объявляет только интерфейс. В
`PropertyRenderContext` должны быть заданы `actorRef` и `scopeRef`. Если подписи
нет, значение получает статус `unavailable`: исходная ссылка никогда не
показывается вместо подписи. Визуальный вывод остаётся задачей Admin и Dataview.

## Ограничение

Property не хранит пользовательские значения и не назначает права. Новая схема
должна предпочитать `string@2`; совместимость старого типа не расширяет его
валидацию.
