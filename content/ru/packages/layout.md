---
title: larena/layout
description: Декларативная структура страницы и render plan.
tags: [larena-layout, composition, page]
translation_key: larena.package.layout
---

# `larena/layout`

> **Для кого:** разработчик композиции. **Результат:** описать структуру страницы
> без получения данных и решения прав. **Источник:** package README, Specs и
> Root tests. **Статус:** descriptor and render-plan foundation.

Layout нормализует декларативную структуру областей, секций и блоков и строит
render plan. Текущие точки входа:
`PageAssemblyDescriptorNormalizer` и `MinimalCmsRenderPlanRuntime`.

- Composer: `larena/layout`.
- Владелец: page composition descriptor and render plan.
- Зрелость: bounded foundation.

Пакет зависит от Core. Он переносит структуру, props и ссылки; получение данных,
проверка прав и регистрация компонента принадлежат другим пакетам.

## Проверка

Root tests проверяют конструктор descriptor, версию и rollback принятой
композиции collection-screen.

## Ограничение

Текущий collection-screen уже универсального замысла Layout. Не расширяйте его
до произвольной страницы только на основании формата descriptor.
